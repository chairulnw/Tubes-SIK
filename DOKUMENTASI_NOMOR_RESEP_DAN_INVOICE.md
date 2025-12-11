# Dokumentasi Sistem Nomor Resep dan Invoice
## Sistem Informasi Klinik - Klinik Sehat

---

## 1. SISTEM NOMOR RESEP (Prescription Number System)

### 1.1 Format Nomor Resep
**Format**: `RX + YEAR + XXXX`
- **Contoh**: RX20250001, RX20250002, RX20250003
- **RX**: Prefix untuk resep (Prescription)
- **YEAR**: Tahun saat ini (4 digit)
- **XXXX**: Sequential number (4 digit dengan leading zero)
- **Reset**: Counter reset setiap tahun

### 1.2 Tabel Database

#### Tabel `resep`
```sql
CREATE TABLE resep (
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Tabel `resep_detail` (Optional - untuk detail item obat)
```sql
CREATE TABLE resep_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resep_id INT NOT NULL,
    nama_obat VARCHAR(100) NOT NULL,
    dosis VARCHAR(50) NOT NULL,
    jumlah INT NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    aturan_pakai TEXT NOT NULL,
    harga_satuan DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(10,2) DEFAULT 0
);
```

### 1.3 Workflow Sistem Resep

#### **TAHAP 1: Pembuatan Resep oleh Dokter**
1. Dokter melakukan pemeriksaan di [pemeriksaan.php](pages/pemeriksaan.php)
2. Dokter mencentang checkbox "Rujuk ke Farmasi"
3. Dokter menulis catatan resep (contoh: "Paracetamol 3x1, Amoxicillin 500mg 3x1")
4. Saat submit form pemeriksaan:
   - Sistem otomatis generate nomor resep (format: RX2025XXXX)
   - Data tersimpan ke tabel `resep` dengan status 'pending'
   - Foreign key linked ke `pemeriksaan_id`, `no_rm`, dan `dokter_id`

#### **TAHAP 2: Proses Resep oleh Farmasi**
1. Staff farmasi login dan buka [farmasi.php](pages/farmasi.php)
2. Lihat list resep dengan status pending/diproses
3. Klik button "Proses" → redirect ke [farmasi-proses.php](pages/farmasi-proses.php)
4. Status otomatis berubah jadi 'diproses' saat halaman form dibuka
5. Input detail obat (nama obat, jumlah, satuan, harga, dosis/aturan pakai)
6. Bisa tambah multiple obat dengan button "Tambah Obat"
7. Total biaya otomatis dihitung (jumlah × harga untuk semua obat)
8. Submit → Data tersimpan ke `resep_detail`, status jadi 'selesai', `tanggal_dispensing` di-set, `farmasi_id` di-set

#### **TAHAP 3: Pembayaran**
1. Biaya resep dari field `biaya` di tabel `resep`
2. Status resep harus 'selesai' untuk dihitung dalam pembayaran
3. Terintegrasi dengan sistem pembayaran

### 1.4 File-file Terkait Sistem Resep

| File | Fungsi |
|------|--------|
| `pages/pemeriksaan.php` | Form pemeriksaan dokter dengan checkbox rujukan farmasi |
| `handlers/pemeriksaan_process.php` | Proses simpan pemeriksaan + generate nomor resep |
| `pages/farmasi.php` | Halaman staff farmasi untuk melihat list resep |
| `pages/farmasi-proses.php` | Form untuk input detail obat dan proses resep |
| `handlers/farmasi_process.php` | Proses selesaikan resep (simpan detail obat, update status, biaya, tanggal) |
| `handlers/resep_status_process.php` | Update status resep via AJAX |
| `handlers/get_resep_detail.php` | Get detail resep untuk view |

### 1.5 Fungsi Generate Nomor Resep

**Lokasi**: `handlers/pemeriksaan_process.php`

```php
function generateNoResep($conn) {
    $prefix = "RX" . date('Y');
    $stmt = $conn->prepare("SELECT no_resep FROM resep WHERE no_resep LIKE ? ORDER BY no_resep DESC LIMIT 1");
    $like_pattern = $prefix . "%";
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = intval(substr($row['no_resep'], -4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    $stmt->close();

    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}
```

**Logic**:
1. Prefix = "RX" + tahun saat ini (contoh: RX2025)
2. Query last resep dengan prefix yang sama
3. Ambil 4 digit terakhir, convert ke integer, tambah 1
4. Format dengan leading zero (str_pad)
5. Return nomor resep lengkap

---

## 2. SISTEM NOMOR INVOICE (Invoice Number System)

### 2.1 Format Nomor Invoice
**Format**: `INV + YEAR + XXXX`
- **Contoh**: INV20250001, INV20250002, INV20250003
- **INV**: Prefix untuk invoice
- **YEAR**: Tahun saat ini (4 digit)
- **XXXX**: Sequential number (4 digit dengan leading zero)
- **Reset**: Counter reset setiap tahun

### 2.2 Cara Kerja

#### **Auto-Generate Saat Pembayaran**
1. Kasir buka [pembayaran.php](pages/pembayaran.php)
2. Field "Nomor Invoice" sudah readonly dengan value "(Auto-generate)"
3. Kasir input data pembayaran lainnya (No. RM, biaya, metode)
4. Saat submit form:
   - Backend otomatis generate nomor invoice
   - Format: INV + YEAR + counter 4 digit
   - Tidak perlu input manual

#### **Keuntungan Auto-Generate**:
- ✅ Tidak ada duplikat nomor invoice
- ✅ Format konsisten
- ✅ Mengurangi human error
- ✅ Tracking lebih mudah

### 2.3 File-file Terkait Sistem Invoice

| File | Fungsi |
|------|--------|
| `pages/pembayaran.php` | Form pembayaran dengan field invoice readonly |
| `handlers/pembayaran_process.php` | Proses pembayaran + generate nomor invoice |

### 2.4 Fungsi Generate Nomor Invoice

**Lokasi**: `handlers/pembayaran_process.php`

```php
function generateNoInvoice($conn) {
    $prefix = "INV" . date('Y');
    $stmt = $conn->prepare("SELECT no_invoice FROM pembayaran WHERE no_invoice LIKE ? ORDER BY no_invoice DESC LIMIT 1");
    $like_pattern = $prefix . "%";
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = intval(substr($row['no_invoice'], -4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    $stmt->close();

    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}
```

**Logic**:
1. Prefix = "INV" + tahun saat ini (contoh: INV2025)
2. Query last invoice dengan prefix yang sama
3. Ambil 4 digit terakhir, convert ke integer, tambah 1
4. Format dengan leading zero (str_pad)
5. Return nomor invoice lengkap

### 2.5 Perubahan pada Form Pembayaran

**SEBELUM** (Manual Input):
```html
<input type="text" name="no_invoice" required placeholder="Contoh: INV2025001">
```

**SESUDAH** (Auto-generate):
```html
<input type="text" name="no_invoice" readonly value="(Auto-generate)" style="background-color: #f0f0f0; cursor: not-allowed;">
<small>Nomor invoice akan digenerate otomatis oleh sistem</small>
```

---

## 3. INTEGRASI DENGAN SISTEM LAIN

### 3.1 Integrasi Resep dengan Pemeriksaan
- Saat dokter buat pemeriksaan dengan rujukan farmasi
- Auto-create record di tabel `resep`
- Link via `pemeriksaan_id` foreign key

### 3.2 Integrasi Resep dengan Pembayaran
- Biaya obat dari field `biaya` di tabel `resep`
- Hanya resep dengan status 'selesai' yang dihitung
- Query untuk get biaya obat:
```php
SELECT SUM(biaya) as total_obat
FROM resep
WHERE no_rm = ? AND DATE(tanggal_resep) = ? AND status = 'selesai'
```

### 3.3 Integrasi Invoice dengan Pembayaran
- Nomor invoice auto-generate saat create pembayaran
- Tersimpan di tabel `pembayaran`
- Dapat di-query untuk reporting dan tracking

---

## 4. PENOMORAN LAINNYA DI SISTEM

Untuk konsistensi, sistem menggunakan pattern penomoran yang sama:

| Jenis | Format | Contoh | Reset |
|-------|--------|--------|-------|
| Rekam Medis | RM + YEAR + XXXX | RM20250001 | Per tahun |
| Lab | LAB + YEAR + XXX | LAB2025001 | Per tahun |
| Resep | RX + YEAR + XXXX | RX20250001 | Per tahun |
| Invoice | INV + YEAR + XXXX | INV20250001 | Per tahun |

**Keuntungan Pattern Konsisten**:
- Mudah diingat dan dipahami
- Format uniform di seluruh sistem
- Tracking per tahun lebih mudah
- Scalable (dapat menampung ribuan record per tahun)

---

## 5. TESTING

### 5.1 Test Skenario Resep

1. **Test Create Resep**:
   - Login sebagai dokter
   - Buat pemeriksaan pasien
   - Centang "Rujuk ke Farmasi"
   - Isi catatan resep
   - Submit → Cek nomor resep ter-generate (RX2025XXXX)

2. **Test Proses Resep**:
   - Login sebagai farmasi
   - Lihat resep masuk
   - Klik "Proses" → Status jadi 'diproses'
   - Klik "Selesaikan" → Input biaya → Status jadi 'selesai'

3. **Test Sequential Numbering**:
   - Buat 3 resep berturut-turut
   - Cek nomor: RX20250001, RX20250002, RX20250003
   - Pastikan tidak ada duplikat

### 5.2 Test Skenario Invoice

1. **Test Auto-Generate Invoice**:
   - Login sebagai kasir
   - Buka form pembayaran
   - Field invoice readonly
   - Submit pembayaran
   - Cek success message dengan nomor invoice (INV2025XXXX)

2. **Test Sequential Numbering**:
   - Buat 3 pembayaran berturut-turut
   - Cek nomor: INV20250001, INV20250002, INV20250003
   - Pastikan tidak ada duplikat

3. **Test Year Reset**:
   - Ubah tanggal sistem ke tahun depan (misal: 2026)
   - Buat pembayaran baru
   - Nomor harus mulai dari INV20260001

---

## 6. TROUBLESHOOTING

### 6.1 Masalah: Nomor Duplikat

**Penyebab**: Race condition jika 2 user submit bersamaan

**Solusi**:
- Tabel sudah ada `UNIQUE` constraint pada field `no_resep` dan `no_invoice`
- Database akan reject insert jika ada duplikat
- Error handling di PHP akan catch dan retry

### 6.2 Masalah: Nomor Tidak Sequential

**Penyebab**: Ada record yang di-delete

**Solusi**:
- Ini normal behavior
- Tidak masalah jika ada gap (misal: 001, 002, 005)
- Yang penting adalah uniqueness, bukan continuity

### 6.3 Masalah: Resep Tidak Muncul di Farmasi

**Checklist**:
- Pastikan checkbox "Rujuk ke Farmasi" dicentang
- Cek tabel `resep` apakah record ter-insert
- Cek status resep (harus 'pending' atau 'diproses')
- Cek role user yang login (harus 'farmasi' atau 'admin')

---

## 7. FUTURE ENHANCEMENTS

### 7.1 Resep
- Tambah tabel `resep_detail` untuk detail item obat
- Cetak resep dalam format PDF
- Barcode untuk tracking
- Notifikasi ke pasien saat resep selesai

### 7.2 Invoice
- Cetak invoice dalam format PDF
- Email invoice ke pasien
- QR code untuk verifikasi
- Invoice template yang customizable

---

## Kesimpulan

Sistem nomor resep dan invoice sudah terintegrasi dengan baik dalam Sistem Informasi Klinik:

✅ **Nomor Resep (RX2025XXXX)**:
- Auto-generate saat dokter buat rujukan farmasi
- Terintegrasi dengan workflow pemeriksaan → farmasi → pembayaran
- Status tracking (pending → diproses → selesai)

✅ **Nomor Invoice (INV2025XXXX)**:
- Auto-generate saat kasir proses pembayaran
- Field readonly di form untuk mencegah input manual
- Format konsisten dan unique

Kedua sistem menggunakan pattern yang sama untuk konsistensi dan mudah di-maintain.
