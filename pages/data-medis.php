<?php
/**
 * Halaman Data Medis Pasien
 * Sistem Informasi Klinik
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Ambil data pasien dari session user_id
$conn = getDBConnection();
$pasien_data = null;
$riwayat_pemeriksaan = [];
$wearable_data = [];

try {
    // Ambil data pasien berdasarkan user_id atau no_rm dari session
    if (isset($_SESSION['no_rm'])) {
        // Jika sudah ada no_rm di session (dari login), langsung ambil
        $stmt = $conn->prepare("SELECT * FROM pasien WHERE no_rm = ?");
        $stmt->bind_param("s", $_SESSION['no_rm']);
    } else {
        // Jika belum, cari berdasarkan user_id
        $stmt = $conn->prepare("SELECT * FROM pasien WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $pasien_data = $result->fetch_assoc();
    $stmt->close();

    if ($pasien_data) {
        // Ambil riwayat pemeriksaan
        $stmt = $conn->prepare("
            SELECT p.*, u.nama_lengkap as nama_dokter
            FROM pemeriksaan p
            LEFT JOIN users u ON p.dokter_id = u.id
            WHERE p.no_rm = ?
            ORDER BY p.tanggal_pemeriksaan DESC
            LIMIT 10
        ");
        $stmt->bind_param("s", $pasien_data['no_rm']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $riwayat_pemeriksaan[] = $row;
        }
        $stmt->close();

        // Ambil data wearable terbaru
        $stmt = $conn->prepare("
            SELECT * FROM wearable_data
            WHERE no_rm = ?
            ORDER BY waktu_pembacaan DESC
            LIMIT 5
        ");
        $stmt->bind_param("s", $pasien_data['no_rm']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $wearable_data[] = $row;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
}

closeDBConnection($conn);

// Hitung umur
function hitungUmur($tanggal_lahir) {
    $lahir = new DateTime($tanggal_lahir);
    $sekarang = new DateTime();
    $umur = $sekarang->diff($lahir);
    return $umur->y;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Medis Saya - Sistem Informasi Klinik</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="data-medis-container">
            <h1>Data Medis Saya</h1>
            <p class="subtitle">Riwayat pemeriksaan dan data kesehatan pasien</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!$pasien_data): ?>
                <div class="alert alert-info">
                    Data pasien tidak ditemukan. Silakan hubungi staf pendaftaran untuk mendaftarkan data Anda.
                </div>
                <div class="action-buttons">
                    <a href="../dashboard.php" class="btn btn-light">Kembali ke Dashboard</a>
                </div>
            <?php else: ?>
                <!-- Informasi Pasien -->
                <div class="patient-info-card">
                    <h2>Informasi Pasien</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nomor Rekam Medis:</span>
                            <span class="info-value"><?php echo htmlspecialchars($pasien_data['no_rm']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NIK:</span>
                            <span class="info-value"><?php echo htmlspecialchars($pasien_data['nik']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nama:</span>
                            <span class="info-value"><?php echo htmlspecialchars($pasien_data['nama']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal Lahir:</span>
                            <span class="info-value">
                                <?php
                                    echo date('d F Y', strtotime($pasien_data['tanggal_lahir']));
                                    echo ' (' . hitungUmur($pasien_data['tanggal_lahir']) . ' tahun)';
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Jenis Kelamin:</span>
                            <span class="info-value"><?php echo $pasien_data['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telepon:</span>
                            <span class="info-value"><?php echo htmlspecialchars($pasien_data['telepon']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Pemeriksaan -->
                <div class="medical-history-section">
                    <h2>Riwayat Pemeriksaan</h2>

                    <?php if (empty($riwayat_pemeriksaan)): ?>
                        <p style="color: #666; text-align: center; padding: 40px;">Belum ada riwayat pemeriksaan</p>
                    <?php else: ?>
                        <?php foreach ($riwayat_pemeriksaan as $pemeriksaan): ?>
                            <div class="medical-record-card">
                                <div class="record-header">
                                    <h3>Tanggal <?php echo date('d F Y', strtotime($pemeriksaan['tanggal_pemeriksaan'])); ?></h3>
                                    <span class="record-status status-completed">Selesai</span>
                                </div>
                                <div class="record-body">
                                    <div class="record-item">
                                        <strong>Dokter:</strong> <?php echo htmlspecialchars($pemeriksaan['nama_dokter']); ?>
                                    </div>
                                    <div class="record-item">
                                        <strong>Anamnesis:</strong> <?php echo htmlspecialchars($pemeriksaan['ammnesis']); ?>
                                    </div>
                                    <div class="record-item">
                                        <strong>Diagnosis:</strong> <?php echo nl2br(htmlspecialchars($pemeriksaan['diagnosis'])); ?>
                                    </div>
                                    <div class="record-item">
                                        <strong>Tanda Vital:</strong>
                                        <ul class="vital-signs">
                                            <?php if ($pemeriksaan['tekanan_darah']): ?>
                                                <li>Tekanan Darah: <?php echo htmlspecialchars($pemeriksaan['tekanan_darah']); ?> mmHg</li>
                                            <?php endif; ?>
                                            <?php if ($pemeriksaan['suhu']): ?>
                                                <li>Suhu: <?php echo htmlspecialchars($pemeriksaan['suhu']); ?>°C</li>
                                            <?php endif; ?>
                                            <?php if ($pemeriksaan['nadi']): ?>
                                                <li>Nadi: <?php echo htmlspecialchars($pemeriksaan['nadi']); ?> x/menit</li>
                                            <?php endif; ?>
                                            <?php if ($pemeriksaan['pernapasan']): ?>
                                                <li>Pernapasan: <?php echo htmlspecialchars($pemeriksaan['pernapasan']); ?> x/menit</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="record-item">
                                        <strong>Terapi:</strong> <?php echo htmlspecialchars($pemeriksaan['terapi']); ?>
                                    </div>

                                    <?php
                                    // Get lab results for this pemeriksaan
                                    $conn_lab = getDBConnection();
                                    $stmt_lab = $conn_lab->prepare("
                                        SELECT pl.kode_lab, pl.jenis_tes, pl.tanggal_permintaan, pl.biaya, pl.status,
                                               hl.hasil_pemeriksaan, hl.catatan_petugas, hl.tanggal_hasil,
                                               u.nama_lengkap as petugas_lab
                                        FROM permintaan_lab pl
                                        LEFT JOIN hasil_lab hl ON pl.id = hl.permintaan_lab_id
                                        LEFT JOIN users u ON hl.petugas_lab_id = u.id
                                        WHERE pl.pemeriksaan_id = ?
                                        ORDER BY pl.tanggal_permintaan
                                    ");
                                    $stmt_lab->bind_param("i", $pemeriksaan['id']);
                                    $stmt_lab->execute();
                                    $result_lab = $stmt_lab->get_result();

                                    if ($result_lab->num_rows > 0):
                                    ?>
                                    <div class="lab-section">
                                        <strong>Hasil Laboratorium:</strong>
                                        <?php while ($lab = $result_lab->fetch_assoc()): ?>
                                        <div class="lab-result-box">
                                            <div class="lab-header">
                                                <strong><?php echo htmlspecialchars($lab['jenis_tes']); ?></strong>
                                                <span class="lab-status lab-<?php echo $lab['status']; ?>">
                                                    <?php echo ucfirst($lab['status']); ?>
                                                </span>
                                            </div>
                                            <div class="lab-info">
                                                <small>Kode: <?php echo $lab['kode_lab']; ?> |
                                                Permintaan: <?php echo date('d/m/Y H:i', strtotime($lab['tanggal_permintaan'])); ?></small>
                                            </div>
                                            <?php if ($lab['status'] === 'selesai'): ?>
                                            <div class="lab-result">
                                                <p><strong>Hasil:</strong><br><?php echo nl2br(htmlspecialchars($lab['hasil_pemeriksaan'])); ?></p>
                                                <?php if ($lab['catatan_petugas']): ?>
                                                <p><strong>Catatan:</strong> <?php echo htmlspecialchars($lab['catatan_petugas']); ?></p>
                                                <?php endif; ?>
                                                <small>Diperiksa oleh: <?php echo htmlspecialchars($lab['petugas_lab']); ?>
                                                pada <?php echo date('d/m/Y H:i', strtotime($lab['tanggal_hasil'])); ?></small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php
                                    endif;
                                    $stmt_lab->close();
                                    $conn_lab->close();
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Data Wearable Device -->
                <div class="wearable-section">
                    <h2>Data Wearable Device</h2>
                    <p class="info-note">Data real-time dari perangkat wearable yang terhubung.</p>

                    <?php if (empty($wearable_data)): ?>
                        <p style="color: #666; text-align: center; padding: 40px;">Belum ada data wearable</p>
                    <?php else: ?>
                        <!-- Riwayat Pembacaan -->
                        <div class="wearable-history">
                            <h3>Riwayat Pembacaan Terbaru</h3>
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Detak Jantung</th>
                                        <th>SpO₂</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($wearable_data as $data): ?>
                                        <tr>
                                            <td><?php echo date('d M Y, H:i', strtotime($data['waktu_pembacaan'])); ?></td>
                                            <td><?php echo htmlspecialchars($data['detak_jantung']); ?> bpm</td>
                                            <td><?php echo htmlspecialchars($data['spo2']); ?>%</td>
                                            <td>
                                                <span class="badge-status badge-<?php echo $data['status']; ?>">
                                                    <?php echo ucfirst($data['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="action-buttons">
                    <a href="../dashboard.php" class="btn btn-light">Kembali ke Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Container */
        .container {
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
            width: 100%;
        }

        .data-medis-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .data-medis-container h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 32px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .patient-info-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .patient-info-card h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 600;
        }

        .medical-history-section {
            margin-bottom: 30px;
        }

        .medical-history-section h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .medical-record-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .record-header {
            background: #3498db;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .record-header h3 {
            font-size: 18px;
            margin: 0;
        }

        .record-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-completed {
            background: #27ae60;
        }

        .record-body {
            padding: 20px;
        }

        .record-item {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #2c3e50;
        }

        .record-item:last-child {
            margin-bottom: 0;
        }

        .vital-signs {
            margin: 10px 0 0 20px;
            list-style: disc;
        }

        .vital-signs li {
            margin: 5px 0;
            color: #555;
        }

        .wearable-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .wearable-section h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .info-note {
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .wearable-history h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .history-table thead {
            background: #f8f9fa;
        }

        .history-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #2c3e50;
            border-bottom: 2px solid #e5e7eb;
        }

        .history-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f3f5;
            color: #555;
            font-size: 14px;
        }

        .history-table tbody tr:hover {
            background: #f8f9fa;
        }

        .history-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .badge-normal {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-light {
            background: #f8f9fa;
            color: #2c3e50;
            border: 1px solid #ddd;
        }

        .btn-light:hover {
            background: #e9ecef;
        }

        /* Alert */
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        /* Lab Results Styles */
        .lab-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .lab-result-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .lab-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .lab-status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .lab-status.lab-pending {
            background: #ffeaa7;
            color: #d63031;
        }

        .lab-status.lab-diproses {
            background: #74b9ff;
            color: #0984e3;
        }

        .lab-status.lab-selesai {
            background: #55efc4;
            color: #00b894;
        }

        .lab-info {
            margin-bottom: 10px;
            color: #666;
        }

        .lab-result {
            background: white;
            padding: 12px;
            border-radius: 4px;
            margin-top: 10px;
        }

        .lab-result p {
            margin-bottom: 10px;
        }

        .lab-result pre {
            white-space: pre-wrap;
            font-family: inherit;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .record-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .history-table {
                font-size: 12px;
            }

            .history-table th,
            .history-table td {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
</body>
</html>
