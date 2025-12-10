<?php
/**
 * File Session Management
 */

require_once __DIR__ . '/config.php';

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login
 * @return bool True jika sudah login, false jika belum
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Cek apakah user memiliki role tertentu
 * @param string|array $allowed_roles Role atau array of roles yang diizinkan
 * @return bool True jika user memiliki role yang sesuai
 */
function hasRole($allowed_roles) {
    if (!isLoggedIn()) {
        return false;
    }

    $user_role = $_SESSION['role'];

    // Jika admin, bisa akses semua
    if ($user_role === 'admin') {
        return true;
    }

    // Jika allowed_roles adalah array, cek apakah user_role ada di dalamnya
    if (is_array($allowed_roles)) {
        return in_array($user_role, $allowed_roles);
    }

    // Jika allowed_roles adalah string, bandingkan langsung
    return $user_role === $allowed_roles;
}

/**
 * Redirect ke login jika belum login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        // Gunakan path relatif untuk menghindari redirect loop
        $redirect = (strpos($_SERVER['REQUEST_URI'], '/pages/') !== false) ? '../login.php' : 'login.php';
        header("Location: $redirect");
        exit();
    }
}

/**
 * Redirect jika user tidak memiliki role yang diperlukan
 * @param string|array $allowed_roles Role atau array of roles yang diizinkan
 */
function requireRole($allowed_roles) {
    requireLogin();

    if (!hasRole($allowed_roles)) {
        // Gunakan path relatif
        $redirect = (strpos($_SERVER['REQUEST_URI'], '/pages/') !== false) ? '../dashboard.php?error=access_denied' : 'dashboard.php?error=access_denied';
        header("Location: $redirect");
        exit();
    }
}

/**
 * Get informasi user yang sedang login
 * @return array|null Array berisi data user atau null jika tidak login
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id_user' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'nama_lengkap' => $_SESSION['nama_lengkap'],
        'email' => $_SESSION['email'] ?? null,
        'no_rm' => $_SESSION['no_rm'] ?? null
    ];
}

/**
 * Destroy session dan logout
 */
function logout() {
    // Hapus semua session variables
    session_unset();

    // Destroy session
    session_destroy();

    // Redirect ke login
    header("Location: login.php?logout=1");
    exit();
}

?>
