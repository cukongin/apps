<x-app-layout>
    <x-slot name="header">
        Laporan Harian (Setoran Kasir)
    </x-slot>

    <style>
        @media print {
            @page {
                margin: 2cm;
            }
            @page :first {
                margin-top: 0.5cm;
            }
            body {
                font-family: sans-serif !important;
                font-size: 10pt !important;
                line-height: 1.5 !important;
            }
            table, td, th {
                font-size: 11pt !important;
                padding-top: 4px !important;
                padding-bottom: 4px !important;
            }
        }
    </style>

    <div class="flex flex-col gap-6 print:block print:gap-0">

        <!-- Print Header -->
        <div class="hidden print:block mb-4 text-center">
            <x-kop-laporan />
            <h1 class="text-xl font-bold uppercase text-black mb-1">LAPORAN HARIAN</h1>
            <p class="text-sm text-black font-medium uppercase">
                Hari/Tanggal: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </p>
        </div>
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 print:hidden">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-[#111418] dark:text-white">Laporan Harian</h1>
                <p class="text-[#617589] dark:text-slate-400 text-base">Rekapitulasi setoran kasir hari ini, {{ now()->format('d F Y') }}.</p>
            </div>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 h-11 bg-white dark:bg-[#1a2e1d] border border-[#dbe0e6] dark:border-[#2a452e] rounded-lg text-sm font-bold shadow-sm hover:bg-[#f0f2f4] dark:hover:bg-[#2a452e] transition-all text-[#111812] dark:text-white">
                <span class="material-symbols-outlined text-xl">print</span>
                <span>Cetak Setoran</span>
            </button>
        </div>

        <!-- Big Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:hidden">
            <div class="bg-primary/10 dark:bg-primary/5 rounded-xl p-8 border border-primary/20 flex flex-col items-center justify-center text-center gap-2">
                <p class="text-primary font-bold uppercase tracking-wider text-sm">Total Uang Masuk Hari Ini</p>
                <h2 class="text-5xl font-black text-primary">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h2>
                <div class="flex gap-4 mt-2 text-sm font-medium text-[#617589] dark:text-[#a0c2a7]">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">payments</span> Cash: Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">credit_card</span> Transfer: Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-8 border border-[#dbe0e6] dark:border-[#2a452e] flex flex-col gap-4">
                <h3 class="font-bold text-lg text-[#111812] dark:text-white">Ringkasan Transaksi</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-[#617589] dark:text-[#a0c2a7]">Pembayaran SPP (Tunai/Transfer)</span>
                        <span class="font-bold text-[#111812] dark:text-white">Rp {{ number_format($sortedTransaksi->where('tipe', 'SPP')->where('metode', '!=', 'Subsidi')->sum('nominal'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-orange-500 italic">Subsidi / Beasiswa (Non-Tunai)</span>
                        <span class="font-bold text-orange-500 italic">Rp {{ number_format($sortedTransaksi->where('tipe', 'SPP')->where('metode', 'Subsidi')->sum('nominal'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-[#617589] dark:text-[#a0c2a7]">Tabungan Masuk</span>
                        <span class="font-bold text-[#111812] dark:text-white">Rp {{ number_format($sortedTransaksi->where('tipe', 'Tabungan')->sum('nominal'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-[#617589] dark:text-[#a0c2a7]">Pemasukan Lain</span>
                        <span class="font-bold text-[#111812] dark:text-white">Rp {{ number_format($sortedTransaksi->where('tipe', 'Lainnya')->sum('nominal'), 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-[#dbe0e6] dark:border-[#2a452e] pt-2 flex justify-between items-center font-bold">
                        <span class="text-[#111812] dark:text-white">TOTAL UANG MASUK (CASH)</span>
                        <span class="text-primary">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction List (SCREEN VIEW) -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm overflow-hidden print:hidden">
            <div class="p-4 border-b border-[#dbe0e6] dark:border-[#2a452e] bg-[#f8f9fa] dark:bg-[#233827]">
                <h3 class="font-bold text-[#111812] dark:text-white uppercase tracking-wider text-sm">Rincian Transaksi Hari Ini</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Jam</th>
                            <th class="px-6 py-4">Siswa / Sumber</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">Metode</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dbe0e6] dark:divide-[#2a452e] text-sm">
                        @forelse($sortedTransaksi as $item)
                        <tr class="hover:bg-[#f8f9fa] dark:hover:bg-[#233827] transition-colors">
                            <td class="px-6 py-4 text-[#617589] dark:text-[#a0c2a7] font-mono">{{ $item['jam'] }}</td>
                            <td class="px-6 py-4 font-bold text-[#111812] dark:text-white">{{ $item['siswa'] }}</td>
                            <td class="px-6 py-4">{{ $item['keterangan'] }}</td>
                            <td class="px-6 py-4">
                                @if($item['metode'] == 'Cash' || $item['metode'] == 'Tunai')
                                    <span class="px-2 py-0.5 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold">{{ $item['metode'] }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold">{{ $item['metode'] }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-[#111812] dark:text-white">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Belum ada transaksi hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <!-- Footer Total -->
                    <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                         <tr class="border-t-2 border-[#dbe6dd] dark:border-[#2a3a2d]">
                            <td colspan="4" class="px-6 py-2 text-sm font-black text-left">Total Penerimaan</td>
                            <td class="px-6 py-2 text-sm font-black text-[#111812] dark:text-white text-right">
                                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction List (PRINT VIEW - Standardized) -->
        <div class="hidden print:block">
            <h3 class="font-bold border-b border-black mb-2 uppercase text-sm">Rincian Transaksi</h3>
            <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                <thead>
                    <tr class="border-b border-black">
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 10%; white-space: nowrap;">Jam</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 30%;">Siswa</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 100%;">Keterangan</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 1%; white-space: nowrap;">Metode</th>
                        <th class="py-2 px-2 text-center font-bold uppercase" style="width: 1%; white-space: nowrap;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sortedTransaksi as $item)
                    <tr class="border-b border-black/50">
                        <td class="py-1 px-2 text-center border-r border-black" style="white-space: nowrap;">{{ $item['jam'] }}</td>
                        <td class="py-1 px-2 font-bold border-r border-black">{{ $item['siswa'] }}</td>
                        <td class="py-1 px-2 border-r border-black">{{ $item['keterangan'] }}</td>
                        <td class="py-1 px-2 text-center border-r border-black" style="white-space: nowrap;">{{ $item['metode'] }}</td>
                        <td class="py-1 px-2 text-right font-bold" style="white-space: nowrap;">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <!-- Total -->
                    <tr class="border-t-2 border-black font-bold">
                        <td colspan="4" class="py-2 px-2 text-right uppercase border-r border-black">Total Penerimaan</td>
                        <td class="py-2 px-2 text-right" style="white-space: nowrap;">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </td>
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
                <p>Bangkalan, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p class="font-bold">Bendahara</p>
                <div class="h-24"></div>
                <p class="font-bold underline decoration-dotted underline-offset-4">......................................</p>
            </div>
        </div>
    </div>
</x-app-layout>

