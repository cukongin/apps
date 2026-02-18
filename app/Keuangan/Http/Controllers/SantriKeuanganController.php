<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class SantriKeuanganController extends \App\Http\Controllers\Controller
{
    public function index($id)
    {
        $siswa = \App\Models\Siswa::with(['kelas', 'tagihans', 'tabungans'])->findOrFail($id);

        // Sync Bills (Generate new, clear invalid)
        \App\Keuangan\Services\BillService::syncForsiswa($siswa);

        // Calculate financial stats
        $sisa_tagihan = $siswa->tagihans->where('status', '!=', 'lunas')->sum(function($t) {
            return $t->jumlah - $t->terbayar;
        });

        // Get applicable costs (Biaya Wajib) based on logic from TransaksiController/BillService
        $siswaClass = optional($siswa->kelas)->nama;
        $siswaLevel = optional(optional($siswa->kelas)->level)->nama;

        $biayaWajib = \App\Keuangan\Models\JenisBiaya::where('status', 'active')
            ->where(function($q) use ($siswaClass, $siswaLevel) {
                $q->where('target_type', 'all');

                if ($siswaClass) {
                    $q->orWhere(function($sub) use ($siswaClass) {
                        $sub->where('target_type', 'class')
                            ->where('target_value', 'like', '%' . $siswaClass . '%');
                    });
                }

                if ($siswaLevel) {
                    $q->orWhere(function($sub) use ($siswaLevel) {
                        $sub->where('target_type', 'level')
                            ->where('target_value', 'like', '%' . $siswaLevel . '%');
                    });
                }
            })->get();

        // Recent Transactions
        $recentTransactions = \App\Keuangan\Models\Transaksi::whereHas('tagihan', function($q) use ($id) {
            $q->where('siswa_id', $id);
        })->with(['tagihan.jenisBiaya'])
          ->latest()
          ->take(5)
          ->get();

        return view('keuangan.santri.keuangan.index', compact('siswa', 'sisa_tagihan', 'biayaWajib', 'recentTransactions') + ['santri' => $siswa]);
    }

    public function history($id)
    {
        $siswa = \App\Models\Siswa::with(['tabungans', 'transaksis.tagihan.jenisBiaya'])->findOrFail($id);

        // Map Transactions
        $transaksi_mapped = $siswa->transaksis->map(function ($item) {
            return [
                'type' => 'pembayaran',
                'date' => $item->created_at,
                'nominal' => $item->jumlah_bayar,
                'description' => 'Pembayaran ' . ($item->tagihan->jenisBiaya->nama ?? 'Tagihan'),
                'details' => $item->metode_pembayaran, // tunai/tabungan
                'status' => 'sukses', // Transaction is always success if recorded
                'reference' => $item->id
            ];
        });

        // Map Savings
        $tabungan_mapped = $siswa->tabungans->map(function ($item) {
            return [
                'type' => 'tabungan',
                'date' => $item->created_at,
                'nominal' => $item->jumlah,
                'description' => $item->tipe == 'setor' ? 'Tabungan Masuk' : 'Penarikan Tabungan',
                'details' => $item->tipe, // setor/tarik
                'status' => 'sukses',
                'reference' => $item->id
            ];
        });

        // Merge and Sort
        $history = $transaksi_mapped->merge($tabungan_mapped)->sortByDesc('date');

        return view('keuangan.santri.keuangan.history', compact('siswa', 'history'));
    }
}

