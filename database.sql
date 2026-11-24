-- ================================================================
-- PR. KERETA KENCANA - Hostinger Production Database (prkeretakencana.com)
-- Dibuat pada: 2026-11-24 04:03:22 WIB
-- Catatan: Tanpa CREATE DATABASE / USE agar siap langsung import di phpMyAdmin Hostinger.
-- Satuan stok barangs sudah dikonversi presisi ke SLOP (Opsi B).
-- ================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------
-- Struktur tabel `activity_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2919 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `barang_user`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `barang_user`;
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

-- --------------------------------------------------------
-- Struktur tabel `barangs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `barangs`;
CREATE TABLE `barangs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_id` bigint unsigned NOT NULL,
  `kode_barang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `profil_rasa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `status_stok` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ready Stock',
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `best_seller` tinyint(1) NOT NULL DEFAULT '0',
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barangs_kode_barang_unique` (`kode_barang`),
  UNIQUE KEY `barangs_slug_unique` (`slug`),
  KEY `barangs_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `barangs_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `barangs` (`id`, `kategori_id`, `kode_barang`, `nama`, `slug`, `deskripsi`, `profil_rasa`, `tar_mg`, `nikotin_mg`, `batang_per_bungkus`, `bungkus_per_slop`, `slop_per_bal`, `harga_per_bal`, `harga_per_slop`, `min_order_bal`, `min_order_slop`, `stok`, `status_stok`, `gambar`, `aktif`, `best_seller`, `urutan`, `created_at`, `updated_at`) VALUES
('10', '2', 'SKT-SM12', 'SEMBADA 12', 'sembada-12', 'Sigaret Kretek Tangan Sembada 12 batang dengan racikan tembakau tradisi luhur Blitar, berkarakter mantap, dan aroma gurih klasik untuk pecinta kretek sejati.', 'Klasik, berbobot, aroma tembakau matang dan rempah pilihan', NULL, NULL, '12', '10', '20', '1200000.00', '70000.00', '1', '1', '5020', 'Ready Stock', 'images/Sembada Putih 12.jpg', '1', '1', '1', '2026-09-22 04:31:39', '2026-11-18 06:58:04'),
('11', '2', 'SKT-KK12', 'KERETA KENCANA 12', 'kereta-kencana-12', 'Sigaret Kretek Tangan Kereta Kencana 12, kretek unggulan mahakarya lintingan tangan terlatih dari pabrik Ponggok Blitar dalam balutan kemasan merah emas yang elegan.', 'Full flavour, kaya rempah, pembakaran halus dan nikmat', NULL, NULL, '12', '10', '20', '1200000.00', '70000.00', '1', '1', '5000', 'Ready Stock', 'images/Kereta Kencana Merah 12.jpg', '1', '0', '2', '2026-09-22 04:31:39', '2026-11-17 11:01:37'),
('12', '2', 'SKT-SC12', 'SEMBADA Cethe 12', 'sembada-cethe-12', 'Sigaret Kretek Tangan Sembada Cethe 12 batang dengan racikan istimewa khas tradisi nyethe / cangkrukan Jawa Timur, beraroma harum dan tarikan mantap.', 'Khas aroma cethe kopi, manis gurih seimbang, tarikan mantap', NULL, NULL, '12', '10', '20', '1200000.00', '70000.00', '1', '1', '4960', 'Ready Stock', 'images/Sembada Cethe 12.jpg', '1', '0', '3', '2026-09-29 00:58:38', '2026-11-18 06:54:26');

-- --------------------------------------------------------
-- Struktur tabel `cache`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `cache_locks`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `company_settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `company_settings`;
CREATE TABLE `company_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_settings_setting_key_unique` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `company_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
('51', 'media_file_hero_2', 'pabrik-media/3gMzoLrjoxkzK7bRzsnYN6he8iFL8IpNLUS8IEnn.jpg', '2026-11-24 08:54:08', '2026-11-24 08:58:20'),
('52', 'media_updated_at_hero_2', '2026-11-24', '2026-11-24 08:54:08', '2026-11-24 08:54:08'),
('53', 'media_caption_hero_2', 'Racikan Tembakau & Cengkeh Pilihan', '2026-11-24 08:54:08', '2026-11-24 08:54:08'),
('54', 'media_file_hero_3', 'pabrik-media/2OW8wzbUZJg2Wi0CjdEziAwAFKPfUrPR9Pe8vOuh.jpg', '2026-11-24 08:54:32', '2026-11-24 08:54:32'),
('55', 'media_caption_hero_3', 'Kualitas tembakau', '2026-11-24 08:54:32', '2026-11-24 08:55:01'),
('56', 'media_updated_at_hero_3', '2026-11-24', '2026-11-24 08:54:32', '2026-11-24 08:54:32'),
('57', 'hero_extra_keys', '[\"hero_3\"]', '2026-11-24 08:54:32', '2026-11-24 08:54:32'),
('58', 'media_file_hero_1', 'pabrik-media/vpTGJ7u6LhD60qUrnXrTBGql9XDcirGt71bcrlaA.jpg', '2026-11-24 08:57:33', '2026-11-24 08:57:33'),
('59', 'media_updated_at_hero_1', '2026-11-24', '2026-11-24 08:57:33', '2026-11-24 08:57:33'),
('60', 'media_caption_hero_1', 'Tembakau fresh', '2026-11-24 08:57:33', '2026-11-24 08:57:33'),
('124', 'media_file_login-banner', 'pabrik-media/0C8eRVwFlkqeGDBd5TC1E2fpiW1vO5Ytr5nLqa90.jpg', '2026-11-24 09:21:34', '2026-11-24 09:21:34'),
('125', 'media_updated_at_login-banner', '2026-11-24', '2026-11-24 09:21:35', '2026-11-24 09:21:35'),
('126', 'media_caption_login-banner', 'Portal Resmi Distribusi Sigaret Kretek Tangan • Ponggok, Kab. Blitar', '2026-11-24 09:21:35', '2026-11-24 09:21:35'),
('142', 'media_updated_at_pabrik-ponggok', '2026-11-20', '2026-11-24 09:33:39', '2026-11-24 09:33:39'),
('143', 'media_caption_pabrik-ponggok', 'Pabrik PR. KERETA KENCANA • Ponggok, Kab. Blitar', '2026-11-24 09:33:39', '2026-11-24 09:33:39'),
('144', 'media_file_proses-bahan-baku', 'pabrik-media/JsK3Qcm5ROfoyZVJMLPrQPX1wev79KCde6vkXvJT.jpg', '2026-11-24 09:34:02', '2026-11-24 09:34:02'),
('145', 'media_updated_at_proses-bahan-baku', '2026-11-20', '2026-11-24 09:34:02', '2026-11-24 09:34:02'),
('146', 'media_caption_proses-bahan-baku', 'Gudang Bahan Baku Tembakau & Cengkeh', '2026-11-24 09:34:02', '2026-11-24 09:34:02'),
('147', 'media_file_proses-gudang-distribusi', 'pabrik-media/uLzsj7jgleQwDv75PD2fbV9xmnHJXe78yZQrps0N.jpg', '2026-11-24 09:48:14', '2026-11-24 09:48:14'),
('148', 'media_updated_at_proses-gudang-distribusi', '2026-11-20', '2026-11-24 09:48:14', '2026-11-24 09:48:14'),
('149', 'media_caption_proses-gudang-distribusi', 'Kemasan Produk Resmi Siap Distribusi', '2026-11-24 09:48:14', '2026-11-24 09:48:14'),
('165', 'media_file_fasilitas-tembakau', 'pabrik-media/TQEIQ8OACEbLMAcM2kSFzcC364EvuACwqaez4YoZ.jpg', '2026-11-24 10:03:14', '2026-11-24 10:03:14'),
('166', 'media_updated_at_fasilitas-tembakau', '2026-11-20', '2026-11-24 10:03:14', '2026-11-24 10:03:14'),
('167', 'media_caption_fasilitas-tembakau', 'Gudang Penyimpanan Tembakau & Cengkeh', '2026-11-24 10:03:14', '2026-11-24 10:03:14'),
('168', 'media_file_pabrik-ponggok', 'pabrik-media/flXwwtrc3l16r9YdwIGu9DjjuVlHWUnHXFGbD0Ks.jpg', '2026-11-24 10:03:39', '2026-11-24 10:03:39'),
('169', 'media_file_fasilitas-gudang', 'pabrik-media/w5jxsutlOZLnfwYCCKO0CZiQkweHqKaQzMITBso5.jpg', '2026-11-24 10:04:09', '2026-11-24 10:04:09'),
('170', 'media_updated_at_fasilitas-gudang', '2026-11-20', '2026-11-24 10:04:09', '2026-11-24 10:04:09'),
('171', 'media_caption_fasilitas-gudang', 'Kesiapan Stok Produk & Distribusi Mitra', '2026-11-24 10:04:09', '2026-11-24 10:04:09'),
('172', 'media_file_pabrik-beranda', 'pabrik-media/DpZraEjPRKTjrngZpVud497DCosAwr4jMEGX8ftm.jpg', '2026-11-24 10:04:19', '2026-11-24 10:04:19'),
('173', 'media_updated_at_pabrik-beranda', '2026-11-20', '2026-11-24 10:04:19', '2026-11-24 10:04:19'),
('174', 'media_caption_pabrik-beranda', 'Fasilitas Pabrik PR. KERETA KENCANA • Ponggok, Blitar', '2026-11-24 10:04:19', '2026-11-24 10:04:19');

-- --------------------------------------------------------
-- Struktur tabel `failed_jobs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `job_batches`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `jobs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `kategoris`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kategoris`;
CREATE TABLE `kategoris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kategoris_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kategoris` (`id`, `nama_kategori`, `slug`, `deskripsi`, `created_at`, `updated_at`) VALUES
('2', 'Sigaret Kretek Tangan (SKT)', 'skt', 'Kretek linting tangan tradisional dengan racikan tembakau asli dan cengkeh pilihan warisan nusantara.', '2026-09-17 08:58:20', '2026-09-17 08:58:20');

