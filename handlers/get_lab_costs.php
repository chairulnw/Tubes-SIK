<?php
/**
 * Handler untuk get total biaya lab (AJAX)
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'total_lab' => 0]);
    exit();
}

$no_rm = $_GET['no_rm'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

if (empty($no_rm) || empty($tanggal)) {
    echo json_encode(['success' => true, 'total_lab' => 0]);
    exit();
}

$conn = getDBConnection();

try {
    $stmt = $conn->prepare("
        SELECT SUM(pl.biaya) as total_lab
        FROM permintaan_lab pl
        JOIN pemeriksaan p ON pl.pemeriksaan_id = p.id
        WHERE pl.no_rm = ?
        AND DATE(p.tanggal_pemeriksaan) = ?
        AND pl.status = 'selesai'
    ");
    $stmt->bind_param("ss", $no_rm, $tanggal);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    $total_lab = $row['total_lab'] ?? 0;
    echo json_encode(['success' => true, 'total_lab' => $total_lab]);

} catch (Exception $e) {
    $conn->close();
    echo json_encode(['success' => false, 'total_lab' => 0, 'error' => $e->getMessage()]);
}
?>
