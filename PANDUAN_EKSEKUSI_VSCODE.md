# Panduan Eksekusi Website PR. KERETA KENCANA di VSCode
**Pabrik Sigaret Kretek Mesin (SKM) & Sigaret Kretek Tangan (SKT) Ponggok, Kabupaten Blitar**

Website ini telah berhasil dibangun ulang dan disempurnakan menggunakan **PHP Native, HTML5, CSS3, dan JavaScript murni** (mengikuti standar modul *BKPM - Workshop Sistem Informasi Berbasis Web*).

---

## 1. Cara Eksekusi Langsung di VSCode (Sangat Cepat & Praktis)

Anda tidak perlu setup server yang rumit. Cukup gunakan **PHP Built-in Server** yang sudah tersedia di laptop/komputer Anda:

1. Buka folder proyek ini di **Visual Studio Code**.
2. Buka Terminal di VSCode (Tekan `` Ctrl + ` `` atau menu **Terminal -> New Terminal**).
3. Jalankan perintah berikut di terminal:
   ```bash
   php -S localhost:8000
   ```
4. Buka browser Anda (Google Chrome, Edge, dsb.) dan akses:
   ```
   http://localhost:8000
   ```
5. Website **PR. KERETA KENCANA** langsung berjalan 100% lengkap dengan data produk, perizinan, dan pemesanan!

> **Catatan Keren:** Sistem database diatur fleksibel: jika MySQL (Laragon/XAMPP) Anda belum dinyalakan, sistem otomatis menggunakan SQLite lokal tanpa error. Jika MySQL menyala, sistem otomatis menghubungkan ke database MySQL.

---

## 2. Cara Menggunakan Database MySQL di Laragon / XAMPP (Opsi Pilihan)

Jika Anda ingin menjalankannya melalui Laragon atau XAMPP:
1. Buka Laragon / XAMPP dan klik **Start All** (Apache & MySQL).
2. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Buat database baru dengan nama: `db_kereta_kencana`.
4. Klik tab **Import** dan pilih file `database.sql` yang ada di folder proyek ini, lalu klik **Import**.
5. Konfigurasi host, user, dan password database dapat Anda sesuaikan di file `config/database.php` jika diperlukan (secara default: user `root`, password kosong `""`).

---

## 3. Akun Login Bawaan untuk Pengujian Dashboard Operasional

Untuk masuk ke dashboard manajemen staf dan operasional pabrik:
- URL Login: `http://localhost:8000/login.php`
- Di halaman login juga telah disediakan tombol klik cepat **"Role: Owner"** dan **"Role: Admin"** sehingga Anda tidak perlu mengetik manual.

### A. Role Owner (Pemilik Pabrik)
- **Email:** `owner@keretakencana.com`
- **Kata Sandi:** `password123`
- **Hak Akses:** Akses penuh ke seluruh sistem — melihat total omzet dan metrik pesanan, mengelola status pesanan, menambah/mengubah varian rokok SKM & SKT (CRUD Produk), memperbarui dokumen legalitas/cukai, dan mengelola akun staf/admin.

### B. Role Admin (Staf Operasional Gudang & Penjualan)
- **Email:** `admin@keretakencana.com`
- **Kata Sandi:** `password123`
- **Hak Akses:** Memproses pesanan distributor B2B, memperbarui status pengiriman (*Pending* &rarr; *Dikonfirmasi* &rarr; *Diproses Pabrik* &rarr; *Dikirim* &rarr; *Selesai*), menginput **Nomor DO (Delivery Order)** dan **Nomor Resi Ekspedisi Truk**, serta mencetak faktur invoice resmi.

---

## 4. Daftar Halaman & Fitur Lengkap yang Telah Berhasil Dibangun

| Halaman | Berkas | Deskripsi & Fitur |
| :--- | :--- | :--- |
| **Beranda** | `index.php` | Hero section identitas pabrik, pilar keunggulan produksi, narasi bahan baku tembakau & cengkeh, cuplikan katalog unggulan, dan ajakan kemitraan daerah. |
| **Profil Perusahaan** | `profil.php` | Sejarah pabrik dari linting rumahan hingga modern, Visi & Misi, standar mutu, tabel legalitas cukai & izin usaha resmi, foto gudang, dan integrasi Google Maps pabrik di Ponggok, Blitar. |
| **Katalog Produk** | `produk.php` | Daftar lengkap varian SKM & SKT dengan filter tombol kategori interaktif, rincian kemasan (batang/bungkus, slop/bal, total batang), tar/nikotin, profil rasa, dan harga bal. |
| **Detail Spesifikasi** | `detail-produk.php` | Halaman teknis tiap varian produk rokok beserta tombol pemesanan cepat. |
| **Form Pemesanan B2B** | `pesan.php` | Formulir pemesanan distributor/grosir dengan **kalkulasi otomatis** volume dan estimasi harga secara real-time, validasi batas minimum order bal, identitas mitra, dan penyimpanan ke database. |
| **Faktur / Invoice B2B** | `invoice.php` | Lembar resmi Surat Pesanan B2B siap cetak (`window.print()`), rincian DO/Resi, instruksi distribusi, serta **tombol WhatsApp resmi** yang otomatis menyusun format pesan order lengkap ke nomor pabrik. |
| **Kontak Resmi** | `kontak.php` | Halaman kontak tersendiri, peta responsif Google Maps Ponggok Blitar, info operasional pabrik, tombol chat WhatsApp, dan formulir kirim pesan kemitraan B2B. |
| **Masuk Staf** | `login.php` | Autentikasi aman berbasis sesi PHP & hashing password dengan tombol instan demo akun. |
| **Dashboard Utama** | `admin/index.php` | Ringkasan metrik volume bal, estimasi nilai order, transaksi terbaru, dan pesan kemitraan masuk. |
| **Kelola Pesanan** | `admin/pesanan.php` | Filter status pesanan, update alur pengiriman, input nomor DO & nomor Resi ekspedisi armada. |
| **Rincian Pesanan** | `admin/pesanan_detail.php` | Rincian lengkap item per bal yang dipesan distributor dan tindakan operasional staf. |
| **Kelola Produk** | `admin/produk.php` | CRUD lengkap varian SKM & SKT (tambah, ubah harga bal, edit profil rasa, aktifkan/nonaktifkan). |
| **Kelola Legalitas** | `admin/legalitas.php` | Manajemen dokumen perizinan NPPBKC, NIB, IUI, dan sertifikat merek dagang. |
| **Kelola Staf** | `admin/users.php` | Khusus Role Owner: mendaftarkan akun staf baru dan mengatur perizinan. |

---

## 5. Struktur Direktori Proyek

```
d:/kereta-kencana-portal-main/
├── admin/
│   ├── footer.php
│   ├── header.php
│   ├── index.php
│   ├── legalitas.php
│   ├── logout.php
│   ├── pesanan.php
│   ├── pesanan_detail.php
│   ├── produk.php
│   └── users.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── style.css
│   ├── img/
│   │   ├── gudang-distribusi.jpg
│   │   ├── hero-pabrik.jpg
│   │   ├── produksi-skt.jpg
│   │   └── tembakau-cengkeh.jpg
│   └── js/
│       ├── main.js
│       └── order.js
├── config/
│   ├── app.php
│   └── database.php
├── includes/
│   ├── age_gate.php
│   ├── auth_check.php
│   ├── footer.php
│   └── header.php
├── database.sql
├── detail-produk.php
├── index.php
├── invoice.php
├── kontak.php
├── login.php
├── pesan.php
├── produk.php
├── profil.php
└── PANDUAN_EKSEKUSI_VSCODE.md
```