-- --------------------------------------------------------
-- Struktur tabel `legal_documents`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `legal_documents`;
CREATE TABLE `legal_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_dokumen` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penerbit` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berlaku_sampai` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `legal_documents` (`id`, `nama_dokumen`, `nomor`, `penerbit`, `berlaku_sampai`, `status`, `catatan`, `file_dokumen`, `urutan`, `created_at`, `updated_at`) VALUES
('23', 'NPPBKC (Nomor Pokok Pengusaha Barang Kena Cukai)', '750******-070700-221221*******', 'KPPBC Tipe Madya Pabean C Blitar (Ditjen Bea dan Cukai)', NULL, 'Aktif', 'Pengusaha Pabrik Hasil Tembakau (Ditetapkan 16 Agustus 2022). Sebagian digit disensor untuk mencegah penyalahgunaan/pemalsuan.', NULL, '1', '2026-11-16 12:33:06', '2026-11-16 12:33:06');

-- --------------------------------------------------------
-- Struktur tabel `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
('1', '0001_01_01_000000_create_users_table', '1'),
('2', '0001_01_01_000001_create_cache_table', '1'),
('3', '0001_01_01_000002_create_jobs_table', '1'),
('4', '2026_09_17_000001_create_kategoris_table', '1'),
('5', '2026_09_17_000002_create_barangs_table', '1'),
('6', '2026_09_17_000003_create_transaksis_table', '1'),
('7', '2026_09_17_000004_create_barang_user_table', '1'),
('8', '2026_09_17_000005_create_legal_documents_table', '1'),
('9', '2026_09_17_000006_create_activity_logs_table', '1'),
('10', '2026_09_17_000007_create_company_settings_table', '1'),
('11', '2026_09_17_090633_add_slop_pricing_to_barangs_table', '2'),
('12', '2026_09_27_125846_add_customer_fields_to_users_and_transaksis_table', '3'),
('13', '2026_09_27_133138_create_transaksi_details_table', '4'),
('14', '2026_10_10_143753_create_personal_access_tokens_table', '5'),
('15', '2026_10_14_183500_add_foto_profil_to_users_table', '6'),
('16', '2026_10_14_132127_add_metode_pembayaran_to_transaksis_table', '7'),
('17', '2026_11_05_095503_add_bukti_transfer_to_transaksis_table', '8'),
('18', '2026_11_16_000001_add_file_dokumen_to_legal_documents_table', '9'),
('19', '2026_11_16_000002_update_uniform_slop_prices_on_barangs_table', '10'),
('20', '2026_11_16_000003_update_verified_nppbkc_in_legal_documents_table', '11'),
('21', '2026_11_20_000001_add_best_seller_to_barangs_table', '12'),
('22', '2026_11_23_000001_convert_barangs_stok_to_slop', '13'),
('23', '2026_11_24_000001_convert_staff_role_to_admin', '14');

-- --------------------------------------------------------
-- Struktur tabel `orders_wa_log`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders_wa_log`;
CREATE TABLE `orders_wa_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_mitra` varchar(150) NOT NULL,
  `telepon` varchar(30) NOT NULL,
  `alamat` text NOT NULL,
  `varian_produk` varchar(150) NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `satuan` varchar(20) NOT NULL DEFAULT 'Bal',
  `total_harga` decimal(14,2) NOT NULL DEFAULT '0.00',
  `catatan` text,
  `status` enum('Baru Masuk','Diproses','Selesai','Dibatalkan') NOT NULL DEFAULT 'Baru Masuk',
  `sumber` varchar(30) NOT NULL DEFAULT 'Web WhatsApp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Struktur tabel `password_reset_tokens`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `personal_access_tokens`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=839 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `products`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori` enum('SKM','SKT') NOT NULL,
  `deskripsi` text,
  `profil_rasa` text,
  `tar_mg` decimal(5,1) DEFAULT NULL,
  `nikotin_mg` decimal(5,1) DEFAULT NULL,
  `batang_per_bungkus` int NOT NULL DEFAULT '12',
  `bungkus_per_slop` int NOT NULL DEFAULT '10',
  `slop_per_bal` int NOT NULL DEFAULT '20',
  `harga_per_bal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `min_order_bal` int NOT NULL DEFAULT '5',
  `status_stok` varchar(50) NOT NULL DEFAULT 'Ready Stock',
  `image_url` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Struktur tabel `sessions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `transaksi_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transaksi_details`;
