@extends('layouts.app')

@section('title', 'Katalog Varian Sigaret Kretek Tangan (SKT)')

@section('content')
<!-- Header Banner -->
<section class="page-header">
    <div class="container">
        <span class="header-badge">Katalog Produk Resmi</span>
        <h1 class="page-title">Varian Sigaret Kretek Tangan</h1>
        <p class="page-subtitle" style="margin: 0;">3 Varian SKT 12 Batang Resmi Pabrik PR. KERETA KENCANA Blitar</p>
    </div>
</section>

<section class="section-py">
    <div class="container">
        <!-- Filter & Search Bar -->
        <div class="katalog-filter-bar scroll-reveal reveal-up" style="background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: 2px; padding: 14px 16px; margin-bottom: 28px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
            <div class="filter-categories" style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('katalog.index') }}" class="btn btn-sm {{ !request('kategori') ? 'btn-gold' : 'btn-secondary' }}">
                    Semua Varian
                </a>
                @foreach($kategoris as $k)
                    <a href="{{ route('katalog.index', ['kategori' => $k->slug, 'q' => request('q')]) }}" 
                       class="btn btn-sm {{ request('kategori') === $k->slug ? 'btn-gold' : 'btn-secondary' }}">
                        {{ $k->nama_kategori }} ({{ $k->barangs_count }})
                    </a>
                @endforeach
            </div>

            <form action="{{ route('katalog.index') }}" method="GET" style="display: flex; gap: 8px;">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari varian atau rasa..." 
                       style="padding: 8px 14px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px; font-size: 14px;">
                <button type="submit" class="btn btn-gold btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            </form>
        </div>

        <!-- Products Grid -->
        @if($produks->count() > 0)
            <div class="products-grid">
                @foreach($produks as $p)
                    <div class="product-card scroll-reveal reveal-zoom reveal-delay-{{ ($loop->index % 3) + 1 }}" style="border-radius: 2px; box-shadow: none;">
                        <!-- Main Product Image Wrapper (1 Foto Mewakili 1 Produk) -->
                        <div class="product-img-wrapper" style="position: relative; display: flex; align-items: center; justify-content: center;">
                            <a href="{{ route('produk.detail', $p->slug) }}" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; text-decoration: none;">
                                <img src="{{ $p->image_url }}" alt="{{ $p->nama }}" class="product-img" id="prod-main-img-{{ $p->id }}" style="margin: 0 auto; display: block; object-fit: contain;">
                            </a>
                            
                            <span class="category-tag category-{{ strtolower($p->kategori->slug) }}">
                                {{ $p->kategori->nama_kategori }}
                            </span>
                        </div>

                        <div class="product-info">
                            <div class="product-code font-mono">{{ $p->kode_barang }}</div>
                            <h3 class="product-title"><a href="{{ route('produk.detail', $p->slug) }}">{{ $p->nama }}</a></h3>
                            <p class="product-profile">{{ $p->profil_rasa }}</p>
                            
                            <div class="product-specs-mini font-mono" style="font-size: 0.76rem;">
                                <span>{{ $p->batang_per_bungkus }} Btg/Bks</span>
                                <span>{{ $p->bungkus_per_slop }} Bks/Slop</span>
                            </div>

                            <div class="product-price-row">
                                <div class="price-val">
                                    <small>Harga per Slop (10 Bks):</small>
                                    <strong class="font-mono" style="color: var(--text-dark); font-size: 16px;">{{ $p->formatted_harga_slop }}</strong>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px; line-height: 1.45;">
                                        1&ndash;5 Slop: Rp 70.000 &bull; 6&ndash;10 Slop: Rp 65.000 &bull; &gt;10 Slop: Rp 60.000.<br>
                                        Untuk pembelian skala Bal, hubungi admin untuk info lebih lanjut.
                                    </div>
                                </div>
                                <div style="display: flex; gap: 6px; flex-shrink: 0;">
                                    <a href="{{ route('produk.detail', $p->slug) }}" class="btn btn-secondary btn-sm" title="Detail Spesifikasi">
                                        Detail
                                    </a>
                                    <a href="{{ route('pesanan.form', ['produk' => $p->slug]) }}" class="btn btn-gold btn-sm">
                                        Order
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 30px; display: flex; justify-content: center;">
                {{ $produks->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px; background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px dashed var(--border-light); box-shadow: var(--shadow-sm);">
                <i class="fa-solid fa-box-open" style="font-size: 48px; color: var(--gold); margin-bottom: 16px;"></i>
                <h3 style="color: var(--text-dark); margin-bottom: 8px;">Tidak Ada Produk Ditemukan</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Silakan atur ulang kata kunci pencarian atau pilih kategori lain.</p>
                <a href="{{ route('katalog.index') }}" class="btn btn-gold btn-sm">Lihat Seluruh Katalog</a>
            </div>
        @endif
    </div>
</section>

@endsection

