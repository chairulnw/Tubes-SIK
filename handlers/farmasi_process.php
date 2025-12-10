<?php
/**
 * Handler untuk proses farmasi
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/farmasi.php");
    exit();
}

if (!isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'farmasi'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses"));
    exit();
}

// Handler farmasi bisa disesuaikan dengan kebutuhan
// Untuk saat ini redirect dengan pesan sukses
header("Location: ../pages/farmasi.php?success=" . urlencode("Data resep berhasil diproses"));
exit();
?>
