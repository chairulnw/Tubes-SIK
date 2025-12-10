<?php
/**
 * Handler untuk proses pendaftaran pasien
 * Sistem Informasi Klinik
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/pendaftaran.php");
    exit();
}

// Pastikan user memiliki akses
if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'pendaftaran'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses"));
    exit();
}

// Ambil data dari form
$no_rm = trim($_POST['no_rm']);
$nik = trim($_POST['nik']);
$nama = trim($_POST['nama']);
$tanggal_lahir = $_POST['tanggal_lahir'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$alamat = trim($_POST['alamat']);
$telepon = trim($_POST['telepon']);
$keluhan = trim($_POST['keluhan']);
$username_pasien = isset($_POST['username_pasien']) ? trim($_POST['username_pasien']) : '';

$conn = getDBConnection();

// Cek dan ambil user_id jika username_pasien diisi
$user_id = null;
if (!empty($username_pasien)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND role = 'pasien'");
    $stmt->bind_param("s", $username_pasien);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $user_id = $user_data['id'];
    } else {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/pendaftaran.php?error=" . urlencode("Username '$username_pasien' tidak ditemukan atau bukan role pasien"));
        exit();
    }
    $stmt->close();
}

try {
    // Generate no_rm jika kosong
    if (empty($no_rm)) {
        // Ambil no_rm terakhir
        $result = $conn->query("SELECT no_rm FROM pasien ORDER BY created_at DESC LIMIT 1");
        if ($result->num_rows > 0) {
            $last_rm = $result->fetch_assoc()['no_rm'];
            // Extract nomor dari RM2025001 -> 2025001
            $last_number = intval(substr($last_rm, 2));
            $new_number = $last_number + 1;
            $no_rm = 'RM' . str_pad($new_number, 7, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada data, mulai dari RM2025001
            $no_rm = 'RM2025001';
        }
    }

    // Cek apakah NIK sudah terdaftar
    $stmt = $conn->prepare("SELECT no_rm FROM pasien WHERE nik = ?");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // NIK sudah terdaftar, ambil no_rm yang ada
        $existing = $result->fetch_assoc();
        $no_rm = $existing['no_rm'];
        $stmt->close();

        // Insert ke tabel pendaftaran untuk pasien lama
        $stmt = $conn->prepare("INSERT INTO pendaftaran (no_rm, keluhan, petugas_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $no_rm, $keluhan, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../pages/pendaftaran.php?success=" . urlencode("Pasien lama berhasil didaftarkan dengan No. RM: $no_rm"));
            exit();
        } else {
            throw new Exception("Gagal mendaftarkan pasien: " . $stmt->error);
        }
    } else {
        $stmt->close();

        // Insert pasien baru
        if ($user_id !== null) {
            // Dengan user_id
            $stmt = $conn->prepare("INSERT INTO pasien (no_rm, nik, nama, tanggal_lahir, jenis_kelamin, alamat, telepon, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $no_rm, $nik, $nama, $tanggal_lahir, $jenis_kelamin, $alamat, $telepon, $user_id);
        } else {
            // Tanpa user_id
            $stmt = $conn->prepare("INSERT INTO pasien (no_rm, nik, nama, tanggal_lahir, jenis_kelamin, alamat, telepon) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $no_rm, $nik, $nama, $tanggal_lahir, $jenis_kelamin, $alamat, $telepon);
        }

        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data pasien: " . $stmt->error);
        }
        $stmt->close();

        // Insert ke tabel pendaftaran
        $stmt = $conn->prepare("INSERT INTO pendaftaran (no_rm, keluhan, petugas_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $no_rm, $keluhan, $_SESSION['user_id']);

        if (!$stmt->execute()) {
            throw new Exception("Gagal mendaftarkan pasien: " . $stmt->error);
        }
        $stmt->close();

        $conn->close();
        header("Location: ../pages/pendaftaran.php?success=" . urlencode("Pasien baru berhasil didaftarkan dengan No. RM: $no_rm"));
        exit();
    }

} catch (Exception $e) {
    $conn->close();
    header("Location: ../pages/pendaftaran.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
