-- Warning: column statistics not supported by the server.
-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: SIK
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pasien`
--

DROP TABLE IF EXISTS `pasien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) DEFAULT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `keluhan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pasien`),
  UNIQUE KEY `no_rm` (`no_rm`),
  KEY `idx_pasien_nik` (`nik`),
  KEY `idx_pasien_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pasien`
--

LOCK TABLES `pasien` WRITE;
/*!40000 ALTER TABLE `pasien` DISABLE KEYS */;
INSERT INTO `pasien` VALUES (1,'RM2025001','3201012345670001','Ahmad Suryanto','1985-03-15','L','Jl. Merdeka No. 123, Jakarta','081234567890','Demam dan batuk sejak 3 hari','2025-12-01 17:22:51'),(2,'RM2025002','3201012345670002','Siti Nurhaliza','1990-07-22','P','Jl. Gatot Subroto No. 45, Bandung','081234567891','Sakit kepala berkepanjangan','2025-12-01 17:22:51');
/*!40000 ALTER TABLE `pasien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT,
  `no_invoice` varchar(20) NOT NULL,
  `no_rm` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `kasir` varchar(100) NOT NULL,
  `biaya_pendaftaran` int(11) NOT NULL,
  `biaya_konsultasi` int(11) NOT NULL,
  `biaya_obat` int(11) DEFAULT NULL,
  `biaya_lab` int(11) DEFAULT NULL,
  `total` int(11) NOT NULL,
  `metode` enum('tunai','debit','kredit','bpjs','asuransi') NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pembayaran`),
  KEY `no_rm` (`no_rm`),
  KEY `idx_pembayaran_tanggal` (`tanggal`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan` (
  `id_pemeriksaan` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `dokter` varchar(100) NOT NULL,
  `tekanan_darah` varchar(20) DEFAULT NULL,
  `suhu` decimal(4,1) DEFAULT NULL,
  `nadi` int(11) DEFAULT NULL,
  `pernapasan` int(11) DEFAULT NULL,
  `berat_badan` decimal(5,1) DEFAULT NULL,
  `tinggi_badan` int(11) DEFAULT NULL,
  `anamnesis` text NOT NULL,
  `diagnosis` text NOT NULL,
  `terapi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pemeriksaan`),
  KEY `no_rm` (`no_rm`),
  KEY `idx_pemeriksaan_tanggal` (`tanggal`),
  CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemeriksaan`
--

LOCK TABLES `pemeriksaan` WRITE;
/*!40000 ALTER TABLE `pemeriksaan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pemeriksaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resep`
--

DROP TABLE IF EXISTS `resep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resep` (
  `id_resep` int(11) NOT NULL AUTO_INCREMENT,
  `no_resep` varchar(20) NOT NULL,
  `no_rm` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `dokter` varchar(100) NOT NULL,
  `apoteker` varchar(100) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('pending','processed','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_resep`),
  KEY `no_rm` (`no_rm`),
  KEY `idx_resep_tanggal` (`tanggal`),
  CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep`
--

LOCK TABLES `resep` WRITE;
/*!40000 ALTER TABLE `resep` DISABLE KEYS */;
/*!40000 ALTER TABLE `resep` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resep_obat`
--

DROP TABLE IF EXISTS `resep_obat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resep_obat` (
  `id_obat` int(11) NOT NULL AUTO_INCREMENT,
  `id_resep` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` int(11) DEFAULT NULL,
  `dosis` text NOT NULL,
  PRIMARY KEY (`id_obat`),
  KEY `id_resep` (`id_resep`),
  CONSTRAINT `resep_obat_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep` (`id_resep`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep_obat`
--

LOCK TABLES `resep_obat` WRITE;
/*!40000 ALTER TABLE `resep_obat` DISABLE KEYS */;
/*!40000 ALTER TABLE `resep_obat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `session_id` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `id_user` (`id_user`),
  KEY `idx_sessions_last_activity` (`last_activity`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('m02l80qf8jotiksdc6ra3cithh',1,'2025-12-01 18:14:13','2025-12-01 18:14:32','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pendaftaran','dokter','farmasi','laboratorium','kasir','pasien') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_rm` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  KEY `no_rm` (`no_rm`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$5SOSA02vmsCq6QMIMAtb4O7YhmVJJFiRdDaNQN6GTM0k8sR0BsTT.','admin','Administrator Klinik','admin@klinik.com',NULL,1,'2025-12-01 17:22:51','2025-12-01 18:14:13'),(2,'dr_sinta','$2y$10$5SOSA02vmsCq6QMIMAtb4O7YhmVJJFiRdDaNQN6GTM0k8sR0BsTT.','dokter','Dr. Sinta Wijaya, Sp.PD','sinta@klinik.com',NULL,1,'2025-12-01 17:22:51',NULL),(3,'apt_budi','$2y$10$5SOSA02vmsCq6QMIMAtb4O7YhmVJJFiRdDaNQN6GTM0k8sR0BsTT.','farmasi','Budi Santoso, S.Farm','budi@klinik.com',NULL,1,'2025-12-01 17:22:51',NULL),(4,'kasir_ani','$2y$10$5SOSA02vmsCq6QMIMAtb4O7YhmVJJFiRdDaNQN6GTM0k8sR0BsTT.','kasir','Ani Lestari','ani@klinik.com',NULL,1,'2025-12-01 17:22:51',NULL),(5,'reg_doni','$2y$10$5SOSA02vmsCq6QMIMAtb4O7YhmVJJFiRdDaNQN6GTM0k8sR0BsTT.','pendaftaran','Doni Prasetyo','doni@klinik.com',NULL,1,'2025-12-01 17:22:51',NULL),(6,'irul','$2y$10$C9O2fBAjC5qeIoYJRkWqzeBvg6KFoDEsLr4DBrKQGeZ.RfGSjEyvO','pasien','irul',NULL,NULL,1,'2025-12-01 17:53:16','2025-12-01 18:13:13'),(7,'staf','$2y$10$5SOSA02vmsCq6QMIMAtb4O7YhmVJJFiRdDaNQN6GTM0k8sR0BsTT.','pendaftaran','staf',NULL,NULL,1,'2025-12-01 17:54:15','2025-12-01 18:12:51');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `v_pemeriksaan_lengkap`
--

DROP TABLE IF EXISTS `v_pemeriksaan_lengkap`;
/*!50001 DROP VIEW IF EXISTS `v_pemeriksaan_lengkap`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_pemeriksaan_lengkap` AS SELECT 
 1 AS `id_pemeriksaan`,
 1 AS `no_rm`,
 1 AS `nama_pasien`,
 1 AS `jenis_kelamin`,
 1 AS `tanggal`,
 1 AS `dokter`,
 1 AS `diagnosis`,
 1 AS `terapi`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_resep_lengkap`
--

DROP TABLE IF EXISTS `v_resep_lengkap`;
/*!50001 DROP VIEW IF EXISTS `v_resep_lengkap`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_resep_lengkap` AS SELECT 
 1 AS `id_resep`,
 1 AS `no_resep`,
 1 AS `no_rm`,
 1 AS `nama_pasien`,
 1 AS `tanggal`,
 1 AS `dokter`,
 1 AS `apoteker`,
 1 AS `status`,
 1 AS `daftar_obat`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_users`
--

DROP TABLE IF EXISTS `v_users`;
/*!50001 DROP VIEW IF EXISTS `v_users`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_users` AS SELECT 
 1 AS `id_user`,
 1 AS `username`,
 1 AS `role`,
 1 AS `nama_lengkap`,
 1 AS `email`,
 1 AS `no_rm`,
 1 AS `is_active`,
 1 AS `created_at`,
 1 AS `last_login`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `wearable_data`
--

DROP TABLE IF EXISTS `wearable_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wearable_data` (
  `id_data` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `tanggal_waktu` datetime NOT NULL,
  `detak_jantung` int(11) DEFAULT NULL,
  `tekanan_darah` varchar(20) DEFAULT NULL,
  `suhu_tubuh` decimal(4,1) DEFAULT NULL,
  `langkah_harian` int(11) DEFAULT NULL,
  `kalori_terbakar` int(11) DEFAULT NULL,
  `kualitas_tidur` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_data`),
  KEY `no_rm` (`no_rm`),
  CONSTRAINT `wearable_data_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wearable_data`
--

LOCK TABLES `wearable_data` WRITE;
/*!40000 ALTER TABLE `wearable_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `wearable_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `v_pemeriksaan_lengkap`
--

/*!50001 DROP VIEW IF EXISTS `v_pemeriksaan_lengkap`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_pemeriksaan_lengkap` AS select `p`.`id_pemeriksaan` AS `id_pemeriksaan`,`p`.`no_rm` AS `no_rm`,`pas`.`nama` AS `nama_pasien`,`pas`.`jenis_kelamin` AS `jenis_kelamin`,`p`.`tanggal` AS `tanggal`,`p`.`dokter` AS `dokter`,`p`.`diagnosis` AS `diagnosis`,`p`.`terapi` AS `terapi` from (`pemeriksaan` `p` join `pasien` `pas` on(`p`.`no_rm` = `pas`.`no_rm`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_resep_lengkap`
--

/*!50001 DROP VIEW IF EXISTS `v_resep_lengkap`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_resep_lengkap` AS select `r`.`id_resep` AS `id_resep`,`r`.`no_resep` AS `no_resep`,`r`.`no_rm` AS `no_rm`,`pas`.`nama` AS `nama_pasien`,`r`.`tanggal` AS `tanggal`,`r`.`dokter` AS `dokter`,`r`.`apoteker` AS `apoteker`,`r`.`status` AS `status`,group_concat(concat(`ro`.`nama_obat`,' (',`ro`.`jumlah`,')') separator ', ') AS `daftar_obat` from ((`resep` `r` join `pasien` `pas` on(`r`.`no_rm` = `pas`.`no_rm`)) left join `resep_obat` `ro` on(`r`.`id_resep` = `ro`.`id_resep`)) group by `r`.`id_resep` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_users`
--

/*!50001 DROP VIEW IF EXISTS `v_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_users` AS select `users`.`id_user` AS `id_user`,`users`.`username` AS `username`,`users`.`role` AS `role`,`users`.`nama_lengkap` AS `nama_lengkap`,`users`.`email` AS `email`,`users`.`no_rm` AS `no_rm`,`users`.`is_active` AS `is_active`,`users`.`created_at` AS `created_at`,`users`.`last_login` AS `last_login` from `users` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-02  1:19:31
