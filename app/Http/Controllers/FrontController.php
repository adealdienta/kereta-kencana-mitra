<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LegalDocument;

class FrontController extends Controller
{
    public function beranda()
    {
        $produks = Barang::with('kategori')
            ->aktif()
            ->orderBy('urutan')
            ->take(6)
            ->get();

        $legalitas = LegalDocument::orderBy('urutan')->take(4)->get();

        return view('front.beranda', compact('produks', 'legalitas'));
    }

    public function profil()
    {
        $legalitas = LegalDocument::orderBy('urutan')->get();

        return view('front.profil', compact('legalitas'));
    }
}
