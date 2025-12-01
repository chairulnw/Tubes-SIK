<?php
/**
 * Index / Landing Page
 * Sistem Informasi Klinik - Milestone 1
 * Redirect ke dashboard jika sudah login, atau ke login jika belum
 */

require_once 'includes/config.php';
require_once 'includes/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>
