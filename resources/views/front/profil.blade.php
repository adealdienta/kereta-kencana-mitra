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

<!-- Sejarah & Visi Misi -->
<section class="section-py">
    <div class="container">
        <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
            <div class="scroll-reveal reveal-up">
                <span class="section-subtitle">SEJARAH & IDENTITAS PABRIK</span>
                <h2 class="section-title">WARISAN LINTINGAN TRADISIONAL HINGGA INDUSTRI MODERN</h2>
                <div class="gold-divider" style="margin-left: 0;"></div>
                <p style="color: var(--text-medium); font-size: 1rem; line-height: 1.8; margin-bottom: 16px;">
                    Didirikan di Dusun Subontoro, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, <strong>PR. KERETA KENCANA</strong> berawal dari dedikasi mendalam terhadap cita rasa rokok kretek asli Jawa Timur. Dimulai dari produksi sigaret kretek tangan linting manual, pabrik kami kini telah berkembang memenuhi kapasitas pasar melalui perpaduan lini Sigaret Kretek Mesin (SKM) presisi dan Sigaret Kretek Tangan (SKT) padat rempah.
                </p>
                <p style="color: var(--text-medium); font-size: 1rem; line-height: 1.8;">
                    Dipimpin oleh Bapak Komari Yaman, kami terus memegang teguh komitmen integritas cukai resmi negara, penyerapan tenaga kerja lokal daerah, serta standarisasi mutu daun tembakau dan cengkeh bermutu tinggi.
                </p>
            </div>
            <div class="scroll-reveal reveal-zoom reveal-delay-2">
                <!-- Slot Visual: Logo Resmi Pabrik (Otomatis menampilkan foto gedung pabrik jika pabrik-ponggok.jpg tersedia) -->
                @if(file_exists(public_path('assets/img/pabrik-ponggok.jpg')))
                    <div class="photo-frame-modern" style="border-radius: var(--radius-lg); overflow: hidden; border: 1.5px solid var(--border-light); box-shadow: var(--shadow-md);">
                        <img src="{{ asset('assets/img/pabrik-ponggok.jpg') }}" alt="Pabrik PR. Kereta Kencana Ponggok" class="photo-frame-img" style="height: 320px;">
                        <div class="photo-frame-caption">
                            <i class="fa-solid fa-location-dot" style="color: var(--gold);"></i> Pabrik PR. KERETA KENCANA &bull; Ponggok, Kab. Blitar
                        </div>
                    </div>
                @else
                    <div class="photo-frame-modern" style="padding: 28px 24px; text-align: center; background: var(--bg-surface); border: 1.5px solid var(--border-light); border-radius: var(--radius-lg); box-shadow: var(--shadow-md);">
                        <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana Blitar" style="max-width: 80%; max-height: 250px; object-fit: contain; margin: 0 auto; display: block; filter: drop-shadow(0 6px 14px rgba(38,28,18,0.12));">
                        <div style="border-top: 1px dashed var(--border-light); margin-top: 20px; padding-top: 14px;">
                            <span style="font-size: 0.82rem; font-weight: 700; color: var(--gold-hover); text-transform: uppercase; letter-spacing: 1.5px; display: block;">
                                <i class="fa-solid fa-industry"></i> FASILITAS PABRIK PONGGOK, BLITAR
                            </span>
                            <small style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 3px;">
                                Sigaret Kretek Tangan (SKT) & Mesin &bull; NIB 9120001234567
                            </small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Visi & Misi Cards -->
