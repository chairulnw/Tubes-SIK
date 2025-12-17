<?php
/**
 * API Endpoint untuk menerima data wearable dari IoT device
 * Sistem Informasi Klinik
 */

header('Content-Type: application/json');
require_once '../includes/config.php';

// Get JSON input
$json = file_get_contents('php://input');

// DEBUG: Log raw input for troubleshooting
error_log("[WEARABLE API] Raw input: " . $json);

$data = json_decode($json, true);

// Validate JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("[WEARABLE API] JSON decode error: " . json_last_error_msg());
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON format',
        'debug_raw_input' => substr($json, 0, 200) // First 200 chars for debugging
    ]);
    exit;
}

// Validate required fields (only no_rm and detak_jantung are required, spo2 is optional)
if (!isset($data['no_rm']) || !isset($data['detak_jantung'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required fields (no_rm, detak_jantung)'
    ]);
    exit;
}

// Range validation
$bpm = intval($data['detak_jantung']);
$spo2 = isset($data['spo2']) ? intval($data['spo2']) : null; // SpO2 is optional

if ($bpm < 20 || $bpm > 255) {
    echo json_encode([
        'success' => false,
        'error' => 'detak_jantung out of range (20-255 BPM)'
    ]);
    exit;
}

// Validate spo2 only if provided
if ($spo2 !== null && ($spo2 < 0 || $spo2 > 100)) {
    echo json_encode([
        'success' => false,
        'error' => 'spo2 out of range (0-100%)'
    ]);
    exit;
}

// Calculate status based on thresholds
$status = 'normal';
if ($spo2 !== null) {
    // Calculate status with SpO2
    if ($bpm < 50 || $bpm > 120 || $spo2 < 90) {
        $status = 'danger';
    } elseif (($bpm >= 50 && $bpm < 60) || ($bpm > 100 && $bpm <= 120) || ($spo2 >= 90 && $spo2 < 95)) {
        $status = 'warning';
    }
} else {
    // Calculate status with Heart Rate only
    if ($bpm < 50 || $bpm > 120) {
        $status = 'danger';
    } elseif (($bpm >= 50 && $bpm < 60) || ($bpm > 100 && $bpm <= 120)) {
        $status = 'warning';
    }
}

try {
    $conn = getDBConnection();

    // Check if no_rm exists in pasien table
    $stmt = $conn->prepare("SELECT no_rm FROM pasien WHERE no_rm = ?");
    $stmt->bind_param("s", $data['no_rm']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        echo json_encode([
            'success' => false,
            'error' => 'no_rm not found in pasien table'
        ]);
        exit;
    }
    $stmt->close();

    // Insert data into wearable_data table
    $device_id = isset($data['device_id']) ? $data['device_id'] : 'HR_001';

    // Handle NULL spo2 by binding as integer type (NULL will be inserted if value is null)
    $stmt = $conn->prepare("INSERT INTO wearable_data (no_rm, detak_jantung, spo2, status, device_id) VALUES (?, ?, ?, ?, ?)");
    if ($spo2 === null) {
        // Bind NULL value for spo2
        $null_value = null;
        $stmt->bind_param("siiss", $data['no_rm'], $bpm, $null_value, $status, $device_id);
    } else {
        $stmt->bind_param("siiss", $data['no_rm'], $bpm, $spo2, $status, $device_id);
    }

    if ($stmt->execute()) {
        $data_id = $conn->insert_id;
        $stmt->close();
        $conn->close();

        echo json_encode([
            'success' => true,
            'message' => 'Data wearable tersimpan',
            'data_id' => $data_id,
            'status' => $status
        ]);
    } else {
        $stmt->close();
        $conn->close();
        echo json_encode([
            'success' => false,
            'error' => 'Failed to insert data'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
