<?php
/**
 * Daftar Resep Farmasi
 * Sistem Informasi Klinik
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan farmasi yang bisa akses
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if (!in_array($_SESSION['role'], ['admin', 'farmasi'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses ke halaman ini"));
    exit();
}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$conn = getDBConnection();

// Ambil resep pending/diproses (Permintaan Masuk)
$stmt_pending = $conn->prepare("
    SELECT r.*, p.nama as nama_pasien, u.nama_lengkap as nama_dokter
    FROM resep r
    JOIN pasien p ON r.no_rm = p.no_rm
    JOIN users u ON r.dokter_id = u.id
    WHERE r.status IN ('pending', 'diproses')
    ORDER BY r.tanggal_resep DESC
");
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
$pending_resep = [];
while ($row = $result_pending->fetch_assoc()) {
    $pending_resep[] = $row;
}
$stmt_pending->close();

// Ambil resep selesai (Riwayat)
$stmt_selesai = $conn->prepare("
    SELECT r.*, p.nama as nama_pasien, u.nama_lengkap as nama_dokter,
           u2.nama_lengkap as petugas_farmasi
    FROM resep r
    JOIN pasien p ON r.no_rm = p.no_rm
    JOIN users u ON r.dokter_id = u.id
    LEFT JOIN users u2 ON r.farmasi_id = u2.id
    WHERE r.status = 'selesai'
    ORDER BY r.tanggal_dispensing DESC
    LIMIT 50
");
$stmt_selesai->execute();
$result_selesai = $stmt_selesai->get_result();
$selesai_resep = [];
while ($row = $result_selesai->fetch_assoc()) {
    $selesai_resep[] = $row;
}
$stmt_selesai->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmasi - Sistem Informasi Klinik</title>
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
        .badge-pending {
            background: #ffeaa7;
            color: #d63031;
        }
        .badge-diproses {
            background: #74b9ff;
            color: #0984e3;
        }
        .badge-selesai {
            background: #55efc4;
            color: #00b894;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Farmasi - Daftar Resep</h1>
            <p class="subtitle">Kelola resep dan dispensing obat pasien</p>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('pending')">Permintaan Masuk (<?php echo count($pending_resep); ?>)</button>
                <button class="tab-btn" onclick="switchTab('selesai')">Riwayat Selesai</button>
            </div>

            <!-- Tab Content: Pending -->
            <div id="tab-pending" class="tab-content active">
                <?php if (empty($pending_resep)): ?>
                    <div class="alert alert-info">Tidak ada resep saat ini.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No. Resep</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Dokter</th>
                                    <th>Tanggal Resep</th>
                                    <th>Catatan Dokter</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_resep as $resep): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($resep['no_resep']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($resep['no_rm']); ?></td>
                                    <td><?php echo htmlspecialchars($resep['nama_pasien']); ?></td>
                                    <td><?php echo htmlspecialchars($resep['nama_dokter']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($resep['tanggal_resep'])); ?></td>
                                    <td style="max-width: 200px;"><?php echo nl2br(htmlspecialchars(substr($resep['catatan_resep'], 0, 50))); ?><?php echo strlen($resep['catatan_resep']) > 50 ? '...' : ''; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $resep['status']; ?>">
                                            <?php echo ucfirst($resep['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="farmasi-proses.php?id=<?php echo $resep['id']; ?>" class="btn btn-sm btn-primary">
                                            <?php echo $resep['status'] === 'pending' ? 'Proses' : 'Lanjutkan'; ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab Content: Selesai -->
            <div id="tab-selesai" class="tab-content">
                <?php if (empty($selesai_resep)): ?>
                    <div class="alert alert-info">Belum ada resep yang selesai.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No. Resep</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Dokter</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Petugas Farmasi</th>
                                    <th>Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selesai_resep as $resep): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($resep['no_resep']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($resep['no_rm']); ?></td>
                                    <td><?php echo htmlspecialchars($resep['nama_pasien']); ?></td>
                                    <td><?php echo htmlspecialchars($resep['nama_dokter']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($resep['tanggal_dispensing'])); ?></td>
                                    <td><?php echo htmlspecialchars($resep['petugas_farmasi']); ?></td>
                                    <td>Rp <?php echo number_format($resep['biaya'], 0, ',', '.'); ?></td>
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

    <style>
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