<section class="section-py" style="background: var(--bg-surface-subtle); border-top: 1.5px solid var(--border-light); border-bottom: 1.5px solid var(--border-light); position: relative; overflow: hidden;">
    <div class="container" style="position: relative; z-index: 1;">
        <!-- Header Pemisah Visi & Misi -->
        <div class="section-header text-center scroll-reveal reveal-up" style="margin-bottom: 42px;">
            <span class="eyebrow"><i class="fa-solid fa-compass"></i> LANDASAN STRATEGIS PABRIK</span>
            <h2 class="section-title">VISI & MISI PERUSAHAAN</h2>
            <div class="gold-divider"></div>
            <p class="section-desc" style="margin: 0 auto; color: var(--text-muted);">
                Prinsip arah dan komitmen mutu PR. KERETA KENCANA dalam melayani pasokan hasil tembakau dan kemitraan distributor di seluruh wilayah.
            </p>
        </div>

        <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Kartu Visi -->
            <div class="scroll-reveal reveal-zoom reveal-delay-1" style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-top: 4px solid var(--gold); border-radius: var(--radius-lg); padding: 36px 30px; box-shadow: var(--shadow-md); display: flex; flex-direction: column;">
                <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(179, 139, 63, 0.12); border: 1px solid rgba(179, 139, 63, 0.35); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 20px;">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 1.4rem; font-weight: 700; margin-bottom: 14px; letter-spacing: 0.5px;">
                    Visi Perusahaan
                </h3>
                <p style="color: var(--text-medium); font-size: 0.98rem; line-height: 1.8; margin: 0;">
                    Menjadi produsen sigaret kretek terpercaya di tingkat nasional yang dikenal akan konsistensi cita rasa, kepatuhan regulasi industri hasil tembakau, serta kemitraan bisnis yang adil dan berkelanjutan bagi distributor daerah.
                </p>
            </div>

            <!-- Kartu Misi -->
            <div class="scroll-reveal reveal-zoom reveal-delay-2" style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-top: 4px solid var(--gold); border-radius: var(--radius-lg); padding: 36px 30px; box-shadow: var(--shadow-md); display: flex; flex-direction: column;">
                <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(179, 139, 63, 0.12); border: 1px solid rgba(179, 139, 63, 0.35); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 20px;">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 1.4rem; font-weight: 700; margin-bottom: 14px; letter-spacing: 0.5px;">
                    Misi Perusahaan
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px;">
                    <li style="display: flex; align-items: flex-start; gap: 12px; color: var(--text-medium); font-size: 0.95rem; line-height: 1.6;">
                        <span style="color: var(--gold); font-size: 15px; margin-top: 3px;"><i class="fa-solid fa-circle-check"></i></span>
                        <span>Menjaga kemurnian racikan tembakau pegunungan dan cengkeh pilihan asli Nusantara.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px; color: var(--text-medium); font-size: 0.95rem; line-height: 1.6;">
                        <span style="color: var(--gold); font-size: 15px; margin-top: 3px;"><i class="fa-solid fa-circle-check"></i></span>
                        <span>Mematuhi seluruh regulasi perizinan cukai negara dan standarisasi batas kadar tar/nikotin resmi.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px; color: var(--text-medium); font-size: 0.95rem; line-height: 1.6;">
                        <span style="color: var(--gold); font-size: 15px; margin-top: 3px;"><i class="fa-solid fa-circle-check"></i></span>
                        <span>Mengembangkan jaringan distribusi terstruktur berbasis pasokan berkelanjutan dan kemudahan order bagi mitra.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Legalitas Cukai & Izin Usaha Resmi -->
<section class="section-py">
    <div class="container">
        <div class="section-header text-center scroll-reveal reveal-up">
            <span class="section-subtitle">KEPATUHAN REGULASI NEGARA</span>
            <h2 class="section-title">LEGALITAS & PERIZINAN CUKAI RESMI</h2>
            <div class="gold-divider"></div>
            <p class="section-desc">Seluruh produk PR. KERETA KENCANA diproduksi dengan pita cukai resmi negara dan terdaftar di kementerian terkait.</p>
        </div>

        <div class="scroll-reveal reveal-zoom" style="overflow-x: auto; background: var(--bg-surface); border: 1.5px solid var(--border-light); border-radius: var(--radius-lg); padding: 12px; box-shadow: var(--shadow-md);">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; color: var(--text-dark);">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-light); background: var(--bg-surface-subtle); color: var(--gold-hover);">
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

