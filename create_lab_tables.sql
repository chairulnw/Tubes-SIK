-- SQL Script untuk membuat tabel workflow laboratorium
-- Sistem Informasi Klinik - Klinik Sehat

USE db_klinik;

-- Tabel 1: permintaan_lab
-- Menyimpan permintaan tes laboratorium dari dokter
CREATE TABLE IF NOT EXISTS permintaan_lab (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_lab VARCHAR(20) UNIQUE NOT NULL,
    pemeriksaan_id INT NOT NULL,
    no_rm VARCHAR(20) NOT NULL,
    dokter_id INT NOT NULL,
    tanggal_permintaan DATETIME NOT NULL,
    jenis_tes VARCHAR(100) NOT NULL,
    catatan_dokter TEXT,
    status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending',
    biaya DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (pemeriksaan_id) REFERENCES pemeriksaan(id) ON DELETE CASCADE,
    FOREIGN KEY (no_rm) REFERENCES pasien(no_rm) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_status (status),
    INDEX idx_no_rm (no_rm),
    INDEX idx_pemeriksaan (pemeriksaan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel 2: hasil_lab
-- Menyimpan hasil tes laboratorium yang diinput oleh staff lab
CREATE TABLE IF NOT EXISTS hasil_lab (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permintaan_lab_id INT NOT NULL,
    petugas_lab_id INT NOT NULL,
    tanggal_hasil DATETIME NOT NULL,
    hasil_pemeriksaan TEXT NOT NULL,
    catatan_petugas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (permintaan_lab_id) REFERENCES permintaan_lab(id) ON DELETE CASCADE,
    FOREIGN KEY (petugas_lab_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_permintaan (permintaan_lab_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel 3: jenis_tes_lab
-- Master data untuk jenis-jenis tes laboratorium dan biayanya
CREATE TABLE IF NOT EXISTS jenis_tes_lab (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_tes VARCHAR(20) UNIQUE NOT NULL,
    nama_tes VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    biaya_standar DECIMAL(10,2) NOT NULL,
    deskripsi TEXT,
    persiapan TEXT,
    aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_kategori (kategori),
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data jenis tes laboratorium
INSERT INTO jenis_tes_lab (kode_tes, nama_tes, kategori, biaya_standar, deskripsi, persiapan) VALUES
('LAB001', 'Darah Lengkap', 'Hematologi', 75000.00, 'Pemeriksaan komponen darah lengkap', 'Puasa tidak diperlukan'),
('LAB002', 'Gula Darah Puasa', 'Kimia Klinik', 50000.00, 'Pemeriksaan kadar glukosa darah puasa', 'Puasa 8-12 jam'),
('LAB003', 'Kolesterol Total', 'Kimia Klinik', 60000.00, 'Pemeriksaan kadar kolesterol total', 'Puasa 12 jam'),
('LAB004', 'Urin Lengkap', 'Urinalisis', 40000.00, 'Pemeriksaan urin rutin', 'Urin pagi lebih baik'),
('LAB005', 'Asam Urat', 'Kimia Klinik', 45000.00, 'Pemeriksaan kadar asam urat', 'Hindari makanan tinggi purin 24 jam sebelumnya'),
('LAB006', 'Fungsi Hati (SGOT/SGPT)', 'Kimia Klinik', 85000.00, 'Pemeriksaan fungsi liver', 'Puasa 8 jam'),
('LAB007', 'Fungsi Ginjal (Ureum/Kreatinin)', 'Kimia Klinik', 80000.00, 'Pemeriksaan fungsi ginjal', 'Puasa 8 jam'),
('LAB008', 'Rontgen Thorax', 'Radiologi', 150000.00, 'Foto rontgen dada', 'Lepas perhiasan logam');
