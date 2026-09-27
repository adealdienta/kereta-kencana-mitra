<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PemesananController extends Controller
{
    /**
     * Tampilkan formulir pemesanan rokok Multi-Produk & Multi-Satuan (Khusus Mitra Login)
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
        $preselectedSlug = $request->query('produk');

        return view('front.pesan', compact('produks', 'user', 'isLocked', 'lockedOrder', 'preselectedSlug'));
    }

    /**
     * Simpan pemesanan baru (Mendukung Multi-Produk dan Satuan Independen Slop / Bal per produk)
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Sesi login telah berakhir. Silakan login kembali.');
        }

        $user = Auth::user();

        // Cek kuncian pesanan di sisi server
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
            'nama_mitra' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
        ], [
            'nama_mitra.required' => 'Nama mitra/toko wajib diisi.',
            'telepon.required' => 'Nomor kontak WhatsApp aktif wajib diisi.',
            'alamat.required' => 'Alamat pengiriman tujuan wajib diisi lengkap.',
        ]);

        // Kumpulkan item pesanan (Mendukung Multi-Item via 'items' atau Fallback Single Item)
        $rawItems = [];

        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $barangId => $data) {
                $qty = isset($data['jumlah']) ? (int)$data['jumlah'] : 0;
                $satuan = isset($data['satuan']) && in_array($data['satuan'], ['Slop', 'Bal']) ? $data['satuan'] : 'Slop';

                if ($qty > 0) {
                    $rawItems[] = [
                        'barang_id' => (int)$barangId,
                        'satuan' => $satuan,
                        'jumlah' => $qty,
                    ];
                }
            }
        } elseif ($request->filled('barang_id') && $request->filled('jumlah')) {
            // Fallback kompatibilitas jika dikirim sebagai single item (misal pengujian API/unit test lama)
            $rawItems[] = [
                'barang_id' => (int)$request->barang_id,
                'satuan' => $request->input('satuan', 'Bal'),
                'jumlah' => (int)$request->jumlah,
            ];
        }

        if (empty($rawItems)) {
            return back()->withInput()->withErrors([
                'items' => 'Silakan tentukan minimal 1 varian rokok dengan kuantitas pesanan di atas 0 (Slop atau Bal).',
            ]);
        }

        // Jalankan Database Transaction untuk menjaga integritas data & pemotongan stok
        try {
            $transaksi = DB::transaction(function () use ($request, $user, $rawItems) {
                $kode_transaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $grandTotal = 0;
                $totalUnitQty = 0;
                $processedDetails = [];

                foreach ($rawItems as $itemData) {
                    $barang = Barang::findOrFail($itemData['barang_id']);
                    $satuan = $itemData['satuan'];
                    $qty = $itemData['jumlah'];

                    // Cek minimum order
                    $minOrder = ($satuan === 'Slop') ? ($barang->min_order_slop ?: 1) : ($barang->min_order_bal ?: 1);
                    if ($qty < $minOrder) {
                        throw new Exception("Minimum pemesanan untuk {$barang->nama} satuan {$satuan} adalah {$minOrder} {$satuan}.");
                    }

                    // Cek & Kurangi Stok Gudang (Stok dihitung dalam satuan Bal)
                    $slopPerBal = $barang->slop_per_bal ?: 20;
                    $balDeduct = ($satuan === 'Bal') ? $qty : max(1, (int)ceil($qty / $slopPerBal));

                    if ($barang->stok < $balDeduct) {
                        throw new Exception("Stok untuk varian {$barang->nama} tidak mencukupi (Tersedia: {$barang->stok} Bal, dibutuhkan: {$balDeduct} Bal).");
                    }

                    $hargaSatuan = ($satuan === 'Slop') ? $barang->harga_per_slop : $barang->harga_per_bal;
                    $subtotal = $qty * $hargaSatuan;

                    $grandTotal += $subtotal;
                    $totalUnitQty += $qty;

                    // Kurangi stok produk
                    $barang->decrement('stok', $balDeduct);

                    $processedDetails[] = [
                        'barang_id' => $barang->id,
                        'satuan' => $satuan,
                        'jumlah' => $qty,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Buat Header Transaksi
                $firstDetail = $processedDetails[0];
                $trx = Transaksi::create([
                    'kode_transaksi' => $kode_transaksi,
                    'user_id' => $user->id,
                    'barang_id' => $firstDetail['barang_id'],
                    'nama_mitra' => $request->nama_mitra,
                    'telepon' => $request->telepon,
                    'alamat' => $request->alamat,
                    'jumlah' => $totalUnitQty,
                    'satuan' => count($processedDetails) === 1 ? $firstDetail['satuan'] : 'Campuran',
                    'total_harga' => $grandTotal,
                    'catatan' => $request->catatan,
                    'status' => 'Baru Masuk',
                    'sumber' => 'Web B2B Form',
                ]);

                // Simpan Setiap Item ke tabel transaksi_details
                foreach ($processedDetails as &$detail) {
                    $detail['transaksi_id'] = $trx->id;
                }
                TransaksiDetail::insert($processedDetails);

                return $trx;
            });

            ActivityLogger::log('Pesanan Masuk', "Pesanan multi-item {$transaksi->kode_transaksi} oleh {$request->nama_mitra}");

            return redirect()->route('pesanan.invoice', $transaksi->kode_transaksi)
                ->with('success', 'Pesanan Anda berhasil dibuat! Faktur pemesanan multi-produk telah terbit.');

        } catch (Exception $e) {
            return back()->withInput()->withErrors([
                'items' => $e->getMessage(),
            ]);
        }
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
        $transaksis = Transaksi::with(['details.barang.kategori', 'barang.kategori'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // Cek apakah ada pesanan yang butuh upload foto bukti
        $pendingDelivery = Transaksi::with('details.barang')
            ->where('user_id', $user->id)
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

        $transaksi = Transaksi::with('details.barang', 'barang')->where('user_id', Auth::id())->findOrFail($id);

        if ($transaksi->status !== 'Baru Masuk') {
            return back()->with('error', 'Pesanan yang sudah diproses atau dikirim oleh pabrik tidak dapat dibatalkan mandiri. Silakan hubungi admin via WhatsApp.');
        }

        DB::transaction(function () use ($transaksi) {
            // Kembalikan stok untuk seluruh item produk yang ada di transaksi ini
            if ($transaksi->details && $transaksi->details->isNotEmpty()) {
                foreach ($transaksi->details as $d) {
                    if ($d->barang) {
                        $slopPerBal = $d->barang->slop_per_bal ?: 20;
                        $balDeduct = ($d->satuan === 'Bal') ? $d->jumlah : max(1, (int)ceil($d->jumlah / $slopPerBal));
                        $d->barang->increment('stok', $balDeduct);
                    }
                }
            } elseif ($transaksi->barang) {
                $slopPerBal = $transaksi->barang->slop_per_bal ?: 20;
                $balDeduct = ($transaksi->satuan === 'Bal') ? $transaksi->jumlah : max(1, (int)ceil($transaksi->jumlah / $slopPerBal));
                $transaksi->barang->increment('stok', $balDeduct);
            }

            $transaksi->update([
                'status' => 'Dibatalkan',
                'catatan' => ($transaksi->catatan ? $transaksi->catatan . "\n" : '') . "[Dibatalkan oleh pembeli pada " . now()->format('d/m/Y H:i') . "]",
            ]);
        });

        ActivityLogger::log('Batal Pesanan', "Pesanan {$transaksi->kode_transaksi} dibatalkan oleh pembeli");

        return back()->with('success', "Pesanan {$transaksi->kode_transaksi} berhasil dibatalkan dan seluruh kuota stok telah dipulihkan.");
    }

    /**
     * Tampilan Faktur / Invoice Digital Multi-Item
     */
    public function invoice($kode_transaksi)
    {
        $transaksi = Transaksi::with(['details.barang.kategori', 'barang.kategori'])
            ->where('kode_transaksi', $kode_transaksi)
            ->firstOrFail();

        // Susun teks rincian barang untuk WhatsApp
        $daftarProdukText = "";
        if ($transaksi->details && $transaksi->details->isNotEmpty()) {
            foreach ($transaksi->details as $idx => $d) {
                $num = $idx + 1;
                $nama = $d->barang?->nama ?? 'Produk';
                $daftarProdukText .= "  {$num}. {$nama} ({$d->jumlah} {$d->satuan}) - {$d->formatted_subtotal}%0A";
            }
        } else {
            $daftarProdukText = "  • {$transaksi->barang->nama} ({$transaksi->jumlah} {$transaksi->satuan}) - {$transaksi->formatted_total}%0A";
        }

        // Format pesan WhatsApp resmi
        $pesanWa = "Halo Tim Manajemen PR. KERETA KENCANA,%0A%0A"
            . "Saya telah membuat pemesanan resmi melalui website:%0A"
            . "• No. Pemesanan: *" . $transaksi->kode_transaksi . "*%0A"
            . "• Nama Mitra/Toko: *" . $transaksi->nama_mitra . "*%0A"
            . "• Telepon: " . $transaksi->telepon . "%0A"
            . "• Rincian Item Rokok:%0A" . $daftarProdukText
            . "• Total Tagihan: *" . $transaksi->formatted_total . "*%0A"
            . "• Alamat Tujuan: " . urlencode($transaksi->alamat) . "%0A"
            . ($transaksi->catatan ? ("• Catatan: " . urlencode($transaksi->catatan) . "%0A") : "")
            . "%0AMohon konfirmasi kesiapan pengiriman dan jadwal armada pabrik. Terima kasih.";

        return view('front.invoice', compact('transaksi', 'pesanWa'));
    }

    /**
     * API Log WhatsApp Modal
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
        $unitPrice = $satuan === 'Slop' ? ($barang ? $barang->harga_per_slop : 49000) : ($barang ? $barang->harga_per_bal : 980000);
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

        TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'barang_id' => $barang->id,
            'satuan' => $satuan,
            'jumlah' => $request->jumlah,
            'harga_satuan' => $unitPrice,
            'subtotal' => $total_harga,
        ]);

        ActivityLogger::log('Log Pesanan WhatsApp', "Pemesanan WA dicatat {$kode_transaksi} dari {$request->nama_mitra}");

        return response()->json([
            'status' => 'success',
            'kode_transaksi' => $kode_transaksi,
            'id' => $transaksi->id
        ]);
    }
}