<!-- Komitmen & Fasilitas Produksi Pabrik -->
<section class="section-py bg-darker">
    <div class="container">
        <div class="section-header text-center scroll-reveal reveal-up">
            <span class="eyebrow"><i class="fa-solid fa-award"></i> STANDAR INDUSTRI</span>
            <h2 class="section-title">KOMITMEN MUTU & FASILITAS PABRIK</h2>
            <div class="gold-divider"></div>
            <p class="section-desc" style="margin: 0 auto;">Pilar utama operasional PR. KERETA KENCANA dalam menjaga integritas mutu kretek dan kepatuhan penuh terhadap hukum pertembakauan nasional.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-top: 36px;">
            <div class="scroll-reveal reveal-zoom reveal-delay-1" style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: border-color 0.25s ease, transform 0.25s ease;" onmouseover="this.style.borderColor='var(--gold)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='var(--border-light)'; this.style.transform='translateY(0)'">
                @if(file_exists(public_path('assets/img/fasilitas-tembakau.jpg')))
                    <div style="height: 180px; overflow: hidden; background: #16130f;">
                        <img src="{{ asset('assets/img/fasilitas-tembakau.jpg') }}" alt="Racikan Tembakau" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                @endif
                <div style="padding: 28px;">
                    <div style="width: 50px; height: 50px; background: rgba(179, 139, 63, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 18px;">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 1.15rem; margin-bottom: 8px;">Racikan Daun Tembakau Pilihan</h3>
                    <p style="color: var(--text-medium); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        Menggunakan tembakau pegunungan Jawa berkualitas tinggi dipadu cengkeh beraroma harum untuk menghasilkan karakter hisapan mantap dan cita rasa khas Blitar.
                    </p>
                </div>
            </div>

            <div class="scroll-reveal reveal-zoom reveal-delay-2" style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: border-color 0.25s ease, transform 0.25s ease;" onmouseover="this.style.borderColor='var(--gold)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='var(--border-light)'; this.style.transform='translateY(0)'">
                @if(file_exists(public_path('assets/img/fasilitas-cukai.jpg')))
                    <div style="height: 180px; overflow: hidden; background: #16130f;">
                        <img src="{{ asset('assets/img/fasilitas-cukai.jpg') }}" alt="Pengawasan Cukai" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                @endif
                <div style="padding: 28px;">
                    <div style="width: 50px; height: 50px; background: rgba(179, 139, 63, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 18px;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 1.15rem; margin-bottom: 8px;">Pengawasan Cukai & Legalitas</h3>
                    <p style="color: var(--text-medium); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        Setiap kemasan rokok yang keluar dari lini pabrik telah dilekati pita cukai asli RI dan terdaftar di KPPBC Bea Cukai Blitar, menjamin keamanan bisnis mitra agen.
                    </p>
                </div>
            </div>

            <div class="scroll-reveal reveal-zoom reveal-delay-3" style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: border-color 0.25s ease, transform 0.25s ease;" onmouseover="this.style.borderColor='var(--gold)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='var(--border-light)'; this.style.transform='translateY(0)'">
                @if(file_exists(public_path('assets/img/fasilitas-gudang.jpg')))
                    <div style="height: 180px; overflow: hidden; background: #16130f;">
                        <img src="{{ asset('assets/img/fasilitas-gudang.jpg') }}" alt="Dukungan Mitra & Gudang" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                @endif
                <div style="padding: 28px;">
                    <div style="width: 50px; height: 50px; background: rgba(179, 139, 63, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; margin-bottom: 18px;">
                        <i class="fa-solid fa-handshake-angle"></i>
                    </div>
                    <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 1.15rem; margin-bottom: 8px;">Dukungan Mitra Perintis & Grosir</h3>
                    <p style="color: var(--text-medium); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        Mendukung toko kelontong dan warung dengan pesanan mulai dari <strong>1 slop</strong>, serta kemudahan pasokan skala bal dan karton bagi distributor wilayah.
                    </p>
                </div>
            </div>
        </div>

        <div class="scroll-reveal reveal-up" style="background: var(--bg-surface); border: 1.5px dashed var(--gold); border-radius: var(--radius-lg); padding: 26px; margin-top: 36px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; box-shadow: var(--shadow-md);">
            <div>
                <h4 style="color: var(--text-dark); font-size: 1.1rem; margin-bottom: 4px;">Ingin Berkunjung atau Meninjau Pabrik di Ponggok, Blitar?</h4>
                <p style="color: var(--text-medium); font-size: 0.88rem; margin: 0;">Kami menyambut kunjungan calon mitra distributor untuk verifikasi legalitas dan penjajakan pasokan.</p>
            </div>
            <a href="{{ route('kontak') }}" class="btn btn-gold">
                <i class="fa-solid fa-map-location-dot"></i> Buka Peta Rute & Info Kontak
            </a>
        </div>
    </div>
</section>
@endsection
