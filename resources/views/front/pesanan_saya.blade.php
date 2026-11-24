@extends('layouts.app')

@section('title', 'Pelacakan Pesanan Saya')

@section('content')
<!-- Header Banner -->
<section class="page-header">
    <div class="container">
        <span class="header-badge">Status Pengiriman &amp; Faktur</span>
        <h1 class="page-title">Pelacakan Pesanan Saya</h1>
        <p class="page-subtitle" style="margin: 0;">Pantau tahapan proses pesanan rokok Anda dari lini produksi hingga tiba di toko</p>
    </div>
</section>

<section class="section-py">
    <div class="container" style="max-width: 1000px;">
        
        <!-- Mitra Profile Card -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-light); border-top: 2px solid var(--text-dark); border-radius: 2px; padding: 20px 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div>
                <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 19px; margin-bottom: 3px;">{{ $user->nama_toko ?: $user->name }}</h3>
                <p style="color: var(--text-muted); font-size: 13.5px; margin: 0;">
                    Pemilik: <strong>{{ $user->name }}</strong> &bull; <span class="font-mono" style="font-size: 12.5px;">{{ $user->telepon ?: '-' }}</span> &bull; {{ $user->email }}
                </p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="openMitraProfileModal()" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user-pen"></i> Ubah Data &amp; Sandi
                </button>
                <a href="{{ route('pesanan.form') }}" class="btn btn-gold btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-cart-plus"></i> Buat Pesanan Baru
                </a>
            </div>
        </div>

        @if($pendingDelivery)
            <!-- Alert Khusus Pesanan Siap Konfirmasi -->
            <div style="background: #fefce8; border: 1px solid #d97706; border-radius: 2px; padding: 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div>
                    <strong style="color: #854d0e; font-size: 15px; display: block;">Barang Sedang Dikirim ke Toko Anda</strong>
                    <p style="color: #713f12; font-size: 13.5px; margin: 2px 0 0 0;">
                        Pesanan <strong class="font-mono">{{ $pendingDelivery->kode_transaksi }}</strong> ({{ $pendingDelivery->ringkasan_item }}) sedang di jalan. Saat kurir tiba, segera unggah foto barang yang Anda terima untuk menyelesaikan pesanan.
                    </p>
                </div>
                <button type="button" class="btn btn-sm btn-gold" onclick='openKonfirmasiModal(@json($pendingDelivery->id), @json($pendingDelivery->kode_transaksi), @json($pendingDelivery->ringkasan_item))'>
                    <i class="fa-solid fa-camera"></i> Konfirmasi Terima Sekarang
                </button>
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px 16px; border-radius: 2px; margin-bottom: 20px; font-size: 13.5px;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- Daftar Transaksi Pelanggan -->
        @if($transaksis->isEmpty())
            <div style="background: var(--bg-surface); border: 1px dashed var(--border-light); border-radius: 2px; padding: 48px 20px; text-align: center;">
                <h3 style="font-family: var(--font-serif); color: var(--text-dark); font-size: 20px; margin-bottom: 6px;">Belum Ada Riwayat Pemesanan</h3>
                <p style="color: var(--text-muted); font-size: 14px; max-width: 450px; margin: 0 auto 20px;">
                    Anda belum pernah membuat transaksi pemesanan rokok. Silakan buka formulir pemesanan untuk memulai order perdana Anda.
                </p>
                <a href="{{ route('pesanan.form') }}" class="btn btn-gold">
                    Pesan Rokok Sekarang (Mulai 1 Slop)
                </a>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($transaksis as $t)
                    @php
                        $isTransferBank = $t->metode_pembayaran && stripos($t->metode_pembayaran, 'transfer') !== false;
                        $paymentStatus = $t->status_pembayaran ?? ($t->status === 'Selesai' ? 'Lunas' : 'Belum Dibayar');
                    @endphp
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: 2px; padding: 24px;">
                        
                        <!-- Header Transaksi -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid var(--border-light); padding-bottom: 16px; margin-bottom: 18px;">
                            <div>
                                <span class="font-mono" style="font-size: 14.5px; font-weight: 600; color: var(--text-dark); letter-spacing: 0.02em;">
                                    {{ $t->kode_transaksi }}
                                </span>
                                <div style="color: var(--text-muted); font-size: 12.5px; margin-top: 3px;">
                                    Dipesan pada: {{ $t->created_at->format('d M Y, H:i') }} WIB
                                </div>
                                @if($t->nomor_do || $t->nomor_resi)
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; font-size: 11.5px;">
                                        @if($t->nomor_do)
                                            <span class="font-mono" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #0f172a; padding: 2px 8px; border-radius: 2px;">
                                                No. DO: <strong>{{ $t->nomor_do }}</strong>
                                            </span>
                                        @endif
                                        @if($t->nomor_resi)
                                            <span class="font-mono" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #0f172a; padding: 2px 8px; border-radius: 2px;">
                                                Resi: <strong>{{ $t->nomor_resi }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Status Badge Berbasis Kontras Tinggi -->
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                @if($t->status === 'Baru Masuk')
                                    <span style="background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; padding: 5px 12px; border-radius: 2px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-regular fa-clock"></i> Menunggu Konfirmasi Pabrik
                                    </span>
                                @elseif($t->status === 'Diproses')
                                    <span style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; padding: 5px 12px; border-radius: 2px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-gears"></i> Sedang Diproses Gudang
                                    </span>
                                @elseif($t->status === 'Dikirim')
                                    <span style="background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe; padding: 5px 12px; border-radius: 2px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-truck-fast"></i> Sedang Diantar Kurir Pabrik
                                    </span>
                                @elseif($t->status === 'Selesai')
                                    <span style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 5px 12px; border-radius: 2px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-circle-check"></i> Pesanan Selesai (Diterima)
                                    </span>
                                @else
                                    <span style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 5px 12px; border-radius: 2px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-ban"></i> Dibatalkan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Stepper Progres Barang (Light Editorial Style) -->
                        @if($t->status !== 'Dibatalkan')
                            <div style="background: var(--kk-ivory); border: 1px solid var(--kk-ivory-border); border-radius: 2px; padding: 14px 18px; margin-bottom: 20px;">
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; text-align: center;">
                                    
                                    <!-- Step 1: Diterbitkan -->
                                    <div style="color: {{ in_array($t->status, ['Baru Masuk', 'Diproses', 'Dikirim', 'Selesai']) ? 'var(--text-dark)' : '#a1a1aa' }};">
                                        <div style="font-size: 18px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">1. Order Diterbitkan</div>
                                    </div>

                                    <!-- Step 2: Diproses Gudang -->
                                    <div style="color: {{ in_array($t->status, ['Diproses', 'Dikirim', 'Selesai']) ? 'var(--text-dark)' : '#a1a1aa' }};">
                                        <div style="font-size: 18px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-boxes-packing"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">2. Diproses Gudang</div>
                                    </div>

                                    <!-- Step 3: Dikirim -->
                                    <div style="color: {{ in_array($t->status, ['Dikirim', 'Selesai']) ? 'var(--text-dark)' : '#a1a1aa' }};">
                                        <div style="font-size: 18px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-truck"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">3. Sedang Dikirim</div>
                                    </div>

                                    <!-- Step 4: Selesai -->
                                    <div style="color: {{ $t->status === 'Selesai' ? '#15803d' : '#a1a1aa' }};">
                                        <div style="font-size: 18px; margin-bottom: 4px;">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 600;">4. Diterima Toko</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Detail Produk, Harga & Pembayaran -->
                        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; margin-bottom: 18px;" class="grid-2col">
                            <div>
                                <div style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Item Rokok Dipesan:</div>
                                @if($t->details && $t->details->isNotEmpty())
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        @foreach($t->details as $d)
                                            <div style="font-size: 13.5px; color: var(--text-dark);">
                                                <strong style="color: var(--text-dark);">&bull; {{ $d->barang?->nama ?? 'Produk' }}</strong>: 
                                                <span class="font-mono">{{ $d->jumlah }} {{ $d->satuan }}</span> 
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
                                            <span style="color: var(--text-muted);">({{ $t->jumlah * ($t->barang->bungkus_per_slop ?: 10) }} Bungkus)</span>
                                        @else
                                            <span style="color: var(--text-muted);">({{ $t->jumlah * ($t->barang->slop_per_bal ?: 20) }} Slop &bull; Kemasan Bal)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div style="text-align: right;" class="text-left-mobile">
                                <div style="color: var(--text-muted); font-size: 12px; margin-bottom: 2px;">Total Nilai Pesanan:</div>
                                <div class="font-mono" style="color: var(--text-dark); font-size: 18px; font-weight: 700;">
                                    {{ $t->formatted_total }}
                                </div>
                                <div style="color: var(--text-medium); font-size: 12px; margin-top: 3px;">
                                    {{ $t->metode_pembayaran ?? 'Bayar di Tempat (COD)' }} &bull;
                                    <strong style="color: {{ $paymentStatus === 'Lunas' ? '#15803d' : ($paymentStatus === 'Menunggu Verifikasi' ? '#b45309' : (str_contains($paymentStatus, 'Ditolak') ? '#b91c1c' : '#52525b')) }};">
                                        {{ $paymentStatus }}
                                    </strong>
                                </div>
                                <div style="color: var(--text-muted); font-size: 11.5px; margin-top: 2px;">
                                    Sumber: {{ $t->sumber }}
                                </div>
                            </div>
                        </div>

                        <!-- Info Transfer Bank & Upload Bukti -->
                        @if($isTransferBank && $t->status !== 'Dibatalkan')
                            <div style="background: #f8fafc; border: 1px solid var(--border-light); border-left: 3px solid var(--text-dark); border-radius: 2px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div style="font-size: 12.5px; color: var(--text-dark); line-height: 1.5;">
                                    @if($t->bukti_transfer)
                                        <strong><i class="fa-solid fa-receipt"></i> Bukti Transfer Terunggah</strong>
                                        <span style="color: var(--text-muted); display: block; font-size: 11.5px;">
                                            Diunggah: {{ $t->ditransfer_pada ? $t->ditransfer_pada->format('d M Y, H:i') : '-' }} WIB &bull; Status: <strong>{{ $paymentStatus }}</strong>
                                        </span>
                                    @elseif(!empty($bankInfo['rekening']))
                                        <strong>Pembayaran Transfer {{ $bankInfo['nama'] ?? 'Bank Resmi' }}:</strong>
                                        <span class="font-mono">{{ $bankInfo['rekening'] }}</span> a.n. {{ $bankInfo['atas_nama'] ?? 'PR. KERETA KENCANA' }}
                                        <span style="color: var(--text-muted); display: block; font-size: 11.5px;">
                                            Silakan lakukan transfer sesuai total faktur lalu unggah foto bukti transfer.
                                        </span>
                                    @else
                                        <strong>Pembayaran Transfer Bank:</strong>
                                        <span style="color: var(--text-muted); display: block; font-size: 11.5px;">
                                            Nomor rekening resmi pabrik dikonfirmasi langsung oleh Admin via WhatsApp (<span class="font-mono">{{ $pabrikWaDisplay ?? '+62 857-9062-7104' }}</span>), lalu unggah foto bukti transfer di sini.
                                        </span>
                                    @endif
                                </div>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    @if($t->bukti_transfer)
                                        <a href="{{ asset('storage/' . $t->bukti_transfer) }}" target="_blank" class="btn btn-sm btn-secondary" style="font-size: 12px; padding: 5px 10px;">
                                            <i class="fa-solid fa-eye"></i> Lihat Struk
                                        </a>
                                    @endif
                                    @if($paymentStatus !== 'Lunas')
                                        <button type="button" class="btn btn-sm btn-gold" onclick='openTransferModal(@json($t->id), @json($t->kode_transaksi), @json($t->formatted_total))' style="font-size: 12px;">
                                            <i class="fa-solid fa-upload"></i> {{ $t->bukti_transfer ? 'Unggah Ulang Struk' : 'Unggah Bukti Transfer' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Info Bukti Penerimaan (Jika Ada) -->
                        @if($t->bukti_penerimaan)
                            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 2px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fa-solid fa-camera-retro" style="color: #059669; font-size: 18px;"></i>
                                    <div>
                                        <strong style="color: #065f46; font-size: 13px; display: block;">Bukti Penerimaan Terverifikasi</strong>
                                        <small style="color: var(--text-muted); font-size: 11.5px;">Diterima: {{ $t->diterima_pada ? $t->diterima_pada->format('d M Y, H:i') : '-' }} WIB</small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ asset('storage/' . $t->bukti_penerimaan) }}" target="_blank" class="btn btn-sm btn-secondary" style="font-size: 12px; padding: 4px 10px;">
                                        <i class="fa-solid fa-eye"></i> Lihat Foto Bukti
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Tombol Aksi Pesanan -->
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; border-top: 1px solid var(--border-light); padding-top: 14px;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('pesanan.invoice', $t->kode_transaksi) }}" class="btn btn-sm btn-outline-gold" style="font-size: 12px;">
                                    <i class="fa-solid fa-file-invoice"></i> Buka Faktur / Invoice
                                </a>

                                <!-- Tombol Batalkan Pesanan (Khusus jika masih Baru Masuk dan belum Lunas) -->
                                @if($t->status === 'Baru Masuk' && $paymentStatus !== 'Lunas')
                                    <form action="{{ route('pesanan.batal', $t->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok akan dipulihkan kembali.')" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 12px; color: #b91c1c; border-color: #fca5a5;">
                                            <i class="fa-solid fa-xmark"></i> Batalkan Pesanan
                                        </button>
                                    </form>
                                @elseif($t->status === 'Baru Masuk' && $paymentStatus === 'Lunas')
                                    <span style="font-size: 12px; color: var(--text-muted); padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 4px; background: #f8fafc;">
                                        <i class="fa-solid fa-circle-info"></i> Pembayaran Lunas — Hubungi Admin untuk pembatalan &amp; pengembalian dana
                                    </span>
                                @endif
                            </div>

                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <!-- Tombol Konfirmasi Terima (Khusus jika status Dikirim) -->
                                @if($t->status === 'Dikirim' && !$t->bukti_penerimaan)
                                    <button type="button" class="btn btn-sm btn-gold" onclick='openKonfirmasiModal(@json($t->id), @json($t->kode_transaksi), @json($t->ringkasan_item))'>
                                        <i class="fa-solid fa-camera"></i> Konfirmasi Terima &amp; Unggah Bukti
                                    </button>
                                @endif

                                @if($t->status !== 'Dibatalkan')
                                    <a href="https://wa.me/{{ $pabrikWa ?? config('pabrik.wa_pabrik', '6285790627104') }}?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin menanyakan status pesanan ' . $t->kode_transaksi . ' atas nama ' . $t->nama_mitra) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-secondary" style="font-size: 12px;">
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
    <div style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-top: 4px solid var(--text-dark); border-radius: var(--radius-lg); width: 100%; max-width: 500px; padding: 28px; box-shadow: var(--shadow-lg); position: relative;">
        
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
                <i class="fa-solid fa-cloud-arrow-up"></i> Konfirmasi &amp; Unggah Bukti
            </button>
        </form>
    </div>
