@extends('layouts.app')

@section('title', 'PR. KERETA KENCANA - Pabrik Sigaret Kretek Blitar')

@section('content')
<!-- Hero Section -->
<section class="hero-section" id="heroSection" style="position: relative; overflow: hidden; padding: 60px 0; min-height: 80vh; display: flex; align-items: center; background-color: #0d1012;">
    <!-- Large Background Slider (Auto-Slide / Ken-Burns Fade) -->
    @php
        $heroSlides = [
            [
                'img' => asset('assets/img/produk/trio-produk-kereta-kencana.jpg'),
                'title' => '3 Varian Sigaret Kretek Tangan PR. Kereta Kencana',
            ],
            [
                'img' => asset('assets/img/produk/gallery/kereta-kencana-1.jpg'),
                'title' => 'KERETA KENCANA 12 SKT',
            ],
            [
                'img' => asset('assets/img/produk/gallery/sembada-1.jpg'),
                'title' => 'SEMBADA 12 SKT',
            ],
            [
                'img' => asset('assets/img/produk/gallery/sembada-cethe-1.jpg'),
                'title' => 'SEMBADA CETHE 12 SKT',
            ],
            // Catatan: Tambahkan foto pabrik langsung atau varian rokok baru di sini nantinya:
            // [
            //     'img' => asset('images/pabrik-1.jpg'),
            //     'title' => 'PABRIK PONGGOK BLITAR',
            // ],
        ];
    @endphp

    <div class="hero-slider-container" id="heroBgSlider">
        @foreach($heroSlides as $i => $slide)
            <div class="hero-slide {{ $i === 0 ? 'active' : '' }}" style="background-image: url('{{ $slide['img'] }}');">
                <div class="hero-slide-overlay"></div>
            </div>
        @endforeach
    </div>

    <!-- Navigation Arrows -->
    <button type="button" class="hero-slider-nav prev" onclick="prevHeroSlide()" aria-label="Slide Sebelumnya">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button type="button" class="hero-slider-nav next" onclick="nextHeroSlide()" aria-label="Slide Selanjutnya">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <!-- Slide Indicator Dots -->
    <div class="hero-slider-dots">
        @foreach($heroSlides as $i => $slide)
            <button type="button" class="hero-slider-dot {{ $i === 0 ? 'active' : '' }}" onclick="goToHeroSlide({{ $i }})" aria-label="Slide {{ $i + 1 }}"></button>
        @endforeach
    </div>

    <div class="container" style="position: relative; z-index: 2;">
        <div class="hero-content-clean" style="max-width: 840px; padding: 50px 0;">
            <!-- Tag Atas -->
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(13, 16, 18, 0.78); border: 1px solid rgba(197, 160, 89, 0.55); padding: 7px 18px; border-radius: 30px; margin-bottom: 22px; backdrop-filter: blur(8px); box-shadow: 0 4px 16px rgba(0,0,0,0.5);">
                <i class="fa-solid fa-stamp" style="color: var(--gold); font-size: 13px;"></i>
                <span style="color: var(--gold); font-size: 12.5px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">PABRIK RESMI BERIZIN CUKAI &bull; PONGGOK, KAB. BLITAR</span>
            </div>

            <!-- Judul Utama -->
            <h1 class="hero-slogan" style="margin-top: 0; font-size: 3.25rem; line-height: 1.18; font-weight: 900; color: #ffffff; text-shadow: 0 4px 18px rgba(0,0,0,0.9); letter-spacing: 0.5px; margin-bottom: 22px;">
                DEDIKASI MUTU KRETEK BLITAR UNTUK MITRA DISTRIBUSI
            </h1>

            <!-- Deskripsi -->
            <p class="hero-subheadline" style="font-size: 1.15rem; line-height: 1.85; color: #f8fafc; margin-bottom: 34px; max-width: 780px; text-shadow: 0 2px 14px rgba(0,0,0,0.95); font-weight: 400;">
                PR. KERETA KENCANA memproduksi 3 varian Sigaret Kretek Tangan (SKT) 12 batang berpita cukai resmi negara: SEMBADA, KERETA KENCANA, dan SEMBADA CETHE dengan perpaduan tembakau pegunungan Jawa pilihan dan cengkeh bermutu tinggi.
            </p>

            <!-- Tombol Aksi -->
            <div class="hero-buttons" style="display: flex; gap: 16px; flex-wrap: wrap;">
                <a href="{{ route('pesanan.form') }}" class="btn btn-gold btn-lg" style="box-shadow: 0 8px 24px rgba(197, 160, 89, 0.35); font-weight: 700; padding: 14px 28px;">
                    <i class="fa-solid fa-cart-shopping"></i> Order Sekarang (Mulai 1 Slop)
                </a>
                <a href="{{ route('katalog.index') }}" class="btn btn-secondary btn-lg" style="background: rgba(13, 16, 18, 0.72); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; backdrop-filter: blur(8px); padding: 14px 26px;">
                    <i class="fa-solid fa-boxes-stacked"></i> Lihat Katalog Produk
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Tentang Singkat & Lencana Legalitas -->
<section class="section-about">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <span class="eyebrow">SEKILAS PERUSAHAAN</span>
                <h2 class="section-title">WARISAN LINTINGAN HINGGA MODERNITAS PRODUKSI</h2>
                <div class="about-highlight-box">
                    <p style="margin-bottom: 0; color: #ffffff; font-weight: 600; font-size: 1.05rem;">
                        "Menjaga kemurnian cita rasa kretek warisan nusantara dengan kepatuhan penuh terhadap regulasi pita cukai negara."
                    </p>
                </div>
                <p>
                    Beroperasi di Dusun Subontoro, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur, <strong>PR. KERETA KENCANA</strong> dipimpin oleh Bapak Komari Yaman. Kami memproduksi 3 produk unggulan kretek tangan 12 batang (SEMBADA, KERETA KENCANA, SEMBADA CETHE) dan melayani pengadaan pasokan distributor besar, agen grosir, hingga toko dan warung dengan fleksibilitas order mulai dari <strong>1 slop</strong> hingga kemasan Bal dan karton pengiriman terpadu.
                </p>
                <div style="margin-top: 24px;">
                    <a href="{{ route('profil') }}" class="btn btn-sm btn-outline-gold">
                        Baca Profil Perusahaan & Legalitas Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div>
                <span class="eyebrow">KEPATUHAN REGULASI & PERIZINAN</span>
                <div class="legal-badges-grid">
                    <div class="badge-card">
                        <div class="badge-icon"><i class="fa-solid fa-stamp"></i></div>
                        <div>
                            <div class="badge-title">NPPBKC Resmi</div>
                            <div class="badge-sub">0821.1.2.XXXXX (Bea Cukai Blitar)</div>
                        </div>
                    </div>
                    <div class="badge-card">
                        <div class="badge-icon"><i class="fa-solid fa-file-contract"></i></div>
                        <div>
                            <div class="badge-title">NIB Usaha Terbit</div>
                            <div class="badge-sub">9120001234567 (OSS RBA RI)</div>
                        </div>
                    </div>
                    <div class="badge-card">
                        <div class="badge-icon"><i class="fa-solid fa-shield-halved"></i></div>
                        <div>
                            <div class="badge-title">Pita Cukai Asli</div>
                            <div class="badge-sub">Seri HPTL/Hasil Tembakau Resmi</div>
                        </div>
                    </div>
                    <div class="badge-card">
                        <div class="badge-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <div>
                            <div class="badge-title">Pengiriman Cepat</div>
                            <div class="badge-sub">Armada Pabrik & Ekspedisi Kargo</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Produk Unggulan SKT Resmi -->
