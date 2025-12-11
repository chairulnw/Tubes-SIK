<?php
/**
 * Handler untuk proses resep farmasi
 * Sistem Informasi Klinik
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/farmasi.php");
    exit();
}

if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'farmasi'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses"));
    exit();
}

$resep_id = $_POST['resep_id'] ?? 0;
$total_biaya = $_POST['total_biaya'] ?? 0;
$catatan_apoteker = isset($_POST['catatan_apoteker']) ? trim($_POST['catatan_apoteker']) : '';

// Arrays dari form
$nama_obat_array = $_POST['nama_obat'] ?? [];
$jumlah_array = $_POST['jumlah'] ?? [];
$satuan_array = $_POST['satuan'] ?? [];
$harga_array = $_POST['harga'] ?? [];
$dosis_array = $_POST['dosis'] ?? [];

// Validasi input
if (empty($resep_id) || empty($nama_obat_array)) {
    header("Location: ../pages/farmasi.php?error=" . urlencode("Data tidak lengkap"));
    exit();
}

$conn = getDBConnection();

try {
    // Start transaction
    $conn->begin_transaction();

    // Validasi resep exists
    $stmt = $conn->prepare("SELECT id FROM resep WHERE id = ?");
    $stmt->bind_param("i", $resep_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        throw new Exception("Resep tidak ditemukan");
    }
    $stmt->close();

    // Insert detail obat
    $stmt_detail = $conn->prepare("INSERT INTO resep_detail (resep_id, nama_obat, jumlah, satuan, dosis, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $count = count($nama_obat_array);
    for ($i = 0; $i < $count; $i++) {
        $nama_obat = trim($nama_obat_array[$i]);
        $jumlah = intval($jumlah_array[$i]);
        $satuan = trim($satuan_array[$i]);
        $dosis = trim($dosis_array[$i]);
        $harga = floatval($harga_array[$i]);
        $subtotal = $jumlah * $harga;

        $stmt_detail->bind_param("isissdd", $resep_id, $nama_obat, $jumlah, $satuan, $dosis, $harga, $subtotal);
        $stmt_detail->execute();
    }
    $stmt_detail->close();

    // Update resep - set status selesai, biaya, farmasi_id, tanggal_dispensing
    $stmt_update = $conn->prepare("UPDATE resep SET status = 'selesai', biaya = ?, farmasi_id = ?, tanggal_dispensing = NOW() WHERE id = ?");
    $stmt_update->bind_param("dii", $total_biaya, $_SESSION['user_id'], $resep_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Commit transaction
    $conn->commit();
    $conn->close();

    header("Location: ../pages/farmasi.php?success=" . urlencode("Resep berhasil diselesaikan"));
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    header("Location: ../pages/farmasi.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
