-- ============================================
-- Database Schema for Sistem Informasi Klinik
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS db_klinik;
USE db_klinik;

-- ============================================
-- Table: users
-- Menyimpan data user/pengguna sistem
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'dokter', 'pendaftaran', 'farmasi', 'laboratorium', 'kasir', 'pasien') NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- Table: pasien
-- Menyimpan data pasien
-- ============================================
CREATE TABLE pasien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) NOT NULL UNIQUE,
    nik VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- Table: pendaftaran
-- Menyimpan data pendaftaran pasien
-- ============================================
CREATE TABLE pendaftaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) NOT NULL,
    keluhan TEXT NOT NULL,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('menunggu', 'dipanggil', 'selesai', 'batal') DEFAULT 'menunggu',
    petugas_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (no_rm) REFERENCES pasien(no_rm) ON DELETE CASCADE,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- Table: pemeriksaan
-- Menyimpan data pemeriksaan pasien
-- ============================================
CREATE TABLE pemeriksaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) NOT NULL,
    tanggal_pemeriksaan DATE NOT NULL,
    dokter_id INT NOT NULL,
    keluhan TEXT NOT NULL,
    tekanan_darah VARCHAR(20),
    suhu DECIMAL(4,1),
    nadi INT,
    pernapasan INT,
    berat_badan DECIMAL(5,2),
    tinggi_badan DECIMAL(5,2),
    diagnosis TEXT NOT NULL,
    terapi TEXT NOT NULL,
    ammnesis TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (no_rm) REFERENCES pasien(no_rm) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- ============================================
-- Table: obat
-- Menyimpan master data obat
-- ============================================
CREATE TABLE obat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_obat VARCHAR(20) NOT NULL UNIQUE,
    nama_obat VARCHAR(100) NOT NULL,
    kategori VARCHAR(50),
    satuan VARCHAR(20),
    harga DECIMAL(10,2),
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- Table: resep
-- Menyimpan data resep farmasi
-- ============================================
CREATE TABLE resep (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_resep VARCHAR(20) NOT NULL UNIQUE,
    no_rm VARCHAR(20) NOT NULL,
    tanggal_resep DATE NOT NULL,
    dokter_id INT NOT NULL,
    apoteker_id INT,
    catatan TEXT,
    status ENUM('pending', 'diproses', 'selesai') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (no_rm) REFERENCES pasien(no_rm) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (apoteker_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- Table: resep_detail
-- Menyimpan detail obat dalam resep
-- ============================================
CREATE TABLE resep_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resep_id INT NOT NULL,
    obat_id INT NOT NULL,
    jumlah INT NOT NULL,
    dosis TEXT NOT NULL,
    harga DECIMAL(10,2),
    FOREIGN KEY (resep_id) REFERENCES resep(id) ON DELETE CASCADE,
    FOREIGN KEY (obat_id) REFERENCES obat(id) ON DELETE RESTRICT
);

-- ============================================
-- Table: pembayaran
-- Menyimpan data pembayaran
-- ============================================
CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_invoice VARCHAR(20) NOT NULL UNIQUE,
    no_rm VARCHAR(20) NOT NULL,
    tanggal_pembayaran DATE NOT NULL,
    biaya_pendaftaran DECIMAL(10,2) DEFAULT 0,
    biaya_konsultasi DECIMAL(10,2) DEFAULT 0,
    biaya_obat DECIMAL(10,2) DEFAULT 0,
    biaya_lab DECIMAL(10,2) DEFAULT 0,
    total_bayar DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('tunai', 'debit', 'kredit', 'bpjs', 'asuransi') NOT NULL,
    kasir_id INT,
    status ENUM('pending', 'lunas', 'batal') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (no_rm) REFERENCES pasien(no_rm) ON DELETE CASCADE,
    FOREIGN KEY (kasir_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- Table: wearable_data
-- Menyimpan data dari wearable device
-- ============================================
CREATE TABLE wearable_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) NOT NULL,
    waktu_pembacaan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detak_jantung INT,
    spo2 INT,
    status ENUM('normal', 'warning', 'danger') DEFAULT 'normal',
    device_id VARCHAR(50),
    FOREIGN KEY (no_rm) REFERENCES pasien(no_rm) ON DELETE CASCADE,
    INDEX idx_waktu (waktu_pembacaan),
    INDEX idx_no_rm (no_rm)
);

-- -- ============================================
-- -- Insert sample data for testing
-- -- ============================================

