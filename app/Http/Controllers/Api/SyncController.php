<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataGuru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jenjang;
use App\Models\TahunAjaran;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Pull Master Data (Teachers, Classes, Subjects, Students)
     */
    public function pullMasterData()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'jenjang' => Jenjang::all(),
                    'tahun_ajaran' => TahunAjaran::all(),
                    'semester' => Periode::all(), // Send Periode data as 'semester' key for compatibility or update key? Let's use 'periode' key to be correct.
                    // Actually, let's keep 'semester' key if client expects it, OR update client too.
                    // Plan: Update key to 'semester' containing Periode data, but Client will map it to Periode model.
                    'semester' => Periode::all(),
                    'users' => User::all(), // Sync Users (Required for Siswa/Guru FK)
                    'guru' => DataGuru::all(), // Changed from Guru::all()
                    'kelas' => Kelas::all(),
                    'mapel' => Mapel::all(),
                    'siswa' => Siswa::with('kelas')->get(), // Eager load current class
                    // Add other master data as needed
                ],
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Pull Academic Data (Grades, Attendance, etc.)
     * filtered by Academic Year if provided.
     */
    public function pullAcademicData(Request $request)
    {
        try {
            // Optional: Filter by specific year/semester
            $tahunId = $request->input('tahun_ajaran_id');
            $semesterId = $request->input('semester_id');

            // Fetch Data
            // Corrected Table Names & Column Filters

            // 1. Nilai (nilai_siswa)
            $nilaiQuery = DB::table('nilai_siswa');
            if ($semesterId) $nilaiQuery->where('id_periode', $semesterId);
            elseif ($tahunId) {
                // If only Year ID provided, get periods for that year
                $periodIds = DB::table('periode')->where('id_tahun_ajaran', $tahunId)->pluck('id');
                $nilaiQuery->whereIn('id_periode', $periodIds);
            }
            $nilai = $nilaiQuery->get();

            // 2. Absensi (catatan_kehadiran)
            $absensiQuery = DB::table('catatan_kehadiran');
            if ($semesterId) $absensiQuery->where('id_periode', $semesterId);
            elseif ($tahunId) {
                $periodIds = DB::table('periode')->where('id_tahun_ajaran', $tahunId)->pluck('id');
                $absensiQuery->whereIn('id_periode', $periodIds);
            }
            $absensi = $absensiQuery->get();

            // 3. Catatan Wali Kelas (catatan_wali_kelas)
            $catatanQuery = DB::table('catatan_wali_kelas');
            if ($semesterId) $catatanQuery->where('id_periode', $semesterId);
            elseif ($tahunId) {
                $periodIds = DB::table('periode')->where('id_tahun_ajaran', $tahunId)->pluck('id');
                $catatanQuery->whereIn('id_periode', $periodIds);
            }
            $catatan = $catatanQuery->get();

             // 4. Ekskul (nilai_ekstrakurikuler)
             $ekskulQuery = DB::table('nilai_ekstrakurikuler');
             if ($semesterId) $ekskulQuery->where('id_periode', $semesterId);
             elseif ($tahunId) {
                 $periodIds = DB::table('periode')->where('id_tahun_ajaran', $tahunId)->pluck('id');
                 $ekskulQuery->whereIn('id_periode', $periodIds);
             }
             $ekskul = $ekskulQuery->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'nilai' => $nilai,
                    'absensi' => $absensi,
                    'catatan' => $catatan,
                    'nilai_ekskul' => $ekskul,
                ],
                'timestamp' => now(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Pull Finance Data (Bills, Transactions, Expenses, etc.)
     */
    public function pullFinanceData(Request $request)
    {
        try {
            // Optional: Filter by specific year
            $tahunId = $request->input('tahun_ajaran_id');

            // Finance Master Data
            $kategoriPemasukan = DB::table('kategori_pemasukans')->get();
            $kategoriPengeluaran = DB::table('kategori_pengeluarans')->get();
            $posBayar = DB::table('jenis_biayas')->get(); // Assuming pos_bayar maps to jenis_biayas or similar? Wait, list_tables has 'jenis_biayas'.
            // Let's check 'jenis_bayar' and 'pos_bayar'. List tables has 'jenis_biayas'.
            // User code had 'pos_bayar' and 'jenis_bayar'.
            // I will assume standard pluralization if specific match not found, but 'jenis_biayas' looks like 'jenis_biaya'.
            // To be safe, I'll use the exact names from list_tables.php: 'kategori_pemasukans', 'kategori_pengeluarans', 'jenis_biayas', 'tagihans', 'transaksis', 'pemasukans', 'pengeluarans', 'tabungans'.

            // Correction: I don't see 'pos_bayar' or 'jenis_bayar' in list_tables.
            // I see 'jenis_biayas'. I see 'pemasukans'.
            // I will use the names present in list_tables.

            $kategoriPemasukan = DB::table('kategori_pemasukans')->get();
            $kategoriPengeluaran = DB::table('kategori_pengeluarans')->get();
            $jenisBiaya = DB::table('jenis_biayas')->get();
            // $posBayar - unsure, maybe not existing? skipping for now or mapping to jenis_biayas?

            // Transactional Data
            $tagihanQuery = DB::table('tagihans');
            if ($tahunId) $tagihanQuery->where('tahun_ajaran_id', $tahunId); // Need to verify if 'tagihans' has 'tahun_ajaran_id'
            $tagihan = $tagihanQuery->get();

            $transaksiQuery = DB::table('transaksis');
            $transaksi = $transaksiQuery->get();

            $pemasukanLain = DB::table('pemasukans')->get();
            $pengeluaranLain = DB::table('pengeluarans')->get();
            $tabungan = DB::table('tabungans')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'kategori_pemasukan' => $kategoriPemasukan,
                    'kategori_pengeluaran' => $kategoriPengeluaran,
                    'jenis_biaya' => $jenisBiaya, // Changed key
                    'tagihan' => $tagihan,
                    'transaksi' => $transaksi,
                    'pemasukan_lain' => $pemasukanLain,
                    'pengeluaran_lain' => $pengeluaranLain,
                    'tabungan' => $tabungan,
                ],
                'timestamp' => now(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Receive Push Data from Desktop (Transactions, etc.)
     */
    public function receiveFinancePush(Request $request)
    {
        try {
            $data = $request->all();

            DB::beginTransaction();

            // UPSERT STRATEGY (Use ID as key)
            // Note: If Online has ID 100 and Offline has ID 100 but different content, Offline wins here.

            if (isset($data['tagihan'])) {
                foreach ($data['tagihan'] as $item) DB::table('tagihans')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['transaksi'])) {
                foreach ($data['transaksi'] as $item) DB::table('transaksis')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['pemasukan_lain'])) {
                foreach ($data['pemasukan_lain'] as $item) DB::table('pemasukans')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['pengeluaran_lain'])) {
                foreach ($data['pengeluaran_lain'] as $item) DB::table('pengeluarans')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['tabungan'])) {
                foreach ($data['tabungan'] as $item) DB::table('tabungans')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data received and synchronized.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
