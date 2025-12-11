<?php
/**
 * Form Pemeriksaan
 * Sistem Informasi Klinik - Milestone 1
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan dokter yang bisa akses
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if (!in_array($_SESSION['role'], ['admin', 'dokter'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses ke halaman ini"));
    exit();
}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Get current user
$user = getCurrentUser();

// Get jenis tes lab untuk dropdown
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id, nama_tes, kategori, biaya_standar FROM jenis_tes_lab WHERE aktif = 1 ORDER BY kategori, nama_tes");
$stmt->execute();
$result = $stmt->get_result();
$jenis_tes_lab = [];
while ($row = $result->fetch_assoc()) {
    $jenis_tes_lab[] = $row;
}
$stmt->close();
$conn->close();
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

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

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
                    <h2>Anamnesis dan Diagnosis</h2>
                    <div class="form-group">
                        <label>Anamnesis <span class="required">*</span></label>
                        <textarea name="anamnesis" required rows="4" placeholder="Riwayat penyakit sekarang, keluhan utama..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Diagnosis <span class="required">*</span></label>
                        <textarea name="diagnosis" required rows="3" placeholder="Diagnosis penyakit..."></textarea>
                    </div>
                </div>

                <!-- Section Rujukan -->
                <div class="form-section">
                    <h2>Rujukan</h2>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="butuh_lab" name="butuh_lab" value="1">
                            Rujuk ke Laboratorium
                        </label>
                    </div>

                    <div id="lab_section" style="display: none; margin-left: 25px;">
                        <div class="form-group">
                            <label for="catatan_lab">Catatan untuk Petugas Lab</label>
                            <textarea name="catatan_lab" id="catatan_lab" rows="3" class="form-control" placeholder="Tulis catatan pemeriksaan lab yang diperlukan"></textarea>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label>
                            <input type="checkbox" id="butuh_farmasi" name="butuh_farmasi" value="1">
                            Rujuk ke Farmasi
                        </label>
                    </div>

                    <div id="farmasi_section" style="display: none; margin-left: 25px;">
                        <div class="form-group">
                            <label for="catatan_farmasi">Catatan untuk Petugas Farmasi</label>
                            <textarea name="catatan_farmasi" id="catatan_farmasi" rows="3" class="form-control" placeholder="Tulis resep obat dan dosis"></textarea>
                        </div>
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

    <style>
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .info-box strong {
            color: #0056b3;
        }
    </style>

    <script>
        // Show/hide lab section
        document.getElementById('butuh_lab').addEventListener('change', function() {
            document.getElementById('lab_section').style.display = this.checked ? 'block' : 'none';
        });

        // Show/hide farmasi section
        document.getElementById('butuh_farmasi').addEventListener('change', function() {
            document.getElementById('farmasi_section').style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html>
