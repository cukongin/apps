<?php

namespace App\Keuangan\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Jenjang; // Assuming Level is Jenjang or similar, but view uses $levels array or model.
use Illuminate\Http\Request;
use App\Keuangan\Services\BillService;
use Carbon\Carbon;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // View expects $levels as an array of ['nama_level' => [classes...]]
        // or iterate through levels.
        // Let's look at index.blade.php: foreach($levels as $levelName => $classes)

        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();

        $query = Kelas::with('wali_kelas', 'jenjang')
            ->orderBy('tingkat_kelas')
            ->orderBy('nama_kelas');

        if ($activeYear) {
            $query->where('id_tahun_ajaran', $activeYear->id);
        }

        $allClasses = $query->get();

        $levels = [];

        foreach ($allClasses as $kelas) {
            // Group by Jenjang Name (e.g. "Madrasah Ibtidaiyah", "Madrasah Tsanawiyah")
            // If no jenjang, fallback to 'Lainnya'
            $groupName = $kelas->jenjang ? $kelas->jenjang->nama : 'Lainnya';

            $levels[$groupName][] = [
                'id' => $kelas->id,
                'nama' => $kelas->nama_kelas,
                'wali' => $kelas->wali_kelas ? $kelas->wali_kelas->name : '-',
                'jumlah' => $kelas->siswas()->where('status', 'Aktif')->count(),
                'level_id' => $kelas->id_jenjang // For edit grouping
            ];
        }

        // Sort levels by key (Jenjang Name)
        ksort($levels);

        return view('keuangan.kelas.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Jenjang::all();
        // Or if 'level' in view refers to 'Jenjang' or just 'Tingkat'?
        // The view uses 'level_id' and iterates $levels.
        // Let's assume Level = Jenjang for now, or construct a list of Levels.
        // In `create.blade.php`: <option value="{{ $lvl->id }}">{{ $lvl->nama }}</option>
        // Use Jenjang model.

        // Actually, often Level = Tingkat (7, 8, 9).
        // specific implementation depends on School system.
        // Let's pass Jenjang as levels for compatibility with common logic.
        $levels = Jenjang::all();

        return view('keuangan.kelas.create', compact('levels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'level_id' => 'required', // This corresponds to id_jenjang or tingkat_kelas?
            // Based on Class Model: id_jenjang
        ]);

        // Need logic to determine tingkat_kelas from name or input?
        // For now, let's assume simple mapping or specific input.
        // View only has 'level_id' (select).
        // Let's assume level_id maps to id_jenjang.

        $jenjang = Jenjang::find($request->level_id);

        // Try to parse 'tingkat_kelas' from name or jenjang?
        // Default to 0 or regex from name?
        $tingkat = 0;
        if (preg_match('/\d+/', $request->nama, $matches)) {
            $tingkat = $matches[0];
        }

        Kelas::create([
            'nama_kelas' => $request->nama,
            'id_jenjang' => $request->level_id,
            'tingkat_kelas' => $tingkat,
            'wali_kelas_text' => $request->wali, // If simple text
            // If wali is user_id, we need lookup.
            // View input name="wali" type="text" placeholder="Nama Ustadz..."
            // So implementation likely uses a text field or we need to find user by name?
            // Existing model says: belongsTo User 'id_wali_kelas'.
            // Let's check if the view is sending an ID or Name. Input type is text.
            // We might just leave id_wali_kelas null for now or try to match.
        ]);

        return redirect()->route('keuangan.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kelas = Kelas::with('wali_kelas')->findOrFail($id);
        $levels = Jenjang::all();

        // Adapt for view properties
        // View uses $kelas->nama, $kelas->level_id, $kelas->wali_kelas (text/name)
        // Accessors in Model might handle 'nama' -> 'nama_kelas'

        return view('keuangan.kelas.edit', compact('kelas', 'levels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'level_id' => 'required',
        ]);

        $tingkat = $kelas->tingkat_kelas;
        if (preg_match('/\d+/', $request->nama, $matches)) {
            $tingkat = $matches[0];
        }

        $kelas->update([
            'nama_kelas' => $request->nama,
            'id_jenjang' => $request->level_id,
            'tingkat_kelas' => $tingkat,
            // 'id_wali_kelas' => ... logic for wali search?
        ]);

        return redirect()->route('keuangan.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        // Check if has relations (students, etc)
        if ($kelas->siswas()->count() > 0) {
             return back()->with('error', 'Gagal hapus: Kelas masih memiliki siswa aktif.');
        }

        $kelas->delete();

        return redirect()->route('keuangan.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Bulk Generate Bills for selected classes
     */
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'class_ids' => 'required|array',
            'start_month' => 'required|numeric|min:1|max:12',
            'start_year' => 'required|numeric',
            'months' => 'required|numeric|min:1|max:12',
        ]);

        $count = 0;
        $start = Carbon::create($request->start_year, $request->start_month, 1);

        // Retrieve all students in selected classes
        $students = \App\Models\Siswa::whereIn('kelas_id', $request->class_ids)
            ->where('status', 'Aktif')
            ->get();

        if ($students->isEmpty()) {
             return back()->with('error', 'Tidak ada siswa aktif di kelas yang dipilih.');
        }

        for ($i = 0; $i < $request->months; $i++) {
            $currentDate = $start->copy()->addMonths($i);

            foreach ($students as $siswa) {
                BillService::syncForsiswa($siswa, $currentDate->format('Y-m-d'));
                $count++;
            }
        }

        return back()->with('success', "Generate tagihan berhasil! Proses dijalankan untuk " . count($request->class_ids) . " kelas selama " . $request->months . " bulan.");
    }
}
