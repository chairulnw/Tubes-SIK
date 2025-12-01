<?php
/**
 * Handler untuk proses login
 * Sistem Informasi Klinik - Milestone 1
 */

// Matikan error display dan gunakan output buffering
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once '../includes/config.php';
require_once '../includes/auth.php';

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

// Ambil data dari form
$username = trim($_POST['username']);
$password = $_POST['password'];

// Validasi input kosong
if (empty($username) || empty($password)) {
    header("Location: ../login.php?error=" . urlencode("Username dan password harus diisi"));
    exit();
}

// Coba login
$result = loginUser($username, $password);

// Bersihkan output buffer
ob_end_clean();

if ($result['success']) {
    // Login berhasil, redirect ke dashboard
    header("Location: ../dashboard.php");
    exit();
} else {
    // Login gagal, redirect ke login dengan error message
    $error = urlencode($result['message']);
    header("Location: ../login.php?error=" . $error);
    exit();
}
