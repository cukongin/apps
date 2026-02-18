@extends('layouts.app')

@section('title', 'Monitoring Nilai - ' . $kelas->nama_kelas)

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col gap-4">
        @if(auth()->user()->isAdmin() || auth()->user()->isTu())
        <div class="card-boss !p-4 flex flex-col md:flex-row items-center gap-4 bg-slate-50 dark:bg-slate-800/50 mb-2">
            <div class="flex items-center gap-2 text-slate-500 font-bold text-xs uppercase tracking-wider min-w-fit">
                <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                Filter Admin
            </div>
            <form action="{{ route('walikelas.monitoring') }}" method="GET" class="flex flex-col md:flex-row w-full gap-3">
                 <!-- Jenjang Selector -->
                <div class="relative group w-full md:w-auto">
                    <select name="jenjang" class="input-boss !pl-9 !pr-8 w-full md:min-w-[100px]" onchange="this.form.submit()">
                        @foreach(['MI', 'MTS'] as $j)
                            <option value="{{ $j }}" {{ (request('jenjang') == $j || (empty(request('jenjang')) && $loop->first)) ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">school</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>

                <!-- Class Selector -->
                <div class="relative group w-full md:w-auto">
                    <select name="kelas_id" class="input-boss !pl-9 !pr-8 w-full md:min-w-[200px]" onchange="this.form.submit()">
                        @php
                            $yId = $activeYear->id ?? $kelas->id_tahun_ajaran;
                            $q = \App\Models\Kelas::where('id_tahun_ajaran', $yId)->orderBy('nama_kelas');
                            if(request('jenjang')) {
                                $q->whereHas('jenjang', function($query) {
                                    $query->where('kode', request('jenjang'));
                                });
                            }
                            $allClassesInYear = $q->get();
                        @endphp

                        @if($allClassesInYear->count() == 0)
                            <option value="">Tidak ada kelas</option>
                        @endif

                        @foreach($allClassesInYear as $kls)
                            <option value="{{ $kls->id }}" {{ $kelas->id == $kls->id ? 'selected' : '' }}>
                                {{ $kls->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">class</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>

                 <!-- Period Selector -->
                <div class="relative group w-full md:w-auto">
                    @if(isset($allPeriods) && $allPeriods->count() > 0)
                    <select name="periode_id" class="input-boss !pl-9 !pr-8 w-full md:min-w-[200px]" onchange="this.form.submit()">
                        @foreach($allPeriods as $prd)
                            <option value="{{ $prd->id }}" {{ $periode->id == $prd->id ? 'selected' : '' }}>
                                {{ $prd->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                    @else
                        <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                    @endif
                </div>
            </form>
        </div>
        @endif

        <div class="card-boss !p-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                 <div class="flex items-center gap-2 text-sm font-bold text-slate-500 mb-2">
                    <a href="{{ route('walikelas.dashboard') }}" class="hover:text-primary transition-colors">Dashboard Wali Kelas</a>
                    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                    <span class="text-primary">Monitoring Nilai</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-3xl">query_stats</span>
                    Monitoring Nilai Kelas {{ $kelas->nama_kelas }}
                </h1>
                <div class="flex flex-col gap-2 mt-2">
                     <p class="text-slate-500 dark:text-slate-400 font-medium">
                        Wali Kelas: <span class="text-slate-800 dark:text-white font-bold">{{ $kelas->wali_kelas->name ?? 'Belum ditentukan' }}</span>
                    </p>
                    <div class="flex items-center gap-3">
                         <span class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Aman (≥ 86)
                        </span>
                        <span class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-bold border border-amber-100">
                             <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Perlu Katrol (< 86)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                @if(isset($allLocked) && $allLocked)
                    <div class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-xl text-sm font-bold border border-emerald-200 dark:border-emerald-800 cursor-default shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        <span>Nilai Terkunci</span>
                    </div>
                @else
                    <form action="{{ route('walikelas.monitoring.finalize') }}" method="POST"
                          data-confirm-delete="true"
                          data-title="Kunci Nilai (Final)?"
                          data-message="Nilai akan dikunci dan siap dicetak. Pastikan semua nilai sudah benar."
                          data-confirm-text="Ya, Kunci Nilai!"
                          data-confirm-color="#059669"
                          data-icon="question"
                          class="w-full md:w-auto">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                        <button type="submit" class="w-full md:w-auto btn-boss bg-slate-800 hover:bg-slate-700 text-white shadow-lg shadow-slate-800/20 px-4 py-2.5 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">lock_open</span>
                            <span class="font-bold">Kunci Nilai</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- MOBILE CARD VIEW -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($monitoringData as $data)
            @php
                $isSafe = $data->status === 'aman';
                // Mobile Card Style
                $cardClass = $isSafe ? 'border-slate-200 dark:border-slate-700' : 'border-amber-300 dark:border-amber-700/50 bg-amber-50/50 dark:bg-amber-900/10';
            @endphp
            <div class="card-boss !p-4 flex flex-col gap-4 {{ $cardClass }}">
                <!-- Header: Mapel & Status -->
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-800 dark:text-white text-lg font-arabic">{{ $data->nama_mapel }}</span>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="h-6 w-6 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 font-bold uppercase">
                                {{ substr($data->nama_guru, 0, 1) }}
                            </div>
                            <span class="text-xs font-bold text-slate-500">{{ $data->nama_guru }}</span>
                        </div>
                    </div>
                    @if(!$isSafe)
                    <span class="inline-flex px-2 py-1 rounded-lg text-[10px] font-black bg-amber-100 text-amber-800 uppercase tracking-wide border border-amber-200 shadow-sm animate-pulse">
                        Perlu Katrol
                    </span>
                    @endif
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col p-3 bg-white dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50 text-center shadow-sm">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Rata-Rata</span>
                        <span class="text-xl font-black text-slate-700 dark:text-slate-300">{{ $data->avg_score }}</span>
                    </div>
                    <div class="flex flex-col p-3 bg-white dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50 text-center shadow-sm">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Terendah</span>
                        <span class="text-xl font-black {{ !$isSafe ? 'text-rose-500' : 'text-slate-700' }}">{{ $data->min_score }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-3 mt-auto pt-2 border-t border-slate-100 dark:border-slate-700/50">
                    <a href="{{ route('teacher.input-nilai', ['kelas' => $kelas->id, 'mapel' => $data->id, 'periode_id' => $periode->id]) }}"
                       class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-bold rounded-xl bg-white border border-slate-200 text-slate-700 shadow-sm active:bg-slate-50">
                        <span class="material-symbols-outlined text-[18px]">edit_note</span>
                        Input
                    </a>
                    <a href="{{ route('walikelas.katrol.index', ['kelas_id' => $kelas->id, 'mapel_id' => $data->id]) }}"
                       class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-bold rounded-xl text-white shadow-sm transition-all
                       {{ $isSafe ? 'bg-slate-500' : 'bg-primary shadow-lg shadow-primary/30' }}">
                        <span class="material-symbols-outlined text-[18px]">{{ $isSafe ? 'visibility' : 'upgrade' }}</span>
                        {{ $isSafe ? 'Lihat' : 'Katrol' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 font-medium">
                Belum ada data mata pelajaran.
            </div>
        @endforelse
    </div>

    <!-- DESKTOP TABLE VIEW -->
    <div class="hidden md:block card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-xs font-bold text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Mata Pelajaran</th>
                        <th class="px-6 py-4">Guru Pengampu</th>
                        <th class="px-6 py-4 text-center">Rata-Rata</th>
                        <th class="px-6 py-4 text-center">Terendah</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 bg-white dark:bg-[#1a2332]">
                    @forelse($monitoringData as $data)
                        @php
                            $isSafe = $data->status === 'aman';
                            $rowClass = $isSafe ? 'hover:bg-slate-50 dark:hover:bg-slate-800/50' : 'bg-amber-50 hover:bg-amber-100/50 dark:bg-amber-900/10 dark:hover:bg-amber-900/20';
                        @endphp
                        <tr class="{{ $rowClass }} transition-colors group">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white text-base">
                                {{ $data->nama_mapel }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center text-xs text-slate-500 font-bold uppercase shadow-sm">
                                        {{ substr($data->nama_guru, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-sm">{{ $data->nama_guru }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-slate-700 dark:text-slate-300 text-base">{{ $data->avg_score }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-base {{ !$isSafe ? 'text-rose-500' : 'text-slate-700' }}">{{ $data->min_score }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isSafe)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Aman
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200 animate-pulse shadow-sm">
                                        Perlu Katrol ({{ $data->below_count }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('teacher.input-nilai', ['kelas' => $kelas->id, 'mapel' => $data->id, 'periode_id' => $periode->id]) }}"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg shadow-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-primary hover:border-primary/30 transition-all"
                                       title="Input Nilai sebagai Wali Kelas">
                                        <span class="material-symbols-outlined text-[18px]">edit_note</span>
                                        <span>Input</span>
                                    </a>

                                    <a href="{{ route('walikelas.katrol.index', ['kelas_id' => $kelas->id, 'mapel_id' => $data->id]) }}"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg shadow-sm text-white transition-all
                                       {{ $isSafe ? 'bg-slate-500 hover:bg-slate-600' : 'bg-primary hover:bg-emerald-600 shadow-md shadow-primary/30' }}">
                                        <span class="material-symbols-outlined text-[18px]">{{ $isSafe ? 'visibility' : 'upgrade' }}</span>
                                        <span>{{ $isSafe ? 'Lihat' : 'Katrol' }}</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">
                                Belum ada data mata pelajaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
