<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterMapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::orderBy('nama_mapel', 'asc')
            ->orderBy('nama_kitab', 'asc') // Secondary sort
            ->get();
        return view('master.mapel.index', compact('mapels'));
    }

    public function plotting()
    {
        // Sort by Category first (for grouping), then Name, then Kitab
        $mapels = Mapel::orderBy('kategori', 'asc')
            ->orderBy('nama_mapel', 'asc')
            ->orderBy('nama_kitab', 'asc')
            ->get()
            ->groupBy('kategori');

        $jenjangs = \App\Models\Jenjang::all();
        return view('master.mapel.plotting', compact('mapels', 'jenjangs'));
    }

    public function getPlottingData(Request $request)
    {
        // LOGIC REVISION 2: Use dedicated MapelPlotting table (Template)
        // This ensures configuration persists regardless of individual class changes.

        $activeMapelIds = \App\Models\MapelPlotting::where('id_jenjang', $request->id_jenjang)
            ->where('tingkat_kelas', $request->tingkat_kelas)
            ->pluck('id_mapel')
            ->toArray();

        // Get classes just for info/display
        $classes = \App\Models\Kelas::where('id_jenjang', $request->id_jenjang)
            ->where('tingkat_kelas', $request->tingkat_kelas)
            ->whereHas('tahun_ajaran', function($q){
                $q->where('status', 'aktif');
            })
            ->withCount('pengajar_mapel')
            ->orderBy('nama_kelas')
            ->get();

        return response()->json([
            'activeMapelIds' => $activeMapelIds,
            'classes' => $classes
        ]);
    }

    public function savePlotting(Request $request)
    {
        $request->validate([
            'id_jenjang' => 'required',
            'tingkat_kelas' => 'required',
            'mapel_ids' => 'array'
        ]);

        $selectedMapelIds = $request->mapel_ids ?? [];
        $jenjangId = $request->id_jenjang;
        $tingkat = $request->tingkat_kelas;

        // Backend Validation: Filter Mapels by Jenjang
        if (!empty($selectedMapelIds)) {
            $jenjangModel = \App\Models\Jenjang::find($jenjangId);
            if ($jenjangModel) {
                $kode = trim($jenjangModel->kode); // MI, MTS, TPQ

                // Get Mapels that are NOT compatible and Exclude them
                // Compatible: Target is SEMUA or Matching Kode
                $validMapelIds = \App\Models\Mapel::whereIn('id', $selectedMapelIds)
                    ->where(function($q) use ($kode) {
                        $q->where('target_jenjang', 'SEMUA')
                          ->orWhere('target_jenjang', $kode);
                    })
                    ->pluck('id')
                    ->toArray();

                $selectedMapelIds = $validMapelIds;
            }
        }

        DB::beginTransaction();
        try {
            // 1. Update Template (MapelPlotting)
            // Wipe old configuration for this level
            \App\Models\MapelPlotting::where('id_jenjang', $jenjangId)
                ->where('tingkat_kelas', $tingkat)
                ->delete();

            // Insert new configuration
            foreach ($selectedMapelIds as $mid) {
                \App\Models\MapelPlotting::create([
                    'id_jenjang' => $jenjangId,
                    'tingkat_kelas' => $tingkat,
                    'id_mapel' => $mid
                ]);
            }

            // 2. Sync to Active Classes (Optional but requested "Otomatis generated")
            // The user said: "otomatis ketika generet mapel di Konfigurasi Mapel ikut semua mapelnya.."
            // Meaning: Changing plotting SHOULD affect classes?
            // "kalo mapel ini di plot ke kelas ter tentu dia tiak berubah bosku meskipun di hapus kan beda tabel"
            // INTERPRETATION:
            // - Plotting Config (Template) is separate.
            // - Deleting from Class A does NOT uncheck it in Template.
            // - Applying Template (Save Plotting) SHOULD apply to ALL classes?
            // Let's assume Yes, "Save Plotting" = "Apply Template to All Classes".

            $classes = \App\Models\Kelas::where('id_jenjang', $jenjangId)
                ->where('tingkat_kelas', $tingkat)
                ->whereHas('tahun_ajaran', function($q){
                    $q->where('status', 'aktif');
                })
                ->get();

            $countClasses = $classes->count();

            foreach ($classes as $class) {
                // Remove Old Mapels in Class not in new Selection?
                // Or just Add missing?
                // Usually "Plotting" implies forcing the state.
                // But let's be safe: Add Missing Only, don't remove existing custom ones?
                // User complaint: "ikut semua mapelnya".
                // If I uncheck in plotting, it should ideally be removed from classes?
                // Let's go with "Sync" logic: The class mapels MUST match plotting.
                // BUT user said "meskipun dihapus kan beda tabel".

                // HYBRID APPROACH:
                // 1. Add mapels from template to class if missing.
                // 2. Do NOT remove extra mapels in class (custom ones).

                foreach ($selectedMapelIds as $mid) {
                    \App\Models\PengajarMapel::firstOrCreate([
                        'id_kelas' => $class->id,
                        'id_mapel' => $mid
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('master.mapel.plotting')->with('success', "Konfigurasi disimpan! Diaplikasikan ke $countClasses kelas.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal menyimpan: " . $e->getMessage());
        }
    }

    public function copyPlotting(Request $request) {
        $request->validate([
            'source_jenjang' => 'required',
            'source_tingkat' => 'required',
            'targets' => 'required|array|min:1' // Array of "jenjang_id-tingkat" e.g. "1-2"
        ]);

        // 1. Get Source Template (MapelPlotting)
        // We use the TEMPLATE as the source of truth, not a random class.
        $sourceMapelIds = \App\Models\MapelPlotting::where('id_jenjang', $request->source_jenjang)
            ->where('tingkat_kelas', $request->source_tingkat)
            ->pluck('id_mapel')
            ->toArray();

        if (empty($sourceMapelIds)) {
            return back()->with('error', 'Sumber Plotting (Template) belum diatur. Silakan simpan plotting dulu di tingkat sumber.');
        }

        DB::beginTransaction();
        try {
            $countClassesUpdated = 0;
            $countTargetsUpdated = 0;

            // 2. Loop Targets
            foreach ($request->targets as $targetString) {
                [$tJenjang, $tTingkat] = explode('-', $targetString);

                // A. Update Target Template (MapelPlotting)
                // WIPE old template for target
                \App\Models\MapelPlotting::where('id_jenjang', $tJenjang)
                    ->where('tingkat_kelas', $tTingkat)
                    ->delete();

                // INSERT new template based on source
                foreach ($sourceMapelIds as $mid) {
                    \App\Models\MapelPlotting::create([
                        'id_jenjang' => $tJenjang,
                        'tingkat_kelas' => $tTingkat,
                        'id_mapel' => $mid
                    ]);
                }
                $countTargetsUpdated++;

                // B. Sync to Activ Classes in Target
                $targetClasses = \App\Models\Kelas::where('id_jenjang', $tJenjang)
                    ->where('tingkat_kelas', $tTingkat)
                    ->whereHas('tahun_ajaran', function($q) { $q->where('status', 'aktif'); })
                    ->get();

                foreach ($targetClasses as $class) {
                    // WIPE old class assignments
                    \App\Models\PengajarMapel::where('id_kelas', $class->id)->delete();

                    // INSERT new class assignments
                    foreach ($sourceMapelIds as $mid) {
                        \App\Models\PengajarMapel::create([
                            'id_kelas' => $class->id,
                            'id_mapel' => $mid
                        ]);
                    }
                    $countClassesUpdated++;
                }
            }

            DB::commit();
            return redirect()->back()->with('success', "Sukses menyalin plotting ke $countTargetsUpdated Tingkat ($countClassesUpdated Kelas aktif)!");

        } catch(\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal menyalin: " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required',
            'nama_kitab' => 'nullable|string',
            'kode_mapel' => 'nullable|unique:mapel,kode_mapel',
            'kategori' => 'required', // Allow any string
            'target_jenjang' => 'required|in:MI,MTS,SEMUA',
        ]);

        Mapel::create($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required',
            'nama_kitab' => 'nullable|string',
            'kode_mapel' => 'nullable|unique:mapel,kode_mapel,' . $id,
            'kategori' => 'required', // Allow any string
            'target_jenjang' => 'required|in:MI,MTS,SEMUA',
        ]);

        $mapel->update($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus');
    }

    public function destroyAll()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Mapel::truncate();
            \App\Models\KkmMapel::truncate();
            \App\Models\PengajarMapel::truncate();
            \App\Models\NilaiSiswa::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'SEMUA Mata Pelajaran dan data terkait (Nilai, Guru, KKM) berhasil dihapus bersih.');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'template_import_mapel.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        // Added nama_kitab
        $columns = array('nama_mapel', 'nama_kitab', 'kode_mapel', 'kategori', 'target_jenjang');

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Example Data
            fputcsv($file, array('Matematika Wajib', '', 'MTK-A', 'UMUM', 'MI'));
            fputcsv($file, array('Fiqih', 'Mabadi Fiqhiyah', 'PAI-FIQIH', 'AGAMA', 'MTS'));

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');

        // 1. Read Raw Content
        $content = file_get_contents($file->getPathname());

        // 2. Detect Encoding (Prioritize UTF-8, then Arabic ISO, then fallback)
        // Windows-1256 is not supported on this server, using ISO-8859-6
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-6', 'Windows-1252', 'ASCII'], true);

        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding ?: 'Windows-1252');
        }

        // 3. Remove BOM if present (UTF-8 BOM)
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        // 4. Parse Lines
        $lines = preg_split("/\r\n|\n|\r/", $content); // Robust line splitting

        // Header handling
        $headerLine = array_shift($lines);

        $count = 0;

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $row = str_getcsv($line);

            // Mapping: 0:nama, 1:kitab, 2:kode, 3:kategori, 4:jenjang
            if (count($row) < 5) continue;

            $nama = trim($row[0]);
            $kitab = trim($row[1]);
            $kode = trim($row[2]);
            $kategori = strtoupper(trim($row[3]));
            $jenjang = strtoupper(trim($row[4]));

            // Final safety check for UTF8 validity
            $nama = mb_convert_encoding($nama, 'UTF-8', 'UTF-8');
            $kitab = mb_convert_encoding($kitab, 'UTF-8', 'UTF-8');

            Mapel::updateOrCreate(
                ['kode_mapel' => $kode],
                [
                    'nama_mapel' => $nama,
                    'nama_kitab' => $kitab,
                    'kategori' => $kategori,
                    'target_jenjang' => $jenjang
                ]
            );
            $count++;
        }

        return redirect()->back()->with('success', "Berhasil mengimpor $count Mata Pelajaran (Detected: $encoding).");
    }
}
