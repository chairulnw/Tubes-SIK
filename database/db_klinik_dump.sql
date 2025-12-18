-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_klinik
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `hasil_lab`
--

DROP TABLE IF EXISTS `hasil_lab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hasil_lab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permintaan_lab_id` int(11) NOT NULL,
  `petugas_lab_id` int(11) NOT NULL,
  `tanggal_hasil` datetime NOT NULL,
  `hasil_pemeriksaan` text NOT NULL,
  `catatan_petugas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `petugas_lab_id` (`petugas_lab_id`),
  KEY `idx_permintaan` (`permintaan_lab_id`),
  CONSTRAINT `hasil_lab_ibfk_1` FOREIGN KEY (`permintaan_lab_id`) REFERENCES `permintaan_lab` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hasil_lab_ibfk_2` FOREIGN KEY (`petugas_lab_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hasil_lab`
--

LOCK TABLES `hasil_lab` WRITE;
/*!40000 ALTER TABLE `hasil_lab` DISABLE KEYS */;
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (1,1,4,'2025-12-10 23:23:44','eweq','eqweq','2025-12-10 16:23:44','2025-12-10 16:23:44');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (7,8,2,'2025-12-18 00:46:46','tes','','2025-12-17 17:46:46','2025-12-17 17:46:46');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (8,9,2,'2025-12-18 03:36:13','ok','','2025-12-17 20:36:13','2025-12-17 20:36:13');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (9,10,2,'2025-12-18 04:02:15','ok','','2025-12-17 21:02:15','2025-12-17 21:02:15');
/*!40000 ALTER TABLE `hasil_lab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenis_tes_lab`
--

DROP TABLE IF EXISTS `jenis_tes_lab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jenis_tes_lab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_tes` varchar(20) NOT NULL,
  `nama_tes` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `biaya_standar` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `persiapan` text DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_tes` (`kode_tes`),
  KEY `idx_kategori` (`kategori`),
  KEY `idx_aktif` (`aktif`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_tes_lab`
--

LOCK TABLES `jenis_tes_lab` WRITE;
/*!40000 ALTER TABLE `jenis_tes_lab` DISABLE KEYS */;
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (1,'LAB001','Darah Lengkap','Hematologi',75000.00,'Pemeriksaan komponen darah lengkap','Puasa tidak diperlukan',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (2,'LAB002','Gula Darah Puasa','Kimia Klinik',50000.00,'Pemeriksaan kadar glukosa darah puasa','Puasa 8-12 jam',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (3,'LAB003','Kolesterol Total','Kimia Klinik',60000.00,'Pemeriksaan kadar kolesterol total','Puasa 12 jam',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (4,'LAB004','Urin Lengkap','Urinalisis',40000.00,'Pemeriksaan urin rutin','Urin pagi lebih baik',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (5,'LAB005','Asam Urat','Kimia Klinik',45000.00,'Pemeriksaan kadar asam urat','Hindari makanan tinggi purin 24 jam sebelumnya',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (6,'LAB006','Fungsi Hati (SGOT/SGPT)','Kimia Klinik',85000.00,'Pemeriksaan fungsi liver','Puasa 8 jam',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (7,'LAB007','Fungsi Ginjal (Ureum/Kreatinin)','Kimia Klinik',80000.00,'Pemeriksaan fungsi ginjal','Puasa 8 jam',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
INSERT INTO `jenis_tes_lab` (`id`, `kode_tes`, `nama_tes`, `kategori`, `biaya_standar`, `deskripsi`, `persiapan`, `aktif`, `created_at`, `updated_at`) VALUES (8,'LAB008','Rontgen Thorax','Radiologi',150000.00,'Foto rontgen dada','Lepas perhiasan logam',1,'2025-12-10 16:09:39','2025-12-10 16:09:39');
/*!40000 ALTER TABLE `jenis_tes_lab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obat`
--

DROP TABLE IF EXISTS `obat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_obat` varchar(20) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_obat` (`kode_obat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obat`
--

LOCK TABLES `obat` WRITE;
/*!40000 ALTER TABLE `obat` DISABLE KEYS */;
/*!40000 ALTER TABLE `obat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pasien`
--

DROP TABLE IF EXISTS `pasien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pasien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_rm` (`no_rm`),
  UNIQUE KEY `nik` (`nik`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pasien_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pasien`
--

LOCK TABLES `pasien` WRITE;
/*!40000 ALTER TABLE `pasien` DISABLE KEYS */;
INSERT INTO `pasien` (`id`, `no_rm`, `nik`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `user_id`, `created_at`, `updated_at`) VALUES (1,'RM001','12345678','irul','2025-12-18','L','adasdad','08123456',NULL,'2025-12-10 11:48:14','2025-12-10 11:48:14');
INSERT INTO `pasien` (`id`, `no_rm`, `nik`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `user_id`, `created_at`, `updated_at`) VALUES (2,'RM0000002','123456','Meli','2025-12-05','P','wwqeq','qeq3123',1,'2025-12-10 12:02:40','2025-12-10 12:02:40');
INSERT INTO `pasien` (`id`, `no_rm`, `nik`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `user_id`, `created_at`, `updated_at`) VALUES (5,'RM0000005','1234567890123456789','aruna','2025-12-12','P','rajawali','08123456789',7,'2025-12-12 09:30:34','2025-12-12 09:30:34');
INSERT INTO `pasien` (`id`, `no_rm`, `nik`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `user_id`, `created_at`, `updated_at`) VALUES (6,'RM0000006','123','chairul','2025-12-18','L','cipinang','123',10,'2025-12-17 17:52:03','2025-12-17 17:52:03');
/*!40000 ALTER TABLE `pasien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_invoice` varchar(20) NOT NULL,
  `no_rm` varchar(20) NOT NULL,
  `tanggal_pembayaran` date NOT NULL,
  `biaya_pendaftaran` decimal(10,2) DEFAULT 0.00,
  `biaya_konsultasi` decimal(10,2) DEFAULT 0.00,
  `biaya_obat` decimal(10,2) DEFAULT 0.00,
  `biaya_lab` decimal(10,2) DEFAULT 0.00,
  `total_bayar` decimal(10,2) NOT NULL,
  `metode_pembayaran` enum('tunai','debit','kredit','bpjs','asuransi') NOT NULL,
  `kasir_id` int(11) DEFAULT NULL,
  `status` enum('pending','lunas','batal') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_invoice` (`no_invoice`),
  KEY `no_rm` (`no_rm`),
  KEY `kasir_id` (`kasir_id`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE CASCADE,
  CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`kasir_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemeriksaan`
--

DROP TABLE IF EXISTS `pemeriksaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pemeriksaan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `dokter_id` int(11) NOT NULL,
  `keluhan` text NOT NULL,
  `tekanan_darah` varchar(20) DEFAULT NULL,
  `suhu` decimal(4,1) DEFAULT NULL,
  `nadi` int(11) DEFAULT NULL,
  `pernapasan` int(11) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `diagnosis` text NOT NULL,
  `ammnesis` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `no_rm` (`no_rm`),
  KEY `dokter_id` (`dokter_id`),
  CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_ibfk_2` FOREIGN KEY (`dokter_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemeriksaan`
--

LOCK TABLES `pemeriksaan` WRITE;
/*!40000 ALTER TABLE `pemeriksaan` DISABLE KEYS */;
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (2,'RM0000002','2025-12-10',2,'','321',312.0,312,312,313.00,312.00,'13213','312','2025-12-10 12:04:17','2025-12-10 12:04:17');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (3,'RM0000002','2025-12-10',3,'','100',100.0,100,100,100.00,100.00,'0','sakit dada ku','2025-12-10 12:06:29','2025-12-10 12:06:29');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (4,'RM0000002','2025-12-10',3,'','120',35.0,60,27,70.00,170.00,'0','riwayat pentakir','2025-12-10 14:56:04','2025-12-10 14:56:04');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (5,'RM0000002','2025-12-10',3,'','1',1.0,1,1,1.00,1.00,'0','rr','2025-12-10 16:21:59','2025-12-10 16:21:59');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (16,'RM0000005','2025-12-12',8,'','120',36.0,80,20,50.00,155.00,'0','mual','2025-12-12 09:32:40','2025-12-12 09:32:40');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (17,'RM0000002','2025-12-18',3,'','2',2.0,2,2,2.00,2.00,'0','s','2025-12-17 17:45:09','2025-12-17 17:45:09');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (18,'RM0000006','2025-12-18',2,'','2',2.0,2,2,2.00,2.00,'0','lemah jantung','2025-12-17 20:35:40','2025-12-17 20:35:40');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (19,'RM0000006','2025-12-18',2,'','2',2.0,2,2,2.00,2.00,'2','lemah jantung','2025-12-17 20:41:15','2025-12-17 20:41:15');
/*!40000 ALTER TABLE `pemeriksaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pendaftaran`
--

DROP TABLE IF EXISTS `pendaftaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pendaftaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `keluhan` text NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('menunggu','dipanggil','selesai','batal') DEFAULT 'menunggu',
  `petugas_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `no_rm` (`no_rm`),
  KEY `petugas_id` (`petugas_id`),
  CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE CASCADE,
  CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pendaftaran`
--

LOCK TABLES `pendaftaran` WRITE;
/*!40000 ALTER TABLE `pendaftaran` DISABLE KEYS */;
INSERT INTO `pendaftaran` (`id`, `no_rm`, `keluhan`, `tanggal_daftar`, `status`, `petugas_id`, `created_at`) VALUES (1,'RM001','dasdad','2025-12-10 11:48:14','menunggu',2,'2025-12-10 11:48:14');
INSERT INTO `pendaftaran` (`id`, `no_rm`, `keluhan`, `tanggal_daftar`, `status`, `petugas_id`, `created_at`) VALUES (2,'RM0000002','dadas','2025-12-10 12:02:40','menunggu',2,'2025-12-10 12:02:40');
INSERT INTO `pendaftaran` (`id`, `no_rm`, `keluhan`, `tanggal_daftar`, `status`, `petugas_id`, `created_at`) VALUES (5,'RM0000005','sakit perut','2025-12-12 09:30:34','menunggu',9,'2025-12-12 09:30:34');
INSERT INTO `pendaftaran` (`id`, `no_rm`, `keluhan`, `tanggal_daftar`, `status`, `petugas_id`, `created_at`) VALUES (7,'RM0000006','lemah jantung','2025-12-17 17:52:03','menunggu',2,'2025-12-17 17:52:03');
/*!40000 ALTER TABLE `pendaftaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permintaan_lab`
--

DROP TABLE IF EXISTS `permintaan_lab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permintaan_lab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_lab` varchar(20) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `no_rm` varchar(20) NOT NULL,
  `dokter_id` int(11) NOT NULL,
  `tanggal_permintaan` datetime NOT NULL,
  `jenis_tes` varchar(100) NOT NULL,
  `catatan_dokter` text DEFAULT NULL,
  `status` enum('pending','diproses','selesai','dibatalkan') DEFAULT 'pending',
  `biaya` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_lab` (`kode_lab`),
  KEY `dokter_id` (`dokter_id`),
  KEY `idx_status` (`status`),
  KEY `idx_no_rm` (`no_rm`),
  KEY `idx_pemeriksaan` (`pemeriksaan_id`),
  CONSTRAINT `permintaan_lab_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permintaan_lab_ibfk_2` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE CASCADE,
  CONSTRAINT `permintaan_lab_ibfk_3` FOREIGN KEY (`dokter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permintaan_lab`
--

LOCK TABLES `permintaan_lab` WRITE;
/*!40000 ALTER TABLE `permintaan_lab` DISABLE KEYS */;
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (1,'LAB2025001',5,'RM0000002',3,'2025-12-10 23:21:59','Rontgen Thorax','rrrr','selesai',150000.00,'2025-12-10 16:21:59');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (8,'LAB2025008',17,'RM0000002',3,'2025-12-18 00:45:09','Rujukan Laboratorium','tes','selesai',0.00,'2025-12-17 17:45:09');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (9,'LAB2025009',18,'RM0000006',2,'2025-12-18 03:35:40','Rujukan Laboratorium','','selesai',0.00,'2025-12-17 20:35:40');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (10,'LAB2025010',19,'RM0000006',2,'2025-12-18 03:41:15','Rujukan Laboratorium','','selesai',0.00,'2025-12-17 20:41:15');
/*!40000 ALTER TABLE `permintaan_lab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resep`
--

DROP TABLE IF EXISTS `resep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_resep` varchar(20) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `no_rm` varchar(20) NOT NULL,
  `dokter_id` int(11) NOT NULL,
  `tanggal_resep` datetime NOT NULL,
  `catatan_resep` text NOT NULL,
  `status` enum('pending','diproses','selesai','dibatalkan') DEFAULT 'pending',
  `biaya` decimal(10,2) DEFAULT 0.00,
  `farmasi_id` int(11) DEFAULT NULL,
  `tanggal_dispensing` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_resep` (`no_resep`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep`
--

LOCK TABLES `resep` WRITE;
/*!40000 ALTER TABLE `resep` DISABLE KEYS */;
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (1,'RX20250001',12,'RM0000003',2,'2025-12-11 08:41:15','','selesai',1.00,2,'2025-12-11 08:48:10','2025-12-11 01:41:15','2025-12-11 01:48:10');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (2,'RX20250002',13,'RM0000003',2,'2025-12-11 08:48:35','','diproses',0.00,NULL,NULL,'2025-12-11 01:48:35','2025-12-11 03:28:58');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (3,'RX20250003',14,'RM0000003',2,'2025-12-11 10:38:04','','selesai',1000.00,2,'2025-12-11 10:50:28','2025-12-11 03:38:04','2025-12-11 03:50:28');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (4,'RX20250004',15,'RM0000003',2,'2025-12-12 16:22:56','','pending',0.00,NULL,NULL,'2025-12-12 09:22:56','2025-12-12 09:22:56');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (5,'RX20250005',16,'RM0000005',8,'2025-12-12 16:32:40','omerazol','selesai',4.00,2,'2025-12-18 03:43:10','2025-12-12 09:32:40','2025-12-17 20:43:10');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (6,'RX20250006',17,'RM0000002',3,'2025-12-18 00:45:09','tes','selesai',10000.00,2,'2025-12-18 00:45:52','2025-12-17 17:45:09','2025-12-17 17:45:52');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (7,'RX20250007',18,'RM0000006',2,'2025-12-18 03:35:40','','selesai',6.00,2,'2025-12-18 03:42:48','2025-12-17 20:35:40','2025-12-17 20:42:48');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (8,'RX20250008',19,'RM0000006',2,'2025-12-18 03:41:15','','selesai',5.00,2,'2025-12-18 03:42:27','2025-12-17 20:41:15','2025-12-17 20:42:27');
/*!40000 ALTER TABLE `resep` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resep_detail`
--

DROP TABLE IF EXISTS `resep_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resep_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resep_id` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `dosis` text NOT NULL,
  `harga_satuan` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `resep_id` (`resep_id`),
  CONSTRAINT `resep_detail_ibfk_1` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep_detail`
--

LOCK TABLES `resep_detail` WRITE;
/*!40000 ALTER TABLE `resep_detail` DISABLE KEYS */;
INSERT INTO `resep_detail` (`id`, `resep_id`, `nama_obat`, `jumlah`, `satuan`, `dosis`, `harga_satuan`, `subtotal`) VALUES (1,3,'paracetom',1,'strip','ok',1000.00,1000.00);
INSERT INTO `resep_detail` (`id`, `resep_id`, `nama_obat`, `jumlah`, `satuan`, `dosis`, `harga_satuan`, `subtotal`) VALUES (2,6,'tes',1,'tablet','3x1',10000.00,10000.00);
INSERT INTO `resep_detail` (`id`, `resep_id`, `nama_obat`, `jumlah`, `satuan`, `dosis`, `harga_satuan`, `subtotal`) VALUES (3,8,'t',1,'tablet','t',5.00,5.00);
INSERT INTO `resep_detail` (`id`, `resep_id`, `nama_obat`, `jumlah`, `satuan`, `dosis`, `harga_satuan`, `subtotal`) VALUES (4,7,'r',2,'tablet','2',3.00,6.00);
INSERT INTO `resep_detail` (`id`, `resep_id`, `nama_obat`, `jumlah`, `satuan`, `dosis`, `harga_satuan`, `subtotal`) VALUES (5,5,'r',2,'tablet','2',2.00,4.00);
/*!40000 ALTER TABLE `resep_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','dokter','pendaftaran','farmasi','laboratorium','kasir','pasien') NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (1,'meli','$2y$10$5e2s0d9bxmUAbbneAtTJM.HT0PTimTXrTdF8UDLbnqzP/TzHaGEEu','meli','majl@gmail.com','pasien','aktif','2025-12-10 11:14:15','2025-12-10 11:14:15');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (2,'admin','$2y$10$3tc6YBkEAb5rQis7tQcUk.MU.DU997i5IsAEoe2tDuikd32b.e8cK','admin','admin@gmail.com','admin','aktif','2025-12-10 11:41:57','2025-12-10 11:41:57');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (3,'dinda','$2y$10$gGhO2eXxgWVTopAZ56jz3uIb6/kcbtwqAR.b1AnPiRxLBdp1kYjHm','dinda','dinda@gmail.com','dokter','aktif','2025-12-10 12:05:33','2025-12-10 12:05:33');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (4,'irul','$2y$10$Vqwhf.9gB.pqEEqZcnsaF.VExigmQcmmp10zLSmFE75z7ZdIJ0o.C','irul','irul@gmail.com','laboratorium','aktif','2025-12-10 12:16:40','2025-12-10 12:16:40');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (5,'adeenda','$2y$10$NHJs2ttyFwjFcA7MlRG6Gu2lMlzDVzMJPR0NF1RtQ7fRBdJrOub/G','adindakhai','adinda@gmail.com','pasien','aktif','2025-12-10 17:07:17','2025-12-10 17:07:17');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (6,'demo','$2y$10$liSWWnmqh8ecnouX5BWJVeh9q7qYbjvTQ/QnsGhPPuYnQ1o3SG3YW','demo','demo@gmail.com','admin','aktif','2025-12-11 04:23:31','2025-12-11 04:23:31');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (7,'runaruna','$2y$10$wwbZ0meXCXgGhMnIjzLyYunGFYHymGbyRcMV3XpxJ2uNdDxaASdLq','runa','contoh@gmail.com','pasien','aktif','2025-12-12 09:26:44','2025-12-12 09:26:44');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (8,'runaruni','$2y$10$rK.G6NB4bY1FVn8qDf0Wyey8DGI5JmpkBhLmVD/as8PO17r0Eowxe','runi','demo@gmail.com','dokter','aktif','2025-12-12 09:27:52','2025-12-12 09:27:52');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (9,'chacha','$2y$10$USN2GC6Obqoq4qFP9FqLFOI5PQpiNH0ctvJI.VVtNBUTHu7NOyKAK','chairulrul','contoh@gmail.com','pendaftaran','aktif','2025-12-12 09:28:53','2025-12-12 09:28:53');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES (10,'chairul','$2y$10$T1zxOgSfItAnOQrEUrcSme6M2QuzI3xWCTkW.w6tU/EadrnKKoHgq','chairul','chairul@gmai.com','pasien','aktif','2025-12-17 17:50:06','2025-12-17 17:50:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wearable_data`
--

DROP TABLE IF EXISTS `wearable_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wearable_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `waktu_pembacaan` timestamp NOT NULL DEFAULT current_timestamp(),
  `detak_jantung` int(11) DEFAULT NULL,
  `spo2` int(11) DEFAULT NULL,
  `status` enum('normal','warning','danger') DEFAULT 'normal',
  `device_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_waktu` (`waktu_pembacaan`),
  KEY `idx_no_rm` (`no_rm`),
  CONSTRAINT `wearable_data_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wearable_data`
--

LOCK TABLES `wearable_data` WRITE;
/*!40000 ALTER TABLE `wearable_data` DISABLE KEYS */;
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (1,'RM0000002','2025-12-17 13:51:48',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (2,'RM0000002','2025-12-17 13:52:17',55,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (3,'RM0000002','2025-12-17 13:52:17',45,NULL,'danger','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (4,'RM0000002','2025-12-17 14:13:46',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (5,'RM0000002','2025-12-17 14:38:34',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (6,'RM0000002','2025-12-17 15:50:04',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (7,'RM0000002','2025-12-17 15:50:34',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (8,'RM0000002','2025-12-17 15:51:04',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (9,'RM0000002','2025-12-17 15:51:34',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (10,'RM0000002','2025-12-17 15:52:05',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (11,'RM0000002','2025-12-17 15:52:55',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (12,'RM0000002','2025-12-17 15:53:25',72,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (13,'RM0000002','2025-12-17 15:53:55',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (14,'RM0000002','2025-12-17 15:54:27',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (15,'RM0000002','2025-12-17 15:54:58',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (16,'RM0000002','2025-12-17 15:55:29',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (17,'RM0000002','2025-12-17 15:55:59',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (18,'RM0000002','2025-12-17 15:56:29',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (19,'RM0000002','2025-12-17 15:57:41',61,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (20,'RM0000002','2025-12-17 15:58:12',55,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (21,'RM0000002','2025-12-17 15:58:42',70,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (22,'RM0000002','2025-12-17 17:27:56',53,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (23,'RM0000002','2025-12-17 17:28:27',59,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (24,'RM0000002','2025-12-17 17:28:57',57,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (25,'RM0000002','2025-12-17 17:29:27',57,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (26,'RM0000002','2025-12-17 17:29:57',74,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (27,'RM0000002','2025-12-17 17:30:28',74,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (28,'RM0000002','2025-12-17 17:30:58',77,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (29,'RM0000002','2025-12-17 17:31:28',44,NULL,'danger','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (30,'RM0000002','2025-12-17 17:31:59',51,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (31,'RM0000002','2025-12-17 17:32:29',54,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (32,'RM0000002','2025-12-17 17:32:59',60,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (33,'RM0000002','2025-12-17 17:33:32',41,NULL,'danger','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (34,'RM0000002','2025-12-17 17:34:02',41,NULL,'danger','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (35,'RM0000002','2025-12-17 17:34:34',50,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (36,'RM0000002','2025-12-17 17:35:14',49,NULL,'danger','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (37,'RM0000002','2025-12-17 17:35:45',77,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (38,'RM0000002','2025-12-17 17:36:15',77,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (39,'RM0000002','2025-12-17 17:36:45',62,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (40,'RM0000002','2025-12-17 17:37:15',51,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (41,'RM0000002','2025-12-17 17:37:45',50,NULL,'warning','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (42,'RM0000002','2025-12-17 17:38:15',49,NULL,'danger','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (43,'RM0000002','2025-12-17 17:38:59',77,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (44,'RM0000002','2025-12-17 17:39:29',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (45,'RM0000002','2025-12-17 17:40:01',86,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (46,'RM0000002','2025-12-17 17:40:31',63,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (47,'RM0000002','2025-12-17 17:41:15',47,NULL,'danger','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (48,'RM0000002','2025-12-17 17:41:53',64,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (49,'RM0000002','2025-12-17 17:42:23',91,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (50,'RM0000002','2025-12-18 00:24:01',75,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (51,'RM0000005','2025-12-18 00:24:41',82,NULL,'normal','HR_001');
INSERT INTO `wearable_data` (`id`, `no_rm`, `waktu_pembacaan`, `detak_jantung`, `spo2`, `status`, `device_id`) VALUES (52,'RM0000006','2025-12-18 00:24:47',68,NULL,'normal','HR_001');
/*!40000 ALTER TABLE `wearable_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'db_klinik'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-18  8:48:05
