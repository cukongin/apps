<x-app-layout>
    <x-slot name="header">
        Konfigurasi Biaya Dinamis
    </x-slot>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
            <a class="hover:text-primary transition-colors" href="{{ route('keuangan.dashboard') }}">Keuangan</a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-slate-900 dark:text-white font-medium">Konfigurasi Biaya Dinamis</span>
        </div>

        <!-- Page Heading -->
        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
            <div class="flex flex-col gap-2">
                <h1 class="text-slate-900 dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-tight">Konfigurasi Biaya Dinamis</h1>
                <p class="text-slate-500 text-base font-normal max-w-2xl">Kelola kategori biaya, nominal, dan target siswa secara mandiri tanpa coding. Perubahan akan langsung diterapkan pada tagihan siswa.</p>
            </div>
            <button class="flex items-center gap-2 rounded-xl h-12 px-6 bg-primary text-white font-bold hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 hover:scale-105 active:scale-95">
                <span class="material-symbols-outlined">add</span>
                <span>Tambah Kategori Biaya</span>
            </button>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card-boss p-6 flex flex-col justify-between group hover:border-primary/50 transition-colors">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Total Kategori</p>
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <span class="material-symbols-outlined">category</span>
                    </div>
                </div>
                <div>
                    <p class="text-slate-900 dark:text-white text-3xl font-black">12</p>
                    <p class="text-emerald-500 text-xs font-bold flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-sm">trending_up</span> +2 kategori baru
                    </p>
                </div>
            </div>

            <div class="card-boss p-6 flex flex-col justify-between group hover:border-emerald-500/50 transition-colors">
                 <div class="flex justify-between items-center mb-4">
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Kategori Aktif</p>
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                </div>
                <div>
                    <p class="text-slate-900 dark:text-white text-3xl font-black">8</p>
                    <p class="text-slate-400 text-xs font-medium mt-1">4 Non-aktif / Musiman</p>
                </div>
            </div>

            <div class="card-boss p-6 flex flex-col justify-between group hover:border-orange-500/50 transition-colors">
                 <div class="flex justify-between items-center mb-4">
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Estimasi</p>
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg text-orange-500">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <div>
                    <p class="text-slate-900 dark:text-white text-2xl font-black truncate">Rp 150.000.000</p>
                    <p class="text-rose-500 text-xs font-bold flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-sm">trending_down</span> -5% dr semester lalu
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card-boss p-4 mb-6 flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input class="input-boss w-full pl-10" placeholder="Cari biaya atau target..." type="text"/>
                </div>
            </div>
            <select class="select-boss w-auto">
                <option>Semua Status</option>
                <option>Aktif</option>
                <option>Non-Aktif</option>
            </select>
            <select class="select-boss w-auto">
                <option>Semua Kelas</option>
                <option>Kelas 7</option>
                <option>Kelas 8</option>
                <option>Kelas 9</option>
            </select>
        </div>

        <!-- Dynamic Table -->
        <div class="card-boss !p-0 overflow-hidden min-h-[400px]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase text-xs font-bold">
                            <th class="px-6 py-4">Nama Biaya</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Target Siswa</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        <!-- Row 1 -->
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-slate-800 dark:text-white font-bold group-hover:text-primary transition-colors">Uang Seragam 2024</span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[10px]">label</span> Kebutuhan Awal
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-bold font-mono">
                                Rp 850.000
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg border border-primary/20">Semua Siswa Baru</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold">AKTIF</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-all" title="Edit">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Hapus">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-slate-800 dark:text-white font-bold group-hover:text-primary transition-colors">Uang Bangunan (Infaq)</span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[10px]">label</span> Pembangunan
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-bold font-mono">
                                Rp 2.500.000
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded border border-slate-200 dark:border-slate-600">Kelas 7</span>
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded border border-slate-200 dark:border-slate-600">Kelas 8</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold">AKTIF</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-all" title="Edit">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Hapus">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 3 -->
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                             <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-slate-800 dark:text-white font-bold group-hover:text-primary transition-colors">SPP Bulanan</span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[10px]">label</span> Operasional
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-bold font-mono">
                                Rp 350.000
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800">Semua Siswa</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold">AKTIF</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-all" title="Edit">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Hapus">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 4 (Inactive) -->
                        <tr class="opacity-60 grayscale hover:grayscale-0 hover:opacity-100 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-slate-800 dark:text-white font-bold group-hover:text-primary transition-colors">Wisuda & Kelulusan 2023</span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[10px]">label</span> Akhir Tahun
                                    </span>
                                </div>
                            </td>
                             <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-bold font-mono">
                                Rp 1.200.000
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-lg border border-slate-200">Khusus Kelas 9</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-700 dark:border-slate-600">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    <span class="text-[10px] font-bold">NON-AKTIF</span>
                                </div>
                            </td>
                             <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-all" title="Lihat">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Hapus">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <!-- Pagination -->
            <div class="flex items-center justify-between p-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                <p class="text-xs text-slate-500">Menampilkan <span class="font-bold text-slate-900 dark:text-white">4</span> dari <span class="font-bold text-slate-900 dark:text-white">12</span> kategori</p>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-xs font-bold text-slate-400 cursor-not-allowed" disabled>Sebelumnya</button>
                    <button class="px-3 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-xs font-bold text-slate-700 dark:text-white hover:bg-slate-50 hover:border-primary hover:text-primary transition-all">Selanjutnya</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
