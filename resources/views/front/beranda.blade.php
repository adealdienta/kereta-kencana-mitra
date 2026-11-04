@extends('layouts.app')

@section('title', 'PR. KERETA KENCANA - Pabrik Sigaret Kretek Blitar')

@section('content')
<div class="beranda-no-zoom">
<!-- Hero Section -->
<section class="hero-section" id="heroSection" style="position: relative; overflow: hidden; padding: 115px 0 60px; min-height: 95vh; display: flex; align-items: flex-start; background-color: #0e0c0a;">
    <!-- Large Background Slider (Auto-Slide / Ken-Burns Fade) -->
    @php
        $heroSlides = [
            [
                'img' => asset('assets/img/produksi-pabrik-asli.jpg') . '?v=2',
                'title' => 'Lini Produksi SKT PR. KERETA KENCANA',
            ],
            [
                'img' => asset('assets/img/proses-bahan-baku.jpg') . '?v=2',
                'title' => 'Racikan Tembakau & Cengkeh Pilihan',
            ],
            [
                'img' => asset('assets/img/proses-gudang-distribusi.jpg') . '?v=2',
                'title' => 'Kemasan Slop Resmi 3 Varian SKT',
            ],
            [
                'img' => asset('assets/img/proses-linting-skt.jpg') . '?v=2',
                'title' => 'Kepatuhan Pita Cukai Resmi Negara',
            ],
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
        <div class="hero-content-clean scroll-reveal reveal-up" style="max-width: 800px; padding: 0;">
            <!-- Badge Atas -->
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(22, 19, 15, 0.85); border: 1px solid var(--charcoal-border); padding: 6px 16px; border-radius: 20px; margin-bottom: 22px;">
                <span style="color: var(--gold); font-size: 11px;">✦</span>
                <span style="color: var(--gold-light); font-size: 11.5px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">PABRIK RESMI BERIZIN CUKAI &bull; PONGGOK, KAB. BLITAR</span>
                <span style="color: var(--gold); font-size: 11px;">✦</span>
            </div>

            <!-- Slogan Utama -->
            <h1 class="hero-slogan" style="margin-top: 0; font-size: clamp(2rem, 4vw, 2.85rem); line-height: 1.22; font-weight: 800; color: #ffffff; letter-spacing: 0.5px; margin-bottom: 20px;">
                WARISAN LINTINGAN TRADISIONAL <br>
                <span style="color: var(--gold);">HINGGA INDUSTRI MODERN</span>
            </h1>

        </div>
    </div>
</section>

<!-- Tentang Singkat & Lencana Legalitas (Tata Letak Container Terbingkai Rapi) -->
<section class="section-about">
    <div class="container">
        <div class="about-grid" style="align-items: stretch;">
            <div class="about-text scroll-reveal reveal-up" style="display: flex; flex-direction: column; justify-content: center;">
                <span class="eyebrow">SEKILAS PABRIK KRETEK</span>
                <h2 class="section-title" style="font-size: clamp(1.75rem, 2.8vw, 2.25rem); margin-bottom: 16px;">Tradisi Lintingan Blitar Berizin Resmi Cukai</h2>
                <div class="about-highlight-box">
                    <p style="margin-bottom: 0; color: var(--text-dark); font-weight: 600; font-size: 0.98rem; line-height: 1.65;">
                        "Menjaga kemurnian cita rasa kretek warisan Nusantara dengan kepatuhan penuh terhadap regulasi pita cukai negara."
                    </p>
                </div>
                <p style="margin-bottom: 24px;">
                    Beroperasi di Dusun Subontoro, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur, <strong>PR. KERETA KENCANA</strong> dipimpin oleh Bapak Komari Yaman. Kami memproduksi 3 varian Sigaret Kretek Tangan (SKT) 12 batang (<strong>SEMBADA</strong>, <strong>KERETA KENCANA</strong>, dan <strong>SEMBADA CETHE</strong>) serta melayani pengadaan pasokan distributor besar, agen grosir, hingga toko dan warung dengan fleksibilitas order mulai dari <strong>1 slop</strong> hingga kemasan Bal.
                </p>
                <div>
                    <a href="{{ route('profil') }}" class="btn btn-sm btn-outline-gold">
                        Baca Profil Perusahaan & Legalitas Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="scroll-reveal reveal-up reveal-delay-2" style="display: flex;">
                @if(file_exists(public_path('assets/img/pabrik-beranda.jpg')))
                    <div class="photo-frame-modern" style="width: 100%; min-height: 340px; height: 100%;">
                        <img src="{{ asset('assets/img/pabrik-beranda.jpg') }}?v=2" alt="Gedung Pabrik PR. Kereta Kencana" class="photo-frame-img">
                        <div class="photo-frame-caption">
                            <i class="fa-solid fa-industry" style="color: var(--gold);"></i> Aktivitas & Fasilitas Pabrik Ponggok
                        </div>
                    </div>
                @else
                    <div class="photo-placeholder-slot" style="width: 100%; min-height: 340px; height: 100%;">
                        <div class="photo-placeholder-icon" style="width: 60px; height: 60px; font-size: 22px;">
                            <i class="fa-solid fa-industry"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Gedung & Gerbang Utama Pabrik</div>
                        <div class="photo-placeholder-sub">Dokumentasi tampak depan fasilitas produksi PR. Kereta Kencana di Desa Kebonduren, Kec. Ponggok, Kab. Blitar.</div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/pabrik-beranda.jpg</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Baris 4 Lencana Kepatuhan Regulasi & Perizinan (Sejajar Rapi 4 Kolom) -->
        <div class="legal-badges-4col scroll-reveal reveal-up">
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
</section>

<!-- Produk Unggulan SKT Resmi -->
<section class="section-py" id="produk" style="padding: 88px 0; background: var(--bg-surface-subtle); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <div class="section-header-center scroll-reveal reveal-up">
            <span class="eyebrow">PORTOFOLIO PRODUK RESMI</span>
            <h2 class="section-title" style="font-size: clamp(1.75rem, 3vw, 2.3rem);">Koleksi Sigaret Kretek Tangan (SKT)</h2>
            <div class="gold-divider"></div>
            <p class="section-subtitle">Tiga varian Sigaret Kretek Tangan (SKT) 12 batang berpita cukai resmi negara: <strong>SEMBADA</strong>, <strong>KERETA KENCANA</strong>, dan <strong>SEMBADA CETHE</strong>. Pemesanan langsung pabrik mulai dari 1 slop hingga kemasan bal grosir.</p>
        </div>

        <div class="products-grid">
            @foreach($produks as $p)
                <div class="product-card scroll-reveal reveal-up reveal-delay-{{ ($loop->index % 3) + 1 }}">
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
                                Grosir Bal (20 Slop): <strong style="color: var(--text-dark);">{{ $p->formatted_harga }}</strong>
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

<!-- Warisan & Proses Pembuatan Kretek (Bagian Full-Bleed 50:50 Zig-Zag PR. Kereta Kencana) -->
<section style="padding: 80px 0 0; background: #ffffff;">
    <div class="container">
        <div class="section-header-center scroll-reveal reveal-up" style="margin-bottom: 56px;">
            <span class="eyebrow">WARISAN KRETEK & STANDAR PRODUKSI</span>
            <h2 class="section-title" style="font-size: clamp(1.75rem, 3vw, 2.3rem);">Proses Manufaktur & Jaminan Pasokan</h2>
            <div class="gold-divider"></div>
            <p class="section-subtitle">Ketelitian peracikan daun tembakau dan cengkeh Nusantara, keahlian pelintingan tangan tradisional, hingga kesiapan distribusi berskala slop dan bal.</p>
        </div>
    </div>
    <div class="proses-split-list">
        <!-- Baris 1: Gambar Besar 50% di KIRI — Teks di KANAN -->
        <div class="proses-split-row scroll-reveal reveal-up">
            <div class="proses-split-media">
                @if(file_exists(public_path('assets/img/proses-bahan-baku.jpg')))
                    <div class="photo-frame-modern">
                        <img src="{{ asset('assets/img/proses-bahan-baku.jpg') }}?v=2" alt="Seleksi Tembakau & Cengkeh Pilihan" class="photo-frame-img">
                        <div class="photo-frame-caption">
                            <i class="fa-solid fa-seedling" style="color: var(--gold);"></i> Bahan Baku Tembakau & Cengkeh Nusantara
                        </div>
                    </div>
                @else
                    <div class="photo-placeholder-slot">
                        <div class="photo-placeholder-icon">
                            <i class="fa-solid fa-seedling"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Bahan Baku Tembakau & Cengkeh</div>
                        <div class="photo-placeholder-sub">Dokumentasi gudang penyimpanan daun tembakau kering & rempah cengkeh pilihan PR. Kereta Kencana.</div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/proses-bahan-baku.jpg</span>
                    </div>
                @endif
            </div>
            <div class="proses-split-content">
                <h3 class="proses-split-title">Keunikan Racikan Tembakau & Cengkeh</h3>
                <p class="proses-split-desc">
                    Kretek merupakan karya khas Nusantara yang mengandalkan ketelitian peracikan bahan baku. Di PR. Kereta Kencana, cita rasa setiap varian ditentukan oleh perpaduan daun tembakau pegunungan Jawa pilihan dan bunga cengkeh kering berkualitas tinggi yang diseleksi secara ketat sejak tahap awal penerimaan.
                </p>
                <p class="proses-split-desc">
                    Usia simpan daun tembakau, tingkat kelembaban, serta proporsi antara rajangan tembakau dan cengkeh diatur secara presisi. Seluruh komposisi tersebut diperam bersama saus racikan khas pabrik untuk menghasilkan keseimbangan aroma rempah yang hangat dan kenikmatan tarikan yang konsisten di setiap batang.
                </p>
            </div>
        </div>

        <!-- Baris 2: Teks di KIRI — Gambar Besar 50% di KANAN -->
        <div class="proses-split-row reverse scroll-reveal reveal-up">
            <div class="proses-split-content">
                <h3 class="proses-split-title">Keahlian Linting Tangan & Legalitas Cukai</h3>
                <p class="proses-split-desc">
                    Pembuatan Sigaret Kretek Tangan (SKT) dilakukan langsung oleh tangan-tangan terampil pekerja pabrik di Ponggok, Kabupaten Blitar. Proses pelintingan manual memastikan kepadatan tembakau di setiap batang terjaga merata tanpa mengurangi kelancaran hisapan.
                </p>
                <p class="proses-split-desc">
                    Setiap bungkus rokok isi 12 batang yang telah selesai dilinting melewati tahapan pemeriksaan mutu berlapis sebelum dilekati pita cukai resmi Republik Indonesia yang terdaftar di KPPBC Bea Cukai Blitar. Kepatuhan ini menjamin seluruh produk kami aman dan resmi untuk diperdagangkan oleh mitra toko maupun agen.
                </p>
            </div>
            <div class="proses-split-media">
                @if(file_exists(public_path('assets/img/proses-linting-skt.jpg')))
                    <div class="photo-frame-modern">
                        <img src="{{ asset('assets/img/proses-linting-skt.jpg') }}?v=2" alt="Lini Linting Tangan SKT & Pita Cukai" class="photo-frame-img">
                        <div class="photo-frame-caption">
                            <i class="fa-solid fa-hands-holding-circle" style="color: var(--gold);"></i> Lini Produksi SKT & Pelekatan Pita Cukai
                        </div>
                    </div>
                @else
                    <div class="photo-placeholder-slot">
                        <div class="photo-placeholder-icon">
                            <i class="fa-solid fa-stamp"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Proses Linting SKT & Pita Cukai</div>
                        <div class="photo-placeholder-sub">Dokumentasi pekerja linting Sigaret Kretek Tangan (SKT) & pelekatan pita cukai resmi Bea Cukai.</div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/proses-linting-skt.jpg</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Baris 3: Gambar Besar 50% di KIRI — Teks di KANAN -->
        <div class="proses-split-row scroll-reveal reveal-up">
            <div class="proses-split-media">
                @if(file_exists(public_path('assets/img/proses-gudang-distribusi.jpg')))
                    <div class="photo-frame-modern">
                        <img src="{{ asset('assets/img/proses-gudang-distribusi.jpg') }}?v=2" alt="Gudang Distribusi Slop & Bal" class="photo-frame-img">
                        <div class="photo-frame-caption">
                            <i class="fa-solid fa-boxes-stacked" style="color: var(--gold);"></i> Gudang Stok Slop & Bal Siap Kirim
                        </div>
                    </div>
                @else
                    <div class="photo-placeholder-slot">
                        <div class="photo-placeholder-icon">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Gudang Stok Slop & Kemasan Bal</div>
                        <div class="photo-placeholder-sub">Dokumentasi kesiapan stok kemasan slop dan bal di gudang distribusi PR. Kereta Kencana.</div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/proses-gudang-distribusi.jpg</span>
                    </div>
                @endif
            </div>
            <div class="proses-split-content">
                <h3 class="proses-split-title">Jaringan Distribusi Slop hingga Kemasan Bal</h3>
                <p class="proses-split-desc">
                    Untuk mendukung kelancaran perputaran usaha mitra di berbagai wilayah, fasilitas gudang barang jadi kami menerapkan standar penyimpanan yang bersih dan terstruktur. Setiap produk dikemas rapi ke dalam satuan slop (10 bungkus) dan satuan bal (20 slop / 200 bungkus).
                </p>
                <p class="proses-split-desc">
                    Melalui sistem pemesanan terpadu yang dilengkapi faktur resmi, PR. Kereta Kencana melayani pengiriman pasokan langsung dari pabrik baik untuk kebutuhan toko kelontong dan warung mulai dari 1 slop, maupun pengadaan grosir partai besar bagi agen dan distributor daerah.
                </p>
            </div>
        </div>
    </div>
</section>
</div>
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

