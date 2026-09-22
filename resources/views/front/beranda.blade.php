@extends('layouts.app')

@section('title', 'PR. KERETA KENCANA - Pabrik Sigaret Kretek Blitar')

@section('content')
<!-- Hero Section (Persis Desain Asli) -->
<section class="hero-section">
    <img src="{{ asset('assets/img/hero-pabrik.jpg') }}" alt="Pabrik Rokok Kereta Kencana" class="hero-bg">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <span class="eyebrow"><i class="fa-solid fa-stamp"></i> PABRIK RESMI BERIZIN CUKAI &bull; PONGGOK, KAB. BLITAR</span>
            <h1 class="hero-slogan">DEDIKASI MUTU KRETEK BLITAR UNTUK MITRA DISTRIBUSI</h1>
            <p class="hero-subheadline">
                PR. KERETA KENCANA memproduksi Sigaret Kretek Mesin (SKM) dan Sigaret Kretek Tangan (SKT) bercukai resmi dengan perpaduan tembakau pegunungan Jawa pilihan dan cengkeh Zanzibar beraroma mantap.
            </p>
            <div class="hero-buttons">
                <a href="{{ route('pesanan.form') }}" class="btn btn-gold btn-lg">
                    <i class="fa-solid fa-cart-shopping"></i> Order Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Tentang Singkat & Lencana Legalitas (Persis Desain Asli) -->
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
                    Beroperasi di Dusun Subontoro, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur, <strong>PR. KERETA KENCANA</strong> dipimpin oleh Bapak Komari Yaman. Kami melayani pengadaan pasokan distributor besar, agen grosir, hingga toko dan warung dengan fleksibilitas order mulai dari <strong>1 slop</strong> hingga kemasan Bal dan karton pengiriman terpadu.
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

<!-- Produk Unggulan SKM & SKT (Persis Desain Asli) -->
<section class="section-py" id="produk">
    <div class="container">
        <div class="section-header-center">
            <span class="eyebrow">PRODUK RESMI PABRIK</span>
            <h2 class="section-title">KOLEKSI PRODUK KRETEK RESMI PABRIK</h2>
            <p class="section-subtitle">Pilihan Sigaret Kretek Tangan (SKT) unggulan Blitar berpita cukai resmi: Lini DWIPANTARA, SEMBADA, dan KERETA KENCANA. Melayani pemesanan mulai dari 1 slop hingga paket grosir bal.</p>
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
