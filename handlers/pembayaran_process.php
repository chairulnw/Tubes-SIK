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
$metode_pembayaran = $_POST['metode'];

$total_bayar = $biaya_pendaftaran + $biaya_konsultasi + $biaya_obat + $biaya_lab;

$conn = getDBConnection();

try {
    // Generate no_invoice otomatis dengan format INV + YEAR + XXXX
    $no_invoice = generateNoInvoice($conn);

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

/**
 * Generate nomor invoice unik dengan format INV + YEAR + XXXX
 */
function generateNoInvoice($conn) {
    $prefix = "INV" . date('Y');
    $stmt = $conn->prepare("SELECT no_invoice FROM pembayaran WHERE no_invoice LIKE ? ORDER BY no_invoice DESC LIMIT 1");
    $like_pattern = $prefix . "%";
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = intval(substr($row['no_invoice'], -4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    $stmt->close();

    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}
?>
