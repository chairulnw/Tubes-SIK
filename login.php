<?php
/**
 * Halaman Login
 * Sistem Informasi Klinik - Milestone 1
 */

require_once 'includes/config.php';
require_once 'includes/session.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

if (isset($_GET['success'])) {
    $success = 'Registrasi berhasil! Silakan login dengan akun Anda.';
}

if (isset($_GET['logout'])) {
    $success = 'Anda telah berhasil logout.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🏥 Sistem Informasi Klinik</h1>
                <h2>Login</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="handlers/login_process.php" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus placeholder="Masukkan username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
