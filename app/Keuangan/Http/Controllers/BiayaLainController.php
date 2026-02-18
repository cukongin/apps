<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;
use App\Keuangan\Models\JenisBiaya;
use App\Models\Jenjang; // Changed from App\Keuangan\Models\Level
use App\Models\Kelas;

class BiayaLainController extends \App\Http\Controllers\Controller
{

    public function index()
    {
        $biayas = JenisBiaya::paginate(10);
        return view('keuangan.biaya_lain.index', compact('biayas'));
    }

    public function create()
    {
        $levels = Jenjang::all(); // Fetch Jenjang as levels
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $kelas = Kelas::where('id_tahun_ajaran', $activeYear->id ?? 0)->get();
        return view('keuangan.biaya_lain.create', compact('levels', 'kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tipe' => 'required|in:bulanan,sekali',
            'kategori' => 'required|string',
            'target_type' => 'required|in:all,level,class,custom',
            'target_value' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'recurring_day' => 'nullable|integer|min:1|max:31', // for monthly
            'due_date' => 'nullable|date', // for once
        ]);

        // Default status if not provided
        $validated['status'] = $validated['status'] ?? 'active';

        // Encoding target_value to JSON if it's for level or class to support multiple selections
        if (in_array($request->target_type, ['level', 'class']) && $request->has('target_value')) {
             // The frontend might send it as a comma separated string if I use a hidden input,
             // OR as an array if I change the input name to target_value[].
             // Let's assume I keep the hidden input logic but make it store JSON string in JS.
             // Actually, simplest is to let frontend JS put JSON string into the hidden field.
             // But to be safe, I'll Ensure it's stored safely.
        }

        // If the JS puts a JSON string into 'target_value', then $validated['target_value'] is already a string.
        // So I don't need to change much here if the frontend does the heavy lifting.

        JenisBiaya::create($validated);
        return redirect()->route('keuangan.biaya-lain.index')->with('success', 'Master biaya berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $biaya = JenisBiaya::findOrFail($id);
        $levels = Jenjang::all(); // Fetch Jenjang
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $kelas = Kelas::where('id_tahun_ajaran', $activeYear->id ?? 0)->get();
        return view('keuangan.biaya_lain.edit', compact('biaya', 'levels', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'jumlah' => 'required|numeric',
            'tipe' => 'required',
            'status' => 'nullable',
            'kategori' => 'nullable',
            'target_type' => 'nullable',
            'target_value' => 'nullable'
        ]);

        $biaya = JenisBiaya::findOrFail($id);
        $biaya->update($validated);

        return redirect()->route('keuangan.biaya-lain.index')->with('success', 'Master biaya berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $biaya = JenisBiaya::findOrFail($id);
        $biaya->delete();
        return redirect()->route('keuangan.biaya-lain.index')->with('success', 'Master biaya berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $biaya = JenisBiaya::findOrFail($id);
        $biaya->status = $biaya->status === 'active' ? 'inactive' : 'active';
        $biaya->save();

        return response()->json([
            'success' => true,
            'new_status' => $biaya->status,
            'message' => 'Status biaya berhasil diperbarui.'
        ]);
    }

    // Config method remains for specialized settings if needed, or redirect to index

}

