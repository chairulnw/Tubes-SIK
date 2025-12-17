<?php
/**
 * Form Pemeriksaan Pasien
 * Sistem Informasi Klinik
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

// Ambil no_rm dari parameter
$no_rm = isset($_GET['no_rm']) ? $_GET['no_rm'] : '';

if (empty($no_rm)) {
    header("Location: pemeriksaan.php?error=" . urlencode("No. RM tidak ditemukan"));
    exit();
}

$conn = getDBConnection();

// Ambil data pasien
$stmt = $conn->prepare("
    SELECT p.*, pend.keluhan, pend.tanggal_daftar
    FROM pasien p
    LEFT JOIN pendaftaran pend ON p.no_rm = pend.no_rm
    WHERE p.no_rm = ?
    ORDER BY pend.tanggal_daftar DESC
    LIMIT 1
");
$stmt->bind_param("s", $no_rm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: pemeriksaan.php?error=" . urlencode("Pasien tidak ditemukan"));
    exit();
}

$pasien = $result->fetch_assoc();
$stmt->close();

// Get jenis tes lab untuk dropdown
$stmt = $conn->prepare("SELECT id, nama_tes, kategori, biaya_standar FROM jenis_tes_lab WHERE aktif = 1 ORDER BY kategori, nama_tes");
$stmt->execute();
$result = $stmt->get_result();
$jenis_tes_lab = [];
while ($row = $result->fetch_assoc()) {
    $jenis_tes_lab[] = $row;
}
$stmt->close();
$conn->close();

// Get current user
$user = getCurrentUser();

// Hitung usia
$lahir = new DateTime($pasien['tanggal_lahir']);
$sekarang = new DateTime();
$usia = $sekarang->diff($lahir)->y;
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

            <!-- Info Pasien -->
            <div class="patient-info-card">
                <h3>Informasi Pasien</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">No. RM:</span>
                        <span class="info-value"><strong><?php echo htmlspecialchars($pasien['no_rm']); ?></strong></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama:</span>
                        <span class="info-value"><?php echo htmlspecialchars($pasien['nama']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenis Kelamin:</span>
                        <span class="info-value"><?php echo $pasien['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Usia:</span>
                        <span class="info-value"><?php echo $usia; ?> tahun</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Keluhan:</span>
                        <span class="info-value"><?php echo nl2br(htmlspecialchars($pasien['keluhan'])); ?></span>
                    </div>
                </div>
            </div>

            <form method="post" action="../handlers/pemeriksaan_process.php" class="medical-form">
                <!-- Hidden field untuk no_rm -->
                <input type="hidden" name="no_rm" value="<?php echo htmlspecialchars($pasien['no_rm']); ?>">

                <div class="form-section">
                    <h2>Informasi Pemeriksaan</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Pemeriksaan <span class="required">*</span></label>
                            <input type="date" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label>Nama Dokter <span class="required">*</span></label>
                            <input type="text" name="dokter" required value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>">
                        </div>
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
                        <textarea name="anamnesis" required rows="4" placeholder="Riwayat penyakit sekarang, keluhan utama..."><?php echo htmlspecialchars($pasien['keluhan']); ?></textarea>
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
                        <label class="checkbox-label">
                            <input type="checkbox" id="butuh_lab" name="butuh_lab" value="1">
                            <span>Rujuk ke Laboratorium</span>
                        </label>
                    </div>

                    <div id="lab_section" style="display: none; margin-left: 25px;">
                        <div class="form-group">
                            <label for="catatan_lab">Catatan untuk Petugas Lab</label>
                            <textarea name="catatan_lab" id="catatan_lab" rows="3" class="form-control" placeholder="Tulis catatan pemeriksaan lab yang diperlukan"></textarea>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label class="checkbox-label">
                            <input type="checkbox" id="butuh_farmasi" name="butuh_farmasi" value="1">
                            <span>Rujuk ke Farmasi</span>
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
                    <a href="pemeriksaan.php" class="btn btn-light">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <style>
        .form-container {
            min-width: 800px;
            max-width: 1200px;
        }

        .patient-info-card {
            background: #f8f9fa;
            border: 2px solid #3498db;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .patient-info-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 18px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .info-value {
            font-size: 14px;
            color: #2c3e50;
        }

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

        /* Checkbox alignment */
        .checkbox-label {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            cursor: pointer;
            margin-bottom: 0 !important;
        }

        .checkbox-label input[type="checkbox"] {
            margin: 0 !important;
            padding: 0 !important;
            cursor: pointer;
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .checkbox-label span {
            font-weight: 500;
            line-height: 1.5;
        }

        .form-section .form-group label.checkbox-label {
            display: flex !important;
            align-items: center !important;
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
