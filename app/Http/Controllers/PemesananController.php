<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Str;

class PemesananController extends Controller
{
    public function form(Request $request)
    {
        $produks = Barang::aktif()->orderBy('urutan')->get();
        $selectedProduk = null;
        $selectedSatuan = in_array($request->satuan, ['Slop', 'Bal']) ? $request->satuan : 'Slop';

        if ($request->filled('produk')) {
            $selectedProduk = Barang::where('slug', $request->produk)->orWhere('id', $request->produk)->first();
        }

        return view('front.pesan', compact('produks', 'selectedProduk', 'selectedSatuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'nama_mitra' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'satuan' => ['nullable', 'in:Slop,Bal'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string'],
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $satuan = $request->input('satuan', 'Bal');

        $minOrder = $satuan === 'Slop' ? ($barang->min_order_slop ?: 1) : ($barang->min_order_bal ?: 1);

        if ($request->jumlah < $minOrder) {
            return back()->withInput()->withErrors([
                'jumlah' => "Minimum pemesanan varian {$barang->nama} untuk satuan {$satuan} adalah {$minOrder} {$satuan}.",
            ]);
        }

        $hargaSatuan = $satuan === 'Slop' ? $barang->harga_per_slop : $barang->harga_per_bal;
        $total_harga = $request->jumlah * $hargaSatuan;
        $kode_transaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        // 1. Simpan Transaksi
        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode_transaksi,
            'barang_id' => $barang->id,
            'nama_mitra' => $request->nama_mitra,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'jumlah' => $request->jumlah,
            'satuan' => $satuan,
            'total_harga' => $total_harga,
            'catatan' => $request->catatan,
            'status' => 'Baru Masuk',
            'sumber' => 'Web B2B Form',
        ]);

        // 2. Pengurangan Stok (Sesuai BKPM Acara 18)
        $slopPerBal = $barang->slop_per_bal ?: 20;
        $balDeduct = $satuan === 'Bal' ? $request->jumlah : max(1, (int)ceil($request->jumlah / $slopPerBal));

        if ($barang->stok >= $balDeduct) {
            $barang->decrement('stok', $balDeduct);
        }

        ActivityLogger::log('Pesanan Masuk', "Pesanan baru {$kode_transaksi} oleh {$request->nama_mitra} ({$request->jumlah} {$satuan})");

        return redirect()->route('pesanan.invoice', $kode_transaksi)
            ->with('success', 'Pesanan Anda berhasil diterbitkan! Silakan simpan invoice dan konfirmasi ke WhatsApp manajemen pabrik.');
    }

    public function invoice($kode_transaksi)
    {
        $transaksi = Transaksi::with('barang.kategori')
            ->where('kode_transaksi', $kode_transaksi)
            ->firstOrFail();

        $volumeText = $transaksi->satuan === 'Slop' 
            ? "{$transaksi->jumlah} Slop (" . ($transaksi->jumlah * 10) . " Bungkus)"
            : "{$transaksi->jumlah} Bal (" . ($transaksi->jumlah * ($transaksi->barang->slop_per_bal ?: 20)) . " Slop / " . ($transaksi->jumlah * ($transaksi->barang->slop_per_bal ?: 20) * 10) . " Bungkus)";

        // Format pesan WhatsApp otomatis
        $pesanWa = "Halo Tim Manajemen PR. KERETA KENCANA,%0A%0A"
            . "Saya telah membuat pemesanan resmi melalui website:%0A"
            . "• No. Pemesanan: *" . $transaksi->kode_transaksi . "*%0A"
            . "• Nama Mitra/Toko: *" . $transaksi->nama_mitra . "*%0A"
            . "• Telepon: " . $transaksi->telepon . "%0A"
            . "• Produk: *" . $transaksi->barang->nama . "*%0A"
            . "• Volume: *" . $volumeText . "*%0A"
            . "• Satuan: *" . $transaksi->satuan . "*%0A"
            . "• Estimasi Nilai: *" . $transaksi->formatted_total . "*%0A"
            . "• Alamat Tujuan: " . urlencode($transaksi->alamat) . "%0A"
            . ($transaksi->catatan ? ("• Catatan: " . urlencode($transaksi->catatan) . "%0A") : "")
            . "%0AMohon konfirmasi kesiapan pengiriman dan instruksi pembayarannya. Terima kasih.";

        return view('front.invoice', compact('transaksi', 'pesanWa'));
    }

    public function apiLogWa(Request $request)
    {
        $request->validate([
            'nama_mitra' => ['required', 'string'],
            'telepon' => ['required', 'string'],
            'varian_produk' => ['required', 'string'],
            'jumlah' => ['required', 'integer'],
        ]);

        // Cari barang berdasarkan varian produk
        $barang = Barang::where('nama', $request->varian_produk)
            ->orWhere('slug', $request->varian_produk)
            ->first() ?? Barang::first();

        $satuan = $request->input('satuan', 'Slop');
        $unitPrice = $satuan === 'Slop' ? ($barang ? $barang->harga_per_slop : 62500) : ($barang ? $barang->harga_per_bal : 1250000);
        $total_harga = (float)($request->total_harga ?? ($request->jumlah * $unitPrice));
        $kode_transaksi = 'WA-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode_transaksi,
            'barang_id' => $barang->id,
            'nama_mitra' => $request->nama_mitra,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat ?? 'Via WhatsApp',
            'jumlah' => $request->jumlah,
            'satuan' => $satuan,
            'total_harga' => $total_harga,
            'catatan' => $request->catatan,
            'status' => 'Baru Masuk',
            'sumber' => 'Web WhatsApp',
        ]);

        ActivityLogger::log('Log Pesanan WhatsApp', "Pemesanan WA dicatat {$kode_transaksi} dari {$request->nama_mitra} ({$request->jumlah} {$satuan})");

        return response()->json([
            'status' => 'success',
            'kode_transaksi' => $kode_transaksi,
            'id' => $transaksi->id
        ]);
    }
}
