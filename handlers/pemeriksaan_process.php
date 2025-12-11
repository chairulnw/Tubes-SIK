<?php
/**
 * Handler untuk proses pemeriksaan
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/pemeriksaan.php");
    exit();
}

if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'dokter'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses"));
    exit();
}

$no_rm = trim($_POST['no_rm']);
$tanggal = $_POST['tanggal'];
$keluhan = isset($_POST['keluhan']) ? trim($_POST['keluhan']) : '';
$tekanan_darah = trim($_POST['tekanan_darah']);
$suhu = $_POST['suhu'];
$nadi = $_POST['nadi'];
$pernapasan = $_POST['pernapasan'];
$berat_badan = $_POST['berat_badan'];
$tinggi_badan = $_POST['tinggi_badan'];
$diagnosis = trim($_POST['diagnosis']);
$anamnesis = trim($_POST['anamnesis']);

$conn = getDBConnection();

try {
    // Validasi: Cek apakah No. RM ada di tabel pasien
    $stmt = $conn->prepare("SELECT no_rm, nama FROM pasien WHERE no_rm = ?");
    $stmt->bind_param("s", $no_rm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/pemeriksaan.php?error=" . urlencode("No. RM '$no_rm' tidak ditemukan. Pasien harus didaftarkan terlebih dahulu."));
        exit();
    }
    $stmt->close();

    // Insert pemeriksaan
    $stmt = $conn->prepare("INSERT INTO pemeriksaan (no_rm, tanggal_pemeriksaan, dokter_id, keluhan, tekanan_darah, suhu, nadi, pernapasan, berat_badan, tinggi_badan, diagnosis, ammnesis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissiiiidds", $no_rm, $tanggal, $_SESSION['user_id'], $keluhan, $tekanan_darah, $suhu, $nadi, $pernapasan, $berat_badan, $tinggi_badan, $diagnosis, $anamnesis);

    if ($stmt->execute()) {
        $pemeriksaan_id = $conn->insert_id;
        $stmt->close();

        // Jika ada rujukan lab
        if (isset($_POST['butuh_lab']) && $_POST['butuh_lab'] == '1') {
            $catatan_lab = isset($_POST['catatan_lab']) ? trim($_POST['catatan_lab']) : '';
            $kode_lab = generateKodeLab($conn);

            // Insert permintaan lab dengan jenis tes "Rujukan Laboratorium"
            $stmt_lab = $conn->prepare("INSERT INTO permintaan_lab (kode_lab, pemeriksaan_id, no_rm, dokter_id, tanggal_permintaan, jenis_tes, catatan_dokter, biaya, status) VALUES (?, ?, ?, ?, NOW(), 'Rujukan Laboratorium', ?, 0, 'pending')");
            $stmt_lab->bind_param("sisis", $kode_lab, $pemeriksaan_id, $no_rm, $_SESSION['user_id'], $catatan_lab);
            $stmt_lab->execute();
            $stmt_lab->close();
        }

        // Jika ada rujukan farmasi
        if (isset($_POST['butuh_farmasi']) && $_POST['butuh_farmasi'] == '1') {
            $catatan_farmasi = isset($_POST['catatan_farmasi']) ? trim($_POST['catatan_farmasi']) : '';
            $no_resep = generateNoResep($conn);

            // Insert resep ke tabel resep
            $stmt_resep = $conn->prepare("INSERT INTO resep (no_resep, pemeriksaan_id, no_rm, dokter_id, tanggal_resep, catatan_resep, status) VALUES (?, ?, ?, ?, NOW(), ?, 'pending')");
            $stmt_resep->bind_param("sisis", $no_resep, $pemeriksaan_id, $no_rm, $_SESSION['user_id'], $catatan_farmasi);
            $stmt_resep->execute();
            $stmt_resep->close();
        }

        $conn->close();
        header("Location: ../pages/pemeriksaan.php?success=" . urlencode("Data pemeriksaan berhasil disimpan"));
        exit();
    } else {
        throw new Exception("Gagal menyimpan data: " . $stmt->error);
    }
} catch (Exception $e) {
    $conn->close();
    header("Location: ../pages/pemeriksaan.php?error=" . urlencode($e->getMessage()));
    exit();
}

/**
 * Generate kode lab unik
 */
function generateKodeLab($conn) {
    $prefix = "LAB" . date('Y');
    $stmt = $conn->prepare("SELECT kode_lab FROM permintaan_lab WHERE kode_lab LIKE ? ORDER BY kode_lab DESC LIMIT 1");
    $like_pattern = $prefix . "%";
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = intval(substr($row['kode_lab'], -3));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    $stmt->close();

    return $prefix . str_pad($new_number, 3, '0', STR_PAD_LEFT);
}

/**
 * Generate nomor resep unik
 */
function generateNoResep($conn) {
    $prefix = "RX" . date('Y');
    $stmt = $conn->prepare("SELECT no_resep FROM resep WHERE no_resep LIKE ? ORDER BY no_resep DESC LIMIT 1");
    $like_pattern = $prefix . "%";
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = intval(substr($row['no_resep'], -4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    $stmt->close();

    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}
?>
