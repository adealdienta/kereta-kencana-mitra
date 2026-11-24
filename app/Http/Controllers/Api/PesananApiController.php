<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PesananApiRequest;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Barang;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class PesananApiController extends ApiController
{
    /**
     * Cek Status Kunci Pemesanan (Order Lock)
     * GET /api/v1/pesanan/status-kunci
     */
    public function statusKunci(Request $request)
    {
        $user = $request->user();

        $lockedOrder = Transaksi::where('user_id', $user->id)
            ->where('status', 'Dikirim')
            ->whereNull('bukti_penerimaan')
            ->latest()
            ->first();

        $isLocked = !is_null($lockedOrder);

        return $this->successResponse([
            'terkunci' => $isLocked,
            'pesanan' => $isLocked ? [
                'id' => $lockedOrder->id,
                'kode_transaksi' => $lockedOrder->kode_transaksi,
                'status' => $lockedOrder->status,
                'total_harga' => (float)$lockedOrder->total_harga,
                'created_at' => $lockedOrder->created_at?->toIso8601String(),
            ] : null,
        ], $isLocked
            ? "Pemesanan terkunci. Anda masih memiliki pesanan aktif ({$lockedOrder->kode_transaksi}) yang belum dikonfirmasi penerimaannya."
            : "Akun siap untuk membuat pesanan baru.");
    }

    /**
     * Riwayat Pemesanan Mitra (dengan filter & paginasi)
     * GET /api/v1/pesanan?status=berjalan|selesai|semua&page=
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $statusFilter = strtolower($request->query('status', 'semua'));

        $query = Transaksi::with(['details.barang.kategori', 'barang.kategori'])
            ->where('user_id', $user->id);

        if ($statusFilter === 'berjalan') {
            $query->whereIn('status', ['Baru Masuk', 'Diproses', 'Dikirim']);
        } elseif ($statusFilter === 'selesai') {
            $query->where('status', 'Selesai');
        } elseif ($statusFilter === 'dibatalkan') {
            $query->where('status', 'Dibatalkan');
        }

        $transaksis = $query->latest()->paginate(10);

        $items = collect($transaksis->items())->map(function ($t) {
            return $this->formatTransaksi($t);
        });

        return $this->successResponse([
            'items' => $items,
            'pagination' => [
                'current_page' => $transaksis->currentPage(),
                'last_page' => $transaksis->lastPage(),
                'per_page' => $transaksis->perPage(),
                'total' => $transaksis->total(),
            ],
        ], 'Daftar riwayat pesanan berhasil dimuat.');
    }

    /**
     * Rincian Lengkap Pesanan & Faktur
     * GET /api/v1/pesanan/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $transaksi = Transaksi::with(['details.barang.kategori', 'barang.kategori'])
            ->where('user_id', $user->id)
            ->find($id);

        if (!$transaksi) {
            return $this->errorResponse('Pesanan tidak ditemukan atau bukan milik akun Anda.', 404);
        }

        return $this->successResponse($this->formatTransaksi($transaksi, true), 'Detail pesanan berhasil dimuat.');
    }

    /**
     * Pembuatan Pesanan Grosir Baru (Multi-Item & Multi-Satuan)
     * POST /api/v1/pesanan
     */
    public function store(PesananApiRequest $request)
    {
        $user = $request->user();
        $idempotencyKey = trim((string) $request->header('Idempotency-Key', ''));
        $idempotencyKey = $idempotencyKey !== '' ? substr($idempotencyKey, 0, 100) : null;
        $lock = $idempotencyKey ? Cache::lock("idempotency_lock:order:{$user->id}:{$idempotencyKey}", 15) : null;

        try {
            if ($lock) {
                $lock->block(5);
            }

            // 1. Cek Idempotency Key untuk mencegah pesanan ganda
            if ($idempotencyKey) {
                $cacheKey = "idempotency:order:{$user->id}:{$idempotencyKey}";
                if (Cache::has($cacheKey)) {
                    $cachedOrderId = Cache::get($cacheKey);
                    $existingOrder = Transaksi::with(['details.barang.kategori'])->find($cachedOrderId);
                    if ($existingOrder) {
                        return $this->successResponse($this->formatTransaksi($existingOrder, true), 'Pesanan telah diproses sebelumnya (Idempotent).', 200);
                    }
                }
            }

            // 2. Cek Order Lock di Sisi Server
            $lockedOrder = Transaksi::where('user_id', $user->id)
                ->where('status', 'Dikirim')
                ->whereNull('bukti_penerimaan')
                ->latest()
                ->first();

            if ($lockedOrder) {
                return $this->errorResponse("Pemesanan terkunci! Anda masih memiliki pesanan ({$lockedOrder->kode_transaksi}) yang sedang dalam pengiriman. Unggah bukti penerimaan barang terlebih dahulu sebelum membuat pesanan baru.", 409, [
                    'order_lock' => true,
                    'locked_order_id' => $lockedOrder->id,
                    'kode_transaksi' => $lockedOrder->kode_transaksi,
                ]);
            }

            $itemsPayload = $request->input('items', []);

            // Batasi pemesanan online maksimal 19 Slop gabungan seluruh varian (skala Bal via Admin WA)
            $totalSlopRequested = collect($itemsPayload)->sum(fn ($item) => (int) ($item['jumlah'] ?? 0));
            if ($totalSlopRequested < 1 || $totalSlopRequested > 19) {
                return $this->errorResponse(
                    "Total pemesanan online maksimal 19 Slop gabungan seluruh varian (pilihan Anda saat ini: {$totalSlopRequested} Slop). Untuk pembelian skala Bal (20 Slop ke atas), silakan hubungi Admin via WhatsApp.",
                    422
                );
            }

            // 3. Jalankan Database Transaction dengan Pesimistic Lock & Agregasi Stok (Aturan B2)
            $transaksi = DB::transaction(function () use ($request, $user, $itemsPayload, $idempotencyKey) {
                // Re-cek order lock secara atomik di dalam transaksi
                $txLockedOrder = Transaksi::where('user_id', $user->id)
                    ->where('status', 'Dikirim')
                    ->whereNull('bukti_penerimaan')
                    ->lockForUpdate()
                    ->first();
                if ($txLockedOrder) {
                    throw new Exception("Pemesanan terkunci! Anda masih memiliki pesanan ({$txLockedOrder->kode_transaksi}) yang sedang dalam pengiriman.");
                }

                // Kumpulkan & urutkan ID barang untuk mencegah deadlock
                $barangIds = array_unique(array_map('intval', array_column($itemsPayload, 'barang_id')));
                sort($barangIds);

                // Kunci baris barang untuk update (mencegah race condition overselling)
                $lockedBarangs = Barang::whereIn('id', $barangIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Agregasi kuantitas pemotongan per barang untuk mencegah pemotongan berlebih
                $demandPerBarang = [];
                foreach ($itemsPayload as $item) {
                    $bId = (int)$item['barang_id'];
                    $satuan = $item['satuan'];
                    $qty = (int)$item['jumlah'];

                    if (!isset($lockedBarangs[$bId])) {
                        throw new Exception("Varian produk dengan ID {$bId} tidak ditemukan.");
                    }

                    $b = $lockedBarangs[$bId];

                    if (!$b->aktif) {
                        throw new Exception("Produk {$b->nama} saat ini sedang dinonaktifkan oleh pabrik.");
                    }

                    // Validasi batas minimum pemesanan
                    $minOrder = ($satuan === 'Slop') ? ($b->min_order_slop ?: 1) : ($b->min_order_bal ?: 1);
                    if ($qty < $minOrder) {
                        throw new Exception("Minimum pemesanan untuk {$b->nama} satuan {$satuan} adalah {$minOrder} {$satuan}.");
                    }

                    if (!isset($demandPerBarang[$bId])) {
                        $demandPerBarang[$bId] = [
                            'bal_qty' => 0,
                            'slop_qty' => 0,
                        ];
                    }

                    if ($satuan === 'Bal') {
                        $demandPerBarang[$bId]['bal_qty'] += $qty;
                    } else {
                        $demandPerBarang[$bId]['slop_qty'] += $qty;
                    }
                }

                // Cek dan potong stok gudang secara presisi dalam satuan Slop
                foreach ($demandPerBarang as $bId => $demands) {
                    $b = $lockedBarangs[$bId];
                    $slopPerBal = $b->slop_per_bal ?: 20;

                    if (strcasecmp((string) $b->status_stok, 'Habis') === 0 || $b->stok <= 0) {
                        throw new Exception("Stok {$b->nama} saat ini sedang habis.");
                    }

                    $totalSlopNeeded = ($demands['bal_qty'] * $slopPerBal) + $demands['slop_qty'];

                    if ($b->stok < $totalSlopNeeded) {
                        throw new Exception("Stok untuk varian {$b->nama} tidak mencukupi (Tersedia: {$b->stok} Slop, dibutuhkan: {$totalSlopNeeded} Slop).");
                    }

                    $b->decrement('stok', $totalSlopNeeded);
                }

                // Hitung total Slop pesanan untuk penentuan harga bertingkat
                $totalSlopInOrder = collect($itemsPayload)
                    ->where('satuan', 'Slop')
                    ->sum(fn ($item) => (int) $item['jumlah']);

                // Hitung total dan persiapkan data detail transaksi
                $grandTotal = 0;
                $totalUnitQty = 0;
                $detailsData = [];

                foreach ($itemsPayload as $item) {
                    $bId = (int)$item['barang_id'];
                    $b = $lockedBarangs[$bId];
                    $satuan = $item['satuan'];
                    $qty = (int)$item['jumlah'];

                    // Snapshot harga satuan saat pesanan dibuat (Aturan B7)
                    $hargaSatuan = ($satuan === 'Slop')
                        ? Barang::hargaSlopByQty((int) $totalSlopInOrder)
                        : (float)$b->harga_per_bal;
                    $subtotal = $qty * $hargaSatuan;

                    $grandTotal += $subtotal;
                    $totalUnitQty += $qty;

                    $detailsData[] = [
                        'barang_id' => $bId,
                        'satuan' => $satuan,
                        'jumlah' => $qty,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $namaMitraRaw = $user->nama_toko ? "{$user->nama_toko} ({$user->name})" : $user->name;
                $namaMitra = Str::limit($namaMitraRaw, 150, '');

                $metodeRaw = (string)$request->input('metode_pembayaran', 'COD');
                $metodePembayaran = (stripos($metodeRaw, 'transfer') !== false || stripos($metodeRaw, 'bca') !== false)
                    ? 'Transfer Bank'
                    : 'Bayar di Tempat (COD)';

                // Buat Transaksi Header
                $firstDetail = $detailsData[0];
                $trx = Transaksi::create([
                    'kode_transaksi' => $kodeTransaksi,
                    'user_id' => $user->id,
                    'barang_id' => $firstDetail['barang_id'],
                    'nama_mitra' => $namaMitra,
                    'telepon' => $request->input('telepon') ?: ($user->telepon ?: ''),
                    'alamat' => $request->input('alamat') ?: ($user->alamat ?: ''),
                    'jumlah' => $totalUnitQty,
                    'satuan' => count($detailsData) === 1 ? $firstDetail['satuan'] : 'Campuran',
                    'total_harga' => $grandTotal,
                    'catatan' => $request->input('catatan'),
                    'metode_pembayaran' => $metodePembayaran,
                    'status_pembayaran' => 'Belum Dibayar',
                    'status' => 'Baru Masuk',
                    'sumber' => 'Mobile App',
                ]);

                // Simpan Setiap Item ke TransaksiDetail
                foreach ($detailsData as &$d) {
                    $d['transaksi_id'] = $trx->id;
                }
                TransaksiDetail::insert($detailsData);

                // Catat ke Idempotency Cache jika ada
                if ($idempotencyKey) {
                    Cache::put("idempotency:order:{$user->id}:{$idempotencyKey}", $trx->id, now()->addMinutes(10));
                }

                return $trx;
            });

            ActivityLogger::log('API Pesanan Masuk', "Pesanan baru {$transaksi->kode_transaksi} dibuat via Mobile API oleh {$user->name}");

            $transaksi->load(['details.barang.kategori']);

            return $this->successResponse($this->formatTransaksi($transaksi, true), 'Pesanan berhasil dibuat! Pabrik akan memproses pesanan Anda.', 201);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('API Order QueryException: ' . $e->getMessage());
            return $this->errorResponse('Terjadi kesalahan sistem saat menyimpan pesanan. Silakan coba beberapa saat lagi.', 500);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } finally {
            $lock?->release();
        }
    }

    /**
     * Batalkan Pesanan Mandiri oleh Mitra (Hanya status 'Baru Masuk')
     * POST /api/v1/pesanan/{id}/batal
     */
    public function batalkan(Request $request, $id)
    {
        $user = $request->user();

        try {
            $transaksi = DB::transaction(function () use ($user, $id) {
                $transaksi = Transaksi::with(['details.barang', 'barang'])
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->find($id);

                if (!$transaksi) {
                    return null;
                }

                if ($transaksi->status !== 'Baru Masuk') {
                    throw new Exception('Pesanan yang sudah diproses atau dalam pengiriman tidak dapat dibatalkan mandiri. Hubungi pihak pabrik untuk pembatalan khusus.');
                }

                if ($transaksi->status_pembayaran === 'Lunas') {
                    throw new Exception('Pesanan yang pembayarannya sudah diverifikasi Lunas tidak dapat dibatalkan mandiri. Silakan hubungi Admin pabrik via WhatsApp untuk proses pembatalan dan pengembalian dana.');
                }

                // Kembalikan kuota stok presisi (Slop) seluruh varian barang secara teragregasi
                $restorationMap = [];
                if ($transaksi->details && $transaksi->details->isNotEmpty()) {
                    foreach ($transaksi->details as $d) {
                        if ($d->barang) {
                            $bId = $d->barang_id;
                            $slopPerBal = $d->barang->slop_per_bal ?: 20;
                            $slopUnit = ($d->satuan === 'Bal') ? ($d->jumlah * $slopPerBal) : $d->jumlah;
                            $restorationMap[$bId] = ($restorationMap[$bId] ?? 0) + $slopUnit;
                        }
                    }
                } elseif ($transaksi->barang) {
                    $slopPerBal = $transaksi->barang->slop_per_bal ?: 20;
                    $restorationMap[$transaksi->barang_id] = ($transaksi->satuan === 'Bal') ? ($transaksi->jumlah * $slopPerBal) : $transaksi->jumlah;
                }

                ksort($restorationMap);
                foreach ($restorationMap as $bId => $slopToRestore) {
                    $lockedBarang = Barang::where('id', $bId)->lockForUpdate()->first();
                    if ($lockedBarang) {
                        $lockedBarang->increment('stok', $slopToRestore);
                    }
                }

                $transaksi->update([
                    'status' => 'Dibatalkan',
                    'catatan' => trim(($transaksi->catatan ? $transaksi->catatan . "\n" : '') . "[Dibatalkan oleh pembeli via Mobile pada " . now()->format('d/m/Y H:i') . "]"),
                ]);

                return $transaksi;
            });
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        if (!$transaksi) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        ActivityLogger::log('API Batal Pesanan', "Pesanan {$transaksi->kode_transaksi} dibatalkan oleh mitra via mobile");

        $transaksi->refresh();
        $transaksi->load(['details.barang.kategori']);

        return $this->successResponse($this->formatTransaksi($transaksi, true), "Pesanan {$transaksi->kode_transaksi} berhasil dibatalkan dan kuota stok telah dipulihkan.");
    }

    /**
     * Konfirmasi Terima Barang & Unggah Foto Bukti (Hanya status 'Dikirim')
     * POST /api/v1/pesanan/{id}/konfirmasi-terima
     */
    public function konfirmasiTerima(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'foto_bukti' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'catatan_penerima' => ['nullable', 'string', 'max:500'],
        ], [
            'foto_bukti.required' => 'Foto bukti fisik penerimaan barang wajib diunggah.',
            'foto_bukti.image' => 'Berkas harus berupa gambar foto.',
            'foto_bukti.mimes' => 'Format gambar yang didukung: JPG, PNG, atau WEBP.',
            'foto_bukti.max' => 'Ukuran foto maksimal adalah 5 Megabyte (MB).',
        ]);

        try {
            $transaksi = DB::transaction(function () use ($request, $user, $id) {
                $transaksi = Transaksi::where('user_id', $user->id)->lockForUpdate()->find($id);

                if (!$transaksi) {
                    return null;
                }

                if ($transaksi->status !== 'Dikirim') {
                    throw new Exception("Konfirmasi penerimaan hanya dapat dilakukan jika pesanan dalam status 'Dikirim'. Status saat ini: {$transaksi->status}.");
                }

                if ($transaksi->isTransferBelumLunas()) {
                    throw new Exception('Pesanan Transfer Bank belum dapat dikonfirmasi selesai sebelum pembayaran diverifikasi Lunas oleh admin pabrik.');
                }

                $path = $request->file('foto_bukti')->store('bukti_penerimaan', 'public');

                $transaksi->update([
                    'status' => 'Selesai',
                    'status_pembayaran' => 'Lunas',
                    'bukti_penerimaan' => $path,
                    'diterima_pada' => now(),
                    'catatan_penerima' => $request->input('catatan_penerima'),
                ]);

                return $transaksi;
            });
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        if (!$transaksi) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        ActivityLogger::log('API Konfirmasi Terima', "Mitra {$user->nama_toko} mengonfirmasi terima barang untuk {$transaksi->kode_transaksi}");

        $transaksi->refresh();
        $transaksi->load(['details.barang.kategori']);

        return $this->successResponse($this->formatTransaksi($transaksi, true), "Terima kasih! Bukti penerimaan untuk pesanan {$transaksi->kode_transaksi} telah berhasil dikonfirmasi. Status pesanan kini Selesai.");
    }

    /**
     * Upload Bukti Transfer Bank
     * POST /api/v1/pesanan/{id}/upload-bukti-transfer
     */
    public function uploadBuktiTransfer(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();
        $transaksi = Transaksi::where('user_id', $user->id)->find($id);

        if (!$transaksi) {
            return $this->errorResponse('Pesanan tidak ditemukan atau bukan milik akun Anda.', 404);
        }

        if (!$transaksi->isTransferBank()) {
            return $this->errorResponse('Unggah bukti transfer hanya berlaku untuk pesanan dengan metode pembayaran Transfer Bank.', 422);
        }

        if (in_array($transaksi->status, ['Selesai', 'Dibatalkan'], true)) {
            return $this->errorResponse("Pesanan berstatus '{$transaksi->status}' tidak dapat diunggah bukti transfernya.", 422);
        }

        if ($transaksi->status_pembayaran === 'Lunas') {
            return $this->errorResponse('Pembayaran untuk pesanan ini sudah diverifikasi Lunas.', 422);
        }

        $file = $request->file('bukti_transfer');
        $filename = 'bukti_transfer_' . $transaksi->kode_transaksi . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('bukti_transfer', $filename, 'public');

        if ($transaksi->bukti_transfer && Storage::disk('public')->exists($transaksi->bukti_transfer)) {
            Storage::disk('public')->delete($transaksi->bukti_transfer);
        }

        $transaksi->update([
            'bukti_transfer' => $path,
            'ditransfer_pada' => now(),
            'metode_pembayaran' => 'Transfer Bank',
            'status_pembayaran' => 'Menunggu Verifikasi',
        ]);

        ActivityLogger::log('API Upload Bukti Transfer', "Bukti transfer bank diunggah untuk pesanan {$transaksi->kode_transaksi} oleh {$user->name}");

        $transaksi->refresh();
        $transaksi->load(['details.barang.kategori']);

        return $this->successResponse(
            $this->formatTransaksi($transaksi, true),
            "Bukti transfer berhasil diunggah. Pembayaran Anda kini sedang menunggu verifikasi oleh bagian keuangan pabrik."
        );
    }

    /**
     * Format data transaksi untuk respon API seragam
     */
    protected function formatTransaksi(Transaksi $t, bool $includeDetails = false): array
    {
        $details = [];
        if ($includeDetails || $t->relationLoaded('details')) {
            $details = $t->details->map(function ($d) {
                return [
                    'id' => $d->id,
                    'barang_id' => $d->barang_id,
                    'nama_barang' => $d->barang?->nama ?? 'Produk Rokok',
                    'kode_barang' => $d->barang?->kode_barang ?? '',
                    'satuan' => $d->satuan,
                    'jumlah' => (int)$d->jumlah,
                    'harga_satuan' => (float)$d->harga_satuan,
                    'subtotal' => (float)$d->subtotal,
                    'image_url' => $d->barang?->image_url,
                ];
            });
        }

        $buktiUrl = null;
        if ($t->bukti_penerimaan) {
            $buktiUrl = asset('storage/' . $t->bukti_penerimaan);
        }

        $buktiTransferUrl = null;
        if ($t->bukti_transfer) {
            $buktiTransferUrl = asset('storage/' . $t->bukti_transfer);
        }

        return [
            'id' => $t->id,
            'kode_transaksi' => $t->kode_transaksi,
            'status' => $t->status, // 'Baru Masuk', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'
            'total_harga' => (float)$t->total_harga,
            'formatted_total' => $t->formatted_total,
            'total_bungkus' => (int)$t->total_bungkus,
            'nama_mitra' => $t->nama_mitra,
            'telepon' => $t->telepon,
            'alamat' => $t->alamat,
            'catatan' => $t->catatan,
            'nomor_do' => $t->nomor_do,
            'nomor_resi' => $t->nomor_resi,
            'metode_pembayaran' => $t->metode_pembayaran ?: 'Bayar di Tempat (COD)',
            'status_pembayaran' => $t->status_pembayaran ?: ($t->status === 'Selesai' ? 'Lunas' : 'Belum Dibayar'),
            'ongkos_kirim' => 0, // Informasi gratis kurir resmi pabrik
            'boleh_batal' => $t->status === 'Baru Masuk' && $t->status_pembayaran !== 'Lunas',
            'boleh_konfirmasi' => $t->status === 'Dikirim',
            'bukti_penerimaan_url' => $buktiUrl,
            'bukti_transfer_url' => $buktiTransferUrl,
            'ditransfer_pada' => $t->ditransfer_pada?->toIso8601String(),
            'diterima_pada' => $t->diterima_pada?->toIso8601String(),
            'catatan_penerima' => $t->catatan_penerima,
            'created_at' => $t->created_at?->toIso8601String(),
            'ringkasan_item' => $t->ringkasan_item,
            'items' => $details,
        ];
    }
}
