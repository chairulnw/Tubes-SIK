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
        // Pagination untuk riwayat pemeriksaan
        $items_per_page = 3;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($current_page - 1) * $items_per_page;

        // Hitung total pemeriksaan
        $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM pemeriksaan WHERE no_rm = ?");
        $stmt_count->bind_param("s", $pasien_data['no_rm']);
        $stmt_count->execute();
        $total_pemeriksaan = $stmt_count->get_result()->fetch_assoc()['total'];
        $stmt_count->close();
        $total_pages = ceil($total_pemeriksaan / $items_per_page);

        // Ambil riwayat pemeriksaan dengan pagination
        $stmt = $conn->prepare("
            SELECT p.*, u.nama_lengkap as nama_dokter
            FROM pemeriksaan p
            LEFT JOIN users u ON p.dokter_id = u.id
            WHERE p.no_rm = ?
            ORDER BY p.tanggal_pemeriksaan DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("sii", $pasien_data['no_rm'], $items_per_page, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $riwayat_pemeriksaan[] = $row;
        }
        $stmt->close();

        // Pagination untuk wearable data
        $wearable_per_page = 5;
        $wearable_page = isset($_GET['wpage']) ? max(1, intval($_GET['wpage'])) : 1;
        $wearable_offset = ($wearable_page - 1) * $wearable_per_page;

        // Hitung total wearable data
        $stmt_wcount = $conn->prepare("SELECT COUNT(*) as total FROM wearable_data WHERE no_rm = ?");
        $stmt_wcount->bind_param("s", $pasien_data['no_rm']);
        $stmt_wcount->execute();
        $total_wearable = $stmt_wcount->get_result()->fetch_assoc()['total'];
        $stmt_wcount->close();
        $wearable_total_pages = ceil($total_wearable / $wearable_per_page);

        // Ambil data wearable dengan pagination
        $stmt = $conn->prepare("
            SELECT * FROM wearable_data
            WHERE no_rm = ?
            ORDER BY waktu_pembacaan DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("sii", $pasien_data['no_rm'], $wearable_per_page, $wearable_offset);
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
                        <!-- Pagination untuk Riwayat Pemeriksaan (Atas) -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination-compact">
                                <?php if ($current_page > 1): ?>
                                    <a href="?page=<?php echo $current_page - 1; ?>" class="pagination-btn">&laquo;</a>
                                <?php else: ?>
                                    <span class="pagination-btn disabled">&laquo;</span>
                                <?php endif; ?>

                                <?php
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);

                                if ($start_page > 1): ?>
                                    <a href="?page=1" class="pagination-num">1</a>
                                    <?php if ($start_page > 2): ?>
                                        <span class="pagination-dots">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <?php if ($i == $current_page): ?>
                                        <span class="pagination-num active"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="?page=<?php echo $i; ?>" class="pagination-num"><?php echo $i; ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($end_page < $total_pages): ?>
                                    <?php if ($end_page < $total_pages - 1): ?>
                                        <span class="pagination-dots">...</span>
                                    <?php endif; ?>
                                    <a href="?page=<?php echo $total_pages; ?>" class="pagination-num"><?php echo $total_pages; ?></a>
                                <?php endif; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <a href="?page=<?php echo $current_page + 1; ?>" class="pagination-btn">&raquo;</a>
                                <?php else: ?>
                                    <span class="pagination-btn disabled">&raquo;</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

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
                        <p style="color: #666; text-align: center; padding: 40px;">Belum ada data wearable. Hubungkan perangkat dan tunggu data masuk.</p>
                    <?php else: ?>
                        <!-- Real-time Charts -->
                        <div class="charts-container">
                            <div class="chart-card" style="width: 100%;">
                                <h3>Detak Jantung (BPM)</h3>
                                <canvas id="chartHeartRate"></canvas>
                                <p class="chart-info" id="lastUpdateHR">Last update: <span>-</span></p>
                            </div>
                        </div>

                        <!-- Riwayat Pembacaan -->
                        <div class="wearable-history">
                            <h3>Riwayat Pembacaan Terbaru</h3>

                            <!-- Pagination untuk Wearable Data (Atas) -->
                            <?php if ($wearable_total_pages > 1): ?>
                                <div class="pagination-compact">
                                    <?php if ($wearable_page > 1): ?>
                                        <a href="?page=<?php echo $current_page; ?>&wpage=<?php echo $wearable_page - 1; ?>" class="pagination-btn">&laquo;</a>
                                    <?php else: ?>
                                        <span class="pagination-btn disabled">&laquo;</span>
                                    <?php endif; ?>

                                    <?php
                                    $w_start = max(1, $wearable_page - 2);
                                    $w_end = min($wearable_total_pages, $wearable_page + 2);

                                    if ($w_start > 1): ?>
                                        <a href="?page=<?php echo $current_page; ?>&wpage=1" class="pagination-num">1</a>
                                        <?php if ($w_start > 2): ?>
                                            <span class="pagination-dots">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $w_start; $i <= $w_end; $i++): ?>
                                        <?php if ($i == $wearable_page): ?>
                                            <span class="pagination-num active"><?php echo $i; ?></span>
                                        <?php else: ?>
                                            <a href="?page=<?php echo $current_page; ?>&wpage=<?php echo $i; ?>" class="pagination-num"><?php echo $i; ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <?php if ($w_end < $wearable_total_pages): ?>
                                        <?php if ($w_end < $wearable_total_pages - 1): ?>
                                            <span class="pagination-dots">...</span>
                                        <?php endif; ?>
                                        <a href="?page=<?php echo $current_page; ?>&wpage=<?php echo $wearable_total_pages; ?>" class="pagination-num"><?php echo $wearable_total_pages; ?></a>
                                    <?php endif; ?>

                                    <?php if ($wearable_page < $wearable_total_pages): ?>
                                        <a href="?page=<?php echo $current_page; ?>&wpage=<?php echo $wearable_page + 1; ?>" class="pagination-btn">&raquo;</a>
                                    <?php else: ?>
                                        <span class="pagination-btn disabled">&raquo;</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Detak Jantung</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="wearableTableBody">
                                    <?php foreach ($wearable_data as $data): ?>
                                        <tr>
                                            <td><?php echo date('d M Y, H:i', strtotime($data['waktu_pembacaan'])); ?></td>
                                            <td><?php echo htmlspecialchars($data['detak_jantung']); ?> bpm</td>
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

        /* Pagination Compact */
        .pagination-compact {
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 15px 0;
        }

        .pagination-compact .pagination-btn,
        .pagination-compact .pagination-num {
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            min-width: 32px;
            text-align: center;
            display: inline-block;
        }

        .pagination-compact .pagination-btn:hover:not(.disabled),
        .pagination-compact .pagination-num:hover:not(.active) {
            background: #e9ecef;
            border-color: #adb5bd;
        }

        .pagination-compact .pagination-btn.disabled {
            background: #f8f9fa;
            color: #adb5bd;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination-compact .pagination-num.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .pagination-compact .pagination-dots {
            padding: 0 4px;
            color: #adb5bd;
            font-size: 12px;
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
        /* Chart Cards */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .chart-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .chart-info {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 10px;
        }

        .chart-info span {
            font-weight: bold;
            color: #3498db;
        }

        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <?php if ($pasien_data && !empty($wearable_data)): ?>
    <script>
        // Configuration
        const NO_RM = '<?php echo $pasien_data['no_rm']; ?>';
        const API_URL = '/Tubes-SIK/api/wearable_latest.php';
        const UPDATE_INTERVAL = 10000; // 10 seconds

        // Initialize Charts
        const ctxHR = document.getElementById('chartHeartRate').getContext('2d');
        // const ctxSpO2 = document.getElementById('chartSpO2').getContext('2d'); // Disabled - SpO2 sensor not available

        const chartHR = new Chart(ctxHR, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Detak Jantung (BPM)',
                    data: [],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 40,
                        max: 140,
                        ticks: {
                            stepSize: 20
                        },
                        title: {
                            display: true,
                            text: 'BPM'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    }
                }
            }
        });

        /* SpO2 Chart - Disabled (sensor not available)
        const chartSpO2 = new Chart(ctxSpO2, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'SpO₂ (%)',
                    data: [],
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 80,
                        max: 100,
                        ticks: {
                            stepSize: 5
                        },
                        title: {
                            display: true,
                            text: 'SpO₂ (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    }
                }
            }
        });
        */

        // Function to update charts
        function updateCharts() {
            fetch(`${API_URL}?no_rm=${NO_RM}&limit=20`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        // Reverse untuk urutan kronologis (oldest to newest)
                        const dataPoints = data.data.reverse();

                        // Extract labels dan values
                        const labels = dataPoints.map(d => {
                            const date = new Date(d.waktu_pembacaan);
                            return date.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        });
                        const bpmData = dataPoints.map(d => d.detak_jantung);
                        // const spo2Data = dataPoints.map(d => d.spo2); // Disabled - SpO2 not available

                        // Update Heart Rate Chart
                        chartHR.data.labels = labels;
                        chartHR.data.datasets[0].data = bpmData;
                        chartHR.update('none'); // No animation for smoother updates

                        // Update SpO2 Chart - Disabled (sensor not available)
                        // chartSpO2.data.labels = labels;
                        // chartSpO2.data.datasets[0].data = spo2Data;
                        // chartSpO2.update('none');

                        // Update last update timestamp
                        const now = new Date().toLocaleTimeString('id-ID');
                        document.querySelector('#lastUpdateHR span').textContent = now;
                        // document.querySelector('#lastUpdateSpO2 span').textContent = now; // Disabled

                        // Update table (optional - only first 5 rows)
                        updateTable(data.data.slice(0, 5));

                        console.log(`[${now}] Charts updated successfully`);
                    }
                })
                .catch(error => {
                    console.error('Error updating charts:', error);
                });
        }

        // Function to update table
        function updateTable(dataPoints) {
            const tbody = document.getElementById('wearableTableBody');
            if (!tbody) return;

            tbody.innerHTML = '';
            dataPoints.forEach(d => {
                const date = new Date(d.waktu_pembacaan);
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const statusClass = `badge-${d.status}`;
                const statusText = d.status.charAt(0).toUpperCase() + d.status.slice(1);

                const row = `
                    <tr>
                        <td>${formattedDate}</td>
                        <td>${d.detak_jantung} bpm</td>
                        <td>
                            <span class="badge-status ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        // Initial load
        updateCharts();

        // Auto-update every 10 seconds
        setInterval(updateCharts, UPDATE_INTERVAL);

        console.log('Wearable data monitoring started');
        console.log(`Update interval: ${UPDATE_INTERVAL/1000} seconds`);
    </script>
    <?php endif; ?>

    <script>
    // Smooth scroll untuk pagination
    document.addEventListener('DOMContentLoaded', function() {
        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page');
        const wpage = urlParams.get('wpage');

        // Scroll ke section yang sesuai
        if (page) {
            // Scroll ke riwayat pemeriksaan
            const section = document.querySelector('.medical-history-section');
            if (section) {
                setTimeout(() => {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        } else if (wpage) {
            // Scroll ke wearable data
            const section = document.querySelector('.wearable-section');
            if (section) {
                setTimeout(() => {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        }
    });
    </script>
</body>
</html>
