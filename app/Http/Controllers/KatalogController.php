<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::withCount('barangs')->get();
        $query = Barang::with('kategori')->aktif();

        if ($request->filled('kategori')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('profil_rasa', 'like', "%{$search}%");
            });
        }

        $produks = $query->orderBy('urutan')->paginate(12)->withQueryString();

        return view('front.katalog', compact('produks', 'kategoris'));
    }

    public function detail($slug)
    {
        $produk = Barang::with('kategori')->where('slug', $slug)->firstOrFail();
        $terkait = Barang::with('kategori')
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->aktif()
            ->take(3)
            ->get();

        return view('front.detail_produk', compact('produk', 'terkait'));
    }
}
