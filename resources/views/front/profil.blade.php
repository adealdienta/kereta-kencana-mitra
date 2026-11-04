@extends('layouts.app')

@section('title', 'Profil Perusahaan & Legalitas')

@section('content')
<!-- Header Banner -->
<section class="page-header">
    <div class="container text-center">
        <span class="header-badge">COMPANY PROFILE & LEGALITAS</span>
        <h1 class="page-title">PR. KERETA KENCANA BLITAR</h1>
        <p class="page-subtitle">Dedikasi Mutu Hasil Tembakau Nusantara dari Ponggok, Kabupaten Blitar, Jawa Timur</p>
    </div>
</section>

<!-- Tentang Kami & Sejarah Perusahaan (Tata Letak Editorial Korporat PR. Kereta Kencana) -->
<section class="section-py" style="background: #ffffff;">
    <div class="container">
        <div class="profil-editorial-grid">
            <!-- Kolom Kiri: Narasi Editorial Tentang Kami & Sejarah -->
            <div class="profil-story-col scroll-reveal reveal-up">
                <div class="profil-story-block">
                    <h2 class="profil-heading">Tentang Kami</h2>
                    <p class="profil-paragraph">
                        <strong>PR. KERETA KENCANA</strong> bermula dari sebuah dedikasi sederhana di Kabupaten Blitar, Jawa Timur, dan terus bertumbuh menjadi produsen Sigaret Kretek Tangan (SKT) yang dipercaya oleh jaringan mitra distributor, agen grosir, hingga toko ritel di berbagai wilayah.
                    </p>
                    <p class="profil-paragraph">
                        Dengan memadukan keahlian pelintingan tradisional dan kontrol mutu bahan baku yang ketat, kami memproduksi tiga varian utama kretek 12 batang yaitu <strong>SEMBADA</strong>, <strong>KERETA KENCANA</strong>, dan <strong>SEMBADA CETHE</strong>. Konsistensi cita rasa rempah Nusantara serta kepuasan mitra usaha senantiasa menjadi prioritas utama dalam setiap langkah operasional kami.
                    </p>
                </div>

                <div class="profil-story-block">
                    <h2 class="profil-heading">Sejarah & Perjalanan Pabrik</h2>
                    <p class="profil-paragraph">
                        Didirikan dan beroperasi di Dusun Subontoro, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, perjalanan <strong>PR. KERETA KENCANA</strong> dipimpin langsung oleh <strong>Bapak Komari Yaman</strong>. Sejak awal berdirinya, pabrik ini dibangun di atas fondasi kemurnian racikan daun tembakau pegunungan Jawa dan bunga cengkeh pilihan yang diolah secara teliti.
                    </p>
                    <p class="profil-paragraph">
                        Seiring meningkatnya permintaan pasar, perusahaan terus memperkuat kapasitas produksi padat karya dengan memberdayakan masyarakat lokal sekitar Ponggok, sekaligus mematuhi seluruh perizinan industri hasil tembakau serta pelekatan pita cukai resmi di bawah pengawasan KPPBC Bea Cukai Blitar.
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan: Visual Gedung Pabrik & 4 Kotak Fakta Kunci Korporasi -->
            <div class="profil-visual-col scroll-reveal reveal-zoom reveal-delay-1">
                @if(file_exists(public_path('assets/img/pabrik-ponggok.jpg')))
                    <div class="photo-frame-modern" style="min-height: 330px; border: 1px solid var(--border-light);">
                        <img src="{{ asset('assets/img/pabrik-ponggok.jpg') }}?v=2" alt="Pabrik PR. Kereta Kencana Ponggok" class="photo-frame-img" style="height: 330px;">
                        <div class="photo-frame-caption">
                            <i class="fa-solid fa-location-dot" style="color: var(--gold);"></i> Pabrik PR. KERETA KENCANA &bull; Ponggok, Kab. Blitar
                        </div>
                    </div>
                @else
                    <div class="photo-placeholder-slot" style="min-height: 330px;">
                        <div class="photo-placeholder-icon" style="width: 60px; height: 60px; font-size: 22px;">
                            <i class="fa-solid fa-building-shield"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Tampak Depan & Papan Nama Pabrik</div>
                        <div class="photo-placeholder-sub" style="max-width: 380px;">
                            Dokumentasi gedung utama dan plang resmi <strong>PR. KERETA KENCANA</strong> di Dusun Subontoro, Desa Kebonduren, Kec. Ponggok, Kab. Blitar.
                        </div>
                        <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/pabrik-ponggok.jpg</span>
                    </div>
                @endif

                <div class="profil-fakta-grid">
                    <div class="profil-fakta-card">
                        <div class="profil-fakta-label">Lokasi Operasional</div>
                        <div class="profil-fakta-value">Kec. Ponggok, Kab. Blitar</div>
                    </div>
                    <div class="profil-fakta-card">
                        <div class="profil-fakta-label">Pimpinan Perusahaan</div>
                        <div class="profil-fakta-value">Bapak Komari Yaman</div>
                    </div>
                    <div class="profil-fakta-card">
                        <div class="profil-fakta-label">Lini Produk Utama</div>
                        <div class="profil-fakta-value">3 Varian SKT (12 Batang)</div>
                    </div>
                    <div class="profil-fakta-card">
                        <div class="profil-fakta-label">Skala Pasokan Mitra</div>
                        <div class="profil-fakta-value">Mulai 1 Slop hingga Bal</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visi & Misi (Tata Letak Atas-Bawah Sejajar Penuh) -->
