<x-app-layout>
    <x-slot name="header">
        Manajemen Surat Ijin Santri
    </x-slot>

    <div class="max-w-7xl mx-auto">
         <!-- Header -->
        <header class="bg-white dark:bg-[#1a2e1d] border-b border-[#e5e7eb] dark:border-[#2a402d] px-8 py-5 flex-shrink-0 rounded-t-xl z-10">
            <div class="w-full">
                <!-- Breadcrumbs -->
                <div class="flex flex-wrap items-center gap-2 mb-2 text-sm">
                    <a class="text-[#618968] hover:text-primary transition-colors" href="#">Dashboard</a>
                    <span class="text-gray-300">/</span>
                    <a class="text-[#618968] hover:text-primary transition-colors" href="#">Administrasi</a>
                    <span class="text-gray-300">/</span>
                    <span class="text-[#111812] dark:text-white font-medium">Surat Ijin</span>
                </div>
                <div class="flex flex-wrap justify-between items-end gap-4">
                    <div>
                        <h1 class="text-[#111812] dark:text-white text-3xl font-black tracking-tight leading-tight">Manajemen Surat Ijin Santri</h1>
                        <p class="text-[#618968] dark:text-gray-400 text-base mt-1">Kelola pembuatan dan riwayat surat ijin keluar masuk santri.</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex flex-col lg:flex-row gap-8 mt-6">
            <!-- Left Column: Form -->
            <div class="w-full lg:w-[400px] flex-shrink-0 flex flex-col gap-6">
                <!-- Create Card -->
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-gray-100 dark:border-[#2a402d] overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-[#2a402d] bg-gray-50/50 dark:bg-[#203624]">
                        <h2 class="text-lg font-bold text-[#111812] dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">add_circle</span>
                            Buat Surat Ijin Baru
                        </h2>
                    </div>
                    <div class="p-5 flex flex-col gap-5">
                        <!-- Student Input -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-[#111812] dark:text-gray-200">Nama Santri</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
                                <input class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-gray-50 dark:bg-[#253828] border border-gray-200 dark:border-[#2a402d] text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#111812] dark:text-white placeholder-gray-400" placeholder="Cari nama santri..." type="text"/>
                            </div>
                        </div>
                        <!-- Reason Select -->
                         <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-[#111812] dark:text-gray-200">Alasan Ijin</label>
                            <select class="w-full px-4 py-2.5 rounded-lg bg-gray-50 dark:bg-[#253828] border border-gray-200 dark:border-[#2a402d] text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#111812] dark:text-white appearance-none cursor-pointer">
                                <option disabled="" selected="" value="">Pilih alasan...</option>
                                <option value="sakit">Sakit</option>
                                <option value="pulang">Pulang ke Rumah</option>
                                <option value="keluarga">Keperluan Keluarga</option>
                                <option value="lomba">Lomba / Tugas Sekolah</option>
                            </select>
                        </div>
                         <!-- Duration -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-[#111812] dark:text-gray-200">Durasi (Hari)</label>
                            <div class="flex items-center gap-2">
                                <button class="size-10 rounded-lg border border-gray-200 dark:border-[#2a402d] hover:bg-gray-100 dark:hover:bg-[#253828] flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-sm">remove</span>
                                </button>
                                <input class="flex-1 text-center py-2.5 rounded-lg bg-gray-50 dark:bg-[#253828] border border-gray-200 dark:border-[#2a402d] text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 font-bold text-[#111812] dark:text-white" type="number" value="1"/>
                                <button class="size-10 rounded-lg border border-gray-200 dark:border-[#2a402d] hover:bg-gray-100 dark:hover:bg-[#253828] flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                        </div>
                         <!-- Cost Calculation Box -->
                        <div class="mt-2 p-4 rounded-xl bg-primary/10 border border-primary/20 flex flex-col gap-1">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-[#618968] dark:text-gray-300 font-medium">Biaya Administrasi</span>
                                <span class="text-sm font-bold text-[#111812] dark:text-white">Rp 2.000</span>
                            </div>
                             <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-primary-dark dark:text-primary">Total Biaya</span>
                                <span class="text-xl font-black text-primary-dark dark:text-primary">Rp 2.000</span>
                            </div>
                        </div>
                         <!-- Submit Button -->
                        <button class="w-full py-3 px-4 bg-primary hover:bg-primary-dark active:scale-[0.98] transition-all rounded-lg text-[#111812] font-bold text-sm shadow-md shadow-primary/20 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">print</span>
                            Buat & Bayar Surat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Table History -->
            <div class="flex-1 flex flex-col gap-6 min-w-0">
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-gray-100 dark:border-[#2a402d] flex flex-col h-full min-h-[500px]">
                     <!-- Table Header -->
                     <div class="p-5 border-b border-gray-100 dark:border-[#2a402d] flex flex-wrap justify-between items-center gap-4">
                        <h2 class="text-lg font-bold text-[#111812] dark:text-white">Riwayat Perijinan</h2>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                            <input class="pl-9 pr-4 py-2 rounded-lg bg-gray-50 dark:bg-[#253828] border border-gray-200 dark:border-[#2a402d] text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 w-full sm:w-64 dark:text-white" placeholder="Cari riwayat..." type="text"/>
                        </div>
                    </div>
                     <!-- Table Content -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-700 uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-4">Nama Santri</th>
                                    <th class="px-6 py-4">Tanggal Ijin</th>
                                    <th class="px-6 py-4">Jenis Ijin</th>
                                    <th class="px-6 py-4">Durasi</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                <!-- Row 1 -->
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs">AH</div>
                                            <div class="flex flex-col">
                                                <span class="text-slate-700 dark:text-white font-bold">Ahmad Hasan</span>
                                                <span class="text-xs text-slate-500">Kelas 3 Aliyah</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                        14 Okt 2023
                                        <div class="text-xs text-slate-400">10:30 WIB</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                            Pulang
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-mono">3 Hari</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            Lunas
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="p-1.5 text-slate-500 hover:text-primary transition-colors" title="Cetak Ulang">
                                                <span class="material-symbols-outlined text-[20px]">print</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

