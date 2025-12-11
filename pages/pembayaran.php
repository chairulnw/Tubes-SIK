<?php
/**
 * Form Pembayaran
 * Sistem Informasi Klinik - Milestone 1
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan kasir yang bisa akses
requireRole(['admin', 'kasir']);

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembayaran - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Form Pembayaran</h1>
            <p class="subtitle">Proses pembayaran dan invoice pasien</p>

            <form method="post" action="../handlers/pembayaran_process.php" class="medical-form">
                <div class="form-section">
                    <h2>Informasi Pembayaran</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nomor Invoice <span class="required">*</span></label>
                            <input type="text" name="no_invoice" readonly value="(Auto-generate)" class="readonly-field" style="background-color: #f0f0f0; cursor: not-allowed;">
                            <small>Nomor invoice akan digenerate otomatis oleh sistem</small>
                        </div>

                        <div class="form-group">
                            <label>Nomor Rekam Medis <span class="required">*</span></label>
                            <input type="text" name="no_rm" required placeholder="Contoh: RM2025001">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Pembayaran <span class="required">*</span></label>
                            <input type="date" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label>Nama Kasir <span class="required">*</span></label>
                            <input type="text" name="kasir" required value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Rincian Biaya</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Biaya Pendaftaran (Rp) <span class="required">*</span></label>
                            <input type="number" name="biaya_pendaftaran" required min="0" value="50000" class="biaya-input">
                        </div>

                        <div class="form-group">
                            <label>Biaya Konsultasi (Rp) <span class="required">*</span></label>
                            <input type="number" name="biaya_konsultasi" required min="0" value="100000" class="biaya-input">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Biaya Obat (Rp)</label>
                            <input type="number" name="biaya_obat" min="0" value="0" class="biaya-input">
                        </div>

                        <div class="form-group">
                            <label>Biaya Laboratorium (Rp)</label>
                            <input type="number" name="biaya_lab" min="0" value="0" class="biaya-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Total Bayar (Rp) <span class="required">*</span></label>
                        <input type="number" name="total" required min="0" id="totalBayar" readonly class="total-display">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Metode Pembayaran</h2>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px;">Pilih Metode Pembayaran <span class="required">*</span></label>
                        <div class="radio-group payment-methods">
                            <label><input type="radio" name="metode" value="tunai" required checked> Tunai</label>
                            <label><input type="radio" name="metode" value="debit" required> Kartu Debit</label>
                            <label><input type="radio" name="metode" value="kredit" required> Kartu Kredit</label>
                            <label><input type="radio" name="metode" value="bpjs" required> BPJS</label>
                            <label><input type="radio" name="metode" value="asuransi" required> Asuransi</label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Proses Pembayaran</button>
                    <button type="reset" class="btn btn-secondary">Reset Form</button>
                    <a href="../dashboard.php" class="btn btn-light">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Auto calculate total
        function hitungTotal() {
            const pendaftaran = parseInt(document.querySelector('[name="biaya_pendaftaran"]').value) || 0;
            const konsultasi = parseInt(document.querySelector('[name="biaya_konsultasi"]').value) || 0;
            const obat = parseInt(document.querySelector('[name="biaya_obat"]').value) || 0;
            const lab = parseInt(document.querySelector('[name="biaya_lab"]').value) || 0;

            const total = pendaftaran + konsultasi + obat + lab;
            document.getElementById('totalBayar').value = total;
        }

        // Add event listeners to all biaya inputs
        document.querySelectorAll('.biaya-input').forEach(input => {
            input.addEventListener('input', hitungTotal);
        });

        // Calculate initial total
        hitungTotal();
    </script>
</body>
</html>
