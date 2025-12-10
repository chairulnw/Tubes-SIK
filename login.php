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
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Sistem Informasi Klinik</h1>
                <h2>Login</h2>
                <p class="subtitle">Silakan masuk dengan akun Anda</p>
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

            <form method="POST" action="handlers/login_process.php" class="auth-form">
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

            <p class="auth-footer-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </div>
    </div>

    <style>
        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        .auth-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 50px 50px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .auth-header h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .auth-form {
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5568d3;
        }

        .btn-block {
            width: 100%;
            display: block;
        }

        .auth-footer-link {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .auth-footer-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 576px) {
            .auth-container {
                padding: 40px 25px;
            }
        }
    </style>
</body>
</html>
