<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LegalDocument;
use App\Helpers\ActivityLogger;

class LegalitasController extends Controller
{
    public function index()
    {
        $documents = LegalDocument::orderBy('urutan')->get();
        return view('admin.legalitas.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_dokumen' => ['required', 'string', 'max:200'],
            'nomor' => ['nullable', 'string', 'max:100'],
            'penerbit' => ['nullable', 'string', 'max:150'],
            'berlaku_sampai' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
            'urutan' => ['integer'],
        ]);

        $doc = LegalDocument::create($validated);

        ActivityLogger::log('Tambah Legalitas', "Menambahkan dokumen: {$doc->nama_dokumen}");

        return back()->with('success', 'Dokumen legalitas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $doc = LegalDocument::findOrFail($id);

        $validated = $request->validate([
            'nama_dokumen' => ['required', 'string', 'max:200'],
            'nomor' => ['nullable', 'string', 'max:100'],
            'penerbit' => ['nullable', 'string', 'max:150'],
            'berlaku_sampai' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
            'urutan' => ['integer'],
        ]);

        $doc->update($validated);

        ActivityLogger::log('Ubah Legalitas', "Memperbarui dokumen: {$doc->nama_dokumen}");

        return back()->with('success', 'Dokumen legalitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $doc = LegalDocument::findOrFail($id);
        $nama = $doc->nama_dokumen;
        $doc->delete();

        ActivityLogger::log('Hapus Legalitas', "Menghapus dokumen: {$nama}");

        return back()->with('success', 'Dokumen legalitas berhasil dihapus.');
    }
}
