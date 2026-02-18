@extends('layouts.app')

@section('title', 'Konfigurasi Sistem')

@section('content')
<div class="flex flex-col gap-8 w-full" x-data="settingsPage">

    <!-- Header & Year Management -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="material-symbols-outlined text-4xl text-primary">settings_suggest</span>
                Konfigurasi Sistem
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium text-lg">Pusat pengaturan Tahun Ajaran, Penilaian, dan Rapor.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
             @if($activeYear)
            <div class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                <span>TA: {{ $activeYear->nama }}</span>
            </div>
             <form action="{{ route('settings.year.regenerate', $activeYear->id) }}" method="POST"
                   data-confirm-delete="true"
                   data-title="Perbaiki Periode?"
                   data-message="Generate ulang periode default (Cawu/Semester) untuk tahun aktif ini?"
                   data-confirm-text="Ya, Perbaiki!"
                   data-confirm-color="#ca8a04"
                   data-icon="question">
                 @csrf
                 <button type="submit" class="btn-boss bg-amber-100 text-amber-700 border-amber-200 hover:bg-amber-200 shadow-none px-3" title="Fix Periode">
                     <span class="material-symbols-outlined text-[20px]">build</span>
                 </button>
             </form>
            @else
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded-xl font-bold flex items-center gap-2 border border-red-200">
                <span class="material-symbols-outlined">warning</span> Tahun Ajaran Kosong
            </div>
            @endif

            <!-- Backup Button -->
            <a href="{{ route('backup.store') }}" class="btn-boss bg-slate-800 text-white hover:bg-slate-900 shadow-slate-900/20">
                <span class="material-symbols-outlined text-[20px]">cloud_download</span>
                <span class="hidden sm:inline">Backup DB</span>
            </a>

            <button onclick="document.getElementById('yearModal').classList.remove('hidden')" class="btn-boss btn-primary">
                <span class="material-symbols-outlined text-[20px]">edit_calendar</span>
                <span>Kelola Tahun</span>
            </button>
        </div>
    </div>

    @if(!$activeYear)
        <div class="p-12 text-center bg-amber-50 text-amber-800 rounded-2xl border border-amber-200 shadow-sm">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-50">event_busy</span>
            <h3 class="font-black text-2xl mb-2">Sistem Belum Siap</h3>
            <p class="font-medium">Silakan buat Tahun Ajaran baru terlebih dahulu untuk memulai.</p>
        </div>
    @else

    <!-- TABS NAVIGATION -->
    <!-- TABS NAVIGATION -->
    <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-2 overflow-x-auto no-scrollbar">
        <nav class="flex space-x-2 min-w-max" aria-label="Tabs">
            <!-- APLIKASI -->
            <button @click="activeTab = 'application'"
                :class="activeTab === 'application' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-700'"
                class="group flex items-center px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                <span class="material-symbols-outlined mr-2 text-[20px]" :class="activeTab === 'application' ? 'text-white' : 'text-slate-400 group-hover:text-slate-500'">apps</span>
                Aplikasi
            </button>

            <!-- AKADEMIK -->
            <button @click="activeTab = 'academic'"
                :class="activeTab === 'academic' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-700'"
                class="group flex items-center px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                <span class="material-symbols-outlined mr-2 text-[20px]" :class="activeTab === 'academic' ? 'text-white' : 'text-slate-400 group-hover:text-slate-500'">school</span>
                Akademik
            </button>

            <!-- RAPOR -->
            <button @click="activeTab = 'reports'"
                :class="activeTab === 'reports' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-700'"
                class="group flex items-center px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                <span class="material-symbols-outlined mr-2 text-[20px]" :class="activeTab === 'reports' ? 'text-white' : 'text-slate-400 group-hover:text-slate-500'">assignment</span>
                Rapor & Jenjang
            </button>

            <!-- DATABASE (Admin) -->
            @if(auth()->user()->role === 'admin')
            <div class="w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>

            <button @click="activeTab = 'database'"
                :class="activeTab === 'database' ? 'bg-slate-800 text-white shadow-lg shadow-slate-800/30' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900'"
                class="group flex items-center px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                <span class="material-symbols-outlined mr-2 text-[20px]" :class="activeTab === 'database' ? 'text-white' : 'text-slate-400 group-hover:text-slate-500'">database</span>
                Database & Perawatan
            </button>
            @endif
        </nav>
    </div>


    <!-- TAB CONTENT CONTAINER -->
    <div class="min-h-[400px]">

        <!-- TAB 1: ATURAN PENILAIAN (GRADING) -->
        <!-- THEME SETTINGS TAB -->
    <!-- TAB 1: APLIKASI (APPLICATION) -->
    <div x-show="activeTab === 'application'" class="space-y-6 animate-fade-in-up" style="display: none;">

         <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Identity & Theme -->
            <div class="lg:col-span-2 space-y-6">
                <!-- APP IDENTITY FORM -->
                <div class="card-boss relative !p-6 md:!p-8">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-6 flex items-center gap-2 pb-4 border-b border-slate-100 dark:border-slate-700">
                        <span class="material-symbols-outlined text-primary">storefront</span> Identitas Aplikasi
                    </h3>
                    <form action="{{ route('settings.identity.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Nama Aplikasi</label>
                                    <input type="text" name="app_name" value="{{ \App\Models\GlobalSetting::val('app_name', 'E-Rapor') }}" class="input-boss w-full font-bold text-lg">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Sub-Judul / Tagline</label>
                                    <input type="text" name="app_tagline" value="{{ \App\Models\GlobalSetting::val('app_tagline', 'Integrated System') }}" class="input-boss w-full font-bold text-sm text-slate-600 dark:text-slate-400">
                                </div>
                            </div>
                            <div class="space-y-4">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Logo Sekolah</label>
                                <div class="flex items-start gap-4">
                                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden relative shadow-sm">
                                        @if(\App\Models\GlobalSetting::val('app_logo'))
                                            <img src="{{ asset('public/' . \App\Models\GlobalSetting::val('app_logo')) }}" class="w-full h-full object-contain p-2">
                                        @else
                                            <span class="material-symbols-outlined text-3xl text-slate-300">image</span>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="app_logo" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-dark cursor-pointer" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-6 mt-6 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" class="btn-boss btn-primary flex items-center gap-2">
                                <span class="material-symbols-outlined">save</span> Simpan Identitas
                            </button>
                        </div>
                    </form>
                </div>

                <!-- THEME SETTINGS -->
                <div class="card-boss"
                     x-data="{
                        currentTheme: '{{ \App\Models\GlobalSetting::val('app_theme', 'emerald') }}',
                        previewTheme(themeKey) {
                            this.currentTheme = themeKey;
                            const themes = {
                                'emerald': { '--color-primary': '0 62 41', '--color-primary-dark': '0 35 24', '--color-secondary': '70 112 97', '--color-background-dark': '0 42 28', '--color-surface-dark': '26 46 34' },
                                'blue': { '--color-primary': '30 58 138', '--color-primary-dark': '23 37 84', '--color-secondary': '96 165 250', '--color-background-dark': '15 23 42', '--color-surface-dark': '30 41 59' },
                                'purple': { '--color-primary': '88 28 135', '--color-primary-dark': '59 7 100', '--color-secondary': '192 132 252', '--color-background-dark': '19 7 35', '--color-surface-dark': '45 20 70' },
                                'crimson': { '--color-primary': '153 27 27', '--color-primary-dark': '69 10 10', '--color-secondary': '248 113 113', '--color-background-dark': '25 10 10', '--color-surface-dark': '60 20 20' },
                                'teal': { '--color-primary': '17 94 89', '--color-primary-dark': '4 47 46', '--color-secondary': '45 212 191', '--color-background-dark': '2 25 25', '--color-surface-dark': '10 50 50' },
                                'tosca': { '--color-primary': '3 127 122', '--color-primary-dark': '2 95 91', '--color-secondary': '243 104 53', '--color-background-dark': '1 40 38', '--color-surface-dark': '2 60 58' }
                            };
                            const colors = themes[themeKey];
                            if(colors) {
                                const root = document.documentElement;
                                for (const [key, value] of Object.entries(colors)) {
                                    root.style.setProperty(key, value);
                                }
                            }
                        }
                     }">

                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">palette</span>
                                Tema Warna Aplikasi
                            </h3>
                            <p class="text-slate-500 text-sm mt-1">Klik pilihan untuk melihat simulasi warna secara langsung.</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('settings.theme.update') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                                <!-- Themes Loop (Simplified) -->
                                @foreach(['emerald', 'blue', 'purple', 'crimson', 'teal', 'tosca'] as $t)
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="theme" value="{{ $t }}" class="peer sr-only" :checked="currentTheme === '{{ $t }}'" @change="previewTheme('{{ $t }}')">
                                    <div class="p-4 rounded-xl border-2 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                         :class="currentTheme === '{{ $t }}' ? 'border-primary ring-2 ring-primary bg-primary/10' : 'border-slate-200 hover:border-primary/50 dark:border-slate-700'">
                                        <div class="h-20 bg-slate-100 dark:bg-slate-800 rounded mb-2 flex items-center justify-center text-xs font-bold uppercase text-slate-400">{{ $t }}</div>
                                        <div class="text-center font-bold text-slate-700 dark:text-slate-200 capitilize">{{ $t }}</div>
                                    </div>
                                    <div class="absolute top-2 right-2 transition-opacity duration-300" :class="currentTheme === '{{ $t }}' ? 'opacity-100' : 'opacity-0'">
                                        <span class="material-symbols-outlined text-primary text-2xl">check_circle</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700 pt-6">
                                 <button type="submit" class="btn-boss btn-primary">
                                    <span class="material-symbols-outlined">save</span> Simpan Tampilan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Shortcuts -->
            <div class="space-y-6">
                 <div>
                     <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('settings.users.index') }}" class="card-boss p-6 hover:border-primary/50 hover:bg-primary/5 transition-all group flex items-center gap-4 cursor-pointer no-underline !shadow-sm hover:!shadow-md">
                            <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-primary transition-colors">group</span>
                            <div class="text-left">
                                <h4 class="font-bold text-slate-700 dark:text-white text-sm group-hover:text-primary transition-colors">User & Hak Akses</h4>
                                <p class="text-xs text-slate-500">Kelola akun guru & siswa</p>
                            </div>
                        </a>
                        <a href="{{ route('settings.menus.index') }}" class="card-boss p-6 hover:border-primary/50 hover:bg-primary/5 transition-all group flex items-center gap-4 cursor-pointer no-underline !shadow-sm hover:!shadow-md">
                            <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-primary transition-colors">menu_open</span>
                            <div class="text-left">
                                <h4 class="font-bold text-slate-700 dark:text-white text-sm group-hover:text-primary transition-colors">Menu Sidebar</h4>
                                <p class="text-xs text-slate-500">Atur menu aplikasi</p>
                            </div>
                        </a>
                        <a href="{{ route('settings.pages.index') }}" class="card-boss p-6 hover:border-primary/50 hover:bg-primary/5 transition-all group flex items-center gap-4 cursor-pointer no-underline !shadow-sm hover:!shadow-md">
                            <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-primary transition-colors">article</span>
                            <div class="text-left">
                                <h4 class="font-bold text-slate-700 dark:text-white text-sm group-hover:text-primary transition-colors">Halaman & Informasi</h4>
                                <p class="text-xs text-slate-500">Edit konten halaman</p>
                            </div>
                        </a>
                     </div>
                 </div>
            </div>
         </div>
    </div>

    <!-- TAB 3: RAPOR & JENJANG (REPORTS) -->
    <div x-show="activeTab === 'reports'" class="space-y-6 animate-fade-in-up" style="display: none;">
        <!-- JENJANG LINK CARD -->
        <div class="card-boss !p-8 flex flex-col items-center justify-center text-center min-h-[300px]">
            <div class="bg-indigo-50 dark:bg-indigo-900/30 p-6 rounded-full mb-6">
                <span class="material-symbols-outlined text-6xl text-indigo-600 dark:text-indigo-400">school</span>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-3">
                Konfigurasi Akademik Per Jenjang
            </h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">
                <b>Identitas Sekolah</b>, <b>Kepala Madrasah</b>, Penilaian, KKM, dan Tanggal Rapor kini <b>dipisahkan per Jenjang</b> agar lebih fleksibel.
            </p>

            <a href="{{ route('settings.jenjang.index') }}" class="btn-boss btn-primary text-lg !py-3 !px-8 hover:scale-105 transition-transform shadow-xl shadow-primary/30">
                <span class="material-symbols-outlined">settings_suggest</span>
                Buka Manajemen Jenjang
            </a>

            <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 md:grid-cols-3 gap-6 text-left w-full max-w-3xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-emerald-500 bg-emerald-50 rounded-lg p-1">check_circle</span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">Bobot Penilaian</div>
                        <div class="text-[10px] text-slate-500">Atur % Harian, UTS, UAS berbeda tiap jenjang.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                     <span class="material-symbols-outlined text-emerald-500 bg-emerald-50 rounded-lg p-1">check_circle</span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">Target KKM</div>
                        <div class="text-[10px] text-slate-500">KKM Mapel spesifik untuk setiap jenjang.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                     <span class="material-symbols-outlined text-emerald-500 bg-emerald-50 rounded-lg p-1">check_circle</span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">Titimangsa</div>
                        <div class="text-[10px] text-slate-500">Tanggal rapor & tempat tanda tangan custom.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8">


            <!-- RAPOR OPTIONS -->
            <div>
                 <div class="card-boss relative !p-6 md:!p-8">
                    <div class="mb-4">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Opsi Cetak Rapor</h3>
                        <p class="text-xs text-slate-500">Komponen opsional pada PDF Rapor.</p>
                    </div>

                    <form action="{{ route('settings.users.permissions') }}" method="POST" class="space-y-4">
                        @csrf
                        <!-- Toggle Ekskul -->
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                             <div class="flex items-center gap-3">
                                 <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                                     <span class="material-symbols-outlined text-lg">sports_soccer</span>
                                 </div>
                                 <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Tabel Ekstrakurikuler</span>
                             </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="rapor_show_ekskul" value="0">
                                <input type="checkbox" name="rapor_show_ekskul" value="1" class="sr-only peer" {{ \App\Models\GlobalSetting::val('rapor_show_ekskul', 1) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </div>

                        <!-- Toggle Prestasi -->
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                                    <span class="material-symbols-outlined text-lg">emoji_events</span>
                                </div>
                                <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Tabel Prestasi</span>
                            </div>
                           <label class="relative inline-flex items-center cursor-pointer">
                               <input type="hidden" name="rapor_show_prestasi" value="0">
                               <input type="checkbox" name="rapor_show_prestasi" value="1" class="sr-only peer" {{ \App\Models\GlobalSetting::val('rapor_show_prestasi', 1) ? 'checked' : '' }}>
                               <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                           </label>
                        </div>

                         <!-- Toggle Absensi -->
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg">
                                    <span class="material-symbols-outlined text-lg">fact_check</span>
                                </div>
                                <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Tabel Ketidakhadiran</span>
                            </div>
                           <label class="relative inline-flex items-center cursor-pointer">
                               <input type="hidden" name="rapor_show_absensi" value="0">
                               <input type="checkbox" name="rapor_show_absensi" value="1" class="sr-only peer" {{ \App\Models\GlobalSetting::val('rapor_show_absensi', 1) ? 'checked' : '' }}>
                               <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                           </label>
                        </div>

                         <!-- Toggle Catatan -->
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg">
                                    <span class="material-symbols-outlined text-lg">edit_note</span>
                                </div>
                                <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Catatan Wali Kelas</span>
                            </div>
                           <label class="relative inline-flex items-center cursor-pointer">
                               <input type="hidden" name="rapor_show_catatan" value="0">
                               <input type="checkbox" name="rapor_show_catatan" value="1" class="sr-only peer" {{ \App\Models\GlobalSetting::val('rapor_show_catatan', 1) ? 'checked' : '' }}>
                               <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                           </label>
                        </div>

                        <div class="pt-4 mt-2 border-t border-slate-100 dark:border-slate-700 text-right">
                             <button type="submit" class="btn-boss btn-primary !py-2 !px-4 !text-xs">
                                Simpan Opsi
                            </button>
                        </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>


    <!-- TAB 2: AKADEMIK (ACADEMIC) -->
    <div x-show="activeTab === 'academic'" class="space-y-6 animate-fade-in-up" style="display: none;">

         <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

             <!-- LEFT COLUMN: PERIODE, DEADLINES, WHITELIST -->
             <div class="lg:col-span-2 space-y-6">

                 <!-- Card: Periode & Semester (RESTORED) -->
                 <div class="card-boss relative !p-6 md:!p-8">
                    <div class="flex justify-between items-center mb-6">
                         <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">calendar_month</span> Manajemen Periode
                        </h3>
                        <div class="px-3 py-1 bg-primary/10 text-primary rounded-lg text-xs font-bold">
                            TA: {{ $activeYear->nama }}
                        </div>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl text-xs text-indigo-800 dark:text-indigo-200 mb-6 flex items-start gap-3">
                        <span class="material-symbols-outlined text-lg shrink-0">info</span>
                        <p>
                            Pastikan hanya <b>satu periode per jenjang</b> yang aktif. <br>
                            Mengaktifkan periode baru akan otomatis menutup periode lainnya dalam jenjang yang sama.
                        </p>
                    </div>

                    <div class="space-y-4">
                        @foreach($activeYear->periods->sortBy('lingkup_jenjang') as $p)
                        <div class="p-4 rounded-xl border {{ $p->status == 'aktif' ? 'border-primary bg-primary/5' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
                             x-data="{ editing: false }">

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        {{ $p->nama_periode }}
                                        <span class="text-[10px] px-2 py-0.5 rounded {{ $p->lingkup_jenjang == 'SEMUA' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} font-mono uppercase">{{ $p->lingkup_jenjang }}</span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                        <span>Deadline:</span>
                                        <span class="font-mono bg-slate-100 dark:bg-slate-700 px-1 rounded">{{ $p->end_date ? \Carbon\Carbon::parse($p->end_date)->format('d M Y') : '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($p->status == 'aktif')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm border border-green-200">
                                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Aktif
                                        </span>
                                    @else
                                        <form action="{{ route('settings.period.update', $p->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="aktif">
                                            <button type="submit" class="text-xs font-bold text-slate-400 hover:text-primary transition-colors py-1 px-2 hover:bg-primary/5 rounded-lg">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    <button @click="editing = !editing" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full text-slate-400 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit_calendar</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Edit Form -->
                            <div x-show="editing" class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700" style="display: none;" x-transition>
                                <form action="{{ route('settings.period.update', $p->id) }}" method="POST" class="flex items-end gap-3">
                                    @csrf
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Batas Waktu (Deadline)</label>
                                        <input type="date" name="end_date" value="{{ $p->end_date }}" class="input-boss w-full text-xs py-2">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Status</label>
                                        <select name="status" class="input-boss w-full text-xs py-2">
                                            <option value="aktif" {{ $p->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="tutup" {{ $p->status == 'tutup' ? 'selected' : '' }}>Tutup</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn-boss btn-primary !py-2 !text-xs">Simpan</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                 </div>

                 <!-- Deadlines Config -->
                 <div class="card-boss relative !p-6 md:!p-8">
                    <div class="flex justify-between items-center mb-6">
                         <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-red-500">timer</span> Batas Waktu Input Nilai
                        </h3>
                    </div>

                    <form action="{{ route('settings.deadline.update') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50 rounded-xl text-xs text-amber-800 dark:text-amber-200 flex gap-3 items-start">
                                <span class="material-symbols-outlined text-lg shrink-0">info</span>
                                <p>
                                    Guru tidak dapat menginput/mengedit nilai setelah tanggal ini lewat.
                                    Admin tetap bisa mengedit kapan saja.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Mulai Input</label>
                                    <input type="datetime-local" name="start_date" value="{{ \App\Models\GlobalSetting::val('grading_start_date') }}"
                                           class="input-boss w-full font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Selesai Input (Deadline)</label>
                                    <input type="datetime-local" name="end_date" value="{{ \App\Models\GlobalSetting::val('grading_end_date') }}"
                                           class="input-boss w-full font-bold text-red-600">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-6 mt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" class="btn-boss btn-primary flex items-center gap-2">
                                <span class="material-symbols-outlined">save</span> Simpan Jadwal
                            </button>
                        </div>
                    </form>
                 </div>

                 <!-- Whitelist (Exception) -->
                 <div class="card-boss relative !p-6 md:!p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Whitelist (Akses Khusus)</h3>
                            <p class="text-xs text-slate-500">Daftar guru yang diizinkan input nilai meski sudah lewat deadline.</p>
                        </div>
                        <button onclick="document.getElementById('modalWhitelist').showModal()" class="btn-boss btn-secondary text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">add</span> Tambah Akses
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">Guru</th>
                                    <th class="px-6 py-4">Sampai</th>
                                    <th class="px-6 py-4">Alasan</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                @forelse($whitelist as $w)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-700 dark:text-white">{{ $w->teacher_name }}</td>
                                    <td class="px-6 py-4 text-emerald-600 font-mono text-xs">{{ \Carbon\Carbon::parse($w->valid_until)->format('d M H:i') }}</td>
                                    <td class="px-6 py-4 text-slate-500 italic text-xs">{{ $w->reason }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('settings.deadline.whitelist.revoke', $w->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Cabut Akses">
                                                <span class="material-symbols-outlined text-lg">close</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-xs italic">
                                        Tidak ada guru dalam whitelist. Semua mengikuti jadwal global.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                 </div>
             </div>

             <!-- RIGHT COLUMN: SAFETY LOCK -->
             <div class="space-y-6">
                 <!-- Card: Safety Lock (Moved Here) -->
                 <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <span class="material-symbols-outlined text-6xl text-slate-800 dark:text-white">lock_person</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                         <span class="material-symbols-outlined text-amber-500">lock</span> Mode Edit Data Lama
                    </h3>
                    <p class="text-xs text-slate-500 mb-6 max-w-md">
                        Secara default, tahun ajaran lampau dikunci untuk menjaga validitas data rapor yang sudah terbit.
                        Aktifkan opsi ini jika Anda perlu memperbaiki kesalahan masa lalu.
                    </p>

                    <form action="{{ route('settings.general.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="safety_lock_marker" value="1">
                        <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div>
                                <h4 class="font-bold text-sm text-slate-700 dark:text-white">Izinkan Edit Tahun Lalu</h4>
                                <p class="text-[10px] text-slate-500">Membuka kunci input nilai & kenaikan kelas.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_edit_past_data" value="1" class="sr-only peer" onchange="this.form.submit()" {{ \App\Models\GlobalSetting::val('allow_edit_past_data') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-600"></div>
                            </label>
                        </div>
                    </form>
                 </div>
             </div>
         </div>
    </div>

        <!-- TAB 4: DATABASE & PERAWATAN -->
        <div x-show="activeTab === 'database'" class="animate-fade-in-up" style="display: none;">
            <div class="card-boss relative !p-6 md:!p-8">

                <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">cloud_sync</span> Backup & Restore Database
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Create Backup -->
                    <div class="bg-blue-50/50 dark:bg-blue-900/10 p-6 md:p-8 rounded-xl border border-blue-100 dark:border-blue-800/50 flex flex-col justify-between hover:border-blue-300 transition-colors">
                        <div>
                            <h4 class="font-bold text-blue-800 dark:text-blue-300 text-lg mb-2">Buat Backup Baru</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mb-4 leading-relaxed">
                                Sistem akan membuat file `.sql` lengkap dari database saat ini.
                                File akan disimpan di server dan bisa didownload.
                            </p>
                        </div>
                        <a href="{{ route('backup.store') }}" class="btn-boss bg-blue-600 hover:bg-blue-700 text-white border-blue-600 flex items-center justify-center gap-2">
                             <span class="material-symbols-outlined">save</span> Proses Backup Sekarang
                        </a>
                    </div>

                    <!-- Upload Restore -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-6 md:p-8 rounded-xl border border-slate-200 dark:border-slate-700">
                        <h4 class="font-bold text-slate-800 dark:text-slate-300 text-lg mb-2">Restore dari File</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-4 leading-relaxed">
                            Upload file `.sql` dari komputer Anda untuk mengembalikan database.
                            <br><b class="text-red-500">PERHATIAN:</b> Data saat ini akan ditimpa!
                        </p>
                        <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-end">
                            @csrf
                            <div class="w-full">
                                <input type="file" name="backup_file" accept=".sql" required class="block w-full text-xs text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-xs file:font-bold
                                  file:bg-slate-200 file:text-slate-700
                                  hover:file:bg-slate-300
                                  cursor-pointer">
                            </div>
                            <button type="submit" onclick="return confirm('Yakin ingin merestore database? Data saat ini akan hilang!')" class="btn-boss btn-secondary !py-2">
                                Restore
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Backup List -->
                <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 text-sm uppercase tracking-wider">Riwayat Backup di Server</h4>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white dark:bg-slate-800 text-xs uppercase font-bold text-slate-500 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nama File</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Ukuran</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            @forelse($backups as $backup)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-200">
                                    {{ $backup->filename }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $backup->created_at->format('d M Y H:i') }}
                                    <span class="text-[10px] text-slate-400 block">{{ $backup->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                    {{ $backup->size }}
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <form action="{{ route('backup.restore-local', $backup->filename) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin restore dari file ini? Data saat ini akan hilang!')">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded border border-amber-200 flex items-center gap-1 transition-colors" title="Restore">
                                            <span class="material-symbols-outlined text-[16px]">history</span> Restore
                                        </button>
                                    </form>

                                    <a href="{{ route('backup.download', $backup->filename) }}" class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded border border-blue-200 flex items-center gap-1 transition-colors" title="Download">
                                        <span class="material-symbols-outlined text-[16px]">download</span> Download
                                    </a>

                                    <form action="{{ route('backup.destroy', $backup->filename) }}" method="POST"
                                          onsubmit="return confirm('Hapus file backup ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded border border-red-200 flex items-center gap-1 transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic text-xs">
                                    Belum ada file backup tersimpan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="my-12 border-t border-slate-200 dark:border-slate-700 relative">
                <span class="absolute top-[-10px] left-1/2 -translate-x-1/2 bg-slate-50 dark:bg-slate-900 px-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                    System Maintenance Area
                </span>
            </div>
            <div class="mb-8 p-6 md:p-8 card-boss !bg-red-50 dark:!bg-red-900/10 border-red-200 dark:border-red-800 relative">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white dark:bg-red-800/30 text-red-600 dark:text-red-400 mb-4 shadow-sm border border-red-100">
                        <span class="material-symbols-outlined text-5xl">medical_services</span>
                    </div>
                    <h3 class="text-2xl font-bold text-red-700 dark:text-red-400 mb-2">Peta Masalah & Solusi (System Health)</h3>
                    <p class="text-slate-600 dark:text-slate-300 max-w-2xl mx-auto text-sm">
                        Pusat perbaikan data mandiri. Gunakan fitur-fitur ini untuk memperbaiki error sistem tanpa perlu akses database manual.
                    </p>
                </div>

                <!-- SUPER MIGRATION (DATA SYNC) -->
                <div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-xl text-white flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden max-w-6xl mx-auto ring-1 ring-white/20">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                            <span class="material-symbols-outlined text-9xl">move_up</span>
                    </div>

                    <div class="relative z-10 flex-1">
                        <h3 class="text-2xl font-bold flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined">dataset_linked</span> Super Migration (Data Sync)
                        </h3>
                        <p class="text-emerald-100 max-w-xl text-xs leading-relaxed">
                            Pindahkan data antar server (Local <-> Online) tanpa duplikat.
                            <br>Sistem akan cerdas menggabungkan data (Upsert) tanpa menimpa akun Admin.
                        </p>
                    </div>

                    <div class="relative z-10 flex gap-3">
                        <!-- EXPORT -->
                        <form action="{{ route('settings.migration.export') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold py-3 px-6 rounded-xl backdrop-blur-sm transition-transform hover:scale-105 flex items-center gap-2">
                                <span class="material-symbols-outlined">download</span> Download Data
                            </button>
                        </form>

                        <!-- IMPORT TRIGGER -->
                        <button onclick="document.getElementById('modalMigration').showModal()" class="bg-white text-emerald-800 hover:bg-emerald-50 font-bold py-3 px-6 rounded-xl shadow-lg transition-transform hover:scale-105 flex items-center gap-2">
                            <span class="material-symbols-outlined">upload</span> Upload Data
                        </button>
                    </div>
                </div>

                <!-- MODAL MIGRATION -->
                <dialog id="modalMigration" class="modal rounded-2xl shadow-2xl p-0 backdrop:backdrop-blur-sm bg-transparent">
                    <div class="card-boss w-[500px] m-0 !p-0 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="font-bold text-lg flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600">upload_file</span> Import Data Migrasi</h3>
                            <button onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                        <form action="{{ route('settings.migration.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                            @csrf
                            <div class="p-4 bg-blue-50/50 dark:bg-blue-900/10 text-blue-800 dark:text-blue-300 text-xs rounded-xl border border-blue-100 dark:border-blue-800 flex gap-3 items-start">
                                <span class="material-symbols-outlined text-lg shrink-0">info</span>
                                <div>
                                    <b>Cara Kerja:</b>
                                    <ul class="list-disc ml-4 space-y-1 mt-1 opacity-80">
                                        <li>Data yang sama (misal NISN sama) akan <b>Diupdate</b>.</li>
                                        <li>Data baru akan <b>Ditambahkan</b>.</li>
                                        <li>Data Akun Admin <b>TIDAK</b> akan ditimpa.</li>
                                    </ul>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Pilih File JSON Backup</label>
                                <input type="file" name="backup_file" accept=".json" required
                                    class="block w-full text-xs text-slate-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-xs file:font-bold
                                    file:bg-emerald-50 file:text-emerald-700
                                    hover:file:bg-emerald-100
                                    cursor-pointer border border-slate-200 rounded-xl">
                            </div>

                            <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-700">
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg flex items-center gap-2 hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined">cloud_upload</span> Mulai Migrasi
                                </button>
                            </div>
                        </form>
                    </div>
                </dialog>

                <!-- DATA HYGIENE (CLEANUP) -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6 px-1">
                    <!-- Mapel Cleanup -->
                    <div class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                         <div>
                            <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-amber-500">cleaning_services</span>
                                Cleanup Mapel Salah Jenjang
                            </h4>
                            <p class="text-xs text-slate-500 mb-4">
                                Menghapus mapel yang tidak sesuai dengan jenjang kelas (misal: Mapel MI di kelas MTs).
                            </p>
                         </div>
                         <form action="{{ route('cleanup.mapel.mismatch') }}" method="POST"
                               data-confirm-delete="true"
                               data-title="Hapus Mapel Salah Jenjang?"
                               data-message="Sistem akan menghapus semua assignment mapel yang tidak sesuai jenjang kelas."
                               data-confirm-text="Ya, Bersihkan"
                               data-confirm-color="#d97706">
                            @csrf
                            <button type="submit" class="btn-boss bg-amber-50 text-amber-700 hover:bg-amber-100 border-amber-200 w-full justify-center">
                                Eksekusi Cleanup Mapel
                            </button>
                         </form>
                    </div>

                    <!-- Grades Cleanup -->
                    <div class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                         <div>
                            <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-red-500">assignment_late</span>
                                Cleanup Nilai Salah Jenjang
                            </h4>
                            <p class="text-xs text-slate-500 mb-4">
                                Menghapus nilai siswa untuk mapel yang tidak sesuai jenjangnya. (PERHATIAN: Data nilai akan hilang permanen).
                            </p>
                         </div>
                         <form action="{{ route('cleanup.grades.mismatch') }}" method="POST"
                               data-confirm-delete="true"
                               data-title="Hapus Nilai Salah Jenjang?"
                               data-message="Sistem akan menghapus DATA NILAI yang tidak relevan dengan jenjang kelas. Pastikan sudah backup!"
                               data-confirm-text="Ya, Hapus Permanen"
                               data-confirm-color="#ef4444">
                            @csrf
                            <button type="submit" class="btn-boss bg-red-50 text-red-700 hover:bg-red-100 border-red-200 w-full justify-center">
                                Eksekusi Cleanup Nilai
                            </button>
                         </form>
                    </div>
                </div>

                <!-- AUTO UPDATE CARD -->
                <div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl shadow-xl text-white flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden max-w-6xl mx-auto ring-1 ring-white/20">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                         <span class="material-symbols-outlined text-9xl">cloud_sync</span>
                    </div>

                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold flex items-center gap-2 mb-2">
                             <span class="material-symbols-outlined">rocket_launch</span> Update Aplikasi Otomatis
                        </h3>
                        <p class="text-violet-100 max-w-xl text-xs leading-relaxed">
                            Klik tombol ini untuk menarik update terbaru dari sistem pusat (GitHub).
                            Pastikan koneksi internet server stabil.
                        </p>
                    </div>

                    <form action="{{ route('settings.maintenance.update-app') }}" method="POST" class="relative z-10"
                        data-confirm-delete="true"
                        data-title="Mulai Update Otomatis?"
                        data-message="Website mungkin tidak bisa diakses beberapa detik saat proses update berlangsung.">
                        @csrf
                        <button type="submit" class="bg-white text-violet-700 hover:bg-violet-50 font-bold py-3 px-6 rounded-xl shadow-lg transition-transform hover:scale-105 flex items-center gap-2">
                            <span class="material-symbols-outlined">download</span> Update Sekarang
                        </button>
                    </form>
                </div>

                <!-- GRID CONTAINER FOR TOOLS -->
                <div class="max-w-6xl mx-auto space-y-6">

                    <!-- CARD 1: MAGIC FIX (Full Width) -->
                    <div class="card-boss !border-l-4 !border-l-emerald-500 overflow-hidden text-slate-800 dark:text-white p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">

                        <!-- Icon & Text -->
                        <div class="flex items-start gap-4 flex-1">
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 rounded-full text-emerald-600 dark:text-emerald-400 shrink-0">
                                <span class="material-symbols-outlined text-4xl">auto_fix_high</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl text-slate-800 dark:text-white mb-2">
                                    Perbaikan Sistem Otomatis
                                </h4>
                                <p class="text-slate-500 text-xs mb-3 max-w-2xl leading-relaxed">
                                    Satu klik untuk membereskan masalah umum seperti data sampah, format nama yang salah, dan cache sistem yang menumpuk.
                                </p>
                                <ul class="text-xs text-slate-500 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1">
                                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Bersihkan Cache & Log</li>
                                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hapus Data Sampah (Orphan)</li>
                                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Perbaiki Jenjang Kelas</li>
                                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Rapikan Format Nama</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="shrink-0 w-full md:w-auto">
                            <form action="{{ route('settings.maintenance.magic-fix') }}" method="POST"
                                  data-confirm-delete="true"
                                  data-title="Jalankan Perbaikan?"
                                  data-message="Sistem akan mendiagnosa dan memperbaiki masalah secara otomatis."
                                  data-confirm-text="Ya, Jalankan!"
                                  data-confirm-color="#10b981"
                                  data-icon="question">
                                @csrf
                                <button type="submit" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 group">
                                    <span class="material-symbols-outlined group-hover:rotate-12 transition-transform">auto_fix</span>
                                    <span>Jalankan Magic Fix</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- GRID FOR SECONDARY TOOLS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- CARD 2: FORCE RECALCULATE -->
                        <div class="card-boss !border-l-4 !border-l-amber-400 p-6 md:p-8 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                                 <span class="material-symbols-outlined text-6xl text-amber-500">calculate</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-amber-500">update</span> Hitung Ulang Nilai
                                </h4>
                                <p class="text-xs text-slate-500 mb-4 pr-12 leading-relaxed">
                                    Gunakan ini jika nilai Rapor tidak berubah setelah mengedit bobot atau rumus.
                                </p>
                            </div>
                            <form action="{{ route('settings.maintenance.force-calcs') }}" method="POST"
                                  data-confirm-delete="true"
                                  data-title="Hitung Ulang Total?"
                                  data-message="Mulai perhitungan ulang nilai Rapor massal."
                                  data-confirm-text="Ya, Hitung Ulang!"
                                  data-confirm-color="#f59e0b"
                                  data-icon="question">
                                @csrf
                                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl shadow transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">refresh</span> HITUNG ULANG
                                </button>
                            </form>
                        </div>

                        <!-- CARD 3: RESET PROMOTION -->
                        <div class="card-boss !border-l-4 !border-l-red-400 p-6 md:p-8 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                                 <span class="material-symbols-outlined text-6xl text-red-500">restart_alt</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-red-500">warning</span> Reset Kenaikan Kelas
                                </h4>
                                <p class="text-xs text-slate-500 mb-4 pr-12 leading-relaxed">
                                    Kembalikan semua siswa ke <b>Kelas Asal</b>. Gunakan hanya jika terjadi kesalahan fatal.
                                </p>
                            </div>
                            <form action="{{ route('settings.maintenance.reset-promotion') }}" method="POST"
                                  data-confirm-delete="true"
                                  data-title="RESET Kenaikan Kelas?"
                                  data-message="BAHAYA: Data kenaikan kelas akan DIHAPUS TOTAL. Siswa kembali ke kelas lama."
                                  data-confirm-text="Ya, Reset Total!"
                                  data-confirm-color="#ef4444"
                                  data-icon="warning">
                                @csrf
                                <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-700 hover:text-red-800 font-bold py-3 px-4 rounded-xl shadow-sm border border-red-200 transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">history</span> RESET KENAIKAN
                                </button>
                            </form>
                        </div>
                    </div>


                    <!-- CARD 12: FACTORY RESET (DANGER ZONE) -->
                    <div class="bg-red-50 dark:bg-red-900/10 p-6 md:p-8 rounded-2xl border-2 border-dashed border-red-300 dark:border-red-800 flex flex-col items-center text-center gap-4 mt-8 col-span-full">
                        <h4 class="font-bold text-2xl text-red-600 dark:text-red-400 flex items-center gap-2">
                            <span class="material-symbols-outlined text-3xl">dangerous</span> ZONA BAHAYA: RESET TOTAL
                        </h4>
                        <p class="text-slate-600 dark:text-slate-300 max-w-2xl text-xs leading-relaxed">
                            Tindakan ini akan <b>MENGHAPUS SEMUA DATA</b> (Siswa, Guru, Kelas, Nilai, Absensi). <br>
                            Yang tersisa HANYA akun <b>Admin & Staff TU</b> serta Data Master (Mapel & Tahun Ajaran). <br>
                            Gunakan ini jika Anda ingin memulai sistem dari nol (Fresh Start).
                        </p>
                        <form action="{{ route('settings.maintenance.reset-system') }}" method="POST" onsubmit="return confirmReset(event)">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg transition-transform hover:scale-105 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">delete_forever</span> RESET SISTEM DARI AWAL
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
    @endif
</div>

<script>
function confirmReset(e) {
    e.preventDefault(); // Stop form
    const form = e.target;

    Swal.fire({
        title: '⚠️ ZONA BAHAYA!',
        text: "Anda akan MENGHAPUS SEMUA DATA (Siswa, Guru, Nilai, Kelas). Admin & TU tidak dihapus. Tindakan ini TIDAK BISA DIBATALKAN.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semuanya!',
        cancelButtonText: 'Batal',
        background: '#fff',
        color: '#545454'
    }).then((result) => {
        if (result.isConfirmed) {
            // Second Level: Challenge
            Swal.fire({
                title: 'KONFIRMASI TERAKHIR',
                text: 'Ketik "RESET" (Huruf Besar) untuk mengeksekusi penghapusan massal.',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'Ketik RESET disini...'
                },
                showCancelButton: true,
                confirmButtonText: 'LEDAKKAN',
                confirmButtonColor: '#d33',
                showLoaderOnConfirm: true,
                preConfirm: (text) => {
                    if (text !== 'RESET') {
                        Swal.showValidationMessage('Kode salah! Ketik "RESET" dengan huruf besar.')
                    }
                    return text === 'RESET';
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Kiamat...',
                        text: 'Sistem sedang dibersihkan. Jangan tutup halaman ini.',
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    }).then(() => {
                        form.submit(); // Submit the form programmatically
                    });
                }
            });
        }
    })
    return false;
}
</script>

<!-- Modal Year -->
<div id="yearModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('yearModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
            <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2" id="modal-title">
                     <span class="material-symbols-outlined text-primary">calendar_month</span> Kelola Tahun Ajaran
                </h3>

                <div class="mt-6 flex flex-col gap-6">
                    <!-- Form New -->
                    <form action="{{ route('settings.year.store') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="nama" placeholder="Contoh: 2025/2026" class="input-boss flex-1" required>
                        <button type="submit" class="btn-boss btn-primary">Tambah</button>
                    </form>

                    <!-- List Archived -->
                    <div class="flex flex-col gap-2 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        <label class="text-[10px] font-bold uppercase text-slate-500">Arsip Tahun Ajaran</label>
                        @foreach($archivedYears as $year)
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 transition-colors">
                             <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">{{ $year->nama }}</span>
                             <div class="flex gap-1">
                                <form action="{{ route('settings.year.toggle', $year->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] bg-slate-200 hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg font-bold transition-colors">Aktifkan</button>
                                </form>
                                 <form action="{{ route('settings.year.destroy', $year->id) }}" method="POST"
                                      data-confirm-delete="true"
                                      data-title="Hapus Tahun Ajaran?"
                                      data-message="Semua data kelas, nilai, dan absensi di tahun ini akan hilang PERMANEN.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg font-bold transition-colors">Hapus</button>
                                </form>
                             </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="btn-boss btn-secondary w-full sm:w-auto" onclick="document.getElementById('yearModal').classList.add('hidden')">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Whitelist -->
<dialog id="modalWhitelist" class="modal rounded-2xl shadow-2xl p-0 backdrop:backdrop-blur-sm bg-transparent">
    <div class="card-boss w-full max-w-md m-0 !p-0 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">verified_user</span> Beri Akses Khusus
            </h3>
            <button onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <form action="{{ route('settings.deadline.whitelist.store') }}" method="POST" class="p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Pilih Guru</label>
                <div class="relative">
                    <select name="id_guru" required class="input-boss w-full appearance-none">
                        <option value="" disabled selected>-- Cari Nama Guru --</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Periode Akses</label>
                <div class="relative">
                    <select name="id_periode" class="input-boss w-full appearance-none">
                        @foreach($periods as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_periode }} ({{ $p->lingkup_jenjang }})</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </div>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4">
                <div>
                     <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Durasi Akses</label>
                     <div class="relative">
                         <select name="duration" class="input-boss w-full appearance-none">
                             <option value="1">1 Hari (24 Jam)</option>
                             <option value="3">3 Hari</option>
                             <option value="7">1 Minggu</option>
                         </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <span class="material-symbols-outlined text-sm">expand_more</span>
                        </div>
                     </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Alasan</label>
                    <input type="text" name="alasan" required placeholder="Contoh: Sakit..." class="input-boss w-full">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="document.getElementById('modalWhitelist').close()" class="btn-boss btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn-boss btn-primary text-xs">Berikan Akses</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('settingsPage', () => ({
            activeTab: 'application',
            isLocked: {{ $isLocked ? 'true' : 'false' }},
            jenjang: '{{ $jenjang }}',
            loading: false,

            init() {
                const params = new URLSearchParams(window.location.search);
                const requestedTab = params.get('tab');
                const validTabs = ['application', 'academic', 'reports', 'database'];

                // Legacy Map
                const legacyMap = {
                    'general': 'academic',
                    'identity': 'application',
                    'theme': 'application',
                    'grading': 'reports',
                    'backup': 'database',
                    'maintenance': 'database'
                };

                if (validTabs.includes(requestedTab)) {
                    this.activeTab = requestedTab;
                } else if (legacyMap[requestedTab]) {
                    this.activeTab = legacyMap[requestedTab];
                }

                // Check for errors
                if (document.querySelector('.text-red-600')) {
                    // document.querySelector('.text-red-600').scrollIntoView();
                }
            },

            saveRules() {
                this.loading = true;
                this.$el.closest('form').submit();
            }
        }))
    })
</script>
@endsection

