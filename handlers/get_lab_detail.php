<?php
/**
 * Handler untuk get detail hasil lab (AJAX)
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$id = $_GET['id'] ?? 0;

$conn = getDBConnection();

try {
    $stmt = $conn->prepare("
        SELECT pl.kode_lab, pl.jenis_tes, pl.no_rm,
               p.nama as nama_pasien,
               hl.hasil_pemeriksaan, hl.catatan_petugas
        FROM permintaan_lab pl
        JOIN pasien p ON pl.no_rm = p.no_rm
        LEFT JOIN hasil_lab hl ON pl.id = hl.permintaan_lab_id
        WHERE pl.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $lab = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => true, 'lab' => $lab]);
    } else {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
    }

} catch (Exception $e) {
    $conn->close();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
