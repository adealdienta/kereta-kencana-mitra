<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Barang::aktif()->count();
        $totalPesanan = Transaksi::where('status', '!=', 'Dibatalkan')->count();
        $totalOmzet = Transaksi::where('status', '!=', 'Dibatalkan')
            ->where(function ($q) {
                $q->where('status_pembayaran', 'Lunas')
                  ->orWhere('status', 'Selesai');
            })
            ->sum('total_harga');

        // Hitung total kuantitas rokok presisi dalam satuan Slop & Bungkus (pesanan aktif diproses/dikirim/selesai)
        $activeTransaksis = Transaksi::with(['details.barang', 'barang'])
            ->whereNotIn('status', ['Dibatalkan', 'Baru Masuk'])
            ->get();

        $totalSlop = 0;
        $totalBungkus = 0;
        foreach ($activeTransaksis as $t) {
            if ($t->details && $t->details->isNotEmpty()) {
                foreach ($t->details as $d) {
                    $slopPerBal = $d->barang?->slop_per_bal ?: 20;
                    $bungkusPerSlop = $d->barang?->bungkus_per_slop ?: 10;
                    $itemSlop = ($d->satuan === 'Bal') ? ($d->jumlah * $slopPerBal) : $d->jumlah;
                    $totalSlop += $itemSlop;
                    $totalBungkus += $itemSlop * $bungkusPerSlop;
                }
            } else {
                $slopPerBal = $t->barang?->slop_per_bal ?: 20;
                $bungkusPerSlop = $t->barang?->bungkus_per_slop ?: 10;
                $itemSlop = ($t->satuan === 'Bal') ? ($t->jumlah * $slopPerBal) : $t->jumlah;
                $totalSlop += $itemSlop;
                $totalBungkus += $itemSlop * $bungkusPerSlop;
            }
        }

        $balCount = intdiv($totalSlop, 20);
        $slopSisa = $totalSlop % 20;

        if ($balCount > 0 && $slopSisa > 0) {
            $volumeDistribusi = "{$balCount} Bal, {$slopSisa} Slop";
        } elseif ($balCount > 0) {
            $volumeDistribusi = "{$balCount} Bal";
        } elseif ($slopSisa > 0) {
            $volumeDistribusi = "{$slopSisa} Slop";
        } else {
            $volumeDistribusi = "0 Slop";
        }

        $totalBal = $balCount; // Backward compatibility

        $pesananTerbaru = Transaksi::with(['details.barang', 'barang'])->latest()->take(5)->get();
        // Ambang stok menipis: <= 400 Slop (setara <= 20 Bal)
        $stokMenipis = Barang::aktif()->where('stok', '<=', 400)->orderBy('stok')->take(5)->get();
        $aktivitasTerbaru = ActivityLog::latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalPesanan',
            'totalBal',
            'volumeDistribusi',
            'totalBungkus',
            'totalOmzet',
            'pesananTerbaru',
            'stokMenipis',
            'aktivitasTerbaru'
        ));
    }
}
