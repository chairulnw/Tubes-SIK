<?php
/**
 * Dashboard Utama
 * Sistem Informasi Klinik
 */

require_once 'includes/config.php';
require_once 'includes/session.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
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
    <title>Dashboard - Sistem Informasi Klinik</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .dashboard-container {
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
            width: 100%;
        }

        .dashboard-header {
            margin-bottom: 40px;
        }

        .dashboard-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .dashboard-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: #667eea;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }

        .dashboard-card:hover::before {
            transform: scaleX(1);
        }

        .card-title {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .card-description {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #667eea;
        }

        .card-action::after {
            content: '→';
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover .card-action::after {
            transform: translateX(4px);
        }

        /* Admin Section */
        .admin-section {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid #e5e7eb;
        }

        .admin-section h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 24px;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* Footer */
        .main-footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }

        .main-footer p {
            margin: 0;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 24px 16px;
            }

            .dashboard-header h1 {
                font-size: 24px;
            }

            .cards-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p style="color: #6b7280; margin-top: 8px;">Selamat datang di Sistem Informasi Klinik Sehat</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Main Menu Cards -->
        <div class="cards-grid">
            <?php if (in_array($_SESSION['role'], ['admin', 'pendaftaran'])): ?>
                <a href="pages/pendaftaran.php" class="dashboard-card">
                    <h3 class="card-title">Pendaftaran Pasien</h3>
                    <p class="card-description">Daftar pasien baru atau pasien lama untuk pemeriksaan</p>
                    <span class="card-action">Buka Halaman</span>
                </a>
            <?php endif; ?>

            <?php if (in_array($_SESSION['role'], ['admin', 'dokter'])): ?>
                <a href="pages/pemeriksaan.php" class="dashboard-card">
                    <h3 class="card-title">Pemeriksaan</h3>
                    <p class="card-description">Input data pemeriksaan dan diagnosis pasien</p>
                    <span class="card-action">Buka Halaman</span>
                </a>
            <?php endif; ?>

            <?php if (in_array($_SESSION['role'], ['admin', 'farmasi'])): ?>
                <a href="pages/farmasi.php" class="dashboard-card">
                    <h3 class="card-title">Farmasi</h3>
                    <p class="card-description">Kelola resep dan dispensing obat pasien</p>
                    <span class="card-action">Buka Halaman</span>
                </a>
            <?php endif; ?>

            <?php if (in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
                <a href="pages/pembayaran.php" class="dashboard-card">
                    <h3 class="card-title">Pembayaran</h3>
                    <p class="card-description">Proses pembayaran dan invoice pasien</p>
                    <span class="card-action">Buka Halaman</span>
                </a>
            <?php endif; ?>

            <?php if (in_array($_SESSION['role'], ['pasien'])): ?>
                <a href="pages/data-medis.php" class="dashboard-card">
                    <h3 class="card-title">Data Medis Saya</h3>
                    <p class="card-description">Lihat riwayat pemeriksaan dan data wearable</p>
                    <span class="card-action">Buka Halaman</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Admin Section -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="admin-section">
                <h2>Administrator</h2>
                <div class="cards-grid">
                    <a href="pages/kelola-user.php" class="dashboard-card">
                        <h3 class="card-title">Kelola User</h3>
                        <p class="card-description">Manajemen user dan hak akses sistem</p>
                        <span class="card-action">Buka Halaman</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <p>&copy; 2025 Sistem Informasi Klinik. All rights reserved.</p>
    </footer>
</body>
</html>
