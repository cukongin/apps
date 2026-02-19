<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jenjang;
use App\Models\TahunAjaran;
use App\Models\Semester;
use RealRashid\SweetAlert\Facades\Alert;

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
            Alert::error('Gagal', 'URL Server atau Token belum dikonfigurasi di .env!');
            return redirect()->back();
        }

        try {
            // 1. Pull Master Data
            $response = Http::withHeaders(['X-Sync-Token' => $token])
                            ->get($baseUrl . '/api/sync/master-data');

            if ($response->failed()) {
                throw new \Exception('Gagal terhubung ke server: ' . $response->body());
            }

            $data = $response->json('data');

            DB::beginTransaction();

            // Sync Jenjang (Example)
            foreach ($data['jenjang'] as $item) {
                Jenjang::updateOrCreate(['id' => $item['id']], $item);
            }

            // Sync Tahun Ajaran
            foreach ($data['tahun_ajaran'] as $item) {
                TahunAjaran::updateOrCreate(['id' => $item['id']], $item);
            }

            // Sync Semester
             foreach ($data['semester'] as $item) {
                Semester::updateOrCreate(['id' => $item['id']], $item);
            }

            // Sync Guru (Match by NIP or Name if NIP null)
            foreach ($data['guru'] as $item) {
                Guru::updateOrCreate(['id' => $item['id']], $item);
            }

            // Sync Mapel
            foreach ($data['mapel'] as $item) {
                Mapel::updateOrCreate(['id' => $item['id']], $item);
            }

            // Sync Kelas
            foreach ($data['kelas'] as $item) {
                Kelas::updateOrCreate(['id' => $item['id']], $item);
            }

            // Sync Siswa
            foreach ($data['siswa'] as $item) {
                Siswa::updateOrCreate(['id' => $item['id']], $item);
            }

            DB::commit();

            // 2. Pull Academic Data (Optional: Separate button or same flow)
             $academicResponse = Http::withHeaders(['X-Sync-Token' => $token])
                            ->get($baseUrl . '/api/sync/academic-data');

            if ($academicResponse->successful()) {
                $acData = $academicResponse->json('data');

                // Sync Nilai (Direct DB Insert for performance/simplicity)
                // Note: We should truncate or be careful with duplicates.
                // For "Pull" strategy, usually we replace local data with server data for the active period.

                foreach ($acData['nilai'] as $item) {
                    DB::table('nilai')->updateOrInsert(
                        ['id' => $item['id']],
                        (array)$item
                    );
                }

                foreach ($acData['absensi'] as $item) {
                    DB::table('absensi')->updateOrInsert(
                        ['id' => $item['id']],
                        (array)$item
                    );
                }
            }

            // 3. Pull Finance Data (New Request)
            $financeResponse = Http::withHeaders(['X-Sync-Token' => $token])
                            ->get($baseUrl . '/api/sync/finance-data');

            if ($financeResponse->successful()) {
                $finData = $financeResponse->json('data');

                // Sync Master Finance
                foreach ($finData['pos_bayar'] as $item) DB::table('pos_bayar')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['jenis_bayar'] as $item) DB::table('jenis_bayar')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['kategori_pemasukan'] as $item) DB::table('kategori_pemasukan')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['kategori_pengeluaran'] as $item) DB::table('kategori_pengeluaran')->updateOrInsert(['id' => $item['id']], (array)$item);

                // Sync Transactions (Heavy Data)
                foreach ($finData['tagihan'] as $item) DB::table('tagihan')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['transaksi'] as $item) DB::table('transaksi')->updateOrInsert(['id' => $item['id']], (array)$item);

                foreach ($finData['pemasukan_lain'] as $item) DB::table('pemasukan_lain')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['pengeluaran_lain'] as $item) DB::table('pengeluaran_lain')->updateOrInsert(['id' => $item['id']], (array)$item);
                foreach ($finData['tabungan'] as $item) DB::table('tabungan')->updateOrInsert(['id' => $item['id']], (array)$item);
            }

            Alert::success('Berhasil', 'Sinkronisasi Lengkap (Akademik & Keuangan) Selesai!');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
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
            Alert::error('Gagal', 'URL Server atau Token belum dikonfigurasi!');
            return redirect()->back();
        }

        try {
            // Collect Local Finance Data to Push
            // We push everything, Server will handle deduplication (Upsert).
            // Warning: Huge payload if database is large. Ideally chunk it or filter by date.

            $payload = [
                'tagihan' => DB::table('tagihan')->get()->toArray(),
                'transaksi' => DB::table('transaksi')->get()->toArray(),
                'pemasukan_lain' => DB::table('pemasukan_lain')->get()->toArray(),
                'pengeluaran_lain' => DB::table('pengeluaran_lain')->get()->toArray(),
                'tabungan' => DB::table('tabungan')->get()->toArray(),
            ];

            // Send to Server
            $response = Http::withHeaders(['X-Sync-Token' => $token])
                            ->post($baseUrl . '/api/sync/finance-push', $payload);

            if ($response->successful()) {
                Alert::success('Berhasil', 'Data Keuangan Berhasil Dikirim ke Server!');
            } else {
                throw new \Exception('Gagal kirim: ' . $response->body());
            }

            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Error', 'Gagal Push: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
