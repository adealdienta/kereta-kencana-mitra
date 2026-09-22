# PR. KERETA KENCANA — Portal B2B & Manajemen Operasional Pabrik
**Sistem Informasi Berbasis Web Pabrik Rokok (SKM & SKT) Ponggok, Kabupaten Blitar**  
*Dibangun Menggunakan Framework Laravel Sesuai Kurikulum Modul BKPM (Workshop Sistem Informasi Berbasis Web 2)*

---

## 1. Ikhtisar Sistem
Website resmi dan sistem operasional B2B untuk pabrik rokok **PR. KERETA KENCANA** (berlokasi di Dusun Subontoro, Desa Kebonduren, Kec. Ponggok, Kabupaten Blitar, Jawa Timur):
- **Age Gate & Regulasi (18+)**: Sesuai hukum dan ketentuan industri hasil tembakau RI.
- **Company Profile**: Sejarah lintingan tradisional hingga modern, Visi-Misi, standar mutu tembakau Jawa & cengkeh Zanzibar, tabel perizinan cukai resmi (NPPBKC, NIB, IUI, Hak Merek DJKI), dan embed peta interaktif Google Maps pabrik.
- **Katalog Produk B2B**: Varian Sigaret Kretek Mesin (SKM) dan Sigaret Kretek Tangan (SKT) dengan spesifikasi tar/nikotin, rincian kemasan (batang/bungkus, slop, bal), serta filter dinamis.
- **Sistem Pemesanan Grosir**: Formulir order distributor B2B dengan kalkulasi harga dinamis real-time, validasi batas minimum order bal, pengurangan stok otomatis, dan penerbitan faktur resmi yang terintegrasi dengan WhatsApp pabrik.
- **Panel Dashboard Manajemen (Multi-Role RBAC)**:
  - **Owner**: Ringkasan metrik bisnis, omzet distribusi, CRUD produk, manajemen staf/admin, dan dokumen perizinan.
  - **Super Admin**: Pengelolaan teknis sistem, manajemen akun pengguna, dan audit trail log.
  - **Staff**: Pengelolaan operasional pesanan distributor, penerbitan **Nomor DO (Delivery Order)** dan **Nomor Resi Truk Ekspedisi**, serta cetak invoice.

---

## 2. Keselarasan dengan Modul Perkuliahan BKPM

| Acara BKPM | Materi / Fitur yang Diterapkan |
| :--- | :--- |
| **Acara 5 - 6** | Instalasi framework Laravel 11/12, arsitektur MVC, konfigurasi `.env`, dan database MySQL di Laragon. |
| **Acara 7, 13, 15, 17, 20** | Model Eloquent, Relasi (`hasMany`, `belongsTo`, pivot `barang_user`), Database Migrations, dan Database Seeders (`UserSeeder`, `KategoriSeeder`, `BarangSeeder`, `LegalitasSeeder`, `TransaksiSeeder`). |
| **Acara 8 - 10** | Autentikasi berbasis session, Middleware `CheckRole`, pengamanan rute dashboard, dan hierarki peran (Owner, Super Admin, Staff). |
| **Acara 11 - 12** | Blade Layouts terstruktur (`layouts/app.blade.php`, `layouts/admin.blade.php`), komponen navigasi, alert flash messages, dan modal 18+. |
| **Acara 13 - 16** | CRUD Kategori Sigaret (`SKM` & `SKT`), CRUD Produk Rokok lengkap dengan fitur upload foto/gambar produk (`storage/app/public`). |
| **Acara 17 - 19** | CRUD Transaksi Pemesanan Distributor B2B, otomatisasi pengurangan stok (Acara 18), pemulihan stok saat pesanan dibatalkan (Stok Recovery), serta relasi antar tabel. |
| **Acara 21 - 23** | API endpoint JSON untuk log pesanan WhatsApp, Filter pencarian status pesanan dan kategori produk. |
| **Acara 24 - 26** | Lembar cetak faktur / Delivery Order siap print (`window.print()`), audit trail activity logging. |
| **Acara 27** | Pengujian otomatis Unit & Feature Test (`php artisan test`) — **100% Passed (7 tests, 20 assertions)**. |

---

## 3. Cara Menjalankan Aplikasi di Laragon / VSCode

### Opsi A: Menggunakan Artisan Server (Rekomendasi Cepat)
1. Buka terminal di folder proyek (`c:\laragon\www\kereta-kencana-mitra`).
2. Jalankan perintah:
   ```bash
   php artisan serve
   ```
3. Buka browser dan kunjungi:
   ```
   http://127.0.0.1:8000
   ```

### Opsi B: Menggunakan Virtual Host Otomatis Laragon
1. Buka aplikasi Laragon dan klik **Start All** (Apache & MySQL).
2. Akses langsung melalui URL:
   ```
   http://kereta-kencana-mitra.test
   ```

---

## 4. Akun Login Bawaan untuk Evaluasi Sistem

Akses halaman login di: `http://127.0.0.1:8000/login`  
*(Tersedia tombol klik instan Demo Role di halaman login tanpa perlu mengetik manual)*

1. **Role Owner (Pemilik Pabrik)**
   - **Email:** `owner@keretakencana.com`
   - **Password:** `password123`
   - **Akses:** Akses penuh seluruh metrik, omzet distribusi, CRUD produk, manajemen staf, dan legalitas.

2. **Role Staff (Operasional Gudang & Pemesanan)**
   - **Email:** `staff@keretakencana.com`
   - **Password:** `password123`
   - **Akses:** Memproses pesanan distributor B2B, update nomor DO / Resi, dan mencetak faktur invoice.

3. **Role Super Admin (Teknis / Web Dev)**
   - **Email:** `superadmin@keretakencana.com`
   - **Password:** `password123`
   - **Akses:** Manajemen akun, pemeliharaan sistem, dan jejak rekam audit trail log.

---

## 5. Pengujian Otomatis (Feature & Unit Tests)
Untuk menjalankan seluruh suite pengujian otomatis:
```bash
php artisan test
```
Hasil pengujian:
```text
Pass: 7 tests, 20 assertions
- test_halaman_publik_dapat_diakses
- test_halaman_detail_produk
- test_alur_pemesanan_b2b_mengurangi_stok
- test_role_owner_dapat_membuka_dashboard
- test_role_staff_dibatasi_dari_menu_users
```