CREATE TABLE `transaksi_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `barang_id` bigint unsigned NOT NULL,
  `satuan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Slop',
  `jumlah` int NOT NULL DEFAULT '1',
  `harga_satuan` decimal(14,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(14,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_details_transaksi_id_foreign` (`transaksi_id`),
  KEY `transaksi_details_barang_id_foreign` (`barang_id`),
  CONSTRAINT `transaksi_details_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_details_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=963 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaksi_details` (`id`, `transaksi_id`, `barang_id`, `satuan`, `jumlah`, `harga_satuan`, `subtotal`, `created_at`, `updated_at`) VALUES
('543', '657', '10', 'Slop', '1', '70000.00', '70000.00', '2026-11-17 03:16:11', '2026-11-17 03:16:11'),
('560', '676', '12', 'Slop', '1', '70000.00', '70000.00', '2026-11-17 03:50:05', '2026-11-17 03:50:05'),
('620', '736', '12', 'Slop', '2', '70000.00', '140000.00', '2026-11-18 06:48:23', '2026-11-18 06:48:23'),
('621', '737', '12', 'Slop', '1', '70000.00', '70000.00', '2026-11-18 06:54:26', '2026-11-18 06:54:26');

-- --------------------------------------------------------
-- Struktur tabel `transaksis`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transaksis`;
CREATE TABLE `transaksis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `barang_id` bigint unsigned DEFAULT NULL,
  `nama_mitra` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `satuan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bal',
  `total_harga` decimal(14,2) NOT NULL DEFAULT '0.00',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COD',
  `status_pembayaran` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Dibayar',
  `bukti_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ditransfer_pada` timestamp NULL DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baru Masuk',
  `bukti_penerimaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diterima_pada` timestamp NULL DEFAULT NULL,
  `catatan_penerima` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nomor_do` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_resi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sumber` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Web B2B',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksis_kode_transaksi_unique` (`kode_transaksi`),
  KEY `transaksis_user_id_foreign` (`user_id`),
  KEY `transaksis_barang_id_foreign` (`barang_id`),
  CONSTRAINT `transaksis_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaksis` (`id`, `kode_transaksi`, `user_id`, `barang_id`, `nama_mitra`, `telepon`, `alamat`, `jumlah`, `satuan`, `total_harga`, `catatan`, `metode_pembayaran`, `status_pembayaran`, `bukti_transfer`, `ditransfer_pada`, `status`, `bukti_penerimaan`, `diterima_pada`, `catatan_penerima`, `nomor_do`, `nomor_resi`, `sumber`, `created_at`, `updated_at`) VALUES
('657', 'TRX-20261117-1HRO', '944', '10', 'Toko Hebat (Ade Aldienta)', '+6285257907797', 'Jl,anggrek', '1', 'Slop', '70000.00', NULL, 'Transfer Bank', 'Belum Dibayar', NULL, NULL, 'Baru Masuk', NULL, NULL, NULL, NULL, NULL, 'Mobile App', '2026-11-17 03:16:11', '2026-11-18 07:32:48'),
('676', 'TRX-20261117-GMKX', '944', '12', 'Toko Hebat (Ade Aldienta)', '+6285257907797', 'Jl,anggrek', '1', 'Slop', '70000.00', NULL, 'Transfer Bank', 'Ditolak / Belum Valid', 'bukti_transfer/bukti_transfer_TRX-20261117-GMKX_1794986190.jpg', '2026-11-18 07:16:30', 'Dikirim', NULL, NULL, NULL, NULL, NULL, 'Mobile App', '2026-11-17 03:50:05', '2026-11-18 07:17:16'),
('736', 'TRX-20261118-IEQC', '944', '12', 'Toko Hebat (Ade Aldienta)', '+6285257907797', 'Jl,anggrek', '2', 'Slop', '140000.00', NULL, 'Transfer Bank', 'Lunas', 'bukti_transfer/bukti_transfer_TRX-20261118-IEQC_1794986399.jpg', '2026-11-18 07:19:59', 'Selesai', 'bukti_penerimaan/4oWc983tYeHtYyoqFwoF7t3rLweLdsAFPGqzBc86.jpg', '2026-11-18 07:20:58', 'sampai pak/bu', NULL, NULL, 'Mobile App', '2026-11-18 06:48:23', '2026-11-18 07:20:58'),
('737', 'TRX-20261118-ZHUL', '944', '12', 'Toko Hebat (Ade Aldienta)', '+6285257907797', 'Jl,anggrek', '1', 'Slop', '70000.00', NULL, 'Bayar di Tempat (COD)', 'Belum Dibayar', NULL, NULL, 'Diproses', NULL, NULL, NULL, NULL, NULL, 'Mobile App', '2026-11-18 06:54:26', '2026-11-18 07:42:35');

-- --------------------------------------------------------
-- Struktur tabel `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_toko` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `foto_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2390 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `nama_toko`, `email`, `telepon`, `alamat`, `foto_profil`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
('1', 'Bapak Komari Yaman (Owner)', NULL, 'owner@keretakencana.com', NULL, NULL, NULL, NULL, '$2y$12$3wpQqcKO2jNxwTvR6daa7u/59vgEnpPWCnV7y4aCTSg24dEi37QHe', 'owner', NULL, '2026-09-17 08:58:20', '2026-11-17 10:58:55'),
('2', 'Super Admin (Web Dev)', NULL, 'superadmin@keretakencana.com', NULL, NULL, NULL, NULL, '$2y$12$57hwHpxMkEk3OSdI4.HG/e/pjARnr.ZwwQd4Uih3qsJy5gBTfXB5y', 'superadmin', NULL, '2026-09-17 08:58:20', '2026-11-17 10:58:55'),
('4', 'Admin Gudang & Pemesanan', NULL, 'admin@keretakencana.com', NULL, NULL, NULL, NULL, '$2y$12$CFUtgS/xSv6J/7MWnXBo.e.KkLpN5xhYxsQ25OdpzmjeBaAVmjYhq', 'admin', NULL, '2026-09-17 08:58:20', '2026-10-01 12:30:36'),
('944', 'Ade Aldienta', 'Toko Hebat', 'tokohebat777@gmail.com', '+6285257907797', 'Jl,anggrek', NULL, NULL, '$2y$12$ut5rhG9Y.UVmsJMeCgZHJuYu/ZF1FO6z/gtH7NwBEb1QZb301HK0y', 'pelanggan', NULL, '2026-11-14 11:11:18', '2026-11-17 03:01:36');

SET FOREIGN_KEY_CHECKS=1;
