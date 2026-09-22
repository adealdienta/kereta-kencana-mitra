<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur DO #{{ $transaksi->kode_transaksi }} - PR. KERETA KENCANA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #111; margin: 30px; line-height: 1.5; }
        .header { border-bottom: 2px solid #222; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
        .meta { text-align: right; }
        .grid { display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 8px 10px; }
        th { background: #f0f0f0; text-align: left; }
        .total-row { font-weight: bold; font-size: 14px; background: #fafafa; }
        .footer-sign { display: flex; justify-content: space-between; margin-top: 40px; }
        .sign-box { text-align: center; width: 200px; }
        .sign-line { margin-top: 60px; border-top: 1px solid #111; font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; background: #fdf6b2; padding: 10px 15px; border: 1px solid #fde047;">
        Tombol otomatis cetak telah diaktifkan. Klik tombol cetak browser Anda atau tutup tab ini jika selesai.
    </div>

    <div class="header">
        <div>
            <div class="title">PR. KERETA KENCANA</div>
            <div>Pabrik Sigaret Kretek Mesin & Sigaret Kretek Tangan</div>
            <div>Dusun Subontoro, Desa Kebonduren, Kec. Ponggok, Blitar - Jawa Timur</div>
            <div><strong>NPPBKC: 0821.1.2.XXXXX (Bea Cukai Blitar)</strong></div>
        </div>
        <div class="meta">
            <div style="font-size: 16px; font-weight: bold; border: 1px solid #222; padding: 4px 8px; display: inline-block;">
                FAKTUR & DELIVERY ORDER
            </div>
            <div style="font-family: monospace; font-size: 14px; margin-top: 6px;">#{{ $transaksi->kode_transaksi }}</div>
            <div>Tanggal: {{ $transaksi->created_at->format('d/m/Y H:i') }} WIB</div>
        </div>
    </div>

    <div class="grid">
        <div style="width: 48%;">
            <strong>Tujuan Pengiriman Mitra Distributor:</strong>
            <div style="font-size: 15px; font-weight: bold; margin-top: 4px;">{{ $transaksi->nama_mitra }}</div>
            <div>Telepon: {{ $transaksi->telepon }}</div>
            <div>Alamat: {{ $transaksi->alamat }}</div>
        </div>
        <div style="width: 48%;">
            <strong>Informasi Dokumen Pengiriman Armada:</strong>
            <div style="margin-top: 4px;">Nomor DO: <strong>{{ $transaksi->nomor_do ?? '-' }}</strong></div>
            <div>Nomor Resi / Kargo: <strong>{{ $transaksi->nomor_resi ?? '-' }}</strong></div>
            <div>Status: <strong>{{ $transaksi->status }}</strong></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode & Varian Rokok</th>
                <th style="text-align: center;">Kemasan</th>
                <th style="text-align: center;">Volume Pesanan</th>
                <th style="text-align: right;">Harga Satuan</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>
                    <strong>{{ $transaksi->barang->nama }}</strong>
                    <div>{{ $transaksi->barang->kategori->nama_kategori }} (Kode: {{ $transaksi->barang->kode_barang }})</div>
                </td>
                <td style="text-align: center;">
                    @if($transaksi->satuan === 'Slop')
                        1 Slop ({{ $transaksi->barang->bungkus_per_slop }} Bungkus)
                    @else
                        1 Bal ({{ $transaksi->barang->slop_per_bal }} Slop / {{ $transaksi->barang->slop_per_bal * $transaksi->barang->bungkus_per_slop }} Bks)
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $transaksi->jumlah }} {{ $transaksi->satuan }}</td>
                <td style="text-align: right;">Rp {{ number_format($transaksi->total_harga / max(1, $transaksi->jumlah), 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $transaksi->formatted_total }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">TOTAL NILAI TRANSAKSI:</td>
                <td style="text-align: right;">{{ $transaksi->formatted_total }}</td>
            </tr>
        </tfoot>
    </table>

    @if($transaksi->catatan)
        <div style="margin-bottom: 20px; padding: 8px; border: 1px dashed #666;">
            <strong>Catatan Logistik:</strong> {{ $transaksi->catatan }}
        </div>
    @endif

    <div class="footer-sign">
        <div class="sign-box">
            <div>Diterima Oleh,</div>
            <div class="sign-line">{{ $transaksi->nama_mitra }}</div>
            <div>Mitra Distributor / Penerima</div>
        </div>
        <div class="sign-box">
            <div>Pengemudi / Ekspedisi,</div>
            <div class="sign-line">Armada Pengirim</div>
            <div>Truk Logistik Pabrik</div>
        </div>
        <div class="sign-box">
            <div>Ponggok, {{ date('d F Y') }}</div>
            <div class="sign-line">PR. KERETA KENCANA</div>
            <div>Bagian Administrasi & Cukai</div>
        </div>
    </div>
</body>
</html>
