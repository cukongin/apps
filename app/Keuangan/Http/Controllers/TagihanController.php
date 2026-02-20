<?php

namespace App\Keuangan\Http\Controllers;

use App\Keuangan\Models\Tagihan;
use App\Keuangan\Models\JenisBiaya;
use Illuminate\Http\Request;

class TagihanController extends \App\Http\Controllers\Controller
{
    public function edit($id)
    {
        $tagihan = Tagihan::with(['siswa', 'jenisBiaya'])->findOrFail($id);
        $jenisBiaya = JenisBiaya::where('status', 'active')->get();
        return view('keuangan.tagihan.edit', compact('tagihan', 'jenisBiaya'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|in:belum,cicilan,lunas',
            'keterangan' => 'nullable|string'
        ]);

        $tagihan = Tagihan::findOrFail($id);

        if ($request->jumlah < $tagihan->terbayar) {
            return back()->with('error', 'Gagal: Nominal tagihan tidak boleh lebih kecil dari total yang sudah dibayarkan (Rp ' . number_format($tagihan->terbayar, 0, ',', '.') . ').');
        }

        $tagihan->update([
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan
        ]);

        // Recalculate status based on new amount
        \App\Keuangan\Services\BillService::updateStatus($tagihan);

        return back()->with('success', 'Data tagihan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->terbayar > 0) {
            return back()->with('error', 'Tagihan tidak dapat dihapus karena sudah ada riwayat pembayaran. Silakan Hapus/Refund transaksinya terlebih dahulu di Menu Pembayaran.');
        }

        $tagihan->transaksis()->delete();
        $tagihan->delete();

        return back()->with('success', 'Tagihan kosong berhasil dihapus.');
    }

    public function waive($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->status == 'lunas' || $tagihan->terbayar >= $tagihan->jumlah) {
            return back()->with('error', 'Tagihan sudah lunas atau terselesaikan.');
        }

        $sisaDiputihkan = $tagihan->jumlah - $tagihan->terbayar;

        \DB::transaction(function() use ($tagihan, $sisaDiputihkan) {
            // Membuat transaksi subsidi agar tercatat di Laporan BKU / Diskon
            \App\Keuangan\Models\Transaksi::create([
                'tagihan_id' => $tagihan->id,
                'jumlah_bayar' => $sisaDiputihkan,
                'metode_pembayaran' => 'Subsidi',
                'keterangan' => 'Pemutihan (Waive) Manual oleh Admin',
                'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $tagihan->keterangan = $tagihan->keterangan . ' (Sisa Diputihkan)';
            $tagihan->save();

            // Self-healing status
            \App\Keuangan\Services\BillService::updateStatus($tagihan);
        });

        return back()->with('success', 'Sisa tagihan Rp ' . number_format($sisaDiputihkan, 0, ',', '.') . ' berhasil diputihkan (dicatat sbg Subsidi). Status kini Lunas.');
    }

    public function generateFuture(Request $request, $siswaId)
    {
        $siswa = \App\Models\Siswa::findOrFail($siswaId);
        $months = $request->input('months', 12);

        $startDate = null;
        if ($request->has('start_month') && $request->has('start_year')) {
            $startDate = $request->start_year . '-' . $request->start_month . '-01';
        }

        $count = \App\Keuangan\Services\BillService::generateFutureBills($siswa, $months, $startDate);

        return back()->with('success', "Berhasil generate $count tagihan baru untuk masa depan.");
    }

    public function resetBills($siswaId)
    {
        $siswa = \App\Models\Siswa::findOrFail($siswaId);
        // Only delete UNPAID and ZERO terbayar bills to prevent financial data and orphaned transaction loss
        $deleted = $siswa->tagihans()
            ->where('status', 'belum')
            ->where('terbayar', '<=', 0) // Extra safety
            ->delete();

        return back()->with('success', "Berhasil mereset (menghapus) $deleted tagihan yang belum lunas.");
    }
}

