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

// Ambil daftar pasien yang sudah mendaftar hari ini dan belum diperiksa
// atau pasien yang sudah diperiksa (untuk riwayat)
$stmt = $conn->prepare("
    SELECT p.no_rm, p.nama, p.tanggal_lahir, p.jenis_kelamin, p.alamat,
           pend.tanggal_daftar, pend.keluhan,
           (SELECT COUNT(*) FROM pemeriksaan WHERE no_rm = p.no_rm AND DATE(tanggal_pemeriksaan) = CURDATE()) as sudah_diperiksa_hari_ini
    FROM pasien p
    JOIN pendaftaran pend ON p.no_rm = pend.no_rm
    WHERE DATE(pend.tanggal_daftar) = CURDATE()
    ORDER BY pend.tanggal_daftar ASC
");
$stmt->execute();
$result = $stmt->get_result();
$pasien_list = [];
while ($row = $result->fetch_assoc()) {
    $pasien_list[] = $row;
}
$stmt->close();
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
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
