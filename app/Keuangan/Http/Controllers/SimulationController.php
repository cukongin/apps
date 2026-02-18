<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;
use App\Keuangan\Services\BillService;
use Carbon\Carbon;

class SimulationController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return view('keuangan.simulation.index');
    }

    public function run(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020|max:2030',
        ]);

        $targetDate = Carbon::create($request->year, $request->month, 1, 0, 0, 0);

        try {
            BillService::generateForAll($targetDate->format('Y-m-d'));

            return back()->with('success', 'Simulasi Tagihan untuk periode ' . $targetDate->locale('id')->isoFormat('MMMM Y') . ' berhasil dijalankan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjalankan simulasi: ' . $e->getMessage());
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020|max:2030',
        ]);

        try {
            $deleted = \App\Keuangan\Models\Tagihan::whereMonth('created_at', $request->month)
                ->whereYear('created_at', $request->year)
                ->where('status', 'belum_lunas')
                ->where('terbayar', 0)
                ->whereHas('jenisBiaya', function($q) {
                    $q->where('tipe', 'bulanan');
                })
                ->delete();

            if ($deleted > 0) {
                 return back()->with('success', "Berhasil menghapus $deleted tagihan simulasi (belum lunas) untuk periode ini.");
            } else {
                 return back()->with('info', "Tidak ditemukan tagihan simulasi yang bisa dihapus untuk periode ini (mungkin sudah dibayar atau tidak ada).");
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus simulasi: ' . $e->getMessage());
        }
    }
}
