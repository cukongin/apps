<?php

namespace App\Keuangan\Http\Controllers;

use App\Keuangan\Models\KategoriKeringanan;
use App\Keuangan\Models\AturanDiskon;
use App\Keuangan\Models\JenisBiaya;
use Illuminate\Http\Request;

class KeringananController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $kategoris = KategoriKeringanan::with('aturanDiskons.jenisBiaya')->get();
        return view('keuangan.keuangan.keringanan.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'nullable'
        ]);

        KategoriKeringanan::create($request->all());

        return redirect()->route('keuangan.keringanan.index')->with('success', 'Kategori Keringanan berhasil dibuat.');
    }

    public function edit(KategoriKeringanan $keringanan, Request $request)
    {
        $jenisBiayas = JenisBiaya::where('status', 'active')->get();
        $keringanan->load('aturanDiskons');

        // Load Members (Active siswa with this category)
        $members = \App\Models\Siswa::where('kategori_keringanan_id', $keringanan->id)
            ->where('status', 'Aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Load Levels/Classes for Filter (Only for Active Academic Year)
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();

        $levels = \App\Models\Jenjang::with(['kelas' => function($q) use ($activeYear) {
            if ($activeYear) {
                $q->where('id_tahun_ajaran', $activeYear->id);
            }
            $q->orderBy('nama_kelas');
        }])->get();

        // Load Candidates based on Filter
        $candidates = [];
        $selectedKelasId = $request->query('kelas_id');

        if ($selectedKelasId) {
            $candidates = \App\Models\Siswa::where('status', 'Aktif')
                ->whereHas('anggota_kelas', function($q) use ($selectedKelasId, $activeYear) {
                    $q->where('id_kelas', $selectedKelasId)
                      ->where('status', 'aktif');

                    if ($activeYear) {
                        $q->whereHas('kelas', function($sub) use ($activeYear) {
                            $sub->where('id_tahun_ajaran', $activeYear->id);
                        });
                    }
                })
                ->where(function($q) use ($keringanan) {
                    $q->whereNull('kategori_keringanan_id')
                      ->orWhere('kategori_keringanan_id', '!=', $keringanan->id);
                })
                ->orderBy('nama_lengkap')
                ->get();
        }

        return view('keuangan.keuangan.keringanan.edit', compact('keringanan', 'jenisBiayas', 'members', 'levels', 'candidates', 'selectedKelasId'));
    }

    public function addMember(Request $request, KategoriKeringanan $keringanan)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = \App\Models\Siswa::findOrFail($request->siswa_id);
        $siswa->kategori_keringanan_id = $keringanan->id;
        $siswa->save();

        return redirect()->back()->with('success', "Siswa {$siswa->nama} berhasil ditambahkan ke kategori {$keringanan->nama}.");
    }

    public function removeMember(KategoriKeringanan $keringanan, \App\Models\siswa $siswa)
    {
        if ($siswa->kategori_keringanan_id == $keringanan->id) {
            $siswa->kategori_keringanan_id = null;
            $siswa->save();
            return redirect()->back()->with('success', "Siswa {$siswa->nama} dihapus dari kategori ini.");
        }

        return redirect()->back()->with('error', "Siswa tidak terdaftar di kategori ini.");
    }

    public function update(Request $request, KategoriKeringanan $keringanan)
    {
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'nullable'
        ]);

        $keringanan->update($request->only('nama', 'deskripsi'));

        // Sync Rules
        // Delete existing (simple approach for now, or updateOrCreate)
        // Let's assume the form submits an array: rules[jenis_biaya_id][tipe] and rules[jenis_biaya_id][jumlah]

        // Better: The user submits specific rules.
        // Let's iterate through the input 'rules'

        if ($request->has('rules')) {
            // Clear old rules? Or update?
            // Safer to delete all for this category and recreate, or updateOrCreate.
            // Let's use updateOrCreate for each JenisBiaya.

            foreach($request->rules as $jenisBiayaId => $data) {
                if (empty($data['jumlah']) || $data['jumlah'] == 0) {
                    // Remove rule if amount is 0
                    AturanDiskon::where('kategori_keringanan_id', $keringanan->id)
                        ->where('jenis_biaya_id', $jenisBiayaId)
                        ->delete();
                    continue;
                }

                AturanDiskon::updateOrCreate(
                    [
                        'kategori_keringanan_id' => $keringanan->id,
                        'jenis_biaya_id' => $jenisBiayaId
                    ],
                    [
                        'tipe_diskon' => $data['tipe'],
                        'jumlah' => $data['jumlah']
                    ]
                );
            }
        }

        return redirect()->route('keuangan.keringanan.index')->with('success', 'Aturan Keringanan berhasil diperbarui.');
    }

    public function destroy(KategoriKeringanan $keringanan)
    {
        $keringanan->delete();
        return redirect()->route('keuangan.keringanan.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

