@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">analytics</span>
                Dashboard Akademik
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Tahun Ajaran Aktif: <span class="font-bold text-primary bg-primary/10 px-2 py-0.5 rounded">{{ $activeYear->nama }}</span>
            </p>
        </div>
        <div>
            <!-- Date Filter or Actions could go here -->
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Siswa -->
        <div class="card-boss !p-5 flex items-center gap-4 group hover:-translate-y-1 transition-transform">
            <div class="size-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-3xl">school</span>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $countSiswa }}</p>
            </div>
        </div>

        <!-- Guru -->
        <div class="card-boss !p-5 flex items-center gap-4 group hover:-translate-y-1 transition-transform">
            <div class="size-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-3xl">person_apron</span>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Guru</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $countGuru }}</p>
            </div>
        </div>

        <!-- Kelas -->
        <div class="card-boss !p-5 flex items-center gap-4 group hover:-translate-y-1 transition-transform">
            <div class="size-14 rounded-2xl bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center text-violet-600 dark:text-violet-400 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-3xl">meeting_room</span>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rombel Aktif</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $countKelas }}</p>
            </div>
        </div>

        <!-- Mapel -->
        <div class="card-boss !p-5 flex items-center gap-4 group hover:-translate-y-1 transition-transform">
            <div class="size-14 rounded-2xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-3xl">menu_book</span>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mata Pelajaran</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $countMapel }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grade Distribution -->
        <div class="card-boss !p-6 flex flex-col h-[400px]">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">pie_chart</span> Distribusi Predikat Nilai
            </h3>
            <div class="relative flex-1 w-full min-h-0">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>

        <!-- Class Performance -->
        <div class="card-boss !p-6 flex flex-col h-[400px]">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">bar_chart</span> Rata-Rata Nilai per Kelas
            </h3>
            <div class="relative flex-1 w-full min-h-0">
                <canvas id="classChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Progress Table -->
    @if(isset($progressData) && count($progressData) > 0)
    <div class="card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">checklist</span> Progress Penilaian per Kelas
            </h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">Kelas</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">Wali Kelas</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 text-center">Total Siswa</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">Progress</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($progressData as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $item['kelas'] }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $item['wali_kelas'] }}</td>
                        <td class="px-6 py-4 text-center font-bold text-slate-700 dark:text-slate-300">{{ $item['total_siswa'] }}</td>
                        <td class="px-6 py-4 w-1/3">
                            <div class="flex items-center gap-3">
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-primary h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $item['percentage'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 w-8 text-right">{{ $item['percentage'] }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($item['percentage'] < 100)
                            <form action="{{ route('dashboard.remind') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="wali_id" value="{{ $item['wali_id'] }}">
                                <input type="hidden" name="kelas_name" value="{{ $item['kelas'] }}">
                                <button type="submit" class="text-amber-600 hover:text-amber-700 dark:text-amber-500 dark:hover:text-amber-400 font-bold text-xs flex items-center gap-1 justify-end ml-auto bg-amber-50 dark:bg-amber-900/20 px-3 py-1.5 rounded-lg border border-amber-200 dark:border-amber-800 transition-colors" title="Ingatkan Wali Kelas">
                                    <span class="material-symbols-outlined text-[16px]">notifications_active</span> Ingatkan
                                </button>
                            </form>
                            @else
                                <span class="text-emerald-500 flex items-center justify-end gap-1 text-xs font-bold bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Komplit
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data from Controller
    const predikatData = @json($chartPredikat);
    const classData = @json($classPerformance);

    // Check dark mode
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#cbd5e1' : '#64748b';
    const gridColor = isDark ? '#334155' : '#f1f5f9';

    // 1. Grade Distribution Pie
    const ctxGrade = document.getElementById('gradeChart').getContext('2d');
    new Chart(ctxGrade, {
        type: 'doughnut',
        data: {
            labels: ['Excellent (A)', 'Good (B)', 'Average (C)', 'Poor (D)'],
            datasets: [{
                data: [predikatData.A, predikatData.B, predikatData.C, predikatData.D],
                backgroundColor: ['#10b981', '#14b8a6', '#f59e0b', '#f43f5e'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 600 } }
                }
            },
            cutout: '70%',
            radius: '90%'
        }
    });

    // 2. Class Performance Bar
    const ctxClass = document.getElementById('classChart').getContext('2d');
    new Chart(ctxClass, {
        type: 'bar',
        data: {
            labels: classData.map(c => c.nama_kelas),
            datasets: [{
                label: 'Rata-Rata Kelas',
                data: classData.map(c => c.average),
                backgroundColor: '#006241',
                borderRadius: 6,
                barThickness: 30
            }]
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
                legend: { display: false }
            }
        }
    });
</script>
@endsection
