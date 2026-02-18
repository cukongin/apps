<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class PengaturanController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $users = \App\Models\User::orderBy('name')->get();
        
        // Fetch System Identity Settings
        $setting = [
            'nama_sistem' => \App\Models\Setting::get('nama_sistem', 'Sistem Pesantren'),
            'alamat' => \App\Models\Setting::get('alamat', ''),
            'no_telp' => \App\Models\Setting::get('no_telp', ''),
            'logo' => \App\Models\Setting::get('logo', null),
        ];

        return view('keuangan.pengaturan.index', compact('users', 'setting'));
    }

    public function updateIdentity(Request $request)
    {
        $request->validate([
            'nama_sistem' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'logo' => 'nullable|image|max:2048'
        ]);

        \App\Models\Setting::set('nama_sistem', $request->nama_sistem);
        \App\Models\Setting::set('alamat', $request->alamat);
        \App\Models\Setting::set('no_telp', $request->no_telp);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists? Ideally yes.
            $path = $request->file('logo')->store('settings', 'public');
            \App\Models\Setting::set('logo', $path);
        }

        return redirect()->back()->with('success', 'Identitas sistem berhasil diperbarui.');
    }

    public function fixStorageLink()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        try {
            // 1. Coba pakai Artisan dulu (Normalnya ini)
            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Throwable $e) {
                // Ignore silent fail, proceed to manual
            }

            // 2. Cek apakah sudah terbentuk? Jika belum, buat manual
            if (!file_exists($link)) {
                if (function_exists('symlink')) {
                    symlink($target, $link);
                } else {
                    return redirect()->back()->with('error', 'Fungsi symlink() dimatikan oleh Hosting. Hubungi Admin Hosting.');
                }
            } else {
                // Jika sudah ada tapi bukan link atau broken
                 if (is_link($link)) {
                     // Link valid/invalid, recreate untuk memastikan
                     unlink($link);
                     symlink($target, $link);
                 } else {
                     // Ada folder/file bernama storage (konflik)
                     // Jangan hapus, rename saja biar aman
                     rename($link, $link . '_backup_' . time());
                     symlink($target, $link);
                 }
            }

            return redirect()->back()->with('success', 'Storage Link berhasil diperbaiki! Cek kembali gambar.');

        } catch (\Throwable $e) {
            // Check for Windows Permission Error
            if (str_contains($e->getMessage(), 'Permission denied')) {
                return redirect()->back()->with('error', 'Gagal (Izin Ditolak): Jika di Localhost/Windows, jalankan "php artisan storage:link" manual via Terminal (Run as Administrator).');
            }
            return redirect()->back()->with('error', 'Gagal Total: ' . $e->getMessage());
        }
    }

    public function whatsapp()
    {
        $wa_api_url = \App\Models\Setting::get('wa_api_url', 'https://api.fonnte.com/send');
        $wa_api_token = \App\Models\Setting::get('wa_api_token', '');
        $wa_payment_template = \App\Models\Setting::get('wa_payment_template');
        $wa_mode = \App\Models\Setting::get('wa_mode', 'api');
        
        return view('keuangan.pengaturan.pengaturan_notifikasi_whatsapp', compact('wa_api_url', 'wa_api_token', 'wa_payment_template', 'wa_mode'));
    }

    public function updateWhatsapp(Request $request)
    {
        $request->validate([
            'wa_mode' => 'required|in:api,web',
            'wa_api_url' => 'nullable|url', // Nullable if web mode
            'wa_api_token' => 'nullable|string', // Nullable if web mode
            'wa_payment_template' => 'nullable|string',
        ]);

        \App\Models\Setting::set('wa_mode', $request->wa_mode);
        \App\Models\Setting::set('wa_api_url', $request->wa_api_url);
        \App\Models\Setting::set('wa_api_token', $request->wa_api_token);
        \App\Models\Setting::set('wa_payment_template', $request->wa_payment_template);

        return redirect()->back()->with('success', 'Pengaturan WhatsApp berhasil disimpan.');
    }

    public function testWhatsapp(Request $request)
    {
        $request->validate([
            'target' => 'required|numeric',
            'message' => 'nullable|string'
        ]);

        $message = $request->message ?? "Halo! Ini adalah pesan tes dari Sistem Keuangan Sekolah (BENDANA). 🚀";
        $mode = \App\Models\Setting::get('wa_mode', 'api');

        if ($mode === 'web') {
            // Web Mode: Return data for frontend redirect
            $target = $request->target;
            if (substr($target, 0, 1) == '0') {
                $target = '62' . substr($target, 1);
            }
            
            $url = "https://web.whatsapp.com/send?phone=" . $target . "&text=" . urlencode($message);
            
            return redirect()->back()
                ->with('success', 'Tes Mode Web: Jendela WhatsApp akan terbuka otomatis.')
                ->with('wa_test_url', $url);
        }

        // API Mode
        $result = \App\Services\WhatsAppService::send($request->target, $message);

        if ($result['status']) {
            return redirect()->back()->with('success', 'Pesan tes berhasil dikirim! Silakan cek HP tujuan.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim pesan: ' . $result['message']);
        }
    }

    public function logs()
    {
        $logs = \App\Models\AuditLog::with('user')->latest()->paginate(20);
        return view('keuangan.pengaturan.logs', compact('logs'));
    }

    // User Management Methods
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin_utama,kepala_madrasah,pengawas,bendahara,teller_tabungan,staf_keuangan,staf_administrasi',
            'foto' => 'nullable|image|max:2048' // Optional photo
        ]);

        $user = new \App\Models\User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->role = $request->role;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('users', 'public');
            $user->foto = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string|in:admin_utama,kepala_madrasah,pengawas,bendahara,teller_tabungan,staf_keuangan,staf_administrasi',
            'foto' => 'nullable|image|max:2048'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('users', 'public');
            $user->foto = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}

