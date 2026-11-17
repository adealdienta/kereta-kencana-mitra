<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'file_dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'urutan' => ['integer'],
        ]);

        if ($request->hasFile('file_dokumen')) {
            $validated['file_dokumen'] = $request->file('file_dokumen')->store('legalitas', 'public');
        }

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
            'file_dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'urutan' => ['integer'],
        ]);

        if ($request->hasFile('file_dokumen')) {
            if ($doc->file_dokumen && Storage::disk('public')->exists($doc->file_dokumen)) {
                Storage::disk('public')->delete($doc->file_dokumen);
            }
            $validated['file_dokumen'] = $request->file('file_dokumen')->store('legalitas', 'public');
        }

        $doc->update($validated);

        ActivityLogger::log('Ubah Legalitas', "Memperbarui dokumen: {$doc->nama_dokumen}");

        return back()->with('success', 'Dokumen legalitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $doc = LegalDocument::findOrFail($id);
        $nama = $doc->nama_dokumen;

        if ($doc->file_dokumen && Storage::disk('public')->exists($doc->file_dokumen)) {
            Storage::disk('public')->delete($doc->file_dokumen);
        }

        $doc->delete();

        ActivityLogger::log('Hapus Legalitas', "Menghapus dokumen: {$nama}");

        return back()->with('success', 'Dokumen legalitas berhasil dihapus.');
    }
}
