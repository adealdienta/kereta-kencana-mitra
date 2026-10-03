@extends('layouts.app')

@section('title', 'Pelacakan Pesanan Saya')

@section('content')
<!-- Header Banner -->
<section class="page-header">
    <div class="container text-center">
        <span class="header-badge"><i class="fa-solid fa-boxes-packing"></i> STATUS PENGIRIMAN & FAKTUR</span>
        <h1 class="page-title">PELACAKAN PESANAN SAYA</h1>
        <p class="page-subtitle">Pantau tahapan proses pesanan rokok Anda dari lini produksi hingga tiba di toko</p>
    </div>
</section>

<section class="section-py">
    <div class="container" style="max-width: 1000px;">
        
        <!-- Mitra Profile Card -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 20px 24px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(179,139,63,0.12); color: var(--gold-hover); display: flex; align-items: center; justify-content: center; font-size: 22px;">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h3 style="color: var(--text-dark); font-size: 18px; margin-bottom: 3px;">{{ $user->nama_toko ?: $user->name }}</h3>
                    <p style="color: var(--text-muted); font-size: 13px; margin: 0;">
                        Pemilik: <strong>{{ $user->name }}</strong> &bull; WA: {{ $user->telepon ?: '-' }} &bull; {{ $user->email }}
                    </p>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="openMitraProfileModal()" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user-pen"></i> Ubah Data & Sandi
                </button>
                <a href="{{ route('pesanan.form') }}" class="btn btn-gold btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-cart-plus"></i> Buat Pesanan Baru
                </a>
            </div>
        </div>

        @if($pendingDelivery)
            <!-- Alert Khusus Pesanan Siap Konfirmasi -->
            <div style="background: #fefce8; border: 1px solid #fde047; border-radius: 8px; padding: 18px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="color: #ca8a04; font-size: 26px;">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <div>
                        <strong style="color: #854d0e; font-size: 15px; display: block;">Barang Sedang Dikirim ke Toko Anda</strong>
                        <p style="color: #713f12; font-size: 13px; margin: 2px 0 0 0;">
                            Pesanan <strong>{{ $pendingDelivery->kode_transaksi }}</strong> ({{ $pendingDelivery->barang->nama }}) sedang di jalan. Saat armada tiba, segera unggah foto barang yang Anda terima untuk menyelesaikan pesanan.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-gold" onclick="openKonfirmasiModal('{{ $pendingDelivery->id }}', '{{ $pendingDelivery->kode_transaksi }}', '{{ $pendingDelivery->barang->nama }}')">
                    <i class="fa-solid fa-camera"></i> Konfirmasi Terima Sekarang
                </button>
            </div>
        @endif

        <!-- Daftar Transaksi Pelanggan -->
        @if($transaksis->isEmpty())
            <div style="background: var(--bg-surface); border: 1px dashed var(--border-light); border-radius: var(--radius-lg); padding: 50px 20px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="font-size: 40px; color: var(--text-muted); margin-bottom: 12px;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 style="color: var(--text-dark); font-size: 18px; margin-bottom: 6px;">Belum Ada Riwayat Pemesanan</h3>
                <p style="color: var(--text-muted); font-size: 13.5px; max-width: 450px; margin: 0 auto 20px;">
                    Anda belum pernah membuat transaksi pemesanan rokok. Silakan buka formulir pemesanan untuk memulai order perdana Anda.
                </p>
                <a href="{{ route('pesanan.form') }}" class="btn btn-gold">
                    <i class="fa-solid fa-cart-plus"></i> Pesan Rokok Sekarang (Mulai 1 Slop)
                </a>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($transaksis as $t)
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); transition: 0.2s;">
                        
                        <!-- Header Transaksi -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid var(--border-light); padding-bottom: 16px; margin-bottom: 18px;">
                            <div>
                                <span style="font-family: monospace; font-size: 14px; font-weight: 700; color: var(--gold-hover); letter-spacing: 0.5px;">
                                    {{ $t->kode_transaksi }}
                                </span>
                                <div style="color: var(--text-muted); font-size: 12px; margin-top: 3px;">
                                    Dipesan pada: {{ $t->created_at->format('d M Y, H:i') }} WIB
                                </div>
                            </div>

                            <!-- Status Badge Berbasis Icon -->
                            <div>
                                @if($t->status === 'Baru Masuk')
                                    <span style="background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-regular fa-clock"></i> Menunggu Konfirmasi Pabrik
                                    </span>
                                @elseif($t->status === 'Diproses')
                                    <span style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-gears"></i> Sedang Diproses di Gudang
                                    </span>
                                @elseif($t->status === 'Dikirim')
                                    <span style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-truck-fast"></i> Sedang Dikirim Armada Pabrik
                                    </span>
                                @elseif($t->status === 'Selesai')
                                    <span style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-circle-check"></i> Pesanan Selesai (Diterima)
                                    </span>
                                @else
                                    <span style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-ban"></i> Dibatalkan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Icon Stepper Sederhana Progres Barang -->
                        @if($t->status !== 'Dibatalkan')
                            <div style="background: rgba(0,0,0,0.25); border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px; text-align: center;">
                                    
                                    <!-- Step 1: Diterbitkan -->
                                    <div style="color: {{ in_array($t->status, ['Baru Masuk', 'Diproses', 'Dikirim', 'Selesai']) ? 'var(--gold)' : 'var(--text-muted)' }};">
                                        <div style="font-size: 20px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">1. Order Diterbitkan</div>
                                    </div>

                                    <!-- Step 2: Diproses Gudang -->
                                    <div style="color: {{ in_array($t->status, ['Diproses', 'Dikirim', 'Selesai']) ? 'var(--gold)' : 'var(--text-muted)' }};">
                                        <div style="font-size: 20px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-boxes-packing"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">2. Diproses Gudang</div>
                                    </div>

                                    <!-- Step 3: Dikirim -->
                                    <div style="color: {{ in_array($t->status, ['Dikirim', 'Selesai']) ? 'var(--gold)' : 'var(--text-muted)' }};">
                                        <div style="font-size: 20px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-truck"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">3. Sedang Dikirim</div>
                                    </div>

                                    <!-- Step 4: Selesai -->
                                    <div style="color: {{ $t->status === 'Selesai' ? '#10b981' : 'var(--text-muted)' }};">
                                        <div style="font-size: 20px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">4. Diterima Toko</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Detail Produk & Harga -->
                        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; margin-bottom: 18px;" class="grid-2col">
                            <div>
                                <div style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Item Rokok Dipesan:</div>
                                @if($t->details && $t->details->isNotEmpty())
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        @foreach($t->details as $d)
                                            <div style="font-size: 13.5px; color: var(--text-dark);">
                                                <strong style="color: var(--gold-hover);">&bull; {{ $d->barang?->nama ?? 'Produk' }}</strong>: 
                                                {{ $d->jumlah }} {{ $d->satuan }} 
                                                <span style="color: var(--text-muted); font-size: 12px;">
                                                    ({{ $d->total_bungkus }} Bks &bull; {{ $d->formatted_subtotal }})
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($t->barang)
                                    <div style="color: var(--text-dark); font-size: 15px; font-weight: 700;">
                                        {{ $t->barang->nama }} (SKT 12 Batang)
                                    </div>
                                    <div style="color: var(--text-medium); font-size: 13px; margin-top: 2px;">
                                        Jumlah: <strong>{{ $t->jumlah }} {{ $t->satuan }}</strong>
                                        @if($t->satuan === 'Slop')
                                            <span style="color: var(--text-muted);">({{ $t->jumlah * 10 }} Bungkus)</span>
                                        @else
                                            <span style="color: var(--text-muted);">({{ $t->jumlah * ($t->barang->slop_per_bal ?: 20) }} Slop / {{ $t->jumlah * ($t->barang->slop_per_bal ?: 20) * 10 }} Bks)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div style="text-align: right;" class="text-left-mobile">
                                <div style="color: var(--text-muted); font-size: 12px; margin-bottom: 2px;">Total Nilai Pesanan:</div>
                                <div style="color: var(--gold-hover); font-size: 18px; font-weight: 800;">
                                    {{ $t->formatted_total }}
                                </div>
                                <div style="color: var(--text-muted); font-size: 12px;">
                                    Sumber: {{ $t->sumber }}
                                </div>
                            </div>
                        </div>

                        <!-- Info Bukti Penerimaan (Jika Ada) -->
                        @if($t->bukti_penerimaan)
                            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fa-solid fa-camera-retro" style="color: #059669; font-size: 18px;"></i>
                                    <div>
                                        <strong style="color: #065f46; font-size: 13px; display: block;">Bukti Penerimaan Terverifikasi</strong>
                                        <small style="color: var(--text-muted); font-size: 11.5px;">Diterima: {{ $t->diterima_pada ? $t->diterima_pada->format('d M Y, H:i') : '-' }} WIB</small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ asset('storage/' . $t->bukti_penerimaan) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size: 12px; padding: 4px 10px;">
                                        <i class="fa-solid fa-eye"></i> Lihat Foto Bukti
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Tombol Aksi Pesanan -->
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; border-top: 1px solid var(--border-light); padding-top: 14px;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('pesanan.invoice', $t->kode_transaksi) }}" class="btn btn-sm btn-outline-gold" style="font-size: 12px;">
                                    <i class="fa-solid fa-file-invoice"></i> Buka Faktur / Invoice
                                </a>

                                <!-- Tombol Batalkan Pesanan (Khusus jika masih Baru Masuk) -->
                                @if($t->status === 'Baru Masuk')
                                    <form action="{{ route('pesanan.batal', $t->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok akan dipulihkan kembali.')" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline" style="font-size: 12px; color: #ef4444; border-color: rgba(239,68,68,0.4);">
                                            <i class="fa-solid fa-xmark"></i> Batalkan Pesanan
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div>
                                <!-- Tombol Konfirmasi Terima (Khusus jika status Dikirim) -->
                                @if($t->status === 'Dikirim' && !$t->bukti_penerimaan)
                                    <button type="button" class="btn btn-sm btn-gold" onclick="openKonfirmasiModal('{{ $t->id }}', '{{ $t->kode_transaksi }}', '{{ $t->barang->nama }}')">
                                        <i class="fa-solid fa-camera"></i> Konfirmasi Terima & Unggah Bukti
                                    </button>
                                @endif

                                @if($t->status !== 'Dibatalkan')
                                    <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin menanyakan status pesanan ' . $t->kode_transaksi . ' atas nama ' . $t->nama_mitra) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size: 12px;">
                                        <i class="fa-brands fa-whatsapp"></i> Chat Admin Pabrik
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach

                <!-- Pagination -->
                <div style="margin-top: 20px;">
                    {{ $transaksis->links() }}
                </div>
            </div>
        @endif

    </div>
