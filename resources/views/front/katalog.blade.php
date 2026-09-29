@extends('layouts.app')

@section('title', 'Katalog Varian Sigaret Kretek (SKM & SKT)')

@section('content')
<!-- Header Banner -->
<section class="page-header" style="background: radial-gradient(ellipse at top, rgba(197, 160, 89, 0.12) 0%, transparent 60%), linear-gradient(180deg, #0d1012 0%, #15191d 100%); border-bottom: 1px solid rgba(197, 160, 89, 0.2); padding: 60px 0;">
    <div class="container text-center">
        <span class="header-badge">KATALOG PRODUK RESMI</span>
        <h1 class="page-title">VARIAN SIGARET KRETEK RESMI</h1>
        <p class="page-subtitle">3 Varian Sigaret Kretek Tangan (SKT) 12 Batang Resmi Pabrik PR. KERETA KENCANA Blitar</p>
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
                        <!-- Main Product Image Wrapper with Label & Zoom Button -->
                        <div class="product-img-wrapper" style="position: relative;">
                            <img src="{{ $p->gallery_images[0]['url'] ?? $p->image_url }}" alt="{{ $p->nama }}" class="product-img" id="prod-main-img-{{ $p->id }}" style="transition: opacity 0.25s ease;">
                            
                            <span class="category-tag category-{{ strtolower($p->kategori->slug) }}">
                                {{ $p->kategori->nama_kategori }}
                            </span>
                            <span class="stock-tag stock-{{ Str::slug($p->status_stok) }}">
                                {{ $p->status_stok }}
                            </span>

                            <span class="view-label-badge" id="prod-label-{{ $p->id }}">
                                <i class="fa-solid fa-camera"></i> {{ $p->gallery_images[0]['badge'] ?? 'Bungkus' }}
                            </span>

                            <button type="button" class="btn-open-gallery-zoom" onclick="openProductLightbox('{{ $p->id }}')" title="Buka Galeri Foto (4 Foto Asli)">
                                <i class="fa-solid fa-expand"></i>
                            </button>
                        </div>

                        <!-- 4 Thumbnails Selector (Inspirasi Wismilak) -->
                        @if(count($p->gallery_images) > 1)
                            <div class="product-gallery-selector">
                                @foreach($p->gallery_images as $idx => $img)
                                    <button type="button" 
                                            class="product-gallery-thumb {{ $idx === 0 ? 'active' : '' }}" 
                                            data-prod-id="{{ $p->id }}"
                                            data-img-url="{{ $img['url'] }}"
                                            data-img-badge="{{ $img['badge'] }}"
                                            onclick="switchProductCardImage('{{ $p->id }}', '{{ $img['url'] }}', '{{ $img['badge'] }}', this)" 
                                            title="{{ $img['label'] }} (Klik untuk pratinjau)">
                                        <img src="{{ $img['url'] }}" alt="{{ $img['label'] }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif

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

