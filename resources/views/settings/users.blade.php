@extends('layouts.app')

@section('title', 'Manajemen User & Akses')

@section('content')
<div class="flex flex-col gap-6" x-data="{
    activeTab: 'users',
    selectedUsers: [],
    get allSelected() {
        return this.selectedUsers.length === {{ $users->count() }} && this.selectedUsers.length > 0;
    },
    toggleAll() {
        if (this.allSelected) {
            this.selectedUsers = [];
        } else {
            this.selectedUsers = [{{ $users->pluck('id')->map(fn($id) => "'$id'")->implode(',') }}];
        }
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen User & Akses</h1>
            <p class="text-slate-500 text-sm">Kelola akun dan sinkronisasi data guru.</p>
        </div>

        <!-- NEW BOSS TAB NAVIGATION -->
        <div class="flex p-1 space-x-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
             <button @click="activeTab = 'users'"
                 :class="activeTab === 'users' ? 'bg-white dark:bg-slate-600 shadow text-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                 class="px-4 py-2 text-sm font-bold rounded-lg transition-all">
                 Akun Pengguna
             </button>
             <button @click="activeTab = 'sync'"
                 :class="activeTab === 'sync' ? 'bg-white dark:bg-slate-600 shadow text-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                 class="px-4 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2">
                 Sinkronisasi Guru
                 @if($teachersWithoutAccount->count() > 0)
                 <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $teachersWithoutAccount->count() }}</span>
                 @endif
             </button>
             <button @click="activeTab = 'permissions'"
                 :class="activeTab === 'permissions' ? 'bg-white dark:bg-slate-600 shadow text-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                 class="px-4 py-2 text-sm font-bold rounded-lg transition-all">
                 Kontrol Akses
             </button>
        </div>
    </div>

    <!-- TAB 1: USERS LIST -->
    <div x-show="activeTab === 'users'" class="space-y-6 animate-fade-in-up">
        <!-- Filter Info -->
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('settings.users.index') }}" class="px-3 py-1 text-xs font-bold rounded-full border transition-all {{ !request('role') ? 'bg-primary text-white border-primary' : 'bg-white text-slate-600 border-slate-200 hover:border-primary' }}">Semua</a>
            <a href="{{ route('settings.users.index', ['role' => 'teacher']) }}" class="px-3 py-1 text-xs font-bold rounded-full border transition-all {{ request('role') == 'teacher' ? 'bg-primary text-white border-primary' : 'bg-white text-slate-600 border-slate-200 hover:border-primary' }}">Guru</a>
            <a href="{{ route('settings.users.index', ['role' => 'admin']) }}" class="px-3 py-1 text-xs font-bold rounded-full border transition-all {{ request('role') == 'admin' ? 'bg-primary text-white border-primary' : 'bg-white text-slate-600 border-slate-200 hover:border-primary' }}">Admin</a>
            <a href="{{ route('settings.users.index', ['role' => 'staff_tu']) }}" class="px-3 py-1 text-xs font-bold rounded-full border transition-all {{ request('role') == 'staff_tu' ? 'bg-primary text-white border-primary' : 'bg-white text-slate-600 border-slate-200 hover:border-primary' }}">Staff TU</a>
            <a href="{{ route('settings.users.index', ['role' => 'student']) }}" class="px-3 py-1 text-xs font-bold rounded-full border transition-all {{ request('role') == 'student' ? 'bg-primary text-white border-primary' : 'bg-white text-slate-600 border-slate-200 hover:border-primary' }}">Siswa</a>
            <a href="{{ route('settings.users.index', ['role' => 'bendahara']) }}" class="px-3 py-1 text-xs font-bold rounded-full border transition-all {{ request('role') == 'bendahara' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-slate-600 border-slate-200 hover:border-purple-600' }}">Bendahara</a>
            <a href="{{ route('settings.users.index', ['role' => 'staf_keuangan']) }}" class="px-3 py-1 text-xs font-bold rounded-full border transition-all {{ request('role') == 'staf_keuangan' ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-200 hover:border-teal-600' }}">Staf Keuangan</a>
        </div>

        <!-- Actions Bar -->
        @if(request('role'))
        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 p-4 rounded-xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                 <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-full">
                     <span class="material-symbols-outlined">warning</span>
                 </div>
                 <div>
                     <h3 class="font-bold text-amber-800 dark:text-amber-400 text-sm">Mass Action: Reset Password</h3>
                     <p class="text-xs text-amber-700 dark:text-amber-500">Reset password untuk semua user dengan role <strong>{{ ucfirst(request('role')) }}</strong> sekaligus.</p>
                 </div>
            </div>
            <form action="{{ route('settings.users.export') }}" method="POST"
                  data-confirm-delete="true"
                  data-title="RESET & EXPORT PASSWORD?"
                  data-message="PERINGATAN KERAS: Semua password user role {{ request('role') }} akan di-RESET dan diganti baru. File CSV berisi password baru akan didownload."
                  data-confirm-text="Ya, Reset & Export!"
                  data-confirm-color="#0f172a"
                  data-icon="warning">
                @csrf
                <input type="hidden" name="role" value="{{ request('role') }}">
                <button type="submit" class="btn-boss bg-slate-800 hover:bg-slate-900 text-white border-slate-800 shadow-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Reset & Export CSV
                </button>
            </form>
        </div>
        @endif

        <!-- Search -->
        <div class="card-boss !p-2 flex items-center">
            <form action="{{ route('settings.users.index') }}" method="GET" class="relative w-full flex items-center gap-2">
                <input type="hidden" name="role" value="{{ request('role') }}">
                <button type="submit" class="absolute left-3 text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau Email User..."
                       class="w-full pl-10 pr-4 py-3 rounded-lg border-none focus:ring-0 text-sm font-bold text-slate-700 dark:text-white dark:bg-transparent placeholder:font-normal">
            </form>
        </div>

        <!-- Table -->
        <div class="card-boss !p-0 overflow-hidden">
            <div class="px-4 py-3 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-900/30 flex items-center justify-between" x-show="selectedUsers.length > 0" x-transition x-cloak>
               <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                   <span class="material-symbols-outlined text-sm">check_circle</span>
                   <span class="font-bold text-sm" x-text="selectedUsers.length + ' User terpilih'"></span>
               </div>
               <form action="{{ route('settings.users.bulk_destroy') }}" method="POST"
                     data-confirm-delete="true"
                     data-title="Hapus User Terpilih?"
                     data-message="Yakin ingin menghapus user terpilih? Data tidak dapat dikembalikan.">
                    @csrf
                    @method('DELETE')
                    <!-- Hidden inputs for each selected ID -->
                    <template x-for="id in selectedUsers">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="bg-white text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-50 transition-colors shadow-sm">
                        Hapus Terpilih
                    </button>
               </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/50">
                        <tr>
                            <th class="p-4 w-10">
                                <input type="checkbox" @click="toggleAll" :checked="allSelected" class="rounded border-slate-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
                            </th>
                            <th class="p-4 pl-2 text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Email (Username)</th>
                            <th class="p-4 pr-6 text-xs font-bold text-slate-500 uppercase text-right tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group" :class="{'bg-primary/5': selectedUsers.includes('{{ $user->id }}')}">
                            <td class="p-4">
                                <input type="checkbox" value="{{ $user->id }}" x-model="selectedUsers" class="rounded border-slate-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
                            </td>
                            <td class="p-4 pl-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-500 dark:text-slate-300 ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $user->name }}</span>
                                        @if($user->wali_kelas_aktif)
                                        <span class="text-[10px] items-center gap-1 inline-flex text-orange-600 bg-orange-50 border border-orange-100 px-2 py-0.5 rounded-full w-fit max-w-[150px] truncate mt-1">
                                            <span class="material-symbols-outlined text-[10px]">school</span> Wali Kelas {{ $user->wali_kelas_aktif->nama_kelas }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <form action="{{ route('settings.users.role', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="text-[10px] font-bold uppercase rounded-lg border-transparent focus:ring-0 focus:border-transparent cursor-pointer py-1.5 pl-3 pr-8 appearance-none shadow-sm transition-all hover:shadow-md {{ $user->role == 'admin' ? 'bg-red-50 text-red-700 border-red-100' : ($user->role == 'teacher' ? 'bg-blue-50 text-blue-700 border-blue-100' : ($user->role == 'staff_tu' ? 'bg-amber-50 text-amber-700 border-amber-100' : ($user->role == 'bendahara' ? 'bg-purple-50 text-purple-700 border-purple-100' : ($user->role == 'staf_keuangan' ? 'bg-teal-50 text-teal-700 border-teal-100' : 'bg-slate-100 text-slate-700 border-slate-200')))) }}">
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>ADMIN</option>
                                        <option value="teacher" {{ $user->role == 'teacher' ? 'selected' : '' }}>GURU</option>
                                        <option value="staff_tu" {{ $user->role == 'staff_tu' ? 'selected' : '' }}>STAFF TU</option>
                                        <option value="bendahara" {{ $user->role == 'bendahara' ? 'selected' : '' }}>BENDAHARA</option>
                                        <option value="staf_keuangan" {{ $user->role == 'staf_keuangan' ? 'selected' : '' }}>STAF KEUANGAN</option>
                                        <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>SISWA</option>
                                    </select>
                                </form>
                            </td>
                            <td class="p-4">
                                <span class="font-mono text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">{{ $user->email }}</span>
                            </td>
                            <td class="p-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="{{ route('settings.users.impersonate', $user->id) }}" method="POST" target="_blank">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg text-xs font-bold flex items-center gap-1 transition-all shadow-sm" title="Login Ajaib">
                                            <span class="material-symbols-outlined text-[14px]">bolt</span>
                                            Log In
                                        </button>
                                    </form>
                                    <form action="{{ route('settings.users.generate', $user->id) }}" method="POST"
                                          data-confirm-delete="true"
                                          data-title="Generate Ulang Akun?"
                                          data-message="Password lama akan hilang dan diganti baru.">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg text-xs font-bold flex items-center gap-1 transition-all shadow-sm" title="Reset Password">
                                            <span class="material-symbols-outlined text-[14px]">refresh</span>
                                            Reset
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400 italic">
                                Tidak ada user ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                {{ $users->appends(['role' => request('role'), 'search' => request('search')])->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: SYNC TEACHERS -->
    <div x-show="activeTab === 'sync'" class="space-y-6 animate-fade-in-up" style="display: none;">
        <div class="card-boss !bg-primary/5 !border-primary/20 p-6 flex items-start gap-4">
            <div class="p-3 bg-white text-primary rounded-xl shadow-sm border border-primary/10">
                 <span class="material-symbols-outlined text-2xl">sync_problem</span>
            </div>
            <div>
                <h3 class="font-bold text-primary mb-1">Sinkronisasi Akun Guru</h3>
                <p class="text-sm text-primary/80 leading-relaxed mb-0">
                    Daftar guru dibawah ini terdaftar di Data Master tapi <b>belum memiliki akun login</b>.
                    <br>Klik tombol "Buat Akun" untuk membuatkan akun login mereka secara otomatis.
                </p>
            </div>
        </div>

        <!-- Google Sheet Configuration Form (Real-time Login) -->
        <div class="card-boss relative !p-6 md:!p-8">
            <form action="{{ route('settings.update-sheet-id') }}" method="POST">
                @csrf
                <h4 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 text-lg">
                    <span class="material-symbols-outlined text-green-600">settings_ethernet</span>
                    Konfigurasi Login Real-time (Google Sheet)
                </h4>

                <div class="bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 p-4 rounded-xl mb-6 text-xs border border-slate-200 dark:border-slate-700 flex gap-3 items-start">
                    <span class="material-symbols-outlined text-lg">info</span>
                    <div>
                        <strong>Cara Kerja:</strong> Saat guru login dengan kode, sistem akan langsung mengecek ke Google Sheet ini.
                        <br>Pastikan Sheet disetting <strong>"Anyone with the link can view"</strong>.
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">ID Spreadsheet / Link Google Sheet</label>
                        <input type="text" name="sheet_id"
                               value="{{ \App\Models\GlobalSetting::val('teacher_sheet_id') }}"
                               placeholder="Contoh: 1WIshP6qoS8b4KoULTepKCprElBnxsuwLv3OuwHvnKr0"
                               class="input-boss w-full font-mono text-sm" required>
                        <p class="text-[10px] text-slate-400 mt-2 ml-1">
                            <span class="font-bold">Format Sheet:</span> Kolom A = Nama Guru, Kolom B = Kode Akses (6 Digit).
                        </p>
                    </div>
                    <button type="submit" class="btn-boss bg-slate-800 hover:bg-slate-900 text-white shadow-lg flex items-center justify-center gap-2 h-[46px]">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Simpan ID
                    </button>
                </div>
            </form>
        </div>

        <div class="card-boss !p-0 overflow-hidden">
             <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/50">
                    <tr>
                        <th class="p-4 pl-6 text-xs font-bold text-slate-500 uppercase">Nama Guru</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">NIP / NUPTK</th>
                        <th class="p-4 pr-6 text-xs font-bold text-slate-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                    @forelse($teachersWithoutAccount as $teacher)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="p-4 pl-6 text-sm font-bold text-slate-800 dark:text-white">{{ $teacher->nama }}</td>
                        <td class="p-4 text-sm text-slate-500 font-mono">{{ $teacher->nip ?? $teacher->nuptk ?? '-' }}</td>
                        <td class="p-4 pr-6 text-right">
                             <form action="{{ route('settings.users.sync-teacher') }}" method="POST">
                                @csrf
                                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                <button type="submit" class="px-4 py-2 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg text-xs font-bold whitespace-nowrap transition-all border border-primary/20 hover:border-primary">
                                    + Buat Akun
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full flex items-center justify-center mb-2">
                                    <span class="material-symbols-outlined text-3xl">check_circle</span>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-lg">Semua Aman!</h4>
                                <span class="text-sm text-slate-500">Semua guru di Data Master sudah memiliki akun login.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: PERMISSIONS -->
    <div x-show="activeTab === 'permissions'" class="space-y-6 animate-fade-in-up" style="display: none;">
        <form action="{{ route('settings.users.permissions') }}" method="POST">
            @csrf

            <div class="card-boss space-y-8 !p-6 md:!p-8">

                <!-- Guru Permissions -->
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                             <span class="material-symbols-outlined">school</span>
                        </div>
                        Hak Akses Guru
                    </h3>
                    <div class="space-y-3 pl-14">
                        @php
                            $guruKeys = [
                                'access_guru_input_nilai' => 'Bisa Akses Menu Input Nilai & Jadwal',
                            ];
                        @endphp
                        @foreach($guruKeys as $key => $label)
                        <label class="flex items-center gap-3 group cursor-pointer">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input type="checkbox" name="{{ $key }}" value="1" {{ old($key, \App\Models\GlobalSetting::val($key, 1)) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer transition-all group-hover:scale-110">
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors select-none">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-slate-700">

                <!-- Wali Kelas Permissions -->
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <div class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                            <span class="material-symbols-outlined">supervisor_account</span>
                        </div>
                        Hak Akses Wali Kelas
                    </h3>
                    <div class="space-y-3 pl-14">
                        @php
                            $waliKeys = [
                                'access_wali_input_catatan' => 'Bisa Akses Menu Catatan Siswa',
                                'access_wali_input_absensi' => 'Bisa Akses Menu Absensi',
                                'access_wali_input_ekskul' => 'Bisa Akses Menu Ekstrakurikuler',
                                'access_wali_kenaikan_kelas' => 'Bisa Akses Menu Kenaikan Kelas',
                                'access_wali_cetak_rapor' => 'Bisa Akses Menu Cetak Rapor',
                                'access_wali_monitoring_nilai' => 'Bisa Akses Menu Monitoring Penilaian',
                            ];
                        @endphp
                        @foreach($waliKeys as $key => $label)
                         <label class="flex items-center gap-3 group cursor-pointer">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input type="checkbox" name="{{ $key }}" value="1" {{ old($key, \App\Models\GlobalSetting::val($key, 1)) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer transition-all group-hover:scale-110">
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors select-none">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 text-right">
                    <button type="submit" class="btn-boss btn-primary flex items-center gap-2 ml-auto">
                        <span class="material-symbols-outlined">save</span>
                        Simpan Pengaturan Akses
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>

<!-- Credential Modal -->
@if(session('generated_credential'))
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-data="{ open: true }" x-show="open" x-cloak>
    <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-2xl w-full max-w-md p-0 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-200 overflow-hidden">
        <div class="bg-green-50 dark:bg-green-900/20 p-6 text-center border-b border-green-100 dark:border-green-800/50">
             <div class="bg-white dark:bg-surface-dark text-green-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm ring-4 ring-green-100 dark:ring-green-900/30">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Akun Berhasil Dibuat!</h2>
            <p class="text-green-700 dark:text-green-400 text-xs font-bold mt-1 uppercase tracking-wider">Role: {{ session('generated_credential')['role'] }}</p>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-xl border border-slate-100 dark:border-slate-700 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama User</span>
                    <span class="font-bold text-slate-800 dark:text-white text-sm">{{ session('generated_credential')['name'] }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Login</span>
                    <span class="font-mono text-sm font-bold text-primary select-all">{{ session('generated_credential')['email'] }}</span>
                </div>
                <div class="flex justify-between items-center bg-white dark:bg-slate-900 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Password</span>
                    <span class="font-mono text-xl font-bold text-slate-800 dark:text-white select-all tracking-wider">
                        {{ session('generated_credential')['password'] }}
                    </span>
                </div>
            </div>

            <button @click="open = false" class="btn-boss btn-primary w-full justify-center">
                Selesai & Tutup
            </button>
        </div>
    </div>
</div>
@endif

@endsection
