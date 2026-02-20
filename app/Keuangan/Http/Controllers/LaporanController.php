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
        // 1.B SUBSIDI (Informational Only)
        // Corrected Logic: Calculate Implicit Discount (Standard Fee - Billed Amount)
        // Because 'Subsidi' transactions might not exist in the database.
        $subsidiDetails = \App\Keuangan\Models\Tagihan::with(['siswa.kelas', 'jenisBiaya'])
            ->join('jenis_biayas', 'tagihans.jenis_biaya_id', '=', 'jenis_biayas.id')
            ->whereBetween('tagihans.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereRaw('tagihans.jumlah < jenis_biayas.jumlah')
            ->select('tagihans.*', \Illuminate\Support\Facades\DB::raw('(jenis_biayas.jumlah - tagihans.jumlah) as nilai_subsidi'))
            ->get();

        $totalSubsidi = $subsidiDetails->sum('nilai_subsidi');

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

    public function tahunan(Request $request) {
        $year = $request->input('year', date('Y'));

        // 1. Initialize Monthly Data (Jan-Dec)
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [
                'name' => \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM'),
                'spp' => 0,
                'tabungan' => 0,
                'lain' => 0,
                'masuk' => 0,
                'keluar' => 0,
                'saldo' => 0
            ];
        }

        // 2. Aggregate Transactions (SPP) - REAL CASH ONLY
        $transaksis = Transaksi::whereYear('created_at', $year)
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->selectRaw('MONTH(created_at) as month, SUM(jumlah_bayar) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // 3. Aggregate Tabungan (Savings)
        $tabungans = \App\Keuangan\Models\Tabungan::whereYear('created_at', $year)
            ->where('tipe', 'setor')
            ->selectRaw('MONTH(created_at) as month, SUM(jumlah) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // 4. Aggregate Pemasukan Lain
        $pemasukans = Pemasukan::whereYear('tanggal_pemasukan', $year)
            ->selectRaw('MONTH(tanggal_pemasukan) as month, SUM(jumlah) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // 5. Aggregate Pengeluaran
        $pengeluarans = Pengeluaran::whereYear('tanggal_pengeluaran', $year)
            ->selectRaw('MONTH(tanggal_pengeluaran) as month, SUM(jumlah) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // 6. Merge Data
        $totalPemasukanTahun = 0;
        $totalPengeluaranTahun = 0;

        foreach ($months as $m => $data) {
            $months[$m]['spp'] = $transaksis[$m] ?? 0;
            $months[$m]['tabungan'] = $tabungans[$m] ?? 0;
            $months[$m]['lain'] = $pemasukans[$m] ?? 0;

            $months[$m]['masuk'] = $months[$m]['spp'] + $months[$m]['tabungan'] + $months[$m]['lain'];
            $months[$m]['keluar'] = $pengeluarans[$m] ?? 0;
            $months[$m]['saldo'] = $months[$m]['masuk'] - $months[$m]['keluar'];

            $totalPemasukanTahun += $months[$m]['masuk'];
            $totalPengeluaranTahun += $months[$m]['keluar'];
        }

        // Summary (Todo: calculated carry forward if needed)
        $saldoAwal = 0;
        $saldoAkhir = $saldoAwal + $totalPemasukanTahun - $totalPengeluaranTahun;

        return view('keuangan.laporan.tahunan', compact('months', 'year', 'totalPemasukanTahun', 'totalPengeluaranTahun', 'saldoAwal', 'saldoAkhir'));
    }

    public function subsidi(Request $request) {
        $query = Transaksi::with(['tagihan.siswa.kelas.level', 'tagihan.jenisBiaya'])
            ->where('metode_pembayaran', 'Subsidi');

        // Filter by Date Range if needed
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Get Raw Data first to group by Student
        $transactions = $query->orderBy('created_at', 'desc')->get();

        // Group by Student
        $students = $transactions->groupBy(function($item) {
            return $item->tagihan->siswa_id;
        })->map(function($items) {
            $first = $items->first();
            return [
                'siswa' => $first->tagihan->siswa,
                'total_subsidi' => $items->sum('jumlah_bayar'),
                'history' => $items,
                'count' => $items->count()
            ];
        })->sortByDesc('total_subsidi');

        $totalSubsidiAll = $transactions->sum('jumlah_bayar');

        return view('keuangan.laporan.subsidi', compact('students', 'totalSubsidiAll'));
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
        $query = Transaksi::with(['tagihan.siswa.kelas.level', 'tagihan.jenisBiaya'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $realTransaksis = (clone $query)->get();

        // 2. Implicit Subsidies (Virtual Transactions)
        $implicitSubsidies = \App\Keuangan\Models\Tagihan::with(['siswa.kelas.level', 'jenisBiaya'])
            ->join('jenis_biayas', 'tagihans.jenis_biaya_id', '=', 'jenis_biayas.id')
            ->whereBetween('tagihans.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereRaw('tagihans.jumlah < jenis_biayas.jumlah')
            ->select('tagihans.*', \Illuminate\Support\Facades\DB::raw('(jenis_biayas.jumlah - tagihans.jumlah) as nilai_subsidi'))
            ->get()
            ->map(function($tagihan) {
                // Create a Virtual Transaction Object
                $t = new Transaksi();
                $t->id = 'sub_' . $tagihan->id; // Fake ID
                $t->tagihan_id = $tagihan->id;
                $t->jumlah_bayar = $tagihan->nilai_subsidi;
                $t->metode_pembayaran = 'Subsidi';
                $t->keterangan = 'Otomatis: Keringanan Biaya';
                $t->created_at = $tagihan->created_at;

                // Manually set relation to avoid needing to query again
                $t->setRelation('tagihan', $tagihan);

                return $t;
            });

        // 3. Merge & Sort
        $allTransaksis = $realTransaksis->concat($implicitSubsidies)->sortByDesc('created_at');

        // Stats
        $summary = $allTransaksis->groupBy(function($item) { return $item->tagihan->jenisBiaya->nama ?? 'Lainnya'; })
            ->map(function($group) { return $group->sum('jumlah_bayar'); });

        $totalCash = $allTransaksis->where('metode_pembayaran', '!=', 'Subsidi')->sum('jumlah_bayar');
        $totalSubsidi = $allTransaksis->where('metode_pembayaran', 'Subsidi')->sum('jumlah_bayar');
        $totalPemasukanSantri = $totalCash + $totalSubsidi;

        // Screen View Data (Manual Pagination)
        $page = $request->input('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $items = $allTransaksis->slice($offset, $perPage)->values();

        $transaksis = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allTransaksis->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
        // Use Service to get raw data
        $rawTagihans = $this->financialService->getArrearsReport(['kelas_id' => $request->kelas_id, 'tingkat' => $request->tingkat]);

        // Filter & Calculate Virtual Arrears (Live Discount Application)
        $tagihans = $rawTagihans->map(function($t) {
            $discountAmount = 0;

            // Check for Discount Rules
            if ($t->siswa && $t->siswa->kategoriKeringanan && $t->siswa->kategoriKeringanan->aturanDiskons) {
                // Find rule for this bill type
                $rule = $t->siswa->kategoriKeringanan->aturanDiskons->firstWhere('jenis_biaya_id', $t->jenis_biaya_id);

                if ($rule) {
                    if ($rule->tipe_diskon == 'percentage' || $rule->tipe_diskon == 'persen') {
                        $discountAmount = $t->jumlah * ($rule->jumlah / 100);
                    } elseif ($rule->tipe_diskon == 'nominal') {
                        $discountAmount = $rule->jumlah;
                    }
                }
            }

            // Implicit Discount Check (Standard - Billed)
            // Just in case rule is missing but bill was generated lower
            // Actually, we should take the MAX of intended discount vs implicit discount?
            // Let's stick to Rule-based first as it overrides everything for "Current Status".

            $effectiveBill = max(0, $t->jumlah - $discountAmount);
            $remaining = max(0, $effectiveBill - $t->terbayar);

            // Attach temporary attribute for View
            $t->sisa_tagihan_net = $remaining;
            $t->discount_applied = $discountAmount;

            return $t;
        })->filter(function($t) {
            // Only keep if there is remaining debt (after discount)
            return $t->sisa_tagihan_net > 0;
        });

        $totalTunggakan = $tagihans->sum('sisa_tagihan_net');

        $summary = $tagihans->groupBy(function($item) { return $item->jenisBiaya->nama ?? 'Lainnya'; })
            ->map(function($group) { return $group->sum('sisa_tagihan_net'); });

        $classRecap = $tagihans->groupBy(function($item) {
            $siswa = $item->siswa;
            if (!$siswa) return 'Data Korup';
            $kelasObj = $siswa->kelas_saat_ini->kelas ?? null;
            return $kelasObj ? $kelasObj->nama : 'Tanpa Kelas Aktif';
        })->map(function($classBills, $className) {
            $students = $classBills->groupBy('siswa_id')->map(function($studentBills) {
                $student = $studentBills->first()->siswa;
                $billTotal = $studentBills->sum('sisa_tagihan_net');
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
