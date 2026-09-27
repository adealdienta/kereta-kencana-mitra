<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PemesananController extends Controller
{
    /**
     * Tampilkan formulir pemesanan rokok (Khusus Pengguna Login / Mitra)
     */
    public function form(Request $request)
    {
        // 1. Wajib Login untuk memesan
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Silakan masuk atau daftar akun mitra terlebih dahulu untuk mengisi formulir pemesanan.');
        }

        $user = Auth::user();

        // 2. Cek Aturan Kunci Pemesanan (Order Lock)
        // Kunci HANYA AKTIF jika ada pesanan dengan status 'Dikirim' yang belum diunggah bukti penerimaannya
        $lockedOrder = Transaksi::where('user_id', $user->id)
            ->where('status', 'Dikirim')
            ->whereNull('bukti_penerimaan')
            ->latest()
            ->first();

        $isLocked = !is_null($lockedOrder);

        $produks = Barang::aktif()->orderBy('urutan')->get();
        $selectedProduk = null;
        $selectedSatuan = in_array($request->satuan, ['Slop', 'Bal']) ? $request->satuan : 'Slop';

        if ($request->filled('produk')) {
            $selectedProduk = Barang::where('slug', $request->produk)->orWhere('id', $request->produk)->first();
        }

        return view('front.pesan', compact('produks', 'selectedProduk', 'selectedSatuan', 'user', 'isLocked', 'lockedOrder'));
    }

    /**
     * Simpan pemesanan baru dari formulir web
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Sesi login telah berakhir. Silakan login kembali.');
        }

        $user = Auth::user();

        // Cek kembali kuncian pesanan di sisi server
        $lockedOrder = Transaksi::where('user_id', $user->id)
            ->where('status', 'Dikirim')
            ->whereNull('bukti_penerimaan')
            ->latest()
            ->first();

        if ($lockedOrder) {
            return back()->withInput()->withErrors([
                'order_lock' => "Pemesanan terkunci! Anda masih memiliki pesanan aktif ({$lockedOrder->kode_transaksi}) yang sedang dalam pengiriman. Mohon unggah bukti penerimaan barang terlebih dahulu sebelum membuat pesanan baru.",
            ]);
        }

        $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'nama_mitra' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'satuan' => ['nullable', 'in:Slop,Bal'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string'],
        ], [
            'barang_id.required' => 'Silakan pilih salah satu varian produk rokok.',
            'nama_mitra.required' => 'Nama mitra/toko wajib diisi.',
            'telepon.required' => 'Nomor kontak WhatsApp aktif wajib diisi.',
            'alamat.required' => 'Alamat pengiriman tujuan wajib diisi lengkap.',
            'jumlah.min' => 'Jumlah pesanan minimal 1 unit.',
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

        // 1. Simpan Transaksi dengan relasi user_id
        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode_transaksi,
            'user_id' => $user->id,
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
            ->with('success', 'Pesanan Anda berhasil dibuat! Faktur pemesanan telah terbit dan pesanan Anda kini masuk dalam antrean gudang.');
    }

    /**
     * Menu Pelacakan Pesanan Pelanggan (Pesanan Saya)
     */
    public function pelacakan(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Silakan masuk untuk melihat daftar pelacakan pesanan Anda.');
        }

        $user = Auth::user();
        $transaksis = Transaksi::with(['barang.kategori'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // Cek apakah ada pesanan yang butuh upload foto bukti
        $pendingDelivery = Transaksi::where('user_id', $user->id)
            ->where('status', 'Dikirim')
            ->whereNull('bukti_penerimaan')
            ->first();

        return view('front.pesanan_saya', compact('transaksis', 'user', 'pendingDelivery'));
    }

    /**
     * Konfirmasi Penerimaan Barang & Unggah Foto Bukti
     */
    public function konfirmasiTerima(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $transaksi = Transaksi::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($transaksi->status, ['Dikirim', 'Diproses'])) {
            return back()->with('error', 'Pesanan ini tidak berada dalam tahap pengiriman.');
        }

        $request->validate([
            'foto_bukti' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'catatan_penerima' => ['nullable', 'string', 'max:500'],
        ], [
            'foto_bukti.required' => 'Foto bukti penerimaan barang wajib diunggah.',
            'foto_bukti.image' => 'Berkas harus berupa gambar foto.',
            'foto_bukti.mimes' => 'Format gambar yang didukung: JPG, PNG, atau WEBP.',
            'foto_bukti.max' => 'Ukuran foto maksimal adalah 5 Megabyte (MB).',
        ]);

        $path = $request->file('foto_bukti')->store('bukti_penerimaan', 'public');

        $transaksi->update([
            'status' => 'Selesai',
            'bukti_penerimaan' => $path,
            'diterima_pada' => now(),
            'catatan_penerima' => $request->catatan_penerima,
        ]);

        ActivityLogger::log('Bukti Terima Diunggah', "Mitra '{$transaksi->nama_mitra}' telah mengunggah bukti terima untuk transaksi {$transaksi->kode_transaksi}");

        return redirect()->route('pesanan.saya')
            ->with('success', "Terima kasih! Bukti penerimaan untuk pesanan {$transaksi->kode_transaksi} telah berhasil dikonfirmasi. Status pesanan Anda kini SELESAI dan hak pemesanan baru telah aktif.");
    }

    /**
     * Pembatalan Mandiri oleh Pelanggan (Hanya jika status 'Baru Masuk')
     */
    public function batalkanPesanan(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $transaksi = Transaksi::where('user_id', Auth::id())->findOrFail($id);

        if ($transaksi->status !== 'Baru Masuk') {
            return back()->with('error', 'Pesanan yang sudah diproses atau dikirim oleh pabrik tidak dapat dibatalkan mandiri. Silakan hubungi admin via WhatsApp.');
        }

        // Kembalikan stok barang yang sudah didecrement
        $slopPerBal = $transaksi->barang->slop_per_bal ?: 20;
        $balDeduct = $transaksi->satuan === 'Bal' ? $transaksi->jumlah : max(1, (int)ceil($transaksi->jumlah / $slopPerBal));
        $transaksi->barang->increment('stok', $balDeduct);

        $transaksi->update([
            'status' => 'Dibatalkan',
            'catatan' => ($transaksi->catatan ? $transaksi->catatan . "\n" : '') . "[Dibatalkan oleh pembeli pada " . now()->format('d/m/Y H:i') . "]",
        ]);

        ActivityLogger::log('Batal Pesanan', "Pesanan {$transaksi->kode_transaksi} dibatalkan oleh pembeli");

        return back()->with('success', "Pesanan {$transaksi->kode_transaksi} berhasil dibatalkan dan kuota stok telah dipulihkan.");
    }

    /**
     * Tampilan Faktur / Invoice Digital
     */
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
            . "%0AMohon informasi jadwal pengiriman armada pabrik. Terima kasih.";

        return view('front.invoice', compact('transaksi', 'pesanWa'));
    }

    /**
     * API Log WhatsApp Modal (BKPM Acara 21)
     */
    public function apiLogWa(Request $request)
    {
        $request->validate([
            'nama_mitra' => ['required', 'string'],
            'telepon' => ['required', 'string'],
            'varian_produk' => ['required', 'string'],
            'jumlah' => ['required', 'integer'],
        ]);

        $barang = Barang::where('nama', $request->varian_produk)
            ->orWhere('slug', $request->varian_produk)
            ->first() ?? Barang::first();

        $satuan = $request->input('satuan', 'Slop');
        $unitPrice = $satuan === 'Slop' ? ($barang ? $barang->harga_per_slop : 62500) : ($barang ? $barang->harga_per_bal : 1250000);
        $total_harga = (float)($request->total_harga ?? ($request->jumlah * $unitPrice));
        $kode_transaksi = 'WA-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode_transaksi,
            'user_id' => Auth::id(),
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
