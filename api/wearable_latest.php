<?php
/**
 * API Endpoint untuk get data wearable terbaru
 * Digunakan untuk AJAX polling di halaman data-medis.php
 * Sistem Informasi Klinik
 */

header('Content-Type: application/json');
require_once '../includes/config.php';

// Get parameters
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;

// Limit validation (max 50 data points untuk chart)
if ($limit < 1 || $limit > 50) {
    $limit = 20;
}

try {
    $conn = getDBConnection();

    // Get latest wearable data (from shared device - no filter by no_rm)
    $stmt = $conn->prepare("SELECT w.id, w.waktu_pembacaan, w.detak_jantung, w.spo2, w.status, w.device_id, w.no_rm, p.nama as nama_pasien
                           FROM wearable_data w
                           LEFT JOIN pasien p ON w.no_rm = p.no_rm
                           ORDER BY w.waktu_pembacaan DESC
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'waktu_pembacaan' => $row['waktu_pembacaan'],
            'detak_jantung' => intval($row['detak_jantung']),
            'spo2' => $row['spo2'] ? intval($row['spo2']) : null,
            'status' => $row['status'],
            'device_id' => $row['device_id'],
            'no_rm' => $row['no_rm'],
            'nama_pasien' => $row['nama_pasien']
        ];
    }

    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'count' => count($data),
        'data' => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
