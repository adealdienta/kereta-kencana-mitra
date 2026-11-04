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
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['barang.kategori', 'details.barang.kategori', 'user']);

        // Filter status (BKPM Acara 23)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter sumber (Web B2B vs Offline)
        if ($request->filled('sumber')) {
            $query->where('sumber', 'like', "%{$request->sumber}%");
        }

        // Pencarian nama mitra atau kode transaksi (BKPM Acara 23)
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
        $transaksi = Transaksi::with(['details.barang', 'barang'])->findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:Baru Masuk,Diproses,Dikirim,Selesai,Dibatalkan'],
            'nomor_do' => ['nullable', 'string', 'max:100'],
            'nomor_resi' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
        ]);

        $statusLama = $transaksi->status;
        $statusBaru = $validated['status'];

        // Jika status diubah menjadi 'Dibatalkan', kembalikan stok (BKPM Acara 18: Stok Recovery)
        if ($statusBaru === 'Dibatalkan' && $statusLama !== 'Dibatalkan') {
            if ($transaksi->details && $transaksi->details->isNotEmpty()) {
                foreach ($transaksi->details as $detail) {
                    if ($detail->barang) {
                        $slopPerBal = $detail->barang->slop_per_bal ?: 20;
                        $balRestored = ($detail->satuan === 'Bal') ? $detail->jumlah : max(1, (int)ceil($detail->jumlah / $slopPerBal));
                        $detail->barang->increment('stok', $balRestored);
                    }
                }
            } elseif ($transaksi->barang) {
                $slopPerBal = $transaksi->barang->slop_per_bal ?: 20;
                $balDeduct = $transaksi->satuan === 'Bal' ? $transaksi->jumlah : max(1, (int)ceil($transaksi->jumlah / $slopPerBal));
                $transaksi->barang->increment('stok', $balDeduct);
            }
        }
        // Jika status semula 'Dibatalkan' lalu diaktifkan kembali
        elseif ($statusLama === 'Dibatalkan' && $statusBaru !== 'Dibatalkan') {
            if ($transaksi->details && $transaksi->details->isNotEmpty()) {
                foreach ($transaksi->details as $detail) {
                    if ($detail->barang) {
                        $slopPerBal = $detail->barang->slop_per_bal ?: 20;
                        $balDeduct = ($detail->satuan === 'Bal') ? $detail->jumlah : max(1, (int)ceil($detail->jumlah / $slopPerBal));
                        $detail->barang->decrement('stok', $balDeduct);
                    }
                }
            } elseif ($transaksi->barang) {
                $slopPerBal = $transaksi->barang->slop_per_bal ?: 20;
                $balDeduct = $transaksi->satuan === 'Bal' ? $transaksi->jumlah : max(1, (int)ceil($transaksi->jumlah / $slopPerBal));
                $transaksi->barang->decrement('stok', $balDeduct);
            }
        }

        // Proteksi Fraud: Staf operasional tidak boleh mengubah status ke 'Selesai' secara sepihak
        if ($statusBaru === 'Selesai' && $statusLama !== 'Selesai') {
            if (Auth::user()->isStaff()) {
                return back()->with('error', 'Akses ditolak: Staf operasional tidak memiliki hak menyelesaikan pesanan secara langsung. Status Selesai hanya dapat diotorisasi oleh Owner / Super Admin dengan melampirkan foto Surat Jalan fisik, atau dikonfirmasi langsung oleh mitra di aplikasi.');
            }
        }

        // Jika Owner/Super Admin mengubah langsung ke Selesai
        if ($statusBaru === 'Selesai' && !$transaksi->diterima_pada) {
            $transaksi->diterima_pada = now();
        }

        $transaksi->update($validated);

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

        $transaksi = Transaksi::findOrFail($id);

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

        $path = $request->file('bukti_surat_jalan')->store('bukti_penerimaan', 'public');
        $roleName = strtoupper(Auth::user()->role);
        $userName = Auth::user()->name;

        $transaksi->update([
            'status' => 'Selesai',
            'diterima_pada' => now(),
            'bukti_penerimaan' => $path,
            'catatan_penerima' => "[Verifikasi Manual {$roleName} - {$userName}]: " . trim($validated['alasan_bypass']),
        ]);

        ActivityLogger::log('Verifikasi Manual Transaksi', "Owner/Super Admin {$userName} mengotorisasi penyelesaian transaksi {$transaksi->kode_transaksi} dengan lampiran surat jalan fisik.");

        return back()->with('success', "Transaksi {$transaksi->kode_transaksi} berhasil diselesaikan dengan arsip bukti fisik surat jalan. Kunci pemesanan mitra telah dibuka.");
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
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $satuan = $request->satuan;
        $hargaSatuan = $satuan === 'Slop' ? $barang->harga_per_slop : $barang->harga_per_bal;
        $total_harga = $request->jumlah * $hargaSatuan;

        // Validasi stok
        $slopPerBal = $barang->slop_per_bal ?: 20;
        $balDeduct = $satuan === 'Bal' ? $request->jumlah : max(1, (int)ceil($request->jumlah / $slopPerBal));

        if ($barang->stok < $balDeduct) {
            return back()->withInput()->withErrors([
                'jumlah' => "Stok di gudang tidak mencukupi (sisa {$barang->stok} Bal).",
            ]);
        }

        $kode_transaksi = 'OFF-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode_transaksi,
            'user_id' => $request->user_id ?: null,
            'barang_id' => $barang->id,
            'nama_mitra' => $request->nama_mitra,
            'telepon' => $request->telepon ?: '-',
            'alamat' => $request->alamat ?: 'Serah Terima Langsung di Pabrik (Ponggok, Blitar)',
            'jumlah' => $request->jumlah,
            'satuan' => $satuan,
            'total_harga' => $total_harga,
            'catatan' => $request->catatan ?: 'Pembelian Tunai / Kasir Langsung di Pabrik',
            'status' => 'Selesai',
            'sumber' => 'Langsung di Pabrik (Offline)',
            'diterima_pada' => now(),
            'catatan_penerima' => 'Barang langsung diterima pembeli di pabrik (Cash & Carry)',
        ]);

        // Kurangi stok gudang
        $barang->decrement('stok', $balDeduct);

        // Catat rincian item ke transaksi_details
        TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'barang_id' => $barang->id,
            'satuan' => $satuan,
            'jumlah' => $request->jumlah,
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $total_harga,
        ]);

        ActivityLogger::log('Transaksi Offline', "Transaksi kasir langsung di pabrik {$kode_transaksi} ({$request->nama_mitra})");

        return redirect()->route('admin.transaksis.faktur', $transaksi->id)
            ->with('success', "Transaksi langsung di pabrik {$kode_transaksi} berhasil dicatat dan lunas! Faktur kasir siap dicetak.");
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::with(['details.barang', 'barang'])->findOrFail($id);

        // BKPM Acara 18: Stok Recovery jika transaksi dihapus sebelum selesai
        if ($transaksi->status !== 'Dibatalkan') {
            if ($transaksi->details && $transaksi->details->isNotEmpty()) {
                foreach ($transaksi->details as $detail) {
                    if ($detail->barang) {
                        $slopPerBal = $detail->barang->slop_per_bal ?: 20;
                        $balRestored = ($detail->satuan === 'Bal') ? $detail->jumlah : max(1, (int)ceil($detail->jumlah / $slopPerBal));
                        $detail->barang->increment('stok', $balRestored);
                    }
                }
            } elseif ($transaksi->barang) {
                $slopPerBal = $transaksi->barang->slop_per_bal ?: 20;
                $balDeduct = $transaksi->satuan === 'Bal' ? $transaksi->jumlah : max(1, (int)ceil($transaksi->jumlah / $slopPerBal));
                $transaksi->barang->increment('stok', $balDeduct);
            }
        }

        $kode = $transaksi->kode_transaksi;
        $transaksi->delete();

        ActivityLogger::log('Hapus Transaksi', "Menghapus transaksi: {$kode}");

        return redirect()->route('admin.transaksis.index')->with('success', 'Transaksi berhasil dihapus dan stok telah dipulihkan.');
    }

    public function cetakFaktur($id)
    {
        $transaksi = Transaksi::with(['barang.kategori', 'details.barang.kategori'])->findOrFail($id);
        return view('admin.transaksis.faktur', compact('transaksi'));
    }
}
