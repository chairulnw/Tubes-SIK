<?php
/**
 * Form Pendaftaran Pasien
 * Sistem Informasi Klinik - Milestone 1
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan staf pendaftaran yang bisa akses
requireRole(['admin', 'pendaftaran']);

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Pasien - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Form Pendaftaran Pasien</h1>
            <p class="subtitle">Daftarkan pasien baru atau pasien lama untuk pemeriksaan</p>

            <form method="post" action="../handlers/pendaftaran_process.php" class="medical-form">
                <div class="form-section">
                    <h2>Data Rekam Medis</h2>
                    <div class="form-group">
                        <label>Nomor Rekam Medis</label>
                        <input type="text" name="no_rm" placeholder="Contoh: RM2025001">
                        <small>Kosongkan jika pasien baru, sistem akan generate otomatis</small>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Data Pribadi Pasien</h2>
                    <div class="form-group">
                        <label>NIK <span class="required">*</span></label>
                        <input type="text" name="nik" required maxlength="20">
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Lahir <span class="required">*</span></label>
                            <input type="date" name="tanggal_lahir" required>
                        </div>

                        <div class="form-group">
                            <label>Jenis Kelamin <span class="required">*</span></label>
                            <div class="radio-group">
                                <label><input type="radio" name="jenis_kelamin" value="L" required> Laki-laki</label>
                                <label><input type="radio" name="jenis_kelamin" value="P" required> Perempuan</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat <span class="required">*</span></label>
                        <textarea name="alamat" required rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon <span class="required">*</span></label>
                        <input type="tel" name="telepon" required>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Keluhan</h2>
                    <div class="form-group">
                        <label>Keluhan Pasien <span class="required">*</span></label>
                        <textarea name="keluhan" required rows="4" placeholder="Deskripsikan keluhan pasien..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Daftar Pasien</button>
                    <button type="reset" class="btn btn-secondary">Reset Form</button>
                    <a href="../dashboard.php" class="btn btn-light">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
