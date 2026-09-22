-- =======================================================
-- SKEMA DATABASE KHUSUS VERSI PHP NATIVE: PR. KERETA KENCANA
-- Gunakan file ini jika ingin mengimpor database untuk proyek PHP Native lama
-- =======================================================

CREATE DATABASE IF NOT EXISTS `db_kereta_kencana_native` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_kereta_kencana_native`;

-- 1. TABEL PENGGUNA & HAK AKSES (Owner & Admin)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('owner', 'admin') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABEL KATALOG PRODUK (SKM & SKT)
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `nama` VARCHAR(150) NOT NULL,
  `kategori` ENUM('SKM', 'SKT') NOT NULL,
  `deskripsi` TEXT NULL,
  `profil_rasa` TEXT NULL,
  `tar_mg` DECIMAL(5,1) NULL,
  `nikotin_mg` DECIMAL(5,1) NULL,
  `batang_per_bungkus` INT NOT NULL DEFAULT 12,
  `bungkus_per_slop` INT NOT NULL DEFAULT 10,
  `slop_per_bal` INT NOT NULL DEFAULT 20,
  `harga_per_bal` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `min_order_bal` INT NOT NULL DEFAULT 5,
  `status_stok` VARCHAR(50) NOT NULL DEFAULT 'Ready Stock',
  `image_url` VARCHAR(255) NULL,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `urutan` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABEL LEGALITAS & IZIN PABRIK (Lencana Kepatuhan)
CREATE TABLE IF NOT EXISTS `legal_documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_dokumen` VARCHAR(200) NOT NULL,
  `nomor` VARCHAR(100) NULL,
  `penerbit` VARCHAR(150) NULL,
  `berlaku_sampai` DATE NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Aktif',
  `catatan` TEXT NULL,
  `urutan` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. TABEL LOG REKAP PEMESANAN WHATSAPP (Arsip Pabrik)
CREATE TABLE IF NOT EXISTS `orders_wa_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_mitra` VARCHAR(150) NOT NULL,
  `telepon` VARCHAR(30) NOT NULL,
  `alamat` TEXT NOT NULL,
  `varian_produk` VARCHAR(150) NOT NULL,
  `jumlah` INT NOT NULL DEFAULT 1,
  `satuan` VARCHAR(20) NOT NULL DEFAULT 'Bal',
  `total_harga` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `catatan` TEXT NULL,
  `status` ENUM('Baru Masuk', 'Diproses', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Baru Masuk',
  `sumber` VARCHAR(30) NOT NULL DEFAULT 'Web WhatsApp',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. TABEL PENGATURAN KANTOR PABRIK
CREATE TABLE IF NOT EXISTS `company_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEED DATA NATIVE
INSERT INTO `users` (`nama`, `email`, `password`, `role`) VALUES
('Bapak Komari Yaman (Owner)', 'owner@keretakencana.com', '$2y$10$wN1eK6K2p8PzB6J5B0yCseWc7vW9sN0J3W.mJ2YxR4m0H8k2C.tOa', 'owner'),
('Admin Operasional Pabrik', 'admin@keretakencana.com', '$2y$10$wN1eK6K2p8PzB6J5B0yCseWc7vW9sN0J3W.mJ2YxR4m0H8k2C.tOa', 'admin')
ON DUPLICATE KEY UPDATE `email`=`email`;

INSERT INTO `products` (`slug`, `nama`, `kategori`, `deskripsi`, `profil_rasa`, `tar_mg`, `nikotin_mg`, `batang_per_bungkus`, `bungkus_per_slop`, `slop_per_bal`, `harga_per_bal`, `min_order_bal`, `status_stok`, `image_url`, `urutan`) VALUES
('kencana-merah-12', 'Kencana Merah 12', 'SKM', 'Sigaret Kretek Mesin full flavour dengan racikan tembakau Jawa pilihan dan saus cengkeh gurih berkarakter mantap.', 'Full flavour, manis cengkeh pekat, aroma hangat', 28.0, 1.8, 12, 10, 20, 1250000, 5, 'Ready Stock', 'assets/img/hero-pabrik.jpg', 1),
('kencana-mild-16', 'Kencana Mild 16', 'SKM', 'Sigaret Kretek Mesin low tar nicotine untuk target pasar perkotaan.', 'Ringan, bersih, sentuhan cengkeh lembut', 14.0, 1.0, 16, 10, 20, 1450000, 5, 'Ready Stock', 'assets/img/gudang-distribusi.jpg', 2),
('kencana-biru-20', 'Kencana Biru 20', 'SKM', 'SKM isi 20 batang, karakter medium untuk konsumsi harian distributor grosir volume besar.', 'Medium body, seimbang, after taste manis', 22.0, 1.5, 20, 10, 20, 1750000, 5, 'Pre-Order', 'assets/img/hero-pabrik.jpg', 3),
('kereta-klasik-12', 'Kereta Klasik 12', 'SKT', 'Sigaret Kretek Tangan linting tradisional khas Blitar dengan daun tembakau asli pegunungan dan rempah aromatik.', 'Tebal, rempah kuat, aroma kretek klasik', 32.0, 2.1, 12, 10, 20, 980000, 10, 'Ready Stock', 'assets/img/produksi-skt.jpg', 4),
('kereta-sepur-16', 'Kereta Sepur 16', 'SKT', 'SKT isi 16 batang dengan saus warisan resep turun-temurun.', 'Gurih, manis saus, bakar lambat', 30.0, 2.0, 16, 10, 20, 1180000, 10, 'Ready Stock', 'assets/img/produksi-skt.jpg', 5),
('kencana-emas-12', 'Kencana Emas 12', 'SKT', 'SKT premium mahakarya lintingan tangan terlatih dengan cengkeh Zanzibar Grade A dan tembakau Srintil istimewa.', 'Premium, kompleks, aroma cengkeh wangi', 29.0, 1.9, 12, 10, 20, 1520000, 10, 'Pre-Order', 'assets/img/tembakau-cengkeh.jpg', 6)
ON DUPLICATE KEY UPDATE `slug`=`slug`;

INSERT INTO `legal_documents` (`nama_dokumen`, `nomor`, `penerbit`, `berlaku_sampai`, `status`, `catatan`, `urutan`) VALUES
('NPPBKC (Izin Pita Cukai Bea Cukai)', '0821.1.2.XXXXX', 'KPPBC Tipe Madya Blitar', '2028-12-31', 'Aktif', 'Izin pita cukai resmi dari Direktorat Jenderal Bea dan Cukai.', 1),
('NIB (Nomor Induk Berusaha)', '9120001234567', 'Kementerian Investasi / BKPM RI', '2030-01-01', 'Aktif', 'Identitas berusaha sektor industri pengolahan tembakau.', 2),
('Izin Usaha Industri (IUI)', '503/IUI/TMBK/2021', 'DPMPTSP Kabupaten Blitar', '2029-06-30', 'Aktif', 'Izin pabrikan sigaret kretek di Ponggok Blitar.', 3),
('Sertifikat Merek Dagang Kereta Kencana', 'IDM000987654', 'DJKI Kemenkumham RI', '2032-10-15', 'Aktif', 'Hak eksklusif merek dagang kelas 34.', 4)
ON DUPLICATE KEY UPDATE `nama_dokumen`=`nama_dokumen`;
