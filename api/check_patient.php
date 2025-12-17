<?php
/**
 * API untuk mengecek apakah pasien dengan No. RM sudah ada
 * Sistem Informasi Klinik
 */

header('Content-Type: application/json');
require_once '../includes/config.php';

// Get no_rm from GET parameter
$no_rm = isset($_GET['no_rm']) ? trim($_GET['no_rm']) : '';

if (empty($no_rm)) {
    echo json_encode([
        'success' => false,
        'error' => 'No. RM tidak boleh kosong'
    ]);
    exit;
}

try {
    $conn = getDBConnection();

    // Check if patient exists (join with users to get username if linked)
    $stmt = $conn->prepare("
        SELECT p.no_rm, p.nik, p.nama, p.tanggal_lahir, p.jenis_kelamin, p.alamat, p.telepon, u.username
        FROM pasien p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.no_rm = ?
    ");
    $stmt->bind_param("s", $no_rm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pasien = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        echo json_encode([
            'success' => true,
            'exists' => true,
            'data' => $pasien
        ]);
    } else {
        $stmt->close();
        $conn->close();

        echo json_encode([
            'success' => true,
            'exists' => false,
            'message' => 'No. RM tidak ditemukan. Silakan isi data pasien baru.'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
