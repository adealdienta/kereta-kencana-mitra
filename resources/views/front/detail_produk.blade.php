@extends('layouts.app')

@section('title', 'Spesifikasi ' . $produk->nama)

@section('content')
<section class="section-py">
    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="{{ route('katalog.index') }}" style="color: var(--text-medium); text-decoration: none; font-size: 14px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Katalog Produk
            </a>
        </div>

        <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: 2px; padding: 32px;">
            <!-- Gambar Produk Resmi (1 Foto Mewakili 1 Produk) -->
            <div>
                <div style="position: relative; background: var(--kk-ivory); border-radius: 2px; border: 1px solid var(--kk-ivory-border); padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 380px;">
                    <img id="detailMainImg" src="{{ $produk->image_url }}" alt="{{ $produk->nama }}" 
                         style="max-width: 100%; max-height: 360px; width: auto; height: auto; object-fit: contain; mix-blend-mode: multiply;">
                </div>

                <div style="margin-top: 16px; display: flex; gap: 10px; justify-content: space-between; align-items: center;">
                    <span class="category-tag category-{{ strtolower($produk->kategori->slug) }}" style="position: static;">
                        {{ $produk->kategori->nama_kategori }}
                    </span>
                </div>
            </div>

            <!-- Rincian Produk -->
            <div>
                <div class="font-mono" style="color: var(--kk-bronze); font-size: 13px; margin-bottom: 6px; font-weight: 600;">
                    KODE: {{ $produk->kode_barang }}
                </div>
                <h1 style="font-family: var(--font-serif); font-size: 32px; color: var(--text-dark); margin-bottom: 12px;">
                    {{ $produk->nama }}
                </h1>
                <p style="color: var(--text-medium); line-height: 1.68; margin-bottom: 24px;">
                    {{ $produk->deskripsi }}
                </p>

                <div style="background: var(--kk-ivory); border: 1px solid var(--kk-ivory-border); border-radius: 2px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="color: var(--text-dark); font-family: var(--font-serif); font-size: 16px; margin-bottom: 14px; border-bottom: 1px solid var(--kk-ivory-border); padding-bottom: 8px;">
                        Spesifikasi Teknis Hasil Tembakau
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 14px; color: var(--text-dark);">
                        <div><strong>Profil Rasa:</strong><br><span style="color: var(--text-medium);">{{ $produk->profil_rasa ?? '-' }}</span></div>
                        <div><strong>Kemasan Per Bungkus:</strong><br><span class="font-mono" style="color: var(--text-medium); font-size: 13px;">{{ $produk->batang_per_bungkus }} Batang</span></div>
                        <div><strong>Kemasan Per Slop:</strong><br><span class="font-mono" style="color: var(--text-medium); font-size: 13px;">{{ $produk->bungkus_per_slop }} Bungkus</span></div>
                        <div><strong>Batas Order Slop Online:</strong><br><span class="font-mono" style="color: var(--text-dark); font-weight: 600; font-size: 13px;">1 &ndash; {{ max(1, ($produk->slop_per_bal ?: 20) - 1) }} Slop</span></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 16px; background: #ffffff; padding: 20px 24px; border-radius: 2px; border: 1px solid var(--border-light); margin-bottom: 24px;">
                    <div>
                        <div style="margin-bottom: 10px;">
                            <span style="display: block; color: var(--text-muted); font-size: 12px;">Harga Satuan Slop (10 Bungkus):</span>
                            <span class="font-mono" style="font-size: 24px; color: var(--text-dark); font-weight: 600;">{{ $produk->formatted_harga_slop }}</span>
                            <span style="font-size: 12px; color: var(--text-muted);">/ Slop (1&ndash;5 Slop)</span>
                        </div>
                        <div class="font-mono" style="font-size: 12px; color: var(--text-dark); background: var(--kk-ivory); border: 1px solid var(--kk-ivory-border); padding: 8px 10px; margin-bottom: 8px; line-height: 1.55;">
                            <div>1&ndash;5 Slop &nbsp;: Rp 70.000 / Slop</div>
                            <div>6&ndash;10 Slop : Rp 65.000 / Slop</div>
                            <div>11&ndash;19 Slop: Rp 60.000 / Slop</div>
                        </div>
                        <div style="font-size: 12.5px; color: var(--text-medium); line-height: 1.5;">
                            Untuk pembelian skala Bal, hubungi admin untuk info lebih lanjut.
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; gap: 8px;">
                        <a href="{{ route('pesanan.form', ['produk' => $produk->slug]) }}" class="btn btn-gold" style="padding: 12px 20px; text-align: center;">
                            <i class="fa-solid fa-cart-shopping"></i> Order Online (Slop)
                        </a>
                        <a href="https://wa.me/{{ config('pabrik.wa_pabrik', '6285790627104') }}?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin menanyakan ketersediaan stok & pembelian skala Bal untuk rokok ' . $produk->nama . '.') }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="btn btn-outline-gold" 
                           style="padding: 10px 20px; text-align: center; font-size: 13px;">
                            <i class="fa-brands fa-whatsapp" style="color: #16a34a;"></i> Tanya Stok &amp; Skala Bal via WA
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terkait -->
        @if($terkait->count() > 0)
            <div style="margin-top: 50px;">
                <h3 style="font-family: var(--font-serif); color: var(--text-dark); margin-bottom: 20px;">Varian Sejenis Lainnya</h3>
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
                                        <strong class="font-mono" style="color: var(--text-dark);">{{ $t->formatted_harga_slop }} / Slop</strong>
                                        <small style="color: var(--text-muted); display: block; font-size: 11px; line-height: 1.4;">
                                            1&ndash;5 Slop: Rp 70.000 &bull; 6&ndash;10: Rp 65.000 &bull; &gt;10: Rp 60.000
                                        </small>
                                    </div>
                                    <a href="{{ route('produk.detail', $t->slug) }}" class="btn btn-secondary btn-sm">Detail</a>
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

