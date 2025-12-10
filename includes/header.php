<?php
/**
 * Header Component
 * Sistem Informasi Klinik - Milestone 1
 */

// Ambil inisial nama untuk avatar
$nama_lengkap = $_SESSION['nama_lengkap'] ?? 'User';
$initial = strtoupper(substr($nama_lengkap, 0, 2));
?>
<style>
    .navbar {
        background: #2c3e50;
        color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 64px;
    }

    .nav-brand {
        color: white;
        font-size: 22px;
        font-weight: 600;
        margin: 0;
    }

    .nav-menu {
        display: flex;
        gap: 5px;
        align-items: center;
        flex: 1;
        margin: 0 30px;
    }

    .nav-link {
        text-decoration: none;
        color: rgba(255,255,255,0.9);
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .nav-link:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #667eea;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        color: white;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: white;
        line-height: 1.2;
    }

    .user-role {
        font-size: 12px;
        color: rgba(255,255,255,0.7);
        line-height: 1.2;
    }

    .btn-logout {
        background: #e74c3c;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-logout:hover {
        background: #c0392b;
    }

    @media (max-width: 1024px) {
        .nav-menu {
            gap: 2px;
            margin: 0 15px;
        }

        .nav-link {
            padding: 6px 12px;
            font-size: 13px;
        }
    }

    @media (max-width: 768px) {
        .nav-container {
            flex-wrap: wrap;
            padding: 10px 20px;
        }

        .nav-brand {
            font-size: 18px;
        }

        .nav-menu {
            order: 3;
            width: 100%;
            flex-direction: column;
            gap: 5px;
            margin: 10px 0 0 0;
        }

        .nav-link {
            width: 100%;
            text-align: center;
        }

        .user-info {
            display: none;
        }
    }
</style>

<nav class="navbar">
    <div class="nav-container">
        <h1 class="nav-brand">Klinik Sehat</h1>

        <div class="nav-menu">
            <a href="<?php echo SITE_URL; ?>/dashboard.php" class="nav-link">Dashboard</a>
        </div>

        <div class="nav-user">
            <div class="user-avatar"><?php echo $initial; ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($nama_lengkap); ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
            </div>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
</nav>
