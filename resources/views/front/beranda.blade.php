@extends('layouts.app')

@section('title', 'PR. KERETA KENCANA - Pabrik Sigaret Kretek Blitar')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background: radial-gradient(circle at 80% 35%, rgba(197, 160, 89, 0.12), transparent 55%), #0d1012; position: relative; overflow: hidden; padding: 40px 0;">
    <div class="container" style="position: relative; z-index: 2;">
        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center;" class="grid-2col">
            <div class="hero-content" style="padding: 40px 0;">
                <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(197, 160, 89, 0.12); border: 1px solid rgba(197, 160, 89, 0.3); padding: 6px 14px; border-radius: 20px; margin-bottom: 16px;">
                    <i class="fa-solid fa-stamp" style="color: var(--gold); font-size: 13px;"></i>
                    <span style="color: var(--gold); font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">PABRIK RESMI BERIZIN CUKAI &bull; PONGGOK, KAB. BLITAR</span>
                </div>
                <h1 class="hero-slogan" style="margin-top: 0; font-size: 3rem; line-height: 1.2;">DEDIKASI MUTU KRETEK BLITAR UNTUK MITRA DISTRIBUSI</h1>
                <p class="hero-subheadline" style="font-size: 1.05rem; line-height: 1.8; color: var(--text-light); margin-bottom: 26px;">
                    PR. KERETA KENCANA memproduksi 3 varian Sigaret Kretek Tangan (SKT) 12 batang berpita cukai resmi negara: <strong>SEMBADA</strong>, <strong>KERETA KENCANA</strong>, dan <strong>SEMBADA CETHE</strong> dengan perpaduan tembakau pegunungan Jawa pilihan dan cengkeh bermutu tinggi.
                </p>
                <div class="hero-buttons" style="display: flex; gap: 14px; flex-wrap: wrap;">
                    <a href="{{ route('pesanan.form') }}" class="btn btn-gold btn-lg">
                        <i class="fa-solid fa-cart-shopping"></i> Order Sekarang (Mulai 1 Slop)
                    </a>
                    <a href="{{ route('katalog.index') }}" class="btn btn-secondary btn-lg" style="background: rgba(255,255,255,0.06); border: 1px solid var(--charcoal-border); color: #fff;">
                        <i class="fa-solid fa-boxes-stacked"></i> Lihat 3 Produk Resmi
                    </a>
                </div>
            </div>

            <!-- Official Logo Showcase on Hero -->
            <div style="text-align: center;">
                <div style="background: rgba(22, 27, 31, 0.85); border: 1px solid rgba(197, 160, 89, 0.35); border-radius: 16px; padding: 36px 28px; box-shadow: 0 16px 40px rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: inline-block; width: 100%; max-width: 420px;">
                    <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="Logo Resmi PR. Kereta Kencana" style="max-height: 220px; width: auto; margin: 0 auto; object-fit: contain; filter: drop-shadow(0 8px 24px rgba(0,0,0,0.5));">
                    <div style="font-family: var(--font-serif); color: #ffffff; font-size: 20px; font-weight: 800; margin-top: 18px; letter-spacing: 1px;">
                        PR. KERETA KENCANA
                    </div>
                    <div style="color: var(--gold); font-size: 13px; font-weight: 600; margin-top: 4px;">
                        Pabrik Sigaret Kretek Ponggok &bull; Blitar
                    </div>
                    <div style="color: var(--text-muted); font-size: 11.5px; margin-top: 6px;">
                        <i class="fa-solid fa-circle-check" style="color: var(--success);"></i> NPPBKC: 0821.1.2.XXXXX (Bea Cukai Blitar)
                    </div>
                </div>
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
