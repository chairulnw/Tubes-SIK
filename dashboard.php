<?php
/**
 * Dashboard Utama
 * Sistem Informasi Klinik - Milestone 1
 */

require_once 'includes/config.php';
require_once 'includes/session.php';

// Pastikan user sudah login
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="dashboard">
            <div class="welcome-section">
                <h1>Selamat Datang, <?php echo htmlspecialchars($user['nama_lengkap']); ?>!</h1>
                <p class="role-badge role-<?php echo $user['role']; ?>">
                    Role: <?php echo strtoupper($user['role']); ?>
                </p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'access_denied'): ?>
                <div class="alert alert-error">
                    Anda tidak memiliki akses ke halaman tersebut.
                </div>
            <?php endif; ?>

            <div class="menu-grid">
                <?php if (hasRole(['admin', 'pendaftaran'])): ?>
                    <a href="pages/pendaftaran.php" class="menu-card card-green">
                        <div class="card-icon">📋</div>
                        <h3>Pendaftaran Pasien</h3>
                        <p>Daftar pasien baru atau pasien lama untuk pemeriksaan</p>
                    </a>
                <?php endif; ?>

                <?php if (hasRole(['admin', 'dokter'])): ?>
                    <a href="pages/pemeriksaan.php" class="menu-card card-blue">
                        <div class="card-icon">🩺</div>
                        <h3>Pemeriksaan</h3>
                        <p>Input data pemeriksaan dan diagnosis pasien</p>
                    </a>
                <?php endif; ?>

                <?php if (hasRole(['admin', 'farmasi'])): ?>
                    <a href="pages/farmasi.php" class="menu-card card-red">
                        <div class="card-icon">💊</div>
                        <h3>Farmasi</h3>
                        <p>Kelola resep dan dispensing obat pasien</p>
                    </a>
                <?php endif; ?>

                <?php if (hasRole(['admin', 'kasir'])): ?>
                    <a href="pages/pembayaran.php" class="menu-card card-orange">
                        <div class="card-icon">💳</div>
                        <h3>Pembayaran</h3>
                        <p>Proses pembayaran dan invoice pasien</p>
                    </a>
                <?php endif; ?>

                <?php if (hasRole(['admin', 'laboratorium'])): ?>
                    <a href="pages/laboratorium.php" class="menu-card card-purple">
                        <div class="card-icon">🔬</div>
                        <h3>Laboratorium</h3>
                        <p>Input dan kelola hasil pemeriksaan laboratorium</p>
                    </a>
                <?php endif; ?>

                <?php if (hasRole('pasien')): ?>
                    <a href="pages/pasien_dashboard.php" class="menu-card card-teal">
                        <div class="card-icon">👤</div>
                        <h3>Data Medis Saya</h3>
                        <p>Lihat riwayat pemeriksaan dan data wearable</p>
                    </a>
                <?php endif; ?>
            </div>

            <?php if (hasRole('admin')): ?>
                <div class="admin-section">
                    <h2>Akses Administrator</h2>
                    <div class="menu-grid">
                        <a href="pages/user_management.php" class="menu-card card-dark">
                            <div class="card-icon">👥</div>
                            <h3>Kelola User</h3>
                            <p>Manajemen user dan hak akses</p>
                        </a>
                        <a href="pages/reports.php" class="menu-card card-dark">
                            <div class="card-icon">📊</div>
                            <h3>Laporan</h3>
                            <p>View dan export laporan klinik</p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
