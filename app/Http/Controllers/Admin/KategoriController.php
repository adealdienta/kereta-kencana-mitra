<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('barangs')->get();
        return view('admin.kategoris.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategoris.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategoris,nama_kategori'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($request->nama_kategori);

        $kategori = Kategori::create($validated);

        ActivityLogger::log('Tambah Kategori', "Menambahkan kategori: {$kategori->nama_kategori}");

        return redirect()->route('admin.kategoris.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategoris.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategoris,nama_kategori,' . $kategori->id],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($request->nama_kategori);

        $kategori->update($validated);

        ActivityLogger::log('Ubah Kategori', "Memperbarui kategori: {$kategori->nama_kategori}");

        return redirect()->route('admin.kategoris.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        // BKPM Acara 14: Pengecekan Relasi Kategori dengan Barang sebelum dihapus
        if ($kategori->barangs()->count() > 0) {
            return back()->with('error', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh varian produk rokok.');
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        ActivityLogger::log('Hapus Kategori', "Menghapus kategori: {$nama}");

        return redirect()->route('admin.kategoris.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
