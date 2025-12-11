<?php
/**
 * Handler untuk proses resep farmasi
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

$resep_id = $_POST['resep_id'];
$biaya = $_POST['biaya'];

$conn = getDBConnection();

try {
    // Cek apakah resep exists
    $stmt = $conn->prepare("SELECT id, status FROM resep WHERE id = ?");
    $stmt->bind_param("i", $resep_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/farmasi.php?error=" . urlencode("Resep tidak ditemukan"));
        exit();
    }
    $stmt->close();

    // Update status resep menjadi selesai
    $stmt = $conn->prepare("UPDATE resep SET status = 'selesai', biaya = ?, farmasi_id = ?, tanggal_dispensing = NOW() WHERE id = ?");
    $stmt->bind_param("dii", $biaya, $_SESSION['user_id'], $resep_id);

    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan data resep: " . $stmt->error);
    }
    $stmt->close();

    $conn->close();
    header("Location: ../pages/farmasi.php?success=" . urlencode("Resep berhasil diproses"));
    exit();

} catch (Exception $e) {
    $conn->close();
    header("Location: ../pages/farmasi.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
