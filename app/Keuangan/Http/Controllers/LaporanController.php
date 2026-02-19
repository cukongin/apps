<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

use App\Keuangan\Models\Transaksi;
use App\Keuangan\Models\Pemasukan;
use App\Keuangan\Models\Pengeluaran;
use Carbon\Carbon;

class LaporanController extends \App\Http\Controllers\Controller
{
    protected $financialService;

    public function __construct(\App\Services\FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. PENDAPATAN SEKOLAH (SPP, Daftar Ulang, dll)
        // Group by Date + Category + Level
        $pendapatanSiswa = Transaksi::with(['tagihan.jenisBiaya', 'tagihan.siswa.kelas.level'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->get()
            ->groupBy(function($item) {
                 return $item->created_at->format('Y-m-d') . '|' . ($item->tagihan->jenisBiaya->nama ?? 'Pembayaran Siswa') . '|' . ($item->tagihan->siswa->kelas->level->nama ?? 'Umum');
            })
            ->map(function ($group) {
                $first = $group->first();
                $levelName = $first->tagihan->siswa->kelas->level->nama ?? 'Umum';
                $categoryName = $first->tagihan->jenisBiaya->nama ?? 'SPP';

                return [
                    'date' => $first->created_at,
                    'type' => 'in',
                    'category' => $categoryName,
                    'group_type' => 'pendapatan',
                    'description' => "Pemasukan " . $categoryName . " - " . $levelName . " (" . $group->count() . " Siswa)",
                    'amount' => $group->sum('jumlah_bayar'),
                    'raw_data' => null
                ];
            })->values();

        // 1.B SUBSIDI (Informational Only)
        $subsidiDetails = Transaksi::with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', 'Subsidi')
            ->orderBy('created_at')
            ->get();

        $totalSubsidi = $subsidiDetails->sum('jumlah_bayar');

        // 2. PEMASUKAN LAIN
        $pemasukanLain = Pemasukan::whereBetween('tanggal_pemasukan', [$startDate, $endDate])
            ->where('kategori', '!=', 'Pembayaran siswa')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->tanggal_pemasukan),
                    'type' => 'in',
                    'category' => $item->kategori ?? 'Pemasukan Lain',
                    'group_type' => 'pendapatan',
                    'description' => $item->sumber . ' - ' . $item->keterangan,
                    'amount' => $item->jumlah,
                    'raw_data' => $item
                ];
            });

        // 3. PENGELUARAN OPERASIONAL
        $pengeluaranOps = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->tanggal_pengeluaran),
                    'type' => 'out',
                    'category' => $item->kategori ?? 'Pengeluaran',
                    'group_type' => 'pengeluaran',
                    'description' => $item->judul . ' - ' . $item->deskripsi,
                    'amount' => $item->jumlah,
                    'evidence' => $item->bukti_foto, // Add photo path
                    'raw_data' => $item
                ];
            });

        // MERGE ALL
        $ledgerCollection = $pendapatanSiswa
            ->concat($pemasukanLain)
            ->concat($pengeluaranOps)
            ->sortBy(function($item) {
                return $item['date']->timestamp;
            });

        // CALCULATE SUMMARY
        $financialSummary = [
            'pendapatan_sekolah' => $pendapatanSiswa->sum('amount') + $pemasukanLain->sum('amount'),
            'pengeluaran_sekolah' => $pengeluaranOps->sum('amount'),
        ];

        $financialSummary['total_masuk'] = $financialSummary['pendapatan_sekolah'];
        $financialSummary['total_keluar'] = $financialSummary['pengeluaran_sekolah'];
        $financialSummary['saldo_net'] = $financialSummary['total_masuk'] - $financialSummary['total_keluar'];

        // Pagination
        $page = $request->input('page', 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $items = $ledgerCollection->slice($offset, $perPage)->values();

        $ledger = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $ledgerCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $groupedLedger = $ledgerCollection->groupBy('category');

        // CHART DATA
        $dailyData = $ledgerCollection->groupBy(function($item) {
            return $item['date']->format('Y-m-d');
        })->map(function($dayItems) {
            return [
                'in' => $dayItems->where('type', 'in')->sum('amount'),
                'out' => $dayItems->where('type', 'out')->sum('amount'),
            ];
        })->sortKeys();

        $chartData = [
            'labels' => $dailyData->keys()->map(fn($date) => Carbon::parse($date)->format('d M'))->values(),
            'income' => $dailyData->pluck('in')->values(),
            'expense' => $dailyData->pluck('out')->values(),
        ];

        // 5. PREPARE PRINT RECAP (Hierarchical as requested)
        $printIncome = collect();

        // A. Income Recap (Category + Level)
        // Request: "SPP TPQ 5 KELAS 105 SISWA 50000"
        $recapSiswa = Transaksi::with(['tagihan.jenisBiaya', 'tagihan.siswa.kelas.level'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->get()
            ->groupBy(function($item) {
                 $cat = $item->tagihan->jenisBiaya->nama ?? 'Pembayaran Siswa';
                 $lvl = $item->tagihan->siswa->kelas->level->nama ?? 'Umum';
                 return $cat . '|' . $lvl;
            })
            ->map(function ($group) {
                $first = $group->first();
                $cat = $first->tagihan->jenisBiaya->nama ?? 'Pembayaran Siswa';
                $lvl = $first->tagihan->siswa->kelas->level->nama ?? 'Umum';

                $studentCount = $group->pluck('tagihan.siswa.id')->unique()->count();
                $classCount = $group->pluck('tagihan.siswa.kelas.id')->unique()->count();

                // Format: "SPP TPQ (5 Kelas, 105 Siswa)"
                return [
                    'category' => $cat,
                    'description' => "$cat $lvl ($classCount Kelas, $studentCount Siswa)",
                    'amount' => $group->sum('jumlah_bayar')
                ];
            });

        // B. Other Income
        $recapLain = Pemasukan::whereBetween('tanggal_pemasukan', [$startDate, $endDate])
            ->where('kategori', '!=', 'Pembayaran siswa')
            ->get()
            ->groupBy(function($item) {
                return ($item->kategori ?? 'Lain-lain') . '|' . $item->keterangan;
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'category' => $first->kategori ?? 'Lain-lain',
                    'description' => "Pemasukan " . ($first->kategori ?? '') . " - " . $first->keterangan,
                    'amount' => $group->sum('jumlah')
                ];
            });

        $printIncome = $recapSiswa->concat($recapLain)->sortBy('category');

        // C. Expense Recap
        $printExpense = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                 return ($item->kategori ?? 'Umum') . '|' . $item->judul;
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'category' => $first->kategori ?? 'Pengeluaran',
                    'description' => "Pengeluaran " . ($first->kategori ?? '') . " - " . $first->judul,
                    'amount' => $group->sum('jumlah')
                ];
            })->sortBy('category');

        return view('keuangan.laporan.index', compact('ledger', 'groupedLedger', 'startDate', 'endDate', 'financialSummary', 'totalSubsidi', 'subsidiDetails', 'chartData', 'printIncome', 'printExpense'));
    }

    public function santri(Request $request) {
        // ... (Legacy Method) ...
        return $this->santriLegacy($request);
    }

    public function pengeluaran(Request $request) {
         // ... (Legacy Method) ...
         return $this->pengeluaranLegacy($request);
    }

    public function tunggakan(Request $request) {
         // ... (Legacy Method) ...
         return $this->tunggakanLegacy($request);
    }

    public function tahunan() {
        return view('keuangan.laporan.tahunan');
    }

    // --- Private Legacy Methods to keep Class Clean ---
    private function santriLegacy($request) {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Transaksi::with(['tagihan.siswa.kelas.level', 'tagihan.jenisBiaya'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $allTransaksis = (clone $query)->get();

        // Stats
        $summary = $allTransaksis->groupBy(function($item) { return $item->tagihan->jenisBiaya->nama ?? 'Lainnya'; })
            ->map(function($group) { return $group->sum('jumlah_bayar'); });
        $totalPemasukanSantri = $allTransaksis->sum('jumlah_bayar');
        $totalCash = $allTransaksis->where('metode_pembayaran', '!=', 'Subsidi')->sum('jumlah_bayar');
        $totalSubsidi = $allTransaksis->where('metode_pembayaran', 'Subsidi')->sum('jumlah_bayar');

        // Screen View Data (Paginated)
        $transaksis = $query->latest()->paginate(20)->withQueryString();
        $groupedTransaksis = $allTransaksis->sortBy(function($item) { return $item->tagihan->siswa->kelas->level->id ?? 999; })
            ->groupBy(function($item) { return $item->tagihan->siswa->kelas->level->nama ?? 'Lainnya'; });

        // PRINT RECAP (Hierarchical Summary - BKU Style)
        $printRecap = $allTransaksis->where('metode_pembayaran', '!=', 'Subsidi')
            ->groupBy(function($item) {
                 $cat = $item->tagihan->jenisBiaya->nama ?? 'Pembayaran Siswa';
                 $lvl = $item->tagihan->siswa->kelas->level->nama ?? 'Umum';
                 return $cat . '|' . $lvl;
            })
            ->map(function ($group) {
                $first = $group->first();
                $cat = $first->tagihan->jenisBiaya->nama ?? 'Pembayaran Siswa';
                $lvl = $first->tagihan->siswa->kelas->level->nama ?? 'Umum';

                $studentCount = $group->pluck('tagihan.siswa.id')->unique()->count();
                $classCount = $group->pluck('tagihan.siswa.kelas.id')->unique()->count();

                return [
                    'category' => $cat,
                    'level' => $lvl,
                    'description' => "$cat $lvl ($classCount Kelas, $studentCount Siswa)",
                    'amount' => $group->sum('jumlah_bayar')
                ];
            })->sortBy('category')->values();

        return view('keuangan.laporan.santri', compact('transaksis', 'groupedTransaksis', 'printRecap', 'startDate', 'endDate', 'summary', 'totalPemasukanSantri', 'totalCash', 'totalSubsidi'));
    }

    private function pengeluaranLegacy($request) {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
        $allPengeluarans = (clone $query)->get();

        // Stats
        $summary = $allPengeluarans->groupBy('kategori')->map(function($group) { return $group->sum('jumlah'); });
        $totalPengeluaran = $allPengeluarans->sum('jumlah');

        // Screen View
        $pengeluarans = $query->latest()->paginate(20)->withQueryString();
        $groupedPengeluarans = $allPengeluarans->sortByDesc('tanggal_pengeluaran')->groupBy('kategori');

        // PRINT RECAP (Accumulated by Category)
        $printRecap = $allPengeluarans->groupBy('kategori')
            ->map(function($items, $cat) {
                return [
                    'category' => $cat,
                    'count' => $items->count(),
                    'amount' => $items->sum('jumlah')
                ];
            })->sortByDesc('amount');

        return view('keuangan.laporan.pengeluaran', compact('pengeluarans', 'groupedPengeluarans', 'printRecap', 'startDate', 'endDate', 'summary', 'totalPengeluaran'));
    }

    private function tunggakanLegacy($request) {
         $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $kelasOptions = \App\Models\Kelas::where('id_tahun_ajaran', $activeYear->id ?? 0)->orderBy('nama_kelas')->get();
        $levels = ['TPQ', 'Ula', 'Wustho', 'Aliya'];
        // Use Service for complex logic
        $tagihans = $this->financialService->getArrearsReport(['kelas_id' => $request->kelas_id, 'tingkat' => $request->tingkat]);
        $totalTunggakan = $tagihans->sum(function($t) { return $t->jumlah - $t->terbayar; });
        $summary = $tagihans->groupBy(function($item) { return $item->jenisBiaya->nama ?? 'Lainnya'; })->map(function($group) { return $group->sum(function($t) { return $t->jumlah - $t->terbayar; }); });
        $classRecap = $tagihans->groupBy(function($item) {
            $siswa = $item->siswa; // Renamed from santri
            if (!$siswa) return 'Data Korup';
            $kelasObj = $siswa->kelas_saat_ini->kelas ?? null;
            return $kelasObj ? $kelasObj->nama : 'Tanpa Kelas Aktif';
        })->map(function($classBills, $className) {
            $students = $classBills->groupBy('siswa_id')->map(function($studentBills) {
                $student = $studentBills->first()->siswa;
                $billTotal = $studentBills->sum(function($t) { return $t->jumlah - $t->terbayar; });
                return ['id' => $student->id, 'nama' => $student->nama, 'nis' => $student->nis, 'total' => $billTotal, 'bills' => $studentBills];
            })->sortByDesc('total');
            return ['nama_kelas' => $className, 'total_tunggakan' => $students->sum('total'), 'student_count' => $students->count(), 'students' => $students];
        })->sortByDesc('total_tunggakan');

        // PRINT RECAP (Summary by Class/Level - Boss Style)
        $printRecap = $classRecap->map(function($class) {
            return [
                'category' => 'Kelas ' . $class['nama_kelas'],
                'count' => $class['student_count'] . ' Siswa',
                'amount' => $class['total_tunggakan']
            ];
        })->values();

        return view('keuangan.laporan.tunggakan', compact('classRecap', 'printRecap', 'summary', 'totalTunggakan', 'kelasOptions', 'levels'));
    }
}
