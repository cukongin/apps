<x-app-layout>
    <x-slot name="header">
        Laporan Tunggakan (Piutang)
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
            <h1 class="text-xl font-bold uppercase text-black mb-1">LAPORAN TUNGGAKAN SISWA</h1>
            <p class="text-sm text-black font-medium uppercase">
                Per Tanggal: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </p>
        </div>

        <!-- Summary Cards by Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 print:hidden">
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-5 shadow-sm border-l-4 border-red-500">
                <p class="text-xs font-bold text-[#618968] uppercase mb-1">Total Tunggakan</p>
                <h3 class="text-2xl font-black text-red-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-1">Belum terbayar</p>
            </div>
            @foreach($summary as $category => $total)
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-5 shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d]">
                <p class="text-xs font-bold text-[#618968] uppercase mb-1">{{ $category }}</p>
                <h3 class="text-xl font-bold text-[#111812] dark:text-white">Rp {{ number_format($total, 0, ',', '.') }}</h3>
            </div>
            @endforeach
        </div>

        <!-- Filter & Sort UI -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-4 mb-6 print:hidden">
            <form action="{{ route('keuangan.laporan.tunggakan') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">

                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tingkatan</label>
                    <select name="tingkat" class="w-full md:w-40 rounded-lg border-gray-300 text-sm focus:ring-[#618968] focus:border-[#618968] dark:bg-[#2a3a2d] dark:border-[#2a452e] dark:text-white">
                        <option value="">Semua</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}" {{ request('tingkat') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full md:w-40 rounded-lg border-gray-300 text-sm focus:ring-[#618968] focus:border-[#618968] dark:bg-[#2a3a2d] dark:border-[#2a452e] dark:text-white">
                        <option value="">Semua</option>
                        @foreach($kelasOptions as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Urutan</label>
                    <select name="sort" class="w-full md:w-40 rounded-lg border-gray-300 text-sm focus:ring-[#618968] focus:border-[#618968] dark:bg-[#2a3a2d] dark:border-[#2a452e] dark:text-white">
                        <option value="total_desc" {{ request('sort') == 'total_desc' ? 'selected' : '' }}>Terbesar (Default)</option>
                        <option value="total_asc" {{ request('sort') == 'total_asc' ? 'selected' : '' }}>Terkecil</option>
                        <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="kelas_asc" {{ request('sort') == 'kelas_asc' ? 'selected' : '' }}>Kelas</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-[#1f2937] hover:bg-[#374151] text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('keuangan.laporan.tunggakan') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Class Arrears Accordion (SCREEN VIEW) -->
        <div class="space-y-4 print:hidden">
            <div class="flex justify-between items-center mb-4">
                 <div>
                    <h2 class="text-xl font-black text-[#111812] dark:text-white">Rekapitulasi Per Kelas</h2>
                    <p class="text-sm text-[#618968]">
                        @if(request('tingkat') || request('kelas_id'))
                            Menampilkan hasil filter.
                        @else
                            Menampilkan seluruh kelas yang memiliki tunggakan.
                        @endif
                    </p>
                </div>
                <button onclick="window.print()" class="btn-boss btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Cetak Laporan
                </button>
            </div>

            @forelse($classRecap as $class)
                <div x-data="{ expanded: false }" class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] overflow-hidden transition-all duration-300 hover:shadow-md">
                    <!-- Card Header (Clickable) -->
                    <div @click="expanded = !expanded" class="p-4 cursor-pointer flex justify-between items-center bg-gray-50/50 dark:bg-[#203623] hover:bg-gray-100 dark:hover:bg-[#2a452e] transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shadow-sm">
                                {{ substr($class['nama_kelas'], 0, 2) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-[#111812] dark:text-white">{{ $class['nama_kelas'] }}</h3>
                                <p class="text-xs text-gray-500 font-medium">{{ $class['student_count'] }} Santri Menunggak</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-xs text-gray-400 uppercase font-bold">Total Tunggakan</p>
                                <p class="text-lg font-black text-red-600">Rp {{ number_format($class['total_tunggakan'], 0, ',', '.') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''">expand_more</span>
                        </div>
                    </div>

                    <!-- Card Body (Expandable) -->
                    <div x-show="expanded" x-collapse class="border-t border-[#dbe6dd] dark:border-[#2a3a2d]">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-[#1e3a24]">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-bold text-[#618968] uppercase w-10">No</th>
                                    <th class="px-6 py-3 text-xs font-bold text-[#618968] uppercase">Nama Santri</th>
                                    <th class="px-6 py-3 text-xs font-bold text-[#618968] uppercase">Rincian</th>
                                    <th class="px-6 py-3 text-xs font-bold text-[#618968] uppercase text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                                @foreach($class['students'] as $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3b25] transition-colors">
                                        <td class="px-6 py-3 text-sm text-center text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-3 text-sm font-bold text-[#111812] dark:text-white">
                                            {{ $student['nama'] }}
                                            <span class="block text-xs text-gray-400 font-normal">NIS: {{ $student['nis'] ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-sm">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($student['bills'] as $bill)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">
                                                        {{ $bill->jenisBiaya->nama }} ({{ $bill->keterangan }}): {{ number_format($bill->jumlah - $bill->terbayar, 0, ',', '.') }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-sm font-bold text-red-600 text-right">
                                            Rp {{ number_format($student['total'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-12 text-center shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d]">
                    <span class="material-symbols-outlined text-6xl text-primary/20 mb-4">check_circle</span>
                    <h3 class="text-xl font-bold text-[#111812] dark:text-white">Tidak Ada Tunggakan</h3>
                    <p class="text-gray-500">Alhamdulillah, semua pembayaran telah lunas.</p>
                </div>
            @endforelse
        </div>

        <!-- Arrears Table (PRINT VIEW - Summary Mode) -->
        <div class="hidden print:block space-y-6">
            @if(isset($printRecap) && $printRecap->count() > 0)
                <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black print-color-exact">
                            <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Kelas</th>
                            <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Jumlah Siswa Menunggak</th>
                            <th class="py-2 px-2 text-center font-bold uppercase">Total Tunggakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($printRecap as $item)
                        <tr class="border-b border-black/50">
                            <td class="py-2 px-4 align-top border-r border-black font-bold uppercase">{{ $item['category'] }}</td>
                            <td class="py-2 px-4 align-top border-r border-black text-center">{{ $item['count'] }}</td>
                            <td class="py-2 px-4 align-top text-right font-bold">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-200 font-black border-t-2 border-black print-color-exact">
                            <td colspan="2" class="py-2 px-4 text-right uppercase border-r border-black">TOTAL TUNGGAKAN</td>
                            <td class="py-2 px-4 text-right text-red-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p class="text-center italic py-4">Tidak ada tunggakan siswa.</p>
            @endif
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

