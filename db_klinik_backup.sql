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
-- Current Database: `db_klinik`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `db_klinik` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `db_klinik`;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hasil_lab`
--

LOCK TABLES `hasil_lab` WRITE;
/*!40000 ALTER TABLE `hasil_lab` DISABLE KEYS */;
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (1,1,4,'2025-12-10 23:23:44','eweq','eqweq','2025-12-10 16:23:44','2025-12-10 16:23:44');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (2,5,4,'2025-12-11 08:44:10','1231','','2025-12-11 01:44:10','2025-12-11 01:44:10');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (3,4,4,'2025-12-11 08:44:14','13131','','2025-12-11 01:44:14','2025-12-11 01:44:14');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (4,3,4,'2025-12-11 08:44:18','13123','','2025-12-11 01:44:18','2025-12-11 01:44:18');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (5,2,4,'2025-12-11 08:44:22','13123','','2025-12-11 01:44:22','2025-12-11 01:44:22');
INSERT INTO `hasil_lab` (`id`, `permintaan_lab_id`, `petugas_lab_id`, `tanggal_hasil`, `hasil_pemeriksaan`, `catatan_petugas`, `created_at`, `updated_at`) VALUES (6,6,2,'2025-12-11 10:38:35','aman','','2025-12-11 03:38:35','2025-12-11 03:38:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pasien`
--

LOCK TABLES `pasien` WRITE;
/*!40000 ALTER TABLE `pasien` DISABLE KEYS */;
INSERT INTO `pasien` (`id`, `no_rm`, `nik`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `user_id`, `created_at`, `updated_at`) VALUES (1,'RM001','12345678','irul','2025-12-18','L','adasdad','08123456',NULL,'2025-12-10 11:48:14','2025-12-10 11:48:14');
INSERT INTO `pasien` (`id`, `no_rm`, `nik`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `user_id`, `created_at`, `updated_at`) VALUES (2,'RM0000002','123456','Meli','2025-12-05','P','wwqeq','qeq3123',1,'2025-12-10 12:02:40','2025-12-10 12:02:40');
INSERT INTO `pasien` (`id`, `no_rm`, `nik`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `user_id`, `created_at`, `updated_at`) VALUES (3,'RM0000003','123','123','2025-12-11','P','123','123',5,'2025-12-10 17:09:35','2025-12-10 17:09:35');
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
INSERT INTO `pembayaran` (`id`, `no_invoice`, `no_rm`, `tanggal_pembayaran`, `biaya_pendaftaran`, `biaya_konsultasi`, `biaya_obat`, `biaya_lab`, `total_bayar`, `metode_pembayaran`, `kasir_id`, `status`, `created_at`) VALUES (1,'INV20250001','RM0000003','2025-12-11',50000.00,100000.00,0.00,0.00,150000.00,'tunai',2,'lunas','2025-12-11 01:45:37');
INSERT INTO `pembayaran` (`id`, `no_invoice`, `no_rm`, `tanggal_pembayaran`, `biaya_pendaftaran`, `biaya_konsultasi`, `biaya_obat`, `biaya_lab`, `total_bayar`, `metode_pembayaran`, `kasir_id`, `status`, `created_at`) VALUES (3,'INV20250002','RM0000003','2025-12-11',50000.00,100000.00,1000.00,100.00,151100.00,'tunai',2,'lunas','2025-12-11 03:31:18');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (6,'RM0000003','2025-12-11',2,'','123',123.0,123,123,123.00,123.00,'123','123','2025-12-10 17:10:20','2025-12-10 17:10:20');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (7,'RM0000003','2025-12-11',2,'','123',123.0,123,123,123.00,123.00,'123','123','2025-12-11 01:35:02','2025-12-11 01:35:02');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (8,'RM0000003','2025-12-11',2,'','123',123.0,123,123,123.00,123.00,'123','123','2025-12-11 01:35:05','2025-12-11 01:35:05');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (9,'RM0000003','2025-12-11',2,'','123',123.0,123,123,123.00,123.00,'123','123','2025-12-11 01:36:32','2025-12-11 01:36:32');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (10,'RM0000003','2025-12-11',2,'','123',123.0,123,123,123.00,123.00,'123','123','2025-12-11 01:36:34','2025-12-11 01:36:34');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (11,'RM0000003','2025-12-11',2,'','1',1.0,11,1,1.00,1.00,'1','1','2025-12-11 01:37:16','2025-12-11 01:37:16');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (12,'RM0000003','2025-12-11',2,'','1',1.0,11,1,1.00,1.00,'1','1','2025-12-11 01:41:15','2025-12-11 01:41:15');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (13,'RM0000003','2025-12-11',2,'','1',1.0,1,1,1.00,1.00,'11','11','2025-12-11 01:48:35','2025-12-11 01:48:35');
INSERT INTO `pemeriksaan` (`id`, `no_rm`, `tanggal_pemeriksaan`, `dokter_id`, `keluhan`, `tekanan_darah`, `suhu`, `nadi`, `pernapasan`, `berat_badan`, `tinggi_badan`, `diagnosis`, `ammnesis`, `created_at`, `updated_at`) VALUES (14,'RM0000003','2025-12-11',2,'','2',2.0,2,2,2.00,2.00,'0','sakit perut','2025-12-11 03:38:04','2025-12-11 03:38:04');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pendaftaran`
--

LOCK TABLES `pendaftaran` WRITE;
/*!40000 ALTER TABLE `pendaftaran` DISABLE KEYS */;
INSERT INTO `pendaftaran` (`id`, `no_rm`, `keluhan`, `tanggal_daftar`, `status`, `petugas_id`, `created_at`) VALUES (1,'RM001','dasdad','2025-12-10 11:48:14','menunggu',2,'2025-12-10 11:48:14');
INSERT INTO `pendaftaran` (`id`, `no_rm`, `keluhan`, `tanggal_daftar`, `status`, `petugas_id`, `created_at`) VALUES (2,'RM0000002','dadas','2025-12-10 12:02:40','menunggu',2,'2025-12-10 12:02:40');
INSERT INTO `pendaftaran` (`id`, `no_rm`, `keluhan`, `tanggal_daftar`, `status`, `petugas_id`, `created_at`) VALUES (3,'RM0000003','123','2025-12-10 17:09:35','menunggu',2,'2025-12-10 17:09:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permintaan_lab`
--

