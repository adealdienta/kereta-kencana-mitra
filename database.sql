-- =======================================================
-- SKEMA DATABASE: PR. KERETA KENCANA (Laravel Version)
-- Sesuai dengan progres produk resmi: Dwipantara, Sembada, Kereta Kencana
-- =======================================================

CREATE DATABASE IF NOT EXISTS db_kereta_kencana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_kereta_kencana;

-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: db_kereta_kencana
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Bapak Komari Yaman (Owner)','owner@keretakencana.com',NULL,'$2y$12$Z6X7w2XlMDgLsTZJm6mIpuTPawSaHjWttCD20O4xmxC.DyQz2JyUm','owner',NULL,'2026-09-17 01:58:20','2026-09-17 01:58:20'),(2,'Super Admin (Web Dev)','superadmin@keretakencana.com',NULL,'$2y$12$Z6X7w2XlMDgLsTZJm6mIpuTPawSaHjWttCD20O4xmxC.DyQz2JyUm','superadmin',NULL,'2026-09-17 01:58:20','2026-09-17 01:58:20'),(3,'Staf Operasional Pabrik','staff@keretakencana.com',NULL,'$2y$12$Z6X7w2XlMDgLsTZJm6mIpuTPawSaHjWttCD20O4xmxC.DyQz2JyUm','staff',NULL,'2026-09-17 01:58:20','2026-09-17 01:58:20'),(4,'Admin Gudang & Pemesanan','admin@keretakencana.com',NULL,'$2y$12$Z6X7w2XlMDgLsTZJm6mIpuTPawSaHjWttCD20O4xmxC.DyQz2JyUm','staff',NULL,'2026-09-17 01:58:20','2026-09-17 01:58:20');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('4TPxuvBBowHSKbKPgZPdfc7RQfZiZ4QdY8L0BlUv',NULL,'127.0.0.1','Symfony','eyJfdG9rZW4iOiJ1VFo1eWxacHZIY3lOOFRqRG1sVzhaOVdwemZFOXY3U3I0WWsxclR5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdCIsInJvdXRlIjoiYmVyYW5kYSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1790047487),('DCUNde4PheIGgpRQIeEefRsXdVAYWCz7vNl5Wwon',NULL,'127.0.0.1','Symfony','eyJfdG9rZW4iOiJpTmxoeWtEWjFtWldXdjBGTld0Z0V5VEo2V1RmeUx0U09PYXZaVDZhIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdCIsInJvdXRlIjoiYmVyYW5kYSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1790049050),('n2GaA81QM4KdHodj5hliPCgwKb7fOqM7W9HyITru',NULL,'127.0.0.1','Symfony','eyJfdG9rZW4iOiJ3SmM5WUNlZHlwNHRlVXFPRW9Oa05PT0E3VUVUTkphRENOWWpBdE1sIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdCIsInJvdXRlIjoiYmVyYW5kYSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1790048650),('RKVo050S77kdlQm7M5qyvFvUgKbZsXlDMisaxDM0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJNZmV1OFo3OXlvQjRPYXRkb0JSQzVLa3lBdnFPNHJUamM4T0w0eGROIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImJlcmFuZGEifX0=',1790050745);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_17_000001_create_kategoris_table',1),(5,'2026_09_17_000002_create_barangs_table',1),(6,'2026_09_17_000003_create_transaksis_table',1),(7,'2026_09_17_000004_create_barang_user_table',1),(8,'2026_09_17_000005_create_legal_documents_table',1),(9,'2026_09_17_000006_create_activity_logs_table',1),(10,'2026_09_17_000007_create_company_settings_table',1),(11,'2026_09_17_090633_add_slop_pricing_to_barangs_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategoris`
--

DROP TABLE IF EXISTS `kategoris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategoris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kategoris_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategoris`
--

LOCK TABLES `kategoris` WRITE;
/*!40000 ALTER TABLE `kategoris` DISABLE KEYS */;
INSERT INTO `kategoris` VALUES (1,'Sigaret Kretek Mesin (SKM)','skm','Rokok kretek yang diproduksi menggunakan mesin modern berstandar tinggi dengan tarikan halus dan filter presisi.','2026-09-17 01:58:20','2026-09-17 01:58:20'),(2,'Sigaret Kretek Tangan (SKT)','skt','Kretek linting tangan tradisional dengan racikan tembakau asli dan cengkeh pilihan warisan nusantara.','2026-09-17 01:58:20','2026-09-17 01:58:20');
/*!40000 ALTER TABLE `kategoris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barangs`
--

DROP TABLE IF EXISTS `barangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barangs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_id` bigint unsigned NOT NULL,
  `kode_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `profil_rasa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tar_mg` decimal(5,1) DEFAULT NULL,
  `nikotin_mg` decimal(5,1) DEFAULT NULL,
  `batang_per_bungkus` int NOT NULL DEFAULT '12',
  `bungkus_per_slop` int NOT NULL DEFAULT '10',
  `slop_per_bal` int NOT NULL DEFAULT '20',
  `harga_per_bal` decimal(14,2) NOT NULL DEFAULT '0.00',
  `harga_per_slop` decimal(14,2) DEFAULT NULL,
  `min_order_bal` int NOT NULL DEFAULT '5',
  `min_order_slop` int NOT NULL DEFAULT '1',
  `stok` int NOT NULL DEFAULT '100',
  `status_stok` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ready Stock',
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barangs_kode_barang_unique` (`kode_barang`),
  UNIQUE KEY `barangs_slug_unique` (`slug`),
  KEY `barangs_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `barangs_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barangs`
