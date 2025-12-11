<?php
/**
 * Form Proses Resep Farmasi
 * Sistem Informasi Klinik
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan farmasi yang bisa akses
requireRole(['admin', 'farmasi']);

$user = getCurrentUser();
$resep_id = $_GET['id'] ?? 0;

$conn = getDBConnection();

// Ambil data resep
$stmt = $conn->prepare("
    SELECT r.*, p.nama as nama_pasien, p.alamat, u.nama_lengkap as nama_dokter
    FROM resep r
    JOIN pasien p ON r.no_rm = p.no_rm
    JOIN users u ON r.dokter_id = u.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $resep_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: farmasi.php?error=" . urlencode("Resep tidak ditemukan"));
    exit();
}

$resep = $result->fetch_assoc();
$stmt->close();

// Update status jadi diproses jika masih pending
if ($resep['status'] === 'pending') {
    $stmt = $conn->prepare("UPDATE resep SET status = 'diproses' WHERE id = ?");
    $stmt->bind_param("i", $resep_id);
    $stmt->execute();
    $stmt->close();
    $resep['status'] = 'diproses';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Resep - Sistem Informasi Klinik</title>
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
        .obat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .obat-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .obat-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        .obat-title span {
            font-weight: 600;
            font-size: 16px;
            color: #2c3e50;
        }
        .hapus-obat {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }
        .hapus-obat:hover {
            background: #c0392b;
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background: #229954;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #0056b3;
            font-size: 16px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Form Resep Farmasi</h1>
            <p class="subtitle">Input detail obat dan dispensing</p>

            <!-- Info Resep -->
            <div class="info-box">
                <h3>Informasi Resep</h3>
                <p><strong>No. Resep:</strong> <?php echo htmlspecialchars($resep['no_resep']); ?></p>
                <p><strong>No. RM:</strong> <?php echo htmlspecialchars($resep['no_rm']); ?></p>
                <p><strong>Pasien:</strong> <?php echo htmlspecialchars($resep['nama_pasien']); ?></p>
                <p><strong>Dokter:</strong> <?php echo htmlspecialchars($resep['nama_dokter']); ?></p>
                <p><strong>Tanggal Resep:</strong> <?php echo date('d/m/Y H:i', strtotime($resep['tanggal_resep'])); ?></p>
                <p><strong>Catatan Dokter:</strong></p>
                <p style="white-space: pre-wrap; background: white; padding: 10px; border-radius: 4px; margin-top: 5px;"><?php echo htmlspecialchars($resep['catatan_resep']); ?></p>
            </div>

            <form method="post" action="../handlers/farmasi_process.php" class="medical-form">
                <input type="hidden" name="resep_id" value="<?php echo $resep['id']; ?>">

                <div class="form-section">
                    <div class="form-group">
                        <label>Nama Apoteker <span class="required">*</span></label>
                        <input type="text" name="apoteker" required value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" readonly style="background-color: #f0f0f0;">
                    </div>
                </div>

                <div class="form-section">
                    <div class="obat-header">
                        <h2>Daftar Obat</h2>
                        <button type="button" id="tambahObat" class="btn btn-success">+ Tambah Obat</button>
                    </div>

                    <div id="obatContainer">
                        <div class="obat-item">
                            <div class="obat-title">
                                <span>Obat 1</span>
                                <button type="button" class="hapus-obat" style="display:none;">Hapus</button>
                            </div>

                            <div class="form-group">
                                <label>Nama Obat <span class="required">*</span></label>
                                <input type="text" name="nama_obat[]" required placeholder="Contoh: Paracetamol 500mg">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Jumlah <span class="required">*</span></label>
                                    <input type="number" name="jumlah[]" required min="1" placeholder="10">
                                </div>

                                <div class="form-group">
                                    <label>Satuan <span class="required">*</span></label>
                                    <select name="satuan[]" required>
                                        <option value="tablet">Tablet</option>
                                        <option value="kapsul">Kapsul</option>
                                        <option value="botol">Botol</option>
                                        <option value="tube">Tube</option>
                                        <option value="strip">Strip</option>
                                        <option value="box">Box</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Harga Satuan (Rp) <span class="required">*</span></label>
                                    <input type="number" name="harga[]" required min="0" placeholder="5000" class="harga-input">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Dosis dan Aturan Pakai <span class="required">*</span></label>
                                <textarea name="dosis[]" required rows="2" placeholder="Contoh: 3x1 tablet sehari setelah makan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Total Biaya</h2>
                    <div class="form-group">
                        <label>Total Biaya Obat (Rp) <span class="required">*</span></label>
                        <input type="number" name="total_biaya" id="totalBiaya" required min="0" readonly style="background-color: #f0f0f0; font-size: 18px; font-weight: 600; color: #27ae60;">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Catatan</h2>
                    <div class="form-group">
                        <label>Catatan Apoteker (opsional)</label>
                        <textarea name="catatan_apoteker" rows="3" placeholder="Catatan tambahan tentang resep ini..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Selesaikan Resep</button>
                    <button type="reset" class="btn btn-secondary">Reset Form</button>
                    <a href="farmasi.php" class="btn btn-light">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        let obatCount = 1;

        // Function to calculate total
        function hitungTotal() {
            let total = 0;
            const obatItems = document.querySelectorAll('.obat-item');

            obatItems.forEach(item => {
                const jumlah = parseInt(item.querySelector('input[name="jumlah[]"]').value) || 0;
                const harga = parseInt(item.querySelector('input[name="harga[]"]').value) || 0;
                total += jumlah * harga;
            });

            document.getElementById('totalBiaya').value = total;
        }

        // Event listener for price and quantity changes
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name="jumlah[]"]') || e.target.matches('input[name="harga[]"]')) {
                hitungTotal();
            }
        });

        document.getElementById('tambahObat').addEventListener('click', function() {
            obatCount++;
            const container = document.getElementById('obatContainer');
            const newObat = document.createElement('div');
            newObat.className = 'obat-item';
            newObat.innerHTML = `
                <div class="obat-title">
                    <span>Obat ${obatCount}</span>
                    <button type="button" class="hapus-obat">Hapus</button>
                </div>
                <div class="form-group">
                    <label>Nama Obat <span class="required">*</span></label>
                    <input type="text" name="nama_obat[]" required placeholder="Contoh: Amoxicillin 500mg">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jumlah <span class="required">*</span></label>
                        <input type="number" name="jumlah[]" required min="1" placeholder="10">
                    </div>
                    <div class="form-group">
                        <label>Satuan <span class="required">*</span></label>
                        <select name="satuan[]" required>
                            <option value="tablet">Tablet</option>
                            <option value="kapsul">Kapsul</option>
                            <option value="botol">Botol</option>
                            <option value="tube">Tube</option>
                            <option value="strip">Strip</option>
                            <option value="box">Box</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga Satuan (Rp) <span class="required">*</span></label>
                        <input type="number" name="harga[]" required min="0" placeholder="15000" class="harga-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>Dosis dan Aturan Pakai <span class="required">*</span></label>
                    <textarea name="dosis[]" required rows="2" placeholder="Contoh: 3x1 kapsul sehari setelah makan"></textarea>
                </div>
            `;
            container.appendChild(newObat);
            updateObatNumbers();
        });

        document.getElementById('obatContainer').addEventListener('click', function(e) {
            if (e.target.classList.contains('hapus-obat')) {
                e.target.closest('.obat-item').remove();
                updateObatNumbers();
                hitungTotal();
            }
        });

        function updateObatNumbers() {
            const items = document.querySelectorAll('.obat-item');
            obatCount = items.length;
            items.forEach((item, index) => {
                item.querySelector('.obat-title span').textContent = `Obat ${index + 1}`;
                const hapusBtn = item.querySelector('.hapus-obat');
                hapusBtn.style.display = items.length > 1 ? 'inline-block' : 'none';
            });
        }

        // Initial calculation
        hitungTotal();
    </script>
</body>
</html>
