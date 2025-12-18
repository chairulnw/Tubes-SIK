# Database Backup - Sistem Informasi Klinik

## File Database
- `db_klinik_dump.sql` - Full database dump (struktur + data)

## Cara Restore Database

### Method 1: Menggunakan MySQL Command Line

```bash
# Windows (XAMPP)
C:\xampp\mysql\bin\mysql.exe -u root -p < db_klinik_dump.sql

# Linux/Mac
mysql -u root -p < db_klinik_dump.sql
```

### Method 2: Menggunakan phpMyAdmin

1. Buka phpMyAdmin di browser: `http://localhost/phpmyadmin`
2. Klik tab **Import**
3. Klik **Choose File** dan pilih `db_klinik_dump.sql`
4. Klik **Go** untuk memulai import

### Method 3: Menggunakan MySQL Command dengan Database Selection

```bash
# Buat database baru (jika belum ada)
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS db_klinik;"

# Import dump file
C:\xampp\mysql\bin\mysql.exe -u root db_klinik < db_klinik_dump.sql
```

## Struktur Database

### Tabel Utama:
- **users** - Pengguna sistem (admin, dokter, farmasi, laboratorium, pendaftaran)
- **pasien** - Data pasien
- **pendaftaran** - Registrasi kunjungan pasien
- **pemeriksaan** - Rekam medis pemeriksaan dokter
- **wearable_data** - Data real-time dari IoT wearable device (Heart Rate)

### Tabel Laboratorium:
- **jenis_tes_lab** - Jenis-jenis tes laboratorium
- **permintaan_lab** - Permintaan pemeriksaan lab dari dokter
- **hasil_lab** - Hasil pemeriksaan laboratorium

### Tabel Farmasi:
- **obat** - Master data obat
- **resep** - Resep dari dokter
- **resep_detail** - Detail obat dalam resep

## Informasi Penting

### Default Users:
```
Admin:
  Username: admin
  Password: admin123

Dokter:
  Username: dokter
  Password: dokter123

Farmasi:
  Username: farmasi
  Password: farmasi123

Laboratorium:
  Username: laboratorium
  Password: lab123

Pendaftaran:
  Username: pendaftaran
  Password: pendaftaran123
```

**PENTING:** Segera ganti password default setelah instalasi untuk keamanan!

### Sample Pasien:
- **RM001** - irul
- **RM0000002** - Meli (memiliki data wearable)
- **RM0000005** - aruna
- **RM0000006** - chairul

## Fitur Database

### 1. Shared Wearable Device
Database mendukung **1 device untuk seluruh klinik**. Data wearable disimpan dengan `no_rm` untuk identifikasi pasien yang sedang diukur.

### 2. Status Calculation
Tabel `wearable_data` memiliki kolom `status` (ENUM: 'normal', 'warning', 'danger') yang dihitung otomatis berdasarkan:
- **Normal**: 60 ≤ BPM ≤ 100
- **Warning**: (50 ≤ BPM < 60) OR (100 < BPM ≤ 120)
- **Danger**: BPM < 50 OR BPM > 120

### 3. Foreign Key Relationships
- `pasien.user_id` → `users.id` (Optional link ke akun user)
- `pendaftaran.no_rm` → `pasien.no_rm`
- `pemeriksaan.no_rm` → `pasien.no_rm`
- `wearable_data.no_rm` → `pasien.no_rm`
- `permintaan_lab.pemeriksaan_id` → `pemeriksaan.id`
- `hasil_lab.permintaan_lab_id` → `permintaan_lab.id`

## Backup Schedule (Rekomendasi)

- **Daily**: Backup otomatis setiap hari (gunakan cron/task scheduler)
- **Weekly**: Backup mingguan disimpan di lokasi terpisah
- **Monthly**: Archive bulanan untuk audit

## Cara Membuat Backup Baru

```bash
# Backup dengan timestamp
C:\xampp\mysql\bin\mysqldump.exe -u root db_klinik > db_klinik_backup_2025-12-18.sql

# Backup terkompresi
C:\xampp\mysql\bin\mysqldump.exe -u root db_klinik | gzip > db_klinik_backup_2025-12-18.sql.gz
```

## Troubleshooting

### Error: "Access denied for user 'root'@'localhost'"
Tambahkan password jika MySQL root memiliki password:
```bash
C:\xampp\mysql\bin\mysql.exe -u root -p < db_klinik_dump.sql
```

### Error: "Unknown database 'db_klinik'"
Database harus dibuat terlebih dahulu:
```bash
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE db_klinik;"
```

### Error: "Table already exists"
Drop database terlebih dahulu (HATI-HATI: akan menghapus semua data):
```bash
C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE IF EXISTS db_klinik; CREATE DATABASE db_klinik;"
C:\xampp\mysql\bin\mysql.exe -u root db_klinik < db_klinik_dump.sql
```

## Database Version Info
- MySQL Version: 5.x / 8.x compatible
- Character Set: utf8mb4
- Collation: utf8mb4_general_ci
- Created: December 2025
- Last Updated: 2025-12-18
