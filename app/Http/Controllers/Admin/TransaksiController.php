<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Barang;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Exception;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['barang.kategori', 'details.barang.kategori', 'user']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter status pembayaran
        if ($request->filled('status_pembayaran')) {
            if ($request->status_pembayaran === 'Belum Dibayar') {
                $query->where(function ($q) {
                    $q->where('status_pembayaran', 'Belum Dibayar')
                      ->orWhereNull('status_pembayaran');
                });
            } else {
                $query->where('status_pembayaran', $request->status_pembayaran);
            }
        }

        // Filter sumber (Web, Mobile App, WhatsApp, Offline)
        if ($request->filled('sumber')) {
            $query->where('sumber', 'like', "%{$request->sumber}%");
        }

        // Pencarian nama mitra atau kode transaksi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('nama_mitra', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('nomor_do', 'like', "%{$search}%")
                  ->orWhere('nomor_resi', 'like', "%{$search}%");
            });
        }

        $transaksis = $query->latest()->paginate(12)->withQueryString();

        return view('admin.transaksis.index', compact('transaksis'));
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['barang.kategori', 'details.barang.kategori', 'user'])->findOrFail($id);
        return view('admin.transaksis.show', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Baru Masuk,Diproses,Dikirim,Selesai,Dibatalkan'],
            'nomor_do' => ['nullable', 'string', 'max:100'],
            'nomor_resi' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
        ]);

        $statusBaru = $validated['status'];

        try {
            $transaksi = DB::transaction(function () use ($id, $validated, $statusBaru) {
                $transaksi = Transaksi::with(['details.barang', 'barang'])->lockForUpdate()->findOrFail($id);
                $statusLama = $transaksi->status;

                // Admin operasional tidak boleh mengubah/membatalkan pesanan yang sudah Selesai
                if ($statusLama === 'Selesai' && $statusBaru !== 'Selesai' && Auth::user()->isAdmin()) {
                    throw new Exception('Pesanan yang sudah Selesai hanya dapat dikoreksi atau dibatalkan oleh Owner atau Super Admin.');
                }

                // Opsi 1: Pesanan Transfer Bank wajib punya bukti transfer & diverifikasi Lunas sebelum diproses/dikirim/diselesaikan
                if (in_array($statusBaru, ['Diproses', 'Dikirim', 'Selesai'], true) && $transaksi->isTransferBelumLunas()) {
                    $alasan = !$transaksi->bukti_transfer
                        ? 'pembeli belum mengunggah bukti transfer bank'
                        : 'bukti transfer bank belum diverifikasi Lunas oleh admin';

                    throw new Exception("Status tidak dapat diubah ke '{$statusBaru}' karena {$alasan}.");
                }

                // Proteksi Fraud: Admin operasional tidak boleh mengubah status ke 'Selesai' secara sepihak
                if ($statusBaru === 'Selesai' && $statusLama !== 'Selesai' && Auth::user()->isAdmin()) {
                    throw new Exception('Akses ditolak: Admin operasional tidak memiliki hak menyelesaikan pesanan secara langsung. Status Selesai hanya dapat diotorisasi oleh Owner / Super Admin dengan melampirkan foto Surat Jalan fisik, atau dikonfirmasi langsung oleh mitra.');
                }

                if ($statusBaru === 'Dibatalkan' && $statusLama !== 'Dibatalkan') {
                    // Agregasi pengembalian stok presisi (Slop) per barang_id
                    $restorationMap = [];
                    if ($transaksi->details && $transaksi->details->isNotEmpty()) {
                        foreach ($transaksi->details as $detail) {
                            if ($detail->barang) {
                                $slopPerBal = $detail->barang->slop_per_bal ?: 20;
                                $slopUnit = ($detail->satuan === 'Bal') ? ($detail->jumlah * $slopPerBal) : $detail->jumlah;
                                $restorationMap[$detail->barang_id] = ($restorationMap[$detail->barang_id] ?? 0) + $slopUnit;
                            }
                        }
                    } elseif ($transaksi->barang) {
                        $slopPerBal = $transaksi->barang->slop_per_bal ?: 20;
                        $slopUnit = $transaksi->satuan === 'Bal' ? ($transaksi->jumlah * $slopPerBal) : $transaksi->jumlah;
                        $restorationMap[$transaksi->barang_id] = $slopUnit;
                    }

                    ksort($restorationMap);
                    foreach ($restorationMap as $bId => $slopRestored) {
                        $lockedBarang = Barang::where('id', $bId)->lockForUpdate()->first();
                        if ($lockedBarang) {
                            $lockedBarang->increment('stok', $slopRestored);
                        }
                    }
                } elseif ($statusLama === 'Dibatalkan' && $statusBaru !== 'Dibatalkan') {
                    // Validasi kecukupan stok presisi (Slop) sebelum mengaktifkan kembali pesanan
                    $demandMap = [];
                    if ($transaksi->details && $transaksi->details->isNotEmpty()) {
                        foreach ($transaksi->details as $detail) {
                            $slopPerBal = $detail->barang?->slop_per_bal ?: 20;
                            $slopUnit = ($detail->satuan === 'Bal') ? ($detail->jumlah * $slopPerBal) : $detail->jumlah;
                            $demandMap[$detail->barang_id] = ($demandMap[$detail->barang_id] ?? 0) + $slopUnit;
                        }
                    } elseif ($transaksi->barang_id) {
                        $slopPerBal = $transaksi->barang?->slop_per_bal ?: 20;
                        $slopUnit = $transaksi->satuan === 'Bal' ? ($transaksi->jumlah * $slopPerBal) : $transaksi->jumlah;
                        $demandMap[$transaksi->barang_id] = $slopUnit;
                    }

                    ksort($demandMap);
                    foreach ($demandMap as $bId => $slopDeduct) {
                        $lockedBarang = Barang::lockForUpdate()->findOrFail($bId);
                        if ($lockedBarang->stok < $slopDeduct) {
                            throw new Exception("Stok {$lockedBarang->nama} tidak mencukupi untuk mengaktifkan kembali pesanan (tersedia: {$lockedBarang->stok} Slop, butuh: {$slopDeduct} Slop).");
                        }
                        $lockedBarang->decrement('stok', $slopDeduct);
                    }
                }

                if ($statusBaru === 'Selesai') {
                    if (!$transaksi->diterima_pada) {
                        $validated['diterima_pada'] = now();
                    }
                    $validated['status_pembayaran'] = 'Lunas';
                }

                $transaksi->update($validated);
                return $transaksi;
            });
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        ActivityLogger::log('Update Transaksi', "Memperbarui status transaksi {$transaksi->kode_transaksi} menjadi {$statusBaru}");

        return back()->with('success', 'Status dan dokumen pengiriman pesanan berhasil diperbarui.');
    }

    /**
     * Verifikasi Manual Penerimaan Khusus Owner / Super Admin via Surat Jalan Fisik (Arsip Wajib & Audit Trail)
     */
    public function verifikasiManual(Request $request, $id)
    {
        if (!Auth::user()->isOwner() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Hanya Owner atau Super Admin yang berwenang melakukan verifikasi manual / bypass penyelesaian pesanan.');
        }

        $validated = $request->validate([
            'bukti_surat_jalan' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'alasan_bypass' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'bukti_surat_jalan.required' => 'Wajib melampirkan foto fisik Surat Jalan / Kwitansi Setoran bertanda tangan basah.',
            'bukti_surat_jalan.image' => 'Berkas bukti surat jalan harus berupa gambar (JPG, PNG, WEBP).',
            'bukti_surat_jalan.max' => 'Ukuran foto surat jalan maksimal 3 MB.',
            'alasan_bypass.required' => 'Alasan verifikasi manual wajib diisi untuk keperluan audit internal.',
            'alasan_bypass.min' => 'Alasan verifikasi manual minimal 5 karakter.',
        ]);

        try {
            $transaksi = DB::transaction(function () use ($request, $id, $validated) {
                $transaksi = Transaksi::lockForUpdate()->findOrFail($id);

                if ($transaksi->status !== 'Dikirim') {
                    throw new Exception("Verifikasi manual hanya dapat dilakukan untuk pesanan berstatus 'Dikirim'. Status saat ini: {$transaksi->status}.");
                }

                if ($transaksi->isTransferBelumLunas()) {
                    throw new Exception('Verifikasi manual tidak dapat dilakukan sebelum bukti transfer bank diunggah dan diverifikasi Lunas.');
                }

                $path = $request->file('bukti_surat_jalan')->store('bukti_penerimaan', 'public');
                $roleName = strtoupper(Auth::user()->role);
                $userName = Auth::user()->name;

                $transaksi->update([
                    'status' => 'Selesai',
                    'status_pembayaran' => 'Lunas',
                    'diterima_pada' => now(),
                    'bukti_penerimaan' => $path,
                    'catatan_penerima' => "[Verifikasi Manual {$roleName} - {$userName}]: " . trim($validated['alasan_bypass']),
                ]);

                return $transaksi;
            });
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $userName = Auth::user()->name;
        ActivityLogger::log('Verifikasi Manual Transaksi', "Owner/Super Admin {$userName} mengotorisasi penyelesaian transaksi {$transaksi->kode_transaksi} dengan lampiran surat jalan fisik.");

        return back()->with('success', "Transaksi {$transaksi->kode_transaksi} berhasil diselesaikan dengan arsip bukti fisik surat jalan. Kunci pemesanan mitra telah dibuka.");
    }

    /**
     * Verifikasi Pembayaran Transfer Bank oleh Admin/Keuangan Pabrik
     */
    public function verifikasiPembayaran(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:terima,tolak'],
        ]);

        $action = $validated['action'];

        try {
            $transaksi = DB::transaction(function () use ($id, $action) {
                $transaksi = Transaksi::lockForUpdate()->findOrFail($id);

                if (in_array($transaksi->status, ['Selesai', 'Dibatalkan'], true)) {
                    throw new Exception("Pembayaran pesanan dengan status '{$transaksi->status}' sudah final dan tidak dapat diubah lagi.");
                }

                if (!$transaksi->isTransferBank()) {
                    throw new Exception('Verifikasi transfer hanya berlaku untuk transaksi dengan metode Transfer Bank.');
                }

                if (!$transaksi->bukti_transfer) {
                    throw new Exception('Bukti transfer belum diunggah oleh pembeli, sehingga pembayaran belum dapat diverifikasi.');
                }

                if ($transaksi->status_pembayaran === 'Lunas') {
                    throw new Exception('Pembayaran pesanan ini sudah diverifikasi Lunas dan tidak dapat ditolak kembali.');
                }

                $transaksi->update([
                    'status_pembayaran' => $action === 'terima' ? 'Lunas' : 'Ditolak / Belum Valid',
                ]);

                return $transaksi;
            });
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($action === 'terima') {
            ActivityLogger::log('Verifikasi Pembayaran', "Pembayaran Transfer Bank transaksi {$transaksi->kode_transaksi} diverifikasi Lunas oleh " . Auth::user()->name);
            return back()->with('success', "Pembayaran Transfer Bank untuk transaksi {$transaksi->kode_transaksi} berhasil diverifikasi Lunas.");
        }

        ActivityLogger::log('Tolak Bukti Transfer', "Bukti transfer bank transaksi {$transaksi->kode_transaksi} ditolak oleh " . Auth::user()->name);
        return back()->with('warning', "Bukti transfer transaksi {$transaksi->kode_transaksi} ditandai tidak valid / ditolak.");
    }

    /**
     * Formulir Input Penjualan Langsung / Kasir Offline di Pabrik
     */
    public function createOffline()
    {
        $barangs = Barang::aktif()->orderBy('urutan')->get();
        $pelanggans = User::where('role', 'pelanggan')->orderBy('name')->get();

        return view('admin.transaksis.create_offline', compact('barangs', 'pelanggans'));
    }

    /**
     * Simpan Transaksi Offline / Langsung di Pabrik
     */
    public function storeOffline(Request $request)
    {
        $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'satuan' => ['required', 'in:Slop,Bal'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'nama_mitra' => ['required', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'user_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'pelanggan')],
        ]);

        try {
            $result = DB::transaction(function () use ($request) {
                $barang = Barang::where('id', $request->barang_id)->lockForUpdate()->firstOrFail();

                if (!$barang->aktif) {
                    throw new Exception("Produk {$barang->nama} saat ini sedang nonaktif.");
                }

                $satuan = $request->satuan;
                $hargaSatuan = $satuan === 'Slop'
                    ? Barang::hargaSlopByQty((int) $request->jumlah)
                    : $barang->harga_per_bal;
                $total_harga = $request->jumlah * $hargaSatuan;

                // Validasi stok presisi dalam satuan Slop
                $slopPerBal = $barang->slop_per_bal ?: 20;
                $slopDeduct = $satuan === 'Bal' ? ((int) $request->jumlah * $slopPerBal) : (int) $request->jumlah;

                if ($barang->stok < $slopDeduct) {
                    throw new Exception("Stok di gudang tidak mencukupi (tersisa {$barang->formatted_stok}, butuh {$slopDeduct} Slop).");
                }

                $kode_transaksi = 'OFF-' . date('Ymd') . '-' . strtoupper(Str::random(4));

                $transaksi = Transaksi::create([
                    'kode_transaksi' => $kode_transaksi,
                    'user_id' => $request->user_id ?: null,
                    'barang_id' => $barang->id,
                    'nama_mitra' => Str::limit(trim($request->nama_mitra), 150, ''),
                    'telepon' => $request->telepon ?: '-',
                    'alamat' => $request->alamat ?: 'Serah Terima Langsung di Pabrik (Ponggok, Blitar)',
                    'jumlah' => $request->jumlah,
                    'satuan' => $satuan,
                    'total_harga' => $total_harga,
                    'catatan' => $request->catatan ?: 'Pembelian Tunai / Kasir Langsung di Pabrik',
                    'metode_pembayaran' => 'Tunai (Kasir Pabrik)',
                    'status_pembayaran' => 'Lunas',
                    'status' => 'Selesai',
                    'sumber' => 'Langsung di Pabrik (Offline)',
                    'diterima_pada' => now(),
                    'catatan_penerima' => 'Barang langsung diterima pembeli di pabrik (Cash & Carry)',
                ]);

                // Kurangi stok gudang presisi dalam satuan Slop
                $barang->decrement('stok', $slopDeduct);

                // Catat rincian item ke transaksi_details
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'satuan' => $satuan,
                    'jumlah' => $request->jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $total_harga,
                ]);

                return $transaksi;
            });
        } catch (Exception $e) {
            return back()->withInput()->withErrors([
                'jumlah' => $e->getMessage(),
            ]);
        }

        ActivityLogger::log('Transaksi Offline', "Transaksi kasir langsung di pabrik {$result->kode_transaksi} ({$request->nama_mitra})");

        return redirect()->route('admin.transaksis.faktur', $result->id)
            ->with('success', "Transaksi langsung di pabrik {$result->kode_transaksi} berhasil dicatat dan lunas! Faktur kasir siap dicetak.");
    }

    public function cetakFaktur($id)
    {
        $transaksi = Transaksi::with(['barang.kategori', 'details.barang.kategori'])->findOrFail($id);
        return view('admin.transaksis.faktur', compact('transaksi'));
    }
}
