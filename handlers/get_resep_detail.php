<?php
/**
 * Handler untuk get detail resep (AJAX)
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$resep_id = $_GET['id'] ?? 0;

$conn = getDBConnection();

try {
    $stmt = $conn->prepare("
        SELECT r.*, p.nama as nama_pasien, u.nama_lengkap as nama_dokter,
               u2.nama_lengkap as petugas_farmasi
        FROM resep r
        JOIN pasien p ON r.no_rm = p.no_rm
        JOIN users u ON r.dokter_id = u.id
        LEFT JOIN users u2 ON r.farmasi_id = u2.id
        WHERE r.id = ?
    ");
    $stmt->bind_param("i", $resep_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $resep = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => true, 'resep' => $resep]);
    } else {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Resep tidak ditemukan']);
    }

} catch (Exception $e) {
    $conn->close();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
