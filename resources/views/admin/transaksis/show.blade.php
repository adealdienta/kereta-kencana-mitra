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
                <span style="color: #64748b; font-size: 12px;">Diterbitkan: {{ $transaksi->created_at->format('d F Y H:i') }} WIB</span>
            </div>
            <div>
                <a href="{{ route('admin.transaksis.faktur', $transaksi->id) }}" target="_blank" 
                   style="background: #0f1316; color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; font-weight: 600;">
                    <i class="fa-solid fa-print"></i> Cetak Faktur B2B
                </a>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="font-size: 13px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">Informasi Distributor:</h4>
            <div style="font-size: 15px; font-weight: 700; color: #1e293b;">{{ $transaksi->nama_mitra }}</div>
            <div style="color: #475569; font-size: 13px; margin-top: 4px;"><i class="fa-solid fa-phone"></i> {{ $transaksi->telepon }}</div>
            <div style="color: #475569; font-size: 13px; margin-top: 4px;"><i class="fa-solid fa-location-dot"></i> {{ $transaksi->alamat }}</div>
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
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 12px; font-size: 13px; color: #92400e;">
                <strong>Catatan Mitra:</strong> {{ $transaksi->catatan }}
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
                    <option value="Baru Masuk" {{ $transaksi->status === 'Baru Masuk' ? 'selected' : '' }}>Baru Masuk</option>
                    <option value="Diproses" {{ $transaksi->status === 'Diproses' ? 'selected' : '' }}>Diproses Pabrik</option>
                    <option value="Selesai" {{ $transaksi->status === 'Selesai' ? 'selected' : '' }}>Selesai / Terkirim</option>
                    <option value="Dibatalkan" {{ $transaksi->status === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <small style="color: #64748b; font-size: 11px;">*Jika dibatalkan, sistem akan otomatis memulihkan stok bal ke gudang.</small>
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
    </div>
</div>
@endsection
