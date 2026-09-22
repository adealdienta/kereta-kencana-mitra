<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Helpers\ActivityLogger;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['barang.kategori', 'user']);

        // Filter status (BKPM Acara 23)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
        $transaksi = Transaksi::with(['barang.kategori', 'user'])->findOrFail($id);
        return view('admin.transaksis.show', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:Baru Masuk,Diproses,Selesai,Dibatalkan'],
            'nomor_do' => ['nullable', 'string', 'max:100'],
            'nomor_resi' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
        ]);

        $statusLama = $transaksi->status;
        $statusBaru = $validated['status'];

        // Jika status diubah menjadi 'Dibatalkan', kembalikan stok (BKPM Acara 18: Stok Recovery)
        if ($statusBaru === 'Dibatalkan' && $statusLama !== 'Dibatalkan') {
            $transaksi->barang->increment('stok', $transaksi->jumlah);
        }
        // Jika status semula 'Dibatalkan' lalu diaktifkan kembali
        elseif ($statusLama === 'Dibatalkan' && $statusBaru !== 'Dibatalkan') {
            $transaksi->barang->decrement('stok', $transaksi->jumlah);
        }

        $transaksi->update($validated);

        ActivityLogger::log('Update Transaksi', "Memperbarui status transaksi {$transaksi->kode_transaksi} menjadi {$statusBaru}");

        return back()->with('success', 'Status dan dokumen pengiriman pesanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // BKPM Acara 18: Stok Recovery jika transaksi dihapus sebelum selesai
        if ($transaksi->status !== 'Dibatalkan') {
            $transaksi->barang->increment('stok', $transaksi->jumlah);
        }

        $kode = $transaksi->kode_transaksi;
        $transaksi->delete();

        ActivityLogger::log('Hapus Transaksi', "Menghapus transaksi: {$kode}");

        return redirect()->route('admin.transaksis.index')->with('success', 'Transaksi berhasil dihapus dan stok telah dipulihkan.');
    }

    public function cetakFaktur($id)
    {
        $transaksi = Transaksi::with(['barang.kategori'])->findOrFail($id);
        return view('admin.transaksis.faktur', compact('transaksi'));
    }
}