<section class="section-py" style="background: var(--bg-surface-subtle); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light); position: relative; overflow: hidden;">
    <div class="container" style="position: relative; z-index: 1;">
        <!-- Header Pemisah Visi & Misi -->
        <div class="section-header text-center scroll-reveal reveal-up" style="margin-bottom: 48px;">
            <span class="eyebrow">LANDASAN STRATEGIS PABRIK</span>
            <h2 class="section-title">Visi & Misi Perusahaan</h2>
            <div class="gold-divider"></div>
            <p class="section-desc" style="margin: 0 auto; color: var(--text-medium);">
                Prinsip arah dan komitmen mutu PR. KERETA KENCANA dalam melayani pasokan hasil tembakau dan kemitraan distributor di seluruh wilayah.
            </p>
        </div>

        <div class="visi-misi-stack">
            <!-- Kotak Atas: Visi Perusahaan (Full Width Sejajar) -->
            <div class="visi-card-stacked scroll-reveal reveal-up">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(180, 136, 52, 0.10); border: 1px solid rgba(180, 136, 52, 0.25); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 20px; flex-shrink: 0;">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <div>
                        <span class="eyebrow" style="margin-bottom: 2px; font-size: 0.7rem;">ARAH UTAMA KORPORASI</span>
                        <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 1.35rem; font-weight: 800; margin: 0;">
                            Visi Perusahaan
                        </h3>
                    </div>
                </div>
                <p style="color: var(--text-dark); font-size: 1.05rem; font-weight: 500; line-height: 1.85; margin: 0; padding-top: 16px; border-top: 1px solid var(--border-light);">
                    "Menjadi produsen sigaret kretek terpercaya di tingkat nasional yang dikenal akan konsistensi cita rasa, kepatuhan regulasi industri hasil tembakau, serta kemitraan bisnis yang adil dan berkelanjutan bagi distributor daerah."
                </p>
            </div>

            <!-- Kotak Bawah: Misi Perusahaan (Full Width Sejajar dengan Visi) -->
            <div class="misi-card-stacked scroll-reveal reveal-up reveal-delay-1">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 6px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(180, 136, 52, 0.10); border: 1px solid rgba(180, 136, 52, 0.25); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 20px; flex-shrink: 0;">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <div>
                        <span class="eyebrow" style="margin-bottom: 2px; font-size: 0.7rem;">PILAR PELAKSANAAN OPERASIONAL</span>
                        <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 1.35rem; font-weight: 800; margin: 0;">
                            Misi Perusahaan
                        </h3>
                    </div>
                </div>

                <div class="misi-points-grid">
                    <div class="misi-point-box">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--gold-hover); font-weight: 800; font-size: 0.82rem; letter-spacing: 1px; text-transform: uppercase;">
                            <i class="fa-solid fa-circle-check" style="color: var(--gold);"></i> MISI 01 &bull; MUTU RACIKAN
                        </div>
                        <p style="color: var(--text-medium); font-size: 0.94rem; line-height: 1.65; margin: 0;">
                            Menjaga kemurnian racikan tembakau pegunungan dan cengkeh pilihan asli Nusantara demi mempertahankan karakter rasa kretek khas Blitar.
                        </p>
                    </div>

                    <div class="misi-point-box">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--gold-hover); font-weight: 800; font-size: 0.82rem; letter-spacing: 1px; text-transform: uppercase;">
                            <i class="fa-solid fa-circle-check" style="color: var(--gold);"></i> MISI 02 &bull; KEPATUHAN HUKUM
                        </div>
                        <p style="color: var(--text-medium); font-size: 0.94rem; line-height: 1.65; margin: 0;">
                            Mematuhi seluruh regulasi perizinan cukai negara (NPPBKC & NIB Resmi) serta standarisasi batas kadar tar dan nikotin pada setiap varian produk.
                        </p>
                    </div>

                    <div class="misi-point-box">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--gold-hover); font-weight: 800; font-size: 0.82rem; letter-spacing: 1px; text-transform: uppercase;">
                            <i class="fa-solid fa-circle-check" style="color: var(--gold);"></i> MISI 03 &bull; JARINGAN MITRA
                        </div>
                        <p style="color: var(--text-medium); font-size: 0.94rem; line-height: 1.65; margin: 0;">
                            Mengembangkan jaringan distribusi terstruktur berbasis pasokan berkelanjutan dan kemudahan pemesanan mulai dari 1 slop hingga kemasan bal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Legalitas Cukai & Izin Usaha Resmi -->