-- -- Sample users
-- INSERT INTO users (username, password, nama_lengkap, email, role, status) VALUES
-- ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@klinik.com', 'admin', 'aktif'),
-- ('dr.siti', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Siti Nurhaliza, Sp.PD', 'siti@klinik.com', 'dokter', 'aktif'),
-- ('dr.budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Santoso, Sp.JP', 'budi@klinik.com', 'dokter', 'aktif'),
-- ('petugas1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ani Wijaya', 'ani@klinik.com', 'pendaftaran', 'aktif'),
-- ('apoteker1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dewi Kusuma', 'dewi@klinik.com', 'farmasi', 'aktif'),
-- ('kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rina Saputri', 'rina@klinik.com', 'kasir', 'aktif'),
-- ('ahmad', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Suryanto', 'ahmad@email.com', 'pasien', 'aktif');

-- -- Sample pasien
-- INSERT INTO pasien (no_rm, nik, nama, tanggal_lahir, jenis_kelamin, alamat, telepon, user_id) VALUES
-- ('RM2025001', '3201012345670001', 'Ahmad Suryanto', '1985-03-15', 'L', 'Jl. Merdeka No. 123, Jakarta Pusat', '081234567890', 7),
-- ('RM2025002', '3201012345670002', 'Siti Aminah', '1990-07-20', 'P', 'Jl. Sudirman No. 45, Jakarta Selatan', '081234567891', NULL),
-- ('RM2025003', '3201012345670003', 'Budi Hartono', '1978-12-10', 'L', 'Jl. Thamrin No. 78, Jakarta Barat', '081234567892', NULL);

-- -- Sample obat
-- INSERT INTO obat (kode_obat, nama_obat, kategori, satuan, harga, stok) VALUES
-- ('OBT001', 'Paracetamol 500mg', 'Analgesik', 'Tablet', 500, 1000),
-- ('OBT002', 'Amoxicillin 500mg', 'Antibiotik', 'Kapsul', 2000, 500),
-- ('OBT003', 'Captopril 12.5mg', 'Antihipertensi', 'Tablet', 1500, 800),
-- ('OBT004', 'Vitamin C 500mg', 'Vitamin', 'Tablet', 1000, 1200),
-- ('OBT005', 'Omeprazole 20mg', 'Antasida', 'Kapsul', 3000, 300);

-- -- Sample pemeriksaan
-- INSERT INTO pemeriksaan (no_rm, tanggal_pemeriksaan, dokter_id, keluhan, tekanan_darah, suhu, nadi, pernapasan, diagnosis, terapi) VALUES
-- ('RM2025001', '2025-12-01', 2, 'Demam dan batuk sejak 3 hari', '120/80', 38.5, 85, 22, 'ISPA (Infeksi Saluran Pernapasan Akut)', 'Paracetamol 3x500mg, Amoxicillin 3x500mg, Istirahat cukup'),
-- ('RM2025001', '2025-11-15', 3, 'Pemeriksaan rutin kesehatan jantung', '140/90', 36.5, 75, 18, 'Kondisi jantung normal, hipertensi ringan', 'Captopril 2x12.5mg, Diet rendah garam, Olahraga teratur');

-- -- Sample wearable data
-- INSERT INTO wearable_data (no_rm, waktu_pembacaan, detak_jantung, spo2, status) VALUES
-- ('RM2025001', '2025-12-03 14:30:00', 72, 98, 'normal'),
-- ('RM2025001', '2025-12-03 12:00:00', 78, 97, 'normal'),
-- ('RM2025001', '2025-12-03 09:15:00', 68, 98, 'normal'),
-- ('RM2025001', '2025-12-03 06:30:00', 65, 97, 'normal'),
-- ('RM2025001', '2025-12-02 22:00:00', 70, 98, 'normal');

-- -- ============================================
-- -- Create indexes for better performance
-- -- ============================================
-- CREATE INDEX idx_pasien_nik ON pasien(nik);
-- CREATE INDEX idx_pasien_nama ON pasien(nama);
-- CREATE INDEX idx_pemeriksaan_tanggal ON pemeriksaan(tanggal_pemeriksaan);
-- CREATE INDEX idx_pembayaran_tanggal ON pembayaran(tanggal_pembayaran);
-- CREATE INDEX idx_resep_tanggal ON resep(tanggal_resep);

-- -- ============================================
-- -- Notes:
-- -- Default password for all sample users: 'password'
-- -- Password is hashed using bcrypt
-- -- ============================================
