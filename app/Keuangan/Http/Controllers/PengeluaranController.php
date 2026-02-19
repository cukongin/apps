<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Keuangan\Models\Pengeluaran;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $pengeluarans = \App\Keuangan\Models\Pengeluaran::with('details')->latest()->paginate(10);
        $kategoriList = \App\Keuangan\Models\KategoriPengeluaran::orderBy('nama')->get();
        // Get unique units for suggestions
        $satuanList = \App\Keuangan\Models\PengeluaranDetail::select('satuan')->distinct()->whereNotNull('satuan')->orderBy('satuan')->pluck('satuan');

        return view('keuangan.pengeluaran.index', compact('pengeluarans', 'kategoriList', 'satuanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal_pengeluaran' => 'required|date',
            'kategori' => 'required|string',
            'deskripsi' => 'nullable|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'details' => 'required|array|min:1',
            'details.*.nama_barang' => 'required|string|max:255',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.satuan' => 'nullable|string|max:50',
            'details.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $path = null;
            if ($request->hasFile('bukti_foto')) {
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('struk', $filename, 'public');
            }

            $pengeluaran = Pengeluaran::create([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
                'kategori' => $request->kategori,
                'bukti_foto' => $path,
                'jumlah' => 0,
                'user_id' => auth()->id()
            ]);

            $total = 0;
            foreach ($request->details as $item) {
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $pengeluaran->details()->create([
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'] ?? null,
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $subtotal
                ]);
                $total += $subtotal;
            }

            $pengeluaran->update(['jumlah' => $total]);

            DB::commit();
            return back()->with('success', 'Pengeluaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pengeluaran = \App\Keuangan\Models\Pengeluaran::findOrFail($id);

        // Permission: 'admin_utama', 'bendahara', OR 'Owner' can delete
        if (!in_array(auth()->user()->role, ['admin_utama', 'bendahara']) && $pengeluaran->user_id != auth()->id()) {
            return back()->with('error', 'Akses Ditolak! Data hanya bisa dihapus oleh Pembuat, Admin, atau Bendahara.');
        }

        if ($pengeluaran->bukti_foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($pengeluaran->bukti_foto)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengeluaran->bukti_foto);
        }

        $pengeluaran->delete();
        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}

