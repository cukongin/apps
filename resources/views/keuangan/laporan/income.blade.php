<x-app-layout>
    <x-slot name="header">
        Laporan Pemasukan & Donatur
    </x-slot>

    <style>
        @media print {
            @page {
                margin: 1.5cm;
            }
            @page :first {
                margin-top: 0.5cm;
            }
            body {
                font-family: sans-serif !important;
                font-size: 10pt !important;
                line-height: 1 !important;
            }
            table, td, th {
                font-size: 10pt !important;
                padding-top: 4px !important;
                padding-bottom: 4px !important;
            }
            .break-before-page {
                page-break-before: always !important;
                display: block !important;
            }
            .break-inside-avoid {
                page-break-inside: avoid !important;
            }
        }
    </style>

    <div class="max-w-[1400px] mx-auto space-y-8 px-6 py-8 print:p-0 print:max-w-none">

        <!-- Print Header -->
        <div class="hidden print:block mb-4 text-center">
            <x-kop-laporan />
            <h1 class="text-xl font-bold uppercase text-black mb-1">LAPORAN PEMASUKAN & DONATUR</h1>
            <p class="text-sm text-black font-medium uppercase">
                Per Tanggal: {{ now()->locale('id')->isoFormat('D MMMM Y') }}
            </p>
        </div>

        <!-- Breadcrumb & Title (Screen Only) -->
        <div class="print:hidden">
            <div class="flex items-center gap-2 text-sm mb-4">
                <a class="text-[#618968] dark:text-[#8ab391] hover:text-primary transition-colors font-medium" href="#">Keuangan</a>
                <span class="text-[#618968] dark:text-[#8ab391] material-symbols-outlined text-sm">chevron_right</span>
                <span class="text-[#111812] dark:text-white font-medium">Laporan Pemasukan</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="flex flex-col gap-2 max-w-2xl">
                    <h1 class="text-[#111812] dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Laporan Pemasukan & Donatur</h1>
                    <p class="text-[#618968] dark:text-[#8ab391] text-base font-normal">Rekapitulasi dana masuk, donasi, dan pembayaran administrasi bulanan.</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="flex items-center justify-center gap-2 h-10 px-4 bg-white dark:bg-[#1a2c1d] border border-[#e5e7eb] dark:border-[#2a3c2d] rounded-lg text-[#111812] dark:text-white text-sm font-bold hover:bg-gray-50 dark:hover:bg-[#1f262e] transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span>Cetak Laporan</span>
                    </button>
                    <!-- Excel Export Button (Future Ext) -->
                    <!--
                    <button class="flex items-center justify-center gap-2 h-10 px-4 bg-primary text-[#111812] rounded-lg text-sm font-bold hover:bg-opacity-90 transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">table_view</span>
                        <span>Ekspor Excel</span>
                    </button>
                    -->
                </div>
            </div>
        </div>

        <!-- KPI Cards (Screen Only) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 print:hidden">
            <!-- Card 1 -->
            <div class="bg-white dark:bg-[#1a2c1d] p-5 rounded-xl border border-[#f0f4f1] dark:border-[#2a3c2d] shadow-sm flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">volunteer_activism</span>
                    </div>
                    <span class="flex items-center gap-1 text-xs font-bold text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">trending_up</span> +12%
                    </span>
                </div>
                <div>
                    <p class="text-[#618968] dark:text-[#8ab391] text-sm font-medium mb-1">Total Donasi Bulan Ini</p>
                    <h3 class="text-[#111812] dark:text-white text-2xl font-bold">Rp 45.000.000</h3>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="bg-white dark:bg-[#1a2c1d] p-5 rounded-xl border border-[#f0f4f1] dark:border-[#2a3c2d] shadow-sm flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-outlined">diversity_3</span>
                    </div>
                    <span class="flex items-center gap-1 text-xs font-bold text-blue-700 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">trending_up</span> +5%
                    </span>
                </div>
                <div>
                    <p class="text-[#618968] dark:text-[#8ab391] text-sm font-medium mb-1">Jumlah Donatur Aktif</p>
                    <h3 class="text-[#111812] dark:text-white text-2xl font-bold">124 Orang</h3>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="bg-white dark:bg-[#1a2c1d] p-5 rounded-xl border border-[#f0f4f1] dark:border-[#2a3c2d] shadow-sm flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                    </div>
                    <span class="flex items-center gap-1 text-xs font-bold text-gray-700 bg-gray-100 dark:bg-gray-800 dark:text-gray-400 px-2 py-1 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">trending_flat</span> 0%
                    </span>
                </div>
                <div>
                    <p class="text-[#618968] dark:text-[#8ab391] text-sm font-medium mb-1">Pemasukan Lainnya (SPP & Adm)</p>
                    <h3 class="text-[#111812] dark:text-white text-2xl font-bold">Rp 12.500.000</h3>
                </div>
            </div>
        </div>

        <!-- Filters (Screen Only) -->
        <div class="bg-white dark:bg-[#1a2c1d] rounded-xl border border-[#f0f4f1] dark:border-[#2a3c2d] shadow-sm flex flex-col print:hidden">
            <!-- Filter Bar -->
            <div class="p-5 border-b border-[#f0f4f1] dark:border-[#2a3c2d] flex flex-col lg:flex-row gap-4 justify-between items-end lg:items-center">
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Date Range -->
                    <div class="flex items-center gap-2 bg-[#f0f4f1] dark:bg-[#132215] px-3 py-2 rounded-lg">
                        <span class="material-symbols-outlined text-[#618968] text-[20px]">calendar_today</span>
                        <input class="bg-transparent border-none text-sm text-[#111812] dark:text-white focus:ring-0 p-0 w-36 placeholder:text-[#111812] dark:placeholder:text-gray-400" placeholder="{{ date('01 M - t M Y') }}" type="text"/>
                    </div>
                    <!-- Category Dropdown -->
                    <div class="relative">
                        <select class="appearance-none bg-white dark:bg-[#132215] border border-[#e5e7eb] dark:border-[#2a3c2d] text-[#111812] dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full px-3 py-2 pr-8 cursor-pointer">
                            <option selected="">Semua Kategori</option>
                            <option>Donasi</option>
                            <option>SPP</option>
                            <option>Administrasi</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </div>
                    </div>
                    <!-- Method Dropdown -->
                    <div class="relative">
                        <select class="appearance-none bg-white dark:bg-[#132215] border border-[#e5e7eb] dark:border-[#2a3c2d] text-[#111812] dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full px-3 py-2 pr-8 cursor-pointer">
                            <option selected="">Semua Metode</option>
                            <option>Transfer Bank</option>
                            <option>Tunai</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </div>
                    </div>
                </div>
                <!-- Reset Link -->
                <button class="text-sm font-bold text-green-700 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 hover:underline">
                    Reset Filter
                </button>
            </div>

            <!-- Table (Screen View) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#fcfdfc] dark:bg-[#132215] border-b border-[#f0f4f1] dark:border-[#2a3c2d]">
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] dark:text-[#8ab391] uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] dark:text-[#8ab391] uppercase tracking-wider">Sumber / Nama</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] dark:text-[#8ab391] uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] dark:text-[#8ab391] uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] dark:text-[#8ab391] uppercase tracking-wider text-right">Nominal</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] dark:text-[#8ab391] uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f4f1] dark:divide-[#2a3c2d]">
                        <!-- Row 1 -->
                        <tr class="hover:bg-[#f0f4f1] dark:hover:bg-[#1f262e] transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#111812] dark:text-white">24 Okt 2023</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#111812] dark:text-white">H. Ahmad</span>
                                    <span class="text-xs text-[#618968]">Donatur Tetap</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                    Donasi Pembangunan
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#111812] dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-[#618968]">account_balance</span>
                                    Transfer BSI
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#111812] dark:text-white text-right">Rp 5.000.000</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button class="text-gray-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                            </td>
                        </tr>
                        <!-- More rows... -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-[#f0f4f1] dark:border-[#2a3c2d] flex items-center justify-between">
                <p class="text-sm text-[#618968]">Menampilkan 1-5 dari 48 data</p>
                <div class="flex items-center gap-2">
                    <button class="size-8 flex items-center justify-center rounded-lg border border-[#e5e7eb] dark:border-[#2a3c2d] text-[#618968] hover:bg-gray-50 dark:hover:bg-[#1f262e] disabled:opacity-50">
                        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                    </button>
                    <button class="size-8 flex items-center justify-center rounded-lg bg-primary text-[#111812] text-sm font-bold shadow-sm">1</button>
                    <button class="size-8 flex items-center justify-center rounded-lg border border-[#e5e7eb] dark:border-[#2a3c2d] text-[#111812] dark:text-white hover:bg-gray-50 dark:hover:bg-[#1f262e] text-sm font-medium">2</button>
                    <!-- ... -->
                </div>
            </div>
        </div>

        <!-- Print View Table -->
        <div class="hidden print:block">
            <h3 class="font-bold border-b border-black mb-2 uppercase text-sm">Rincian Pemasukan</h3>
            <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                <thead>
                    <tr class="border-b border-black">
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 15%; white-space: nowrap;">Tanggal</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 30%;">Sumber / Nama</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 25%;">Kategori</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 15%;">Metode</th>
                        <th class="py-2 px-2 text-center font-bold uppercase" style="width: 15%; white-space: nowrap;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr class="border-b border-black/50">
                        <td class="py-1 px-2 text-center border-r border-black">24 Okt 2023</td>
                        <td class="py-1 px-2 font-bold border-r border-black">H. Ahmad</td>
                        <td class="py-1 px-2 border-r border-black">Donasi Pembangunan</td>
                        <td class="py-1 px-2 text-center border-r border-black">Transfer BSI</td>
                        <td class="py-1 px-2 text-right font-bold">Rp 5.000.000</td>
                    </tr>

                    <!-- Total Footer -->
                    <tr class="border-t-2 border-black font-bold">
                        <td colspan="4" class="py-2 px-2 text-right uppercase border-r border-black">Total Pemasukan</td>
                        <td class="py-2 px-2 text-right text-green-700">Rp 57.500.000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="hidden print:flex justify-between items-start mt-12 px-8 font-sans text-black page-break-inside-avoid">
            <div class="text-center">
                <p>Mengetahui,</p>
                <p class="font-bold">Kepala Madrasah</p>
                <div class="h-24"></div>
                <p class="font-bold underline decoration-dotted underline-offset-4">......................................</p>
            </div>
            <div class="text-center">
                <p>Bangkalan, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p class="font-bold">Bendahara</p>
                <div class="h-24"></div>
                <p class="font-bold underline decoration-dotted underline-offset-4">......................................</p>
            </div>
        </div>
    </div>
</x-app-layout>

