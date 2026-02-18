<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;
use App\Keuangan\Models\Pemasukan;
use App\Keuangan\Models\KategoriPemasukan;

class PemasukanController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $pemasukans = \App\Keuangan\Models\Pemasukan::latest()->paginate(10);
        $kategoriList = \App\Keuangan\Models\KategoriPemasukan::orderBy('nama')->get();
        return view('keuangan.pemasukan.index', compact('pemasukans', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sumber' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pemasukan' => 'required|date',
            'kategori' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        Pemasukan::create($data);

        return back()->with('success', 'Data pemasukan berhasil disimpan.');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);

        // Permission: Only 'admin_utama' OR 'Owner' can delete
        if (auth()->user()->role !== 'admin_utama' && $pemasukan->user_id != auth()->id()) {
            return back()->with('error', 'Akses Ditolak! Data hanya bisa dihapus oleh Pembuat atau Admin.');
        }

        $pemasukan->delete();
        return back()->with('success', 'Data pemasukan berhasil dihapus.');
    }
}

