<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\LegalDocument;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Barang::count();
        $totalPesanan = Transaksi::count();
        $totalOmzet = Transaksi::where('status', '!=', 'Dibatalkan')->sum('total_harga');

        // Hitung total kuantitas rokok presisi dalam satuan Slop & Bal
        $activeTransaksis = Transaksi::with(['details.barang', 'barang'])
            ->where('status', '!=', 'Dibatalkan')
            ->get();

        $totalSlop = 0;
        foreach ($activeTransaksis as $t) {
            if ($t->details && $t->details->isNotEmpty()) {
                foreach ($t->details as $d) {
                    $totalSlop += $d->total_slop;
                }
            } else {
                $slopPerBal = $t->barang?->slop_per_bal ?: 20;
                $totalSlop += ($t->satuan === 'Bal') ? ($t->jumlah * $slopPerBal) : $t->jumlah;
            }
        }

        $balCount = intdiv($totalSlop, 20);
        $slopSisa = $totalSlop % 20;
        $totalBungkus = $totalSlop * 10;

        if ($balCount > 0 && $slopSisa > 0) {
            $volumeDistribusi = "{$balCount} Bal, {$slopSisa} Slop";
        } elseif ($balCount > 0) {
            $volumeDistribusi = "{$balCount} Bal";
        } elseif ($slopSisa > 0) {
            $volumeDistribusi = "{$slopSisa} Slop";
        } else {
            $volumeDistribusi = "0 Bal";
        }

        $totalBal = $balCount; // Backward compatibility

        $pesananTerbaru = Transaksi::with('barang')->latest()->take(5)->get();
        $stokMenipis = Barang::where('stok', '<=', 20)->take(5)->get();
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
