<x-app-layout>
    <x-slot name="header">
        Laporan Kas (Buku Kas Umum)
    </x-slot>

    <style>
        @media print {
            @page {
                margin: 1.5cm;
                size: auto; /* auto is better for diverse paper sizes, or use 'A4 portrait' */
            }
            @page :first {
                margin-top: 0.5cm;
            }
            html, body {
                height: auto !important;
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            /* Reset Dashboard Layout Constraints */
            body > div, main, nav, aside {
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                display: block !important; /* Break flex locks */
            }
            /* Target the specific scrolling container in app.blade.php */
            main > div.overflow-y-auto {
                height: auto !important;
                overflow: visible !important;
                padding: 0 !important; /* Remove padding if needed, or keep for spacing */
            }
            body {
                font-family: sans-serif !important;
                font-size: 10pt !important;
                line-height: 1.5 !important;
            }
            /* Hide Sidebar and Header explicitly again just in case */
            aside, header, .no-print {
                display: none !important;
            }

            table, td, th {
                font-size: 10pt !important;
                padding-top: 4px !important;
                padding-bottom: 4px !important;
            }
            /* Force Grid for Summary Cards Horizontal Alignment (2 Columns now) */
            .print-grid-3 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 1.5rem !important;
            }

            .print-color-exact {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* Force Card Borders */
            .card-green { border-left-color: #16a34a !important; border-left-width: 4px !important; }
            .card-yellow { border-left-color: #ca8a04 !important; border-left-width: 4px !important; }
            .card-black { background-color: #111812 !important; color: white !important; }
        }
    </style>

    <div class="max-w-7xl mx-auto px-6 py-8 print:p-0 print:max-w-none">

        <!-- Print Header -->
        <div class="hidden print:block mb-4 text-center">
            <x-kop-laporan />
            <h1 class="text-xl font-bold uppercase text-black mb-1">LAPORAN REKAPITULASI BUKU KAS UMUM</h1>
            <p class="text-sm text-black font-medium uppercase">
                Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
            </p>
        </div>

        <!-- Filter Section -->
        <div class="print:hidden bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
            <form action="{{ route('keuangan.laporan.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                </div>
                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                </div>
                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined">filter_list</span>
                        Filter
                    </button>
                    <button type="button" onclick="window.print()" class="bg-[#f0f4f1] border border-[#dbe6dd] dark:bg-[#2a3a2d] dark:border-[#2a452e] hover:bg-gray-100 dark:hover:bg-[#203623] text-[#637588] dark:text-[#a0b0a3] font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined">print</span>
                        Cetak
                    </button>
                    <!-- Route to Santri Report (Using corrected route name if different, usually finance has prefix) -->
                    <!-- Assuming routes are 'keuangan.laporan.santri' per previous knowledge -->
                    <a href="{{ route('keuangan.laporan.santri') }}" class="bg-black text-white hover:bg-gray-800 font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined">description</span>
                        Laporan Rinci SPP/Santri
                    </a>
                </div>
            </form>
        </div>
        <!-- Chart Section (Screen Only) -->
        <div class="print:hidden bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
            <h3 class="text-lg font-bold text-[#111812] dark:text-white mb-4">Grafik Arus Kas (Harian)</h3>
            <div class="relative h-72 w-full">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Register the plugin globally or locally
                Chart.register(ChartDataLabels);

                const chartData = {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: @json($chartData['income']),
                            backgroundColor: '#16a34a', // Green-600
                            borderColor: '#16a34a',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Pengeluaran',
                            data: @json($chartData['expense']),
                            backgroundColor: '#dc2626', // Red-600
                            borderColor: '#dc2626',
                            borderWidth: 1,
                            borderRadius: 4,
                        }
                    ]
                };

                // 1. Screen Chart (No Data Labels, nice tooltips)
                const ctxScreen = document.getElementById('cashFlowChart').getContext('2d');
                new Chart(ctxScreen, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            datalabels: { display: false } // Hide on screen to keep it clean
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f3f4f6' },
                                ticks: { callback: function(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); } }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // 2. Print Chart (High Res, Data Labels, Color)
                if (document.getElementById('printCashFlowChart')) {
                    const ctxPrint = document.getElementById('printCashFlowChart').getContext('2d');

                    new Chart(ctxPrint, {
                        type: 'bar',
                        data: chartData, // Use original Green/Red data
                        options: {
                            animation: false,
                            responsive: true,
                            maintainAspectRatio: false,
                            devicePixelRatio: 2,
                            layout: {
                                padding: { top: 20 } // Space for labels
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#000',
                                        callback: function(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); }
                                    },
                                    grid: { color: '#e5e7eb' }
                                },
                                x: {
                                    ticks: { color: '#000' },
                                    grid: { display: false }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#000',
                                        font: { size: 10, family: 'sans-serif' },
                                        usePointStyle: true,
                                    }
                                },
                                tooltip: { enabled: false }, // No tooltips on print
                                datalabels: { // SHOW LABELS ON TOP
                                    display: true,
                                    color: '#000',
                                    anchor: 'end',
                                    align: 'top',
                                    formatter: function(value) {
                                        if (value === 0) return '';
                                        return new Intl.NumberFormat('id-ID').format(value);
                                    },
                                    font: {
                                        weight: 'bold',
                                        size: 9
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>

        <!-- Summary Cards (School Only) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 print:hidden">
            <!-- 1. School Revenue (Pendapatan Murni) -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-6 border border-[#dbe6dd] dark:border-[#2a3a2d] shadow-sm border-l-4 border-green-500 card-green print-color-exact">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg text-green-600 print-color-exact">
                        <span class="material-symbols-outlined text-2xl">account_balance</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Arus Kas Sekolah (SPP/Ops)</p>
                        <h3 class="text-2xl font-bold text-[#111812] dark:text-white mt-1">
                             Rp {{ number_format($financialSummary['saldo_net'], 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total Pendapatan:</span>
                        <span class="font-bold text-green-600">+Rp {{ number_format($financialSummary['pendapatan_sekolah'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total Pengeluaran:</span>
                        <span class="font-bold text-red-500">-Rp {{ number_format($financialSummary['pengeluaran_sekolah'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Subsidy (Informational) - HIDDEN ON PRINT -->
            <div class="print:hidden bg-white dark:bg-[#1a2e1d] rounded-xl p-6 border-l-4 border-yellow-500 shadow-sm card-yellow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-yellow-600">
                        <span class="material-symbols-outlined text-2xl">local_offer</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Diskon (Subsidi)</p>
                        <h3 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                             Rp {{ number_format($totalSubsidi ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                <div class="text-xs text-gray-400 italic mt-2">
                    *Nilai ini tidak masuk/mengurangi saldo kas sekolah (hanya pencatatan).
                </div>
            </div>

            <!-- 3. Cash Balance (Saldo Akhir) -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-6 shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] border-l-4 border-blue-500 card-blue print-color-exact">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600 print-color-exact">
                        <span class="material-symbols-outlined text-2xl">wallet</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Saldo Akhir Sekolah</p>
                        <h3 class="text-3xl font-bold text-[#111812] dark:text-white mt-1">
                            Rp {{ number_format($financialSummary['saldo_net'], 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                <!-- Mini Stats -->
                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-800 pt-4">
                     <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pemasukan Period Ini</p>
                        <p class="font-bold text-green-600">+Rp {{ number_format($financialSummary['total_masuk'], 0, ',', '.') }}</p>
                     </div>
                     <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pengeluaran Periode Ini</p>
                        <p class="font-bold text-red-500">-Rp {{ number_format($financialSummary['total_keluar'], 0, ',', '.') }}</p>
                     </div>
                </div>
            </div>
        </div>
        <!-- Ledger Table (SCREEN VIEW - Chronological) -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] overflow-hidden print:hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-[#1e3a24]">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Kategori</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase text-right">Debit (Masuk)</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase text-right">Kredit (Keluar)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                        @forelse($ledger as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3b25] transition-colors">
                            <td class="px-6 py-4 text-sm text-[#111812] dark:text-white whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $item['type'] == 'in' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $item['category'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#4a5568] dark:text-gray-300 max-w-xs truncate" title="{{ $item['description'] }}">
                                {{ $item['description'] }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-[#111812] dark:text-white text-right">
                                @if($item['type'] == 'in')
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-[#111812] dark:text-white text-right">
                                @if($item['type'] == 'out')
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">account_balance_wallet</span>
                                <p>Tidak ada transaksi pada periode ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-[#1e3a24] border-t-2 border-[#dbe6dd] dark:border-[#2a3a2d]">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-sm font-black text-[#111812] dark:text-white text-right uppercase">TOTAL</td>
                            <td class="px-6 py-3 text-sm font-black text-primary-dark dark:text-primary text-right">
                                Rp {{ number_format($financialSummary['total_masuk'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-sm font-black text-red-600 dark:text-red-400 text-right">
                                Rp {{ number_format($financialSummary['total_keluar'], 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-sm font-black text-[#111812] dark:text-white text-right uppercase">SALDO PERIODE INI</td>
                            <td colspan="2" class="px-6 py-3 text-sm font-black text-center {{ $financialSummary['saldo_net'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                Rp {{ number_format($financialSummary['saldo_net'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-[#dbe6dd] dark:border-[#2a3a2d]">
                {{ $ledger->links() }}
            </div>
        </div>

        <!-- RECAP TABLE (PRINT VIEW - Separated Pemasukan & Pengeluaran PER PERIOD) -->
        <div class="hidden print:block space-y-8">

             @php
                // Use the passed Period Recap variables
                $incomeGroups = ($printIncome ?? collect())->groupBy('category');
                $expenseGroups = ($printExpense ?? collect())->groupBy('category');
            @endphp

            <!-- I. PEMASUKAN TABLE (Recap) -->
            <div>
                <h3 class="font-bold text-lg uppercase mb-2">I. Pemasukan (Per Kategori & Kelas)</h3>
                <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black print-color-exact">
                            <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 50%;">Keterangan</th>
                            <th class="py-2 px-2 text-center font-bold uppercase" style="width: 50%;">Nominal Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomeGroups as $category => $items)
                            <tr class="bg-gray-50 border-b border-black print-color-exact">
                                <td colspan="2" class="py-1 px-4 font-bold uppercase italic border-r border-black">{{ $category }}</td>
                            </tr>
                            @foreach($items as $item)
                            <tr class="border-b border-black/50">
                                <td class="py-1 px-4 align-top border-r border-black">{{ $item['description'] }}</td>
                                <td class="py-1 px-4 align-top text-right font-bold">
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                            @php $subtotal = collect($items)->sum('amount'); @endphp
                            <tr class="border-t border-black bg-gray-50 print-color-exact">
                                <td class="py-1 px-4 text-right uppercase border-r border-black font-bold">Subtotal {{ $category }}:</td>
                                <td class="py-1 px-4 text-right font-bold">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                             <tr><td colspan="2" class="text-center py-2">Tidak ada pemasukan.</td></tr>
                        @endforelse
                        <tr class="border-t-2 border-black bg-gray-200 print-color-exact">
                            <td class="py-2 px-4 text-right uppercase border-r border-black font-black text-lg">TOTAL PEMASUKAN</td>
                            <td class="py-2 px-4 text-right font-black text-lg">
                                 Rp {{ number_format($financialSummary['total_masuk'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- II. PENGELUARAN TABLE (Recap) -->
            <div>
                <h3 class="font-bold text-lg uppercase mb-2 mt-6">II. Pengeluaran (Per Kategori & Item)</h3>
                <table class="w-full text-left border-collapse border border-black" style="table-layout: auto;">
                    <thead>
                         <tr class="bg-gray-100 border-b border-black print-color-exact">
                            <th class="py-2 px-2 text-center font-bold uppercase border-r border-black" style="width: 50%;">Keterangan</th>
                            <th class="py-2 px-2 text-center font-bold uppercase" style="width: 50%;">Nominal Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                         @forelse($expenseGroups as $category => $items)
                            <tr class="bg-gray-50 border-b border-black print-color-exact">
                                <td colspan="2" class="py-1 px-4 font-bold uppercase italic border-r border-black">{{ $category }}</td>
                            </tr>
                            @foreach($items as $item)
                            <tr class="border-b border-black/50">
                                <td class="py-1 px-4 align-top border-r border-black">{{ $item['description'] }}</td>
                                <td class="py-1 px-4 align-top text-right font-bold">
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                            @php $subtotal = collect($items)->sum('amount'); @endphp
                            <tr class="border-t border-black bg-gray-50 print-color-exact">
                                <td class="py-1 px-4 text-right uppercase border-r border-black font-bold">Subtotal {{ $category }}:</td>
                                <td class="py-1 px-4 text-right font-bold">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                         @empty
                             <tr><td colspan="2" class="text-center py-2">Tidak ada pengeluaran.</td></tr>
                        @endforelse
                        <tr class="border-t-2 border-black bg-gray-200 print-color-exact">
                            <td class="py-2 px-4 text-right uppercase border-r border-black font-black text-lg">TOTAL PENGELUARAN</td>
                            <td class="py-2 px-4 text-right font-black text-lg">
                                 Rp {{ number_format($financialSummary['total_keluar'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Balance Summary (Explicit Calculation) -->
            <div class="border-2 border-black p-4 mt-6 bg-white break-inside-avoid print-color-exact">
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-bold uppercase text-gray-600">Total Pemasukan (Real Cash)</span>
                        <span class="font-bold text-green-700 text-lg">
                            + Rp {{ number_format($financialSummary['total_masuk'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-bold uppercase text-gray-600">Total Pengeluaran</span>
                        <span class="font-bold text-red-700 text-lg">
                            - Rp {{ number_format($financialSummary['total_keluar'], 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Divider Line -->
                    <div class="border-b-2 border-black my-1"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-xl font-black uppercase">SALDO PERIODE INI (SURPLUS / DEFISIT)</span>
                        <span class="text-3xl font-black {{ $financialSummary['saldo_net'] >= 0 ? 'text-green-800' : 'text-red-800' }}">
                            Rp {{ number_format($financialSummary['saldo_net'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- III. SUBSIDI INFO (Summary Only) -->
            <div class="break-inside-avoid mt-8">
                <h3 class="font-bold text-lg uppercase mb-2 text-yellow-700">III. Informasi Subsidi / Keringanan Biaya</h3>
                <div class="border-2 border-yellow-500 p-4 bg-yellow-50 print-color-exact flex justify-between items-center">
                    <span class="font-bold uppercase text-yellow-800">Total Subsidi Diberikan Periode Ini</span>
                    <span class="text-xl font-black text-yellow-700">
                        Rp {{ number_format($totalSubsidi ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-xs italic mt-2 text-gray-600">* Rincian penerima subsidi dapat dilihat pada Laporan Rinci Per-Santri.</p>
                <p class="text-xs italic text-gray-600">* Nilai ini tidak masuk/mengurangi saldo kas sekolah (hanya sebagai pencatatan keringanan biaya).</p>
            </div>
        </div>

        <!-- IV. CHART SECTION (Print Only) -->
        <!-- Fix: Use off-screen positioning with FIXED WIDTH (A4 Safe Area) so Chart.js renders correct size -->
        <div class="break-inside-avoid mt-4 mb-2 border-2 border-dashed border-gray-300 p-4 absolute -left-[9000px] top-0 w-[18cm] print:static print:w-full print:block overflow-hidden">
            <h3 class="font-bold text-lg uppercase mb-2 text-center">IV. Grafik Arus Kas Periode Ini</h3>
            <div style="position: relative; height: 200px; width: 100%;">
                <canvas id="printCashFlowChart"></canvas>
            </div>
            <div class="mt-1 text-center text-xs text-gray-500 italic">
                * Grafik ini digenerate otomatis berdasarkan data transaksi harian.
            </div>
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
