<?php
/**
 * Daftar Pasien untuk Pemeriksaan
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

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$conn = getDBConnection();

// Ambil daftar pasien yang sudah mendaftar hari ini TAPI BELUM diperiksa (Permintaan Masuk)
$stmt_pending = $conn->prepare("
    SELECT p.no_rm, p.nama, p.tanggal_lahir, p.jenis_kelamin, p.alamat,
           pend.tanggal_daftar, pend.keluhan
    FROM pasien p
    JOIN pendaftaran pend ON p.no_rm = pend.no_rm
    WHERE DATE(pend.tanggal_daftar) = CURDATE()
    AND NOT EXISTS (
        SELECT 1 FROM pemeriksaan
        WHERE no_rm = p.no_rm
        AND DATE(tanggal_pemeriksaan) = CURDATE()
    )
    ORDER BY pend.tanggal_daftar ASC
");
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
$pasien_list = [];
while ($row = $result_pending->fetch_assoc()) {
    $pasien_list[] = $row;
}
$stmt_pending->close();

// Ambil riwayat pemeriksaan (hari ini dan kemarin)
$stmt_riwayat = $conn->prepare("
    SELECT px.*, p.nama as nama_pasien, p.tanggal_lahir, p.jenis_kelamin,
           u.nama_lengkap as nama_dokter
    FROM pemeriksaan px
    JOIN pasien p ON px.no_rm = p.no_rm
    JOIN users u ON px.dokter_id = u.id
    WHERE DATE(px.tanggal_pemeriksaan) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ORDER BY px.tanggal_pemeriksaan DESC
    LIMIT 50
");
$stmt_riwayat->execute();
$result_riwayat = $stmt_riwayat->get_result();
$riwayat_list = [];
while ($row = $result_riwayat->fetch_assoc()) {
    $riwayat_list[] = $row;
}
$stmt_riwayat->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1;
        }
        .table-responsive {
            overflow-x: auto;
            margin-top: 20px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        .data-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-belum {
            background: #ffeaa7;
            color: #d63031;
        }
        .badge-sudah {
            background: #55efc4;
            color: #00b894;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            margin-right: 5px;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 12px 20px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .form-container {
            min-width: 900px;
            max-width: 1400px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            border-bottom: 2px solid #dee2e6;
        }

        .tab-btn {
            padding: 12px 24px;
            background: white;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #6c757d;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            color: #3498db;
            background: #f8f9fa;
        }

        .tab-btn.active {
            color: #3498db;
            border-bottom-color: #3498db;
            background: #f8f9fa;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Pemeriksaan - Daftar Pasien</h1>
            <p class="subtitle">Pilih pasien yang akan diperiksa</p>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('pending')">Pendaftaran Hari Ini (<?php echo count($pasien_list); ?>)</button>
                <button class="tab-btn" onclick="switchTab('riwayat')">Riwayat Pemeriksaan</button>
            </div>

            <!-- Tab Content: Pending -->
            <div id="tab-pending" class="tab-content active">
                <?php if (empty($pasien_list)): ?>
                    <div class="alert alert-info">Tidak ada pasien yang mendaftar hari ini.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Usia</th>
                                    <th>Keluhan</th>
                                    <th>Waktu Daftar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pasien_list as $pasien): ?>
                                <?php
                                    // Hitung usia
                                    $lahir = new DateTime($pasien['tanggal_lahir']);
                                    $sekarang = new DateTime();
                                    $usia = $sekarang->diff($lahir)->y;
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($pasien['no_rm']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($pasien['nama']); ?></td>
                                    <td><?php echo $pasien['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                    <td><?php echo $usia; ?> tahun</td>
                                    <td style="max-width: 200px;"><?php echo nl2br(htmlspecialchars(substr($pasien['keluhan'], 0, 50))); ?><?php echo strlen($pasien['keluhan']) > 50 ? '...' : ''; ?></td>
                                    <td><?php echo date('H:i', strtotime($pasien['tanggal_daftar'])); ?></td>
                                    <td>
                                        <?php if ($pasien['sudah_diperiksa_hari_ini'] > 0): ?>
                                            <span class="badge badge-sudah">Sudah diperiksa</span>
                                        <?php else: ?>
                                            <span class="badge badge-belum">Belum diperiksa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="pemeriksaan-proses.php?no_rm=<?php echo urlencode($pasien['no_rm']); ?>" class="btn btn-sm btn-primary">
                                            Proses
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab Content: Riwayat -->
            <div id="tab-riwayat" class="tab-content">
                <?php if (empty($riwayat_list)): ?>
                    <div class="alert alert-info">Belum ada riwayat pemeriksaan.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Dokter</th>
                                    <th>Diagnosis</th>
                                    <th>Tanda Vital</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayat_list as $px): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($px['tanggal_pemeriksaan'])); ?></td>
                                    <td><strong><?php echo htmlspecialchars($px['no_rm']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($px['nama_pasien']); ?></td>
                                    <td><?php echo htmlspecialchars($px['nama_dokter']); ?></td>
                                    <td style="max-width: 250px;"><?php echo nl2br(htmlspecialchars(substr($px['diagnosis'], 0, 80))); ?><?php echo strlen($px['diagnosis']) > 80 ? '...' : ''; ?></td>
                                    <td style="font-size: 12px;">
                                        <?php if ($px['tekanan_darah']): ?>TD: <?php echo htmlspecialchars($px['tekanan_darah']); ?><br><?php endif; ?>
                                        <?php if ($px['suhu']): ?>Suhu: <?php echo $px['suhu']; ?>°C<br><?php endif; ?>
                                        <?php if ($px['nadi']): ?>Nadi: <?php echo $px['nadi']; ?> x/mnt<?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all buttons
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => btn.classList.remove('active'));

            // Show selected tab
            document.getElementById('tab-' + tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
