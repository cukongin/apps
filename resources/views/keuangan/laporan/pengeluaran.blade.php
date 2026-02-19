<x-app-layout>
    <x-slot name="header">
        Laporan Pengeluaran
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

    <div class="max-w-7xl mx-auto px-6 py-8 print:p-0 print:max-w-none">

        <!-- Print Header -->
        <div class="hidden print:block mb-4 text-center">
            <x-kop-laporan />
            <h1 class="text-xl font-bold uppercase text-black mb-1">LAPORAN PENGELUARAN</h1>
            <p class="text-sm text-black font-medium uppercase">
                Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
            </p>
        </div>

        <!-- Filter Section -->
        <div class="print:hidden bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
            <form action="{{ route('keuangan.laporan.pengeluaran') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
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
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-5 shadow-sm border-l-4 border-red-500">
                <p class="text-xs font-bold text-[#618968] uppercase mb-1">Total Pengeluaran</p>
                <h3 class="text-2xl font-black text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-1">Periode ini</p>
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
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Kategori</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                        @forelse($pengeluarans as $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3b25] transition-colors">
                                <td class="px-6 py-4 text-sm text-[#111812] dark:text-white whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($p->tanggal_pengeluaran)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary/20 text-primary-dark dark:text-primary">
                                        {{ $p->kategori }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#111812] dark:text-white max-w-sm truncate">
                                    <span class="font-bold">{{ $p->judul }}</span> - {{ $p->deskripsi }}
                                </td>
                                <td class="px-6 py-4 text-sm text-[#111812] dark:text-white text-right font-bold">
                                    Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">receipt_long</span>
                                    <p>Tidak ada pengeluaran pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-[#dbe6dd] dark:border-[#2a3a2d]">
                {{ $pengeluarans->links() }}
            </div>
        </div>

        <!-- Expense Table (PRINT VIEW - Summary Mode) -->
        <div class="hidden print:block space-y-6">
            @if(isset($printRecap) && $printRecap->count() > 0)
                <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black print-color-exact">
                            <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Kategori</th>
                            <th class="py-2 px-2 text-center font-bold uppercase border-r border-black">Jumlah Transaksi</th>
                            <th class="py-2 px-2 text-center font-bold uppercase">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($printRecap as $item)
                        <tr class="border-b border-black/50">
                            <td class="py-2 px-4 align-top border-r border-black font-bold uppercase">{{ $item['category'] }}</td>
                            <td class="py-2 px-4 align-top border-r border-black text-center">{{ $item['count'] }} Item</td>
                            <td class="py-2 px-4 align-top text-right font-bold">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-200 font-black border-t-2 border-black print-color-exact">
                            <td colspan="2" class="py-2 px-4 text-right uppercase border-r border-black">TOTAL PENGELUARAN</td>
                            <td class="py-2 px-4 text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p class="text-center italic py-4">Tidak ada pengeluaran.</p>
            @endif
        </div>

        <!-- Expense Table (PRINT VIEW - Detailed List) -->
        <div class="hidden print:block space-y-6 mt-8">
            <h3 class="font-bold text-lg uppercase mb-2 border-b-2 border-black inline-block">RINCIAN TRANSAKSI</h3>
            <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                <thead>
                    <tr class="border-b border-black">
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 17%; white-space: nowrap;">Tanggal</th>
                        <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 100%;">Keterangan Pengeluaran</th>
                        <th class="py-2 px-2 text-center font-bold uppercase" style="width: 1%; white-space: nowrap;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedPengeluarans as $category => $items)
                        <!-- Category Header Row -->
                        <tr class="bg-gray-100 border-b border-black">
                            <td colspan="3" class="py-1 px-2 font-bold uppercase">{{ $category }}</td>
                        </tr>

                        <!-- Items -->
                        @foreach($items as $item)
                        <tr class="border-b border-black/50">
                            <td class="py-1 px-2 align-top border-r border-black" style="white-space: nowrap;">{{ \Carbon\Carbon::parse($item->tanggal_pengeluaran)->format('d M Y') }}</td>
                            <td class="py-1 px-2 align-top border-r border-black">
                                <div>
                                    <span class="font-bold">{{ $item->judul }}</span>
                                    @if($item->deskripsi && $item->deskripsi != '-')
                                        - <span class="italic text-gray-600 print:text-black">{{ $item->deskripsi }}</span>
                                    @endif
                                </div>

                                <!-- Detailed Items -->
                                @if($item->details->count() > 0)
                                    <table class="w-full text-xs mt-1 border-t border-black/30">
                                        @foreach($item->details as $d)
                                            {{-- Skip redundant details from Simple Mode --}}
                                            @if($d->jumlah == 1 && $d->satuan == '-' && $d->nama_barang == $item->judul)
                                                @continue
                                            @endif

                                        <tr>
                                            <td class="py-0 px-0 pl-2 text-gray-600 print:text-black/80" style="width: 10px;">-</td>
                                            <td class="py-0 px-0">{{ $d->nama_barang }}</td>
                                            <td class="py-0 px-0 text-right w-16" style="white-space: nowrap;">
                                                @if(!($d->jumlah == 1 && $d->satuan == '-'))
                                                    {{ $d->jumlah }} {{ $d->satuan }} x {{ number_format($d->harga_satuan, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td class="py-0 px-0 text-right w-20 font-medium border-l border-black/10 pl-1">
                                                = {{ number_format($d->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </td>
                            <td class="py-1 px-2 align-top text-right" style="white-space: nowrap;">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach

                        <!-- Subtotal per Category -->
                        <tr class="border-t border-black font-bold bg-gray-50">
                            <td colspan="2" class="py-1 px-2 text-right uppercase border-r border-black">Subtotal {{ $category }}:</td>
                            <td class="py-1 px-2 text-right text-black" style="white-space: nowrap;">
                                Rp {{ number_format($items->sum('jumlah'), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Grand Total -->
                    <tr class="border-t-2 border-black font-bold text-base">
                        <td colspan="2" class="py-2 px-2 text-right uppercase border-r border-black">TOTAL PENGELUARAN</td>
                        <td class="py-2 px-2 text-right text-red-600" style="white-space: nowrap;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
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

    <!-- Pre-calculate Receipt Count -->
    @php
        $totalReceipts = 0;
        foreach($groupedPengeluarans as $category => $items) {
            foreach($items as $item) {
                if($item->bukti_foto) {
                    $totalReceipts++;
                }
            }
        }
    @endphp

    <!-- LAMPIRAN NOTA (Halaman Baru - Only if receipts exist) -->
    @if($totalReceipts > 0)
    <div class="block print:break-before-page mt-8 w-full print:mt-2">
        <div class="mb-6 pt-8 print:pt-0 text-center">
            <h2 class="text-lg font-bold uppercase border-b-2 border-black inline-block pb-1">LAMPIRAN BUKTI TRANSAKSI</h2>
            <p class="text-xs mt-1 text-gray-600 print:text-black">{{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}</p>
        </div>

        <!-- Grid Responsive: 1 col mobile, 3 tablet, 4 desktop. Print: 2 cols fixed -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 print:border-none print:shadow-none print:p-0">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 print:grid-cols-2 gap-6 print:gap-2">
            @foreach($groupedPengeluarans as $category => $items)
                @foreach($items as $item)
                    @if($item->bukti_foto)
                        <div class="bg-white border border-gray-200 print:border-black p-2 rounded-lg shadow-sm print:shadow-none print:rounded-none flex flex-col break-inside-avoid print:h-[10.5cm] print:p-0">
                            <!-- Image Container -->
                            <div class="flex-grow overflow-hidden flex items-center justify-center bg-gray-50 print:bg-white border border-gray-100 print:border-gray-200 relative mb-2 rounded aspect-[3/4] print:aspect-auto print:h-full print:w-full print:mb-0 print:border-b print:border-black">
                                <img src="/bukti/{{ $item->bukti_foto }}"
                                     onload="rotateIfLandscape(this)"
                                     class="max-h-full max-w-full object-contain transition-transform duration-0 origin-center print:w-full print:h-full print:object-contain"
                                     alt="Struk">
                            </div>

                            <!-- Keterangan -->
                            <div class="text-xs border-t border-gray-100 print:border-black pt-1 bg-white z-10 print:text-[10pt]">
                                <div class="font-bold text-sm truncate text-[#111812] print:text-black">{{ $item->judul }}</div>
                                <div class="flex justify-between mt-1 text-gray-600 print:text-black font-medium">
                                    <span>{{ \Carbon\Carbon::parse($item->tanggal_pengeluaran)->format('d M Y') }}</span>
                                    <span class="font-bold text-[#111812] print:text-black">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
    </div>


    <script>
        function rotateIfLandscape(img) {
            // Check natural dimensions
            if (img.naturalWidth > img.naturalHeight) {
                // Get container dimensions
                var parent = img.parentElement;
                var containerW = parent.clientWidth;
                var containerH = parent.clientHeight;

                // Swap dimensions for the unrotated element
                // We want the unrotated image to act as if it is inside a container of size (H x W)
                img.style.width = containerH + 'px';
                img.style.height = containerW + 'px';

                // Reset constraints that might interfere
                img.style.maxWidth = "none";
                img.style.maxHeight = "none";

                // Ensure image fits inside this new "swapped" box
                img.style.objectFit = "contain";

                // Rotate
                img.style.transform = "rotate(-90deg)";
            }
        }
    </script>
    @endif
</x-app-layout>

