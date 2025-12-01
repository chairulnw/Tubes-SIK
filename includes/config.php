<?php
/**
 * File Konfigurasi Database
 * Sistem Informasi Klinik - Milestone 1
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'SIK');

// Site URL - sesuaikan dengan lokasi project Anda
// Contoh: 'http://localhost/Tubes-SIK' atau 'http://localhost:8000'
define('SITE_URL', 'http://localhost/Tubes-SIK');

/**
 * Fungsi untuk mendapatkan koneksi database
 * @return mysqli Database connection object
 */
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }

    // Set charset ke utf8mb4 untuk mendukung karakter Unicode penuh
    $conn->set_charset("utf8mb4");

    return $conn;
}

/**
 * Fungsi untuk menutup koneksi database
 * @param mysqli $conn Database connection object
 */
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Timezone setting
date_default_timezone_set('Asia/Jakarta');

// Error reporting untuk development (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Matikan agar tidak ganggu header redirect

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set ke 1 jika menggunakan HTTPS
ini_set('session.cookie_samesite', 'Lax');

?>
