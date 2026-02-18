@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@section('content')
    <div class="max-w-7xl mx-auto flex flex-col gap-8 pt-6">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex flex-col gap-2">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    Halo, {{ auth()->user()->name }}
                    <span class="material-symbols-outlined text-amber-500 animate-bounce">savings</span>
                </h2>
                <p class="text-slate-500 dark:text-slate-400">
                    Laporan Keuangan & Arus Kas Madrasah
                    <span class="font-bold text-primary">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('keuangan.transaksi.create') }}" class="btn-boss btn-primary shadow-lg shadow-primary/30">
                    <span class="material-symbols-outlined absolute left-4">add_circle</span>
                    Input Transaksi
                </a>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Pemasukan -->
            <div class="card-boss p-6 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-all duration-500 transform group-hover:scale-110 group-hover:rotate-12">
                    <span class="material-symbols-outlined text-9xl text-emerald-600">trending_up</span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-emerald-100/50 text-emerald-600 rounded-lg">
                            <span class="material-symbols-outlined">trending_up</span>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pemasukan</p>
                    </div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        Rp {{ number_format($summary['total_masuk'], 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-emerald-600 font-bold mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Bulan Ini
                    </p>
                </div>
            </div>

            <!-- Pengeluaran -->
            <div class="card-boss p-6 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-all duration-500 transform group-hover:scale-110 group-hover:rotate-12">
                    <span class="material-symbols-outlined text-9xl text-red-600">trending_down</span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-red-100/50 text-red-600 rounded-lg">
                            <span class="material-symbols-outlined">trending_down</span>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pengeluaran</p>
                    </div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        Rp {{ number_format($summary['total_keluar'], 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-red-600 font-bold mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        Bulan Ini
                    </p>
                </div>
            </div>

            <!-- Saldo -->
            <div class="col-span-1 relative overflow-hidden rounded-2xl p-6 text-white shadow-xl shadow-blue-600/20 group">
                <!-- Background Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-800 z-0"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 z-0 mix-blend-overlay"></div>

                <div class="absolute -right-8 -bottom-8 opacity-10 group-hover:opacity-20 transition-transform duration-700 group-hover:scale-110">
                    <span class="material-symbols-outlined text-[150px]">account_balance_wallet</span>
                </div>

                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-blue-100 text-xs font-bold uppercase tracking-widest">Saldo Bersih (Net)</p>
                            <div class="bg-white/10 backdrop-blur-md p-1.5 rounded-lg border border-white/20">
                                <span class="material-symbols-outlined text-white text-sm">account_balance</span>
                            </div>
                        </div>
                        <h3 class="text-4xl font-black tracking-tight">
                            Rp {{ number_format($summary['saldo_net'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between">
                         <span class="text-xs font-medium text-blue-100">Posisi Keuangan Saat Ini</span>
                         @if($summary['saldo_net'] >= 0)
                            <span class="px-2 py-1 bg-emerald-400/20 text-emerald-100 rounded text-[10px] font-bold border border-emerald-400/30">
                                SURPLUS
                            </span>
                         @else
                            <span class="px-2 py-1 bg-red-400/20 text-red-100 rounded text-[10px] font-bold border border-red-400/30">
                                DEFISIT
                            </span>
                         @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Charts & Secondary Info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Main Chart -->
            <div class="lg:col-span-2 card-boss p-6">
                <div class="flex items-center justify-between mb-6">
                     <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">monitoring</span>
                        Arus Kas Bulan Ini
                     </h3>
                     <a href="{{ route('keuangan.laporan.harian') }}" class="text-xs font-bold text-primary hover:underline">Lihat Detail Laporan</a>
                </div>
                <div class="h-[300px] w-full relative">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>

            <!-- Side Widgets -->
            <div class="flex flex-col gap-6">

                <!-- Tunggakan Widget -->
                <div class="card-boss p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold text-slate-500 uppercase">Total Tunggakan Siswa</p>
                        <span class="material-symbols-outlined text-orange-500">pending_actions</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white">
                        Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Akumulasi tagihan belum lunas.</p>
                    <div class="mt-4">
                        <a href="{{ route('keuangan.laporan.tunggakan') }}" class="btn-boss w-full btn-secondary text-xs justify-center">
                            Cek Rincian Tunggakan
                        </a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card-boss p-6">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Menu Cepat</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('keuangan.laporan.pengeluaran') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-white border border-slate-100 dark:border-slate-700 shadow-sm transition-all group">
                            <span class="material-symbols-outlined text-red-500 mb-2 group-hover:scale-110 transition-transform">receipt_long</span>
                            <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 text-center">Pengeluaran</span>
                        </a>
                        <a href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-white border border-slate-100 dark:border-slate-700 shadow-sm transition-all group">
                            <span class="material-symbols-outlined text-blue-500 mb-2 group-hover:scale-110 transition-transform">description</span>
                            <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 text-center">Rekap Tahunan</span>
                        </a>
                         <a href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-white border border-slate-100 dark:border-slate-700 shadow-sm transition-all group">
                            <span class="material-symbols-outlined text-emerald-500 mb-2 group-hover:scale-110 transition-transform">calculate</span>
                            <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 text-center">Kalkulator</span>
                        </a>
                         <a href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-white border border-slate-100 dark:border-slate-700 shadow-sm transition-all group">
                            <span class="material-symbols-outlined text-purple-500 mb-2 group-hover:scale-110 transition-transform">settings</span>
                            <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 text-center">Pengaturan</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Chart Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cashflowChart').getContext('2d');

            // Gradient for Income
            let gradientIn = ctx.createLinearGradient(0, 0, 0, 300);
            gradientIn.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald
            gradientIn.addColorStop(1, 'rgba(16, 185, 129, 0)');

             // Gradient for Expense
            let gradientOut = ctx.createLinearGradient(0, 0, 0, 300);
            gradientOut.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); // Red
            gradientOut.addColorStop(1, 'rgba(239, 68, 68, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: {!! json_encode($chartData['income']) !!},
                            borderColor: '#10b981',
                            backgroundColor: gradientIn,
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($chartData['expense']) !!},
                            borderColor: '#ef4444',
                            backgroundColor: gradientOut,
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(30, 41, 59, 0.9)',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 },
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
                                color: 'rgba(148, 163, 184, 0.1)',
                                borderDash: [4, 4]
                            },
                             ticks: {
                                callback: function(value, index, values) {
                                    if(value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + ' Jt';
                                    if(value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + ' Rb';
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
