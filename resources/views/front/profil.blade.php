@extends('layouts.app')

@section('title', 'Profil Perusahaan & Legalitas')

@section('content')
<!-- Header Banner -->
<section class="page-header" style="background: linear-gradient(rgba(13, 16, 18, 0.85), rgba(13, 16, 18, 0.95)), url('{{ asset('assets/img/hero-pabrik.jpg') }}') center/cover no-repeat; padding: 70px 0;">
    <div class="container text-center">
        <span class="header-badge">COMPANY PROFILE & LEGALITAS</span>
        <h1 class="page-title">PR. KERETA KENCANA BLITAR</h1>
        <p class="page-subtitle">Dedikasi Mutu Hasil Tembakau Nusantara dari Ponggok, Kabupaten Blitar, Jawa Timur</p>
    </div>
</section>

<!-- Sejarah & Visi Misi -->
<section class="section-py">
    <div class="container">
        <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
            <div>
                <span class="section-subtitle">SEJARAH & IDENTITAS PABRIK</span>
                <h2 class="section-title">WARISAN LINTINGAN TRADISIONAL HINGGA INDUSTRI MODERN</h2>
                <div class="gold-divider" style="margin-left: 0;"></div>
                <p style="color: var(--text-light); line-height: 1.8; margin-bottom: 16px;">
                    Didirikan di Dusun Subontoro, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, <strong>PR. KERETA KENCANA</strong> berawal dari dedikasi mendalam terhadap cita rasa rokok kretek asli Jawa Timur. Dimulai dari produksi sigaret kretek tangan linting manual, pabrik kami kini telah berkembang memenuhi kapasitas pasar melalui perpaduan lini Sigaret Kretek Mesin (SKM) presisi dan Sigaret Kretek Tangan (SKT) padat rempah.
                </p>
                <p style="color: var(--text-light); line-height: 1.8;">
                    Dipimpin oleh Bapak Komari Yaman, kami terus memegang teguh komitmen integritas cukai resmi negara, penyerapan tenaga kerja lokal daerah, serta standarisasi mutu daun tembakau dan cengkeh bermutu tinggi.
                </p>
            </div>
            <div>
                <img src="{{ asset('assets/img/produksi-skt.jpg') }}" alt="Produksi Pabrik Kereta Kencana" style="width: 100%; border-radius: var(--radius-lg); border: 1px solid var(--charcoal-border); box-shadow: var(--shadow-md);">
            </div>
        </div>
    </div>
</section>

<!-- Visi & Misi Cards -->
<section class="section-py bg-darker">
    <div class="container">
        <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-compass"></i></div>
                <h3>Visi Perusahaan</h3>
                <p>Menjadi produsen sigaret kretek terpercaya di tingkat nasional yang dikenal akan konsistensi cita rasa, kepatuhan regulasi industri hasil tembakau, serta kemitraan bisnis yang adil dan berkelanjutan bagi distributor daerah.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-bullseye"></i></div>
                <h3>Misi Perusahaan</h3>
                <ul style="color: var(--text-muted); list-style-type: none; padding-left: 0; line-height: 1.8;">
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-check" style="color: var(--gold);"></i> Menjaga kemurnian racikan tembakau pegunungan dan cengkeh pilihan.</li>
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-check" style="color: var(--gold);"></i> Mematuhi seluruh regulasi perizinan, cukai, dan batas kadar tar/nikotin.</li>
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-check" style="color: var(--gold);"></i> Mengembangkan jaringan distribusi terstruktur berbasis pasokan berkelanjutan.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Legalitas Cukai & Izin Usaha Resmi -->
<section class="section-py">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-subtitle">KEPATUHAN REGULASI NEGARA</span>
            <h2 class="section-title">LEGALITAS & PERIZINAN CUKAI RESMI</h2>
            <div class="gold-divider"></div>
            <p class="section-desc">Seluruh produk PR. KERETA KENCANA diproduksi dengan pita cukai resmi negara dan terdaftar di kementerian terkait.</p>
        </div>

        <div style="overflow-x: auto; background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 10px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; color: var(--text-light);">
                <thead>
                    <tr style="border-bottom: 1px solid var(--charcoal-border); background: rgba(255,255,255,0.02); color: var(--gold);">
                        <th style="padding: 14px;">Nama Dokumen Perizinan</th>
                        <th style="padding: 14px;">Nomor Dokumen</th>
                        <th style="padding: 14px;">Instansi Penerbit</th>
                        <th style="padding: 14px;">Masa Berlaku</th>
                        <th style="padding: 14px;">Status Legal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($legalitas as $doc)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 14px; font-weight: 600;">{{ $doc->nama_dokumen }}</td>
                            <td style="padding: 14px; font-family: monospace; color: var(--gold);">{{ $doc->nomor ?? '-' }}</td>
                            <td style="padding: 14px;">{{ $doc->penerbit ?? '-' }}</td>
                            <td style="padding: 14px;">{{ $doc->berlaku_sampai ? $doc->berlaku_sampai->format('d M Y') : 'Berlaku Selama Operasional' }}</td>
                            <td style="padding: 14px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: var(--success); padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
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

