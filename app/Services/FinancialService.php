<?php

namespace App\Services;

use App\Keuangan\Models\Transaksi;
use App\Keuangan\Models\Pemasukan;
use App\Keuangan\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialService
{
    /**
     * Get Opening Balance (Saldo Awal) before a specific date
     */
    public function getOpeningBalance($date)
    {
        // 1. PENDAPATAN SISWA (Real Cash Only) before date
        $pendapatanSiswa = Transaksi::where('created_at', '<', $date . ' 00:00:00')
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->sum('jumlah_bayar');

        // 2. PEMASUKAN LAIN before date
        $pemasukanLain = Pemasukan::where('tanggal_pemasukan', '<', $date)
            ->sum('jumlah');

        // 3. PENGELUARAN before date
        $pengeluaran = Pengeluaran::where('tanggal_pengeluaran', '<', $date)
            ->sum('jumlah');

        return ($pendapatanSiswa + $pemasukanLain) - $pengeluaran;
    }

    /**
     * Get Complete Financial Summary for a period
     * Used in: LaporanController, DashboardController
     */
    public function getSummary($startDate, $endDate)
    {
        // 1. PENDAPATAN SISWA (Real Cash Only)
        $pendapatanSiswa = Transaksi::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->sum('jumlah_bayar');

        // 2. PEMASUKAN LAIN
        $pemasukanLain = Pemasukan::whereBetween('tanggal_pemasukan', [$startDate, $endDate])
            ->sum('jumlah');

        // 3. PENGELUARAN
        $pengeluaran = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->sum('jumlah');

        $totalMasuk = $pendapatanSiswa + $pemasukanLain;
        $totalKeluar = $pengeluaran;
        $saldo = $totalMasuk - $totalKeluar;

        return [
            'pendapatan_siswa_real' => $pendapatanSiswa, // Cash only
            'pemasukan_lain' => $pemasukanLain,
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'saldo_net' => $saldo
        ];
    }

    /**
     * Get Daily Chart Data
     */
    public function getChartData($startDate, $endDate)
    {
        // Aggregate Transactions (Date => Amount)
        $inSiswa = Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_bayar) as total')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->groupBy('date')
            ->pluck('total', 'date');

        $inLain = Pemasukan::selectRaw('tanggal_pemasukan as date, SUM(jumlah) as total')
            ->whereBetween('tanggal_pemasukan', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('total', 'date');

        $out = Pengeluaran::selectRaw('tanggal_pengeluaran as date, SUM(jumlah) as total')
            ->whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('total', 'date');

        // Merge Dates
        $allDates = $inSiswa->keys()
            ->merge($inLain->keys())
            ->merge($out->keys())
            ->unique()
            ->sort()
            ->values();

        // Build Data
        $dates = [];
        $incomeData = [];
        $expenseData = [];

        foreach ($allDates as $date) {
            $valSiswa = $inSiswa[$date] ?? 0;
            $valLain = $inLain[$date] ?? 0;
            $valOut = $out[$date] ?? 0;

            $dates[] = Carbon::parse($date)->format('d M');
            $incomeData[] = $valSiswa + $valLain;
            $expenseData[] = $valOut;
        }

        return [
            'labels' => $dates,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    /**
     * Get Total Income (All Time or Filtered)
     * Used for Current Balance Calculation
     */
    public static function getTotalIncome($startDate = null, $endDate = null)
    {
        $transaksiQuery = Transaksi::query();
        $pemasukanQuery = Pemasukan::query();

        if ($startDate && $endDate) {
            $transaksiQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $pemasukanQuery->whereBetween('tanggal_pemasukan', [$startDate, $endDate]);
        }

        // We use has('tagihan') to ensure data integrity, matching Dashboard logic
        $pendapatanSiswa = $transaksiQuery->has('tagihan')
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->sum('jumlah_bayar');

        $pemasukanLain = $pemasukanQuery->sum('jumlah');

        return $pendapatanSiswa + $pemasukanLain;
    }

    /**
     * Get Total Expense (All Time or Filtered)
     * Used for Current Balance Calculation
     */
    public static function getTotalExpense($startDate = null, $endDate = null)
    {
        $query = Pengeluaran::query();

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
        }

        return $query->sum('jumlah');
    }

    /**
     * Get Monthly Summary for Chart (Specific Year)
     * Optimized: Use Group By SQL (Matches Master)
     */
    public static function getMonthlySummary($year)
    {
        $incomeData = array_fill(1, 12, 0);
        $expenseData = array_fill(1, 12, 0);

        // 1. Transactions (SPP)
        $monthlySpp = Transaksi::has('tagihan')
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(jumlah_bayar) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // 2. Manual Income
        $monthlyIncome = Pemasukan::whereYear('tanggal_pemasukan', $year)
            ->selectRaw('MONTH(tanggal_pemasukan) as month, SUM(jumlah) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Merge Income Sources
        foreach ($monthlySpp as $month => $amount) {
            $incomeData[$month] += $amount;
        }
        foreach ($monthlyIncome as $month => $amount) {
            $incomeData[$month] += $amount;
        }

        // 3. Expenses
        $monthlyExpense = Pengeluaran::whereYear('tanggal_pengeluaran', $year)
            ->selectRaw('MONTH(tanggal_pengeluaran) as month, SUM(jumlah) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($monthlyExpense as $month => $amount) {
            $expenseData[$month] += $amount;
        }

        return [
            'income' => array_values($incomeData),
            'expense' => array_values($expenseData)
        ];
    }

    /**
     * Get Student Income (Real Cash) for Ledger
     */
    public function getStudentIncomeLedger($startDate, $endDate)
    {
        return Transaksi::with(['tagihan.jenisBiaya', 'tagihan.siswa.kelas.level'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', '!=', 'Subsidi')
            ->get();
    }

    /**
     * Get Subsidies for Ledger
     */
    public function getSubsidiesLedger($startDate, $endDate)
    {
        return Transaksi::with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', 'Subsidi')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get Other Income for Ledger
     */
    public function getOtherIncomeLedger($startDate, $endDate)
    {
        return Pemasukan::whereBetween('tanggal_pemasukan', [$startDate, $endDate])->get();
    }

    /**
     * Get Expenses for Ledger
     */
    public function getExpensesLedger($startDate, $endDate)
    {
        return Pengeluaran::whereBetween('tanggal_pengeluaran', [$startDate, $endDate])->get();
    }

    /**
     * Get Student Transaction Report (Detailed)
     */
    public function getStudentTransactionReport($startDate, $endDate)
    {
        return Transaksi::with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    /**
     * Get Expense Report (Detailed)
     */
    public function getExpenseReport($startDate, $endDate)
    {
        return Pengeluaran::with('details')->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
    }

    /**
     * Get Arrears Report Data
     */
    public function getArrearsReport($filters = [])
    {
        $query = \App\Keuangan\Models\Tagihan::with(['siswa.kelas_saat_ini.kelas', 'jenisBiaya', 'siswa.kelas', 'siswa.kategoriKeringanan.aturanDiskons'])
            ->where('status', '!=', 'lunas');

        // Apply Filters
        if (!empty($filters['kelas_id'])) {
            $query->whereHas('siswa.kelas_saat_ini', function($q) use ($filters) {
                $q->where('id_kelas', $filters['kelas_id']);
            });
        }

        if (!empty($filters['tingkat'])) {
            $query->whereHas('siswa.kelas_saat_ini.kelas.level', function($q) use ($filters) {
                $q->where('nama', $filters['tingkat']);
            });
        }

        return $query->get();
    }

    /**
     * Get Total Tunggakan (Simple)
     */
    public function getTotalTunggakan()
    {
        return \App\Keuangan\Models\Tagihan::where('status', '!=', 'lunas')
            ->sum(DB::raw('jumlah - terbayar'));
    }
    /**
     * Count Students with Arrears (Unique)
     */
    public function countStudentsWithArrears()
    {
        return \App\Keuangan\Models\Tagihan::where('status', '!=', 'lunas')
            ->distinct('siswa_id')
            ->count('siswa_id');
    }

    /**
     * Get Arrears Recap Per Class (Optimized SQL)
     */
    public function getArrearsRecapPerClass($yearId, $categoryId = null)
    {
        $query = DB::table('kelas')
            ->select(
                'kelas.id',
                'kelas.nama_kelas',
                'kelas.id_jenjang as level_id',
                DB::raw('COUNT(DISTINCT anggota_kelas.id_siswa) as total_students'),
                DB::raw('COUNT(DISTINCT tagihans.siswa_id) as students_with_arrears'),
                DB::raw('COALESCE(SUM(tagihans.jumlah - tagihans.terbayar), 0) as total_tunggakan')
            )
            ->leftJoin('anggota_kelas', function($join) {
                $join->on('kelas.id', '=', 'anggota_kelas.id_kelas')
                     ->where('anggota_kelas.status', '=', 'aktif');
            })
            ->leftJoin('siswa', function($join) {
                $join->on('anggota_kelas.id_siswa', '=', 'siswa.id')
                     ->where('siswa.status', '=', 'Aktif');
            })
            ->leftJoin('tagihans', function($join) use ($categoryId) {
                $join->on('siswa.id', '=', 'tagihans.siswa_id')
                     ->where('tagihans.status', '!=', 'lunas');

                if ($categoryId && $categoryId != 'all') {
                    $join->where('tagihans.jenis_biaya_id', '=', $categoryId);
                }
            })
            ->where('kelas.id_tahun_ajaran', $yearId)
            ->groupBy('kelas.id', 'kelas.nama_kelas', 'kelas.id_jenjang')
            ->orderBy('kelas.nama_kelas');

        return $query->get();
    }
}
