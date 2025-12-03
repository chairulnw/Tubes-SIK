# Mockup HTML - Sistem Informasi Klinik

Folder ini berisi mockup HTML statis untuk semua halaman Sistem Informasi Klinik. Mockup ini dibuat untuk visualisasi dan presentasi desain interface tanpa fungsionalitas backend.

## Daftar Halaman

### 1. Login (`login.html`)
- Halaman login untuk masuk ke sistem
- Form input username dan password
- Link ke halaman registrasi
- Menampilkan demo credentials

### 2. Registrasi (`register.html`)
- Halaman pendaftaran akun baru
- Form input data user dan pemilihan role
- Validasi password menggunakan JavaScript
- Link ke halaman login

### 3. Dashboard (`dashboard.html`)
- Halaman utama setelah login
- Menampilkan menu grid dengan berbagai modul:
  - Pendaftaran Pasien
  - Pemeriksaan
  - Farmasi
  - Pembayaran
  - Data Medis Saya
  - Kelola User (Admin)

### 4. Pendaftaran Pasien (`pendaftaran.html`)
- Form untuk mendaftarkan pasien baru atau lama
- Input data rekam medis
- Input data pribadi pasien (NIK, nama, tanggal lahir, jenis kelamin, alamat, telepon)
- Input keluhan pasien

### 5. Pemeriksaan (`pemeriksaan.html`)
- Form pemeriksaan dokter
- Input informasi pemeriksaan (No RM, tanggal, nama dokter)
- Input tanda vital (tekanan darah, suhu, nadi, pernapasan, BB, TB)
- Input diagnosis dan terapi (anamnesis, diagnosis, terapi)

### 6. Farmasi (`farmasi.html`)
- Form resep farmasi
- Input informasi resep (No resep, No RM, tanggal, dokter, apoteker)
- Daftar obat yang bisa ditambah dinamis
- Fitur tambah/hapus obat menggunakan JavaScript
- Input nama obat, jumlah, harga, dan dosis

### 7. Pembayaran (`pembayaran.html`)
- Form pembayaran pasien
- Input informasi pembayaran (No invoice, No RM, tanggal, kasir)
- Rincian biaya (pendaftaran, konsultasi, obat, laboratorium)
- Auto-calculate total pembayaran menggunakan JavaScript
- Pilihan metode pembayaran (tunai, debit, kredit, BPJS, asuransi)

### 8. Data Medis Saya (`data-medis.html`)
- Halaman untuk pasien melihat data medis pribadi
- Informasi pasien (No RM, NIK, nama, tanggal lahir, dll)
- Riwayat pemeriksaan dengan detail lengkap
- Data wearable device real-time:
  - Detak jantung (Heart Rate) dengan nilai rata-rata, tertinggi, dan terendah
  - Saturasi oksigen darah (SpO2) dengan nilai rata-rata, tertinggi, dan terendah
  - Grafik trend detak jantung 24 jam terakhir
  - Tabel riwayat pembacaan sensor
  - Status kesehatan (Normal/Warning/Danger)
- Animasi pulse pada card wearable untuk simulasi real-time monitoring

### 9. Kelola User (`kelola-user.html`)
- Halaman manajemen user (khusus admin)
- Tabel daftar user dengan informasi lengkap
- Fitur search dan filter berdasarkan role
- Modal form untuk tambah/edit user
- Fungsi hapus user dengan konfirmasi
- Badge untuk membedakan role user
- Status user (aktif/nonaktif)

## Fitur

### Fitur Umum
- **Responsive Design**: Semua halaman responsive dan mobile-friendly
- **Navigasi**: Header navigasi konsisten di semua halaman (kecuali login/register)
- **Footer**: Footer konsisten di semua halaman
- **Alert Messages**: Support untuk menampilkan pesan error dan success

### Fitur JavaScript
- **Validasi Form Register**: Validasi password match dan minimal length
- **Dynamic Obat List**: Tambah/hapus obat di halaman farmasi
- **Auto Calculate**: Hitung total pembayaran otomatis di halaman pembayaran
- **Modal Management**: Modal popup untuk tambah/edit user di halaman kelola user
- **Wearable Animation**: Animasi pulse pada card wearable device untuk simulasi monitoring real-time

## Cara Menggunakan

1. Buka file HTML di browser
2. Untuk testing lokal, bisa menggunakan:
   - Live Server di VS Code
   - Atau buka langsung file HTML di browser
3. Semua link antar halaman sudah terhubung

## Struktur File

```
mockup/
├── css/
│   └── style.css          # File CSS yang sama dengan versi PHP
├── login.html             # Halaman login
├── register.html          # Halaman registrasi
├── dashboard.html         # Halaman dashboard
├── pendaftaran.html       # Form pendaftaran pasien
├── pemeriksaan.html       # Form pemeriksaan
├── farmasi.html           # Form farmasi
├── pembayaran.html        # Form pembayaran
├── data-medis.html        # Halaman data medis pasien
├── kelola-user.html       # Halaman manajemen user
└── README.md              # Dokumentasi ini
```

## Catatan

- File-file ini adalah mockup statis (tidak terhubung ke database)
- Form action mengarah ke "#" (tidak memproses data)
- Untuk implementasi lengkap, gunakan versi PHP di folder utama
- CSS menggunakan file yang sama dengan versi PHP untuk konsistensi tampilan

## Pengembangan Selanjutnya

Mockup ini bisa dikembangkan menjadi:
- Prototype interaktif dengan JavaScript
- Testing UI/UX
- Presentasi desain ke stakeholder
- Template untuk implementasi framework frontend (React, Vue, dll)

---

**Sistem Informasi Klinik** - Mockup HTML Version 1.0
