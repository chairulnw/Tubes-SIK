<?php
/**
 * API Endpoint untuk get data wearable terbaru
 * Digunakan untuk AJAX polling di halaman data-medis.php
 * Sistem Informasi Klinik
 */

header('Content-Type: application/json');
require_once '../includes/config.php';

// Get parameters
$no_rm = isset($_GET['no_rm']) ? trim($_GET['no_rm']) : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;

// Validate no_rm
if (empty($no_rm)) {
    echo json_encode([
        'success' => false,
        'error' => 'no_rm parameter required'
    ]);
    exit;
}

// Limit validation (max 50 data points untuk chart)
if ($limit < 1 || $limit > 50) {
    $limit = 20;
}

try {
    $conn = getDBConnection();

    // Get latest wearable data
    $stmt = $conn->prepare("SELECT id, waktu_pembacaan, detak_jantung, spo2, status, device_id
                           FROM wearable_data
                           WHERE no_rm = ?
                           ORDER BY waktu_pembacaan DESC
                           LIMIT ?");
    $stmt->bind_param("si", $no_rm, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'waktu_pembacaan' => $row['waktu_pembacaan'],
            'detak_jantung' => intval($row['detak_jantung']),
            'spo2' => intval($row['spo2']),
            'status' => $row['status'],
            'device_id' => $row['device_id']
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
