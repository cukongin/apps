<?php

namespace App\Keuangan\Http\Controllers;

use App\Models\Siswa;
use App\Keuangan\Models\Tabungan;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabunganController extends \App\Http\Controllers\Controller
{
    // List of students to select for savings detail
    public function index(Request $request)
    {
        $query = Kelas::with(['siswas' => function($q) {
            $q->orderBy('nama_lengkap', 'asc');
        }]);

        if ($request->has('search_global') && $request->search_global != '') {
            $term = $request->search_global;
            $query->where(function($q) use ($term) {
                // Search by Class Name
                $q->where('nama_kelas', 'like', '%' . $term . '%')
                // OR Search by Student Name/NIS inside the class
                  ->orWhereHas('siswas', function($s) use ($term) {
                      $s->where('nama_lengkap', 'like', '%' . $term . '%')
                        ->orWhere('nis', 'like', '%' . $term . '%');
                  });
            });
        }

        $classes = $query->get()->map(function($kelas) use ($request) {
            // Filter students within class if searching specifically for student
            if ($request->has('search_global') && $request->search_global != '') {
                $term = $request->search_global;

                // If the CLASS name itself matches the search term, show ALL students in that class
                if (stripos($kelas->nama, $term) !== false) {
                     $kelas->filtered_santris = $kelas->siswas;
                } else {
                    // Otherwise, filter students by name/nis
                    $kelas->filtered_santris = $kelas->siswas->filter(function($participant) use ($term) {
                        return stripos($participant->nama, $term) !== false || stripos($participant->nis, $term) !== false;
                    });
                }
            } else {
                $kelas->filtered_santris = $kelas->siswas;
            }
            return $kelas;
        });

        // Auto-select logic similar to Payments
        $selectedClass = null;
        if ($request->has('class_id')) {
            $selectedClass = $classes->where('id', $request->class_id)->first();
        } elseif ($request->has('search_global') && $classes->count() == 1) {
            $selectedClass = $classes->first();
        }

        if ($request->ajax()) {
            return view('keuangan.tabungan.partials.student-list', compact('selectedClass'))->render();
        }

        return view('keuangan.tabungan.list', compact('classes', 'selectedClass'));
    }

    // Detail Savings History for a Student
    public function show($id)
    {
        $siswa = Siswa::with(['kelas', 'tabungans' => function($q) {
            $q->latest();
        }])->findOrFail($id);

        return view('keuangan.tabungan.index', compact('siswa') + ['santri' => $siswa]);
    }

    // Process Deposit/Withdrawal
    public function store(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|in:setor,tarik',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                // Lock row to prevent double-spend (race condition)
                $siswa = Siswa::where('id', $id)->lockForUpdate()->firstOrFail();
                $currentBalance = $siswa->saldo_tabungan;

                if ($request->tipe == 'tarik') {
                    if ($currentBalance < $request->jumlah) {
                        throw new \Exception('Saldo tidak mencukupi untuk penarikan.');
                    }
                    $saldoAkhir = $currentBalance - $request->jumlah;
                } else {
                    $saldoAkhir = $currentBalance + $request->jumlah;
                }

                // Create Transaction
                Tabungan::create([
                    'siswa_id' => $siswa->id,
                    'tipe' => $request->tipe,
                    'jumlah' => $request->jumlah,
                    'keterangan' => $request->keterangan ?? ($request->tipe == 'setor' ? 'Setoran Tunai' : 'Penarikan Tunai'),
                    'saldo_akhir' => $saldoAkhir
                ]);

                // Update Master Balance
                $siswa->update(['saldo_tabungan' => $saldoAkhir]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Transaksi berhasil diproses.');
    }
}

