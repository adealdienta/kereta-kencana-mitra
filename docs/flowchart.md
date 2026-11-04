# DOKUMENTASI FLOWCHART SISTEM APLIKASI MOBILE MITRA B2B
## PR. KERETA KENCANA
**Standar Konvensi: ISO 5807 (Unified Single End-to-End System Flowchart)**  
**Penyusun: Senior System Analyst**  
**Versi: 2.0 (Single Unified Flowchart: Dari Registrasi/Login hingga Logout & Penutupan Aplikasi)**

---

## 1. RINGKASAN TEMUAN SISTEM (SYSTEM ANALYSIS SUMMARY)

Berdasarkan analisis menyeluruh terhadap arsitektur kode sumber Android Kotlin (`mobile_app/app/src/main/`) dan backend Laravel API (`app/Http/Controllers/Api/`, `routes/api.php`):

### 1.1 Modul dan Fitur Terintegrasi
1. **Entry Point & Inisialisasi**: `SplashActivity.kt` menampilkan jeda visual (1.5 detik), memeriksa status sesi akun di `SessionManager.kt`, lalu membuka kontainer 3 tab utama `MainActivity.kt`.
2. **Autentikasi Terpadu**:
   - `RegisterActivity.kt` & `RegisterSuccessActivity.kt`: Formulir pendaftaran mitra grosir, validasi error lokal/server (seperti mockup S6/S7), persetujuan usia 21+, dan penerbitan sesi aktif otomatis (S8).
   - `ForgotPasswordActivity.kt`: Mekanisme pemulihan sandi 4 langkah menggunakan kode OTP 6 digit via email (S12, S13, S14, S15).
   - `LoginBottomSheetFragment.kt`: Modal dialog masuk akun multi-identifier (Email atau WhatsApp) menghasilkan Sanctum Bearer Token (S5/S11/S16).
3. **Katalog Produk & Detail Grosir**:
   - `BerandaFragment.kt` & `BarangAdapter.kt`: Pemuatan katalog rokok kretek pabrik (`GET /api/v1/barang`), kalkulasi stok perkiraan slop, dan fitur tambah slop cepat.
   - `DetailProdukActivity.kt`: Tampilan spesifikasi kadar Tar, Nikotin, batang rokok, pemilihan satuan kargo independen (**Slop** dan **Bal** - Aturan B1), serta kepatuhan batas minimum order (Aturan B3).
4. **Keranjang Belanja & Proteksi Order Lock**:
   - `CartActivity.kt` & `CartManager.kt`: Penyimpanan baris belanja multi-satuan ke SharedPreferences, input catatan kurir pengantaran, dan verifikasi status *Order Lock* (`GET /api/v1/pesanan/status-kunci` - Aturan B4). Jika pesanan berstatus *Dikirim* masih berjalan, tombol checkout dikunci otomatis.
5. **Checkout & Transaksi Pembelian**:
   - `CheckoutActivity.kt` & `OrderSuccessActivity.kt`: Pemilihan metode pembayaran (*COD / Bayar di Tempat* atau *Transfer Bank Pabrik*), pengiriman header `Idempotency-Key` (UUID) untuk mencegah pesanan ganda, transaksi database berkunci pesimistik (`lockForUpdate()`), agregasi kebutuhan bal/slop, pemotongan stok gudang pabrik otomatis, dan integrasi WhatsApp konfirmasi kode transaksi.
6. **Pelacakan (Tracking) & Pengesahan Penerimaan**:
   - `PesananFragment.kt` & `DetailPesananActivity.kt`: Filter status (*Semua*, *Berjalan*, *Selesai*, *Dibatalkan*), stepper pelacakan 4 tahap (*Dipesan* ➔ *Disiapkan* ➔ *Dikirim* ➔ *Selesai*), serta pembatalan mandiri untuk status *Baru Masuk* disertai pengembalian otomatis kuota stok barang ke gudang pabrik.
   - `KonfirmasiPenerimaanActivity.kt`: Pengambilan foto fisik barang tiba di toko, unggah berkas multipart, pengesahan status *Selesai* & pembayaran *Lunas*, serta pembukaan kembali Order Lock akun.
7. **Penerbitan Dokumen Faktur Resmi**:
   - `FakturActivity.kt`: Penerbitan dokumen faktur resmi PR. Kereta Kencana, penyertaan nomor izin cukai tembakau **NPPBKC**, stempel digital dinamis lunas/belum lunas, pembuatan QR Code dinamis ZXing, pencetakan struk printer thermal (`PrintHelper`), dan ekspor berkas PDF (`PdfDocument` & `FileProvider`).
8. **Pengelolaan Profil Toko & Logout Sesi**:
   - `AkunFragment.kt`, `DataTokoActivity.kt`, `AlamatTokoActivity.kt`, `UbahSandiActivity.kt`: Pengunggahan avatar foto toko via multipart, pembaruan data toko & alamat pengiriman, ubah kata sandi akun, CS WhatsApp pabrik, dialog konfirmasi keluar (S23), pencabutan token Sanctum server, dan pembersihan sesi serta keranjang belanja lokal.

### 1.2 Catatan Penyelarasan Desain (S1–S36) vs Realisasi Kode
- **Peta Interaktif (S19)**: Direalisasikan dengan form input teks alamat lengkap terpadu pada `DataTokoActivity.kt` dan `AlamatTokoActivity.kt` yang tersimpan langsung di profil pelanggan untuk pedoman kurir pabrik tanpa dependensi Google Maps SDK.
- **Pusat Notifikasi (S21/S22)**: Ikon notifikasi pada header Beranda menampilkan Toast notifikasi armada pengiriman, sementara histori dan pelacakan dipusatkan langsung pada tab Pesanan dan Stepper Tracking.
- **Modal Login (S5/S11/S16)**: Disajikan melalui modal bottom sheet dialog (`LoginBottomSheetFragment.kt`) yang fleksibel dipanggil dari tab Beranda, Keranjang, Pesanan, maupun Akun tanpa merusak tumpukan navigasi (back stack).

---

## 2. TABEL LEGENDA SIMBOL FLOWCHART (STANDAR ISO 5807)

| Simbol ISO 5807 | Notasi Sintaks Mermaid | Definisi & Penggunaan dalam Sistem |
| :--- | :--- | :--- |
| **Terminator** | `id([Mulai])` / `id([Selesai])` | Titik awal peluncuran aplikasi dan titik akhir penyelesaian sesi |
| **Process** | `id[Nama proses]` | Operasi manipulasi data internal, kalkulasi, atau perubahan state sistem |
| **Decision** | `id{Kondisi?}` | Percabangan logika kondisi yang menghasilkan minimal 2 jalur berlabel |
| **Input / Output** | `id[/Input atau Output/]` | Komunikasi data digital (Request / Response REST API JSON) |
| **Predefined Process** | `id[[Nama Subrutin]]` | Pemanggilan fungsi/subrutin independen (Idempotency UUID, QR Code, Print, PDF) |
| **Database** | `id[(Database / Storage)]` | Media penyimpanan data persisten (MySQL server, SharedPreferences, File Storage) |
| **Dokumen** | `id@{ shape: doc, label: "Dokumen" }` | Dokumen resmi cetak/digital (Faktur & Nota Penjualan Resmi) |
| **Manual Input** | `id[/Input manual\]` | Input pengguna via keyboard/form (kredensial, qty, catatan) atau kamera fisik |
| **Connector** | `id((A))` | Penghubung visual antarbangsal alur dalam satu flowchart utuh |
| **Display** | `id@{ shape: display, label: "Tampilan" }` | Tampilan visual antarmuka pengguna (Activity, Fragment, Modal Dialog) |

---

## 3. UNIFIED FLOWCHART: ALUR UTAMA APLIKASI MOBILE MITRA (END-TO-END)

