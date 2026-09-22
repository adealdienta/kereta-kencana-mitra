@extends('layouts.app')

@section('title', 'Faktur Pesanan B2B #' . $transaksi->kode_transaksi)

@section('content')
<section class="section-py">
    <div class="container" style="max-width: 860px;">
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;" class="no-print">
            <a href="{{ route('pesanan.form') }}" style="color: var(--gold); text-decoration: none; font-size: 14px;">
                <i class="fa-solid fa-arrow-left"></i> Buat Pesanan Baru
            </a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-print"></i> Cetak Dokumen Faktur
            </button>
        </div>

        <div class="invoice-box" style="background: #ffffff; color: #1e293b; border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-lg); font-size: 14px;">
            <!-- Header Faktur -->
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 24px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" style="height: 60px; width: auto; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px; background: #fff;">
                    <div>
                        <h2 style="font-family: var(--font-serif); color: #0f1316; font-size: 24px; margin-bottom: 4px;">
                            PR. KERETA KENCANA
                        </h2>
                        <p style="color: #64748b; font-size: 12px; margin-bottom: 2px;">Pabrik Sigaret Kretek Mesin & Sigaret Kretek Tangan</p>
                        <p style="color: #64748b; font-size: 12px; margin-bottom: 2px;">Dsn. Subontoro, Ds. Kebonduren, Kec. Ponggok, Blitar</p>
                        <p style="color: #059669; font-size: 12px; font-weight: 600;">NPPBKC: 0821.1.2.XXXXX (Bea Cukai Blitar)</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="background: #f1f5f9; padding: 6px 12px; border-radius: 4px; display: inline-block; font-weight: 700; color: #0f1316; margin-bottom: 8px;">
                        SURAT PESANAN B2B
                    </div>
                    <div style="font-family: monospace; font-size: 16px; font-weight: 700; color: #b45309;">
                        #{{ $transaksi->kode_transaksi }}
                    </div>
                    <small style="color: #64748b;">Tanggal: {{ $transaksi->created_at->format('d/m/Y H:i') }} WIB</small>
                </div>
            </div>

            <!-- Identitas Mitra -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 24px;">
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 4px;">Tujuan Pengiriman Mitra:</span>
                    <strong style="font-size: 16px; color: #0f1316;">{{ $transaksi->nama_mitra }}</strong>
                    <div style="color: #475569; margin-top: 4px;"><i class="fa-solid fa-phone"></i> {{ $transaksi->telepon }}</div>
                    <div style="color: #475569; margin-top: 4px;"><i class="fa-solid fa-location-dot"></i> {{ $transaksi->alamat }}</div>
                </div>
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 4px;">Status Operasional Pabrik:</span>
                    <div style="margin-bottom: 6px;">
                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 13px;">
                            <i class="fa-solid fa-clock-rotate-left"></i> {{ $transaksi->status }}
                        </span>
                    </div>
                    @if($transaksi->nomor_do)
                        <div style="color: #475569;"><strong>No. DO:</strong> {{ $transaksi->nomor_do }}</div>
                    @endif
                    @if($transaksi->nomor_resi)
                        <div style="color: #475569;"><strong>No. Resi Truk:</strong> {{ $transaksi->nomor_resi }}</div>
                    @endif
                </div>
            </div>

            <!-- Tabel Pesanan -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                <thead>
                    <tr style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 12px;">Varian Produk Kretek</th>
                        <th style="padding: 12px; text-align: center;">Satuan Kemasan</th>
                        <th style="padding: 12px; text-align: center;">Volume Pesanan</th>
                        <th style="padding: 12px; text-align: right;">Harga Satuan</th>
                        <th style="padding: 12px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 14px 12px;">
                            <strong>{{ $transaksi->barang->nama }}</strong>
                            <div style="font-size: 12px; color: #64748b;">
                                {{ $transaksi->barang->kategori->nama_kategori }} | Kode: {{ $transaksi->barang->kode_barang }}
                            </div>
                        </td>
                        <td style="padding: 14px 12px; text-align: center;">
                            @if($transaksi->satuan === 'Slop')
                                1 Slop ({{ $transaksi->barang->bungkus_per_slop }} Bungkus)
                            @else
                                1 Bal ({{ $transaksi->barang->slop_per_bal }} Slop / {{ $transaksi->barang->slop_per_bal * $transaksi->barang->bungkus_per_slop }} Bungkus)
                            @endif
                        </td>
                        <td style="padding: 14px 12px; text-align: center; font-weight: 700;">
                            {{ $transaksi->jumlah }} {{ $transaksi->satuan }}
                        </td>
                        <td style="padding: 14px 12px; text-align: right;">
                            Rp {{ number_format($transaksi->total_harga / max(1, $transaksi->jumlah), 0, ',', '.') }} / {{ $transaksi->satuan }}
                        </td>
                        <td style="padding: 14px 12px; text-align: right; font-weight: 700; color: #0f1316;">
                            {{ $transaksi->formatted_total }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="padding: 14px 12px; text-align: right; font-weight: 700; font-size: 15px;">TOTAL NILAI FAKTUR B2B:</td>
                        <td style="padding: 14px 12px; text-align: right; font-weight: 800; font-size: 18px; color: #b45309;">
                            {{ $transaksi->formatted_total }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if($transaksi->catatan)
                <div style="background: #f8fafc; border-left: 4px solid #b45309; padding: 12px 16px; margin-bottom: 24px; font-size: 13px;">
                    <strong>Catatan Mitra:</strong> {{ $transaksi->catatan }}
                </div>
            @endif

            <!-- Instruksi & Tanda Tangan -->
            <div style="border-top: 1px dashed #cbd5e1; padding-top: 20px; display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">
                <div style="font-size: 12px; color: #64748b; line-height: 1.6;">
                    <strong>Ketentuan Distribusi:</strong>
                    <ol style="padding-left: 16px; margin-top: 4px;">
                        <li>Faktur ini merupakan dokumen resmi pemesanan produk tembakau PR. KERETA KENCANA.</li>
                        <li>Pastikan konfirmasi nomor pesanan ke WhatsApp manajemen pabrik untuk alokasi nomor armada pengiriman.</li>
                        <li>Barang yang dikirim telah dilengkapi pita cukai Republik Indonesia resmi dan utuh.</li>
                    </ol>
                </div>
                <div style="text-align: center; font-size: 12px; color: #475569;">
                    <div>Ponggok, Blitar, {{ date('d M Y') }}</div>
                    <div style="margin-top: 60px; font-weight: 700; text-decoration: underline;">PR. KERETA KENCANA</div>
                    <div>Bagian Logistik & Distribusi</div>
                </div>
            </div>

            <!-- Tombol Konfirmasi WhatsApp (No Print) -->
            <div style="margin-top: 30px; text-align: center;" class="no-print">
                <a href="https://wa.me/6281234567890?text={{ $pesanWa }}" target="_blank" 
                   class="btn btn-gold" style="padding: 14px 28px; font-size: 15px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-brands fa-whatsapp" style="font-size: 20px;"></i> Konfirmasi Pesanan ke WhatsApp Pabrik
                </a>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
@media print {
    .no-print, .main-header, .main-footer, .government-warning, .float-wa-btn {
        display: none !important;
    }
    body {
        background: #fff !important;
        color: #000 !important;
    }
    .invoice-box {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endpush
@endsection
