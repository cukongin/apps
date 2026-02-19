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

            DB::commit();
            Log::info('Sync Master Data Completed Successfully');

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
                        (array)$item
                    );
                }

                foreach ($acData['absensi'] as $item) {
                    DB::table('catatan_kehadiran')->updateOrInsert(
                        ['id' => $item['id']],
                        (array)$item
                    );
                }

                if (isset($acData['catatan'])) {
                    foreach ($acData['catatan'] as $item) {
                        DB::table('catatan_wali_kelas')->updateOrInsert(
                            ['id' => $item['id']],
                            (array)$item
                        );
                    }
                }

                if (isset($acData['nilai_ekskul'])) {
                    foreach ($acData['nilai_ekskul'] as $item) {
                        DB::table('nilai_ekstrakurikuler')->updateOrInsert(
                            ['id' => $item['id']],
                            (array)$item
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
                    foreach ($finData['jenis_biaya'] as $item) DB::table('jenis_biayas')->updateOrInsert(['id' => $item['id']], (array)$item);
                }

                foreach ($finData['kategori_pemasukan'] as $item) DB::table('kategori_pemasukans')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['kategori_pengeluaran'] as $item) DB::table('kategori_pengeluarans')->updateOrInsert(['id' => $item['id']], (array)$item);

                // Sync Transactions (Heavy Data)
                foreach ($finData['tagihan'] as $item) DB::table('tagihans')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['transaksi'] as $item) DB::table('transaksis')->updateOrInsert(['id' => $item['id']], (array)$item);

                foreach ($finData['pemasukan_lain'] as $item) DB::table('pemasukans')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['pengeluaran_lain'] as $item) DB::table('pengeluarans')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['tabungan'] as $item) DB::table('tabungans')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

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
            Log::error('Sync Failed Global', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error: Terjadi kesalahan: ' . $e->getMessage());
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
}
