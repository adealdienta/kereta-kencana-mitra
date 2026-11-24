@extends('layouts.admin')

@section('title', 'Pengaturan Pabrik & Galeri Media')
@section('header_title', 'Pengaturan Pabrik, Kontak WA, & Galeri Foto')

@section('content')
<div style="max-width: 1120px; display: flex; flex-direction: column; gap: 24px;">

    <!-- Bagian 1: Identitas Kontak, Cukai & Rekening -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;">
        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 4px 0; font-size: 17px; color: #0f172a;">
                <i class="fa-solid fa-sliders" style="color: #9a743b;"></i> Informasi Resmi Pabrik &amp; Sinkronisasi Mobile
            </h3>
            <p style="margin: 0; font-size: 13px; color: #64748b;">
                Perubahan nomor WhatsApp, NPPBKC, atau rekening tujuan transfer di bawah ini otomatis berlaku di halaman website publik, faktur DO, serta aplikasi Android mitra.
            </p>
        </div>

        <form action="{{ route('admin.pengaturan.umum') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Nomor WhatsApp Admin Resmi <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="wa_pabrik" class="form-control" required
                           value="{{ old('wa_pabrik', $waPabrik) }}" placeholder="6285790627104"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: monospace; font-size: 14px;">
                    <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">
                        Tampil di web &amp; faktur sebagai: <strong>{{ $waPabrikDisplay }}</strong>
                    </small>
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Nomor NPPBKC (Cukai Pabrik) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nppbkc" class="form-control" required
                           value="{{ old('nppbkc', $nppbkc) }}" placeholder="750******-070700-221221*******"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: monospace; font-size: 14px;">
                    <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">
                        Ditampilkan di faktur Delivery Order &amp; API pengaturan.
                    </small>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Jam Operasional Pabrik <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="jam_operasional" class="form-control" required
                           value="{{ old('jam_operasional', $jamOperasional) }}" placeholder="Buka: 09.00 - 16.00 WIB (Senin - Sabtu)"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">
                        Tampil otomatis di Footer Website, halaman Profil Pabrik, serta Checkout Aplikasi Mobile.
                    </small>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                    Rekening Transfer Bank Pabrik (Opsional)
                </div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 12px 0;">
                    Kosongkan kolom rekening di bawah bila nomor rekening hanya ingin diberikan secara manual oleh Admin via WhatsApp setelah pemesanan masuk.
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                    <div class="form-group">
                        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nama Bank</label>
                        <input type="text" name="bank_nama" class="form-control"
                               value="{{ old('bank_nama', $bankInfo['nama'] ?? '') }}" placeholder="Contoh: Bank BCA / BRI / Mandiri"
                               style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nomor Rekening</label>
                        <input type="text" name="bank_rekening" class="form-control"
                               value="{{ old('bank_rekening', $bankInfo['rekening'] ?? '') }}" placeholder="Kosongkan jika lewat WhatsApp"
                               style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: monospace; font-size: 13.5px;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #475569; margin-bottom: 5px;">Atas Nama Rekening</label>
                        <input type="text" name="bank_atas_nama" class="form-control"
                               value="{{ old('bank_atas_nama', $bankInfo['atas_nama'] ?? '') }}" placeholder="Contoh: PR. KERETA KENCANA"
                               style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="background: #0f172a; color: #ffffff; border: none; padding: 11px 22px; border-radius: 6px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Pabrik
            </button>
        </form>
    </div>

    <!-- Bagian 2: Banner Hero Slider Beranda -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;">
        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
            <div>
                <h3 style="margin: 0 0 4px 0; font-size: 17px; color: #0f172a;">
                    <i class="fa-solid fa-panorama" style="color: #9a743b;"></i> Banner Slider Beranda (Hero)
                </h3>
                <p style="margin: 0; font-size: 13px; color: #64748b;">
                    Ganti foto bawaan atau tambahkan slide baru (lebih dari 2 gambar) untuk slider latar halaman utama. Format JPG/PNG/WebP maks. 4 MB (disarankan lanskap 16:9).
                </p>
            </div>
            <span style="background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
                Aktif: {{ count($heroSlides) }} Gambar Slide
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px;">
            @foreach($heroSlides as $slide)
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #f8fafc; display: flex; flex-direction: column;">
                    <div style="position: relative; height: 180px; background: #0f172a;">
                        @if($slide['exists'])
                            <img src="{{ $slide['url'] }}" alt="{{ $slide['title'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @endif
                        <span style="position: absolute; top: 10px; left: 10px; background: rgba(15, 23, 42, 0.85); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                            {{ $slide['location'] }}
                        </span>
                        @if(!($slide['is_built_in'] ?? true))
                            <span style="position: absolute; top: 10px; right: 10px; background: #0284c7; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 10.5px; font-weight: 700;">
                                Slide Tambahan
                            </span>
                        @elseif($slide['is_custom'])
                            <span style="position: absolute; top: 10px; right: 10px; background: #059669; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 10.5px; font-weight: 700;">
                                Kustom Aktif
                            </span>
                        @endif
                    </div>

                    <div style="padding: 16px; flex: 1; display: flex; flex-direction: column; gap: 10px;">
                        <strong style="font-size: 14px; color: #0f172a;">{{ $slide['label'] }}</strong>
                        <form action="{{ route('admin.pengaturan.media.update', $slide['key']) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 10px;">
                            @csrf
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Judul / Keterangan Slide</label>
                                <input type="text" name="caption" value="{{ $slide['caption'] }}" class="form-control" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Unggah Foto Baru (Opsional)</label>
                                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="form-control" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; font-size: 12.5px;">
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button type="submit" class="btn btn-primary btn-sm" style="background: #0f172a; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; font-size: 12.5px; font-weight: 600; cursor: pointer;">
                                    <i class="fa-solid fa-upload"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                        @if(!($slide['is_built_in'] ?? true))
                            <form action="{{ route('admin.pengaturan.media.reset', $slide['key']) }}" method="POST" onsubmit="return confirm('Hapus slide banner tambahan ini dari halaman beranda?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 6px 12px; border: 1px solid #fecaca; background: #fef2f2; color: #b91c1c; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                    <i class="fa-solid fa-trash"></i> Hapus Slide Ini
                                </button>
                            </form>
                        @elseif($slide['is_custom'])
                            <form action="{{ route('admin.pengaturan.media.reset', $slide['key']) }}" method="POST" onsubmit="return confirm('Kembalikan gambar ke bawaan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 6px 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 6px; cursor: pointer;">
                                    <i class="fa-solid fa-rotate-left"></i> Kembalikan Default
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Kartu Tambah Slide Banner Baru -->
            <div style="border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; padding: 20px; display: flex; flex-direction: column; justify-content: center; gap: 12px;">
                <div>
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #0f172a; color: #fcd34d; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-bottom: 8px;">
                        <i class="fa-solid fa-plus"></i> Slide #{{ count($heroSlides) + 1 }}
                    </span>
                    <h4 style="margin: 0 0 4px 0; font-size: 15px; color: #0f172a;">Tambah Gambar Slide Banner Baru</h4>
                    <p style="margin: 0; font-size: 12.5px; color: #64748b;">
                        Tambahkan gambar ketiga, keempat, dan seterusnya agar slider halaman beranda lebih bervariasi.
                    </p>
                </div>

                <form action="{{ route('admin.pengaturan.hero.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Judul / Keterangan Slide (Opsional)</label>
                        <input type="text" name="caption" placeholder="Contoh: Aktivitas Pengiriman Armada Pabrik" class="form-control" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; background: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Pilih Foto Banner <span style="color: #dc2626;">*</span></label>
                        <input type="file" name="foto" required accept="image/jpeg,image/png,image/webp" class="form-control" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; font-size: 12.5px;">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm" style="background: #9a743b; color: #fff; border: none; padding: 9px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-plus-circle"></i> Tambahkan ke Slider Beranda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bagian 2B: Gambar Panel Kanan Halaman Login -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;">
        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 4px 0; font-size: 17px; color: #0f172a;">
                <i class="fa-solid fa-right-to-bracket" style="color: #9a743b;"></i> Gambar Panel Kanan Halaman Login
            </h3>
            <p style="margin: 0; font-size: 13px; color: #64748b;">
                Gambar ini tampil penuh di sisi kanan layar pada halaman <strong>Masuk Akun (/login)</strong> berdampingan dengan form login di kiri. Format JPG/PNG/WebP maks. 4 MB.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; align-items: start;">
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #0f172a; position: relative; height: 220px;">
                @if(!empty($loginBanner['exists']))
                    <img src="{{ $loginBanner['url'] }}" alt="{{ $loginBanner['caption'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                @endif
                <span style="position: absolute; top: 10px; left: 10px; background: rgba(15, 23, 42, 0.85); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                    {{ $loginBanner['location'] }}
                </span>
                @if(!empty($loginBanner['is_custom']))
                    <span style="position: absolute; top: 10px; right: 10px; background: #059669; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 10.5px; font-weight: 700;">
                        Kustom Aktif
                    </span>
                @endif
            </div>

            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; background: #f8fafc; display: flex; flex-direction: column; gap: 12px;">
                <strong style="font-size: 14px; color: #0f172a;">{{ $loginBanner['label'] }}</strong>
                <form action="{{ route('admin.pengaturan.media.update', $loginBanner['key']) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Keterangan Singkat di Sisi Kanan (Opsional)</label>
                        <input type="text" name="caption" value="{{ $loginBanner['caption'] }}" class="form-control" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; background: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Unggah Gambar Halaman Login (JPG/PNG/WebP)</label>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="form-control" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; font-size: 12.5px;">
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary btn-sm" style="background: #0f172a; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-size: 12.5px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-upload"></i> Simpan Gambar Login
                        </button>
                    </div>
                </form>

                @if(!empty($loginBanner['is_custom']))
                    <form action="{{ route('admin.pengaturan.media.reset', $loginBanner['key']) }}" method="POST" onsubmit="return confirm('Kembalikan gambar halaman login ke bawaan?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 6px 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 6px; cursor: pointer;">
                            <i class="fa-solid fa-rotate-left"></i> Kembalikan Default
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Bagian 3: Foto Fasilitas & Proses Produksi -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;">
        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 4px 0; font-size: 17px; color: #0f172a;">
                <i class="fa-solid fa-images" style="color: #9a743b;"></i> Galeri Foto Fasilitas &amp; Proses Produksi
            </h3>
            <p style="margin: 0; font-size: 13px; color: #64748b;">
                Foto berikut tampil di halaman <strong>Beranda</strong> dan <strong>Profil Pabrik</strong> beserta keterangan &amp; tanggal pembaruan foto.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
            @foreach($fasilitasImages as $key => $img)
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #f8fafc; display: flex; flex-direction: column;">
                    <div style="position: relative; height: 170px; background: #1e293b;">
                        @if(!empty($img['exists']))
                            <img src="{{ $img['url'] }}" alt="{{ $img['alt'] ?? $key }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 12.5px;">
                                Belum ada gambar
                            </div>
                        @endif
                        <span style="position: absolute; top: 10px; left: 10px; background: rgba(15, 23, 42, 0.85); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                            {{ $img['location'] ?? $key }}
                        </span>
                        @if(!empty($img['is_custom']))
                            <span style="position: absolute; top: 10px; right: 10px; background: #059669; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 10.5px; font-weight: 700;">
                                Kustom Aktif
                            </span>
                        @endif
                    </div>

                    <div style="padding: 16px; flex: 1; display: flex; flex-direction: column; gap: 10px;">
                        <div style="font-size: 11.5px; color: #64748b; font-family: monospace;">Slot: {{ $key }} &bull; Diperbarui: {{ $img['updated_at_label'] ?? '-' }}</div>
                        <form action="{{ route('admin.pengaturan.media.update', $key) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 10px;">
                            @csrf
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Keterangan (Caption Foto)</label>
                                <input type="text" name="caption" value="{{ $img['caption'] ?? '' }}" class="form-control" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Tanggal Pembaruan Foto</label>
                                <input type="date" name="updated_at" value="{{ $img['updated_at'] ?? date('Y-m-d') }}" class="form-control" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Unggah Foto Pengganti</label>
                                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="form-control" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; font-size: 12.5px;">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm" style="background: #0f172a; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; font-size: 12.5px; font-weight: 600; cursor: pointer;">
                                    <i class="fa-solid fa-upload"></i> Simpan Foto &amp; Keterangan
                                </button>
                            </div>
                        </form>
                        @if(!empty($img['is_custom']))
                            <form action="{{ route('admin.pengaturan.media.reset', $key) }}" method="POST" onsubmit="return confirm('Kembalikan gambar ini ke bawaan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 6px 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 6px; cursor: pointer;">
                                    <i class="fa-solid fa-rotate-left"></i> Kembalikan Default
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
