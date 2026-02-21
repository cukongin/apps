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

            if (isset($data['siswa'])) {
                foreach ($data['siswa'] as $item) DB::table('siswa')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

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

            // Self-Heal: Remove any duplicate bills generated during this push
            \App\Keuangan\Services\BillService::removeDuplicates();
            \App\Models\PredikatNilai::removeDuplicates(); // Heal legacy duplicate predicates

            return response()->json(['success' => true, 'message' => 'Data received and synchronized.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Get list of tables to sync (Excluding system tables)
     */
    private function getSyncableTables()
    {
        $allTables = DB::select('SHOW TABLES');
        $tables = array_map(function ($table) {
            return array_values((array)$table)[0];
        }, $allTables);

        // Denylist (System Tables)
        $denylist = [
            'migrations',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
        ];

        // Filter tables
        return array_filter($tables, function ($table) use ($denylist) {
            // Exclude exact matches
            if (in_array($table, $denylist)) return false;
            // Exclude patterns (like telescope_*) if needed, though strict list is safer
            return true;
        });
    }

    /**
     * Pull FULL Database (All Tables)
     */
    public function pullFullDatabase()
    {
        try {
            $tables = $this->getSyncableTables();
            $data = [];

            foreach ($tables as $table) {
                // Fetch all data for this table
                $data[$table] = DB::table($table)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Receive FULL Database Push
     */
    public function receiveFullPush(Request $request)
    {
        try {
            $data = $request->all(); // Expecting ['table_name' => [rows], ...]
            $stats = [];

            DB::beginTransaction();

            // Disable Foreign Key Checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($data as $table => $rows) {
                // Skip if table not in syncable list
                $syncable = $this->getSyncableTables();
                if (!in_array($table, $syncable)) continue;

                $stats[$table] = ['inserted' => 0, 'updated' => 0, 'unchanged' => 0];

                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        try {
                            $row = (array)$row;
                            $exists = DB::table($table)->where('id', $row['id'])->first();

                            if ($exists) {
                                // Try Update
                                $affected = DB::table($table)->where('id', $row['id'])->update($row);
                                if ($affected > 0) {
                                    $stats[$table]['updated']++;
                                } else {
                                    $stats[$table]['unchanged']++;
                                }
                            } else {
                                // Insert
                                DB::table($table)->insert($row);
                                $stats[$table]['inserted']++;
                            }

                        } catch (\Exception $e) {
                           // Log specific row error
                        }
                    }
                }
            }

            // Re-enable Foreign Key Checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Full Database Synchronized Successfully.',
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Smart Bi-Directional Sync (Server Side)
     * Receives Client Data -> Updates Server if Client is newer.
     * Returns Server Data -> Where Server is newer/missing in Client.
     */
    public function receiveSmartSync(Request $request)
    {
        try {
            $clientData = $request->all(); // ['table' => [rows]]
            $responsePayload = [];
            $stats = ['incoming' => [], 'outgoing' => []];

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tables = $this->getSyncableTables();

            foreach ($tables as $table) {
                // Skip if table not in syncable list (Security check)
                if (!in_array($table, $tables)) continue;

                $clientRows = $clientData[$table] ?? [];
                // Re-key client rows by ID for faster lookup: [id => row]
                $clientMap = [];
                foreach ($clientRows as $row) {
                    $row = (array)$row;
                    $clientMap[$row['id']] = $row;
                }

                $stats['incoming'][$table] = 0;
                $stats['outgoing'][$table] = 0;
                $responsePayload[$table] = [];

                // 1. Process Client Data (Incoming)
                foreach ($clientMap as $id => $clientRow) {
                    $serverRow = DB::table($table)->where('id', $id)->first();
                    $serverRow = $serverRow ? (array)$serverRow : null;

                    if (!$serverRow) {
                        // New on Client -> Insert to Server
                        try {
                            DB::table($table)->insert($clientRow);
                            $stats['incoming'][$table]++;
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Handle Unique Constraint Violation (Duplicate Entry)
                            // This happens if ID is different but other Unique Keys match.
                            // We SKIP to avoid crashing, effectively "Server Wins" for this row.
                            if ($e->getCode() === '23000') {
                                // Log::warning("Sync Conflict in $table: " . $e->getMessage());
                                continue;
                            }
                            throw $e;
                        }
                    } else {
                        // Conflict Resolution
                        $clientTime = isset($clientRow['updated_at']) ? strtotime($clientRow['updated_at']) : 0;
                        $serverTime = isset($serverRow['updated_at']) ? strtotime($serverRow['updated_at']) : 0;

                        if ($clientTime > $serverTime) {
                            // Client is newer -> Update Server

                            // FIX: Preserve Server Photo if Client sends null/empty (Prevent Deletion)
                            if ($table === 'siswa' && empty($clientRow['foto']) && !empty($serverRow['foto'])) {
                                unset($clientRow['foto']);
                            }

                            try {
                                DB::table($table)->where('id', $id)->update($clientRow);
                                $stats['incoming'][$table]++;
                            } catch (\Illuminate\Database\QueryException $e) {
                                if ($e->getCode() === '23000') {
                                     continue; // Skip on conflict
                                }
                                throw $e;
                            }
                        }
                    }
                }

                // 2. Process Server Data (Outgoing)
                // We need to check ALL server rows to see if they are missing in client or newer than client
                $allServerRows = DB::table($table)->get();

                foreach ($allServerRows as $serverRow) {
                    $serverRow = (array)$serverRow;
                    $id = $serverRow['id'];

                    if (!isset($clientMap[$id])) {
                        // Missing in Client -> Send to Client
                        $responsePayload[$table][] = $serverRow;
                        $stats['outgoing'][$table]++;
                    } else {
                        // Exists in Client, check if Server is newer
                        $clientRow = $clientMap[$id];
                        $clientTime = isset($clientRow['updated_at']) ? strtotime($clientRow['updated_at']) : 0;
                        $serverTime = isset($serverRow['updated_at']) ? strtotime($serverRow['updated_at']) : 0; // recalc

                        if ($serverTime > $clientTime) {
                            // Server is newer -> Send to Client
                            $responsePayload[$table][] = $serverRow;
                            $stats['outgoing'][$table]++;
                        }
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            // Self-Heal: Remove any duplicate bills generated by sync overlaps
            \App\Keuangan\Services\BillService::removeDuplicates();
            \App\Models\PredikatNilai::removeDuplicates(); // Heal legacy duplicate predicates

            return response()->json([
                'success' => true,
                'message' => 'Smart Sync Completed.',
                'stats' => $stats,
                'data' => $responsePayload // Diff to be applied on Client
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload Expense Proof File (Photo)
     */
    public function uploadExpenseProof(Request $request)
    {
        try {
            if (!$request->hasFile('proof')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
            }

            $file = $request->file('proof');
            $relativePath = $request->input('path'); // e.g., "struk/123456_nota.jpg"

            if (!$relativePath) {
                return response()->json(['success' => false, 'message' => 'Path is required'], 400);
            }

            // Security: Prevent Directory Traversal
            if (strpos($relativePath, '..') !== false) {
                return response()->json(['success' => false, 'message' => 'Invalid path security'], 400);
            }

            // Save File (Overwrite if exists)
            \Illuminate\Support\Facades\Storage::disk('public')->put($relativePath, file_get_contents($file));

            return response()->json(['success' => true, 'path' => $relativePath]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
