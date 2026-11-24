@extends('layouts.app')

@section('title', 'PR. KERETA KENCANA - Pabrik Sigaret Kretek Blitar')

@section('content')
<div class="beranda-no-zoom">
<!-- Hero Section -->
<section class="hero-section" id="heroSection">
    <!-- Large Background Slider (Auto-Slide / Ken-Burns Fade) -->
    @php
        $heroSlides = $heroSlides ?? \App\Services\PabrikConfigService::getHeroSlides();
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
        <div class="hero-content-clean scroll-reveal reveal-up" style="max-width: 780px; padding: 0;">
            <!-- Slogan Utama -->
            <h1 class="hero-slogan" style="margin-top: 0; font-size: clamp(2rem, 4vw, 2.85rem); line-height: 1.22; font-weight: 800; color: #ffffff; letter-spacing: 0.5px; margin-bottom: 16px;">
                WARISAN LINTINGAN TRADISIONAL <br>
                <span style="color: #d6c3a1;">KRETEK ASLI BLITAR</span>
            </h1>

            <!-- Subheadline -->
            <p class="hero-subheadline" style="font-size: clamp(0.98rem, 1.45vw, 1.1rem); line-height: 1.75; color: #f4f4f5; margin-bottom: 28px; max-width: 640px;">
                Produsen resmi Sigaret Kretek Tangan (SKT) Blitar dengan racikan tembakau &amp; cengkeh Nusantara pilihan.
            </p>

            <!-- Tombol Aksi Utama -->
            <div class="hero-buttons" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="#produk" class="btn btn-gold" style="padding: 12px 24px; font-size: 0.94rem; font-weight: 700;">
                    Lihat Varian Produk
                </a>
                <a href="https://wa.me/{{ $pabrikWa ?? config('pabrik.wa_pabrik', '6285790627104') }}?text={{ urlencode('Halo Manajemen PR. KERETA KENCANA, saya ingin bertanya mengenai pasokan dan kemitraan produk rokok.') }}" target="_blank" rel="noopener noreferrer" class="btn" style="background: rgba(16, 15, 14, 0.55); border: 1px solid rgba(255, 255, 255, 0.45); color: #ffffff; padding: 12px 22px; font-size: 0.94rem; font-weight: 600;">
                    <i class="fa-brands fa-whatsapp"></i> Hubungi Kemitraan
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Tentang Singkat (Identitas Pabrik) -->
<section class="section-about">
    <div class="about-grid">
        <div class="about-text scroll-reveal reveal-up">
            <h2 class="section-title" style="font-size: clamp(1.85rem, 2.9vw, 2.3rem); margin-bottom: 14px;">Sigaret Kretek Tangan Blitar Berpita Cukai Resmi</h2>
            <div class="about-highlight-box" style="border-radius: 0;">
                <p style="margin-bottom: 0; font-weight: 500; font-size: 0.98rem; line-height: 1.62;">
                    Tiga varian SKT isi 12 batang dengan pita cukai resmi untuk pasokan toko ritel hingga agen grosir daerah.
                </p>
            </div>
            <p style="margin-bottom: 22px;">
                Beroperasi di Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur, <strong>PR. KERETA KENCANA</strong> memproduksi <strong>SEMBADA 12</strong>, <strong>KERETA KENCANA 12</strong>, dan <strong>SEMBADA Cethe 12</strong> di bawah pengawasan KPPBC Tipe Madya Pabean C Blitar.
            </p>
            <div>
                <a href="{{ route('profil') }}" class="btn btn-sm btn-outline-gold">
                    Baca Profil Perusahaan &amp; Legalitas <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="about-media-flush scroll-reveal reveal-up reveal-delay-2">
            @php $imgMeta = $fasilitasImages['pabrik-beranda'] ?? []; @endphp
            @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                <div class="photo-frame-modern">
                    <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Gedung Pabrik PR. Kereta Kencana' }}" class="photo-frame-img">
                    @include('front._facility_caption', ['imgMeta' => $imgMeta])
                </div>
            @else
                <div class="photo-placeholder-slot">
                    <div class="photo-placeholder-icon" style="width: 56px; height: 56px; font-size: 20px;">
                        <i class="fa-solid fa-industry"></i>
                    </div>
                    <div class="photo-placeholder-title">Foto Gedung &amp; Gerbang Utama Pabrik</div>
                    <div class="photo-placeholder-sub">Dokumentasi tampak depan fasilitas produksi PR. Kereta Kencana di Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kec. Ponggok, Kab. Blitar.</div>
                    <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/pabrik-beranda.jpg</span>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Showcase Varian Produk (Coverflow Hitam #produk) -->
<section class="kk-spice-showcase" id="produk">
    <div class="container">
        <div class="kk-showcase-header scroll-reveal reveal-up">
            <span class="kk-showcase-eyebrow">Sigaret Kretek Tangan &bull; 12 Batang</span>
            <h2 class="kk-showcase-title">Varian Produk PR. Kereta Kencana</h2>
            <p class="kk-showcase-subtitle">SEMBADA 12, KERETA KENCANA 12, dan SEMBADA Cethe 12</p>
        </div>

        <div class="kk-coverflow-stage scroll-reveal reveal-up" id="kkCoverflowShowcase">
            <button type="button" class="kk-coverflow-arrow kk-coverflow-prev" aria-label="Varian Sebelumnya">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="kk-coverflow-track">
                @foreach($produks as $p)
                    @php
                        $cardVariant = match($p->slug) {
                            'kereta-kencana-12' => [
                                'class' => 'kk-spice-card--crimson',
                                'color' => '#6e1b18',
                            ],
                            'sembada-cethe-12' => [
                                'class' => 'kk-spice-card--plum',
                                'color' => '#3f2436',
                            ],
                            default => [
                                'class' => 'kk-spice-card--bronze',
                                'color' => '#6b5536',
                            ],
                        };
                    @endphp
                    <article
                        class="kk-spice-card {{ $cardVariant['class'] }}"
                        data-pos="0"
                        data-slug="{{ $p->slug }}"
                        data-name="{{ $p->nama }}"
                        data-sub="{{ $p->kategori->nama_kategori }} • {{ $p->batang_per_bungkus }} Batang"
                        data-taste="Profil Rasa: {{ $p->profil_rasa }}"
                        data-desc="{{ $p->deskripsi }}"
                        data-image="{{ $p->image_url }}"
                        data-color="{{ $cardVariant['color'] }}"
                        data-url="{{ route('produk.detail', $p->slug) }}"
                    >
                        @if($p->best_seller)
                            <span class="bestseller-tag">Paling Diminati</span>
                        @endif
                        <div class="kk-spice-media">
                            <img src="{{ $p->image_url }}" alt="{{ $p->nama }}" class="kk-spice-img">
                        </div>
                        <div class="kk-spice-overlay">
                            <h3 class="kk-spice-name">{{ $p->nama }}</h3>
                            <button type="button" class="kk-spice-cta">
                                Selengkapnya +
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <button type="button" class="kk-coverflow-arrow kk-coverflow-next" aria-label="Varian Berikutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div class="kk-coverflow-dots" aria-label="Pilih varian produk">
                @foreach($produks as $p)
                    <button type="button" class="kk-coverflow-dot" aria-label="Lihat {{ $p->nama }}"></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Modal Detail Varian (Gaya Overlay Rempah Djarum) -->
<div class="kk-variant-modal" id="kkVariantModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kkModalTitle">
    <button type="button" class="kk-variant-modal-nav kk-variant-modal-prev" id="kkModalPrev" aria-label="Varian Sebelumnya">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div class="kk-variant-modal-inner">
        <div class="kk-variant-modal-visual">
            <div class="kk-variant-modal-pedestal">
                <img src="" alt="" id="kkModalImg">
            </div>
        </div>

        <div class="kk-variant-modal-card">
            <button type="button" class="kk-variant-modal-close" id="kkModalClose" aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 class="kk-variant-modal-title" id="kkModalTitle"></h3>
            <p class="kk-variant-modal-sub" id="kkModalSub"></p>
            <p class="kk-variant-modal-taste" id="kkModalTaste"></p>
            <p class="kk-variant-modal-desc" id="kkModalDesc"></p>
            <div class="kk-variant-modal-actions">
                <a href="#" id="kkModalDetailLink" class="btn btn-gold" style="padding: 11px 22px; font-weight: 700; font-size: 0.88rem;">
                    Halaman Detail Varian <i class="fa-solid fa-arrow-right"></i>
                </a>
                @if(!Auth::check() || Auth::user()->isPelanggan())
                    <a href="{{ route('pesanan.form') }}" id="kkModalOrderLink" data-base-url="{{ route('pesanan.form') }}" class="btn kk-btn-dark-outline" style="padding: 11px 20px; font-size: 0.88rem;">
                        Pesan Varian Ini
                    </a>
                @else
                    <a href="{{ route('admin.transaksis.offline') }}" class="btn kk-btn-dark-outline" style="padding: 11px 20px; font-size: 0.88rem;">
                        Catat Kasir Offline
                    </a>
                @endif
            </div>
        </div>
    </div>

    <button type="button" class="kk-variant-modal-nav kk-variant-modal-next" id="kkModalNext" aria-label="Varian Berikutnya">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>

<!-- Keunggulan Kemitraan Langsung Pabrik -->
<section class="editorial-benefits-section">
    <div class="container">
        <div class="section-header scroll-reveal reveal-up" style="margin-bottom: 32px; max-width: 700px;">
            <h2 class="section-title" style="font-size: clamp(1.7rem, 2.8vw, 2.2rem);">Alasan Mitra Memilih Pasokan Kami</h2>
            <p class="section-desc">Kepastian legalitas cukai, pasokan langsung dari pabrik Blitar, serta kemudahan pemesanan untuk toko maupun agen daerah.</p>
        </div>

        <div class="editorial-benefits-grid">
            <div class="editorial-benefit-item scroll-reveal reveal-up reveal-delay-1">
                <div class="editorial-benefit-head">
                    <h3 class="editorial-benefit-title">Pasokan Langsung Pabrik</h3>
                </div>
                <p class="editorial-benefit-desc">
                    Produk dikirim langsung dari fasilitas PR. Kereta Kencana di Ponggok, Blitar tanpa rantai perantara berlapis.
                </p>
            </div>

            <div class="editorial-benefit-item scroll-reveal reveal-up reveal-delay-2">
                <div class="editorial-benefit-head">
                    <h3 class="editorial-benefit-title">Harga Bertingkat Slop</h3>
                </div>
                <p class="editorial-benefit-desc">
                    Semua varian memiliki tarif seragam: Rp 70.000 (1&ndash;5 Slop), Rp 65.000 (6&ndash;10 Slop), dan Rp 60.000 (11&ndash;19 Slop).
                </p>
            </div>

            <div class="editorial-benefit-item scroll-reveal reveal-up reveal-delay-3">
                <div class="editorial-benefit-head">
                    <h3 class="editorial-benefit-title">Berpita Cukai Resmi</h3>
                </div>
                <p class="editorial-benefit-desc">
                    Terdaftar di KPPBC TMP C Blitar (NPPBKC) serta dilekati pita cukai asli Republik Indonesia pada setiap bungkusnya.
                </p>
            </div>

            <div class="editorial-benefit-item scroll-reveal reveal-up reveal-delay-4">
                <div class="editorial-benefit-head">
                    <h3 class="editorial-benefit-title">Pengiriman &amp; Pembayaran</h3>
                </div>
                <p class="editorial-benefit-desc">
                    Mendukung pengiriman armada pabrik maupun ekspedisi kargo dengan opsi Bayar di Tempat (COD) dan Transfer Bank.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 3 Langkah Memulai Kemitraan -->
<section class="editorial-steps-section">
    <div class="container">
        <div class="section-header scroll-reveal reveal-up" style="margin-bottom: 34px; max-width: 720px;">
            <h2 class="section-title" style="font-size: clamp(1.7rem, 2.8vw, 2.2rem);">3 Langkah Menjadi Mitra Pasokan</h2>
            <p class="section-desc">Alur pengadaan stok langsung dari pabrik Ponggok, Blitar untuk pemilik toko maupun agen daerah.</p>
        </div>

        <div class="editorial-steps-grid">
            <div class="editorial-step-item scroll-reveal reveal-up reveal-delay-1">
                <span class="editorial-step-num">01</span>
                <h3 class="editorial-step-title">Pilih Varian &amp; Jumlah</h3>
                <p class="editorial-step-desc">
                    Gabungkan varian <strong>SEMBADA 12</strong>, <strong>KERETA KENCANA 12</strong>, atau <strong>SEMBADA Cethe 12</strong>. Order online tersedia hingga total maksimal <strong>19 Slop gabungan</strong> per transaksi.
                </p>
            </div>

            <div class="editorial-step-item scroll-reveal reveal-up reveal-delay-2">
                <span class="editorial-step-num">02</span>
                <h3 class="editorial-step-title">Tentukan Pembayaran</h3>
                <p class="editorial-step-desc">
                    Pilih metode <strong>Bayar di Tempat (COD)</strong> atau <strong>Transfer Bank</strong> saat mengisi formulir order web. Untuk pengadaan skala Bal (20 Slop ke atas), hubungi WhatsApp Admin.
                </p>
            </div>

            <div class="editorial-step-item scroll-reveal reveal-up reveal-delay-3">
                <span class="editorial-step-num">03</span>
                <h3 class="editorial-step-title">Pengiriman &amp; Konfirmasi</h3>
                <p class="editorial-step-desc">
                    Pesanan diproses dari gudang Ponggok, Blitar, lalu pantau pengiriman dan konfirmasi penerimaan barang melalui menu <em>Pesanan Saya</em>.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Proses Produksi SKT (Full-Bleed 50:50 Zig-Zag) -->
<section style="padding: 0; background: #ffffff;">
    <div id="prosesManufakturHeader" style="background: #000000; color: #ffffff; padding: 54px 0 48px;">
        <div class="container">
            <div class="section-header scroll-reveal reveal-up" style="margin-bottom: 0; max-width: 720px;">
                <h2 class="section-title" style="font-size: clamp(1.75rem, 3vw, 2.3rem); color: #ffffff;">Proses Manufaktur &amp; Jaminan Pasokan</h2>
                <p class="section-desc" style="color: #ffffff; opacity: 0.92; margin-bottom: 0;">Tahap penyiapan bahan baku tembakau dan cengkeh, pelintingan tangan SKT 12 batang, hingga kesiapan gudang distribusi.</p>
            </div>
        </div>
    </div>
    <div class="proses-split-list">
        <!-- Baris 1: Gambar Besar 50% di KIRI — Teks di KANAN -->
        <div class="proses-split-row scroll-reveal reveal-up">
            <div class="proses-split-media">
                @php $imgMeta = $fasilitasImages['proses-bahan-baku'] ?? []; @endphp
                @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                    <div class="photo-frame-modern">
                        <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Bahan Baku Tembakau dan Cengkeh' }}" class="photo-frame-img">
                        @include('front._facility_caption', ['imgMeta' => $imgMeta])
                    </div>
                @else
                    <div class="photo-placeholder-slot">
                        <div class="photo-placeholder-icon">
                            <i class="fa-solid fa-box-archive"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Bahan Baku Tembakau &amp; Cengkeh</div>
                        <div class="photo-placeholder-sub">Dokumentasi penyimpanan daun tembakau kering &amp; cengkeh di fasilitas PR. Kereta Kencana.</div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/proses-bahan-baku.jpg</span>
                    </div>
                @endif
            </div>
            <div class="proses-split-content">
                <h3 class="proses-split-title">Penyiapan Bahan &amp; Peracikan Kretek</h3>
                <p class="proses-split-desc">
                    Daun tembakau dan bunga cengkeh melalui tahap penyortiran serta penyesuaian kadar kelembaban di ruang penyimpanan sebelum diracik untuk tiap varian produk.
                </p>
                <p class="proses-split-desc">
                    Takaran rajangan tembakau, cengkeh, dan saus racikan pabrik dijaga seragam agar profil rasa SEMBADA 12, KERETA KENCANA 12, maupun SEMBADA Cethe 12 tetap konsisten antar-batch.
                </p>
            </div>
        </div>

        <!-- Baris 2: Teks di KIRI — Gambar Besar 50% di KANAN -->
        <div class="proses-split-row reverse scroll-reveal reveal-up">
            <div class="proses-split-content">
                <h3 class="proses-split-title">Linting Tangan SKT &amp; Pelekatan Pita Cukai</h3>
                <p class="proses-split-desc">
                    Pelintingan Sigaret Kretek Tangan (SKT) 12 batang dikerjakan manual oleh tenaga pelinting lokal di Ponggok, Kabupaten Blitar guna menjaga kepadatan batang yang merata.
                </p>
                <p class="proses-split-desc">
                    Setiap bungkus melewati pemeriksaan kemasan sebelum dilekati pita cukai resmi Republik Indonesia di bawah pengawasan KPPBC Tipe Madya Pabean C Blitar.
                </p>
            </div>
            <div class="proses-split-media">
                @php $imgMeta = $fasilitasImages['proses-linting-skt'] ?? []; @endphp
                @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                    <div class="photo-frame-modern">
                        <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Lini Linting Tangan SKT & Pita Cukai' }}" class="photo-frame-img">
                        @include('front._facility_caption', ['imgMeta' => $imgMeta])
                    </div>
                @else
                    <div class="photo-placeholder-slot">
                        <div class="photo-placeholder-icon">
                            <i class="fa-solid fa-stamp"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Proses Linting SKT &amp; Pita Cukai</div>
                        <div class="photo-placeholder-sub">Dokumentasi pekerja linting Sigaret Kretek Tangan (SKT) &amp; pelekatan pita cukai resmi Bea Cukai.</div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/proses-linting-skt.jpg</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Baris 3: Gambar Besar 50% di KIRI — Teks di KANAN -->
        <div class="proses-split-row scroll-reveal reveal-up">
            <div class="proses-split-media">
                @php $imgMeta = $fasilitasImages['proses-gudang-distribusi'] ?? []; @endphp
                @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                    <div class="photo-frame-modern">
                        <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Gudang Distribusi Slop & Bal' }}" class="photo-frame-img">
                        @include('front._facility_caption', ['imgMeta' => $imgMeta])
                    </div>
                @else
                    <div class="photo-placeholder-slot">
                        <div class="photo-placeholder-icon">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Gudang Penyimpanan &amp; Pengemasan</div>
                        <div class="photo-placeholder-sub">Dokumentasi kesiapan pengemasan slop dan bal di gudang distribusi PR. Kereta Kencana.</div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/proses-gudang-distribusi.jpg</span>
                    </div>
                @endif
            </div>
            <div class="proses-split-content">
                <h3 class="proses-split-title">Pengemasan &amp; Kesiapan Gudang Distribusi</h3>
                <p class="proses-split-desc">
                    Fasilitas gudang barang jadi kami menerapkan penataan stok yang rapi agar setiap pengiriman keluar dalam kondisi segel dan kemasan yang baik.
                </p>
                <p class="proses-split-desc">
                    Armada pabrik dan mitra kargo siap menyalurkan pasokan secara terjadwal ke toko ritel, warung, maupun agen daerah di Jawa Timur dan sekitarnya.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Penutup -->
<section style="background: var(--kk-ivory); border-top: 1px solid var(--kk-ivory-border); padding: 64px 0;">
    <div class="container" style="text-align: center; max-width: 720px;">
        <span style="font-family: var(--font-mono); font-size: 0.78rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--kk-bronze); display: block; margin-bottom: 10px;">
            Kemitraan Pasokan Langsung
        </span>
        <h2 class="section-title" style="font-size: clamp(1.75rem, 3vw, 2.25rem); margin-bottom: 14px;">
            Siap Memesan Pasokan untuk Toko atau Keagenan Anda?
        </h2>
        <p class="section-desc" style="margin: 0 auto 28px;">
            Ajukan pemesanan online hingga 19 Slop per transaksi, atau konsultasikan pengadaan skala Bal langsung bersama tim manajemen pabrik.
        </p>
        <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
            @if(!Auth::check() || Auth::user()->isPelanggan())
                <a href="{{ route('pesanan.form') }}" class="btn btn-gold" style="padding: 13px 26px; font-weight: 700;">
                    <i class="fa-solid fa-cart-shopping"></i> Order Online Sekarang
                </a>
            @else
                <a href="{{ route('admin.transaksis.offline') }}" class="btn btn-gold" style="padding: 13px 26px; font-weight: 700;">
                    <i class="fa-solid fa-cash-register"></i> Buka Kasir Pabrik (Offline)
                </a>
            @endif
            <a href="{{ route('katalog.index') }}" class="btn btn-outline-gold" style="padding: 13px 24px;">
                Lihat Katalog Lengkap
            </a>
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

