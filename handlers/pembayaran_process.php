<?php
/**
 * Handler untuk proses pembayaran
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/pembayaran.php");
    exit();
}

if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'kasir'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses"));
    exit();
}

$no_rm = trim($_POST['no_rm']);
$tanggal = $_POST['tanggal'];
$biaya_pendaftaran = $_POST['biaya_pendaftaran'];
$biaya_konsultasi = $_POST['biaya_konsultasi'];
$biaya_obat = $_POST['biaya_obat'];
$biaya_lab = $_POST['biaya_lab'];
$metode_pembayaran = $_POST['metode_pembayaran'];

$total_bayar = $biaya_pendaftaran + $biaya_konsultasi + $biaya_obat + $biaya_lab;

$conn = getDBConnection();

try {
    // Generate no_invoice
    $result = $conn->query("SELECT no_invoice FROM pembayaran ORDER BY created_at DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $last_invoice = $result->fetch_assoc()['no_invoice'];
        $last_number = intval(substr($last_invoice, 3));
        $new_number = $last_number + 1;
        $no_invoice = 'INV' . str_pad($new_number, 7, '0', STR_PAD_LEFT);
    } else {
        $no_invoice = 'INV0000001';
    }

    $stmt = $conn->prepare("INSERT INTO pembayaran (no_invoice, no_rm, tanggal_pembayaran, biaya_pendaftaran, biaya_konsultasi, biaya_obat, biaya_lab, total_bayar, metode_pembayaran, kasir_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'lunas')");
    $stmt->bind_param("sssdddddsi", $no_invoice, $no_rm, $tanggal, $biaya_pendaftaran, $biaya_konsultasi, $biaya_obat, $biaya_lab, $total_bayar, $metode_pembayaran, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/pembayaran.php?success=" . urlencode("Pembayaran berhasil. No. Invoice: $no_invoice"));
        exit();
    } else {
        throw new Exception("Gagal menyimpan data: " . $stmt->error);
    }
} catch (Exception $e) {
    $conn->close();
    header("Location: ../pages/pembayaran.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
