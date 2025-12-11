<?php
/**
 * Handler untuk input hasil laboratorium
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/laboratorium.php");
    exit();
}

if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'laboratorium'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses"));
    exit();
}

$permintaan_lab_id = $_POST['permintaan_lab_id'];
$hasil_pemeriksaan = trim($_POST['hasil_pemeriksaan']);
$catatan_petugas = isset($_POST['catatan_petugas']) ? trim($_POST['catatan_petugas']) : '';

$conn = getDBConnection();

try {
    // Cek apakah permintaan lab exists
    $stmt = $conn->prepare("SELECT id, status FROM permintaan_lab WHERE id = ?");
    $stmt->bind_param("i", $permintaan_lab_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/laboratorium.php?error=" . urlencode("Permintaan lab tidak ditemukan"));
        exit();
    }
    $stmt->close();

    // Insert hasil lab
    $stmt = $conn->prepare("INSERT INTO hasil_lab (permintaan_lab_id, petugas_lab_id, tanggal_hasil, hasil_pemeriksaan, catatan_petugas) VALUES (?, ?, NOW(), ?, ?)");
    $stmt->bind_param("iiss", $permintaan_lab_id, $_SESSION['user_id'], $hasil_pemeriksaan, $catatan_petugas);

    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan hasil lab: " . $stmt->error);
    }
    $stmt->close();

    // Update status permintaan lab menjadi selesai
    $stmt = $conn->prepare("UPDATE permintaan_lab SET status = 'selesai' WHERE id = ?");
    $stmt->bind_param("i", $permintaan_lab_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();
    header("Location: ../pages/laboratorium.php?success=" . urlencode("Hasil lab berhasil disimpan"));
    exit();

} catch (Exception $e) {
    $conn->close();
    header("Location: ../pages/laboratorium.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
