<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PR. KERETA KENCANA') - Pabrik Sigaret Kretek Blitar</title>
    
    <!-- Favicon Resmi PR. KERETA KENCANA -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo-resmi.png') }}?v=kencana3">
    <link rel="shortcut icon" href="{{ asset('assets/img/logo-resmi.png') }}?v=kencana3">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-resmi.png') }}?v=kencana3">
    
    <!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tema CSS Asli PR. KERETA KENCANA -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
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
                <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" class="logo-img-brand">
                <div class="logo-text">
                    <div class="logo-title">KERETA KENCANA</div>
                    <div class="logo-sub">PABRIK SIGARET KRETEK BLITAR</div>
                </div>
            </a>

            <nav class="main-nav" id="mainNav">
                <a href="{{ route('beranda') }}" class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('profil') }}" class="nav-link {{ request()->routeIs('profil') ? 'active' : '' }}">Profil Pabrik</a>
                <a href="{{ route('katalog.index') }}" class="nav-link {{ request()->routeIs('katalog.*') || request()->routeIs('produk.*') ? 'active' : '' }}">Katalog Rokok</a>
                <a href="{{ route('pesanan.form') }}" class="nav-link {{ request()->routeIs('pesanan.*') ? 'active' : '' }}">Order</a>
                <a href="{{ route('kontak') }}" class="nav-link {{ request()->routeIs('kontak') ? 'active' : '' }}">Kontak & Lokasi</a>
                
                <div class="nav-actions">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-gold">
                        <i class="fa-solid fa-shield-halved"></i> Portal Staf
                    </a>
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
                        <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" class="logo-img-brand">
                        <div class="logo-text">
                            <div class="logo-title">PR. KERETA KENCANA</div>
                            <div class="logo-sub">PABRIK SIGARET KRETEK BLITAR</div>
                        </div>
                    </div>
                    <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.7; margin-bottom: 20px;">
                        Pabrik rokok resmi berizin cukai di Blitar, Jawa Timur. Menghadirkan kretek berkualitas tinggi dengan perpaduan tembakau pegunungan pilihan dan cengkeh nusantara, melayani mitra toko, warung, hingga distributor bal partai besar.
                    </p>
                    <div class="legal-badges-grid" style="margin-top: 0;">
                        <div class="badge-card">
                            <div class="badge-icon"><i class="fa-solid fa-stamp"></i></div>
                            <div>
                                <div class="badge-title">NPPBKC Resmi</div>
                                <div class="badge-sub">0821.1.2.XXXXX (Bea Cukai)</div>
                            </div>
                        </div>
                        <div class="badge-card">
                            <div class="badge-icon"><i class="fa-solid fa-file-contract"></i></div>
                            <div>
                                <div class="badge-title">NIB OSS RI</div>
                                <div class="badge-sub">9120001234567 Terbit</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Navigasi Cepat -->
                <div>
                    <div class="eyebrow" style="margin-bottom: 14px;"><i class="fa-solid fa-compass"></i> NAVIGASI SITUS</div>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.92rem;">
                        <li><a href="{{ route('beranda') }}" style="color: var(--text-light); display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: var(--gold);"></i> Beranda Utama</a></li>
                        <li><a href="{{ route('profil') }}" style="color: var(--text-light); display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: var(--gold);"></i> Profil & Legalitas Pabrik</a></li>
                        <li><a href="{{ route('katalog.index') }}" style="color: var(--text-light); display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: var(--gold);"></i> Katalog Produk Rokok</a></li>
                        <li><a href="{{ route('pesanan.form') }}" style="color: var(--text-light); display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: var(--gold);"></i> Formulir Order (Slop / Bal)</a></li>
                        <li><a href="{{ route('kontak') }}" style="color: var(--text-light); display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-chevron-right" style="font-size: 10px; color: var(--gold);"></i> Kontak & Titik Lokasi Pabrik</a></li>
                        <li><a href="{{ route('login') }}" style="color: var(--gold); display: inline-flex; align-items: center; gap: 8px; font-weight: 600;"><i class="fa-solid fa-shield-halved" style="font-size: 11px;"></i> Portal Staf Internal</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kantor & Jam Kerja Operasional -->
                <div>
                    <div class="eyebrow" style="margin-bottom: 14px;"><i class="fa-solid fa-building"></i> KANTOR & OPERASIONAL</div>
                    <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius); padding: 18px; margin-bottom: 14px;">
                        <p style="color: #ffffff; font-size: 0.9rem; font-weight: 600; margin-bottom: 6px;">
                            <i class="fa-solid fa-location-dot" style="color: var(--gold); margin-right: 4px;"></i> PR. KERETA KENCANA
                        </p>
                        <p style="color: var(--text-muted); font-size: 0.83rem; line-height: 1.6; margin-bottom: 12px;">
                            Dusun Subontoro, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur 66153
                        </p>
                        <div style="border-top: 1px dashed var(--charcoal-border); padding-top: 10px; font-size: 0.82rem; color: var(--text-light);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span class="text-muted">Senin - Sabtu:</span>
                                <strong>08:00 - 16:00 WIB</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span class="text-muted">Minggu / Libur:</span>
                                <span style="color: var(--gold);">Libur Produksi</span>
                            </div>
                        </div>
                    </div>

                    <a href="https://maps.app.goo.gl/3fxK2wXZoiRLs2sg8" target="_blank" class="btn btn-sm btn-outline-gold btn-block">
                        <i class="fa-solid fa-diamond-turn-right"></i> Buka Titik Google Maps
                    </a>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} PR. KERETA KENCANA Ponggok, Kabupaten Blitar. Seluruh hak cipta dilindungi undang-undang.</p>
                <div style="margin-top: 6px; font-size: 0.75rem; color: #64748b;">
                    Ade Aldienta Web Prototype
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
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
