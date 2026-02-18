<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends \App\Http\Controllers\Controller
{
    protected $financialService;

    public function __construct(\App\Services\FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        // Ensure all bills are generated for accurate stats
        // Now safe to run because BillService::syncForsiswa checks for existence.
        \App\Keuangan\Services\BillService::generateForAll();

        // Ensure data is clean (Fix Duplicates) - Optional, can be removed if slow
        \App\Keuangan\Services\BillService::removeDuplicates();

        // Data for Filters
        $levels = \App\Keuangan\Models\Level::all();
        $categories = \App\Keuangan\Models\JenisBiaya::where('status', 'active')->get();

        // 0. CHECK SEARCH MODE
        if ($request->has('search_global') && strlen($request->search_global) > 1) {
            $term = $request->search_global;

            // Direct Student Query
            $siswas = \App\Models\Siswa::with(['kelas_saat_ini.kelas', 'tagihans.jenisBiaya'])
                ->where('nama_lengkap', 'like', '%' . $term . '%')
                ->orWhere('nis_lokal', 'like', '%' . $term . '%') // Using nis_lokal based on Accessor
                ->get()
                ->map(function($siswa) {
                    // Calculate Arrears
                    $siswa->total_tunggakan = $siswa->tagihans->where('status', '!=', 'lunas')->sum(function($t) {
                        return $t->jumlah - $t->terbayar;
                    });
                    return $siswa;
                })
                ->sortByDesc('total_tunggakan');

            // Quick Filters on Search Result (Optional)
            if ($request->has('level_id') && $request->level_id != 'all') {
                // Filter by Level logic... omitted for speed unless requested
            }

            // Return View with Search Results
            if ($request->ajax()) {
                return view('keuangan.pembayaran.partials.student-list', [
                    'siswas' => $siswas,
                    'isSearch' => true,
                    'selectedClass' => null
                ])->render();
            }

            // Global Stats for View (Using Service to prevent duplicates)
            $globalTotalTunggakan = $this->financialService->getTotalTunggakan();
            $globalStudentsWithArrears = $this->financialService->countStudentsWithArrears();

            return view('keuangan.pembayaran.index', compact('siswas', 'globalTotalTunggakan', 'globalStudentsWithArrears', 'levels', 'categories'))
                ->with('isSearchMode', true)
                ->with('searchQuery', $term);
        }

        // --- NORMAL CLASS VIEW MODE ---

        // --- NORMAL CLASS VIEW MODE ---

        // 1. Optimized Class Recap (DB Aggregation)
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $categoryId = ($request->has('category_id') && $request->category_id != 'all') ? $request->category_id : null;

        // Fetch raw data via Service
        $recap = $this->financialService->getArrearsRecapPerClass($activeYear->id ?? 0, $categoryId);

        // Filter by Level
        if ($request->has('level_id') && $request->level_id != 'all') {
            $recap = $recap->where('level_id', $request->level_id);
        }

        // Transform and Calculate Pcentages
        $classes = $recap->map(function($row) {
             $row->nama = $row->nama_kelas; // Alias for View compatibility

             $paidCount = $row->total_students - $row->students_with_arrears;
             $row->paid_percentage = $row->total_students > 0 ? round(($paidCount / $row->total_students) * 100) : 100;

             return $row;
        });

        // Sorting Logic
        $sortBy = $request->sort_by ?? 'total_tunggakan';
        $sortOrder = $request->sort_order ?? 'desc';

        if ($sortOrder == 'asc') {
            $classes = $classes->sortBy($sortBy);
        } else {
            $classes = $classes->sortByDesc($sortBy);
        }

        // 2. Fetch Selected Class Students if ID present (Lazy Loading)
        $selectedClass = null;
        $siswas = collect([]);

        if ($request->has('class_id')) {
            // Find in recap to get basic stats
            $selectedClass = $classes->where('id', $request->class_id)->first();

            if ($selectedClass) {
                // Fetch Detailed Students for this class ONLY
                $fullClass = \App\Models\Kelas::where('id', $request->class_id)
                    ->with(['anggota_kelas' => function($q) {
                        $q->where('status', 'aktif')->with(['siswa.tagihans']);
                    }])
                    ->first();

                if ($fullClass) {
                    $siswas = $fullClass->anggota_kelas->map(function($anggota) use ($request) {
                        $siswa = $anggota->siswa;
                        if (!$siswa) return null;

                        $arrearsQuery = $siswa->tagihans->where('status', '!=', 'lunas');
                        if ($request->has('category_id') && $request->category_id != 'all') {
                            $arrearsQuery = $arrearsQuery->where('jenis_biaya_id', $request->category_id);
                        }
                        $siswa->total_tunggakan = $arrearsQuery->sum(function ($t) { return $t->jumlah - $t->terbayar; });
                        return $siswa;
                    })->filter()->sortByDesc('total_tunggakan')->values();
                }
            }
        }

        // Global Stats (Calculated from filtered classes)
        $globalTotalTunggakan = $classes->sum('total_tunggakan');
        $globalStudentsWithArrears = $classes->sum('students_with_arrears');

        if ($request->ajax() && $request->has('class_id')) {
            return view('keuangan.pembayaran.partials.student-list', compact('selectedClass', 'siswas'))->render();
        }

        return view('keuangan.pembayaran.index', compact('classes', 'selectedClass', 'siswas', 'globalTotalTunggakan', 'globalStudentsWithArrears', 'levels', 'categories'));
    }

    public function create($id)
    {
        $santri = \App\Models\Siswa::with(['kelas', 'tagihans.jenisBiaya'])->findOrFail($id);

        $recentTransactions = \App\Keuangan\Models\Transaksi::whereHas('tagihan', function($q) use ($id) {
            $q->where('siswa_id', $id);
        })->with(['tagihan.jenisBiaya'])
          ->latest()
          ->take(10)
          ->get();

        if (request()->ajax() || request('mode') == 'modal') {
            return view('keuangan.transaksi.partials.form', compact('santri', 'recentTransactions'));
        }

        return view('keuangan.transaksi.create', compact('santri', 'recentTransactions'));
    }

    public function edit($id)
    {
        $transaksi = \App\Keuangan\Models\Transaksi::with(['tagihan.jenisBiaya', 'tagihan.siswa'])->findOrFail($id);
        return view('keuangan.transaksi.edit', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $transaksi = \App\Keuangan\Models\Transaksi::with('tagihan')->findOrFail($id);
        $redirectId = $transaksi->tagihan->siswa_id;

        try {
            \DB::transaction(function () use ($request, $transaksi) {
                $tagihan = $transaksi->tagihan;

                // Revert old payment from tagihan
                $tagihan->terbayar -= $transaksi->jumlah_bayar;

                // Validate new amount
                $maxPayable = $tagihan->jumlah - $tagihan->terbayar;

                // Allow small epsilon diff for float? Using integer for rupiah is safer.
                // Assuming integer DB columns for amounts.
                if ($request->jumlah_bayar > $maxPayable) {
                     throw new \Exception('Jumlah pembayaran melebihi sisa tagihan saat ini (Max: Rp ' . number_format($maxPayable, 0, ',', '.') . ')');
                }

                // Apply new payment
                $tagihan->terbayar += $request->jumlah_bayar;

                \App\Keuangan\Services\BillService::updateStatus($tagihan);

                // Update Transaction
                $transaksi->update([
                    'jumlah_bayar' => $request->jumlah_bayar,
                    'keterangan' => $request->keterangan,
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('pembayaran.create', $redirectId)->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $transaksi = \App\Keuangan\Models\Transaksi::with('tagihan')->findOrFail($id);
        $redirectId = $transaksi->tagihan->siswa_id;

        try {
            \DB::transaction(function () use ($transaksi) {
                $tagihan = $transaksi->tagihan;

                // Revert payment
                $tagihan->terbayar -= $transaksi->jumlah_bayar;

                \App\Keuangan\Services\BillService::updateStatus($tagihan);
                $transaksi->delete();
            });

            // Force clear view cache to ensure Dashboard and Recap update immediately (Fix for Hostinger)
            \Illuminate\Support\Facades\Artisan::call('view:clear');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('pembayaran.create', $redirectId)->with('success', 'Pembayaran berhasil dihapus.');
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'bills' => 'required|array', // Array of {tagihan_id => nominal}
            'metode' => 'required|in:tunai,tabungan',
        ]);

        $siswa = \App\Models\Siswa::findOrFail($id);
        $totalBayarNeeded = 0;
        $billsToProcess = [];

        // 1. Pre-calculate total and validate inputs
        $selectedBillIds = $request->input('tagihan_id', []);

        foreach ($request->bills as $tagihanId => $nominal) {
            // STRICT FILTER: Only process bills that are checked
            if (!in_array($tagihanId, $selectedBillIds)) {
                continue;
            }

            $nominal = (int) str_replace(['Rp ', '.', ','], '', $nominal);
            if ($nominal > 0) {
                $tagihan = \App\Keuangan\Models\Tagihan::findOrFail($tagihanId);
                $sisa = $tagihan->jumlah - $tagihan->terbayar;

                if ($nominal > $sisa) {
                    return back()->with('error', 'Pembayaran melebihi sisa tagihan untuk ' . $tagihan->jenisBiaya->nama);
                }

                $totalBayarNeeded += $nominal;
                $billsToProcess[] = [
                    'tagihan' => $tagihan,
                    'nominal' => $nominal
                ];
            }
        }

        if ($totalBayarNeeded == 0) {
            return back()->with('error', 'Belum ada tagihan yang dipilih atau nominal 0.');
        }

        // 2. Additional validation for Tabungan
        if ($request->metode == 'tabungan') {
            if ($siswa->saldo_tabungan < $totalBayarNeeded) {
                return back()->with('error', 'Saldo tabungan tidak mencukupi. Total Tagihan: Rp ' . number_format($totalBayarNeeded,0,',','.') . ', Saldo: Rp ' . number_format($siswa->saldo_tabungan,0,',','.'));
            }
        }

        // Initialize variable to capture ID from closure
        $lastTransaksiId = null;

        try {
            \DB::transaction(function () use ($request, $siswa, $billsToProcess, $totalBayarNeeded, &$lastTransaksiId) {

                // 3. Process Deduction if Tabungan
                if ($request->metode == 'tabungan') {
                    $saldoAkhir = $siswa->saldo_tabungan - $totalBayarNeeded;

                    // Create History in Tabungan
                    \App\Keuangan\Models\Tabungan::create([
                        'siswa_id' => $siswa->id,
                        'tipe' => 'tarik',
                        'jumlah' => $totalBayarNeeded,
                        'keterangan' => 'Pembayaran Tagihan (Otomatis)',
                        'saldo_akhir' => $saldoAkhir
                    ]);

                    // Update Master Balance
                    $siswa->update(['saldo_tabungan' => $saldoAkhir]);
                }

                // 4. Process Each Bill Payment
                foreach ($billsToProcess as $item) {
                    $tagihan = $item['tagihan'];
                    $nominal = $item['nominal'];

                    // Determine details for Description
                    $detailInfo = '';
                    if ($tagihan->jenisBiaya->tipe == 'bulanan') {
                        // Use created_at as proxy for month, or parse from keterangan
                        $detailInfo = ' (' . $tagihan->created_at->locale('id')->isoFormat('MMMM Y') . ')';
                    }

                    // Create Transaction
                    $transaksi = \App\Keuangan\Models\Transaksi::create([
                        'tagihan_id' => $tagihan->id,
                        'jumlah_bayar' => $nominal,
                        'metode_pembayaran' => $request->metode,
                        'keterangan' => 'Pembayaran ' . ucfirst($request->metode) . $detailInfo . ($request->metode == 'tabungan' ? ' (Potong Saldo)' : '')
                    ]);

                    // Capture ID
                    $lastTransaksiId = $transaksi->id;

                    // Update Tagihan Status
                    $tagihan->terbayar += $nominal;
                    \App\Keuangan\Services\BillService::updateStatus($tagihan);
                }

                // 5. Create Pemasukan (Income) Record - Aggregated
                if ($totalBayarNeeded > 0) {
                    // Generate Description
                    $descriptionParts = [];
                    $groupedTypes = [];

                    foreach ($billsToProcess as $item) {
                        $tagihan = $item['tagihan'];
                        $typeName = $tagihan->jenisBiaya->nama;

                        $monthInfo = '';
                        if ($tagihan->jenisBiaya->tipe == 'bulanan') {
                             $monthInfo = $tagihan->created_at->locale('id')->isoFormat('MMMM');
                        }

                        if (!isset($groupedTypes[$typeName])) {
                            $groupedTypes[$typeName] = [];
                        }
                        // Add month info if valid, otherwise empty string to just count
                        $groupedTypes[$typeName][] = $monthInfo;
                    }

                    foreach ($groupedTypes as $name => $details) {
                        // Filter out empty strings (non-monthly items)
                        $months = array_filter($details);

                        if (count($months) > 0) {
                            // Unique months just in case
                            // $months = array_unique($months); // Keep duplicates if paying multiple years? unlikely for same month name.
                            // e.g. SPP (Januari, Februari)
                            $descriptionParts[] = "$name (" . implode(', ', $months) . ")";
                        } else {
                            // Non-monthly items
                            $count = count($details);
                            if ($count > 1) {
                                $descriptionParts[] = "$name ($count Item)";
                            } else {
                                $descriptionParts[] = $name;
                            }
                        }
                    }

                    $incomeTitle = 'Pembayaran siswa: ' . implode(', ', $descriptionParts);
                    $incomeDesc = "Diterima dari: {$siswa->nama} ({$siswa->nis})\nMetode: " . ucfirst($request->metode);

                    // Determine Category ID (Default to 'Lainnya' if not match, usually SPP -> Operasional or specific)
                    // simplified: just string for now as per Pemasukan model, or ID if relation exists.
                    // Checking Pemasukan Migration: 'kategori' is string.

                    \App\Keuangan\Models\Pemasukan::create([
                        'user_id' => auth()->id(),
                        'sumber' => $siswa->nama, // Or 'Wali siswa'
                        'jumlah' => $totalBayarNeeded,
                        'kategori' => 'Pembayaran siswa', // You might want to make this dynamic or config
                        'keterangan' => $incomeDesc . "\nRincian: $incomeTitle",
                        'tanggal_pemasukan' => now(), // Or request date
                    ]);
                }
            });

            // 5. Send WhatsApp Notification (After Transaction Commit)
            // Use no_hp_wali (Parent) or no_hp (Student)
            $targetPhone = $siswa->no_hp_wali ?? ($siswa->no_hp ?? null);

            if ($targetPhone) {
                try {
                    // Template Processing
                    $template = \App\Models\Setting::get('wa_payment_template', "*PEMBAYARAN DITERIMA* 💰\n\nTerima kasih, pembayaran SPP/Biaya a.n. *{nama}* telah kami terima.\n\n📅 Tanggal: {tanggal}\n💵 Nominal: Rp {nominal}\n💳 Metode: {metode}\n\nRincian:{rincian}\n\n_Pesan ini dikirim otomatis oleh Sistem Keuangan Sekolah._");
                    $rincianText = "";
                    foreach ($billsToProcess as $item) {
                        $tagihan = $item['tagihan'];
                        $detail = "";
                        if ($tagihan->jenisBiaya->tipe == 'bulanan') {
                            $detail = " (" . $tagihan->created_at->locale('id')->isoFormat('MMMM') . ")";
                        }
                        $rincianText .= "\n- " . $tagihan->jenisBiaya->nama . $detail . " (Rp " . number_format($item['nominal'], 0, ',', '.') . ")";
                    }
                    $message = str_replace(
                        ['{nama}', '{nominal}', '{tanggal}', '{metode}', '{rincian}'],
                        [$siswa->nama, number_format($totalBayarNeeded, 0, ',', '.'), date('d-m-Y H:i'), ucfirst($request->metode), $rincianText],
                        $template
                    );

                    // Check Mode
                    $waMode = \App\Models\Setting::get('wa_mode', 'api');

                    if ($waMode === 'api') {
                        // FIRE AND FORGET (Server-side)
                        \App\Services\WhatsAppService::send($targetPhone, $message);
                    } else {
                        // PREPARE FOR REDIRECT (Client-side)
                        session()->flash('wa_target', $targetPhone);
                        session()->flash('wa_message', $message);
                    }

                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to process WA: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Handle AJAX / JSON Response for Modal
        if ($request->ajax() || $request->wantsJson()) {
            $response = [
                'success' => true,
                'message' => 'Pembayaran berhasil diproses.',
                'siswa_id' => $siswa->id,
                'class_id' => $siswa->kelas_id,
                'transaksi_id' => $lastTransaksiId
            ];

            // If Session Flash doesn't persist well in AJAX, send explicitly
            if (session()->has('wa_target')) {
                $response['wa_target'] = session('wa_target');
                $response['wa_message'] = session('wa_message');
            }

            return response()->json($response);
        }

        if ($request->redirect_to == 'index') {
            return redirect()->route('pembayaran.index', ['class_id' => $siswa->kelas_id])->with('success', 'Pembayaran berhasil diproses.');
        }

        return redirect()->route('pembayaran.create', $id)->with('success', 'Pembayaran berhasil diproses.');
    } // Added missing brace

    public function history(Request $request)
    {
        $query = \App\Keuangan\Models\Transaksi::with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])
            ->latest();

        // Date Filter
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->has('month') && $request->has('year')) {
             $query->whereMonth('created_at', $request->month)
                   ->whereYear('created_at', $request->year);
        }

        // Search Filter (siswa Name or Transaction ID)
        if ($request->has('search') && $request->search != '') {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                // Assuming no direct transaction code column, using ID or siswa Name
                $q->where('id', $term)
                  ->orWhereHas('tagihan.siswa', function($s) use ($term) {
                      $s->where('nama', 'like', '%' . $term . '%');
                  });
            });
        }

        $transaksis = $query->paginate(20)->withQueryString();

        return view('keuangan.transaksi.history', compact('transaksis'));
    }

    public function printReceipt($id)
    {
        // 1. Find the requested transaction
        $primary = \App\Keuangan\Models\Transaksi::with(['tagihan.siswa'])->findOrFail($id);

        // 2. Find siblings (Same siswa, Same Timestamp)
        // Created within the same bulk action usually share exact timestamp.
        $transaksiCollection = \App\Keuangan\Models\Transaksi::whereHas('tagihan', function($q) use ($primary) {
                $q->where('siswa_id', $primary->tagihan->siswa_id);
            })
            ->where('created_at', $primary->created_at) // Exact timestamp match
            ->with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])
            ->get();

        // Safety: If collection empty (should not happen if primary exists), use primary
        if ($transaksiCollection->isEmpty()) {
            $transaksiCollection = collect([$primary]);
        }

        return view('keuangan.spp.receipt', compact('transaksiCollection'));
    }

    public function printThermal($id)
    {
        // 1. Find the requested transaction
        $primary = \App\Keuangan\Models\Transaksi::with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])->findOrFail($id);

        // 2. Find siblings (Same siswa, Same Timestamp)
        $transaksiCollection = \App\Keuangan\Models\Transaksi::whereHas('tagihan', function($q) use ($primary) {
                $q->where('siswa_id', $primary->tagihan->siswa_id);
            })
            ->where('created_at', $primary->created_at) // Exact timestamp match
            ->with(['tagihan.siswa.kelas', 'tagihan.jenisBiaya'])
            ->get();

        if ($transaksiCollection->isEmpty()) {
            $transaksiCollection = collect([$primary]);
        }

        return view('keuangan.transaksi.print_thermal', compact('transaksiCollection'));
    }
}

