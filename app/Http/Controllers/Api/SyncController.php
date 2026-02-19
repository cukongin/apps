<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jenjang;
use App\Models\TahunAjaran;
use App\Models\Semester;
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
                    'semester' => Semester::all(),
                    'users' => User::all(), // Sync Users (Required for Siswa/Guru FK)
                    'guru' => Guru::all(),
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
            // Note: Adjust table names based on your actual schema for grades/attendance
            // Using DB::table for raw performance if models are complex, or Models if clean.

            // Example for Grades (Nilai)
            $nilaiQuery = DB::table('nilai');
            if ($tahunId) $nilaiQuery->where('tahun_ajaran_id', $tahunId);
            if ($semesterId) $nilaiQuery->where('semester_id', $semesterId);
            $nilai = $nilaiQuery->get();

            // Example for Attendance (Absensi)
            $absensiQuery = DB::table('absensi');
            if ($tahunId) $absensiQuery->where('tahun_ajaran_id', $tahunId);
            if ($semesterId) $absensiQuery->where('semester_id', $semesterId);
            $absensi = $absensiQuery->get();

            // Example for Catatan Wali Kelas
            $catatanQuery = DB::table('catatan_walikelas');
            if ($tahunId) $catatanQuery->where('tahun_ajaran_id', $tahunId);
            if ($semesterId) $catatanQuery->where('semester_id', $semesterId);
            $catatan = $catatanQuery->get();

             // Example for Prestasi/Ekskul
             $ekskulQuery = DB::table('nilai_ekskul');
             if ($tahunId) $ekskulQuery->where('tahun_ajaran_id', $tahunId);
             if ($semesterId) $ekskulQuery->where('semester_id', $semesterId);
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
            $kategoriPemasukan = DB::table('kategori_pemasukan')->get(); // Adjust table name
            $kategoriPengeluaran = DB::table('kategori_pengeluaran')->get(); // Adjust table name
            $posBayar = DB::table('pos_bayar')->get(); // Adjust table name
            $jenisBayar = DB::table('jenis_bayar')->get(); // Adjust table name

            // Transactional Data
            // Note: Filter optimizations should be applied for large datasets (e.g. paging or date range)
            // For now, we pull based on Active Year if possible, or all (Warning: Heavy).

            $tagihanQuery = DB::table('tagihan');
            if ($tahunId) $tagihanQuery->where('tahun_ajaran_id', $tahunId);
            $tagihan = $tagihanQuery->get();

            $transaksiQuery = DB::table('transaksi'); // Linked to tagihan usually
            // if ($tahunId) ... connection to year is via tagihan usually.
            // For simplicity, we might just pull all relevant/recent.
            $transaksi = $transaksiQuery->get();

            $pemasukanLain = DB::table('pemasukan_lain')->get();
            $pengeluaranLain = DB::table('pengeluaran_lain')->get();
            $tabungan = DB::table('tabungan')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'kategori_pemasukan' => $kategoriPemasukan,
                    'kategori_pengeluaran' => $kategoriPengeluaran,
                    'pos_bayar' => $posBayar,
                    'jenis_bayar' => $jenisBayar,
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
                foreach ($data['tagihan'] as $item) DB::table('tagihan')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['transaksi'])) {
                foreach ($data['transaksi'] as $item) DB::table('transaksi')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['pemasukan_lain'])) {
                foreach ($data['pemasukan_lain'] as $item) DB::table('pemasukan_lain')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['pengeluaran_lain'])) {
                foreach ($data['pengeluaran_lain'] as $item) DB::table('pengeluaran_lain')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            if (isset($data['tabungan'])) {
                foreach ($data['tabungan'] as $item) DB::table('tabungan')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data received and synchronized.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
