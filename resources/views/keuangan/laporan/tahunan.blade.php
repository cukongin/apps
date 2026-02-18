<x-app-layout>
    <x-slot name="header">
        Laporan Ringkasan Tahunan
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

    <div class="max-w-[1440px] w-full mx-auto py-8">

        <!-- Print Header -->
        <div class="hidden print:block mb-4 text-center">
            <x-kop-laporan />
            <h1 class="text-xl font-bold uppercase text-black mb-1">LAPORAN RINGKASAN TAHUNAN</h1>
            <p class="text-sm text-black font-medium uppercase">
                Tahun Anggaran: 2024
            </p>
        </div>

        <!-- Page Header & Actions (Screen Only) -->
        <div class="print:hidden">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-3xl font-black text-[#111812] dark:text-white mb-2">Laporan Ringkasan Tahunan</h2>
                    <p class="text-[#618968] dark:text-gray-400 text-base max-w-2xl">
                        Rekapitulasi keuangan tahunan Madrasah Nurul Ainy. Pantau arus kas masuk dan keluar untuk evaluasi anggaran.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative group">
                        <div class="flex items-center gap-2 bg-white dark:bg-[#1a2e1d] border border-gray-200 dark:border-[#2f4532] rounded-lg px-4 py-2.5 cursor-pointer shadow-sm hover:border-primary transition-all">
                            <span class="material-symbols-outlined text-[#618968]">calendar_month</span>
                            <span class="text-sm font-semibold text-[#111812] dark:text-white">Tahun: 2024</span>
                            <span class="material-symbols-outlined text-[#618968] text-lg">expand_more</span>
                        </div>
                    </div>
                    <button onclick="window.print()" class="flex items-center gap-2 bg-primary hover:bg-[#0ea626] text-white rounded-lg px-5 py-2.5 shadow-md shadow-primary/20 transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span class="text-sm font-bold">Cetak Laporan</span>
                    </button>
                </div>
            </div>

            <!-- Summary Cards (Screen Only) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-gray-100 dark:border-[#2f4532] shadow-sm flex flex-col gap-4 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-blue-500">account_balance_wallet</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600 dark:text-blue-400">
                            <span class="material-symbols-outlined">account_balance_wallet</span>
                        </div>
                        <p class="text-sm font-medium text-[#618968] dark:text-gray-400">Saldo Awal Tahun</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[#111812] dark:text-white tracking-tight">Rp 150.000.000</p>
                        <p class="text-xs text-[#618968] mt-1">Carry over 2023</p>
                    </div>
                </div>
                <!-- Card 2 -->
                 <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-gray-100 dark:border-[#2f4532] shadow-sm flex flex-col gap-4 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-primary">trending_up</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg text-primary">
                            <span class="material-symbols-outlined">trending_up</span>
                        </div>
                        <p class="text-sm font-medium text-[#618968] dark:text-gray-400">Total Pemasukan</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[#111812] dark:text-white tracking-tight">Rp 850.000.000</p>
                        <div class="flex items-center gap-1 mt-1 text-primary text-xs font-semibold">
                            <span class="material-symbols-outlined text-sm">arrow_upward</span>
                            <span>12% dari target</span>
                        </div>
                    </div>
                </div>
                 <!-- Card 3 -->
                <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-gray-100 dark:border-[#2f4532] shadow-sm flex flex-col gap-4 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-orange-500">trending_down</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-500">
                            <span class="material-symbols-outlined">trending_down</span>
                        </div>
                        <p class="text-sm font-medium text-[#618968] dark:text-gray-400">Total Pengeluaran</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[#111812] dark:text-white tracking-tight">Rp 600.000.000</p>
                        <div class="flex items-center gap-1 mt-1 text-orange-500 text-xs font-semibold">
                            <span class="material-symbols-outlined text-sm">arrow_upward</span>
                            <span>5% vs tahun lalu</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section (Screen Only) -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-gray-200 dark:border-[#2f4532] shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-200 dark:border-[#2f4532] flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#111812] dark:text-white">Rincian Transaksi Bulanan</h3>
                    <button class="text-primary hover:text-[#0ea626] text-sm font-semibold flex items-center gap-1">
                        Export Excel
                        <span class="material-symbols-outlined text-sm">download</span>
                    </button>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 dark:bg-[#132015] border-b border-gray-200 dark:border-[#2f4532]">
                            <tr>
                                <th class="px-6 py-4 font-bold text-[#111812] dark:text-white sticky left-0 bg-gray-50 dark:bg-[#132015]">Bulan</th>
                                <th class="px-6 py-4 font-semibold text-[#618968]">SPP Santri</th>
                                <th class="px-6 py-4 font-semibold text-[#618968]">Tabungan</th>
                                <th class="px-6 py-4 font-semibold text-[#618968]">Donatur</th>
                                <th class="px-6 py-4 font-bold text-primary bg-primary/5 dark:bg-primary/10">Total Pemasukan</th>
                                <th class="px-6 py-4 font-bold text-orange-500 bg-orange-50 dark:bg-orange-900/10">Total Pengeluaran</th>
                                <th class="px-6 py-4 font-bold text-[#111812] dark:text-white text-right">Saldo Bulanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#2f4532]">
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3622] transition-colors">
                                <td class="px-6 py-4 font-medium text-[#111812] dark:text-white sticky left-0 bg-white dark:bg-[#1a2e1d]">Januari</td>
                                <td class="px-6 py-4 text-[#618968]">Rp 25.000.000</td>
                                <td class="px-6 py-4 text-[#618968]">Rp 5.000.000</td>
                                <td class="px-6 py-4 text-[#618968]">Rp 10.000.000</td>
                                <td class="px-6 py-4 font-semibold text-primary bg-primary/5 dark:bg-primary/10">Rp 40.000.000</td>
                                <td class="px-6 py-4 font-semibold text-orange-500 bg-orange-50 dark:bg-orange-900/10">Rp 15.000.000</td>
                                <td class="px-6 py-4 font-bold text-[#111812] dark:text-white text-right">Rp 25.000.000</td>
                            </tr>
                            <!-- More rows... -->
                        </tbody>
                     </table>
                </div>
            </div>
        </div>

        <!-- Print View Table (Single Table Combined) -->
        <div class="hidden print:block">
            <h3 class="font-bold border-b border-black mb-2 uppercase text-sm">Rincian Transaksi Bulanan</h3>
            <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                <thead>
                    <tr class="border-b border-black">
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Bulan</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Pemasukan</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Pengeluaran</th>
                        <th class="py-2 px-2 text-center font-bold uppercase">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr class="border-b border-black/50">
                        <td class="py-1 px-2 border-r border-black">Januari</td>
                        <td class="py-1 px-2 text-right border-r border-black">Rp 40.000.000</td>
                        <td class="py-1 px-2 text-right border-r border-black">Rp 15.000.000</td>
                        <td class="py-1 px-2 text-right font-bold">Rp 25.000.000</td>
                    </tr>
                    <!-- Filter other rows if needed or iterate -->

                    <!-- Total Footer -->
                     <tr class="border-t-2 border-black font-bold">
                        <td class="py-2 px-2 text-right uppercase border-r border-black">Total</td>
                        <td class="py-2 px-2 text-right border-r border-black text-green-700">Rp 850.000.000</td>
                        <td class="py-2 px-2 text-right border-r border-black text-red-600">Rp 600.000.000</td>
                        <td class="py-2 px-2 text-right text-black">Rp 400.000.000</td>
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

