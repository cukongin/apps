<?php

namespace App\Keuangan\Services;

use App\Models\Siswa;
use App\Keuangan\Models\JenisBiaya;
use App\Keuangan\Models\Tagihan;
use Carbon\Carbon;

class BillService
{
    public static function syncForsiswa(Siswa $siswa, $targetDate = null)
    {
        // Use provided date or default to now
        $date = $targetDate ? Carbon::parse($targetDate) : Carbon::now();

        // 1. Get Active Biaya for this student
        $siswaClass = optional($siswa->kelas)->nama;
        $siswaLevel = optional(optional($siswa->kelas)->level)->nama;

        $biayaWajib = JenisBiaya::where('status', 'active')
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

        foreach ($biayaWajib as $biaya) {
            // Calculate Amount & Discount Logic
            $originalAmount = $biaya->jumlah;
            $discountAmount = 0;
            $discountInfo = '';

            if ($siswa->kategori_keringanan_id) {
                // Check if there is a discount rule for this bill type
                $aturan = \App\Keuangan\Models\AturanDiskon::where('kategori_keringanan_id', $siswa->kategori_keringanan_id)
                    ->where('jenis_biaya_id', $biaya->id)
                    ->first();

// Debug line removed

                if ($aturan) {
// Debug line removed

                    if ($aturan->tipe_diskon == 'nominal') {
                        $discountAmount = $aturan->jumlah;
                        $discountInfo = ' (Disc. Rp ' . number_format($discountAmount, 0, ',', '.') . ' - ' . $siswa->kategoriKeringanan->nama . ')';
                    } elseif ($aturan->tipe_diskon == 'persen' || $aturan->tipe_diskon == 'percentage') {
                        $discountAmount = $originalAmount * ($aturan->jumlah / 100);
                        $discountInfo = ' (Disc. ' . (0 + $aturan->jumlah) . '% - ' . $siswa->kategoriKeringanan->nama . ')';
                    }

                    file_put_contents('d:/XAMPP/htdocs/siapps/debug_bill_service.log', "Calculated Discount: $discountAmount\n", FILE_APPEND);

                    // Cap discount at original amount
                    if ($discountAmount > $originalAmount) $discountAmount = $originalAmount;
                }
            }

            if ($biaya->tipe == 'bulanan') {
                $currentMonth = $date->month;
                $currentYear = $date->year;

                // Check specific month/year
                $existing = Tagihan::where('siswa_id', $siswa->id)
                    ->where('jenis_biaya_id', $biaya->id)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->first();

                if ($existing) {
                    self::processDiscountUpdate($existing, $discountAmount, $discountInfo, $date);
                }

                if (!$existing) {
                    \Illuminate\Support\Facades\DB::transaction(function() use ($siswa, $biaya, $originalAmount, $discountAmount, $discountInfo, $date) {
                        $tagihan = Tagihan::create([
                            'siswa_id' => $siswa->id,
                            'jenis_biaya_id' => $biaya->id,
                            'jumlah' => $originalAmount, // Always Full Amount
                            'terbayar' => 0,
                            'status' => 'belum_lunas',
                            'keterangan' => 'Tagihan Bulan ' . $date->locale('id')->isoFormat('MMMM Y'),
                            'created_at' => $date->format('Y-m-d H:i:s'),
                            'updated_at' => $date->format('Y-m-d H:i:s')
                        ]);

                        // Apply Discount as Transaction
                        if ($discountAmount > 0) {
                            \App\Keuangan\Models\Transaksi::create([
                                'tagihan_id' => $tagihan->id,
                                'jumlah_bayar' => $discountAmount,
                                'metode_pembayaran' => 'Subsidi',
                                'keterangan' => 'Otomatis: ' . trim($discountInfo),
                                'created_at' => $date->format('Y-m-d H:i:s')
                            ]);

                            $tagihan->increment('terbayar', $discountAmount);
                            $tagihan->terbayar += $discountAmount; // Update instance for updateStatus check
                            self::updateStatus($tagihan);
                        }
                    });
                }
            } elseif ($biaya->tipe == 'sekali') {
                // Check existance
                $exists = Tagihan::where('siswa_id', $siswa->id)
                    ->where('jenis_biaya_id', $biaya->id)
                    ->exists();

                if ($exists) {
                    $bill = Tagihan::where('siswa_id', $siswa->id)
                        ->where('jenis_biaya_id', $biaya->id)
                        ->first();
                    if ($bill) {
                        self::processDiscountUpdate($bill, $discountAmount, $discountInfo, $date);
                    }
                }

                if (!$exists) {
                     \Illuminate\Support\Facades\DB::transaction(function() use ($siswa, $biaya, $originalAmount, $discountAmount, $discountInfo, $date) {
                        $tagihan = Tagihan::create([
                            'siswa_id' => $siswa->id,
                            'jenis_biaya_id' => $biaya->id,
                            'jumlah' => $originalAmount, // Always Full Amount
                            'terbayar' => 0,
                            'status' => 'belum_lunas',
                            'keterangan' => 'Tagihan ' . $biaya->nama,
                            'created_at' => $date->format('Y-m-d H:i:s'),
                            'updated_at' => $date->format('Y-m-d H:i:s')
                        ]);

                         // Apply Discount as Transaction
                        if ($discountAmount > 0) {
                            \App\Keuangan\Models\Transaksi::create([
                                'tagihan_id' => $tagihan->id,
                                'jumlah_bayar' => $discountAmount,
                                'metode_pembayaran' => 'Subsidi',
                                'keterangan' => 'Otomatis: ' . trim($discountInfo),
                                'created_at' => $date->format('Y-m-d H:i:s')
                            ]);

                            $tagihan->increment('terbayar', $discountAmount);
                            $tagihan->terbayar += $discountAmount; // Update instance for updateStatus check
                            self::updateStatus($tagihan);
                        }
                    });
                }
            }
        }
    }

