# Sistem Informasi Klinik

## Milestone 1 - Database Setup & Authentication System

Sistem Informasi Kesehatan berbasis web untuk mengelola operasional klinik, termasuk pendaftaran pasien, pemeriksaan, farmasi, dan pembayaran dengan sistem autentikasi berbasis role.

---

## 📋 Daftar Isi

- [Fitur Milestone 1](#fitur-milestone-1)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Project](#struktur-project)
- [Instalasi](#instalasi)
- [Cara Menggunakan](#cara-menggunakan)
- [Role dan Hak Akses](#role-dan-hak-akses)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [Screenshots](#screenshots)

---

## 🚀 Fitur Milestone 1

### ✅ Sistem Autentikasi
- [x] Register user dengan berbagai role
- [x] Login dengan username dan password (password hashing)
- [x] Session management dengan database tracking
- [x] Logout dengan pembersihan session
- [x] Role-based access control (RBAC)

### ✅ Database
- [x] 8 tabel dengan relasi foreign key yang proper
- [x] User authentication tables (users, sessions)
- [x] Medical records tables (pasien, pemeriksaan, resep, pembayaran)
- [x] Wearable data integration table

### ✅ User Interface
- [x] Halaman login dengan validasi
- [x] Halaman register dengan password validation
- [x] Dashboard dengan menu berbasis role
- [x] Form pendaftaran pasien
- [x] Form pemeriksaan dokter
- [x] Form farmasi/resep
- [x] Form pembayaran
- [x] Responsive design untuk mobile

### ✅ Security
- [x] Password hashing dengan bcrypt
- [x] Prepared statements untuk mencegah SQL injection
- [x] Session security (HttpOnly, SameSite)
- [x] Role-based authorization

---

## 💻 Teknologi yang Digunakan

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Server:** Apache / Nginx
- **Tools:** XAMPP / WAMP / LAMP

---

## 📁 Struktur Project

```
Tubes-SIK/
│
├── css/
│   └── style.css                 # Stylesheet lengkap
│
├── js/
│   └── validation.js             # JavaScript validasi form
│
├── includes/
│   ├── config.php                # Konfigurasi database
│   ├── auth.php                  # Fungsi autentikasi
│   ├── session.php               # Session management
│   ├── header.php                # Header component
│   └── footer.php                # Footer component
│
├── handlers/
│   ├── login_process.php         # Handler login
│   └── register_process.php      # Handler registrasi
│
├── pages/
│   ├── pendaftaran.php           # Form pendaftaran pasien
│   ├── pemeriksaan.php           # Form pemeriksaan dokter
│   ├── farmasi.php               # Form resep farmasi
│   └── pembayaran.php            # Form pembayaran
│
├── database_setup.sql            # SQL untuk setup database
├── index.php                     # Landing page
├── login.php                     # Halaman login
├── register.php                  # Halaman registrasi
├── dashboard.php                 # Dashboard utama
├── logout.php                    # Handler logout
└── README.md                     # Dokumentasi ini
```

---

## 🔧 Instalasi

### 1. Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.3+
- Apache/Nginx web server
- XAMPP/WAMP/LAMP (recommended untuk development)

### 2. Clone/Download Project

```bash
# Jika menggunakan Git
git clone [repository-url]

# Atau download ZIP dan extract ke folder htdocs (XAMPP)
# Contoh: C:\xampp\htdocs\Tubes-SIK
```

### 3. Setup Database

1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Jangan perlu buat database manual (sudah ada di SQL file)
3. Import file `database_setup.sql`:
   - **JANGAN** pilih database tertentu dulu
   - Langsung klik tab "Import" di halaman utama phpMyAdmin
   - Pilih file `database_setup.sql`
   - Klik "Go"
   - Database `SIK` akan otomatis dibuat beserta semua tabelnya

**PENTING:** File SQL sudah include DROP TABLE, jadi aman untuk di-import berkali-kali.

**Atau jalankan via command line:**

```bash
mysql -u root -p < database_setup.sql
```

### 4. Konfigurasi Koneksi Database

Edit file `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Sesuaikan dengan username MySQL Anda
define('DB_PASS', '');              // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'SIK');           // HARUS 'SIK' sesuai database yang dibuat
define('SITE_URL', 'http://localhost/Tubes-SIK'); // Sesuaikan dengan path project
```

### 5. Set Permissions (Linux/Mac)

```bash
chmod -R 755 /path/to/Tubes-SIK
```

### 6. Start Server

- **XAMPP:** Start Apache dan MySQL dari Control Panel
- **Command Line PHP:**
  ```bash
  php -S localhost:8000
  ```

### 7. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/Tubes-SIK
```

---

## 📖 Cara Menggunakan

### Login

1. Buka http://localhost/Tubes-SIK
2. Anda akan diarahkan ke halaman login
3. Gunakan salah satu akun demo:

**Akun Demo:**
- **Admin:** `admin` / `Password123`
- **Dokter:** `dr_sinta` / `Password123`
- **Farmasi:** `apt_budi` / `Password123`
- **Kasir:** `kasir_ani` / `Password123`
- **Pendaftaran:** `reg_doni` / `Password123`

### Registrasi User Baru

1. Klik "Daftar di sini" di halaman login
2. Isi form registrasi:
   - Username (minimal 4 karakter, hanya huruf, angka, underscore)
   - Password (minimal 8 karakter, harus ada huruf besar, kecil, dan angka)
   - Konfirmasi Password
   - Nama Lengkap
   - Email (opsional)
   - Pilih Role
3. Klik "Daftar"
4. Setelah berhasil, login dengan username dan password yang baru dibuat

### Menggunakan Dashboard

Setelah login, Anda akan melihat dashboard dengan menu sesuai role:

- **Admin:** Akses ke semua menu
- **Pendaftaran:** Menu Pendaftaran Pasien
- **Dokter:** Menu Pemeriksaan
- **Farmasi:** Menu Farmasi/Resep
- **Kasir:** Menu Pembayaran

### Logout

Klik tombol "Logout" di navigation bar (pojok kanan atas).

---

## 👥 Role dan Hak Akses

| Role | Akses Menu |
|------|------------|
| **Admin** | Semua menu (Pendaftaran, Pemeriksaan, Farmasi, Pembayaran, Laboratorium, User Management, Reports) |
| **Pendaftaran** | Pendaftaran Pasien |
| **Dokter** | Pemeriksaan Pasien |
| **Farmasi** | Resep dan Obat |
| **Kasir** | Pembayaran |
| **Laboratorium** | Hasil Lab |
| **Pasien** | Data Medis Pribadi |

---

## 🗄️ Database Schema

### Tabel Utama:

1. **users** - Data user untuk autentikasi
   - id_user, username, password (hashed), role, nama_lengkap, email, no_rm, is_active, created_at, last_login

2. **sessions** - Tracking session login
   - session_id, id_user, login_time, last_activity, ip_address, user_agent

3. **pasien** - Data pasien
   - id_pasien, no_rm, nik, nama, tanggal_lahir, jenis_kelamin, alamat, telepon, keluhan

4. **pemeriksaan** - Data pemeriksaan dokter
   - id_pemeriksaan, no_rm, tanggal, dokter, tanda_vital, anamnesis, diagnosis, terapi

5. **resep** - Header resep
   - id_resep, no_resep, no_rm, tanggal, dokter, apoteker, catatan, status

6. **resep_obat** - Detail obat dalam resep
   - id_obat, id_resep, nama_obat, jumlah, harga, dosis

7. **pembayaran** - Data pembayaran pasien
   - id_pembayaran, no_invoice, no_rm, tanggal, kasir, rincian_biaya, total, metode, status

8. **wearable_data** - Data dari wearable devices
   - id_data, no_rm, tanggal_waktu, detak_jantung, tekanan_darah, suhu_tubuh, dll

### Relasi:
- users.no_rm → pasien.no_rm (untuk role pasien)
- pemeriksaan.no_rm → pasien.no_rm
- resep.no_rm → pasien.no_rm
- resep_obat.id_resep → resep.id_resep
- pembayaran.no_rm → pasien.no_rm
- wearable_data.no_rm → pasien.no_rm
- sessions.id_user → users.id_user

---

## 🧪 Testing

### Checklist Testing Milestone 1:

#### Authentication
- [x] Register user baru
- [x] Login dengan credentials yang benar
- [x] Login gagal dengan credentials salah
- [x] Password ter-hash di database
- [x] Session tersimpan setelah login
- [x] Redirect ke dashboard setelah login
- [x] Logout menghapus session

#### Authorization
- [x] Admin dapat akses semua menu
- [x] Dokter hanya dapat akses Pemeriksaan
- [x] Farmasi hanya dapat akses Farmasi
- [x] Kasir hanya dapat akses Pembayaran
- [x] Pendaftaran hanya dapat akses Pendaftaran
- [x] Redirect dengan error jika akses tanpa izin

#### Database
- [x] Semua tabel terbuat dengan benar
- [x] Foreign key constraints berfungsi
- [x] Data sample ter-insert
- [x] Prepared statements mencegah SQL injection

#### UI/UX
- [x] Form login dan register berfungsi
- [x] Dashboard menampilkan menu sesuai role
- [x] Navigation bar dinamis berdasarkan role
- [x] Responsive di mobile
- [x] Error dan success messages muncul

---

## 📸 Screenshots

(Tambahkan screenshots di sini saat presentasi)

1. Login Page
2. Register Page
3. Dashboard Admin
4. Dashboard Dokter
5. Form Pendaftaran
6. Form Pemeriksaan
7. Form Farmasi
8. Form Pembayaran

---

## 🐛 Troubleshooting

### Error: Connection failed
- Pastikan MySQL/MariaDB berjalan
- Cek username dan password di `includes/config.php`
- Pastikan database `database_klinik` sudah dibuat

### Error: 404 Not Found
- Pastikan SITE_URL di `includes/config.php` sesuai dengan path project Anda
- Jika menggunakan XAMPP, path biasanya: `http://localhost/Tubes-SIK`

### Error: Session tidak tersimpan
- Pastikan PHP session sudah enabled
- Cek permission folder session PHP

### CSS/JS tidak load
- Cek path di `<link>` dan `<script>` tag
- Pastikan folder `css` dan `js` ada dan accessible

---

## 📝 To-Do Next Milestone

- [ ] Implementasi CRUD lengkap untuk semua modul
- [ ] Pencarian dan filter data
- [ ] Export data ke PDF/Excel
- [ ] Dashboard dengan statistik dan grafik
- [ ] Integrasi wearable device (IoT)
- [ ] API untuk mobile app
- [ ] Unit testing

---

## 👨‍💻 Developer

Tugas Besar - Sistem Informasi Kesehatan
Milestone 1: Database Setup & Authentication System

---

## 📄 License

Project ini dibuat untuk keperluan akademik.

---

## 🙏 Acknowledgments

- PHP Documentation
- MySQL Documentation
- MDN Web Docs
- Stack Overflow Community

---

**Selamat menggunakan Sistem Informasi Klinik! 🏥**

Untuk pertanyaan atau bug report, silakan hubungi developer.
