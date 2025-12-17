<?php
/**
 * Halaman Laboratorium
 * Sistem Informasi Klinik
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan laboratorium yang bisa akses
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if (!in_array($_SESSION['role'], ['admin', 'laboratorium'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses ke halaman ini"));
    exit();
}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$conn = getDBConnection();

// Ambil permintaan lab yang pending/diproses
$stmt_pending = $conn->prepare("
    SELECT pl.*, p.nama as nama_pasien, u.nama_lengkap as nama_dokter
    FROM permintaan_lab pl
    JOIN pasien p ON pl.no_rm = p.no_rm
    JOIN users u ON pl.dokter_id = u.id
    WHERE pl.status IN ('pending', 'diproses')
    ORDER BY pl.tanggal_permintaan DESC
");
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
$pending_labs = [];
while ($row = $result_pending->fetch_assoc()) {
    $pending_labs[] = $row;
}
$stmt_pending->close();

// Ambil hasil lab yang sudah selesai
$stmt_selesai = $conn->prepare("
    SELECT pl.*, p.nama as nama_pasien, u.nama_lengkap as nama_dokter,
           hl.hasil_pemeriksaan, hl.catatan_petugas, hl.tanggal_hasil,
           u2.nama_lengkap as petugas_lab
    FROM permintaan_lab pl
    JOIN pasien p ON pl.no_rm = p.no_rm
    JOIN users u ON pl.dokter_id = u.id
    LEFT JOIN hasil_lab hl ON pl.id = hl.permintaan_lab_id
    LEFT JOIN users u2 ON hl.petugas_lab_id = u2.id
    WHERE pl.status = 'selesai'
    ORDER BY hl.tanggal_hasil DESC
    LIMIT 50
");
$stmt_selesai->execute();
$result_selesai = $stmt_selesai->get_result();
$selesai_labs = [];
while ($row = $result_selesai->fetch_assoc()) {
    $selesai_labs[] = $row;
}
$stmt_selesai->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium - Sistem Informasi Klinik</title>
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
        .form-container {
            min-width: 900px;
            max-width: 1400px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Laboratorium</h1>
            <p class="subtitle">Kelola permintaan dan hasil tes laboratorium</p>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('pending')">Permintaan Masuk (<?php echo count($pending_labs); ?>)</button>
                <button class="tab-btn" onclick="switchTab('selesai')">Riwayat Selesai</button>
            </div>

            <!-- Tab Content: Pending -->
            <div id="tab-pending" class="tab-content active">
                <?php if (empty($pending_labs)): ?>
                    <div class="alert alert-info">Tidak ada permintaan lab saat ini.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode Lab</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Tes</th>
                                    <th>Dokter</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_labs as $lab): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($lab['kode_lab']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($lab['no_rm']); ?></td>
                                    <td><?php echo htmlspecialchars($lab['nama_pasien']); ?></td>
                                    <td><?php echo htmlspecialchars($lab['jenis_tes']); ?></td>
                                    <td><?php echo htmlspecialchars($lab['nama_dokter']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($lab['tanggal_permintaan'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $lab['status']; ?>">
                                            <?php echo ucfirst($lab['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($lab['status'] === 'pending'): ?>
                                            <button class="btn btn-sm btn-warning" onclick="updateStatus(<?php echo $lab['id']; ?>, 'diproses')">Proses</button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-primary" onclick="openInputModal(<?php echo $lab['id']; ?>, '<?php echo htmlspecialchars($lab['kode_lab']); ?>', '<?php echo htmlspecialchars($lab['jenis_tes']); ?>')">Input Hasil</button>
                                        <?php if (!empty($lab['catatan_dokter'])): ?>
                                            <button class="btn btn-sm btn-secondary" onclick="showNote('<?php echo htmlspecialchars(addslashes($lab['catatan_dokter'])); ?>')">Lihat Catatan</button>
                                        <?php endif; ?>
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
                <?php if (empty($selesai_labs)): ?>
                    <div class="alert alert-info">Belum ada hasil lab yang selesai.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode Lab</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Tes</th>
                                    <th>Tanggal Hasil</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selesai_labs as $lab): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($lab['kode_lab']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($lab['no_rm']); ?></td>
                                    <td><?php echo htmlspecialchars($lab['nama_pasien']); ?></td>
                                    <td><?php echo htmlspecialchars($lab['jenis_tes']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($lab['tanggal_hasil'])); ?></td>
                                    <td><?php echo htmlspecialchars($lab['petugas_lab']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" onclick="viewDetail(<?php echo $lab['id']; ?>)">Lihat Detail</button>
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

    <!-- Modal Input Hasil Lab -->
    <div id="inputModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Input Hasil Laboratorium</h2>
            <form id="formInputHasil" method="post" action="../handlers/lab_result_process.php">
                <input type="hidden" name="permintaan_lab_id" id="input_permintaan_id">

                <div class="form-group">
                    <label>Kode Lab</label>
                    <input type="text" id="input_kode_lab" readonly class="form-control">
                </div>

                <div class="form-group">
                    <label>Jenis Tes</label>
                    <input type="text" id="input_jenis_tes" readonly class="form-control">
                </div>

                <div class="form-group">
                    <label for="hasil_pemeriksaan">Hasil Pemeriksaan *</label>
                    <textarea name="hasil_pemeriksaan" id="hasil_pemeriksaan" rows="6" required class="form-control" placeholder="Contoh:&#10;Hemoglobin: 12.5 g/dL&#10;Leukosit: 8.500/µL&#10;Eritrosit: 4.5 juta/µL"></textarea>
                    <small>Input hasil pemeriksaan laboratorium</small>
                </div>

                <div class="form-group">
                    <label for="catatan_petugas">Catatan Petugas</label>
                    <textarea name="catatan_petugas" id="catatan_petugas" rows="3" class="form-control" placeholder="Catatan tambahan (opsional)"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Hasil</button>
                    <button type="button" class="btn btn-light" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Hasil -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetailModal()">&times;</span>
            <h2>Detail Hasil Laboratorium</h2>
            <div id="detailContent"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <style>
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            color: #2c3e50;
        }

        .tab-btn.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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

        .btn-warning {
            background: #fdcb6e;
            color: #2d3436;
        }

        .btn-warning:hover {
            background: #f39c12;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 12px 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>

    <script>
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

            // Show selected tab
            document.getElementById('tab-' + tab).classList.add('active');
            event.target.classList.add('active');
        }

        function updateStatus(id, status) {
            if (confirm('Ubah status permintaan lab menjadi "' + status + '"?')) {
                fetch('../handlers/lab_status_process.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'permintaan_lab_id=' + id + '&status=' + status
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function openInputModal(id, kode, jenis) {
            document.getElementById('input_permintaan_id').value = id;
            document.getElementById('input_kode_lab').value = kode;
            document.getElementById('input_jenis_tes').value = jenis;
            document.getElementById('inputModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('inputModal').style.display = 'none';
            document.getElementById('formInputHasil').reset();
        }

        function showNote(note) {
            alert('Catatan Dokter:\n\n' + note);
        }

        function viewDetail(id) {
            // Fetch detail via AJAX
            fetch('../handlers/get_lab_detail.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '<div class="detail-box">';
                        html += '<p><strong>Kode Lab:</strong> ' + data.lab.kode_lab + '</p>';
                        html += '<p><strong>Jenis Tes:</strong> ' + data.lab.jenis_tes + '</p>';
                        html += '<p><strong>Pasien:</strong> ' + data.lab.nama_pasien + ' (' + data.lab.no_rm + ')</p>';
                        html += '<p><strong>Hasil Pemeriksaan:</strong><br><pre>' + data.lab.hasil_pemeriksaan + '</pre></p>';
                        if (data.lab.catatan_petugas) {
                            html += '<p><strong>Catatan:</strong> ' + data.lab.catatan_petugas + '</p>';
                        }
                        html += '</div>';
                        document.getElementById('detailContent').innerHTML = html;
                        document.getElementById('detailModal').style.display = 'block';
                    }
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            let inputModal = document.getElementById('inputModal');
            let detailModal = document.getElementById('detailModal');
            if (event.target == inputModal) {
                closeModal();
            }
            if (event.target == detailModal) {
                closeDetailModal();
            }
        }
    </script>
</body>
</html>
