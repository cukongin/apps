@extends('layouts.app')

@section('title', 'Global Monitoring Nilai')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header & Filters -->
    <div class="card-boss !p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">travel_explore</span>
                    Global Monitoring
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pantau progress penginputan nilai seluruh kelas.</p>
            </div>

            <!-- Unified Filter -->
            <form action="{{ route('tu.monitoring.global') }}" method="GET" class="w-full md:w-auto flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <input type="hidden" name="year_id" value="{{ $activeYear->id }}">

                <!-- Jenjang Selector -->
                <div class="relative group w-full md:w-auto">
                    <select name="jenjang" class="input-boss !pl-9 !pr-8 w-full md:min-w-[140px]" onchange="this.form.submit()">
                        <option value="" {{ empty(request('jenjang')) ? 'selected' : '' }}>Semua Jenjang</option>
                        @foreach(['MI', 'MTS'] as $j)
                            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">school</span>
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>

                <!-- Class Selector -->
                <div class="relative group w-full md:w-auto md:min-w-[200px]">
                    <select name="kelas_id" class="input-boss !pl-9 !pr-8 w-full" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @if(isset($allClasses))
                            @foreach($allClasses as $kls)
                                <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama_kelas }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">class</span>
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                         <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>

                <!-- Period Selector -->
                <div class="relative group w-full md:w-auto">
                    <select name="period_id" class="input-boss !pl-9 !pr-8 w-full md:min-w-[160px]" onchange="this.form.submit()">
                        <option value="all" {{ $selectedPeriodId == 'all' ? 'selected' : '' }}>Semua Periode</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ ($currentPeriod && $currentPeriod->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                         <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Content -->
    <div class="">

        @if(count($monitoringData) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($monitoringData as $data)
            <div class="card-boss !p-5 flex flex-col gap-4 relative overflow-hidden group hover:shadow-lg transition-all">
                <!-- Progress Background -->
                <div class="absolute bottom-0 left-0 h-1 bg-slate-100 dark:bg-slate-700 w-full">
                    <div class="h-full bg-{{ $data->color }}-500 transition-all duration-1000" style="width: {{ $data->progress }}%"></div>
                </div>

                <div class="flex justify-between items-start">
                    <div>
                        <a href="{{ route('reports.class.analytics', $data->class->id) }}" class="text-lg font-bold text-slate-900 dark:text-white hover:text-primary transition-colors underline-offset-4 hover:underline">
                            {{ $data->class->nama_kelas }}
                        </a>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-bold">{{ $data->class->wali_kelas->name ?? 'No Wali' }}</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        {{ $data->student_count }} Siswa
                    </span>
                </div>

                <div class="flex items-center gap-4 my-2">
                    <div class="relative size-16">
                        <svg class="size-full rotate-[-90deg]" viewBox="0 0 36 36">
                            <path class="text-slate-100 dark:text-slate-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4" />
                            <path class="text-{{ $data->color }}-500 transition-all duration-1000" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="{{ $data->progress }}, 100" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $data->progress }}%</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 flex-1">
                        <div class="text-xs text-slate-500">Status Pengisian</div>
                        <div class="text-sm font-bold text-{{ $data->color }}-600">{{ $data->status }}</div>
                    </div>
                </div>

                <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs text-slate-500">
                    <span>{{ $data->mapel_count }} Mapel</span>
                    <span>{{ $data->period_label }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center h-[50vh] text-slate-400">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-20">search_off</span>
            <p class="text-lg font-medium">Tidak ada data kelas ditemukan.</p>
            <p class="text-sm">Coba ubah filter di atas.</p>
        </div>
        @endif

    </div>
</div>
@endsection