Flowchart tunggal di bawah ini menggambarkan seluruh siklus hidup aplikasi mobile mitra B2B PR. Kereta Kencana secara utuh dari awal peluncuran splash screen, autentikasi (login, register, lupa sandi), penjelajahan katalog rokok, manajemen keranjang multi-satuan, validasi order lock, transaksi checkout & pembayaran, pelacakan pesanan 4 tahap, pembatalan mandiri, konfirmasi penerimaan fisik, penerbitan/pencetakan faktur resmi, hingga manajemen akun toko dan logout.

```mermaid
flowchart TD
    classDef terminator fill:#d4edda,stroke:#28a745,stroke-width:2px,color:#155724;
    classDef decision fill:#fff3cd,stroke:#ffc107,stroke-width:2px,color:#856404;
    classDef io fill:#d1ecf1,stroke:#17a2b8,stroke-width:2px,color:#0c5460;
    classDef database fill:#e2e3e5,stroke:#6c757d,stroke-width:2px,color:#383d41;
    classDef process fill:#ffffff,stroke:#343a40,stroke-width:2px,color:#212529;

    %% ----------------------------------------------------
    %% 1. INISIALISASI & SPLASH SCREEN
    %% ----------------------------------------------------
    START([Mulai])
    SPLASH[Jalankan Delay Splash Screen 1.5 Detik]
    DISP_SPLASH@{ shape: display, label: "Tampilkan Layar Splash Screen (S1)" }
    CHECK_AUTO_LOGIN{"Cek Sesi Login di SharedPreferences?"}
    SET_SESSION_ACTIVE[Muat Token & Profil User dari SessionManager]
    DB_SESSION_LOCAL[(SharedPreferences: SessionManager)]
    DEC_AUTH_CHOICE{"Pilih Jalur Akses Pengguna?"}

    START --> SPLASH
    SPLASH --> DISP_SPLASH
    DISP_SPLASH --> CHECK_AUTO_LOGIN
    CHECK_AUTO_LOGIN -->|Ada Sesi Aktif| SET_SESSION_ACTIVE
    SET_SESSION_ACTIVE --> DB_SESSION_LOCAL
    DB_SESSION_LOCAL --> CONN_MAIN((M))
    CHECK_AUTO_LOGIN -->|Belum Ada Sesi| DEC_AUTH_CHOICE

    %% ----------------------------------------------------
    %% 2. JALUR REGISTRASI MITRA BARU
    %% ----------------------------------------------------
    DISP_REG@{ shape: display, label: "Buka Layar RegisterActivity (S7)" }
    INP_REG[/"Input Nama Toko, Pemilik, WA, Email, Sandi, & Centang 21+"\]
    VAL_REG{"Validasi Formulir Lengkap & Syarat Usia 21+?"}
    ERR_REG_INPUT["Tampilkan Border Merah & Error Input (S6)"]
    REQ_REG[/Kirim Request POST /api/v1/register/]
    DB_USERS_SERVER[(Database Users MySQL)]
    RES_REG[/Terima Respons Registrasi/]
    DEC_REG_RES{"Apakah Pendaftaran Berhasil Diterima?"}
    ERR_REG_SERVER[Tampilkan Pesan Error Server]
    SAVE_REG_SESSION[Simpan Token Sanctum & User ke SessionManager]
    DISP_REG_SUCCESS@{ shape: display, label: "Buka Layar RegisterSuccessActivity (S8)" }
    BTN_START_SHOP[Klik Mulai Berbelanja]

    DEC_AUTH_CHOICE -->|Daftar Akun Baru| DISP_REG
    DISP_REG --> INP_REG
    INP_REG --> VAL_REG
    VAL_REG -->|Tidak Valid| ERR_REG_INPUT
    ERR_REG_INPUT --> INP_REG
    VAL_REG -->|Valid| REQ_REG
    REQ_REG --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_REG
    RES_REG --> DEC_REG_RES
    DEC_REG_RES -->|Gagal / Duplikat| ERR_REG_SERVER
    ERR_REG_SERVER --> INP_REG
    DEC_REG_RES -->|Berhasil| SAVE_REG_SESSION
    SAVE_REG_SESSION --> DB_SESSION_LOCAL
    SAVE_REG_SESSION --> DISP_REG_SUCCESS
    DISP_REG_SUCCESS --> BTN_START_SHOP
    BTN_START_SHOP --> CONN_MAIN

    %% ----------------------------------------------------
    %% 3. JALUR LUPA KATA SANDI (4 LANGKAH OTP)
    %% ----------------------------------------------------
    DISP_FORGOT1@{ shape: display, label: "Buka ForgotPasswordActivity Step 1 (S12)" }
    INP_FORGOT_ID[/"Masukkan Email / No WhatsApp Terdaftar"\]
    REQ_FORGOT[/Kirim Request POST /api/v1/forgot-password/]
    RES_FORGOT[/Terima Respons Permintaan OTP/]
    DEC_FORGOT_USER{"Apakah Akun Terdaftar di Sistem?"}
    ERR_FORGOT_NOTFOUND[Tampilkan Pesan Akun Tidak Ditemukan]
    SEND_OTP[Server Kirim 6-Digit OTP via Email & Timer 45 Detik]
    DISP_FORGOT2@{ shape: display, label: "Pindah ke Step 2: Masukkan Kode OTP (S13)" }
    INP_OTP[/"Masukkan 6 Digit Kode Verifikasi OTP"\]
    REQ_VERIFY_OTP[/Kirim Request POST /api/v1/verify-reset-token/]
    RES_VERIFY_OTP[/Terima Respons Verifikasi OTP/]
    DEC_OTP_VALID{"Apakah Kode OTP Cocok & Belum Kedaluwarsa?"}
    ERR_OTP[Tampilkan Pesan OTP Salah atau Usang]
    DISP_FORGOT3@{ shape: display, label: "Pindah ke Step 3: Kata Sandi Baru (S14)" }
    INP_NEW_PASS[/"Masukkan Sandi Baru Min 6 Karakter & Konfirmasi"\]
    VAL_NEW_PASS{"Sandi Minimal 6 Karakter & Konfirmasi Cocok?"}
    ERR_NEW_PASS[Tampilkan Pesan Konfirmasi Sandi Tidak Cocok]
    REQ_RESET_PASS[/Kirim Request POST /api/v1/reset-password/]
    RES_RESET_PASS[/Terima Respons Reset Sandi/]
    DISP_FORGOT4@{ shape: display, label: "Pindah ke Step 4: Berhasil Diubah (S15)" }
    BTN_LOGIN_AGAIN[Klik Tombol Masuk Sekarang]

    DEC_AUTH_CHOICE -->|Lupa Kata Sandi| DISP_FORGOT1
    DISP_FORGOT1 --> INP_FORGOT_ID
    INP_FORGOT_ID --> REQ_FORGOT
    REQ_FORGOT --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_FORGOT
    RES_FORGOT --> DEC_FORGOT_USER
    DEC_FORGOT_USER -->|Tidak Ditemukan| ERR_FORGOT_NOTFOUND
    ERR_FORGOT_NOTFOUND --> INP_FORGOT_ID
    DEC_FORGOT_USER -->|Ditemukan| SEND_OTP
    SEND_OTP --> DISP_FORGOT2
    DISP_FORGOT2 --> INP_OTP
    INP_OTP --> REQ_VERIFY_OTP
    REQ_VERIFY_OTP --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_VERIFY_OTP
    RES_VERIFY_OTP --> DEC_OTP_VALID
    DEC_OTP_VALID -->|Salah / Kedaluwarsa| ERR_OTP
    ERR_OTP --> INP_OTP
    DEC_OTP_VALID -->|Valid| DISP_FORGOT3
    DISP_FORGOT3 --> INP_NEW_PASS
    INP_NEW_PASS --> VAL_NEW_PASS
    VAL_NEW_PASS -->|Tidak Cocok| ERR_NEW_PASS
    ERR_NEW_PASS --> INP_NEW_PASS
    VAL_NEW_PASS -->|Cocok| REQ_RESET_PASS
    REQ_RESET_PASS --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_RESET_PASS
    RES_RESET_PASS --> DISP_FORGOT4
    DISP_FORGOT4 --> BTN_LOGIN_AGAIN
    BTN_LOGIN_AGAIN --> DISP_LOGIN

    %% ----------------------------------------------------
    %% 4. JALUR LOGIN MITRA (MODAL BOTTOM SHEET)
    %% ----------------------------------------------------
    DISP_LOGIN@{ shape: display, label: "Tampilkan Modal BottomSheetLogin (S5/S11/S16)" }
    INP_LOGIN[/"Masukkan Email / No WA & Kata Sandi"\]
    VAL_LOGIN{"Apakah Isian Formulir Lengkap?"}
    ERR_LOGIN_EMPTY[Tampilkan Peringatan Field Wajib Diisi]
    REQ_LOGIN[/Kirim Request POST /api/v1/login/]
    RES_LOGIN[/Terima Respons Login/]
    DEC_LOGIN_RES{"Kredensial Valid & Role Pelanggan?"}
    ERR_LOGIN_FAIL[Tampilkan Pesan Kredensial Salah]
    SAVE_LOGIN_SESSION[Simpan Token Sanctum & Profil ke SessionManager]
    TOAST_WELCOME[Tampilkan Toast Selamat Datang & Tutup Modal]

    DEC_AUTH_CHOICE -->|Masuk Akun| DISP_LOGIN
    DISP_LOGIN --> INP_LOGIN
    INP_LOGIN --> VAL_LOGIN
    VAL_LOGIN -->|Tidak Lengkap| ERR_LOGIN_EMPTY
    ERR_LOGIN_EMPTY --> INP_LOGIN
    VAL_LOGIN -->|Lengkap| REQ_LOGIN
    REQ_LOGIN --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_LOGIN
    RES_LOGIN --> DEC_LOGIN_RES
    DEC_LOGIN_RES -->|Salah / Tidak Sah| ERR_LOGIN_FAIL
    ERR_LOGIN_FAIL --> INP_LOGIN
    DEC_LOGIN_RES -->|Sah (Berhasil)| SAVE_LOGIN_SESSION
    SAVE_LOGIN_SESSION --> DB_SESSION_LOCAL
    SAVE_LOGIN_SESSION --> TOAST_WELCOME
    TOAST_WELCOME --> CONN_MAIN

    DEC_AUTH_CHOICE -->|Jelajah sebagai Tamu| CONN_MAIN

    %% ----------------------------------------------------
    %% 5. KONTAINER UTAMA (MAIN ACTIVITY - 3 TAB)
    %% ----------------------------------------------------
    DISP_MAIN@{ shape: display, label: "Buka Layar Utama MainActivity (3 Tab)" }
    DEC_NAV_MAIN{"Pilih Tab Navigasi atau Fitur?"}

    CONN_MAIN --> DISP_MAIN
    DISP_MAIN --> DEC_NAV_MAIN
    DEC_NAV_MAIN -->|Tab Beranda (Katalog)| CONN_BERANDA((B))
    DEC_NAV_MAIN -->|Tab Pesanan (Riwayat)| CONN_PESANAN((P))
    DEC_NAV_MAIN -->|Tab Akun (Profil Toko)| CONN_AKUN((U))
    DEC_NAV_MAIN -->|Tutup Aplikasi| FINISH([Selesai])

    %% ----------------------------------------------------
    %% 6. TAB BERANDA, KATALOG ROKOK, & DETAIL PRODUK
    %% ----------------------------------------------------
    DISP_BERANDA@{ shape: display, label: "Tampilkan Layar BerandaFragment (S2)" }
    REQ_KATALOG[/Kirim Request GET /api/v1/barang/]
    DB_BARANG_SERVER[(Database Barangs MySQL)]
    RES_KATALOG[/Terima Respons Katalog Rokok Kretek/]
    DEC_KATALOG_LOAD{"Apakah Katalog Berhasil Dimuat?"}
    ERR_KATALOG[Tampilkan Pesan Offline & Tombol SwipeRefresh]
    RENDER_KATALOG["Render Produk KK Merah 12, Sembada, Cethe"]
    DEC_BERANDA_ACT{"Pilih Interaksi pada Katalog?"}

    ADD_QUICK_SLOP[Tambah 1 Slop Minimal ke Keranjang]
    SAVE_CART_LOCAL[Simpan Varian & Qty ke CartManager]
    DB_CART_LOCAL[(SharedPreferences: CartManager)]
    UPDATE_CART_BAR[Perbarui Bar Ringkasan Total Slop & Harga di Beranda]

    DISP_DETAIL_PRODUK@{ shape: display, label: "Buka Layar DetailProdukActivity (S3)" }
    SHOW_SPECS[Tampilkan Tar, Nikotin, Batang, & Estimasi Stok]
    INP_SATUAN[/"Pilih Satuan Kargo: Slop atau Bal (Aturan B1)"\]
    INP_QTY[/"Atur Jumlah Kuantitas Pembelian (+/-)"\]
    VAL_MIN_ORDER{"Kuantitas Memenuhi Minimal Order Satuan (Aturan B3)?"}
    ERR_MIN_ORDER[Tampilkan Peringatan Batas Minimal Order]
    BTN_ADD_CART[Klik Tombol Tambah ke Keranjang]

    CONN_BERANDA --> DISP_BERANDA
    DISP_BERANDA --> REQ_KATALOG
    REQ_KATALOG --> DB_BARANG_SERVER
    DB_BARANG_SERVER --> RES_KATALOG
    RES_KATALOG --> DEC_KATALOG_LOAD
    DEC_KATALOG_LOAD -->|Gagal / Offline| ERR_KATALOG
    ERR_KATALOG --> REQ_KATALOG
    DEC_KATALOG_LOAD -->|Berhasil| RENDER_KATALOG

    RENDER_KATALOG --> DEC_BERANDA_ACT
    DEC_BERANDA_ACT -->|Tambah Slop Cepat| ADD_QUICK_SLOP
    ADD_QUICK_SLOP --> SAVE_CART_LOCAL

    DEC_BERANDA_ACT -->|Klik Kartu Produk| DISP_DETAIL_PRODUK
    DISP_DETAIL_PRODUK --> SHOW_SPECS
    SHOW_SPECS --> INP_SATUAN
    INP_SATUAN --> INP_QTY
    INP_QTY --> VAL_MIN_ORDER
    VAL_MIN_ORDER -->|Kurang| ERR_MIN_ORDER
    ERR_MIN_ORDER --> INP_QTY
    VAL_MIN_ORDER -->|Valid| BTN_ADD_CART
    BTN_ADD_CART --> SAVE_CART_LOCAL

    SAVE_CART_LOCAL --> DB_CART_LOCAL
    DB_CART_LOCAL --> UPDATE_CART_BAR
    UPDATE_CART_BAR --> DISP_BERANDA

    DEC_BERANDA_ACT -->|Klik Bar Keranjang| DISP_CART

    %% ----------------------------------------------------
    %% 7. KERANJANG BELANJA & ORDER LOCK VALIDATION
    %% ----------------------------------------------------
    DISP_CART@{ shape: display, label: "Buka Layar CartActivity (S4)" }
    READ_CART_ITEMS[/Baca Item dari CartManager/]
    DEC_CART_EMPTY{"Apakah Keranjang Memiliki Item?"}
    SHOW_EMPTY_CART[Tampilkan Tampilan Keranjang Kosong]
    BACK_TO_KATALOG[Klik Pilih Produk Rokok]
    SHOW_CART_ITEMS[Tampilkan Daftar Item, Qty, & Subtotal Belanja]
    DEC_CART_ACTION{"Pilih Aksi di Keranjang?"}
    MODIFY_CART_ITEM[Ubah Kuantitas / Hapus Baris Item]
    INP_CATATAN_KURIR[/"Ketik Catatan Khusus Pengantaran Kurir"\]
    CHECK_CART_LOGIN{"Cek Status Sesi Akun Pengguna?"}
    TRIGGER_CART_LOGIN[Buka Modal LoginBottomSheetFragment]
    REQ_STATUS_KUNCI[/Kirim Request GET /api/v1/pesanan/status-kunci/]
    DB_TRX_SERVER[(Database Transaksis MySQL)]
    RES_STATUS_KUNCI[/Terima Respons Status Kunci/]
    DEC_ORDER_LOCK{"Apakah Akun Terkena Order Lock (Aturan B4)?"}
    SHOW_ORDER_LOCK_BANNER[Tampilkan Banner Peringatan & Kunci Tombol Checkout]
    ENABLE_CHECKOUT_BTN[Aktifkan Tombol Lanjut ke Checkout]

    DISP_CART --> READ_CART_ITEMS
    READ_CART_ITEMS --> DEC_CART_EMPTY
    DEC_CART_EMPTY -->|Kosong| SHOW_EMPTY_CART
    SHOW_EMPTY_CART --> BACK_TO_KATALOG
    BACK_TO_KATALOG --> DISP_BERANDA
    DEC_CART_EMPTY -->|Ada Item| SHOW_CART_ITEMS
    SHOW_CART_ITEMS --> DEC_CART_ACTION

    DEC_CART_ACTION -->|Ubah Qty / Hapus Item| MODIFY_CART_ITEM
    MODIFY_CART_ITEM --> SAVE_CART_LOCAL
    DEC_CART_ACTION -->|Ketik Catatan Kurir| INP_CATATAN_KURIR
    INP_CATATAN_KURIR --> CHECK_CART_LOGIN
    DEC_CART_ACTION -->|Klik Lanjut Checkout| CHECK_CART_LOGIN

    CHECK_CART_LOGIN -->|Tamu / Belum Login| TRIGGER_CART_LOGIN
    TRIGGER_CART_LOGIN --> DISP_LOGIN
    CHECK_CART_LOGIN -->|Sudah Login| REQ_STATUS_KUNCI
    REQ_STATUS_KUNCI --> DB_TRX_SERVER
    DB_TRX_SERVER --> RES_STATUS_KUNCI
    RES_STATUS_KUNCI --> DEC_ORDER_LOCK
    DEC_ORDER_LOCK -->|Ya: Ada Pesanan Dikirim| SHOW_ORDER_LOCK_BANNER
    SHOW_ORDER_LOCK_BANNER --> DISP_CART
    DEC_ORDER_LOCK -->|Tidak: Siap Pesan Baru| ENABLE_CHECKOUT_BTN
    ENABLE_CHECKOUT_BTN --> DISP_CHECKOUT

    %% ----------------------------------------------------
    %% 8. CHECKOUT & PEMBUATAN PESANAN GROSIR
    %% ----------------------------------------------------
    DISP_CHECKOUT@{ shape: display, label: "Buka Layar CheckoutActivity (S9)" }
    LOAD_CHECKOUT_DATA[Muat Snapshot Profil Toko & Ringkasan Kargo]
    REQ_PENGATURAN_BANK[/Kirim Request GET /api/v1/pengaturan/]
    DB_CONFIG_SERVER[(Database Pengaturans MySQL)]
    RES_PENGATURAN_BANK[/Terima Info Rekening Bank Resmi Pabrik/]
    INP_METODE_BAYAR[/"Pilih Metode Pembayaran: COD atau Transfer Bank"\]
    DEC_METODE_BAYAR{"Metode Pembayaran yang Dipilih?"}
    SET_METODE_COD[Pilih COD Tunai saat Kurir Tiba di Toko]
    SET_METODE_TRANSFER[Tampilkan Rekening Bank Pabrik & Opsi Salin]
    BTN_KIRIM_PESANAN[Klik Tombol Kirim Pesanan]
    SHOW_CONFIRM_ORDER_DLG[Tampilkan Dialog Konfirmasi Pengiriman Pesanan]
    DEC_CONFIRM_ORDER{"Konfirmasi Kirim Pesanan?"}
    GEN_IDEMPOTENCY_KEY[[Subrutin: Buat UUID Idempotency-Key]]
    REQ_STORE_PESANAN[/Kirim Request POST /api/v1/pesanan/]

    BE_CHECK_LOCK{"Server: Verifikasi Ulang Order Lock?"}
    RES_409_CONFLICT[/Terima Respons 409 Conflict/]
    ERR_409_DLG[Tampilkan Alert Pesanan Masih Berjalan]
    BE_LOCK_STOCK[[Subrutin: Kunci Baris Barang lockForUpdate]]
    BE_CHECK_STOCK{"Server: Cek Kecukupan Agregasi Stok Gudang?"}
    RES_422_UNPROCESSABLE[/Terima Respons 422 Error/]
    ERR_422_DLG[Tampilkan Alert Stok Gudang Tidak Cukup]
    BE_DEDUCT_STOCK[Potong Stok Gudang Pabrik decrement]
    BE_INSERT_TRX[Simpan Header Transaksi Status Baru Masuk & Detail Item]
    BE_CACHE_IDEMPOTENCY[Simpan Kunci Idempotensi ke Cache 10 Menit]
    RES_201_CREATED[/Terima Respons 201 Pesanan Berhasil Dibuat/]

    CLEAR_LOCAL_CART[Kosongkan Keranjang Belanja CartManager]
    DISP_ORDER_SUCCESS@{ shape: display, label: "Buka Layar OrderSuccessActivity (S10)" }
    SHOW_SUCCESS_TRX_INFO[Tampilkan Kode Transaksi & Total Tagihan]
    DEC_ORDER_SUCCESS_ACT{"Pilih Tindak Lanjut Pesanan?"}
    OPEN_WA_CONFIRM[Buka WhatsApp Admin Pabrik dengan Template Chat Kode TRX]

    DISP_CHECKOUT --> LOAD_CHECKOUT_DATA
    LOAD_CHECKOUT_DATA --> REQ_PENGATURAN_BANK
    REQ_PENGATURAN_BANK --> DB_CONFIG_SERVER
    DB_CONFIG_SERVER --> RES_PENGATURAN_BANK
    RES_PENGATURAN_BANK --> INP_METODE_BAYAR

    INP_METODE_BAYAR --> DEC_METODE_BAYAR
    DEC_METODE_BAYAR -->|COD (Bayar di Tempat)| SET_METODE_COD
    DEC_METODE_BAYAR -->|Transfer Bank| SET_METODE_TRANSFER
    SET_METODE_COD --> BTN_KIRIM_PESANAN
    SET_METODE_TRANSFER --> BTN_KIRIM_PESANAN

    BTN_KIRIM_PESANAN --> SHOW_CONFIRM_ORDER_DLG
    SHOW_CONFIRM_ORDER_DLG --> DEC_CONFIRM_ORDER
    DEC_CONFIRM_ORDER -->|Batal| DISP_CHECKOUT
    DEC_CONFIRM_ORDER -->|Ya, Kirim| GEN_IDEMPOTENCY_KEY
    GEN_IDEMPOTENCY_KEY --> REQ_STORE_PESANAN

    REQ_STORE_PESANAN --> BE_CHECK_LOCK
    BE_CHECK_LOCK -->|Terkunci: Status 409| RES_409_CONFLICT
    RES_409_CONFLICT --> ERR_409_DLG
    ERR_409_DLG --> DISP_CART
    BE_CHECK_LOCK -->|Aman| BE_LOCK_STOCK
    BE_LOCK_STOCK --> BE_CHECK_STOCK
    BE_CHECK_STOCK -->|Stok Kurang: Status 422| RES_422_UNPROCESSABLE
    RES_422_UNPROCESSABLE --> ERR_422_DLG
    ERR_422_DLG --> DISP_CHECKOUT
    BE_CHECK_STOCK -->|Stok Cukup| BE_DEDUCT_STOCK
    BE_DEDUCT_STOCK --> BE_INSERT_TRX
    BE_INSERT_TRX --> DB_TRX_SERVER
    DB_TRX_SERVER --> BE_CACHE_IDEMPOTENCY
    BE_CACHE_IDEMPOTENCY --> RES_201_CREATED

    RES_201_CREATED --> CLEAR_LOCAL_CART
    CLEAR_LOCAL_CART --> DB_CART_LOCAL
    DB_CART_LOCAL --> DISP_ORDER_SUCCESS
    DISP_ORDER_SUCCESS --> SHOW_SUCCESS_TRX_INFO
    SHOW_SUCCESS_TRX_INFO --> DEC_ORDER_SUCCESS_ACT

    DEC_ORDER_SUCCESS_ACT -->|Hubungi WA Pabrik| OPEN_WA_CONFIRM
    OPEN_WA_CONFIRM --> CONN_PESANAN
    DEC_ORDER_SUCCESS_ACT -->|Lihat Status Pesanan| CONN_PESANAN

    %% ----------------------------------------------------
    %% 9. TAB PESANAN, TRACKING STEPPER, & KONFIRMASI TERIMA
    %% ----------------------------------------------------
    DISP_PESANAN_FRAGMENT@{ shape: display, label: "Tampilkan Tab PesananFragment" }
    DEC_PESANAN_AUTH{"Apakah Pengguna Sudah Login?"}
    SHOW_GUEST_PESANAN[Tampilkan Tampilan Tamu S36]
    BTN_GUEST_LOGIN[Klik Masuk Akun Mitra]
    INP_FILTER_PESANAN[/"Pilih Filter: Semua / Berjalan / Selesai / Dibatalkan"\]
    REQ_LIST_PESANAN[/Kirim Request GET /api/v1/pesanan?status=.../]
    RES_LIST_PESANAN[/Terima Daftar Riwayat Pesanan/]
    DEC_PESANAN_DATA{"Apakah Daftar Pesanan Kosong?"}
    SHOW_EMPTY_PESANAN[Tampilkan Tampilan Pesanan Kosong S35]
    BTN_PESAN_SEKARANG[Klik Pesan Sekarang]
    RENDER_PESANAN_CARDS[Tampilkan Kartu Pesanan Berdasarkan Filter]
    CLICK_PESANAN_CARD[Klik Kartu Pesanan untuk Melihat Detail]

    DISP_DETAIL_PESANAN@{ shape: display, label: "Buka Layar DetailPesananActivity" }
    REQ_DETAIL_PESANAN[/Kirim Request GET /api/v1/pesanan/{id}//]
    RES_DETAIL_PESANAN[/Terima Rincian Lengkap Pesanan/]
    RENDER_STEPPER_TRACKING["Render Stepper Pelacakan 4 Tahap (S25/S27/S31)"]
    DEC_PESANAN_STATUS{"Status Pesanan Saat Ini?"}

    SHOW_BTN_BATAL[Tampilkan Tombol Batalkan Pesanan]
    CLICK_BATAL_PESANAN[Klik Batalkan Pesanan]
    SHOW_CONFIRM_BATAL_DLG[Tampilkan Dialog Peringatan Batal S32]
    DEC_CONFIRM_BATAL{"Konfirmasi Batalkan Pesanan?"}
    REQ_BATAL_PESANAN[/Kirim Request POST /api/v1/pesanan/{id}/batal/]
    BE_RESTORE_STOCK[Server: Kembalikan Kuota Stok ke Gudang increment]
    BE_SET_CANCELED[Server: Ubah Status Menjadi Dibatalkan]
    RES_BATAL_PESANAN[/Terima Respons Batal Berhasil/]
    TOAST_BATAL_OK[Tampilkan Toast Pesanan Berhasil Dibatalkan]

    SHOW_INFO_DIPROSES[Tampilkan Status Gudang Sedang Menyiapkan Barang]

    SHOW_BTN_KONFIRMASI[Tampilkan Tombol Konfirmasi Penerimaan Barang]
    OPEN_KONFIRMASI_ACTIVITY[Buka KonfirmasiPenerimaanActivity S28]
    DISP_KONFIRMASI@{ shape: display, label: "Tampilkan Layar KonfirmasiPenerimaanActivity (S28)" }
    INP_FOTO_BUKTI[/"Ambil / Pilih Foto Fisik Penerimaan Muatan di Toko"\]
    INP_CATATAN_PENERIMA[/"Ketik Catatan Penerima (Opsional)"\]
    VAL_FOTO_BUKTI{"Apakah Foto Bukti Fisik Sudah Terpilih?"}
    ERR_FOTO_EMPTY[Tampilkan Peringatan Foto Bukti Wajib Diunggah]
    BTN_SUBMIT_BUKTI[Klik Kirim Bukti Penerimaan]
    SHOW_CONFIRM_TERIMA_DLG[Tampilkan Dialog Tanggung Jawab Penerimaan Fisik & COD]
    DEC_CONFIRM_TERIMA{"Konfirmasi Penyelesaian Pesanan?"}
    REQ_UPLOAD_BUKTI[/Kirim Request POST /api/v1/pesanan/{id}/konfirmasi-terima (Multipart)/]
    BE_SET_SELESAI[Server: Simpan Bukti, Set Status Selesai, Set Lunas, & Buka Order Lock]
    RES_UPLOAD_BUKTI[/Terima Respons Konfirmasi Sukses/]
    TOAST_KONFIRMASI_OK[Tampilkan Toast Penerimaan Barang Berhasil Diselesaikan]

    SHOW_SELESAI_ACTIONS[Tampilkan Foto Bukti Fisik, Tombol Faktur, & Pesan Lagi]
    DEC_SELESAI_ACT{"Pilih Aksi Pesanan Selesai?"}
    CONFIRM_REORDER_DLG[Tampilkan Dialog Tambah Muatan ke Keranjang S34]
    SHOW_BANNER_CANCELED[Tampilkan Banner Peringatan Pesanan Dibatalkan S33]

    CONN_PESANAN --> DISP_PESANAN_FRAGMENT
    DISP_PESANAN_FRAGMENT --> DEC_PESANAN_AUTH
    DEC_PESANAN_AUTH -->|Belum Login| SHOW_GUEST_PESANAN
    SHOW_GUEST_PESANAN --> BTN_GUEST_LOGIN
    BTN_GUEST_LOGIN --> DISP_LOGIN

    DEC_PESANAN_AUTH -->|Sudah Login| INP_FILTER_PESANAN
    INP_FILTER_PESANAN --> REQ_LIST_PESANAN
    REQ_LIST_PESANAN --> DB_TRX_SERVER
    DB_TRX_SERVER --> RES_LIST_PESANAN
    RES_LIST_PESANAN --> DEC_PESANAN_DATA
    DEC_PESANAN_DATA -->|Kosong| SHOW_EMPTY_PESANAN
    SHOW_EMPTY_PESANAN --> BTN_PESAN_SEKARANG
    BTN_PESAN_SEKARANG --> CONN_BERANDA

    DEC_PESANAN_DATA -->|Ada Data| RENDER_PESANAN_CARDS
    RENDER_PESANAN_CARDS --> CLICK_PESANAN_CARD
    CLICK_PESANAN_CARD --> DISP_DETAIL_PESANAN
    DISP_DETAIL_PESANAN --> REQ_DETAIL_PESANAN
    REQ_DETAIL_PESANAN --> DB_TRX_SERVER
    DB_TRX_SERVER --> RES_DETAIL_PESANAN
    RES_DETAIL_PESANAN --> RENDER_STEPPER_TRACKING
    RENDER_STEPPER_TRACKING --> DEC_PESANAN_STATUS

    DEC_PESANAN_STATUS -->|Baru Masuk| SHOW_BTN_BATAL
    SHOW_BTN_BATAL --> CLICK_BATAL_PESANAN
    CLICK_BATAL_PESANAN --> SHOW_CONFIRM_BATAL_DLG
    SHOW_CONFIRM_BATAL_DLG --> DEC_CONFIRM_BATAL
    DEC_CONFIRM_BATAL -->|Tidak| DISP_DETAIL_PESANAN
    DEC_CONFIRM_BATAL -->|Ya, Batalkan| REQ_BATAL_PESANAN
    REQ_BATAL_PESANAN --> BE_RESTORE_STOCK
    BE_RESTORE_STOCK --> BE_SET_CANCELED
    BE_SET_CANCELED --> DB_TRX_SERVER
    DB_TRX_SERVER --> RES_BATAL_PESANAN
    RES_BATAL_PESANAN --> TOAST_BATAL_OK
    TOAST_BATAL_OK --> DISP_PESANAN_FRAGMENT

    DEC_PESANAN_STATUS -->|Diproses| SHOW_INFO_DIPROSES
    SHOW_INFO_DIPROSES --> DISP_DETAIL_PESANAN

    DEC_PESANAN_STATUS -->|Dikirim| SHOW_BTN_KONFIRMASI
    SHOW_BTN_KONFIRMASI --> OPEN_KONFIRMASI_ACTIVITY
    OPEN_KONFIRMASI_ACTIVITY --> DISP_KONFIRMASI
    DISP_KONFIRMASI --> INP_FOTO_BUKTI
    INP_FOTO_BUKTI --> INP_CATATAN_PENERIMA
    INP_CATATAN_PENERIMA --> VAL_FOTO_BUKTI
    VAL_FOTO_BUKTI -->|Belum Ada| ERR_FOTO_EMPTY
    ERR_FOTO_EMPTY --> INP_FOTO_BUKTI
    VAL_FOTO_BUKTI -->|Sudah Ada| BTN_SUBMIT_BUKTI
    BTN_SUBMIT_BUKTI --> SHOW_CONFIRM_TERIMA_DLG
    SHOW_CONFIRM_TERIMA_DLG --> DEC_CONFIRM_TERIMA
    DEC_CONFIRM_TERIMA -->|Batal| DISP_KONFIRMASI
    DEC_CONFIRM_TERIMA -->|Ya, Konfirmasi| REQ_UPLOAD_BUKTI
    REQ_UPLOAD_BUKTI --> BE_SET_SELESAI
    BE_SET_SELESAI --> DB_TRX_SERVER
    DB_TRX_SERVER --> RES_UPLOAD_BUKTI
    RES_UPLOAD_BUKTI --> TOAST_KONFIRMASI_OK
    TOAST_KONFIRMASI_OK --> DISP_DETAIL_PESANAN

    DEC_PESANAN_STATUS -->|Selesai| SHOW_SELESAI_ACTIONS
    SHOW_SELESAI_ACTIONS --> DEC_SELESAI_ACT
    DEC_SELESAI_ACT -->|Pesan Lagi (Re-order)| CONFIRM_REORDER_DLG
    CONFIRM_REORDER_DLG --> DISP_CART
    DEC_SELESAI_ACT -->|Lihat Faktur & Nota| DISP_FAKTUR

    DEC_PESANAN_STATUS -->|Dibatalkan| SHOW_BANNER_CANCELED
    SHOW_BANNER_CANCELED --> DISP_PESANAN_FRAGMENT

    %% ----------------------------------------------------
    %% 10. FAKTUR RESMI & NOTA PENJUALAN
    %% ----------------------------------------------------
    DISP_FAKTUR@{ shape: display, label: "Buka Layar FakturActivity (S29/S30)" }
    REQ_FAKTUR_DATA[/Kirim Request GET /api/v1/pesanan/{id} & GET /api/v1/pengaturan/]
    RES_FAKTUR_DATA[/Terima Rincian Lengkap & Nomor Izin NPPBKC Pabrik/]
    GEN_QR_CODE[[Subrutin: Generate QR Code Dinamis ZXing No. Invoice]]
    DEC_FAKTUR_STATUS{"Status Pembayaran Pesanan?"}
    SET_STAMP_LUNAS[Tampilkan Stempel Badge Hijau LUNAS COD/Transfer]
    SET_STAMP_BELUM[Tampilkan Stempel Badge Kuning BELUM DIBAYAR]
    DOC_FAKTUR_VIEW@{ shape: doc, label: "Tampilkan Dokumen Faktur Resmi (Kop Pabrik, NPPBKC, Muatan, QR)" }
    DEC_FAKTUR_ACTION{"Pilih Aksi Pengelolaan Dokumen Faktur?"}

    CAPTURE_FAKTUR_BITMAP[Capture Layout Faktur Menjadi Objek Bitmap]
    CALL_PRINT_HELPER[[Subrutin: Inisialisasi Android PrintHelper]]
    INP_PILIH_PRINTER[/"Pilih Printer Thermal / Wireless pada PrintManager"\]
    EXEC_PRINT_NOTA[Eksekusi Percetakan Salinan Struk/Nota Fisik]

    GEN_PDF_DOC[[Subrutin: Render Canvas Layout ke PdfDocument]]
    WRITE_PDF_FILE[Tulis Berkas Faktur_TRX.pdf ke Documents Storage]
    DB_LOCAL_STORAGE[(Penyimpanan Dokumen Perangkat)]
    OPEN_SHARE_PDF[/Buka Dokumen PDF / Bagikan via Android FileProvider/]

    DISP_FAKTUR --> REQ_FAKTUR_DATA
    REQ_FAKTUR_DATA --> DB_TRX_SERVER
    DB_TRX_SERVER --> RES_FAKTUR_DATA
    RES_FAKTUR_DATA --> GEN_QR_CODE
    GEN_QR_CODE --> DEC_FAKTUR_STATUS
    DEC_FAKTUR_STATUS -->|Lunas| SET_STAMP_LUNAS
    DEC_FAKTUR_STATUS -->|Belum Lunas| SET_STAMP_BELUM
    SET_STAMP_LUNAS --> DOC_FAKTUR_VIEW
    SET_STAMP_BELUM --> DOC_FAKTUR_VIEW
    DOC_FAKTUR_VIEW --> DEC_FAKTUR_ACTION

    DEC_FAKTUR_ACTION -->|Cetak Salinan Nota| CAPTURE_FAKTUR_BITMAP
    CAPTURE_FAKTUR_BITMAP --> CALL_PRINT_HELPER
    CALL_PRINT_HELPER --> INP_PILIH_PRINTER
    INP_PILIH_PRINTER --> EXEC_PRINT_NOTA
    EXEC_PRINT_NOTA --> DISP_FAKTUR

    DEC_FAKTUR_ACTION -->|Simpan Dokumen PDF| GEN_PDF_DOC
    GEN_PDF_DOC --> WRITE_PDF_FILE
    WRITE_PDF_FILE --> DB_LOCAL_STORAGE
    DB_LOCAL_STORAGE --> OPEN_SHARE_PDF
    OPEN_SHARE_PDF --> DISP_FAKTUR

    DEC_FAKTUR_ACTION -->|Kembali ke Detail Pesanan| DISP_DETAIL_PESANAN

    %% ----------------------------------------------------
    %% 11. TAB AKUN, PROFIL TOKO, PENGATURAN, & LOGOUT
    %% ----------------------------------------------------
    DISP_AKUN_FRAGMENT@{ shape: display, label: "Tampilkan Tab AkunFragment (S17)" }
    DEC_AKUN_AUTH{"Apakah Pengguna Sudah Login?"}
    SHOW_GUEST_AKUN[Tampilkan Tampilan Tamu & Tombol Masuk/Daftar]
    BTN_AUTH_AKUN[Klik Masuk / Daftar Akun]
    SHOW_AKUN_MENU[Tampilkan Avatar, Nama Toko, Badge Mitra Resmi, & Menu Akun]
    DEC_AKUN_MENU{"Pilih Menu Akun Pengguna?"}

    INP_PICK_AVATAR[/"Pilih Foto Baru dari Galeri / Kamera"\]
    REQ_UPLOAD_AVATAR[/Kirim Request POST /api/v1/profile/foto (Multipart)/]
    RES_UPLOAD_AVATAR[/Terima Respons URL Avatar Baru/]
    SAVE_NEW_AVATAR_SESSION[Perbarui Data User di SessionManager]
    TOAST_AVATAR_OK[Tampilkan Toast Foto Profil Berhasil Diperbarui]

    DISP_DATA_TOKO@{ shape: display, label: "Buka Layar DataTokoActivity (S18/S19)" }
    INP_DATA_TOKO[/"Ubah Nama Toko, Pemilik, Email, WA, & Alamat Toko"\]
    VAL_DATA_TOKO{"Apakah Seluruh Isian Wajib Terisi?"}
    ERR_DATA_TOKO[Tampilkan Peringatan Field Wajib Diisi]
    REQ_UPDATE_PROFILE[/Kirim Request PUT /api/v1/profile/]
    RES_UPDATE_PROFILE[/Terima Respons Pembaruan Profil/]
    SAVE_UPDATED_PROFILE[Perbarui Profil User di SessionManager]
    TOAST_DATA_TOKO_OK[Tampilkan Toast Data Toko Berhasil Disimpan]

    DISP_UBAH_SANDI@{ shape: display, label: "Buka Layar UbahSandiActivity (S20)" }
    INP_UBAH_SANDI[/"Masukkan Sandi Baru Min 6 Karakter & Konfirmasi"\]
    VAL_UBAH_SANDI{"Sandi Minimal 6 Karakter & Konfirmasi Cocok?"}
    ERR_UBAH_SANDI[Tampilkan Peringatan Sandi Tidak Cocok]
    REQ_UPDATE_SANDI[/Kirim Request PUT /api/v1/profile (Password Baru)/]
    RES_UPDATE_SANDI[/Terima Respons Sandi Berhasil Diperbarui/]
    TOAST_SANDI_OK[Tampilkan Toast Sandi Berhasil Diperbarui]

    OPEN_WA_PABRIK[Buka Aplikasi WhatsApp ke Nomor Resmi Admin Pabrik]

    BTN_LOGOUT[Klik Tombol Keluar dari Akun]
    SHOW_CONFIRM_LOGOUT_DLG[Tampilkan Dialog Konfirmasi Keluar (S23)]
    DEC_CONFIRM_LOGOUT{"Konfirmasi Keluar dari Sesi Akun?"}
    REQ_LOGOUT[/Kirim Request POST /api/v1/logout/]
    BE_REVOKE_TOKEN[Server: Cabut & Hapus Token Sanctum Aktif]
    CLEAR_SESSION_LOCAL[Hapus Token & Profil User dari SessionManager]
    CLEAR_CART_LOCAL[Hapus Seluruh Item Keranjang dari CartManager]
    RESET_UI_TO_GUEST[Perbarui Tampilan Aplikasi Kembali ke Status Tamu]

    CONN_AKUN --> DISP_AKUN_FRAGMENT
    DISP_AKUN_FRAGMENT --> DEC_AKUN_AUTH
    DEC_AKUN_AUTH -->|Belum Login| SHOW_GUEST_AKUN
    SHOW_GUEST_AKUN --> BTN_AUTH_AKUN
    BTN_AUTH_AKUN --> DISP_LOGIN

    DEC_AKUN_AUTH -->|Sudah Login| SHOW_AKUN_MENU
    SHOW_AKUN_MENU --> DEC_AKUN_MENU

    DEC_AKUN_MENU -->|Ganti Foto Avatar| INP_PICK_AVATAR
    INP_PICK_AVATAR --> REQ_UPLOAD_AVATAR
    REQ_UPLOAD_AVATAR --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_UPLOAD_AVATAR
    RES_UPLOAD_AVATAR --> SAVE_NEW_AVATAR_SESSION
    SAVE_NEW_AVATAR_SESSION --> DB_SESSION_LOCAL
    DB_SESSION_LOCAL --> TOAST_AVATAR_OK
    TOAST_AVATAR_OK --> DISP_AKUN_FRAGMENT

    DEC_AKUN_MENU -->|Menu Data & Alamat Toko| DISP_DATA_TOKO
    DISP_DATA_TOKO --> INP_DATA_TOKO
    INP_DATA_TOKO --> VAL_DATA_TOKO
    VAL_DATA_TOKO -->|Tidak Lengkap| ERR_DATA_TOKO
    ERR_DATA_TOKO --> INP_DATA_TOKO
    VAL_DATA_TOKO -->|Lengkap| REQ_UPDATE_PROFILE
    REQ_UPDATE_PROFILE --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_UPDATE_PROFILE
    RES_UPDATE_PROFILE --> SAVE_UPDATED_PROFILE
    SAVE_UPDATED_PROFILE --> DB_SESSION_LOCAL
    DB_SESSION_LOCAL --> TOAST_DATA_TOKO_OK
    TOAST_DATA_TOKO_OK --> DISP_AKUN_FRAGMENT

    DEC_AKUN_MENU -->|Menu Ubah Kata Sandi| DISP_UBAH_SANDI
    DISP_UBAH_SANDI --> INP_UBAH_SANDI
    INP_UBAH_SANDI --> VAL_UBAH_SANDI
    VAL_UBAH_SANDI -->|Tidak Cocok| ERR_UBAH_SANDI
    ERR_UBAH_SANDI --> INP_UBAH_SANDI
    VAL_UBAH_SANDI -->|Cocok| REQ_UPDATE_SANDI
    REQ_UPDATE_SANDI --> DB_USERS_SERVER
    DB_USERS_SERVER --> RES_UPDATE_SANDI
    RES_UPDATE_SANDI --> TOAST_SANDI_OK
    TOAST_SANDI_OK --> DISP_AKUN_FRAGMENT

    DEC_AKUN_MENU -->|Hubungi CS Pabrik| OPEN_WA_PABRIK
    OPEN_WA_PABRIK --> DISP_AKUN_FRAGMENT

    DEC_AKUN_MENU -->|Keluar Akun / Logout| BTN_LOGOUT
    BTN_LOGOUT --> SHOW_CONFIRM_LOGOUT_DLG
    SHOW_CONFIRM_LOGOUT_DLG --> DEC_CONFIRM_LOGOUT
    DEC_CONFIRM_LOGOUT -->|Batal| DISP_AKUN_FRAGMENT
    DEC_CONFIRM_LOGOUT -->|Ya, Keluar| REQ_LOGOUT
    REQ_LOGOUT --> BE_REVOKE_TOKEN
    BE_REVOKE_TOKEN --> CLEAR_SESSION_LOCAL
    CLEAR_SESSION_LOCAL --> CLEAR_CART_LOCAL
    CLEAR_CART_LOCAL --> DB_SESSION_LOCAL
    DB_SESSION_LOCAL --> RESET_UI_TO_GUEST
    RESET_UI_TO_GUEST --> FINISH

    %% ----------------------------------------------------
    %% STYLING KELAS (CLASS DEFINITIONS SESUAI STANDAR)
    %% ----------------------------------------------------
    class START,FINISH terminator;
    class CHECK_AUTO_LOGIN,DEC_AUTH_CHOICE,VAL_REG,DEC_REG_RES,DEC_FORGOT_USER,DEC_OTP_VALID,VAL_NEW_PASS,VAL_LOGIN,DEC_LOGIN_RES,DEC_NAV_MAIN,DEC_KATALOG_LOAD,DEC_BERANDA_ACT,VAL_MIN_ORDER,DEC_CART_EMPTY,DEC_CART_ACTION,CHECK_CART_LOGIN,DEC_ORDER_LOCK,DEC_METODE_BAYAR,DEC_CONFIRM_ORDER,BE_CHECK_LOCK,BE_CHECK_STOCK,DEC_ORDER_SUCCESS_ACT,DEC_PESANAN_AUTH,DEC_PESANAN_DATA,DEC_PESANAN_STATUS,DEC_CONFIRM_BATAL,VAL_FOTO_BUKTI,DEC_CONFIRM_TERIMA,DEC_SELESAI_ACT,DEC_FAKTUR_STATUS,DEC_FAKTUR_ACTION,DEC_AKUN_AUTH,DEC_AKUN_MENU,VAL_DATA_TOKO,VAL_UBAH_SANDI,DEC_CONFIRM_LOGOUT decision;
    class REQ_REG,RES_REG,REQ_FORGOT,RES_FORGOT,REQ_VERIFY_OTP,RES_VERIFY_OTP,REQ_RESET_PASS,RES_RESET_PASS,REQ_LOGIN,RES_LOGIN,REQ_KATALOG,RES_KATALOG,READ_CART_ITEMS,REQ_STATUS_KUNCI,RES_STATUS_KUNCI,REQ_PENGATURAN_BANK,RES_PENGATURAN_BANK,REQ_STORE_PESANAN,RES_409_CONFLICT,RES_422_UNPROCESSABLE,RES_201_CREATED,REQ_LIST_PESANAN,RES_LIST_PESANAN,REQ_DETAIL_PESANAN,RES_DETAIL_PESANAN,REQ_BATAL_PESANAN,RES_BATAL_PESANAN,REQ_UPLOAD_BUKTI,RES_UPLOAD_BUKTI,REQ_FAKTUR_DATA,RES_FAKTUR_DATA,OPEN_SHARE_PDF,REQ_UPLOAD_AVATAR,RES_UPLOAD_AVATAR,REQ_UPDATE_PROFILE,RES_UPDATE_PROFILE,REQ_UPDATE_SANDI,RES_UPDATE_SANDI,REQ_LOGOUT io;
    class DB_SESSION_LOCAL,DB_USERS_SERVER,DB_BARANG_SERVER,DB_CART_LOCAL,DB_TRX_SERVER,DB_CONFIG_SERVER,DB_LOCAL_STORAGE database;
    class GEN_IDEMPOTENCY_KEY,BE_LOCK_STOCK,GEN_QR_CODE,CALL_PRINT_HELPER,GEN_PDF_DOC process;
```

