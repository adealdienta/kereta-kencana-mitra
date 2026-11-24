@extends('layouts.app')

@section('title', 'Faktur Pesanan B2B #' . $transaksi->kode_transaksi)

@section('content')
<section class="section-py">
    <div class="container" style="max-width: 860px;">
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;" class="no-print">
            <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                @if(Auth::check() && Auth::user()->isPelanggan())
                    <a href="{{ route('pesanan.saya') }}" style="color: var(--text-dark); text-decoration: none; font-size: 14px; font-weight: 600;">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Pesanan Saya
                    </a>
                    <span style="color: var(--border-light);">|</span>
                    <a href="{{ route('pesanan.form') }}" style="color: var(--text-medium); text-decoration: none; font-size: 14px;">
                        <i class="fa-solid fa-cart-plus"></i> Buat Pesanan Baru
                    </a>
                @elseif(Auth::check())
                    <a href="{{ route('admin.transaksis.show', $transaksi->id) }}" style="color: var(--text-dark); text-decoration: none; font-size: 14px; font-weight: 600;">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail Transaksi Admin
                    </a>
                @endif
            </div>
            <button onclick="window.print()" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-print"></i> Cetak Dokumen Faktur
            </button>
        </div>

        <div class="invoice-box" style="background: #ffffff; color: #1e293b; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-lg); font-size: 14px;">
            <!-- Header Faktur -->
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 24px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <img src="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}" alt="PR. Kereta Kencana" style="height: 60px; width: auto; object-fit: contain;">
                    <div>
                        <h2 style="font-family: var(--font-serif); color: #0f1316; font-size: 24px; margin-bottom: 4px;">
                            PR. KERETA KENCANA
                        </h2>
                        <p style="color: #64748b; font-size: 12px; margin-bottom: 2px;">Pabrik Sigaret Kretek Tangan (SKT)</p>
                        <p style="color: #64748b; font-size: 12px; margin-bottom: 2px;">Dsn. Subontoro RT. 001 RW. 015, Ds. Kebonduren, Kec. Ponggok, Kab. Blitar</p>
                        <p style="color: #059669; font-size: 12px; font-weight: 600;">NPPBKC: 750******-070700-221221******* (KPPBC TMP C Blitar)</p>
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

            <!-- Identitas Mitra & Pembayaran -->
            <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 24px;">
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 4px;">Tujuan Pengiriman Mitra:</span>
                    <strong style="font-size: 16px; color: #0f1316;">{{ $transaksi->nama_mitra }}</strong>
                    <div style="color: #475569; margin-top: 4px;"><i class="fa-solid fa-phone"></i> {{ $transaksi->telepon }}</div>
                    <div style="color: #475569; margin-top: 4px;"><i class="fa-solid fa-location-dot"></i> {{ $transaksi->alamat }}</div>
                </div>
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 4px;">Status &amp; Pembayaran:</span>
                    <div style="margin-bottom: 6px; display: flex; gap: 6px; flex-wrap: wrap;">
                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 12.5px;">
                            <i class="fa-solid fa-clock-rotate-left"></i> {{ $transaksi->status }}
                        </span>
                        <span style="background: {{ $transaksi->status_pembayaran === 'Lunas' ? '#dcfce7; color: #166534;' : ($transaksi->status_pembayaran === 'Menunggu Verifikasi' ? '#fef3c7; color: #92400e;' : '#f1f5f9; color: #334155;') }} padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 12.5px;">
                            {{ $transaksi->metode_pembayaran ?? 'Bayar di Tempat (COD)' }} &bull; {{ $transaksi->status_pembayaran ?? 'Belum Dibayar' }}
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

            @php
                $isTransferBank = $transaksi->metode_pembayaran && stripos($transaksi->metode_pembayaran, 'transfer') !== false;
            @endphp
            @if($isTransferBank && $transaksi->status !== 'Dibatalkan' && $transaksi->status_pembayaran !== 'Lunas')
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #111111; padding: 14px 18px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="font-size: 13px; color: #334155; line-height: 1.55;">
                        @if(!empty($bankInfo['rekening']))
                            <strong style="color: #0f172a; display: block; margin-bottom: 2px;">Instruksi Pembayaran Transfer {{ $bankInfo['nama'] ?? 'Bank Resmi' }}:</strong>
                            No. Rekening: <strong style="font-family: monospace; font-size: 14px; color: #0f172a;">{{ $bankInfo['rekening'] }}</strong> a.n. <strong>{{ $bankInfo['atas_nama'] ?? 'PR. KERETA KENCANA' }}</strong>
                        @else
                            <strong style="color: #0f172a; display: block; margin-bottom: 2px;">Instruksi Pembayaran Transfer Bank:</strong>
                            Nomor rekening tujuan transfer dikonfirmasi resmi melalui Admin WhatsApp (<span style="font-family: monospace; font-weight: 600;">{{ $pabrikWaDisplay ?? '+62 857-9062-7104' }}</span>).
                        @endif
                        <span style="display: block; font-size: 12px; color: #64748b;">
                            Setelah transfer, unggah foto struk/bukti transfer melalui halaman Pesanan Saya agar diproses bagian keuangan.
                        </span>
                    </div>
                    @if(Auth::check() && Auth::user()->isPelanggan())
                        <a href="{{ route('pesanan.saya') }}" class="btn btn-gold btn-sm no-print">
                            <i class="fa-solid fa-upload"></i> Unggah Bukti Transfer
                        </a>
                    @endif
                </div>
            @endif

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
                    @if($transaksi->details && $transaksi->details->isNotEmpty())
                        @foreach($transaksi->details as $d)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 14px 12px;">
                                    <strong>{{ $d->barang?->nama ?? 'Produk' }}</strong>
                                    <div style="font-size: 12px; color: #64748b;">
                                        {{ $d->barang?->kategori?->nama_kategori }} | Kode: {{ $d->barang?->kode_barang }}
                                    </div>
                                </td>
                                <td style="padding: 14px 12px; text-align: center;">
                                    @if($d->satuan === 'Slop')
                                        1 Slop ({{ $d->barang?->bungkus_per_slop ?? 10 }} Bungkus)
                                    @else
                                        1 Bal (Kemasan Bal)
                                    @endif
                                </td>
                                <td style="padding: 14px 12px; text-align: center; font-weight: 700;">
                                    {{ $d->jumlah }} {{ $d->satuan }}
                                </td>
                                <td style="padding: 14px 12px; text-align: right;">
                                    Rp {{ number_format($d->harga_satuan, 0, ',', '.') }} / {{ $d->satuan }}
                                </td>
                                <td style="padding: 14px 12px; text-align: right; font-weight: 700; color: #0f1316;">
                                    {{ $d->formatted_subtotal }}
                                </td>
                            </tr>
                        @endforeach
                    @elseif($transaksi->barang)
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
                                    1 Bal (Kemasan Bal)
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
                    @endif
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
            <div class="grid-2col" style="border-top: 1px dashed #cbd5e1; padding-top: 20px; display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">
                <div style="font-size: 12px; color: #64748b; line-height: 1.6;">
                    <strong>Ketentuan Distribusi:</strong>
                    <ol style="padding-left: 16px; margin-top: 4px;">
                        <li>Faktur ini merupakan dokumen resmi pemesanan produk tembakau PR. KERETA KENCANA.</li>
                        <li>Pastikan konfirmasi nomor pesanan ke WhatsApp manajemen pabrik untuk alokasi nomor armada pengiriman.</li>
                        <li>Barang yang dikirim telah dilengkapi pita cukai Republik Indonesia resmi dan utuh.</li>
                    </ol>
                </div>
                <div style="text-align: center; font-size: 12px; color: #475569;">
                    <div>Ponggok, Blitar, {{ ($transaksi->created_at ?? now())->translatedFormat('d M Y') }}</div>
                    <div style="margin-top: 60px; font-weight: 700; text-decoration: underline;">PR. KERETA KENCANA</div>
                    <div>Bagian Logistik &amp; Distribusi</div>
                </div>
            </div>

            <!-- Tombol Konfirmasi WhatsApp (No Print) -->
            <div style="margin-top: 30px; text-align: center;" class="no-print">
                <a href="https://wa.me/{{ $pabrikWa ?? config('pabrik.wa_pabrik', '6285790627104') }}?text={{ $pesanWa }}" target="_blank" rel="noopener noreferrer"
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
    .no-print, .site-header, .site-footer, .health-warning-bar, .age-gate-overlay {
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
