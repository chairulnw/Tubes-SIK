<?php
/**
 * Handler untuk update status resep (AJAX)
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'farmasi'])) {
    echo json_encode(['success' => false, 'message' => 'Tidak memiliki akses']);
    exit();
}

$resep_id = $_POST['resep_id'] ?? 0;
$status = $_POST['status'] ?? '';

if (!in_array($status, ['pending', 'diproses', 'selesai', 'dibatalkan'])) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit();
}

$conn = getDBConnection();

try {
    $stmt = $conn->prepare("UPDATE resep SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $resep_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
    } else {
        throw new Exception("Gagal update status");
    }

} catch (Exception $e) {
    $conn->close();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
