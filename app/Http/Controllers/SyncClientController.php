<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\DataGuru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jenjang;
use App\Models\TahunAjaran;
use App\Models\Periode;
use Illuminate\Support\Str;
// use RealRashid\SweetAlert\Facades\Alert; (Removed to avoid dependency error)

class SyncClientController extends Controller
{
    public function index()
    {
        return view('settings.sync.index');
    }

    public function pullData(Request $request)
    {
        $baseUrl = config('app.sync_base_url', env('SYNC_BASE_URL'));
        $token = config('app.sync_token', env('SYNC_TOKEN'));

        if (!$baseUrl || !$token) {
            return redirect()->back()->with('error', 'Gagal: URL Server atau Token belum dikonfigurasi di .env!');
        }

        try {
            // 1. Pull Master Data
            $response = Http::withHeaders(['X-Sync-Token' => $token])
                            ->get($baseUrl . '/api/sync/master-data');

            if ($response->failed()) {
                $errorMsg = 'Server Error (' . $response->status() . '): ' . Str::limit(strip_tags($response->body()), 150);
                throw new \Exception($errorMsg);
            }

            $data = $response->json('data');

            Log::info('Sync Master Data Start', [
                'users_count' => count($data['users'] ?? []),
                'jenjang_count' => count($data['jenjang'] ?? []),
                'guru_count' => count($data['guru'] ?? []),
                'siswa_count' => count($data['siswa'] ?? []),
            ]);

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Sync Users First (FK Dependency)
            if (isset($data['users'])) {
                User::unguard();
                foreach ($data['users'] as $item) {
                     // Sanitize if needed
                     User::updateOrCreate(['id' => $item['id']], $item);
                }
                User::reguard();
            }

            // Sync Jenjang
            Jenjang::unguard();
            foreach ($data['jenjang'] as $item) {
                Jenjang::updateOrCreate(['id' => $item['id']], $item);
            }
            Jenjang::reguard();

            // Sync Tahun Ajaran
            TahunAjaran::unguard();
            foreach ($data['tahun_ajaran'] as $item) {
                TahunAjaran::updateOrCreate(['id' => $item['id']], $item);
            }
            TahunAjaran::reguard();

            // Sync Semester (Mapped to Periode Table)
            Periode::unguard();
            foreach ($data['semester'] as $item) {
                Periode::updateOrCreate(['id' => $item['id']], $item);
            }
            Periode::reguard();

            // Sync Guru (Mapped to DataGuru)
            DataGuru::unguard();
            foreach ($data['guru'] as $item) {
                DataGuru::updateOrCreate(['id' => $item['id']], $item);
            }
            DataGuru::reguard();

            // Sync Mapel
            Mapel::unguard();
            foreach ($data['mapel'] as $item) {
                Mapel::updateOrCreate(['id' => $item['id']], $item);
            }
            Mapel::reguard();

            // Sync Kelas
            Kelas::unguard();
            foreach ($data['kelas'] as $item) {
                Kelas::updateOrCreate(['id' => $item['id']], $item);
            }
            Kelas::reguard();

            // Sync Siswa
            Siswa::unguard();
            foreach ($data['siswa'] as $item) {
                // Sanitize: Remove relations/arrays that are not columns
                unset($item['kelas'], $item['user'], $item['jenjang'], $item['rombel'], $item['jurusan']);

                // Extra safety: Remove any other array values (nested relations)
                foreach ($item as $key => $value) {
                    if (is_array($value)) unset($item[$key]);
                }

                try {
                    Siswa::updateOrCreate(['id' => $item['id']], $item);
                } catch (\Exception $e) {
                     Log::error('Failed to sync Siswa ID: ' . $item['id'], ['error' => $e->getMessage()]);
                     throw $e; // Re-throw to trigger rollback
                }
            }
            Siswa::reguard();

            Log::info('Sync Master Data Completed Successfully');

            // Helper Sanitizer untuk array multidimensi agar `updateOrInsert` tidak error ColumnNotFound / Casting
            $sanitizeItem = function($item) {
                $arr = (array)$item;
                foreach ($arr as $k => $v) if(is_array($v)) unset($arr[$k]);
                return $arr;
            };

            // 2. Pull Academic Data (Optional: Separate button or same flow)
             $academicResponse = Http::withHeaders(['X-Sync-Token' => $token])
                            ->get($baseUrl . '/api/sync/academic-data');

            if ($academicResponse->successful()) {
                $acData = $academicResponse->json('data');

                // Sync Nilai (Direct DB Insert for performance/simplicity)
                // Note: We should truncate or be careful with duplicates.
                // For "Pull" strategy, usually we replace local data with server data for the active period.

                // Sync Nilai (Direct DB Insert for performance/simplicity)
                foreach ($acData['nilai'] as $item) {
                     // Ensure no 'kelas' column error by unsetting checks if needed, but usually raw DB insert is fine if columns match
                    DB::table('nilai_siswa')->updateOrInsert(
                        ['id' => $item['id']],
                        $sanitizeItem($item)
                    );
                }

                foreach ($acData['absensi'] as $item) {
                    DB::table('catatan_kehadiran')->updateOrInsert(
                        ['id' => $item['id']],
                        $sanitizeItem($item)
                    );
                }

                if (isset($acData['catatan'])) {
                    foreach ($acData['catatan'] as $item) {
                        DB::table('catatan_wali_kelas')->updateOrInsert(
                            ['id' => $item['id']],
                            $sanitizeItem($item)
                        );
                    }
                }

                if (isset($acData['nilai_ekskul'])) {
                    foreach ($acData['nilai_ekskul'] as $item) {
                        DB::table('nilai_ekstrakurikuler')->updateOrInsert(
                            ['id' => $item['id']],
                            $sanitizeItem($item)
                        );
                    }
                }
            }

            // 3. Pull Finance Data (New Request)
            $financeResponse = Http::withHeaders(['X-Sync-Token' => $token])
                            ->get($baseUrl . '/api/sync/finance-data');

            if ($financeResponse->successful()) {
                $finData = $financeResponse->json('data');

                // Sync Master Finance
                // Server sends 'jenis_biaya' (joined pos_bayar/jenis_bayar) -> insert to 'jenis_biayas'
                if(isset($finData['jenis_biaya'])) {
                    foreach ($finData['jenis_biaya'] as $item) DB::table('jenis_biayas')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));
                }

                foreach ($finData['kategori_pemasukan'] as $item) DB::table('kategori_pemasukans')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));
                foreach ($finData['kategori_pengeluaran'] as $item) DB::table('kategori_pengeluarans')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));

                // Sync Transactions (Heavy Data)
                foreach ($finData['tagihan'] as $item) DB::table('tagihans')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));
                foreach ($finData['transaksi'] as $item) DB::table('transaksis')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));

                foreach ($finData['pemasukan_lain'] as $item) DB::table('pemasukans')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));
                foreach ($finData['pengeluaran_lain'] as $item) DB::table('pengeluarans')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));
                foreach ($finData['tabungan'] as $item) DB::table('tabungans')->updateOrInsert(['id' => $item['id']], $sanitizeItem($item));
            }

            // [PERBAIKAN KEAMANAN TERPADU]: COMMIT SELURUH TRANSAKSI DI SINI SETELAH SEMUA DATA SELESAI MASUK
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            // Self-Heal: Prune any duplicate logical bills
            \App\Keuangan\Services\BillService::removeDuplicates();
            \App\Models\PredikatNilai::removeDuplicates(); // Heal legacy duplicate predicates

            // 4. Prepare Summary Data for UI
            $summary = [
                'Users' => count($data['users'] ?? []),
                'Jenjang' => count($data['jenjang'] ?? []),
                'Tahun Ajaran' => count($data['tahun_ajaran'] ?? []),
                'Periode (Semester)' => count($data['semester'] ?? []), // 'semester' key from server contains Periode data
                'Guru' => count($data['guru'] ?? []),
                'Mapel' => count($data['mapel'] ?? []),
                'Kelas' => count($data['kelas'] ?? []),
                'Siswa' => count($data['siswa'] ?? []),
            ];

            if (isset($acData)) {
                $summary['Nilai Siswa'] = count($acData['nilai'] ?? []);
                $summary['Catatan Kehadiran'] = count($acData['absensi'] ?? []);
                $summary['Catatan Wali Kelas'] = count($acData['catatan'] ?? []);
                $summary['Nilai Ekstrakurikuler'] = count($acData['nilai_ekskul'] ?? []);
            }

            if (isset($finData)) {
                $summary['Jenis Biaya'] = count($finData['jenis_biaya'] ?? []) + count($finData['pos_bayar'] ?? []) + count($finData['jenis_bayar'] ?? []);
                $summary['Kategori Pemasukan'] = count($finData['kategori_pemasukan'] ?? []);
                $summary['Kategori Pengeluaran'] = count($finData['kategori_pengeluaran'] ?? []);
                $summary['Tagihan'] = count($finData['tagihan'] ?? []);
                $summary['Transaksi'] = count($finData['transaksi'] ?? []);
                $summary['Pemasukan Lain'] = count($finData['pemasukan_lain'] ?? []);
                $summary['Pengeluaran Lain'] = count($finData['pengeluaran_lain'] ?? []);
                $summary['Tabungan'] = count($finData['tabungan'] ?? []);
            }

            return redirect()->back()->with('success', 'Berhasil: Sinkronisasi Lengkap Selesai!')->with('sync_summary', $summary);

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Pastikan key kembali menyala walau gagal
            Log::error('Sync Failed Global', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error Sinkronisasi: Data dibatalkan (Rollback) demi mencegah duplikasi. Pesan: ' . $e->getMessage());
        }
    }

    /**
     * Push Finance Data (Desktop -> Server)
     */
    public function pushFinanceData(Request $request)
    {
        $baseUrl = config('app.sync_base_url', env('SYNC_BASE_URL'));
        $token = config('app.sync_token', env('SYNC_TOKEN'));

        if (!$baseUrl || !$token) {
            return redirect()->back()->with('error', 'Gagal: URL Server atau Token belum dikonfigurasi!');
        }

        try {
            // Collect Local Finance Data to Push
            // We push everything, Server will handle deduplication (Upsert).
            // Warning: Huge payload if database is large. Ideally chunk it or filter by date.

            $payload = [
                'siswa' => DB::table('siswa')->get()->toArray(), // Include Student Data (Status updates etc)
                'tagihan' => DB::table('tagihans')->get()->toArray(),
                'transaksi' => DB::table('transaksis')->get()->toArray(),
                'pemasukan_lain' => DB::table('pemasukans')->get()->toArray(),
                'pengeluaran_lain' => DB::table('pengeluarans')->get()->toArray(),
                'tabungan' => DB::table('tabungans')->get()->toArray(),
            ];

            // Send to Server
            $response = Http::withHeaders(['X-Sync-Token' => $token])
                            ->post($baseUrl . '/api/sync/finance-push', $payload);

            if ($response->successful()) {
                $summary = [
                    'Siswa' => count($payload['siswa']),
                    'Tagihan' => count($payload['tagihan']),
                    'Transaksi' => count($payload['transaksi']),
                    'Pemasukan Lain' => count($payload['pemasukan_lain']),
                    'Pengeluaran Lain' => count($payload['pengeluaran_lain']),
                    'Tabungan' => count($payload['tabungan']),
                ];
                return redirect()->back()->with('success', 'Berhasil: Data Keuangan Berhasil Dikirim ke Server!')->with('sync_summary', $summary);
            } else {
                throw new \Exception('Gagal kirim: ' . $response->body());
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: Gagal Push: ' . $e->getMessage());
        }
    }
    /**
     * Get list of tables to sync (Excluding system tables) - Client Side
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
            if (in_array($table, $denylist)) return false;
            return true;
        });
    }

    /**
     * Pull FULL Database (Client -> Server)
     */
    public function pullFullData(Request $request)
    {
        $baseUrl = config('app.sync_base_url', env('SYNC_BASE_URL'));
        $token = config('app.sync_token', env('SYNC_TOKEN'));

        if (!$baseUrl || !$token) {
            return redirect()->back()->with('error', 'Gagal: URL Server atau Token belum dikonfigurasi!');
        }

        try {
            // Request Full Data
            $response = Http::timeout(300)
                            ->withHeaders(['X-Sync-Token' => $token])
                            ->get($baseUrl . '/api/sync/full-database');

            if ($response->failed()) {
                throw new \Exception('Server Error: ' . Str::limit(strip_tags($response->body()), 150));
            }

            $data = $response->json('data'); // ['table_name' => [rows], ...]

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $summary = [];

            foreach ($data as $table => $rows) {
                if (is_array($rows)) {
                    $tableName = Str::headline($table); // Beautify Name
                    $summary[$tableName] = ['inserted' => 0, 'updated' => 0, 'unchanged' => 0];

                    foreach ($rows as $row) {
                        try {
                            $row = (array)$row;
                            $exists = DB::table($table)->where('id', $row['id'])->first();

                            if ($exists) {
                                // Try Update
                                $affected = DB::table($table)->where('id', $row['id'])->update($row);
                                if ($affected > 0) {
                                    $summary[$tableName]['updated']++;
                                } else {
                                    $summary[$tableName]['unchanged']++;
                                }
                            } else {
                                // Insert
                                DB::table($table)->insert($row);
                                $summary[$tableName]['inserted']++;
                            }
                        } catch (\Exception $e) {
                             Log::warning("Sync Row Error in $table: " . $e->getMessage());
                        }
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil: Sinkronisasi FULL DATABASE Selesai!')->with('sync_summary', $summary);

        } catch (\Exception $e) {
             DB::rollBack();
             DB::statement('SET FOREIGN_KEY_CHECKS=1;');
             return redirect()->back()->with('error', 'Error Full Sync: ' . $e->getMessage());
        }
    }

    /**
     * Push FULL Database (Client -> Server)
     */
    public function pushFullData(Request $request)
    {
        $baseUrl = config('app.sync_base_url', env('SYNC_BASE_URL'));
        $token = config('app.sync_token', env('SYNC_TOKEN'));

        if (!$baseUrl || !$token) {
            return redirect()->back()->with('error', 'Gagal: URL Server atau Token belum dikonfigurasi!');
        }

        try {
            // 1. Gather ALL Local Data
            $tables = $this->getSyncableTables();
            $payload = [];

            foreach ($tables as $table) {
                $payload[$table] = DB::table($table)->get()->toArray();
            }

            // 2. Send to Server
            $response = Http::timeout(300)
                            ->withHeaders(['X-Sync-Token' => $token])
                            ->post($baseUrl . '/api/sync/full-push', $payload);

            if ($response->successful()) {
                $rawStats = $response->json('stats') ?? [];
                $summary = [];

                // Format keys to Headline
                foreach ($rawStats as $table => $stat) {
                    $summary[Str::headline($table)] = $stat;
                }

                return redirect()->back()->with('success', 'Berhasil: Kirim FULL DATABASE ke Server Selesai!')->with('sync_summary', $summary);
            } else {
                throw new \Exception('Gagal push: ' . Str::limit(strip_tags($response->body()), 150));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error Push Full: ' . $e->getMessage());
        }
    }
    /**
     * Smart One-Click Sync (Bi-Directional)
     */
    public function syncOneClick(Request $request)
    {
        $baseUrl = config('app.sync_base_url', env('SYNC_BASE_URL'));
        $token = config('app.sync_token', env('SYNC_TOKEN'));

        if (!$baseUrl || !$token) {
            return redirect()->back()->with('error', 'Gagal: URL Server atau Token belum dikonfigurasi!');
        }

        try {
            // 1. Gather ALL Local Data
            $tables = $this->getSyncableTables();
            $payload = [];

            foreach ($tables as $table) {
                $payload[$table] = DB::table($table)->get()->toArray();
            }

            // 2. Send to Server (Smart Sync Endpoint)
            $response = Http::timeout(600) // Longer timeout for processing comparisons
                            ->withHeaders(['X-Sync-Token' => $token])
                            ->post($baseUrl . '/api/sync/smart-sync', $payload);

            if ($response->successful()) {
                $data = $response->json('data') ?? []; // Rows to update locally
                $serverStats = $response->json('stats') ?? ['incoming' => [], 'outgoing' => []];

                // 3. Apply Updates from Server (Outgoing from Server -> Incoming to Client)
                DB::beginTransaction();
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                foreach ($data as $table => $rows) {
                    if (is_array($rows)) {
                        foreach ($rows as $row) {
                            try {
                                DB::table($table)->updateOrInsert(['id' => $row['id']], (array)$row);
                            } catch (\Illuminate\Database\QueryException $e) {
                                if ($e->getCode() === '23000') {
                                    continue; // Skip duplicate conflicts
                                }
                                // Log warning
                            } catch (\Exception $e) {
                                // General error
                            }
                        }
                    }
                }

                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                DB::commit();

                // Self-Heal: Prune any logical duplicate bills created by different instances
                \App\Keuangan\Services\BillService::removeDuplicates();
                \App\Models\PredikatNilai::removeDuplicates(); // Heal legacy duplicate predicates

                // 4. Prepare Summary for View
                $summary = [];
                $allTables = array_unique(array_merge(array_keys($serverStats['incoming']), array_keys($serverStats['outgoing'])));

                foreach ($allTables as $table) {
                    $sent = $serverStats['incoming'][$table] ?? 0; // Client -> Server (Accepted)
                    $received = $serverStats['outgoing'][$table] ?? 0; // Server -> Client (Received)

                    if ($sent > 0 || $received > 0) {
                        $summary[Str::headline($table)] = [
                            'sent' => $sent,
                            'received' => $received
                        ];
                    }
                }

            // 5. Push Photos (Auto-Run)
            $this->pushExpensePhotos($request);

            return redirect()->back()->with('success', 'Smart Sync Selesai! Pertukaran data & Foto berhasil.')->with('smart_sync_summary', $summary);

            } else {
                 throw new \Exception('Gagal Sync: ' . Str::limit(strip_tags($response->body()), 150));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error Smart Sync: ' . $e->getMessage());
        }
    }

    /**
     * Push Expense Photos (Local -> Server)
     * Called automatically after Sync
     */
    public function pushExpensePhotos(Request $request)
    {
        $baseUrl = config('app.sync_base_url', env('SYNC_BASE_URL'));
        $token = config('app.sync_token', env('SYNC_TOKEN'));

        if (!$baseUrl || !$token) return; // Silent fail if not configured

        try {
            // Get Expenses with Photos
            $pengeluaranWithFoto = DB::table('pengeluaran')->whereNotNull('bukti_foto')->get();
            $count = 0;
            $errors = 0;

            foreach ($pengeluaranWithFoto as $p) {
                $path = storage_path('app/public/' . $p->bukti_foto);
                if (file_exists($path)) {
                    $response = Http::withHeaders(['X-Sync-Token' => $token])
                        ->attach('proof', file_get_contents($path), basename($path))
                        ->post($baseUrl . '/api/sync/upload-proof', [
                            'path' => $p->bukti_foto
                        ]);

                    if ($response->successful()) {
                        $count++;
                    } else {
                        $errors++;
                        Log::warning("Failed to upload expense photo ID {$p->id}: " . $response->body());
                    }
                }
            }

            if ($count > 0) {
                return redirect()->back()->with('success', "Sync Foto Selesai: $count foto terupload ($errors gagal).");
            } else {
                return redirect()->back()->with('info', "Sync Foto: Tidak ada foto baru untuk diupload.");
            }

        } catch (\Exception $e) {
            Log::error("Error Push Photos: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error Sync Foto: ' . $e->getMessage());
        }
    }
}
