@extends('layouts.app')

@section('title', 'Spesifikasi ' . $produk->nama)

@section('content')
<section class="section-py">
    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="{{ route('katalog.index') }}" style="color: var(--gold); text-decoration: none; font-size: 14px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Katalog Produk
            </a>
        </div>

        <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 32px;">
            <!-- Gambar Produk -->
            <div>
                <div style="background: #ffffff; border-radius: var(--radius); border: 1px solid var(--charcoal-border); padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 260px; box-shadow: var(--shadow-md);">
                    <img src="{{ $produk->image_url }}" alt="{{ $produk->nama }}" 
                         style="max-width: 100%; max-height: 220px; width: auto; height: auto; object-fit: contain;">
                </div>
                <div style="margin-top: 16px; display: flex; gap: 10px;">
                    <span class="category-tag category-{{ strtolower($produk->kategori->slug) }}" style="position: static;">
                        {{ $produk->kategori->nama_kategori }}
                    </span>
                    <span class="stock-tag stock-{{ Str::slug($produk->status_stok) }}" style="position: static;">
                        {{ $produk->status_stok }} (Tersedia: {{ $produk->stok }} Bal)
                    </span>
                </div>
            </div>

            <!-- Rincian Produk -->
            <div>
                <div style="color: var(--gold); font-family: monospace; font-size: 14px; margin-bottom: 6px;">
                    KODE PRODUK: {{ $produk->kode_barang }}
                </div>
                <h1 style="font-family: var(--font-serif); font-size: 32px; color: var(--text-white); margin-bottom: 12px;">
                    {{ $produk->nama }}
                </h1>
                <p style="color: var(--text-light); line-height: 1.7; margin-bottom: 24px;">
                    {{ $produk->deskripsi }}
                </p>

                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--charcoal-border); border-radius: var(--radius); padding: 20px; margin-bottom: 24px;">
                    <h4 style="color: var(--gold); font-size: 15px; margin-bottom: 14px; text-transform: uppercase;">
                        <i class="fa-solid fa-sliders"></i> Spesifikasi Teknis Hasil Tembakau
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 14px; color: var(--text-light);">
                        <div><strong>Profil Rasa:</strong><br><span style="color: var(--text-muted);">{{ $produk->profil_rasa ?? '-' }}</span></div>
                        <div><strong>Kadar Tar & Nikotin:</strong><br><span style="color: var(--text-muted);">Tar: {{ $produk->tar_mg ?? '-' }} mg | Nic: {{ $produk->nikotin_mg ?? '-' }} mg</span></div>
                        <div><strong>Kemasan Per Bungkus:</strong><br><span style="color: var(--text-muted);">{{ $produk->batang_per_bungkus }} Batang</span></div>
                        <div><strong>Kemasan Per Slop:</strong><br><span style="color: var(--text-muted);">{{ $produk->bungkus_per_slop }} Bungkus</span></div>
                        <div><strong>Kemasan Distribusi Bal:</strong><br><span style="color: var(--text-muted);">{{ $produk->slop_per_bal }} Slop ({{ $produk->slop_per_bal * $produk->bungkus_per_slop }} Bungkus)</span></div>
                        <div><strong>Batas Minimum Pemesanan:</strong><br><span style="color: var(--gold); font-weight: 600;">Mulai 1 Slop (10 Bungkus)</span></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 16px; background: #121619; padding: 20px 24px; border-radius: var(--radius); border: 1px solid var(--charcoal-border); margin-bottom: 24px;">
                    <div>
                        <div style="margin-bottom: 10px;">
                            <span style="display: block; color: var(--text-muted); font-size: 11px; text-transform: uppercase;">Harga Satuan Slop (10 Bungkus):</span>
                            <span style="font-size: 24px; color: var(--gold); font-weight: 800;">{{ $produk->formatted_harga_slop }}</span>
                            <span style="font-size: 12px; color: var(--text-muted);">/ Slop &bull; Min. 1 Slop</span>
                        </div>
                        <div>
                            <span style="display: block; color: var(--text-muted); font-size: 11px; text-transform: uppercase;">Harga Paket Grosir Bal (20 Slop):</span>
                            <span style="font-size: 18px; color: #ffffff; font-weight: 700;">{{ $produk->formatted_harga }}</span>
                            <span style="font-size: 12px; color: var(--text-muted);">/ Bal</span>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; gap: 8px;">
                        <a href="{{ route('pesanan.form', ['produk' => $produk->slug]) }}" class="btn btn-gold" style="padding: 12px 20px; text-align: center;">
                            <i class="fa-solid fa-cart-shopping"></i> Order Online (Slop/Bal)
                        </a>
                        <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin memesan pasokan rokok ' . $produk->nama . ' (Mulai 1 Slop). Mohon info stok & harga partai.') }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="btn btn-wa" 
                           style="padding: 10px 20px; text-align: center; font-size: 13px;">
                            <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terkait -->
        @if($terkait->count() > 0)
            <div style="margin-top: 50px;">
                <h3 style="font-family: var(--font-serif); color: var(--text-white); margin-bottom: 20px;">Varian Sejenis Lainnya</h3>
                <div class="products-grid">
                    @foreach($terkait as $t)
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <img src="{{ $t->image_url }}" alt="{{ $t->nama }}" class="product-img">
                            </div>
                            <div class="product-info">
                                <h4 class="product-title"><a href="{{ route('produk.detail', $t->slug) }}">{{ $t->nama }}</a></h4>
                                <div class="product-price-row">
                                    <div class="price-val">
                                        <strong style="color: var(--gold);">{{ $t->formatted_harga_slop }}</strong>
                                        <small style="color: #94a3b8; display: block; font-size: 11px;">{{ $t->formatted_harga }} / Bal</small>
                                    </div>
                                    <a href="{{ route('produk.detail', $t->slug) }}" class="btn btn-secondary btn-sm">Lihat</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
