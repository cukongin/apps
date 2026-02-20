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

        $tagihan->update([
            'jumlah' => $request->jumlah,
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

        $tagihan->transaksis()->delete();
        $tagihan->delete();

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    public function waive($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->status == 'lunas') {
            return back()->with('error', 'Tagihan sudah lunas.');
        }

        $tagihan->jumlah = $tagihan->terbayar;
        $tagihan->status = 'lunas';
        $tagihan->keterangan = $tagihan->keterangan . ' (Sisa Diputihkan)';
        $tagihan->save();

        return back()->with('success', 'Sisa tagihan berhasil diputihkan. Status kini Lunas.');
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
        // Only delete UNPAID bills to prevent financial data loss
        $deleted = $siswa->tagihans()
            ->where('status', '!=', 'lunas')
            ->delete();

        return back()->with('success', "Berhasil mereset (menghapus) $deleted tagihan yang belum lunas.");
    }
}

