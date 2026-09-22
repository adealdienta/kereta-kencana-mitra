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
        $totalBal = Transaksi::sum('jumlah');
        $totalOmzet = Transaksi::sum('total_harga');

        $pesananTerbaru = Transaksi::with('barang')->latest()->take(5)->get();
        $stokMenipis = Barang::where('stok', '<=', 20)->take(5)->get();
        $aktivitasTerbaru = ActivityLog::latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalPesanan',
            'totalBal',
            'totalOmzet',
            'pesananTerbaru',
            'stokMenipis',
            'aktivitasTerbaru'
        ));
    }
}
