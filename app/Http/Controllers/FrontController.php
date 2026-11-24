<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LegalDocument;
use App\Services\PabrikConfigService;

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
        $fasilitasImages = PabrikConfigService::getFasilitasImages();
        $heroSlides = PabrikConfigService::getHeroSlides();

        return view('front.beranda', compact('produks', 'legalitas', 'fasilitasImages', 'heroSlides'));
    }

    public function profil()
    {
        $legalitas = LegalDocument::orderBy('urutan')->get();
        $fasilitasImages = PabrikConfigService::getFasilitasImages();

        return view('front.profil', compact('legalitas', 'fasilitasImages'));
    }
}
