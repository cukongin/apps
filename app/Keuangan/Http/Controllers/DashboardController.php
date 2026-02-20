<?php

namespace App\Keuangan\Http\Controllers;

use App\Keuangan\Models\Transaksi;
use App\Keuangan\Models\Pengeluaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added this line


use App\Keuangan\Models\Tabungan; // Added import

class DashboardController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role;
        $filterYear = $request->input('filter_year', date('Y'));
        $availableYears = range(date('Y') - 2, date('Y'));

        // Default Values (Zero/Empty)
        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        $saldoSaatIni = 0;
        $totalTabungan = 0;
        $recentTransactions = collect([]); // Empty collection for SPP
        $recentTabungan = collect([]); // Empty collection for Savings
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'income' => array_fill(0, 12, 0),
            'expense' => array_fill(0, 12, 0)
        ];

        // LOGIC FOR BENDAHARA / ADMIN / KEPALA (SPP & Keuangan Focus)
        if (in_array($role, ['admin_utama', 'bendahara', 'staf_keuangan', 'staf_administrasi', 'kepala_madrasah'])) {
            // CENTRALIZED CALCULATION USING FINANCIAL SERVICE
            // This ensures data matches exactly with Laporan/Reports

            // 1. Total Pemasukan
            $totalPemasukan = \App\Services\FinancialService::getTotalIncome();

            // 2. Total Pengeluaran
            $totalPengeluaran = \App\Services\FinancialService::getTotalExpense();

            // 3. Saldo Awal (Real Time)
            $saldoSaatIni = $totalPemasukan - $totalPengeluaran;

            // 5. Recent Transactions
            $recentTransactions = Transaksi::with(['tagihan.siswa', 'tagihan.jenisBiaya'])
                ->latest()
                ->take(5)
                ->get();

            // 6. Chart Data
            $chartSummary = \App\Services\FinancialService::getMonthlySummary($filterYear);
            $chartData['income'] = $chartSummary['income'];
            $chartData['expense'] = $chartSummary['expense'];

            // 7. Additional Stats (For Fancy Dashboard)
            // Diskon: Calculate difference between Standard Fee (JenisBiaya) and Billed Amount (Tagihan)
            $totalDiskon = \App\Keuangan\Models\Tagihan::join('jenis_biayas', 'tagihans.jenis_biaya_id', '=', 'jenis_biayas.id')
                ->whereRaw('tagihans.jumlah < jenis_biayas.jumlah')
                ->sum(\Illuminate\Support\Facades\DB::raw('jenis_biayas.jumlah - tagihans.jumlah'));

            $totalPemasukanLain = \App\Keuangan\Models\Pemasukan::sum('jumlah');

            // 8. Pemasukan SPP Only (for Display)
            // $totalPemasukan contains BOTH SPP and Lain.
            $pemasukanSPP = $totalPemasukan - $totalPemasukanLain;
        }

        // LOGIC FOR TELLER / ADMIN (Tabungan Focus)
        // Note: Admin gets BOTH
        if (in_array($role, ['admin_utama', 'teller_tabungan'])) {
            // 4. Total Tabungan siswa (Liability)
            $totalTabungan = Siswa::sum('saldo_tabungan');

            // 4b. Total Masuk & Keluar (Flow)
            // Use correct namespace for Tabungan
            $totalTabunganMasuk = Tabungan::where('tipe', 'setor')->sum('jumlah');
            $totalTabunganKeluar = Tabungan::where('tipe', 'tarik')->sum('jumlah');

            $recentTabungan = Tabungan::with('siswa')
                ->latest()
                ->take(5)
                ->get();
        } else {
             $totalTabunganMasuk = 0;
             $totalTabunganKeluar = 0;
        }

        return view('keuangan.dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldoSaatIni',
            'totalTabungan',
            'totalTabunganMasuk',
            'totalTabunganKeluar',
            'recentTransactions',
            'recentTabungan',
            'chartData',
            'filterYear',
            'availableYears',
            'totalDiskon',
            'totalPemasukanLain',
            'pemasukanSPP'
        ));
    }
}