<!-- Komitmen & Fasilitas Produksi Pabrik -->
<section class="section-py bg-darker">
    <div class="container">
        <div class="section-header text-center">
            <span class="eyebrow"><i class="fa-solid fa-award"></i> STANDAR INDUSTRI</span>
            <h2 class="section-title">KOMITMEN MUTU & FASILITAS PABRIK</h2>
            <div class="gold-divider"></div>
            <p class="section-desc" style="margin: 0 auto;">Pilar utama operasional PR. KERETA KENCANA dalam menjaga integritas mutu kretek dan kepatuhan penuh terhadap hukum pertembakauan nasional.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-top: 36px;">
            <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 28px; transition: transform 0.25s ease;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 50px; height: 50px; background: rgba(197, 160, 89, 0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 18px;">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <h3 style="font-family: var(--font-serif); color: #ffffff; font-size: 1.15rem; margin-bottom: 8px;">Racikan Daun Tembakau Pilihan</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Menggunakan tembakau pegunungan Jawa berkualitas tinggi dipadu cengkeh beraroma harum untuk menghasilkan karakter hisapan mantap dan cita rasa khas Blitar.
                </p>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 28px; transition: transform 0.25s ease;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 50px; height: 50px; background: rgba(197, 160, 89, 0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 18px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 style="font-family: var(--font-serif); color: #ffffff; font-size: 1.15rem; margin-bottom: 8px;">Pengawasan Cukai & Legalitas</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Setiap kemasan rokok yang keluar dari lini pabrik telah dilekati pita cukai asli RI dan terdaftar di KPPBC Bea Cukai Blitar, menjamin keamanan bisnis mitra agen.
                </p>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 28px; transition: transform 0.25s ease;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 50px; height: 50px; background: rgba(197, 160, 89, 0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 18px;">
                    <i class="fa-solid fa-handshake-angle"></i>
                </div>
                <h3 style="font-family: var(--font-serif); color: #ffffff; font-size: 1.15rem; margin-bottom: 8px;">Dukungan Mitra Perintis & Grosir</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Mendukung toko kelontong dan warung dengan pesanan mulai dari <strong>1 slop</strong>, serta kemudahan pasokan skala bal dan karton bagi distributor wilayah.
                </p>
            </div>
        </div>

        <div style="background: rgba(197, 160, 89, 0.08); border: 1px dashed var(--gold); border-radius: var(--radius-lg); padding: 26px; margin-top: 36px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h4 style="color: #ffffff; font-size: 1.1rem; margin-bottom: 4px;">Ingin Berkunjung atau Meninjau Pabrik di Ponggok, Blitar?</h4>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">Kami menyambut kunjungan calon mitra distributor untuk verifikasi legalitas dan penjajakan pasokan.</p>
            </div>
            <a href="{{ route('kontak') }}" class="btn btn-gold">
                <i class="fa-solid fa-map-location-dot"></i> Buka Peta Rute & Info Kontak
            </a>
        </div>
    </div>
</section>
@endsection
