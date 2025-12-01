<?php
/**
 * Halaman Register
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

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🏥 Sistem Informasi Klinik</h1>
                <h2>Registrasi Akun Baru</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="handlers/register_process.php">
                <div class="form-group">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username" required placeholder="Minimal 4 karakter">
                    <small>Hanya huruf, angka, dan underscore</small>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                    <small>Harus ada huruf besar, kecil, dan angka</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ketik ulang password">
                </div>

                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Nama lengkap Anda">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="contoh@email.com (opsional)">
                </div>

                <div class="form-group">
                    <label for="role">Role / Jabatan <span class="required">*</span></label>
                    <select id="role" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="pendaftaran">Staf Pendaftaran</option>
                        <option value="dokter">Dokter</option>
                        <option value="farmasi">Staf Farmasi</option>
                        <option value="laboratorium">Staf Laboratorium</option>
                        <option value="kasir">Kasir</option>
                        <option value="pasien">Pasien</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>

            <p class="register-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>

    <script>
        // Validasi sederhana form register
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;

            if (password !== confirm) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('Password minimal 8 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>
