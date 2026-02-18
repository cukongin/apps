<?php

namespace App\Keuangan\Http\Controllers;

use App\Keuangan\Models\KategoriPemasukan;
use App\Keuangan\Models\Pemasukan;
use Illuminate\Http\Request;

class KategoriPemasukanController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $kategoris = KategoriPemasukan::all();
        return view('keuangan.kategori_pemasukan.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:kategori_pemasukans,nama|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        KategoriPemasukan::create($request->all());

        return back()->with('success', 'Kategori pemasukan baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_pemasukans,nama,' . $id,
            'deskripsi' => 'nullable|string'
        ]);

        $kategori = KategoriPemasukan::findOrFail($id);
        $oldName = $kategori->nama;
        $newName = $request->nama;

        $kategori->update($request->all());

        // Sync: Update existing records
        if ($oldName !== $newName) {
            Pemasukan::where('kategori', $oldName)->update(['kategori' => $newName]);
        }

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);

        if (Pemasukan::where('kategori', $kategori->nama)->exists()) {
            return back()->with('error', 'Gagal dihapus! Kategori ini sedang digunakan dalam data pemasukan.');
        }

        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}