LOCK TABLES `permintaan_lab` WRITE;
/*!40000 ALTER TABLE `permintaan_lab` DISABLE KEYS */;
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (1,'LAB2025001',5,'RM0000002',3,'2025-12-10 23:21:59','Rontgen Thorax','rrrr','selesai',150000.00,'2025-12-10 16:21:59');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (2,'LAB2025002',9,'RM0000003',2,'2025-12-11 08:36:32','Rujukan Laboratorium','aa','selesai',0.00,'2025-12-11 01:36:32');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (3,'LAB2025003',10,'RM0000003',2,'2025-12-11 08:36:34','Rujukan Laboratorium','aa','selesai',0.00,'2025-12-11 01:36:34');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (4,'LAB2025004',11,'RM0000003',2,'2025-12-11 08:37:16','Rujukan Laboratorium','','selesai',0.00,'2025-12-11 01:37:16');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (5,'LAB2025005',12,'RM0000003',2,'2025-12-11 08:41:15','Rujukan Laboratorium','','selesai',0.00,'2025-12-11 01:41:15');
INSERT INTO `permintaan_lab` (`id`, `kode_lab`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_permintaan`, `jenis_tes`, `catatan_dokter`, `status`, `biaya`, `created_at`) VALUES (6,'LAB2025006',14,'RM0000003',2,'2025-12-11 10:38:04','Rujukan Laboratorium','','selesai',0.00,'2025-12-11 03:38:04');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep`
--

LOCK TABLES `resep` WRITE;
/*!40000 ALTER TABLE `resep` DISABLE KEYS */;
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (1,'RX20250001',12,'RM0000003',2,'2025-12-11 08:41:15','','selesai',1.00,2,'2025-12-11 08:48:10','2025-12-11 01:41:15','2025-12-11 01:48:10');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (2,'RX20250002',13,'RM0000003',2,'2025-12-11 08:48:35','','diproses',0.00,NULL,NULL,'2025-12-11 01:48:35','2025-12-11 03:28:58');
INSERT INTO `resep` (`id`, `no_resep`, `pemeriksaan_id`, `no_rm`, `dokter_id`, `tanggal_resep`, `catatan_resep`, `status`, `biaya`, `farmasi_id`, `tanggal_dispensing`, `created_at`, `updated_at`) VALUES (3,'RX20250003',14,'RM0000003',2,'2025-12-11 10:38:04','','selesai',1000.00,2,'2025-12-11 10:50:28','2025-12-11 03:38:04','2025-12-11 03:50:28');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep_detail`
--

LOCK TABLES `resep_detail` WRITE;
/*!40000 ALTER TABLE `resep_detail` DISABLE KEYS */;
INSERT INTO `resep_detail` (`id`, `resep_id`, `nama_obat`, `jumlah`, `satuan`, `dosis`, `harga_satuan`, `subtotal`) VALUES (1,3,'paracetom',1,'strip','ok',1000.00,1000.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wearable_data`
--

LOCK TABLES `wearable_data` WRITE;
/*!40000 ALTER TABLE `wearable_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `wearable_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-11 12:05:04
