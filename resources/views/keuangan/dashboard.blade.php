<x-app-layout>
    <div class="max-w-7xl mx-auto flex flex-col gap-8 text-[#111812] dark:text-white">
        <!-- Welcome & Date -->
        <div class="flex flex-col md:flex-row justify-between md:items-end gap-4">
            <div>
                <h3 class="text-2xl font-bold">Assalamu'alaikum, {{ Auth::user()->name }}</h3>
                <p class="text-[#618968] dark:text-[#8ab592]">Berikut ringkasan keuangan madrasah hari ini.</p>
            </div>
            <div class="flex items-center gap-2 bg-white dark:bg-[#1a2e1d] px-4 py-2 rounded-lg shadow-sm border border-[#f0f4f1] dark:border-[#2a452e]">
                <span class="material-symbols-outlined text-primary text-sm">calendar_today</span>
                <span class="text-sm font-medium">{{ now()->format('d F Y') }}</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <!-- Quick Actions (Important Buttons) -->
        <!-- Quick Actions (Important Buttons) - Super Beautiful Version -->
        <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @if(in_array(Auth::user()->role, ['admin_utama', 'bendahara', 'staf_keuangan']))
            <a href="{{ route('keuangan.pembayaran.index') }}" class="group relative overflow-hidden rounded-3xl p-6 h-full transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-indigo-500/30">
                <!-- Background Gradient & Glass -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#4f46e5] to-[#7c3aed] opacity-100 transition-opacity"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>

                <!-- Decorative Shapes -->
                <div class="absolute top-[-20%] right-[-10%] w-32 h-32 bg-white/20 rounded-full blur-3xl group-hover:bg-white/30 transition-all duration-500"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-24 h-24 bg-purple-500/40 rounded-full blur-2xl group-hover:scale-125 transition-all duration-500"></div>

                <!-- Content -->
                <div class="relative z-10 flex flex-col h-full justify-between items-start gap-4">
                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="material-symbols-outlined text-3xl text-white drop-shadow-md">payments</span>
                    </div>
                    <div>
                        <h4 class="font-black text-xl text-white tracking-tight leading-none mb-1">Pembayaran</h4>
                        <p class="text-indigo-100 text-sm font-medium opacity-90">SPP & Tagihan</p>
                    </div>
                    <!-- Action Icon -->
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                        <span class="material-symbols-outlined text-white/80">arrow_forward</span>
                    </div>
                </div>
            </a>
            @endif

            @if(in_array(Auth::user()->role, ['admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']))
            <a href="{{ route('keuangan.pemasukan.index') }}" class="group relative overflow-hidden rounded-3xl p-6 h-full transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-teal-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-[#14b8a6] to-[#0d9488] opacity-100"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>

                <div class="absolute top-[-20%] right-[-10%] w-32 h-32 bg-white/20 rounded-full blur-3xl group-hover:bg-white/30 transition-all duration-500"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-24 h-24 bg-cyan-400/40 rounded-full blur-2xl group-hover:scale-125 transition-all duration-500"></div>

                <div class="relative z-10 flex flex-col h-full justify-between items-start gap-4">
                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="material-symbols-outlined text-3xl text-white drop-shadow-md">add_circle</span>
                    </div>
                    <div>
                        <h4 class="font-black text-xl text-white tracking-tight leading-none mb-1">Pemasukan</h4>
                        <p class="text-teal-100 text-sm font-medium opacity-90">Sumber Lain</p>
                    </div>
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                        <span class="material-symbols-outlined text-white/80">arrow_forward</span>
                    </div>
                </div>
            </a>
            @endif

            @if(in_array(Auth::user()->role, ['admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']))
            <a href="{{ route('keuangan.pengeluaran.index') }}" class="group relative overflow-hidden rounded-3xl p-6 h-full transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-rose-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-[#e11d48] to-[#f97316] opacity-100"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>

                <div class="absolute top-[-20%] right-[-10%] w-32 h-32 bg-white/20 rounded-full blur-3xl group-hover:bg-white/30 transition-all duration-500"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-24 h-24 bg-orange-500/40 rounded-full blur-2xl group-hover:scale-125 transition-all duration-500"></div>

                <div class="relative z-10 flex flex-col h-full justify-between items-start gap-4">
                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="material-symbols-outlined text-3xl text-white drop-shadow-md">outbound</span>
                    </div>
                    <div>
                        <h4 class="font-black text-xl text-white tracking-tight leading-none mb-1">Pengeluaran</h4>
                        <p class="text-rose-100 text-sm font-medium opacity-90">Catat Operasional</p>
                    </div>
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                        <span class="material-symbols-outlined text-white/80">arrow_forward</span>
                    </div>
                </div>
            </a>
            @endif

            @if(in_array(Auth::user()->role, ['admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']))
            <a href="{{ route('keuangan.laporan.index') }}" class="group relative overflow-hidden rounded-3xl p-6 h-full transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-[#059669] to-[#10b981] opacity-100"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>

                <div class="absolute top-[-20%] right-[-10%] w-32 h-32 bg-white/20 rounded-full blur-3xl group-hover:bg-white/30 transition-all duration-500"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-24 h-24 bg-teal-500/40 rounded-full blur-2xl group-hover:scale-125 transition-all duration-500"></div>

                <div class="relative z-10 flex flex-col h-full justify-between items-start gap-4">
                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="material-symbols-outlined text-3xl text-white drop-shadow-md">bar_chart</span>
                    </div>
                    <div>
                        <h4 class="font-black text-xl text-white tracking-tight leading-none mb-1">Laporan</h4>
                        <p class="text-emerald-100 text-sm font-medium opacity-90">Rekap Arus Kas</p>
                    </div>
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                        <span class="material-symbols-outlined text-white/80">arrow_forward</span>
                    </div>
                </div>
            </a>
            @endif

            @if(in_array(Auth::user()->role, ['admin_utama', 'teller_tabungan']))
            <a href="{{ route('keuangan.tabungan.index') }}" class="group relative overflow-hidden rounded-3xl p-6 h-full transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-amber-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-[#d97706] to-[#f59e0b] opacity-100"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>

                <div class="absolute top-[-20%] right-[-10%] w-32 h-32 bg-white/20 rounded-full blur-3xl group-hover:bg-white/30 transition-all duration-500"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-24 h-24 bg-yellow-500/40 rounded-full blur-2xl group-hover:scale-125 transition-all duration-500"></div>

                <div class="relative z-10 flex flex-col h-full justify-between items-start gap-4">
                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="material-symbols-outlined text-3xl text-white drop-shadow-md">savings</span>
                    </div>
                    <div>
                        <h4 class="font-black text-xl text-white tracking-tight leading-none mb-1">Tabungan</h4>
                        <p class="text-amber-100 text-sm font-medium opacity-90">Setor & Tarik</p>
                    </div>
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                        <span class="material-symbols-outlined text-white/80">arrow_forward</span>
                    </div>
                </div>
            </a>
            @endif
        </div>

        <!-- Stats Overview -->
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @if(in_array(Auth::user()->role, ['admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']))
            <!-- Saldo -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-2xl p-5 border border-slate-100 dark:border-[#2a452e] shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined">account_balance</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Saldo Kas</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}</h3>
                </div>
            </div>
            <!-- Pemasukan -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-2xl p-5 border border-slate-100 dark:border-[#2a452e] shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined">arrow_downward</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pemasukan SPP</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                </div>
            </div>
            <!-- Pemasukan Lain -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-2xl p-5 border border-slate-100 dark:border-[#2a452e] shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600 dark:text-teal-400">
                    <span class="material-symbols-outlined">add_circle</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pemasukan Lain</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Rp {{ number_format($totalPemasukanLain, 0, ',', '.') }}</h3>
                </div>
            </div>
            <!-- Pengeluaran -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-2xl p-5 border border-slate-100 dark:border-[#2a452e] shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <span class="material-symbols-outlined">arrow_upward</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pengeluaran</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                </div>
            </div>
            <!-- Diskon -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-2xl p-5 border border-slate-100 dark:border-[#2a452e] shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <span class="material-symbols-outlined">percent</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Diskon</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</h3>
                </div>
            </div>
            @endif

            @if(in_array(Auth::user()->role, ['admin_utama', 'teller_tabungan']))
            <!-- Tabungan -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-2xl p-5 border border-slate-100 dark:border-[#2a452e] shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <span class="material-symbols-outlined">savings</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Tabungan</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Rp {{ number_format($totalTabungan, 0, ',', '.') }}</h3>
                </div>
            </div>
            @endif
        </div>

        @if(in_array(Auth::user()->role, ['admin_utama', 'bendahara', 'staf_keuangan', 'staf_administrasi']))
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-[#f0f4f1] dark:border-[#2a452e]">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-lg font-bold">Tren Keuangan Bulanan</h3>
                    <p class="text-sm text-[#618968] dark:text-[#8ab592]">Perbandingan Pemasukan vs Pengeluaran</p>
                </div>

                <form action="{{ route('keuangan.dashboard') }}" method="GET" class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <select name="filter_year" onchange="this.form.submit()" class="bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white py-1.5 pl-3 pr-8 cursor-pointer">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Chart Container -->
            <div class="relative h-56 w-full">
                 <canvas id="financeChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Chart JS -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('financeChart');

            // Brand Colors
            const colorPrimary = '#13ec37'; // Green
            const colorDanger = '#ef4444';  // Red/Orange for Expense

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: {!! json_encode($chartData['income']) !!},
                            backgroundColor: colorPrimary,
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($chartData['expense']) !!},
                            backgroundColor: colorDanger,
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: {
                                    family: "'Manrope', sans-serif",
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1a2e1d',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f0f4f1',
                                drawBorder: false,
                            },
                            ticks: {
                                font: {
                                    family: "'Manrope', sans-serif",
                                    size: 10
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000) + 'Jt';
                                    } else if (value >= 1000) {
                                        return (value / 1000) + 'rb';
                                    }
                                    return value;
                                }
                            },
                            border: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Manrope', sans-serif",
                                    size: 11
                                }
                            },
                            border: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                }
            });
        </script>

        @if(in_array(Auth::user()->role, ['admin_utama', 'bendahara', 'staf_keuangan', 'staf_administrasi', 'kepala_madrasah']))
        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-[#f0f4f1] dark:border-[#2a452e] flex flex-col">
            <div class="p-6 border-b border-[#f0f4f1] dark:border-[#2a452e] flex justify-between items-center">
                <h3 class="text-lg font-bold">Transaksi Terbaru</h3>
                <button class="text-sm font-bold text-primary dark:text-primary hover:text-green-600 transition-colors">Lihat Semua</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-[#152418]">
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Nominal</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#2a452e]">
                        @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-[#203623] transition-colors">
                            <td class="p-4 text-sm whitespace-nowrap">{{ $tx->created_at->format('d M Y') }}</td>
                            <td class="p-4 text-sm font-medium">{{ $tx->keterangan }}<br><span class="text-xs text-gray-400">{{ $tx->tagihan->santri->nama ?? 'Umum' }}</span></td>
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ $tx->tagihan->jenisBiaya->nama ?? '-' }}</span>
                            </td>
                            <td class="p-4 text-sm font-bold text-right">Rp {{ number_format($tx->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Lunas/Bayar</span>
                            </td>
                            <td class="p-4 text-center">
                                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><span class="material-symbols-outlined text-[20px]">more_vert</span></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">Belum ada transaksi terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(in_array(Auth::user()->role, ['admin_utama', 'teller_tabungan']))
        <!-- Recent Tabungan Mutations -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-[#f0f4f1] dark:border-[#2a452e] flex flex-col">
            <div class="p-6 border-b border-[#f0f4f1] dark:border-[#2a452e] flex justify-between items-center">
                <h3 class="text-lg font-bold">Mutasi Tabungan Terakhir</h3>
                <a href="{{ route('keuangan.tabungan.index') }}" class="text-sm font-bold text-primary dark:text-primary hover:text-green-600 transition-colors">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-[#152418]">
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Santri</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Jenis</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Nominal</th>
                            <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#2a452e]">
                        @forelse($recentTabungan ?? [] as $tab)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-[#203623] transition-colors">
                            <td class="p-4 text-sm whitespace-nowrap">{{ $tab->created_at->format('d M Y') }}</td>
                            <td class="p-4 text-sm font-medium">{{ $tab->santri->nama ?? '-' }}</td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tab->tipe == 'setor' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($tab->tipe) }}
                                </span>
                            </td>
                            <td class="p-4 text-sm font-bold text-right {{ $tab->tipe == 'setor' ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($tab->jumlah, 0, ',', '.') }}
                            </td>
                             <td class="p-4 text-sm text-gray-500 hidden sm:table-cell">{{ $tab->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Belum ada mutasi tabungan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        <!-- Footer -->
        <div class="mt-8 text-center pb-4">
            <p class="text-xs text-gray-400 dark:text-gray-600">© {{ date('Y') }} Madrasah Nurul Ainy. All rights reserved.</p>
        </div>
    </div>
</x-app-layout>

