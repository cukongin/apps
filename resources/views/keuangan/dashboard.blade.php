<x-app-layout>
    <div class="max-w-7xl mx-auto flex flex-col gap-8 text-[#111812] dark:text-white">
        <!-- Welcome & Date -->
        <div class="flex flex-col md:flex-row justify-between md:items-end gap-4">
            <div>
                <h3 class="text-2xl font-bold">Assalamu'alaikum, {{ Auth::user()->name }}</h3>
                <p class="text-[#618968] dark:text-[#8ab592]">Berikut ringkasan keuangan madrasah hari ini.</p>
            </div>

            <!-- Filter Year -->
            <form method="GET" action="{{ route('keuangan.dashboard') }}" class="flex items-center gap-2">
                <input type="date" value="{{ date('Y-m-d') }}" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" disabled>
            </form>
        </div>

        <!-- 1. MAIN ACTIONS (Colorful Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pembayaran -->
            <a href="{{ route('keuangan.pembayaran.index') }}" class="relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] text-white hover:shadow-lg hover:scale-[1.02] transition-all group">
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div class="p-3 bg-white/20 w-fit rounded-xl mb-4 group-hover:bg-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75a.75.75 0 0 1-.75-.75V15m.75 0H3m0 0a.75.75 0 0 0-.75.75H2.25" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold">Pembayaran</h4>
                        <p class="text-white/80 text-sm">SPP & Tagihan</p>
                    </div>
                </div>
                <!-- Decor -->
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            </a>

            <!-- Pemasukan -->
            <a href="{{ route('keuangan.pemasukan.index') }}" class="relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br from-[#14B8A6] to-[#0F766E] text-white hover:shadow-lg hover:scale-[1.02] transition-all group">
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div class="p-3 bg-white/20 w-fit rounded-xl mb-4 group-hover:bg-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold">Pemasukan</h4>
                        <p class="text-white/80 text-sm">Sumber Lain</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            </a>

            <!-- Pengeluaran -->
            <a href="{{ route('keuangan.pengeluaran.index') }}" class="relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br from-[#F43F5E] to-[#E11D48] text-white hover:shadow-lg hover:scale-[1.02] transition-all group">
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div class="p-3 bg-white/20 w-fit rounded-xl mb-4 group-hover:bg-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.307a11.95 11.95 0 0 1 5.814-5.519l2.74-1.22m0 0-5.94-2.28m5.94 2.28-2.28 5.941" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold">Pengeluaran <span class="text-lg">↗</span></h4>
                        <p class="text-white/80 text-sm">Catat Operasional</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            </a>

            <!-- Laporan -->
            <a href="{{ route('keuangan.laporan.index') }}" class="relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br from-[#10B981] to-[#047857] text-white hover:shadow-lg hover:scale-[1.02] transition-all group">
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div class="p-3 bg-white/20 w-fit rounded-xl mb-4 group-hover:bg-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold">Laporan</h4>
                        <p class="text-white/80 text-sm">Rekap Arus Kas</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            </a>
        </div>

        <!-- 2. STATISTICS DETAIL -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Saldo Kas -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-2">
                <div class="flex items-center gap-3 text-blue-500 mb-2">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75a.75.75 0 0 1-.75-.75V15m.75 0H3m0 0a.75.75 0 0 0-.75.75H2.25" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">SALDO KAS</span>
                </div>
                <h3 class="text-2xl font-bold">Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}</h3>
            </div>

            <!-- Pemasukan SPP -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-2">
                <div class="flex items-center gap-3 text-green-500 mb-2">
                    <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">PEMASUKAN SPP</span>
                </div>
                <h3 class="text-2xl font-bold">Rp {{ number_format($pemasukanSPP, 0, ',', '.') }}</h3>
            </div>

            <!-- Pemasukan Lain -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-2">
                <div class="flex items-center gap-3 text-teal-500 mb-2">
                    <div class="p-2 bg-teal-50 dark:bg-teal-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">PEMASUKAN LAIN</span>
                </div>
                <h3 class="text-2xl font-bold">Rp {{ number_format($totalPemasukanLain, 0, ',', '.') }}</h3>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-2">
                <div class="flex items-center gap-3 text-red-500 mb-2">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">PENGELUARAN</span>
                </div>
                <h3 class="text-2xl font-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
            </div>

             <!-- Diskon -->
             <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-2">
                <div class="flex items-center gap-3 text-purple-500 mb-2">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a2.25 2.25 0 0 0 2.634-2.66 2.25 2.25 0 0 0 .399-2.58l-2.319-4.64a2.25 2.25 0 0 0-1.607-1.19l-4.821-.965a2.25 2.25 0 0 0-1.6-.288Z" />
                        </svg>
                    </div>
                     <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">TOTAL DISKON</span>
                </div>
                <h3 class="text-2xl font-bold">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- 3. CHART SECTION -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                     <h3 class="text-xl font-bold mb-1">Tren Keuangan Bulanan</h3>
                     <p class="text-gray-500 text-sm">Perbandingan Pemasukan vs Pengeluaran</p>
                </div>
                <form method="GET" action="{{ route('keuangan.dashboard') }}">
                     <select name="filter_year" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="h-80 w-full relative">
                <canvas id="financeChart"></canvas>
            </div>

            <div class="flex justify-end gap-6 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[#12B76A]"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pemasukan</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[#F04438]"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pengeluaran</span>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('financeChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: @json($chartData['income']),
                            backgroundColor: '#12B76A',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Pengeluaran',
                            data: @json($chartData['expense']),
                            backgroundColor: '#F04438',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1e293b',
                            bodyColor: '#475569',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return value / 1000000 + 'Jt';
                                    if (value >= 1000) return value / 1000 + 'Rb';
                                    return value;
                                },
                                font: { size: 10 },
                                color: '#94a3b8'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10 },
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
