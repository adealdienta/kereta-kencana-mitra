@extends('layouts.app')

@section('title', 'Kontak Resmi & Lokasi Pabrik Ponggok Blitar')

@section('content')
<!-- Header Banner -->
<section class="page-header" style="background: linear-gradient(rgba(13, 16, 18, 0.85), rgba(13, 16, 18, 0.95)), url('{{ asset('assets/img/hero-pabrik.jpg') }}') center/cover no-repeat; padding: 60px 0;">
    <div class="container text-center">
        <span class="header-badge">HUBUNGI KAMI</span>
        <h1 class="page-title">KONTAK RESMI & LOKASI PABRIK</h1>
        <p class="page-subtitle">Kantor Operasional & Lini Produksi PR. KERETA KENCANA Ponggok, Kabupaten Blitar</p>
    </div>
</section>

<section class="section-py">
    <div class="container">
        <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px;">
            <!-- Info Kontak -->
            <div>
                <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 30px; margin-bottom: 24px;">
                    <h3 style="color: var(--gold); font-size: 20px; margin-bottom: 16px;">
                        <i class="fa-solid fa-building-circle-check"></i> Kantor & Gudang Pabrik
                    </h3>
                    
                    <div style="margin-bottom: 16px;">
                        <strong style="color: var(--text-white); display: block; margin-bottom: 4px;">Alamat Fisik:</strong>
                        <p style="color: var(--text-light); line-height: 1.6;">
                            Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur (-8.0231433, 112.1001202)
                        </p>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <strong style="color: var(--text-white); display: block; margin-bottom: 4px;">Layanan Telepon & WhatsApp:</strong>
                        <p style="color: var(--text-light); line-height: 1.6;">
                            Telepon Kantor: (0342) 801234<br>
                            WhatsApp Layanan Agen: +62 812-3456-7890
                        </p>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <strong style="color: var(--text-white); display: block; margin-bottom: 4px;">Jam Buka Operasional Gudang:</strong>
                        <p style="color: var(--text-light);">Senin - Sabtu: 08.00 - 16.00 WIB<br>(Hari Minggu & Libur Nasional Tutup)</p>
                    </div>

                    <div style="margin-top: 20px;">
                        <a href="https://maps.app.goo.gl/3fxK2wXZoiRLs2sg8" target="_blank" class="btn btn-gold btn-sm" style="width: 100%; text-align: center;">
                            <i class="fa-solid fa-diamond-turn-right"></i> Buka Rute Google Maps Pabrik
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Pesan Kemitraan -->
            <div>
                <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 30px;">
                    <h3 style="color: var(--gold); font-size: 20px; margin-bottom: 8px;">
                        <i class="fa-solid fa-envelope-open-text"></i> Kirim Permohonan Kemitraan
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">
                        Silakan tinggalkan pesan untuk konsultasi kuota distribusi daerah atau penawaran pasokan tembakau & cengkeh.
                    </p>

                    <form action="{{ route('kontak.kirim') }}" method="POST">
                        @csrf
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Nama Lengkap / Instansi</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Nama Anda atau Nama Toko"
                                   style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Nomor Telepon / WhatsApp</label>
                            <input type="tel" name="telepon" class="form-control" required placeholder="08xxxxxxxxxx"
                                   style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Isi Pesan Kemitraan</label>
                            <textarea name="pesan" class="form-control" rows="4" required placeholder="Jelaskan kebutuhan volume bal rokok atau wilayah rencana distribusi Anda..."
                                      style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 700;">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Pesan ke Manajemen
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Google Maps Embed & Navigasi Logistik -->
        <div style="margin-top: 48px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                <div>
                    <span class="eyebrow"><i class="fa-solid fa-map-location-dot"></i> PETA SATELIT & NAVIGASI PABRIK</span>
                    <h3 style="color: #ffffff; font-family: var(--font-serif); font-size: 1.35rem; margin: 0;">Titik Lokasi Pabrik di Ponggok, Blitar</h3>
                </div>
                <a href="https://www.google.com/maps/dir/?api=1&destination=-8.0231433,112.1001202" target="_blank" class="btn btn-sm btn-gold">
                    <i class="fa-solid fa-diamond-turn-right"></i> Petunjuk Arah Google Maps
                </a>
            </div>

            <div style="border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--charcoal-border); box-shadow: var(--shadow-lg);">
                <iframe 
                    src="https://www.google.com/maps?q=-8.0231433,112.1001202&hl=id&z=15&output=embed" 
                    width="100%" 
                    height="420" 
                    style="border:0; display: block;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>
@endsection
