<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class LaporanHarianController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::now()->format('Y-m-d');

        // 1. Transaksi (SPP & Tagihan)
        $transaksi = \App\Keuangan\Models\Transaksi::with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])
            ->whereDate('created_at', $today)
            ->get();

        // 2. Tabungan Masuk
        $tabungan = \App\Keuangan\Models\Tabungan::with('siswa.kelas')
            ->where('tipe', 'setor')
            ->whereDate('created_at', $today)
            ->get();

        // 3. Pemasukan Lain
        $pemasukan = \App\Keuangan\Models\Pemasukan::whereDate('tanggal_pemasukan', $today)
            ->get();

        // Merge & Sort
        $itemTransaksi = collect();

        foreach($transaksi as $t) {
            $itemTransaksi->push([
                'jam' => $t->created_at->format('H:i'),
                'siswa' => $t->tagihan->siswa->nama . ' (' . ($t->tagihan->siswa->kelas->nama ?? '-') . ')',
                'keterangan' => $t->tagihan->jenisBiaya->nama,
                'metode' => $t->metode_pembayaran,
                'nominal' => $t->jumlah_bayar,
                'tipe' => 'SPP'
            ]);
        }

        foreach($tabungan as $t) {
            $itemTransaksi->push([
                'jam' => $t->created_at->format('H:i'),
                'siswa' => $t->siswa->nama . ' (' . ($t->siswa->kelas->nama ?? '-') . ')',
                'keterangan' => 'Tabungan Harian',
                'metode' => 'Cash', // Tabungan usually cash
                'nominal' => $t->jumlah,
                'tipe' => 'Tabungan'
            ]);
        }

        foreach($pemasukan as $p) {
            $itemTransaksi->push([
                'jam' => \Carbon\Carbon::parse($p->created_at)->format('H:i'),
                'siswa' => '-',
                'keterangan' => $p->sumber . ' - ' . $p->keterangan,
                'metode' => 'Cash',
                'nominal' => $p->jumlah,
                'tipe' => 'Lainnya'
            ]);
        }

        $sortedTransaksi = $itemTransaksi->sortBy('jam');

        // Summary
        // Summary
        // FIX: Exclude 'Subsidi' from Real Cash calculations
        $realTransactions = $sortedTransaksi->where('metode', '!=', 'Subsidi');

        $totalMasuk = $realTransactions->sum('nominal');
        $totalCash = $realTransactions->where('metode', '!=', 'Transfer')->sum('nominal');
        $totalTransfer = $realTransactions->where('metode', 'Transfer')->sum('nominal');

        // Optional: Calculate Subsidy Total separately if needed for display
        $totalSubsidi = $sortedTransaksi->where('metode', 'Subsidi')->sum('nominal');

        return view('keuangan.laporan.harian', compact(
            'sortedTransaksi',
            'totalMasuk',
            'totalCash',
            'totalTransfer',
            'totalSubsidi'
        ));
    }
}

