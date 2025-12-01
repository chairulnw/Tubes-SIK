<?php
/**
 * Header Component
 * Sistem Informasi Klinik - Milestone 1
 */

if (!isset($user)) {
    $user = getCurrentUser();
}
?>
<nav class="main-nav">
    <div class="nav-container">
        <div class="nav-brand">
            <a href="<?php echo SITE_URL; ?>/dashboard.php">
                <h2>🏥 Klinik Sehat</h2>
            </a>
        </div>

        <ul class="nav-menu">
            <li><a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a></li>

            <?php if (hasRole(['admin', 'pendaftaran'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/pendaftaran.php">Pendaftaran</a></li>
            <?php endif; ?>

            <?php if (hasRole(['admin', 'dokter'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/pemeriksaan.php">Pemeriksaan</a></li>
            <?php endif; ?>

            <?php if (hasRole(['admin', 'farmasi'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/farmasi.php">Farmasi</a></li>
            <?php endif; ?>

            <?php if (hasRole(['admin', 'kasir'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/pembayaran.php">Pembayaran</a></li>
            <?php endif; ?>

            <?php if (hasRole(['admin', 'laboratorium'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/laboratorium.php">Lab</a></li>
            <?php endif; ?>

            <li class="nav-user">
                <span class="user-name"><?php echo htmlspecialchars($user['nama_lengkap']); ?></span>
                <span class="user-role">(<?php echo ucfirst($user['role']); ?>)</span>
                <a href="<?php echo SITE_URL; ?>/logout.php" class="btn-logout">Logout</a>
            </li>
        </ul>

        <div class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>