</section>

<!-- Modal Konfirmasi Terima & Upload Foto Bukti -->
<div id="modalKonfirmasiTerima" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(22, 19, 15, 0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-top: 4px solid var(--gold); border-radius: var(--radius-lg); width: 100%; max-width: 500px; padding: 28px; box-shadow: var(--shadow-lg); position: relative;">
        
        <button type="button" onclick="closeKonfirmasiModal()" style="position: absolute; top: 16px; right: 16px; background: none; border: none; color: var(--text-muted); font-size: 22px; cursor: pointer; line-height: 1;">&times;</button>
        
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(5, 150, 105, 0.12); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto 10px;">
                <i class="fa-solid fa-box-check"></i>
            </div>
            <h3 style="color: var(--text-dark); font-size: 18px; margin-bottom: 4px; font-weight: 800;">Konfirmasi Barang Diterima</h3>
            <p style="color: var(--text-muted); font-size: 13px;" id="modalKodeTrx">Kode Transaksi</p>
        </div>

        <form id="formKonfirmasiTerima" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                    Unggah Foto Bukti Fisik Barang Diterima <span style="color: #ef4444;">*</span>
                </label>
                <input type="file" name="foto_bukti" id="inputFotoBukti" class="form-control" required accept="image/*"
                       style="width: 100%; padding: 10px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">
                    Dapat berupa foto tumpukan rokok/slop yang sudah sampai di toko, atau foto kertas surat jalan/nota fisik yang sudah Anda tanda tangani. Maks 5 MB.
                </small>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                    Catatan Penerima (Opsional)
                </label>
                <textarea name="catatan_penerima" class="form-control" rows="2" placeholder="Contoh: Barang diterima dalam kondisi baik, segel cukai utuh..."
                          style="width: 100%; padding: 10px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;"></textarea>
            </div>

            <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fa-solid fa-cloud-arrow-up"></i> Konfirmasi & Unggah Bukti
            </button>
        </form>
    </div>
</div>

<!-- Modal Ubah Data Profil & Kata Sandi Mitra -->
<div id="modalUbahProfilMitra" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #ffffff; border-radius: var(--radius-lg); width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 12px 30px rgba(0,0,0,0.3); border: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-light); padding-bottom: 12px;">
            <div>
                <h3 style="font-family: var(--font-serif); font-size: 18px; font-weight: 700; color: var(--gold-hover); margin: 0;">
                    <i class="fa-solid fa-store" style="margin-right: 6px;"></i> Ubah Data Toko & Kata Sandi
                </h3>
                <small style="color: var(--text-muted); font-size: 12px;">Perbarui data identitas kemitraan dan kredensial akun Anda</small>
            </div>
            <button type="button" onclick="closeMitraProfileModal()" style="background: none; border: none; font-size: 22px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('mitra.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">
                        Nama Pemilik <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">
                        Nama Toko / Usaha <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $user->nama_toko) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">
                        Alamat Email <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">
                        Nomor WhatsApp <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="tel" name="telepon" value="{{ old('telepon', $user->telepon) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">
                    Alamat Pengiriman Toko <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="alamat" rows="2" required style="width: 100%; padding: 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px;">{{ old('alamat', $user->alamat) }}</textarea>
            </div>

            <div class="form-group" style="margin-bottom: 22px; background: #fafaf9; padding: 12px; border-radius: 6px; border: 1px dashed var(--border-light);">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">
                    Kata Sandi Baru (Opsional)
                </label>
                <div style="position: relative;">
                    <input type="password" name="password" id="inputMitraModalPass" minlength="6" placeholder="Kosongkan jika tidak ingin mengganti kata sandi"
                           style="width: 100%; padding: 10px 38px 10px 10px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 13px; background: #fff;">
                    <button type="button" onclick="togglePasswordVisibility('inputMitraModalPass', 'toggleMitraModalEyeIcon')" 
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; font-size: 14px;">
                        <i id="toggleMitraModalEyeIcon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">
                    Isi hanya jika Anda ingin memperbarui kata sandi akun toko (minimal 6 karakter).
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeMitraProfileModal()" class="btn btn-secondary btn-sm" style="padding: 10px 16px;">
                    Batal
                </button>
                <button type="submit" class="btn btn-gold btn-sm" style="padding: 10px 20px; font-weight: 700;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openKonfirmasiModal(id, kode, namaBarang) {
    const modal = document.getElementById('modalKonfirmasiTerima');
    const form = document.getElementById('formKonfirmasiTerima');
    const label = document.getElementById('modalKodeTrx');

    form.action = `/pesanan/${id}/konfirmasi-terima`;
    label.innerText = `${kode} • ${namaBarang}`;
    modal.style.display = 'flex';
}

function closeKonfirmasiModal() {
    document.getElementById('modalKonfirmasiTerima').style.display = 'none';
}

function openMitraProfileModal() {
    document.getElementById('modalUbahProfilMitra').style.display = 'flex';
}

function closeMitraProfileModal() {
    document.getElementById('modalUbahProfilMitra').style.display = 'none';
}

function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection
