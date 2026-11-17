<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PR. KERETA KENCANA') - Pabrik Sigaret Kretek Blitar</title>
    
    <!-- Favicon Resmi PR. KERETA KENCANA (Tanpa Background) -->
    <link rel="icon" type="image/png" sizes="256x256" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
    
    <!-- Google Fonts: Newsreader (Editorial), Source Sans 3 (Body), IBM Plex Mono (Document & Numbers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;0,6..72,700;1,6..72,500&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tema CSS Asli PR. KERETA KENCANA -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}">
    @stack('styles')
</head>
<body>

    <!-- 1. Age Gate 21+ Verification Modal (Sesuai Regulasi Resmi PP 28/2024) -->
    <div id="ageGateOverlay" class="age-gate-overlay" style="display: none;">
        <div class="age-gate-modal">
            <div class="age-badge">21+</div>
            <h2 class="age-gate-title">PERINGATAN REGULASI RESMI</h2>
            <p class="age-gate-text">
                Website ini merupakan portal operasional dan katalog B2B <strong>PR. KERETA KENCANA</strong>. Berdasarkan regulasi Pemerintah Republik Indonesia, informasi produk hasil tembakau hanya diperuntukkan bagi distributor dan pihak yang telah berusia <strong>21 tahun ke atas</strong>.
            </p>
            <div class="age-gate-actions">
                <button type="button" id="btnAcceptAge" class="btn btn-gold btn-block">
                    <i class="fa-solid fa-check"></i> Ya, Saya Berusia 21 Tahun ke Atas
                </button>
                <button type="button" id="btnRejectAge" class="btn btn-outline btn-block">
                    Keluar dari Situs
                </button>
            </div>
            <div style="margin-top: 14px; font-size: 0.72rem; color: #94a3b8;">
                MEROKOK MEMBUNUHMU &bull; KPPBC TIPE MADYA PABEAN C BLITAR
            </div>
        </div>
    </div>

    <!-- 2. Header & Navigasi Sistem -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('beranda') }}" class="logo-brand">
                <img src="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}" alt="PR. Kereta Kencana" class="logo-img-brand">
                <div class="logo-text">
                    <div class="logo-title">KERETA KENCANA</div>
                    <div class="logo-sub">PABRIK SIGARET KRETEK BLITAR</div>
                </div>
            </a>

            <nav class="main-nav" id="mainNav">
                <a href="{{ route('beranda') }}" class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('profil') }}" class="nav-link {{ request()->routeIs('profil') ? 'active' : '' }}">Profil Pabrik</a>
                <a href="{{ route('katalog.index') }}" class="nav-link {{ request()->routeIs('katalog.*') || request()->routeIs('produk.*') ? 'active' : '' }}">Katalog Rokok</a>
                <a href="{{ route('pesanan.form') }}" class="nav-link {{ request()->routeIs('pesanan.form') ? 'active' : '' }}">Order</a>
                @auth
                    @if(Auth::user()->isPelanggan())
                        <a href="{{ route('pesanan.saya') }}" class="nav-link {{ request()->routeIs('pesanan.saya') ? 'active' : '' }}">
                            <i class="fa-solid fa-box-open"></i> Pesanan Saya
                        </a>
                    @endif
                @endauth
                
                <div class="nav-actions" style="display: flex; align-items: center; gap: 8px;">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-sm btn-gold" style="font-size: 12px; padding: 6px 14px;">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk
                        </a>
                    @else
                        @if(Auth::user()->isPelanggan())
                            <span style="font-size: 12px; color: #f4f4f5; display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.2); padding: 5px 10px; border-radius: 6px;">
                                <i class="fa-solid fa-store" style="color: #d4d4d8;"></i> {{ Str::limit(Auth::user()->nama_toko ?: Auth::user()->name, 16) }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 12px; padding: 6px 10px;" title="Keluar">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-gold" style="font-size: 12px; padding: 6px 12px;">
                                <i class="fa-solid fa-shield-halved"></i> Panel Staf
                            </a>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 12px; padding: 6px 10px;" title="Keluar">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        @endif
                    @endguest
                </div>
            </nav>

            <button class="mobile-toggle" id="mobileToggle" aria-label="Menu Navigasi Mobile">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Content Slot -->
    <main>
        @if(session('success'))
            <div class="container" style="margin-top: 20px;">
                <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 14px 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container" style="margin-top: 20px;">
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; padding: 14px 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- 3. Footer & Lokasi Maps -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-top-grid">
                <!-- Kolom 1: Profil & Legalitas Pabrik -->
                <div>
                    <div class="logo-brand" style="margin-bottom: 16px;">
                        <img src="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}" alt="PR. Kereta Kencana" class="logo-img-brand">
                        <div class="logo-text">
                            <div class="logo-title">PR. KERETA KENCANA</div>
                            <div class="logo-sub">PABRIK SIGARET KRETEK BLITAR</div>
                        </div>
                    </div>
                    <p style="color: #a3a3a3; font-size: 0.92rem; line-height: 1.65; margin-bottom: 20px;">
                        Pabrik Sigaret Kretek Tangan (SKT) berizin cukai di Ponggok, Kabupaten Blitar. Memproduksi SEMBADA 12, KERETA KENCANA 12, dan SEMBADA Cethe 12 untuk toko ritel hingga agen distributor.
                    </p>
                    <div class="legal-badges-grid" style="margin-top: 0;">
                        <div class="badge-card">
                            <div class="badge-icon"><i class="fa-solid fa-stamp"></i></div>
                            <div>
                                <div class="badge-title">NPPBKC Resmi</div>
                                <div class="badge-sub" style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.01em;">750******-070700-221221*******</div>
                            </div>
                        </div>
                        <div class="badge-card">
                            <div class="badge-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <div class="badge-title">Pengawasan Cukai</div>
                                <div class="badge-sub">KPPBC TMP C Blitar</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Navigasi Cepat -->
                <div>
                    <div style="font-family: var(--font-mono); font-size: 0.76rem; color: #a3a3a3; margin-bottom: 14px; letter-spacing: 0.04em;">Navigasi Situs</div>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; font-size: 0.94rem;">
                        <li><a href="{{ route('beranda') }}" class="footer-nav-link"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: #737373;"></i> Beranda</a></li>
                        <li><a href="{{ route('profil') }}" class="footer-nav-link"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: #737373;"></i> Profil &amp; Legalitas Pabrik</a></li>
                        <li><a href="{{ route('katalog.index') }}" class="footer-nav-link"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: #737373;"></i> Katalog Produk Rokok</a></li>
                        <li><a href="{{ route('pesanan.form') }}" class="footer-nav-link"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: #737373;"></i> Order Online (Satuan Slop)</a></li>
                        @auth
                            @if(Auth::user()->isPelanggan())
                                <li><a href="{{ route('pesanan.saya') }}" class="footer-nav-link" style="color: #ffffff !important; font-weight: 600;"><i class="fa-solid fa-box-open" style="font-size: 11px; color: #a3a3a3;"></i> Pesanan Saya</a></li>
                            @else
                                <li><a href="{{ route('admin.dashboard') }}" class="footer-nav-link" style="color: #ffffff !important; font-weight: 600;"><i class="fa-solid fa-shield-halved" style="font-size: 11px; color: #a3a3a3;"></i> Dashboard Panel Staf</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="footer-nav-link" style="color: #ffffff !important; font-weight: 600;"><i class="fa-solid fa-shield-halved" style="font-size: 11px; color: #a3a3a3;"></i> Portal Masuk Mitra &amp; Staf</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Kolom 3: Kantor & Layanan Kemitraan -->
                <div>
                    <div style="font-family: var(--font-mono); font-size: 0.76rem; color: #a3a3a3; margin-bottom: 14px; letter-spacing: 0.04em;">Kantor &amp; Kemitraan</div>
                    <div style="background: #141414; border: 1px solid #262626; padding: 18px; margin-bottom: 14px;">
                        <p style="color: #ffffff; font-size: 0.92rem; font-weight: 600; margin-bottom: 6px;">
                            PR. KERETA KENCANA
                        </p>
                        <p style="color: #a3a3a3; font-size: 0.85rem; line-height: 1.55; margin-bottom: 12px;">
                            Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur 66153
                        </p>
                        <div style="border-top: 1px solid #262626; padding-top: 12px;">
                            <p style="color: #ffffff; font-size: 0.88rem; font-weight: 600; line-height: 1.45; margin-bottom: 6px;">
                                Tertarik Menjadi Agen atau Mitra Pasokan Toko?
                            </p>
                            <p style="color: #a3a3a3; font-size: 0.83rem; line-height: 1.58; margin: 0;">
                                Hubungi manajemen PR. Kereta Kencana di Ponggok, Kabupaten Blitar untuk pembelian skala Bal, keagenan daerah, maupun konsultasi pemesanan.
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="https://wa.me/{{ config('pabrik.wa_pabrik', '6285790627104') }}?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin bertanya mengenai kemitraan & pemesanan rokok.') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-gold btn-block">
                            <i class="fa-brands fa-whatsapp"></i> Hubungi Admin via WhatsApp
                        </a>
                        <a href="https://maps.app.goo.gl/3fxK2wXZoiRLs2sg8" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-gold btn-block">
                            <i class="fa-solid fa-diamond-turn-right"></i> Buka Titik Google Maps
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} PR. KERETA KENCANA Ponggok, Kabupaten Blitar. Seluruh hak cipta dilindungi undang-undang.</p>
                <div style="margin-top: 6px; font-size: 0.75rem; color: #737373;">
                    Portal Kemitraan B2B &bull; Pengawasan KPPBC Tipe Madya Pabean C Blitar
                </div>
            </div>
        </div>
    </footer>

    <!-- 4. Peringatan Regulasi Pemerintah -->
    <div class="health-warning-bar">
        <span class="warning-pill">21+ KHUSUS MITRA USAHA</span>
        <span>PERINGATAN: MEROKOK DAPAT MENYEBABKAN KANKER, SERANGAN JANTUNG, IMPOTENSI DAN GANGGUAN KEHAMILAN DAN JANIN.</span>
    </div>

    <!-- Script Utama -->
    <script src="{{ asset('assets/js/main.js') }}?v={{ filemtime(public_path('assets/js/main.js')) }}"></script>
    @stack('scripts')
</body>
</html>
