-- Tabel untuk sistem resep farmasi
-- Sistem Informasi Klinik

CREATE TABLE IF NOT EXISTS resep (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_resep VARCHAR(20) UNIQUE NOT NULL,
    pemeriksaan_id INT NOT NULL,
    no_rm VARCHAR(20) NOT NULL,
    dokter_id INT NOT NULL,
    tanggal_resep DATETIME NOT NULL,
    catatan_resep TEXT NOT NULL,
    status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending',
    biaya DECIMAL(10,2) DEFAULT 0,
    farmasi_id INT DEFAULT NULL,
    tanggal_dispensing DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (pemeriksaan_id) REFERENCES pemeriksaan(id) ON DELETE CASCADE,
    FOREIGN KEY (no_rm) REFERENCES pasien(no_rm) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (farmasi_id) REFERENCES users(id) ON DELETE SET NULL,

    INDEX idx_no_resep (no_resep),
    INDEX idx_status (status),
    INDEX idx_tanggal (tanggal_resep)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel untuk detail item resep (obat-obatan)
CREATE TABLE IF NOT EXISTS resep_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resep_id INT NOT NULL,
    nama_obat VARCHAR(100) NOT NULL,
    dosis VARCHAR(50) NOT NULL,
    jumlah INT NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    aturan_pakai TEXT NOT NULL,
    harga_satuan DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (resep_id) REFERENCES resep(id) ON DELETE CASCADE,

    INDEX idx_resep (resep_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data untuk testing
-- INSERT INTO resep (no_resep, pemeriksaan_id, no_rm, dokter_id, tanggal_resep, catatan_resep, status)
-- VALUES ('RX2025001', 1, 'RM2025001', 2, NOW(), 'Paracetamol 3x1 sehari, Amoxicillin 3x500mg', 'pending');
