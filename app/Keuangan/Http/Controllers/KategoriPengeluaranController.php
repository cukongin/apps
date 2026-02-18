<?php

namespace App\Keuangan\Http\Controllers;

use App\Keuangan\Models\KategoriPengeluaran;
use App\Keuangan\Models\Pengeluaran;
use Illuminate\Http\Request;

class KategoriPengeluaranController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $kategoris = KategoriPengeluaran::all();
        return view('keuangan.kategori_pengeluaran.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:kategori_pengeluarans,nama|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        KategoriPengeluaran::create($request->all());

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_pengeluarans,nama,' . $id,
            'deskripsi' => 'nullable|string'
        ]);

        $kategori = KategoriPengeluaran::findOrFail($id);
        $oldName = $kategori->nama;
        $newName = $request->nama;

        $kategori->update($request->all());

        // Sync: Update existing expenses that used the old category name
        if ($oldName !== $newName) {
            Pengeluaran::where('kategori', $oldName)->update(['kategori' => $newName]);
        }

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);

        // Safety Check: Prevent accidental deletion if used
        if (Pengeluaran::where('kategori', $kategori->nama)->exists()) {
            return back()->with('error', 'Gagal dihapus! Kategori ini sedang digunakan dalam data pengeluaran.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}