<section class="section-py" id="produk">
    <div class="container">
        <div class="section-header-center">
            <span class="eyebrow"><i class="fa-solid fa-boxes-stacked"></i> PRODUK RESMI PABRIK</span>
            <h2 class="section-title">3 KOLEKSI SIGARET KRETEK TANGAN RESMI</h2>
            <p class="section-subtitle">Pilihan Sigaret Kretek Tangan (SKT) 12 batang unggulan Blitar berpita cukai resmi: <strong>SEMBADA</strong>, <strong>KERETA KENCANA</strong>, dan <strong>SEMBADA CETHE</strong>. Melayani pemesanan mulai dari 1 slop (eceran toko) hingga paket grosir bal distributor.</p>
        </div>

        <div class="products-grid">
            @foreach($produks as $p)
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <img src="{{ $p->image_url }}" alt="{{ $p->nama }}" class="product-img">
                        <span class="category-tag category-{{ strtolower($p->kategori->slug) }}">
                            {{ $p->kategori->nama_kategori }}
                        </span>
                        <span class="stock-tag stock-{{ Str::slug($p->status_stok) }}">
                            {{ $p->status_stok }}
                        </span>
                    </div>

                    <div class="product-info">
                        <div class="product-code">{{ $p->kode_barang }}</div>
                        <h3 class="product-title">{{ $p->nama }}</h3>
                        <p class="product-profile"><i class="fa-solid fa-tag"></i> {{ $p->profil_rasa }}</p>

                        @if($p->deskripsi)
                            <p class="product-desc">{{ Str::limit($p->deskripsi, 85) }}</p>
                        @endif

                        <div class="specs-mini">
                            <div class="spec-mini-row">
                                <span class="text-muted">Kadar Tar / Nic:</span>
                                <strong>{{ $p->tar_mg ?? '-' }} mg / {{ $p->nikotin_mg ?? '-' }} mg</strong>
                            </div>
                            <div class="spec-mini-row">
                                <span class="text-muted">Isi Per Bungkus:</span>
                                <strong>{{ $p->batang_per_bungkus }} Batang</strong>
                            </div>
                            <div class="spec-mini-row">
                                <span class="text-muted">Kemasan:</span>
                                <strong>{{ $p->bungkus_per_slop }} Bks/Slop ({{ $p->slop_per_bal }} Slop/Bal)</strong>
                            </div>
                            <div class="spec-mini-row">
                                <span class="text-muted">Min. Order:</span>
                                <strong style="color: var(--gold);">1 Slop (Toko) / 1 Bal (Grosir)</strong>
                            </div>
                        </div>

                        <div class="price-order-box">
                            <div class="price-label">Harga Per Slop (10 Bungkus)</div>
                            <div class="price-main">
                                {{ $p->formatted_harga_slop }} <span style="font-size: 13px; font-weight: normal; color: var(--text-muted);">/ Slop</span>
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">
                                Grosir Bal (20 Slop): <strong style="color: #fff;">{{ $p->formatted_harga }}</strong>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin memesan pasokan rokok ' . $p->nama . ' (Mulai 1 Slop). Mohon info stok & ongkir.') }}" target="_blank" rel="noopener noreferrer" class="btn btn-wa btn-block">
                                    <i class="fa-brands fa-whatsapp"></i> Pesan via WA (Mulai 1 Slop)
                                </a>
                                <a href="{{ route('pesanan.form', ['produk' => $p->slug]) }}" class="btn btn-outline-gold btn-block btn-sm">
                                    <i class="fa-solid fa-file-invoice"></i> Order Faktur Resmi (Slop / Bal)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="noProductResult" style="display: none; text-align: center; padding: 50px 20px; background: var(--charcoal-800); border-radius: var(--radius-lg); border: 1px dashed var(--charcoal-border); margin-top: 24px;">
            <i class="fa-solid fa-box-open" style="font-size: 40px; color: var(--gold); margin-bottom: 12px;"></i>
            <h4 style="color: #ffffff;">Varian Rokok Tidak Ditemukan</h4>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Coba sesuaikan kata kunci pencarian atau ganti filter kategori di atas.</p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Hero Large Background Auto-Slider
    let heroCurrentSlide = 0;
    const heroSlides = document.querySelectorAll('#heroBgSlider .hero-slide');
    const heroDots = document.querySelectorAll('.hero-slider-dot');
    let heroSliderTimer = null;

    function showHeroSlide(index) {
        if (!heroSlides.length) return;
        if (index >= heroSlides.length) index = 0;
        if (index < 0) index = heroSlides.length - 1;
        
        heroCurrentSlide = index;

        heroSlides.forEach((slide, i) => {
            if (i === heroCurrentSlide) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });

        heroDots.forEach((dot, i) => {
            if (i === heroCurrentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function nextHeroSlide() {
        showHeroSlide(heroCurrentSlide + 1);
        resetHeroSliderTimer();
    }

    function prevHeroSlide() {
        showHeroSlide(heroCurrentSlide - 1);
        resetHeroSliderTimer();
    }

    function goToHeroSlide(index) {
        showHeroSlide(index);
        resetHeroSliderTimer();
    }

    function resetHeroSliderTimer() {
        clearInterval(heroSliderTimer);
        heroSliderTimer = setInterval(() => {
            showHeroSlide(heroCurrentSlide + 1);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (heroSlides.length > 1) {
            resetHeroSliderTimer();

            // Pause on hover
            const heroSec = document.getElementById('heroSection');
            if (heroSec) {
                heroSec.addEventListener('mouseenter', () => clearInterval(heroSliderTimer));
                heroSec.addEventListener('mouseleave', () => resetHeroSliderTimer());
            }
        }
    });
</script>
@endpush

