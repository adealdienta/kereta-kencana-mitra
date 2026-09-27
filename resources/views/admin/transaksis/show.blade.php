@extends('layouts.admin')

@section('title', 'Rincian Pesanan #' . $transaksi->kode_transaksi)
@section('header_title', 'Pemrosesan Transaksi & Pengiriman Pabrik')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.transaksis.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px;">
        &larr; Kembali ke Daftar Pesanan
    </a>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px;">
    <!-- Rincian Pesanan -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 4px;">#{{ $transaksi->kode_transaksi }}</h3>
                <span style="color: #64748b; font-size: 12px;">Diterbitkan: {{ $transaksi->created_at->format('d F Y H:i') }} WIB &bull; Saluran: <strong>{{ $transaksi->sumber }}</strong></span>
            </div>
            <div>
                <a href="{{ route('admin.transaksis.faktur', $transaksi->id) }}" target="_blank" 
                   style="background: #0f1316; color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; font-weight: 600;">
                    <i class="fa-solid fa-print"></i> Cetak Faktur B2B
                </a>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="font-size: 13px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">Informasi Mitra / Pembeli:</h4>
            <div style="font-size: 15px; font-weight: 700; color: #1e293b;">{{ $transaksi->nama_mitra }}</div>
            <div style="color: #475569; font-size: 13px; margin-top: 4px;"><i class="fa-solid fa-phone"></i> {{ $transaksi->telepon }}</div>
            <div style="color: #475569; font-size: 13px; margin-top: 4px;"><i class="fa-solid fa-location-dot"></i> {{ $transaksi->alamat }}</div>
            @if($transaksi->user)
                <div style="color: #0284c7; font-size: 12px; margin-top: 4px;">
                    <i class="fa-solid fa-id-badge"></i> Akun Terdaftar: {{ $transaksi->user->name }} ({{ $transaksi->user->email }})
                </div>
            @endif
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin-bottom: 20px;">
            <h4 style="font-size: 13px; text-transform: uppercase; color: #94a3b8; margin-bottom: 10px;">Item Rokok Dipesan:</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="font-size: 15px; color: #0f1316;">{{ $transaksi->barang->nama }}</strong>
                    <div style="font-size: 12px; color: #64748b;">
                        {{ $transaksi->barang->kategori->nama_kategori }} ({{ $transaksi->barang->slop_per_bal }} Slop/Bal)
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 14px; font-weight: 700;">{{ $transaksi->jumlah }} {{ $transaksi->satuan }}</div>
                    <div style="font-size: 12px; color: #64748b;">Rp {{ number_format($transaksi->total_harga / max(1, $transaksi->jumlah), 0, ',', '.') }} / {{ $transaksi->satuan }}</div>
                </div>
            </div>
            <div style="border-top: 1px dashed #cbd5e1; margin-top: 12px; padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; font-size: 14px;">Total Nilai Pesanan:</span>
                <span style="font-size: 18px; font-weight: 800; color: #b45309;">{{ $transaksi->formatted_total }}</span>
            </div>
        </div>

        @if($transaksi->catatan)
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 12px; font-size: 13px; color: #92400e; margin-bottom: 16px;">
                <strong>Catatan Pemesanan:</strong> {{ $transaksi->catatan }}
            </div>
        @endif

        <!-- Bukti Penerimaan Barang -->
        @if($transaksi->bukti_penerimaan)
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <i class="fa-solid fa-camera-retro" style="color: #059669; font-size: 18px;"></i>
                    <h4 style="font-size: 14px; color: #065f46; margin: 0; font-weight: 700;">Bukti Foto Penerimaan Barang</h4>
                </div>
                <p style="color: #047857; font-size: 12.5px; margin: 0 0 10px 0;">
                    Dikonfirmasi Diterima: {{ $transaksi->diterima_pada ? $transaksi->diterima_pada->format('d F Y, H:i') : '-' }} WIB
                    @if($transaksi->catatan_penerima)
                        <br><strong>Catatan:</strong> {{ $transaksi->catatan_penerima }}
                    @endif
                </p>
                <div>
                    <a href="{{ asset('storage/' . $transaksi->bukti_penerimaan) }}" target="_blank">
                        <img src="{{ asset('storage/' . $transaksi->bukti_penerimaan) }}" alt="Foto Bukti Terima" 
                             style="max-width: 100%; max-height: 240px; border-radius: 6px; border: 1px solid #cbd5e1; object-fit: contain;">
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Tindakan Operasional Staf -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
        <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 16px;">
            <i class="fa-solid fa-truck-ramp-box"></i> Status & Logistik Pengiriman
        </h3>

        <form action="{{ route('admin.transaksis.update', $transaksi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Status Pesanan</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    <option value="Baru Masuk" {{ $transaksi->status === 'Baru Masuk' ? 'selected' : '' }}>Baru Masuk (Antrean)</option>
                    <option value="Diproses" {{ $transaksi->status === 'Diproses' ? 'selected' : '' }}>Diproses Gudang</option>
                    <option value="Dikirim" {{ $transaksi->status === 'Dikirim' ? 'selected' : '' }}>Sedang Dikirim Armada Pabrik</option>
                    <option value="Selesai" {{ $transaksi->status === 'Selesai' ? 'selected' : '' }}>Selesai / Diterima</option>
                    <option value="Dibatalkan" {{ $transaksi->status === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <small style="color: #64748b; font-size: 11px;">*Status 'Dikirim' akan memicu notifikasi konfirmasi di HP pembeli.</small>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Nomor DO (Delivery Order Pabrik)</label>
                <input type="text" name="nomor_do" value="{{ old('nomor_do', $transaksi->nomor_do) }}" placeholder="Contoh: DO-KK-2026-0901"
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Nomor Resi / Surat Jalan Ekspedisi</label>
                <input type="text" name="nomor_resi" value="{{ old('nomor_resi', $transaksi->nomor_resi) }}" placeholder="Contoh: CARGO-BLITAR-994"
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <button type="submit" style="background: var(--admin-gold); color: #fff; border: none; padding: 12px 20px; border-radius: 6px; font-weight: 700; width: 100%; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Simpan Pembaharuan Operasional
            </button>
        </form>

        <!-- Tombol Verifikasi Manual oleh Admin via Nota Fisik -->
        @if($transaksi->status === 'Dikirim' && !$transaksi->bukti_penerimaan)
            <div style="border-top: 1px dashed #cbd5e1; margin-top: 20px; padding-top: 16px;">
                <div style="font-size: 12px; color: #64748b; margin-bottom: 8px;">
                    <i class="fa-solid fa-info-circle"></i> Jika kurir pabrik telah kembali dengan membawa Surat Jalan/Nota fisik bertanda tangan toko:
                </div>
                <form action="{{ route('admin.transaksis.verifikasi_manual', $transaksi->id) }}" method="POST" onsubmit="return confirm('Verifikasi pesanan {{ $transaksi->kode_transaksi }} sebagai Selesai berdasarkan surat jalan fisik?')">
                    @csrf
                    <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 10px 16px; border-radius: 6px; font-size: 12.5px; font-weight: 700; width: 100%; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa-solid fa-stamp"></i> Selesaikan Manual via Nota Fisik (Bypass Foto)
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