<!-- Modal Galeri Lightbox (4 Foto Produk Resolusi Penuh) -->
<div id="productLightboxModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(5, 7, 9, 0.92); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
    <div style="position: relative; max-width: 760px; width: 100%; background: #13171b; border: 1px solid rgba(197, 160, 89, 0.4); border-radius: 14px; overflow: hidden; box-shadow: 0 24px 60px rgba(0,0,0,0.8);">
        
        <!-- Header Modal -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid var(--charcoal-border); background: #0e1215;">
            <div>
                <h4 id="lightboxTitle" style="color: #ffffff; font-family: var(--font-serif); margin: 0; font-size: 1.15rem;">Galeri Produk</h4>
                <span id="lightboxLabel" style="color: var(--gold); font-size: 0.82rem; font-weight: 600;">Bungkus Utama</span>
            </div>
            <button type="button" onclick="closeProductLightbox()" style="background: none; border: none; color: #94a3b8; font-size: 26px; cursor: pointer; line-height: 1; padding: 0;" aria-label="Tutup Galeri">
                &times;
            </button>
        </div>

        <!-- Main Lightbox Display -->
        <div style="position: relative; background: #ffffff; height: 420px; display: flex; align-items: center; justify-content: center; padding: 20px; overflow: hidden;">
            <img id="lightboxMainImg" src="" alt="Pratinjau Foto" style="max-height: 100%; max-width: 100%; width: auto; height: auto; object-fit: contain; transition: transform 0.25s ease;">
            
            <!-- Arrow Prev & Next -->
            <button type="button" onclick="prevLightboxImg()" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; background: rgba(19, 23, 27, 0.8); border: 1px solid rgba(197, 160, 89, 0.5); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" onclick="nextLightboxImg()" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; background: rgba(19, 23, 27, 0.8); border: 1px solid rgba(197, 160, 89, 0.5); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <!-- Lightbox Thumbnails Strip -->
        <div id="lightboxThumbStrip" style="display: flex; gap: 8px; padding: 12px 20px; background: #0e1215; border-top: 1px solid var(--charcoal-border); justify-content: center; overflow-x: auto;">
            <!-- Rendered dynamically -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productsGalleryData = {
        @foreach($produks as $p)
            '{{ $p->id }}': {
                name: @json($p->nama),
                images: @json($p->gallery_images)
            },
        @endforeach
    };

    let activeLightboxProdId = null;
    let activeLightboxImgIdx = 0;

    // Switch image on catalog product card
    function switchProductCardImage(prodId, imgUrl, badgeText, btnElement) {
        const mainImg = document.getElementById('prod-main-img-' + prodId);
        const labelBadge = document.getElementById('prod-label-' + prodId);

        if (mainImg) {
            mainImg.style.opacity = '0.35';
            setTimeout(() => {
                mainImg.src = imgUrl;
                mainImg.style.opacity = '1';
            }, 120);
        }

        if (labelBadge) {
            labelBadge.innerHTML = '<i class="fa-solid fa-camera"></i> ' + badgeText;
        }

        if (btnElement && btnElement.parentElement) {
            const siblings = btnElement.parentElement.querySelectorAll('.product-gallery-thumb');
            siblings.forEach(s => s.classList.remove('active'));
            btnElement.classList.add('active');
        }
    }

    // Lightbox Modal Functions
    function openProductLightbox(prodId, index = 0) {
        const prod = productsGalleryData[prodId];
        if (!prod || !prod.images.length) return;

        activeLightboxProdId = prodId;
        activeLightboxImgIdx = index;

        document.getElementById('lightboxTitle').textContent = prod.name;
        updateLightboxView();

        const modal = document.getElementById('productLightboxModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function updateLightboxView() {
        const prod = productsGalleryData[activeLightboxProdId];
        if (!prod) return;

        const imgData = prod.images[activeLightboxImgIdx];
        document.getElementById('lightboxMainImg').src = imgData.url;
        document.getElementById('lightboxLabel').textContent = imgData.label;

        // Render Thumbnails
        const thumbStrip = document.getElementById('lightboxThumbStrip');
        thumbStrip.innerHTML = '';

        prod.images.forEach((img, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'product-gallery-thumb' + (i === activeLightboxImgIdx ? ' active' : '');
            btn.style.width = '46px';
            btn.style.height = '46px';
            btn.style.flex = 'none';
            btn.title = img.label;
            btn.innerHTML = `<img src="${img.url}" alt="${img.label}">`;
            btn.onclick = () => {
                activeLightboxImgIdx = i;
                updateLightboxView();
            };
            thumbStrip.appendChild(btn);
        });
    }

    function prevLightboxImg() {
        const prod = productsGalleryData[activeLightboxProdId];
        if (!prod) return;
        activeLightboxImgIdx = (activeLightboxImgIdx - 1 + prod.images.length) % prod.images.length;
        updateLightboxView();
    }

    function nextLightboxImg() {
        const prod = productsGalleryData[activeLightboxProdId];
        if (!prod) return;
        activeLightboxImgIdx = (activeLightboxImgIdx + 1) % prod.images.length;
        updateLightboxView();
    }

    function closeProductLightbox() {
        document.getElementById('productLightboxModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on Escape or click outside
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeProductLightbox();
        if (e.key === 'ArrowLeft') prevLightboxImg();
        if (e.key === 'ArrowRight') nextLightboxImg();
    });

    document.getElementById('productLightboxModal').addEventListener('click', (e) => {
        if (e.target.id === 'productLightboxModal') {
            closeProductLightbox();
        }
    });
</script>
@endpush

