@extends('layouts.app')

@section('title', 'Katalog Varian Sigaret Kretek (SKM & SKT)')

@section('content')
<!-- Header Banner -->
<section class="page-header" style="background: linear-gradient(rgba(13, 16, 18, 0.85), rgba(13, 16, 18, 0.95)), url('{{ asset('assets/img/hero-pabrik.jpg') }}') center/cover no-repeat; padding: 60px 0;">
    <div class="container text-center">
        <span class="header-badge">KATALOG PRODUK B2B</span>
        <h1 class="page-title">VARIAN SIGARET KRETEK RESMI</h1>
        <p class="page-subtitle">Pilihan Sigaret Kretek Tangan (SKT) Resmi Pabrik: Lini DWIPANTARA, SEMBADA, dan KERETA KENCANA</p>
    </div>
</section>

<section class="section-py">
    <div class="container">
        <!-- Filter & Search Bar (BKPM Acara 23) -->
        <div class="katalog-filter-bar" style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius); padding: 16px; margin-bottom: 30px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
            <div class="filter-categories" style="display: flex; gap: 8px;">
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
                       style="padding: 8px 14px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px; font-size: 14px;">
                <button type="submit" class="btn btn-gold btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            </form>
        </div>

        <!-- Products Grid -->
        @if($produks->count() > 0)
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
                            <h3 class="product-title"><a href="{{ route('produk.detail', $p->slug) }}">{{ $p->nama }}</a></h3>
                            <p class="product-profile"><i class="fa-solid fa-tag"></i> {{ $p->profil_rasa }}</p>
                            
                            <div class="product-specs-mini">
                                <span><i class="fa-solid fa-smoking"></i> {{ $p->batang_per_bungkus }} Btg/Bks</span>
                                <span><i class="fa-solid fa-cubes"></i> {{ $p->bungkus_per_slop }} Bks/Slop</span>
                                <span><i class="fa-solid fa-boxes-packing"></i> {{ $p->slop_per_bal }} Slop/Bal</span>
                            </div>

                            <div class="product-price-row">
                                <div class="price-val">
                                    <small>Mulai 1 Slop (10 Bks):</small>
                                    <strong style="color: var(--gold); font-size: 16px;">{{ $p->formatted_harga_slop }}</strong>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">Grosir: {{ $p->formatted_harga }}/Bal</div>
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <a href="{{ route('produk.detail', $p->slug) }}" class="btn btn-secondary btn-sm" title="Detail Spesifikasi">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </a>
                                    <a href="{{ route('pesanan.form', ['produk' => $p->slug]) }}" class="btn btn-gold btn-sm">
                                        <i class="fa-solid fa-cart-shopping"></i> Order
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
            <div style="text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px dashed var(--charcoal-border);">
                <i class="fa-solid fa-box-open" style="font-size: 48px; color: var(--gold); margin-bottom: 16px;"></i>
                <h3 style="color: var(--text-light); margin-bottom: 8px;">Tidak Ada Produk Ditemukan</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Silakan atur ulang kata kunci pencarian atau pilih kategori lain.</p>
                <a href="{{ route('katalog.index') }}" class="btn btn-gold btn-sm">Lihat Seluruh Katalog</a>
            </div>
        @endif
    </div>
</section>
@endsection
