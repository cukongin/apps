@extends('layouts.app')

@section('title', 'Analytics: ' . $student->nama_lengkap)

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="card-boss !p-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mb-1">Analisis Perkembangan Siswa</h1>
            <p class="text-slate-500 font-medium">{{ $student->nama_lengkap }} ({{ $student->nis_lokal }}) - <span class="text-primary">{{ $kelas->nama_kelas }}</span></p>
        </div>
        <a href="{{ route('reports.leger', ['class_id' => $kelas->id]) }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-bold flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Leger
        </a>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. Current Performance (Bar Chart) -->
        <div class="card-boss !p-6 flex flex-col h-[450px]">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                <div class="size-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <span class="material-symbols-outlined">bar_chart_4_bars</span>
                </div>
                Perbandingan Nilai: Rapor vs Murni
            </h3>
            <div class="relative flex-1 w-full min-h-0">
                <canvas id="gradeComparisonChart"></canvas>
            </div>
            <p class="text-xs text-center text-slate-400 mt-4 font-bold uppercase tracking-wider bg-slate-50 dark:bg-slate-800/50 py-2 rounded-lg">Periode: {{ $periode->nama_periode }}</p>
        </div>

        <!-- 2. Historical Trend (Line Chart) -->
        <div class="card-boss !p-6 flex flex-col h-[450px]">
             <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                <div class="size-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined">ssid_chart</span>
                </div>
                Tren Rata-Rata Nilai (GPA)
            </h3>
            <div class="relative flex-1 w-full min-h-0">
                <canvas id="trendChart"></canvas>
            </div>
            <p class="text-xs text-center text-slate-400 mt-4 font-bold uppercase tracking-wider bg-slate-50 dark:bg-slate-800/50 py-2 rounded-lg">Riwayat Akademik</p>
        </div>
    </div>

</div>

<!-- Auto-Import Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Data from Controller
        const mapels = @json($mapelNames);
        const finalGrades = @json($finalGrades);
        const originalGrades = @json($originalGrades);

        const periods = @json($periodNames);
        const trends = @json($gpaTrends);

        // Check dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#cbd5e1' : '#64748b';
        const gridColor = isDark ? '#334155' : '#f1f5f9';

        // 1. Comparison Chart
        const ctx1 = document.getElementById('gradeComparisonChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: mapels,
                datasets: [
                    {
                        label: 'Nilai Rapor',
                        data: finalGrades,
                        backgroundColor: '#6366f1', // Indigo
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Nilai Murni (Guru)',
                        data: originalGrades,
                        backgroundColor: '#fbbf24', // Amber
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 600 } }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 600 } }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#0f172a',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            afterBody: function(context) {
                                const idx = context[0].dataIndex;
                                const diff = finalGrades[idx] - originalGrades[idx];
                                return diff > 0 ? `Katrol: +${diff}` : '';
                            }
                        }
                    }
                }
            }
        });

        // 2. Trend Chart
        const ctx2 = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: periods,
                datasets: [{
                    label: 'Rata-Rata Nilai',
                    data: trends,
                    borderColor: '#10b981', // Emerald
                    backgroundColor: isDark ? 'rgba(16, 185, 129, 0.1)' : 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        min: 50,
                        max: 100,
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 600 } }
                    }
                },
                plugins: {
                     legend: {
                        labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 600 } }
                    }
                }
            }
        });
    });
</script>
@endsection
