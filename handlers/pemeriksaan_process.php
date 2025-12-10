<?php
/**
 * Handler untuk proses pemeriksaan
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/pemeriksaan.php");
    exit();
}

if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'dokter'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses"));
    exit();
}

$no_rm = trim($_POST['no_rm']);
$tanggal = $_POST['tanggal'];
$keluhan = isset($_POST['keluhan']) ? trim($_POST['keluhan']) : '';
$tekanan_darah = trim($_POST['tekanan_darah']);
$suhu = $_POST['suhu'];
$nadi = $_POST['nadi'];
$pernapasan = $_POST['pernapasan'];
$berat_badan = $_POST['berat_badan'];
$tinggi_badan = $_POST['tinggi_badan'];
$diagnosis = trim($_POST['diagnosis']);
$terapi = trim($_POST['terapi']);
$anamnesis = trim($_POST['anamnesis']);

$conn = getDBConnection();

try {
    // Validasi: Cek apakah No. RM ada di tabel pasien
    $stmt = $conn->prepare("SELECT no_rm, nama FROM pasien WHERE no_rm = ?");
    $stmt->bind_param("s", $no_rm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/pemeriksaan.php?error=" . urlencode("No. RM '$no_rm' tidak ditemukan. Pasien harus didaftarkan terlebih dahulu."));
        exit();
    }
    $stmt->close();

    // Insert pemeriksaan
    $stmt = $conn->prepare("INSERT INTO pemeriksaan (no_rm, tanggal_pemeriksaan, dokter_id, keluhan, tekanan_darah, suhu, nadi, pernapasan, berat_badan, tinggi_badan, diagnosis, terapi, ammnesis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissiiiiddss", $no_rm, $tanggal, $_SESSION['user_id'], $keluhan, $tekanan_darah, $suhu, $nadi, $pernapasan, $berat_badan, $tinggi_badan, $diagnosis, $terapi, $anamnesis);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/pemeriksaan.php?success=" . urlencode("Data pemeriksaan berhasil disimpan"));
        exit();
    } else {
        throw new Exception("Gagal menyimpan data: " . $stmt->error);
    }
} catch (Exception $e) {
    $conn->close();
    header("Location: ../pages/pemeriksaan.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
