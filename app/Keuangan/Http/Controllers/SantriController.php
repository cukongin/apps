<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Siswa;
use App\Models\Kelas;

class SantriController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        // Eager load 'kelas_saat_ini.kelas.level' for the new accessor logic
        $query = Siswa::query()->with(['kelas_saat_ini.kelas.level', 'kelas.level']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('level_id')) {
            $query->whereHas('kelas_saat_ini.kelas', function($q) use ($request) {
                // Use 'id_jenjang' correct column for Academic Class
                $q->where('id_jenjang', $request->level_id);
            });
        }

        if ($request->filled('status')) {
            // Use 'status_siswa' (Academic Column) for filtering
            $query->where('status_siswa', $request->status);
        }

        if ($request->filled('kelas_id')) {
            if ($request->kelas_id == 'no_class') {
                $query->whereDoesntHave('kelas_saat_ini');
            } else {
                $query->whereHas('kelas_saat_ini', function($q) use ($request) {
                    $q->where('id_kelas', $request->kelas_id);
                });
            }
        }

        $santrisPaginator = $query->latest()->paginate(10)->withQueryString();
        $santris = collect($santrisPaginator->items())->map(function($s) {
            return [
                'id' => $s->id,
                'nis' => $s->nis,
                'nama' => $s->nama,
                'kelas' => $s->kelas->nama ?? '-',
                'level' => $s->kelas->level->nama ?? '-',
                'gender' => $s->gender,
                'status' => $s->status, // Accessor now returns status_siswa
                'initial' => strtoupper(substr($s->nama, 0, 2)),
                'color' => 'primary',
                'foto' => $s->foto
            ];
        });

        // Data for Filter and Counts
        // Use Academic 'Jenjang' to ensure data consistency
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $levels = \App\Models\Jenjang::with(['kelas' => function($q) use ($activeYear) {
            if ($activeYear) {
                $q->where('id_tahun_ajaran', $activeYear->id);
            }
        }])->get();
        $total_siswa = Siswa::count();
        $filtered_count = $santrisPaginator->total();

        return view('keuangan.santri.index', compact('santris', 'santrisPaginator', 'levels', 'total_siswa', 'filtered_count'));
    }

    public function show($id)
    {
        $siswa = Siswa::with(['kelas.level'])->findOrFail($id);

        // Auto-Generate Bills on Page Load
        $this->ensureBillsGenerated($siswa);

        // Reload tagihans after potential generation
        $siswa->load(['tagihans.jenisBiaya']);

        // Calculate Stats
        $total_tagihan = $siswa->tagihans->sum('jumlah');
        $total_terbayar = $siswa->tagihans->sum('terbayar');
        $sisa_tagihan = $total_tagihan - $total_terbayar;

        // Fetch Applicable Costs Logic (For Reference in View)
        $siswaClass = optional($siswa->kelas)->nama;
        $siswaLevel = optional(optional($siswa->kelas)->level)->nama;

        $biayaWajib = \App\Keuangan\Models\JenisBiaya::where('status', 'active')
            ->where(function($q) use ($siswaClass, $siswaLevel) {
                $q->where('target_type', 'all');

                if ($siswaClass) {
                    $q->orWhere(function($sub) use ($siswaClass) {
                        $sub->where('target_type', 'class')
                        ->where('target_value', 'like', '%"' . $siswaClass . '"%');
                    });
                }

                if ($siswaLevel) {
                    $q->orWhere(function($sub) use ($siswaLevel) {
                        $sub->where('target_type', 'level')
                        ->where('target_value', 'like', '%"' . $siswaLevel . '"%');
                    });
                }
            })->get();

        // Simple History (Last 10 Transactions)
        $recentTransactions = $siswa->transaksis()
            ->with(['tagihan.jenisBiaya'])
            ->latest()
            ->take(10)
            ->get();

        return view('keuangan.santri.keuangan.index', compact('siswa', 'total_tagihan', 'total_terbayar', 'sisa_tagihan', 'biayaWajib', 'recentTransactions') + ['santri' => $siswa]);
    }

    private function ensureBillsGenerated($siswa)
    {
        // Use syncForsiswa to ensure invalid/inactive bills are removed (cleaned up)
        \App\Keuangan\Services\BillService::syncForsiswa($siswa);
    }

    public function export()
    {
        return response()->streamDownload(function () {
            echo "NIS,Nama,Kelas,Level,Gender\n";
            $siswas = Siswa::with('kelas.level')->get();
            foreach($siswas as $s) {
                 $kelas = $s->kelas->nama ?? 'Belum Ada Kelas';
                 $level = $s->kelas->level->nama ?? '-';
                 echo "{$s->nis},{$s->nama},{$kelas},{$level},{$s->gender}\n";
            }
        }, 'data-siswa.csv');
    }
    public function generateBills($id)
    {
        $siswa = Siswa::findOrFail($id);
        \App\Keuangan\Services\BillService::syncForsiswa($siswa);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Tagihan berhasil digenerate ulang.']);
        }

        return back()->with('success', 'Tagihan berhasil digenerate ulang berdasarkan konfigurasi aktif.');
    } // Added missing brace

    public function destroyAllBills($id)
    {
        $siswa = Siswa::findOrFail($id);

        // Security: Hanya ambil tagihan yang BELUM DIBAYAR sedikitpun (terbayar <= 0 dan belum lunas)
        // Mencegah riwayat transaksi/refund tabungan terhapus begitu saja oleh Admin.
        $tagihanKosong = $siswa->tagihans()
            ->where('status', 'belum')
            ->where('terbayar', '<=', 0)
            ->get();

        $count = 0;
        foreach($tagihanKosong as $tagihan) {
            $tagihan->transaksis()->delete(); // Subsidi virtual kalau ada
            $tagihan->delete();
            $count++;
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Berhasil mereset $count tagihan kosong (Belum Lunas). Tagihan yang sudah ada riwayat bayar dibiarkan."]);
        }

        return back()->with('success', "Berhasil mereset $count tagihan kosong (Belum Lunas). Tagihan yang sudah ada riwayat bayar dibiarkan.");
    }
}