---

## 4. MATRIKS PEMETAAN KODE SUMBER & RELASI DESAIN

| No | Modul / Komponen | File Path | Fungsi / Metode Utama | Mockup UI Terkait |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Splash Screen** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/splash/SplashActivity.kt` | `onCreate()` | S1. Splash |
| 2 | **Layar Kontainer 3 Tab**| `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/main/MainActivity.kt` | `setupNavigation()`, `handleIncomingIntent()` | Beranda, Pesanan, Akun |
| 3 | **Pendaftaran Mitra Baru**| `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/auth/RegisterActivity.kt` | `attemptRegister()`, `handleBackendError()` | S6 & S7. Daftar Mitra |
| 4 | **Pendaftaran Berhasil** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/auth/RegisterSuccessActivity.kt` | `onCreate()` | S8. Akun Disetujui |
| 5 | **Lupa Sandi 4 Tahap** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/auth/ForgotPasswordActivity.kt` | `setupStep1()`, `setupStep2()`, `setupStep3()`, `setupStep4()` | S12, S13, S14, S15 |
| 6 | **Modal Login Dialog** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/auth/LoginBottomSheetFragment.kt` | `attemptLogin()`, `setupPasswordToggle()` | S5, S11, S16. Masuk |
| 7 | **Katalog & Summary Bar** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/beranda/BerandaFragment.kt` | `loadCatalog()`, `updateCartSummaryBar()` | S2. Beranda |
| 8 | **Adapter List Produk** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/beranda/BarangAdapter.kt` | `bind()`, `submitList()` | Kartu Produk Rokok Kretek |
| 9 | **Detail Produk & Satuan**| `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/detail/DetailProdukActivity.kt` | `populateData()`, `selectUnit()`, `updateCounterAndSubtotal()` | S3. Detail Produk |
| 10 | **Keranjang & Order Lock**| `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/cart/CartActivity.kt` | `refreshCartView()`, `checkUserSessionAndOrderLock()` | S4. Keranjang |
| 11 | **Local Cart Storage** | `mobile_app/app/src/main/java/com/keretakencana/mitra/data/local/CartManager.kt` | `addItem()`, `updateQty()`, `removeItem()`, `clearCart()` | Multi-satuan Bal/Slop (B1) |
| 12 | **Checkout Pemesanan** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/cart/CheckoutActivity.kt` | `populateCargoItems()`, `confirmAndSendOrder()`, `executeOrderCreation()` | S9. Pengiriman |
| 13 | **Pesanan Sukses & WA** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/cart/OrderSuccessActivity.kt` | `fetchPengaturanPabrik()`, `setupListeners()` | S10. Pesanan Berhasil |
| 14 | **Riwayat & Filter Order**| `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/pesanan/PesananFragment.kt` | `setFilter()`, `checkSessionAndLoadOrders()`, `eksekusiBatal()` | S24, S26, S35, S36 |
| 15 | **Stepper Tracking Order**| `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/pesanan/DetailPesananActivity.kt` | `loadPesananDetail()`, `renderPesanan()`, `setupStepper()` | S25, S27, S31, S33, S34 |
| 16 | **Konfirmasi Penerimaan** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/pesanan/KonfirmasiPenerimaanActivity.kt`| `validateAndSubmit()`, `uploadBukti()` | S28. Konfirmasi Penerimaan |
| 17 | **Faktur, Nota, & PDF** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/pesanan/FakturActivity.kt` | `renderFaktur()`, `generateAndShowQr()`, `cetakSalinanNota()`, `simpanFakturSebagaiPdf()` | S29 & S30. Faktur |
| 18 | **Profil Toko & Logout** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/akun/AkunFragment.kt` | `updateProfileView()`, `uploadAvatar()`, `showDialogKeluar()`, `eksekusiLogout()` | S17. Akun, S23. Dialog Keluar |
| 19 | **Ubah Data Toko** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/akun/DataTokoActivity.kt` | `populateExistingData()`, `setupListeners()` | S18 & S19. Data Toko |
| 20 | **Ubah Kata Sandi** | `mobile_app/app/src/main/java/com/keretakencana/mitra/ui/akun/UbahSandiActivity.kt` | `setupListeners()` | S20. Ubah Kata Sandi |
| 21 | **API Backend Auth** | `app/Http/Controllers/Api/AuthApiController.php` | `register()`, `login()`, `logout()`, `profile()`, `updateProfile()`, `forgotPassword()`, `resetPassword()` | API Autentikasi Publik & Sanctum |
| 22 | **API Backend Pesanan** | `app/Http/Controllers/Api/PesananApiController.php` | `statusKunci()`, `index()`, `show()`, `store()`, `batalkan()`, `konfirmasiTerima()` | API Transaksi & Agregasi Stok |
| 23 | **API Backend Config** | `app/Http/Controllers/Api/PengaturanApiController.php` | `index()` | API Konfigurasi Rekening & NPPBKC |