<section class="section-py">
    <div class="container">
        <div class="section-header text-center scroll-reveal reveal-up">
            <span class="section-subtitle">KEPATUHAN REGULASI NEGARA</span>
            <h2 class="section-title">Legalitas & Perizinan Cukai Resmi</h2>
            <div class="gold-divider"></div>
            <p class="section-desc" style="margin: 0 auto;">Seluruh produk PR. KERETA KENCANA diproduksi dengan pita cukai resmi negara dan terdaftar di kementerian terkait.</p>
        </div>

        <div class="scroll-reveal reveal-zoom" style="overflow-x: auto; background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 12px; box-shadow: var(--shadow-sm);">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; color: var(--text-dark);">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-light); background: var(--bg-surface-subtle); color: var(--text-dark);">
                        <th style="padding: 14px;">Nama Dokumen Perizinan</th>
                        <th style="padding: 14px;">Nomor Dokumen</th>
                        <th style="padding: 14px;">Instansi Penerbit</th>
                        <th style="padding: 14px;">Masa Berlaku</th>
                        <th style="padding: 14px;">Status Legal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($legalitas as $doc)
                        <tr style="border-bottom: 1px solid var(--border-subtle);">
                            <td style="padding: 14px; font-weight: 600;">{{ $doc->nama_dokumen }}</td>
                            <td style="padding: 14px; font-family: monospace; color: var(--gold-hover); font-weight: 700;">{{ $doc->nomor ?? '-' }}</td>
                            <td style="padding: 14px; color: var(--text-medium);">{{ $doc->penerbit ?? '-' }}</td>
                            <td style="padding: 14px; color: var(--text-medium);">{{ $doc->berlaku_sampai ? $doc->berlaku_sampai->format('d M Y') : 'Berlaku Selama Operasional' }}</td>
                            <td style="padding: 14px;">
                                <span style="background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    <i class="fa-solid fa-circle-check"></i> {{ $doc->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- 4 Pilar Operasional & Fasilitas Pabrik (Grid 2x2 PR. Kereta Kencana) -->
