# PR. KERETA KENCANA — Portal B2B & Manajemen Operasional Pabrik
**Sistem Informasi Berbasis Web & API Pabrik Rokok Sigaret Kretek Tangan (SKT) Ponggok, Kabupaten Blitar**  
*Dibangun Menggunakan Framework Laravel Sesuai Kurikulum Modul BKPM (Workshop Sistem Informasi Berbasis Web 2)*

---

## 1. Ikhtisar Sistem
Website resmi dan sistem operasional B2B untuk pabrik rokok **PR. KERETA KENCANA** (berlokasi di Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kec. Ponggok, Kabupaten Blitar, Jawa Timur):
- **Age Gate & Regulasi (21+)**: Sesuai ketentuan PP 28/2024 tentang pembatasan usia informasi produk hasil tembakau.
- **Company Profile & Legalitas**: Profil pabrik kretek tangan, proses produksi SKT, daftar dokumen legalitas resmi (NPPBKC tersensor untuk pencegahan penyalahgunaan), dan integrasi peta lokasi pabrik.
- **Katalog Produk SKT**: 3 varian Sigaret Kretek Tangan (**SEMBADA 12**, **KERETA KENCANA 12**, dan **SEMBADA Cethe 12**) dengan rincian kemasan (batang/bungkus, slop), profil rasa, serta filter & pencarian dinamis.
- **Sistem Pemesanan Online (Slop) & Pelacakan**:
  - Formulir order online multi-varian hingga total maksimal **19 Slop gabungan** dengan tarif bertingkat otomatis (`1–5 Slop`: Rp 70.000, `6–10 Slop`: Rp 65.000, `>10 Slop`: Rp 60.000). Pembelian skala Bal diarahkan ke WhatsApp Admin pabrik (`+62 857-9062-7104`).
  - Mendukung pembayaran **Bayar di Tempat (COD)** dan **Transfer Bank** (konfirmasi rekening resmi via WhatsApp Admin + unggah bukti transfer di menu *Pesanan Saya*).
  - Dilengkapi penerbitan faktur resmi, mekanisme **Order Lock**, dan konfirmasi penerimaan barang dengan foto bukti.
- **Panel Dashboard Manajemen (Multi-Role RBAC)**:
  - **Owner** & **Super Admin**: Ringkasan metrik stok & transaksi, CRUD produk & kategori, verifikasi pembayaran & verifikasi manual surat jalan fisik, manajemen akun staf, dokumen legalitas, serta Log Audit Trail.
  - **Staff / Admin Gudang**: Pengelolaan operasional pesanan distributor, kasir penjualan langsung (offline), penerbitan **Nomor DO** dan **Nomor Resi**, serta cetak faktur Delivery Order.
- **REST API Mobile (`/api/v1/*`)**: Layanan API Sanctum untuk aplikasi mobile mitra B2B (autentikasi, katalog, order lock, pemesanan idempotent, pelacakan, dan unggah bukti).

---

## 2. Keselarasan dengan Modul Perkuliahan BKPM

| Acara BKPM | Materi / Fitur yang Diterapkan |
| :--- | :--- |
| **Acara 5 - 6** | Instalasi framework Laravel `^13.17` (PHP `^8.3`), arsitektur MVC, konfigurasi `.env`, database MySQL di Laragon, dan kontrol versi Git/GitHub. |
| **Acara 7, 19, 20** | Model Eloquent, Relasi (`hasMany`, `belongsTo`, `belongsToMany` via pivot `barang_user`), Database Migrations, dan Seeders (`UserSeeder`, `KategoriSeeder`, `BarangSeeder`, `LegalitasSeeder`, `TransaksiSeeder`). |
| **Acara 8 - 10** | Autentikasi session web & Sanctum API, Middleware [`CheckRole`](app/Http/Middleware/CheckRole.php) & [`EnsurePelangganRole`](app/Http/Middleware/EnsurePelangganRole.php), dan hierarki peran (`owner`, `superadmin`, `staff`, `pelanggan`). |
| **Acara 11 - 12** | Blade Layouts terstruktur (`layouts/app.blade.php`, `layouts/admin.blade.php`), navigasi responsif, flash messages, serta modal verifikasi usia 21+ dan pratinjau varian. |
| **Acara 13 - 16** | CRUD Kategori Sigaret (SKT) & CRUD Produk Rokok lengkap dengan unggah gambar ke `storage/app/public`. |
| **Acara 17 - 18** | CRUD Transaksi Pemesanan, kasir offline pabrik, pengurangan stok otomatis, serta pemulihan stok (*stock recovery*) saat pesanan dibatalkan/dihapus. |
| **Acara 21 - 23** | Endpoint JSON REST API (`/api/v1/*`), filter pencarian status pesanan, saluran order, kategori produk, dan paginasi `withQueryString()`. |
| **Acara 24 - 26** | Lembar cetak Invoice Mitra & Faktur Delivery Order siap print (`@media print` / `window.print()`), serta audit trail logging (`ActivityLogger`). |
| **Acara 27** | Pengujian otomatis Unit & Feature Test (`php artisan test`) — **41 tests, 246 assertions**. |

---

## 3. Cara Menjalankan Aplikasi di Laragon / VSCode

### Opsi A: Menggunakan Artisan Server (Rekomendasi Cepat)
1. Buka terminal di folder proyek (`d:\kereta-kencana-mitra`).
2. Pastikan layanan MySQL aktif dan jalankan perintah:
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

## 4. Akun Login Bawaan (`UserSeeder`)

Akses halaman login di: `http://127.0.0.1:8000/login`

1. **Role Owner (Pemilik Pabrik)**
   - **Email:** `owner@keretakencana.com`
   - **Password:** `password123`
   - **Akses:** Akses penuh seluruh metrik, pengelolaan transaksi, verifikasi manual, produk, manajemen staf/akun, legalitas, dan audit log.

2. **Role Super Admin (Teknis / Web Dev)**
   - **Email:** `superadmin@keretakencana.com`
   - **Password:** `password123`
   - **Akses:** Pengelolaan penuh operasional, verifikasi manual, manajemen akun, pemeliharaan sistem, dan audit trail log.

3. **Role Staff / Admin Gudang & Pemesanan**
   - **Email:** `admin@keretakencana.com`
   - **Password:** `password123`
   - **Akses:** Memproses pesanan distributor, kasir offline pabrik, update nomor DO / Resi, cetak faktur, dan pengelolaan katalog/legalitas.

---

## 5. Pengujian Otomatis (Feature & Unit Tests)
Untuk menjalankan seluruh suite pengujian otomatis:
```bash
php artisan test
```
Cakupan pengujian meliputi **41 test (246 assertions)** di `tests/Unit/` dan `tests/Feature/` (akses publik, pemesanan B2B multi-item, harga slop bertingkat, upload bukti Transfer Bank, proteksi invoice, RBAC admin, stok recovery, legalitas, dan API Mobile `/api/v1/*`).
