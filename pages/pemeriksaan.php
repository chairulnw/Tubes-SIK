<?php
/**
 * Form Pemeriksaan
 * Sistem Informasi Klinik - Milestone 1
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan dokter yang bisa akses
requireRole(['admin', 'dokter']);

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemeriksaan - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Form Pemeriksaan Pasien</h1>
            <p class="subtitle">Input data pemeriksaan dan diagnosis pasien</p>

            <form method="post" action="../handlers/pemeriksaan_process.php" class="medical-form">
                <div class="form-section">
                    <h2>Informasi Pemeriksaan</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nomor Rekam Medis <span class="required">*</span></label>
                            <input type="text" name="no_rm" required placeholder="Contoh: RM2025001">
                        </div>

                        <div class="form-group">
                            <label>Tanggal Pemeriksaan <span class="required">*</span></label>
                            <input type="date" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Dokter <span class="required">*</span></label>
                        <input type="text" name="dokter" required value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Tanda Vital</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tekanan Darah (mmHg)</label>
                            <input type="text" name="tekanan_darah" placeholder="120/80">
                        </div>

                        <div class="form-group">
                            <label>Suhu (°C)</label>
                            <input type="number" name="suhu" step="0.1" placeholder="36.5">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nadi (x/menit)</label>
                            <input type="number" name="nadi" placeholder="80">
                        </div>

                        <div class="form-group">
                            <label>Pernapasan (x/menit)</label>
                            <input type="number" name="pernapasan" placeholder="20">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Berat Badan (kg)</label>
                            <input type="number" name="berat_badan" step="0.1" placeholder="70.5">
                        </div>

                        <div class="form-group">
                            <label>Tinggi Badan (cm)</label>
                            <input type="number" name="tinggi_badan" placeholder="170">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Diagnosis dan Terapi</h2>
                    <div class="form-group">
                        <label>Anamnesis <span class="required">*</span></label>
                        <textarea name="anamnesis" required rows="4" placeholder="Riwayat penyakit sekarang, keluhan utama..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Diagnosis <span class="required">*</span></label>
                        <textarea name="diagnosis" required rows="3" placeholder="Diagnosis penyakit..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Terapi <span class="required">*</span></label>
                        <textarea name="terapi" required rows="4" placeholder="Rencana terapi dan tindakan..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Pemeriksaan</button>
                    <button type="reset" class="btn btn-secondary">Reset Form</button>
                    <a href="../dashboard.php" class="btn btn-light">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