</div>

<!-- Modal Unggah Bukti Transfer Bank -->
<div id="modalUploadTransfer" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(22, 19, 15, 0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1.5px solid var(--border-light); border-top: 4px solid var(--text-dark); border-radius: var(--radius-lg); width: 100%; max-width: 500px; padding: 28px; box-shadow: var(--shadow-lg); position: relative;">
        <button type="button" onclick="closeTransferModal()" style="position: absolute; top: 16px; right: 16px; background: none; border: none; color: var(--text-muted); font-size: 22px; cursor: pointer; line-height: 1;">&times;</button>

        <div style="text-align: center; margin-bottom: 18px;">
            <h3 style="color: var(--text-dark); font-size: 18px; margin-bottom: 4px; font-weight: 800;">Unggah Bukti Transfer Bank</h3>
            <p style="color: var(--text-muted); font-size: 13px; margin: 0;" id="modalTransferLabel">Kode Transaksi</p>
        </div>

        <div style="background: var(--kk-ivory); border: 1px solid var(--kk-ivory-border); padding: 12px 14px; font-size: 12.5px; color: var(--text-dark); margin-bottom: 16px; line-height: 1.55;">
            @if(!empty($bankInfo['rekening']))
                <strong>{{ $bankInfo['nama'] ?? 'Bank Resmi Pabrik' }}</strong><br>
                No. Rek: <span class="font-mono" style="font-weight: 700;">{{ $bankInfo['rekening'] }}</span> &bull; a.n. <strong>{{ $bankInfo['atas_nama'] ?? 'PR. KERETA KENCANA' }}</strong>
            @else
                <strong>Transfer Bank</strong><br>
                Pastikan Anda telah menerima konfirmasi nomor rekening resmi dari Admin WhatsApp (<span class="font-mono">{{ $pabrikWaDisplay ?? '+62 857-9062-7104' }}</span>) sebelum mengunggah struk.
            @endif
        </div>

        <form id="formUploadTransfer" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                    Foto Struk / Screenshot Transfer <span style="color: #ef4444;">*</span>
                </label>
                <input type="file" name="bukti_transfer" class="form-control" required accept="image/*"
                       style="width: 100%; padding: 10px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">Format: JPG, PNG, atau WEBP (Maks. 5 MB).</small>
            </div>
            <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fa-solid fa-cloud-arrow-up"></i> Kirim Bukti Transfer
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

function openTransferModal(id, kode, totalFormatted) {
    const modal = document.getElementById('modalUploadTransfer');
    const form = document.getElementById('formUploadTransfer');
    const label = document.getElementById('modalTransferLabel');

    form.action = `/pesanan/${id}/upload-bukti-transfer`;
    label.innerText = `${kode} • Tagihan ${totalFormatted}`;
    modal.style.display = 'flex';
}

function closeTransferModal() {
    document.getElementById('modalUploadTransfer').style.display = 'none';
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

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.style.display = 'none';
        }
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeKonfirmasiModal();
        closeTransferModal();
        closeMitraProfileModal();
    }
});
</script>
@endpush
@endsection