    public static function generateForAll($targetDate = null)
    {
        // Get all active students
        $students = Siswa::where('status', 'Aktif')->with('kelas.level')->get();

        foreach ($students as $siswa) {
            self::syncForsiswa($siswa, $targetDate);
        }
    }

    /**
     * Remove duplicate bills
     */
    public static function removeDuplicates()
    {
        // 1. Duplicate One-Time Bills
        $duplicatesOneTime = \Illuminate\Support\Facades\DB::select("
            SELECT siswa_id, jenis_biaya_id, COUNT(*) as count
            FROM tagihans
            JOIN jenis_biayas ON tagihans.jenis_biaya_id = jenis_biayas.id
            WHERE jenis_biayas.tipe = 'sekali'
            GROUP BY siswa_id, jenis_biaya_id
            HAVING count > 1
        ");

        foreach ($duplicatesOneTime as $dup) {
            // Keep the first (oldest), delete others
            $bills = Tagihan::where('siswa_id', $dup->siswa_id)
                ->where('jenis_biaya_id', $dup->jenis_biaya_id)
                ->orderBy('created_at', 'asc')
                ->get(); // get all

            // Skip first
            $bills->shift();

            foreach ($bills as $bill) {
                if ($bill->terbayar == 0) {
                     $bill->delete();
                }
            }
        }

        // 2. Duplicate Monthly Bills (Same Month/Year)
        // This is harder in raw SQL without helper functions for month/year on created_at
        // So we iterate active students and check.
        // Or we use DB raw.
    }
    /**
     * Update Tagihan Status based on Payment Amount
     */
    public static function updateStatus(Tagihan $tagihan)
    {
        // Calculate status dynamically
        if ($tagihan->terbayar >= $tagihan->jumlah) {
            $tagihan->status = 'lunas';
        } elseif ($tagihan->terbayar > 0) {
            $tagihan->status = 'cicilan';
        } else {
            $tagihan->status = 'belum'; // Standardized status name
        }

        $tagihan->save();
        return $tagihan;
    }

    /**
     * Helper to update discount on existing bill
     */
    private static function processDiscountUpdate($tagihan, $discountAmount, $discountInfo, $date)
    {
        // Only process partial/unpaid bills to avoid messing with paid ones?
        // Actually, if status is 'lunas' but full payment was CASH (not subsidy),
        // applying discount should ideally REFUND cash? No, that's too complex.
        // Let's restrict to 'belum' or 'cicilan' OR if existing payment is explicitly 'Subsidi'.

        // Simpler approach: Calculate current subsidy
        $currentSubsidy = $tagihan->transaksis()
            ->where('metode_pembayaran', 'Subsidi')
            ->sum('jumlah_bayar');

        // Check if discount amount changed (allowing valid float comparison)
        if (abs($currentSubsidy - $discountAmount) > 1.0) {
            \Illuminate\Support\Facades\DB::transaction(function() use ($tagihan, $currentSubsidy, $discountAmount, $discountInfo, $date) {
                // 1. Remove old subsidy
                if ($currentSubsidy > 0) {
                    $tagihan->transaksis()->where('metode_pembayaran', 'Subsidi')->delete();
                    $tagihan->decrement('terbayar', $currentSubsidy);
                    $tagihan->terbayar -= $currentSubsidy; // Update instance
                }

                // 2. Add new subsidy
                if ($discountAmount > 0) {
                     \App\Keuangan\Models\Transaksi::create([
                        'tagihan_id' => $tagihan->id,
                        'jumlah_bayar' => $discountAmount,
                        'metode_pembayaran' => 'Subsidi',
                        'keterangan' => 'Koreksi Otomatis: ' . trim($discountInfo),
                        'created_at' => $date->format('Y-m-d H:i:s')
                    ]);
                    $tagihan->increment('terbayar', $discountAmount);
                    $tagihan->terbayar += $discountAmount; // Update instance
                }

                // 3. Update Status
                self::updateStatus($tagihan);
            });
        }
    }
}
