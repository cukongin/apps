<x-app-layout>
    <x-slot name="header">
        Laporan Pembayaran Siswa
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
                line-height: 1 !important;
            }
            table, td, th {
                font-size: 10pt !important;
                padding-top: 4px !important;
                padding-bottom: 4px !important;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto px-6 py-8 print:p-0 print:max-w-none">

        <!-- Print Header -->
        <div class="hidden print:block mb-4 text-center">
            <x-kop-laporan />
            <h1 class="text-xl font-bold uppercase text-black mb-1">LAPORAN PEMBAYARAN SISWA</h1>
            <p class="text-sm text-black font-medium uppercase">
                Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
            </p>
        </div>

        <!-- Filter Section -->
        <div class="print:hidden bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
            <form action="{{ route('keuangan.laporan.santri') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                </div>
                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                </div>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">filter_list</span>
                    Filter
                </button>
                <button type="button" onclick="window.print()" class="bg-[#f0f4f1] border border-[#dbe6dd] dark:bg-[#2a3a2d] dark:border-[#2a452e] hover:bg-gray-100 dark:hover:bg-[#203623] text-[#637588] dark:text-[#a0b0a3] font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">print</span>
                    Cetak
                </button>
            </form>
        </div>

        <!-- Summary Cards by Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 print:hidden">
            <!-- Summary Cards -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-5 shadow-sm border-l-4 border-green-500">
                <p class="text-xs font-bold text-[#618968] uppercase mb-1">Total Uang Tunai (Cash)</p>
                <h3 class="text-2xl font-black text-[#078825] dark:text-green-400">Rp {{ number_format($totalCash, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-gray-500 mt-1">*Tunai & Tabungan</p>
            </div>

            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-5 shadow-sm border-l-4 border-yellow-500">
                <p class="text-xs font-bold text-yellow-700 dark:text-yellow-500 uppercase mb-1">Total Subsidi (Beasiswa)</p>
                <h3 class="text-2xl font-black text-yellow-600 dark:text-yellow-400">Rp {{ number_format($totalSubsidi, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-gray-500 mt-1">*Non-Tunai</p>
            </div>

            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-5 shadow-sm border-l-4 border-blue-500">
                <p class="text-xs font-bold text-blue-700 dark:text-blue-500 uppercase mb-1">Total Nilai Transaksi</p>
                <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400">Rp {{ number_format($totalPemasukanSantri, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-gray-500 mt-1">*Cash + Subsidi</p>
            </div>
            @foreach($summary as $category => $total)
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-5 shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d]">
                <p class="text-xs font-bold text-[#618968] uppercase mb-1">{{ $category }}</p>
                <h3 class="text-xl font-bold text-[#111812] dark:text-white">Rp {{ number_format($total, 0, ',', '.') }}</h3>
            </div>
            @endforeach
        </div>

        <!-- Transaction Table (SCREEN VIEW - Chronological) -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] overflow-hidden print:hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-[#1e3a24]">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Santri</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Level</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Kelas</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Pembayaran</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                        @forelse($transaksis as $t)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3b25] transition-colors">
                                <td class="px-6 py-4 text-sm text-[#111812] dark:text-white whitespace-nowrap">
                                    {{ $t->created_at->format('d M Y') }}
                                    <div class="text-[10px] text-gray-400 font-normal">{{ $t->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-[#111812] dark:text-white">
                                    {{ $t->tagihan->santri->nama ?? '-' }}
                                    <div class="text-xs text-gray-400">{{ $t->tagihan->santri->nis ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#618968]">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $t->tagihan->santri->kelas->level->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#618968]">
                                    {{ $t->tagihan->santri->kelas->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary/20 text-primary-dark dark:text-primary">
                                        {{ $t->tagihan->jenisBiaya->nama ?? '-' }}
                                    </span>
                                    @if($t->metode_pembayaran == 'Subsidi')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 ml-1">
                                            SUBSIDI
                                        </span>
                                    @endif
                                    @if($t->keterangan && $t->keterangan != $t->tagihan->jenisBiaya->nama)
                                        <div class="text-[11px] text-gray-400 mt-0.5">{{ Str::limit($t->keterangan, 30) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-[#111812] dark:text-white text-right">
                                    Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">payments</span>
                                    <p>Tidak ada data pembayaran santri pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[#dbe6dd] dark:border-[#2a3a2d]">
                {{ $transaksis->links() }}
            </div>
        </div>

        <!-- Transaction Table (PRINT VIEW - Summary Mode) -->
        <div class="hidden print:block space-y-6">
            @if(isset($printRecap) && $printRecap->count() > 0)
                <div>
                    <h3 class="font-bold text-lg uppercase mb-2">REKAPITULASI PENERIMAAN (KAS)</h3>
                    <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                        <thead>
                            <tr class="bg-gray-100 border-b border-black print-color-exact">
                                <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Kategori</th>
                                <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Keterangan</th>
                                <th class="py-2 px-2 text-center font-bold uppercase">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentCat = ''; @endphp
                            @foreach($printRecap as $item)
                                <tr class="border-b border-black/50">
                                    <td class="py-1 px-4 align-top border-r border-black font-bold">
                                        @if($currentCat != $item['category'])
                                            {{ $item['category'] }}
                                            @php $currentCat = $item['category']; @endphp
                                        @endif
                                    </td>
                                    <td class="py-1 px-4 align-top border-r border-black">{{ $item['description'] }}</td>
                                    <td class="py-1 px-4 align-top text-right">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-200 font-black border-t-2 border-black print-color-exact">
                                <td colspan="2" class="py-2 px-4 text-right uppercase border-r border-black">TOTAL PENERIMAAN TUNAI</td>
                                <td class="py-2 px-4 text-right">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($totalSubsidi > 0)
                <div class="mt-6">
                    <h3 class="font-bold text-lg uppercase mb-2 text-gray-600">INFORMASI SUBSIDI (NON-TUNAI)</h3>
                    <div class="border border-black p-4">
                        <div class="flex justify-between items-center">
                            <span class="font-bold uppercase">Total Subsidi Diberikan</span>
                            <span class="font-bold text-xl">Rp {{ number_format($totalSubsidi, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs italic mt-1">*Angka subsidi tidak masuk dalam hitungan Kas Sekolah.</p>
                    </div>
                </div>
                @endif

            @else
                <p class="text-center italic py-4">Tidak ada data pembayaran.</p>
            @endif
        </div>

        <!-- Signature Section (Visible Only in Print) -->
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

