<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\LegalDocument;
use App\Models\CompanySetting;
use App\Helpers\ActivityLogger;

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

    public function kontak()
    {
        return view('front.kontak');
    }

    public function kirimPesan(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:30'],
            'pesan' => ['required', 'string'],
        ]);

        ActivityLogger::log('Pesan Kemitraan', 'Pesan dari: ' . $request->nama . ' (' . $request->telepon . ')');

        return back()->with('success', 'Terima kasih, pesan dan permohonan kemitraan Anda telah diterima oleh tim manajemen pabrik.');
    }
}
