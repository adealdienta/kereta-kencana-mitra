@extends('layouts.app')

@section('title', 'Profil Perusahaan & Legalitas')

@section('content')
<!-- Tentang Kami & Fakta Kunci Pabrik -->
<section class="section-py" style="background: #ffffff; padding-top: 48px;">
    <div class="container">
        <div class="profil-editorial-grid">
            <!-- Kolom Kiri: Narasi Factual Pabrik -->
            <div class="profil-story-col scroll-reveal reveal-up">
                <div class="profil-story-block">
                    <h2 class="profil-heading">Tentang Kami</h2>
                    <p class="profil-paragraph">
                        <strong>PR. KERETA KENCANA</strong> merupakan pabrik Sigaret Kretek Tangan (SKT) yang dikelola oleh manajemen resmi perusahaan di Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur.
                    </p>
                    <p class="profil-paragraph">
                        Pabrik kami memproduksi tiga varian SKT isi 12 batang per bungkus dengan pita cukai resmi: <strong>SEMBADA 12</strong>, <strong>KERETA KENCANA 12</strong>, serta <strong>SEMBADA Cethe 12</strong>.
                    </p>
                </div>

                <div class="profil-story-block">
                    <h2 class="profil-heading">Operasional &amp; Distribusi</h2>
                    <p class="profil-paragraph">
                        Seluruh kegiatan produksi dilaksanakan di Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar dengan mematuhi perizinan industri hasil tembakau serta pengawasan pelekatan pita cukai di wilayah kerja KPPBC Tipe Madya Pabean C Blitar.
                    </p>
                    <p class="profil-paragraph">
                        Untuk distribusi mitra, kemasan slop berisi 10 bungkus (120 batang) dapat dipesan langsung melalui formulir pemesanan online (1&ndash;19 slop per varian), sedangkan pasokan skala bal dan kerja sama keagenan ditangani langsung bersama manajemen pabrik via WhatsApp admin.
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan: Foto Pabrik & Tabel Fakta Kunci -->
            <div class="profil-visual-col scroll-reveal reveal-zoom reveal-delay-1">
                @php $imgMeta = $fasilitasImages['pabrik-ponggok'] ?? []; @endphp
                @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                    <div class="photo-frame-modern" style="min-height: 320px; border: 1px solid var(--border-light); border-radius: 0; box-shadow: none;">
                        <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Pabrik PR. Kereta Kencana Ponggok' }}" class="photo-frame-img" style="height: 320px;">
                        @include('front._facility_caption', ['imgMeta' => $imgMeta])
                    </div>
                @else
                    <div class="photo-placeholder-slot" style="min-height: 320px;">
                        <div class="photo-placeholder-icon" style="width: 54px; height: 54px; font-size: 20px;">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <div class="photo-placeholder-title">Foto Tampak Depan &amp; Papan Nama Pabrik</div>
                        <div class="photo-placeholder-sub" style="max-width: 380px;">
                            Lokasi pabrik <strong>PR. KERETA KENCANA</strong> di Dusun Subontoro, Desa Kebonduren, Kec. Ponggok, Kab. Blitar.
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
                        <div class="profil-fakta-label">Pengawasan Cukai</div>
                        <div class="profil-fakta-value">KPPBC TMP C Blitar</div>
                    </div>
                    <div class="profil-fakta-card">
                        <div class="profil-fakta-label">Varian Produksi</div>
                        <div class="profil-fakta-value">3 Varian SKT (12 Batang)</div>
                    </div>
                    <div class="profil-fakta-card">
                        <div class="profil-fakta-label">Satuan Kemasan</div>
                        <div class="profil-fakta-value">10 Bungkus/Slop &bull; Bal via Admin</div>
                    </div>
                    <div class="profil-fakta-card" style="grid-column: 1 / -1;">
                        <div class="profil-fakta-label">Jam Operasional Pabrik</div>
                        <div class="profil-fakta-value">{{ $pabrikJamOperasional ?? config('pabrik.jam_operasional', 'Buka: 09.00 - 16.00 WIB (Senin - Sabtu)') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visi & Misi (Tata Letak Asimetris Editorial: Pernyataan Utama + Daftar Bernomor) -->
<section class="section-py" style="background: var(--kk-ivory); border-top: 1px solid var(--kk-ivory-border);">
    <div class="container">
        <div class="section-header scroll-reveal reveal-up" style="margin-bottom: 32px;">
            <h2 class="section-title">Visi &amp; Misi Perusahaan</h2>
        </div>

        <div class="editorial-visi-misi-grid">
            <!-- Kiri: Pernyataan Visi Utama -->
            <div class="editorial-visi-statement scroll-reveal reveal-up">
                <span class="editorial-kicker-label">Visi Perusahaan</span>
                <p class="editorial-visi-quote">
                    Mengedepankan inovasi, menjaga nilai-nilai tradisi Nusantara, dan memberikan kenikmatan berkualitas dengan harga terjangkau.
                </p>
                <p class="editorial-visi-note">
                    Informasi profil tambahan yang belum tercantum di halaman ini dapat dikonfirmasi langsung kepada manajemen pabrik.
                </p>
            </div>

            <!-- Kanan: 3 Misi Bernomor Monospace dengan Pemisah Garis Tipis -->
            <div class="editorial-misi-list scroll-reveal reveal-up reveal-delay-1">
                <div class="editorial-misi-row">
                    <div class="editorial-misi-num">01</div>
                    <div>
                        <h3 class="editorial-misi-title">Ekonomi Daerah &amp; Kemitraan Petani</h3>
                        <p class="editorial-misi-desc">
                            Mendorong pertumbuhan ekonomi daerah dengan memprioritaskan penyerapan tenaga kerja lokal dan membangun kemitraan adil bersama petani tembakau.
                        </p>
                    </div>
                </div>

                <div class="editorial-misi-row">
                    <div class="editorial-misi-num">02</div>
                    <div>
                        <h3 class="editorial-misi-title">Kemandirian Usaha Mikro</h3>
                        <p class="editorial-misi-desc">
                            Mendukung kemandirian usaha mikro di sekitar wilayah operasional pabrik.
                        </p>
                    </div>
                </div>

                <div class="editorial-misi-row">
                    <div class="editorial-misi-num">03</div>
                    <div>
                        <h3 class="editorial-misi-title">Regulasi Cukai &amp; Konsistensi Mutu</h3>
                        <p class="editorial-misi-desc">
                            Berkomitmen penuh pada kepatuhan regulasi pemerintah, legalitas pita cukai, serta konsistensi mutu produk demi memberikan rasa aman dan kepuasan terbaik bagi konsumen.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Buku Besar Legalitas Cukai & Perizinan Resmi (Latar Hitam Gelap, Tanpa Kartu Membulat) -->
<section class="ledger-section-dark">
    <div class="container">
        <div class="section-header scroll-reveal reveal-up" style="margin-bottom: 28px; max-width: 720px;">
            <h2 class="section-title" style="color: #ffffff;">Legalitas &amp; Perizinan Cukai Resmi</h2>
            <p class="section-desc" style="color: #a3a3a3;">
                Daftar dokumen izin usaha dan pita cukai yang tercatat pada arsip PR. KERETA KENCANA.
            </p>
        </div>

        <div class="ledger-table-wrap scroll-reveal reveal-up">
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th>Nama Dokumen</th>
                        <th>Nomor Dokumen</th>
                        <th>Instansi Penerbit</th>
                        <th>Masa Berlaku</th>
                        <th>Status</th>
                        <th>Arsip Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($legalitas as $doc)
                        @php
                            $statusLower = strtolower($doc->status ?? '');
                            $stampClass = 'ledger-stamp';
                            if (str_contains($statusLower, 'proses')) {
                                $stampClass .= ' is-pending';
                            } elseif (str_contains($statusLower, 'tidak')) {
                                $stampClass .= ' is-inactive';
                            }
                        @endphp
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;">{{ $doc->nama_dokumen }}</td>
                            <td>
                                <span class="ledger-doc-num">{{ $doc->nomor ?? '-' }}</span>
                            </td>
                            <td style="color: #a3a3a3;">{{ $doc->penerbit ?? '-' }}</td>
                            <td style="color: #a3a3a3;">
                                {{ $doc->berlaku_sampai ? $doc->berlaku_sampai->format('d M Y') : 'Selama Operasional' }}
                            </td>
                            <td>
                                <span class="{{ $stampClass }}">{{ $doc->status }}</span>
                            </td>
                            <td>
                                @if($doc->file_dokumen)
                                    <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" rel="noopener noreferrer" class="ledger-doc-link">
                                        Lihat dokumen <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                                    </a>
                                @else
                                    <span class="ledger-physical-note">Arsip fisik pabrik</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p style="margin: 16px 0 0; font-size: 0.86rem; color: #8c8c8c; line-height: 1.55;">
            Sebagian digit nomor izin disensor untuk mencegah penyalahgunaan maupun pemalsuan dokumen. Mitra resmi yang membutuhkan verifikasi dokumen asli dapat menghubungi <a href="https://wa.me/{{ $pabrikWa ?? config('pabrik.wa_pabrik', '6285790627104') }}?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin mengonfirmasi kelengkapan dokumen legalitas kemitraan.') }}" target="_blank" rel="noopener noreferrer" style="color: #ffffff; text-decoration: underline;">Admin via WhatsApp</a>.
        </p>
    </div>
</section>

<!-- 4 Pilar Operasional & Fasilitas Pabrik -->
<section class="section-py" style="background: var(--kk-ivory);">
    <div class="container">
        <div class="section-header scroll-reveal reveal-up" style="margin-bottom: 36px; max-width: 720px;">
            <h2 class="section-title">Fasilitas Produksi &amp; Operasional</h2>
            <p class="section-desc">
                Tahapan kerja di pabrik PR. KERETA KENCANA Ponggok, Kabupaten Blitar dari penyiapan bahan hingga pengiriman mitra.
            </p>
        </div>

        <div class="profil-pilar-grid">
            <!-- Pilar 01 -->
            <div class="profil-pilar-card scroll-reveal reveal-up">
                <div class="profil-pilar-media">
                    @php $imgMeta = $fasilitasImages['fasilitas-tembakau'] ?? []; @endphp
                    @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                        <div class="photo-frame-modern">
                            <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Gudang Bahan Baku & Racikan Tembakau' }}" class="photo-frame-img">
                            @include('front._facility_caption', ['imgMeta' => $imgMeta])
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Gudang Bahan Baku Tembakau &amp; Cengkeh</div>
                            <div class="photo-placeholder-sub">Dokumentasi penyimpanan daun tembakau dan cengkeh di fasilitas pabrik Ponggok.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/fasilitas-tembakau.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">01 &bull; Penyimpanan &amp; Racikan</div>
                    <h3 class="profil-pilar-title">Gudang Bahan Baku Tembakau &amp; Cengkeh</h3>
                    <p class="profil-pilar-desc">
                        Bahan baku daun tembakau dan cengkeh disimpan terpisah dan diracik sesuai standar internal pabrik agar aroma serta karakter hisapan tiga varian tetap seragam antar-produksi.
                    </p>
                </div>
            </div>

            <!-- Pilar 02 -->
            <div class="profil-pilar-card scroll-reveal reveal-up reveal-delay-1">
                <div class="profil-pilar-media">
                    @php $imgMeta = $fasilitasImages['galeri-produksi-skt'] ?? []; @endphp
                    @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                        <div class="photo-frame-modern">
                            <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Ruang Produksi Linting SKT' }}" class="photo-frame-img">
                            @include('front._facility_caption', ['imgMeta' => $imgMeta])
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-hands"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Ruang Produksi Linting SKT</div>
                            <div class="photo-placeholder-sub">Dokumentasi aktivitas pelintingan Sigaret Kretek Tangan (SKT) di ruang produksi.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/galeri-produksi-skt.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">02 &bull; Produksi SKT</div>
                    <h3 class="profil-pilar-title">Ruang Pelintingan Sigaret Kretek Tangan</h3>
                    <p class="profil-pilar-desc">
                        Seluruh varian produk dilinting secara manual (SKT 12 batang) oleh tim pelinting lokal di Kecamatan Ponggok, dengan pengawasan kepadatan isi dan kerapian potongan ujung batang.
                    </p>
                </div>
            </div>

            <!-- Pilar 03 -->
            <div class="profil-pilar-card scroll-reveal reveal-up">
                <div class="profil-pilar-media">
                    @php $imgMeta = $fasilitasImages['fasilitas-cukai'] ?? []; @endphp
                    @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                        <div class="photo-frame-modern">
                            <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Pengawasan Cukai & Pengepakan' }}" class="photo-frame-img">
                            @include('front._facility_caption', ['imgMeta' => $imgMeta])
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-stamp"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Pelekatan Pita Cukai &amp; Pengemasan Slop</div>
                            <div class="photo-placeholder-sub">Dokumentasi pemeriksaan kemasan bungkus rokok &amp; pelekatan pita cukai resmi.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/fasilitas-cukai.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">03 &bull; Cukai &amp; Pengemasan</div>
                    <h3 class="profil-pilar-title">Inspeksi Mutu &amp; Pelekatan Pita Cukai</h3>
                    <p class="profil-pilar-desc">
                        Setiap bungkus diperiksa kondisi lipatan dan segelnya sebelum dilekati pita cukai resmi Republik Indonesia dan dikemas menjadi satuan slop berisi 10 bungkus.
                    </p>
                </div>
            </div>

            <!-- Pilar 04 -->
            <div class="profil-pilar-card scroll-reveal reveal-up reveal-delay-1">
                <div class="profil-pilar-media">
                    @php $imgMeta = $fasilitasImages['fasilitas-gudang'] ?? []; @endphp
                    @if(!empty($imgMeta['exists']) && !empty($imgMeta['url']))
                        <div class="photo-frame-modern">
                            <img src="{{ $imgMeta['url'] }}" alt="{{ $imgMeta['alt'] ?? 'Gudang Barang Jadi & Distribusi' }}" class="photo-frame-img">
                            @include('front._facility_caption', ['imgMeta' => $imgMeta])
                        </div>
                    @else
                        <div class="photo-placeholder-slot">
                            <div class="photo-placeholder-icon">
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                            <div class="photo-placeholder-title">Foto Gudang Barang Jadi &amp; Armada Distribusi</div>
                            <div class="photo-placeholder-sub">Dokumentasi gudang barang jadi dan armada pengiriman menuju mitra.</div>
                            <span class="photo-placeholder-tag"><i class="fa-regular fa-image"></i> public/assets/img/fasilitas-gudang.jpg</span>
                        </div>
                    @endif
                </div>
                <div class="profil-pilar-body">
                    <div class="profil-pilar-tag">04 &bull; Gudang &amp; Distribusi</div>
                    <h3 class="profil-pilar-title">Gudang Barang Jadi &amp; Pengiriman Mitra</h3>
                    <p class="profil-pilar-desc">
                        Menyiapkan pengiriman untuk pesanan satuan slop (1&ndash;19 slop via web) serta muatan skala bal bagi mitra toko dan agen daerah yang berkoordinasi langsung dengan admin pabrik.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
