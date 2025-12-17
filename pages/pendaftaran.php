<?php
/**
 * Form Pendaftaran Pasien
 * Sistem Informasi Klinik - Milestone 1
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

// Hanya admin dan staf pendaftaran yang bisa akses
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if (!in_array($_SESSION['role'], ['admin', 'pendaftaran'])) {
    header("Location: ../dashboard.php?error=" . urlencode("Anda tidak memiliki akses ke halaman ini"));
    exit();
}

$success = '';
$error = '';

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Pasien - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: nowrap;
            align-items: center;
        }
        .radio-group label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px;
            white-space: nowrap;
            margin: 0 !important;
            cursor: pointer;
        }
        .radio-group input[type="radio"] {
            margin: 0 !important;
            flex-shrink: 0;
            width: auto !important;
            height: auto !important;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Form Pendaftaran Pasien</h1>
            <p class="subtitle">Daftarkan pasien baru atau pasien lama untuk pemeriksaan</p>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="../handlers/pendaftaran_process.php" class="medical-form">
                <div class="form-section">
                    <h2>Data Rekam Medis</h2>
                    <div class="form-group">
                        <label>Nomor Rekam Medis</label>
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <div style="flex: 1;">
                                <input type="text" id="no_rm" name="no_rm" placeholder="Contoh: RM2025001">
                                <small>Kosongkan jika pasien baru, sistem akan generate otomatis</small>
                            </div>
                            <button type="button" id="btn-check-rm" class="btn btn-secondary" style="margin-top: 0;">Cek No. RM</button>
                        </div>
                        <div id="check-result" style="margin-top: 10px;"></div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Data Pribadi Pasien</h2>
                    <div class="form-group">
                        <label>NIK <span class="required">*</span></label>
                        <input type="text" id="nik" name="nik" required maxlength="20">
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="nama" name="nama" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Lahir <span class="required">*</span></label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
                        </div>

                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px;">Jenis Kelamin <span class="required">*</span></label>
                            <div class="radio-group">
                                <label><input type="radio" id="jenis_kelamin_l" name="jenis_kelamin" value="L" required> Laki-laki</label>
                                <label><input type="radio" id="jenis_kelamin_p" name="jenis_kelamin" value="P" required> Perempuan</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat <span class="required">*</span></label>
                        <textarea id="alamat" name="alamat" required rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon <span class="required">*</span></label>
                        <input type="tel" id="telepon" name="telepon" required>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Link ke Akun User (Opsional)</h2>
                    <div class="form-group">
                        <label>Username Pasien</label>
                        <input type="text" id="username_pasien" name="username_pasien" placeholder="Masukkan username jika pasien sudah punya akun">
                        <small>Kosongkan jika pasien belum memiliki akun di sistem</small>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Keluhan</h2>
                    <div class="form-group">
                        <label>Keluhan Pasien <span class="required">*</span></label>
                        <textarea name="keluhan" required rows="4" placeholder="Deskripsikan keluhan pasien..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Daftar Pasien</button>
                    <button type="reset" class="btn btn-secondary">Reset Form</button>
                    <a href="../dashboard.php" class="btn btn-light">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.getElementById('btn-check-rm').addEventListener('click', function() {
            const noRM = document.getElementById('no_rm').value.trim();
            const resultDiv = document.getElementById('check-result');

            // Clear previous result
            resultDiv.innerHTML = '';

            if (!noRM) {
                resultDiv.innerHTML = '<div class="alert alert-error">Silakan masukkan No. RM terlebih dahulu</div>';
                return;
            }

            // Show loading
            resultDiv.innerHTML = '<div style="color: #666;">Mengecek No. RM...</div>';

            // Fetch data from API
            fetch('../api/check_patient.php?no_rm=' + encodeURIComponent(noRM))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.exists) {
                        // Patient exists - auto fill form
                        resultDiv.innerHTML = '<div class="alert alert-success">Pasien ditemukan! Data berhasil diisi otomatis.</div>';

                        // Fill form fields (except keluhan)
                        document.getElementById('nik').value = data.data.nik || '';
                        document.getElementById('nama').value = data.data.nama || '';
                        document.getElementById('tanggal_lahir').value = data.data.tanggal_lahir || '';
                        document.getElementById('alamat').value = data.data.alamat || '';
                        document.getElementById('telepon').value = data.data.telepon || '';

                        // Fill username only if not null
                        if (data.data.username) {
                            document.getElementById('username_pasien').value = data.data.username;
                        }

                        // Set radio button for jenis kelamin
                        if (data.data.jenis_kelamin === 'L') {
                            document.getElementById('jenis_kelamin_l').checked = true;
                        } else if (data.data.jenis_kelamin === 'P') {
                            document.getElementById('jenis_kelamin_p').checked = true;
                        }

                        // Disable fields that are auto-filled to prevent editing
                        document.getElementById('nik').readOnly = true;
                        document.getElementById('nama').readOnly = true;
                        document.getElementById('tanggal_lahir').readOnly = true;
                        document.getElementById('alamat').readOnly = true;
                        document.getElementById('telepon').readOnly = true;
                        document.getElementById('jenis_kelamin_l').disabled = true;
                        document.getElementById('jenis_kelamin_p').disabled = true;

                        // Lock username only if it was filled
                        if (data.data.username) {
                            document.getElementById('username_pasien').readOnly = true;
                            document.getElementById('username_pasien').style.backgroundColor = '#f0f0f0';
                        }

                        // Add visual indicator that fields are locked
                        document.getElementById('nik').style.backgroundColor = '#f0f0f0';
                        document.getElementById('nama').style.backgroundColor = '#f0f0f0';
                        document.getElementById('tanggal_lahir').style.backgroundColor = '#f0f0f0';
                        document.getElementById('alamat').style.backgroundColor = '#f0f0f0';
                        document.getElementById('telepon').style.backgroundColor = '#f0f0f0';

                    } else if (data.success && !data.exists) {
                        // Patient not found
                        resultDiv.innerHTML = '<div class="alert alert-info">' + data.message + '</div>';

                        // Enable all fields for manual entry
                        enableAllFields();
                    } else {
                        // Error
                        resultDiv.innerHTML = '<div class="alert alert-error">Error: ' + data.error + '</div>';
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = '<div class="alert alert-error">Terjadi kesalahan saat mengecek No. RM</div>';
                    console.error('Error:', error);
                });
        });

        // Function to enable all fields
        function enableAllFields() {
            document.getElementById('nik').readOnly = false;
            document.getElementById('nama').readOnly = false;
            document.getElementById('tanggal_lahir').readOnly = false;
            document.getElementById('alamat').readOnly = false;
            document.getElementById('telepon').readOnly = false;
            document.getElementById('username_pasien').readOnly = false;
            document.getElementById('jenis_kelamin_l').disabled = false;
            document.getElementById('jenis_kelamin_p').disabled = false;

            // Remove visual indicator
            document.getElementById('nik').style.backgroundColor = '';
            document.getElementById('nama').style.backgroundColor = '';
            document.getElementById('tanggal_lahir').style.backgroundColor = '';
            document.getElementById('alamat').style.backgroundColor = '';
            document.getElementById('telepon').style.backgroundColor = '';
            document.getElementById('username_pasien').style.backgroundColor = '';
        }

        // Reset locked fields when No. RM is changed
        document.getElementById('no_rm').addEventListener('input', function() {
            document.getElementById('check-result').innerHTML = '';
            enableAllFields();
        });
    </script>
</body>
</html>