<section class="section-py" style="background: var(--bg-surface-subtle); border-top: 1px solid var(--border-light);">
    <div class="container">
        <div class="section-header text-center scroll-reveal reveal-up" style="margin-bottom: 52px;">
            <span class="eyebrow">PILAR OPERASIONAL & INFRASTRUKTUR</span>
            <h2 class="section-title">Fasilitas Produksi & Komitmen Mutu</h2>
            <div class="gold-divider"></div>
            <p class="section-desc" style="margin: 0 auto; color: var(--text-medium);">
                Empat pilar utama operasional PR. KERETA KENCANA di Ponggok, Kabupaten Blitar dalam menjaga kualitas racikan produk, pemberdayaan sumber daya manusia, kepatuhan cukai, dan jangkauan pasar.
            </p>
        </div>

        <div class="profil-pilar-grid">
            <!-- Pilar 01: Bahan Baku & Inovasi Racikan -->
            <div class="profil-pilar-card scroll-reveal reveal-up">
                <div class="profil-pilar-media">
                    @if(file_exists(public_path('assets/img/fasilitas-tembakau.jpg')))
                        <div class="photo-frame-modern">
                            <img src="{{ asset('assets/img/fasilitas-tembakau.jpg') }}?v=2" alt="Gudang Bahan Baku & Racikan Tembakau" class="photo-frame-img">
                            <div class="photo-frame-caption">
                                <i class="fa-solid fa-seedling" style="color: var(--gold);"></i> Gudang Penyimpanan Tembakau & Cengkeh
                            </div>
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-seedling"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Gudang Bahan Baku Tembakau & Cengkeh</div>
                            <div class="photo-placeholder-sub">Dokumentasi penyimpanan daun tembakau pegunungan Jawa & cengkeh kering pilihan di pabrik.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/fasilitas-tembakau.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">PILAR 01 &bull; KUALITAS BAHAN BAKU</div>
                    <h3 class="profil-pilar-title">Gudang Penyimpanan & Racikan Tembakau</h3>
                    <p class="profil-pilar-desc">
                        Kami menyeleksi daun tembakau pegunungan Jawa berkualitas tinggi yang dipadukan dengan cengkeh Nusantara beraroma harum. Seluruh bahan baku disimpan pada ruang khusus bersuhu terkontrol dan diperam bersama saus racikan khas pabrik untuk menghasilkan hisapan yang mantap dan konsisten.
                    </p>
                </div>
            </div>

            <!-- Pilar 02: Sumber Daya Manusia (SDM) & Lini SKT -->
            <div class="profil-pilar-card scroll-reveal reveal-up reveal-delay-1">
                <div class="profil-pilar-media">
                    @if(file_exists(public_path('assets/img/galeri-produksi-skt.jpg')))
                        <div class="photo-frame-modern">
                            <img src="{{ asset('assets/img/galeri-produksi-skt.jpg') }}?v=2" alt="Ruang Produksi Linting SKT" class="photo-frame-img">
                            <div class="photo-frame-caption">
                                <i class="fa-solid fa-hands-holding-circle" style="color: var(--gold);"></i> Aktivitas Lini Produksi SKT
                            </div>
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-hands-holding-circle"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Ruang Produksi Linting SKT</div>
                            <div class="photo-placeholder-sub">Dokumentasi aktivitas tenaga kerja pelinting Sigaret Kretek Tangan (SKT) di ruang produksi.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/galeri-produksi-skt.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">PILAR 02 &bull; SUMBER DAYA MANUSIA (SDM)</div>
                    <h3 class="profil-pilar-title">Ruang Pelintingan Sigaret Kretek Tangan</h3>
                    <p class="profil-pilar-desc">
                        Sumber daya manusia merupakan pilar utama keberhasilan produksi kami. Proses pelintingan SKT 12 batang dikerjakan langsung oleh tangan-tangan terampil masyarakat Kecamatan Ponggok, Kabupaten Blitar, dengan penempatan tim kerja terlatih guna menjaga kepadatan dan kerapian setiap batang.
                    </p>
                </div>
            </div>

            <!-- Pilar 03: Kontrol Mutu & Kepatuhan Cukai -->
            <div class="profil-pilar-card scroll-reveal reveal-up">
                <div class="profil-pilar-media">
                    @if(file_exists(public_path('assets/img/fasilitas-cukai.jpg')))
                        <div class="photo-frame-modern">
                            <img src="{{ asset('assets/img/fasilitas-cukai.jpg') }}?v=2" alt="Pengawasan Cukai & Pengepakan" class="photo-frame-img">
                            <div class="photo-frame-caption">
                                <i class="fa-solid fa-shield-halved" style="color: var(--gold);"></i> Pelekatan Pita Cukai & Pengemasan
                            </div>
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Pelekatan Pita Cukai & Pengemasan Slop</div>
                            <div class="photo-placeholder-sub">Dokumentasi inspeksi mutu bungkus rokok & pelekatan pita cukai resmi Bea Cukai Blitar.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/fasilitas-cukai.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">PILAR 03 &bull; KONTROL KUALITAS & REGULASI</div>
                    <h3 class="profil-pilar-title">Inspeksi Mutu & Pelekatan Pita Cukai</h3>
                    <p class="profil-pilar-desc">
                        Kerangka kontrol kualitas yang ketat diterapkan pada setiap bungkus rokok sebelum dikemas ke dalam satuan slop (10 bungkus). Seluruh produk dilekati pita cukai asli Republik Indonesia di bawah pengawasan KPPBC Bea Cukai Blitar demi menjamin kepastian hukum bagi para mitra.
                    </p>
                </div>
            </div>

            <!-- Pilar 04: Pasar & Jaringan Distribusi -->
            <div class="profil-pilar-card scroll-reveal reveal-up reveal-delay-1">
                <div class="profil-pilar-media">
                    @if(file_exists(public_path('assets/img/fasilitas-gudang.jpg')))
                        <div class="photo-frame-modern">
                            <img src="{{ asset('assets/img/fasilitas-gudang.jpg') }}?v=2" alt="Gudang Barang Jadi & Distribusi" class="photo-frame-img">
                            <div class="photo-frame-caption">
                                <i class="fa-solid fa-warehouse" style="color: var(--gold);"></i> Gudang Stok & Armada Pengiriman
                            </div>
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Gudang Barang Jadi & Armada Distribusi</div>
                            <div class="photo-placeholder-sub">Dokumentasi kesiapan stok slop/bal di gudang dan armada pengiriman menuju mitra.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/fasilitas-gudang.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">PILAR 04 &bull; PASAR & JARINGAN DISTRIBUSI</div>
                    <h3 class="profil-pilar-title">Fasilitas Gudang & Pengiriman Mitra</h3>
                    <p class="profil-pilar-desc">
                        Fasilitas gudang barang jadi kami menjaga ketersediaan stok varian <strong>SEMBADA</strong>, <strong>KERETA KENCANA</strong>, dan <strong>SEMBADA CETHE</strong> sepanjang waktu untuk memenuhi permintaan pasar domestik, mulai dari pesanan 1 slop bagi toko ritel hingga kemasan Bal (20 slop) bagi agen dan distributor.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
