<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::all();
        $query = Barang::with('kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $barangs = $query->orderBy('urutan')->paginate(10)->withQueryString();

        return view('admin.barangs.index', compact('barangs', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('admin.barangs.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'nama' => ['required', 'string', 'max:150'],
            'kode_barang' => ['required', 'string', 'max:50', 'unique:barangs,kode_barang'],
            'deskripsi' => ['nullable', 'string'],
            'profil_rasa' => ['nullable', 'string', 'max:255'],
            'tar_mg' => ['nullable', 'numeric'],
            'nikotin_mg' => ['nullable', 'numeric'],
            'batang_per_bungkus' => ['required', 'integer', 'min:1'],
            'bungkus_per_slop' => ['required', 'integer', 'min:1'],
            'slop_per_bal' => ['required', 'integer', 'min:1'],
            'harga_per_bal' => ['required', 'numeric', 'min:0'],
            'harga_per_slop' => ['nullable', 'numeric', 'min:0'],
            'min_order_bal' => ['required', 'integer', 'min:1'],
            'min_order_slop' => ['required', 'integer', 'min:1'],
            'stok' => ['required', 'integer', 'min:0'],
            'status_stok' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'aktif' => ['boolean'],
            'urutan' => ['integer'],
        ]);

        $validated['slug'] = Str::slug($request->nama);
        $validated['aktif'] = $request->boolean('aktif');
        if (empty($validated['harga_per_slop'])) {
            $validated['harga_per_slop'] = round($validated['harga_per_bal'] / ($validated['slop_per_bal'] ?: 20));
        }

        // Handle Image Upload (BKPM Acara 16)
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('produk', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        $barang = Barang::create($validated);

        ActivityLogger::log('Tambah Produk', "Menambahkan produk baru: {$barang->nama} ({$barang->kode_barang})");

        return redirect()->route('admin.barangs.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.barangs.edit', compact('barang', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'nama' => ['required', 'string', 'max:150'],
            'kode_barang' => ['required', 'string', 'max:50', 'unique:barangs,kode_barang,' . $barang->id],
            'deskripsi' => ['nullable', 'string'],
            'profil_rasa' => ['nullable', 'string', 'max:255'],
            'tar_mg' => ['nullable', 'numeric'],
            'nikotin_mg' => ['nullable', 'numeric'],
            'batang_per_bungkus' => ['required', 'integer', 'min:1'],
            'bungkus_per_slop' => ['required', 'integer', 'min:1'],
            'slop_per_bal' => ['required', 'integer', 'min:1'],
            'harga_per_bal' => ['required', 'numeric', 'min:0'],
            'harga_per_slop' => ['nullable', 'numeric', 'min:0'],
            'min_order_bal' => ['required', 'integer', 'min:1'],
            'min_order_slop' => ['required', 'integer', 'min:1'],
            'stok' => ['required', 'integer', 'min:0'],
            'status_stok' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'aktif' => ['boolean'],
            'urutan' => ['integer'],
        ]);

        $validated['slug'] = Str::slug($request->nama);
        $validated['aktif'] = $request->boolean('aktif');
        if (empty($validated['harga_per_slop'])) {
            $validated['harga_per_slop'] = round($validated['harga_per_bal'] / ($validated['slop_per_bal'] ?: 20));
        }

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika disimpan di storage
            if ($barang->gambar && str_starts_with($barang->gambar, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $barang->gambar));
            }
            $path = $request->file('gambar')->store('produk', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        $barang->update($validated);

        ActivityLogger::log('Ubah Produk', "Memperbarui produk: {$barang->nama}");

        return redirect()->route('admin.barangs.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        // Validasi relasi transaksi (BKPM Acara 14/15)
        if ($barang->transaksis()->count() > 0) {
            return back()->with('error', 'Produk ini tidak dapat dihapus karena masih memiliki riwayat transaksi pemesanan.');
        }

        if ($barang->gambar && str_starts_with($barang->gambar, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $barang->gambar));
        }

        $nama = $barang->nama;
        $barang->delete();

        ActivityLogger::log('Hapus Produk', "Menghapus produk: {$nama}");

        return redirect()->route('admin.barangs.index')->with('success', 'Produk berhasil dihapus.');
    }
}
