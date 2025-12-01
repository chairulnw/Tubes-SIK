<?php
/**
 * Handler untuk proses registrasi
 * Sistem Informasi Klinik - Milestone 1
 */

require_once '../includes/config.php';
require_once '../includes/auth.php';

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit();
}

// Ambil data dari form
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$nama_lengkap = trim($_POST['nama_lengkap']);
$email = trim($_POST['email']);
$role = $_POST['role'];

// Validasi input
$errors = [];

// Cek field wajib
if (empty($username) || empty($password) || empty($nama_lengkap) || empty($role)) {
    $errors[] = "Semua field wajib harus diisi";
}

// Validasi username
$username_errors = validateUsername($username);
if (!empty($username_errors)) {
    $errors = array_merge($errors, $username_errors);
}

// Cek konfirmasi password
if ($password !== $confirm_password) {
    $errors[] = "Password dan konfirmasi password tidak cocok";
}

// Validasi password
$password_errors = validatePassword($password);
if (!empty($password_errors)) {
    $errors = array_merge($errors, $password_errors);
}

// Validasi email jika diisi
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid";
}

// Validasi role
$valid_roles = ['pendaftaran', 'dokter', 'farmasi', 'laboratorium', 'kasir', 'pasien'];
if (!in_array($role, $valid_roles)) {
    $errors[] = "Role tidak valid";
}

// Jika ada error, redirect ke register dengan error message
if (!empty($errors)) {
    $error = implode('. ', $errors);
    header("Location: ../register.php?error=" . urlencode($error));
    exit();
}

// Jika email kosong, set ke null
$email = empty($email) ? null : $email;

// Register user
$result = registerUser($username, $password, $role, $nama_lengkap, $email);

if ($result['success']) {
    // Registrasi berhasil, redirect ke login
    header("Location: ../login.php?success=1");
    exit();
} else {
    // Registrasi gagal, redirect ke register dengan error message
    header("Location: ../register.php?error=" . urlencode($result['message']));
    exit();
}
?>