--

LOCK TABLES `barangs` WRITE;
/*!40000 ALTER TABLE `barangs` DISABLE KEYS */;
INSERT INTO `barangs` VALUES (7,2,'SKT-DH12','DWIPANTARA Hitam 12','dwipantara-hitam-12','Sigaret Kretek Tangan Dwipantara varian Hitam dengan racikan tembakau pilihan berkarakter mantap, pembakaran lambat, dan aroma rempah khas nusantara.','Kuat, mantap, gurih rempah pilihan',31.0,2.0,12,10,20,980000.00,49000.00,1,1,200,'Ready Stock','assets/img/hero-pabrik.jpg',1,1,'2026-09-21 21:31:39','2026-09-21 21:31:39'),(8,2,'SKT-DK12','DWIPANTARA Kuning 12','dwipantara-kuning-12','Sigaret Kretek Tangan Dwipantara varian Kuning dengan paduan tembakau Jawa dan cengkeh harum, menghadirkan tarikan yang halus dan seimbang.','Seimbang, wangi manis cengkeh, tarikan halus',29.0,1.9,12,10,20,1050000.00,52500.00,1,1,180,'Ready Stock','assets/img/tembakau-cengkeh.jpg',1,2,'2026-09-21 21:31:39','2026-09-21 21:31:39'),(9,2,'SKT-DHJ12','DWIPANTARA Hijau 12','dwipantara-hijau-12','Sigaret Kretek Tangan Dwipantara varian Hijau dengan sentuhan saus racikan segar dan tembakau alami, cocok untuk penggemar kretek beraroma bersih.','Segar, aroma herbal alami, tarikan enteng',28.0,1.8,12,10,20,1050000.00,52500.00,1,1,150,'Ready Stock','assets/img/gudang-distribusi.jpg',1,3,'2026-09-21 21:31:39','2026-09-21 21:31:39'),(10,2,'SKT-SM12','SEMBADA 12','sembada-12','Sigaret Kretek Tangan Sembada 12 batang dengan racikan tembakau tradisi luhur, cita rasa klasik untuk pecinta kretek sejati.','Klasik, berbobot, aroma tembakau matang',32.0,2.1,12,10,20,950000.00,47500.00,1,1,220,'Ready Stock','assets/img/produksi-skt.jpg',1,4,'2026-09-21 21:31:39','2026-09-21 21:31:39'),(11,2,'SKT-KK12','KERETA KENCANA 12','kereta-kencana-12','Sigaret Kretek Tangan Kereta Kencana 12, kretek unggulan mahakarya lintingan tangan terlatih dari pabrik Ponggok Blitar dengan tembakau dan cengkeh pilihan.','Full flavour, kaya rempah, pembakaran nikmat',30.0,2.0,12,10,20,1150000.00,57500.00,1,1,239,'Ready Stock','assets/img/hero-pabrik.jpg',1,5,'2026-09-21 21:31:39','2026-09-21 21:47:26');
/*!40000 ALTER TABLE `barangs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barang_user`
--

DROP TABLE IF EXISTS `barang_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `barang_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barang_user_user_id_barang_id_unique` (`user_id`,`barang_id`),
  KEY `barang_user_barang_id_foreign` (`barang_id`),
  CONSTRAINT `barang_user_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `barang_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang_user`
--

LOCK TABLES `barang_user` WRITE;
/*!40000 ALTER TABLE `barang_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `barang_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksis`
--

DROP TABLE IF EXISTS `transaksis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `barang_id` bigint unsigned NOT NULL,
  `nama_mitra` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bal',
  `total_harga` decimal(14,2) NOT NULL DEFAULT '0.00',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Baru Masuk','Diproses','Selesai','Dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baru Masuk',
  `nomor_do` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_resi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sumber` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Web B2B',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksis_kode_transaksi_unique` (`kode_transaksi`),
  KEY `transaksis_user_id_foreign` (`user_id`),
  KEY `transaksis_barang_id_foreign` (`barang_id`),
  CONSTRAINT `transaksis_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksis`
--

LOCK TABLES `transaksis` WRITE;
/*!40000 ALTER TABLE `transaksis` DISABLE KEYS */;
INSERT INTO `transaksis` VALUES (1,'TRX-202609-001',3,7,'Toko Berkah Grosir','081234567891','Jl. Basuki Rahmat No. 12, Malang, Jawa Timur',15,'Bal',14700000.00,'Kirim via kargo langganan Blitar-Malang armada cold diesel','Diproses','DO-KK-2026-0901','CARGO-MLG-8823','Web B2B WhatsApp','2026-09-21 21:47:02','2026-09-21 21:47:02'),(2,'TRX-202609-002',3,11,'UD. Tembakau Sentosa','082198765432','Pasar Legi Blok A-4, Kota Blitar',20,'Bal',23000000.00,'Ambil langsung mandiri di pos gerbang gudang pabrik Ponggok','Selesai','DO-KK-2026-0894','PICKUP-PABRIK-042','Web B2B Form','2026-09-21 21:47:02','2026-09-21 21:47:02'),(3,'TRX-202609-003',3,8,'Agen Rokok Jaya Abadi','085712349999','Kec. Wlingi, Kabupaten Blitar',10,'Bal',10500000.00,'Mohon sertakan faktur fisik bercap basah perusahaan','Baru Masuk',NULL,NULL,'Web B2B WhatsApp','2026-09-21 21:47:02','2026-09-21 21:47:02'),(4,'TRX-202609-004',3,10,'Grosir Berkah Mandiri','081333445566','Jl. Dhoho No. 45, Kota Kediri',8,'Bal',7600000.00,'Pesanan rutin distributor Kediri Raya','Diproses','DO-KK-2026-0905','EXP-KDR-1092','Web B2B Form','2026-09-21 21:47:02','2026-09-21 21:47:02'),(5,'TRX-202609-005',3,9,'Mitra Tembakau Sejahtera','085299887711','Pasar Wage Blok B-12, Tulungagung',12,'Bal',12600000.00,'Pengiriman armada pabrik langsung ke gudang Tulungagung','Selesai','DO-KK-2026-0889','TRUCK-KK-08','Web B2B WhatsApp','2026-09-21 21:47:02','2026-09-21 21:47:02'),(6,'TRX-202609-006',3,11,'Warung Kopi Mbak Sri','081398765432','Jl. Pasar Pon No. 5, Blitar',5,'Slop',287500.00,'Pembelian eceran slop untuk warung','Selesai','DO-KK-2026-0910','LOKAL-BLT-055','Web B2B Form','2026-09-21 21:47:02','2026-09-21 21:47:02'),(7,'TRX-202609-007',3,7,'Toko Sumber Rejeki','087811223344','Kec. Srengat, Kabupaten Blitar',3,'Slop',147000.00,'Uji pasar varian Dwipantara Hitam di toko','Diproses','DO-KK-2026-0912','LOKAL-BLT-059','Web B2B WhatsApp','2026-09-21 21:47:02','2026-09-21 21:47:02'),(8,'TRX-202609-008',3,10,'Koperasi Unit Desa Ponggok','081277665544','Dusun Kebonduren, Ponggok, Blitar',2,'Bal',1900000.00,'Pasokan etalase koperasi desa','Baru Masuk',NULL,NULL,'Web B2B Form','2026-09-21 21:47:02','2026-09-21 21:47:02'),(9,'TRX-20260922-DPIU',NULL,11,'UD. Mitra Test Laravel','081234567890','Jl. Pengujian No. 10, Blitar',10,'Bal',11500000.00,'Test order otomatis','Baru Masuk',NULL,NULL,'Web B2B Form','2026-09-21 21:47:26','2026-09-21 21:47:26'),(10,'TRX-20260922-QBWZ',NULL,11,'Warung Kopi Mbak Sri','081398765432','Jl. Pasar Pon No. 5, Blitar',1,'Slop',57500.00,'Pesan minimal 1 slop untuk warung','Baru Masuk',NULL,NULL,'Web B2B Form','2026-09-21 21:47:26','2026-09-21 21:47:26');
/*!40000 ALTER TABLE `transaksis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legal_documents`
--

DROP TABLE IF EXISTS `legal_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `legal_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_dokumen` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penerbit` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berlaku_sampai` date DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legal_documents`
--

LOCK TABLES `legal_documents` WRITE;
/*!40000 ALTER TABLE `legal_documents` DISABLE KEYS */;
INSERT INTO `legal_documents` VALUES (1,'NPPBKC (Nomor Pokok Pengusaha Barang Kena Cukai)','0821.1.2.XXXXX','KPPBC Tipe Madya Pabean C Blitar','2028-12-31','Aktif','Izin pita cukai resmi dari Direktorat Jenderal Bea dan Cukai untuk produksi tembakau.',1,'2026-09-17 01:58:20','2026-09-17 01:58:20'),(2,'NIB (Nomor Induk Berusaha)','9120001234567','Kementerian Investasi / BKPM RI','2030-01-01','Aktif','Identitas berusaha terintegrasi OSS RBA sektor industri pengolahan tembakau.',2,'2026-09-17 01:58:20','2026-09-17 01:58:20'),(3,'Izin Usaha Industri (IUI)','503/IUI/TMBK/2021','DPMPTSP Kabupaten Blitar','2029-06-30','Aktif','Izin operasional pabrik sigaret kretek di kawasan industri Ponggok, Kabupaten Blitar.',3,'2026-09-17 01:58:20','2026-09-17 01:58:20'),(4,'Sertifikat Merek Dagang Kereta Kencana','IDM000987654','DJKI Kementerian Hukum dan HAM RI','2032-10-15','Aktif','Perlindungan hak kekayaan intelektual merek dagang kelas barang 34.',4,'2026-09-17 01:58:20','2026-09-17 01:58:20');
/*!40000 ALTER TABLE `legal_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260917-2B7O oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-17 01:58:34','2026-09-17 01:58:34'),(2,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260917-QIPK oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-17 01:59:43','2026-09-17 01:59:43'),(3,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260917-JTFK oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-17 02:15:08','2026-09-17 02:15:08'),(4,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260917-3MYJ oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-17 02:15:08','2026-09-17 02:15:08'),(5,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260917-PNM5 oleh aldienta test 1 (1 Slop)','127.0.0.1','2026-09-17 02:21:21','2026-09-17 02:21:21'),(6,3,'Staf Operasional Pabrik','staff','Login Sistem','Pengguna berhasil masuk ke dashboard','127.0.0.1','2026-09-17 02:22:01','2026-09-17 02:22:01'),(7,3,'Staf Operasional Pabrik','staff','Update Transaksi','Memperbarui status transaksi TRX-20260917-PNM5 menjadi Diproses','127.0.0.1','2026-09-17 02:22:50','2026-09-17 02:22:50'),(8,3,'Staf Operasional Pabrik','staff','Logout Sistem','Pengguna keluar dari sistem','127.0.0.1','2026-09-17 02:32:37','2026-09-17 02:32:37'),(9,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260917-NB2C oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-17 03:00:03','2026-09-17 03:00:03'),(10,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260917-40JO oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-17 03:00:03','2026-09-17 03:00:03'),(11,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260921-VXCN oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-20 22:01:13','2026-09-20 22:01:13'),(12,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260921-FPFV oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-20 22:01:13','2026-09-20 22:01:13'),(13,1,'Bapak Komari Yaman (Owner)','owner','Login Sistem','Pengguna berhasil masuk ke dashboard','::1','2026-09-20 22:02:59','2026-09-20 22:02:59'),(14,1,'Bapak Komari Yaman (Owner)','owner','Logout Sistem','Pengguna keluar dari sistem','::1','2026-09-20 22:03:41','2026-09-20 22:03:41'),(15,1,'Bapak Komari Yaman (Owner)','owner','Login Sistem','Pengguna berhasil masuk ke dashboard','127.0.0.1','2026-09-21 19:58:59','2026-09-21 19:58:59'),(16,1,'Bapak Komari Yaman (Owner)','owner','Logout Sistem','Pengguna keluar dari sistem','127.0.0.1','2026-09-21 20:00:34','2026-09-21 20:00:34'),(17,3,'Staf Operasional Pabrik','staff','Login Sistem','Pengguna berhasil masuk ke dashboard','127.0.0.1','2026-09-21 20:00:39','2026-09-21 20:00:39'),(18,3,'Staf Operasional Pabrik','staff','Login Sistem','Pengguna berhasil masuk ke dashboard','127.0.0.1','2026-09-21 20:00:46','2026-09-21 20:00:46'),(19,3,'Staf Operasional Pabrik','staff','Update Transaksi','Memperbarui status transaksi TRX-20260921-FPFV menjadi Selesai','127.0.0.1','2026-09-21 20:01:17','2026-09-21 20:01:17'),(20,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-FNKN oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-21 20:16:10','2026-09-21 20:16:10'),(21,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-U3I2 oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-21 20:16:10','2026-09-21 20:16:10'),(22,3,'Staf Operasional Pabrik','staff','Logout Sistem','Pengguna keluar dari sistem','127.0.0.1','2026-09-21 20:16:54','2026-09-21 20:16:54'),(23,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-ANJ2 oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-21 20:23:48','2026-09-21 20:23:48'),(24,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-A0KT oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-21 20:23:48','2026-09-21 20:23:48'),(25,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-PZ7F oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-21 20:44:00','2026-09-21 20:44:00'),(26,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-XDYV oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-21 20:44:00','2026-09-21 20:44:00'),(27,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-QYE8 oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-21 20:50:36','2026-09-21 20:50:36'),(28,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-G3AV oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-21 20:50:36','2026-09-21 20:50:36'),(29,1,'Bapak Komari Yaman (Owner)','owner','Login Sistem','Pengguna berhasil masuk ke dashboard','127.0.0.1','2026-09-21 21:00:02','2026-09-21 21:00:02'),(30,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-WDGY oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-21 21:10:02','2026-09-21 21:10:02'),(31,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-PKW7 oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-21 21:10:02','2026-09-21 21:10:02'),(32,1,'Bapak Komari Yaman (Owner)','owner','Logout Sistem','Pengguna keluar dari sistem','127.0.0.1','2026-09-21 21:18:47','2026-09-21 21:18:47'),(33,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-DPIU oleh UD. Mitra Test Laravel (10 Bal)','127.0.0.1','2026-09-21 21:47:26','2026-09-21 21:47:26'),(34,NULL,'Tamu / Sistem','guest','Pesanan Masuk','Pesanan baru TRX-20260922-QBWZ oleh Warung Kopi Mbak Sri (1 Slop)','127.0.0.1','2026-09-21 21:47:26','2026-09-21 21:47:26');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_settings`
--

DROP TABLE IF EXISTS `company_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_settings_setting_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_settings`
--

LOCK TABLES `company_settings` WRITE;
/*!40000 ALTER TABLE `company_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-22 11:47:57

