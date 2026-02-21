@extends('layouts.app')

@section('title', 'Leger Nilai' . ($kelas ? ' - ' . $kelas->nama_kelas : ''))

@section('content')
@if(!$kelas)
<div class="mb-6 space-y-4 shrink-0">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-primary/10 rounded-xl text-primary">
            <span class="material-symbols-outlined text-3xl">table_chart</span>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Leger Nilai Kelas</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Pilih kelas untuk melihat leger nilai.</p>
        </div>
    </div>
</div>
<x-admin-class-grid :classes="$allClasses" :route-name="request()->route()->getName()" />
@else
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
             <div class="flex items-center gap-2 text-sm font-bold text-slate-500 mb-2">
                <a href="{{ route('walikelas.dashboard') }}" class="hover:text-primary transition-colors">Dashboard Wali Kelas</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-primary">Leger Nilai</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">table_chart</span>
                Leger Nilai Kelas {{ $kelas->nama_kelas }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
                Periode: <span class="text-slate-800 dark:text-white font-bold">{{ $periode->nama_periode }}</span> &bull; Total Siswa: <span class="text-slate-800 dark:text-white font-bold">{{ $students->count() }}</span>
            </p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
             <a href="{{ route('walikelas.leger.export') }}" target="_blank" class="btn-boss bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/20 px-4 py-2.5 flex items-center justify-center gap-2 w-full md:w-auto">
                <span class="material-symbols-outlined text-[20px]">download</span>
                <span class="font-bold">Export Excel</span>
            </a>
            <a href="{{ route('walikelas.leger.rekap') }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm px-4 py-2.5 flex items-center justify-center gap-2 w-full md:w-auto">
                <span class="material-symbols-outlined text-[20px]">history</span>
                <span class="font-bold">Rekap Tahunan</span>
            </a>
        </div>
    </div>

    <!-- Admin / TU Action & Filter -->
    @if(auth()->user()->isAdmin() || auth()->user()->isTu())
    <div class="card-boss !p-4 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50 dark:bg-slate-800/50">
        <!-- Back Button -->
        <a href="{{ route('walikelas.leger') }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            <span>Kembali ke Pilihan Kelas</span>
        </a>

        <!-- Period Selector -->
        <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row w-full md:w-auto gap-3">
             <input type="hidden" name="kelas_id" value="{{ request('kelas_id') ?: ($kelas->id ?? '') }}">
             <div class="relative group w-full md:w-auto">
                <select name="periode_id" class="input-boss appearance-none !bg-none !pl-9 !pr-8 w-full md:min-w-[200px]" onchange="this.form.submit()">
                    @if(isset($periodes) && $periodes->count() > 0)
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}" {{ isset($periode) && $periode->id == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_periode }}
                            </option>
                        @endforeach
                    @else
                        <option value="{{ $periode->id ?? '' }}" selected>{{ $periode->nama_periode ?? 'Periode Aktif' }}</option>
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                </div>
                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </div>
            </div>
        </form>
    </div>
    @endif

    <!-- Desktop Leger Container -->
    <div class="hidden md:block card-boss !p-0 overflow-hidden flex flex-col h-[75vh]">
        <div class="overflow-auto flex-1 relative custom-scrollbar">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 sticky top-0 z-20 shadow-sm">
                    <tr>
                        <th class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 sticky left-0 bg-slate-50 dark:bg-slate-800 z-30 w-12 text-center shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">No</th>
                        <th class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 sticky left-[48px] bg-slate-50 dark:bg-slate-800 z-30 min-w-[250px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Nama Siswa</th>
                        <th class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 w-16 text-center">L/P</th>

                        @foreach($mapels as $mapel)
                        <th class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 min-w-[120px] text-center group relative hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-help">
                            <div class="truncate max-w-[110px] mx-auto text-slate-700 dark:text-slate-300" title="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</div>
                            @if($mapel->nama_kitab)
                                <div class="text-[10px] text-slate-400 truncate max-w-[110px] mx-auto hidden group-hover:block">{{ $mapel->nama_kitab }}</div>
                            @endif
                            <div class="mt-1 text-[10px] inline-flex items-center px-1.5 py-0.5 rounded-full bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-normal">KKM: {{ $kkm[$mapel->id] ?? 70 }}</div>
                        </th>
                        @endforeach

                        <th class="px-4 py-4 border-b border-l border-slate-200 dark:border-slate-700 min-w-[100px] text-center bg-indigo-50/50 text-indigo-700">Total</th>
                        <th class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 min-w-[100px] text-center bg-emerald-50/50 text-emerald-700">Rata2</th>
                        <th class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 min-w-[80px] text-center bg-amber-50/50 text-amber-700">Rank</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($students as $index => $ak)
                    @php
                        $studentGrades = $grades[$ak->id_siswa] ?? collect([]);
                        $totalScore = 0;
                        $countMapel = 0;
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-4 py-3 border-r border-slate-100 dark:border-slate-800 sticky left-0 bg-white dark:bg-[#1a2332] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] text-center font-bold text-slate-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border-r border-slate-100 dark:border-slate-800 sticky left-[48px] bg-white dark:bg-[#1a2332] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                             <div class="font-bold text-slate-900 dark:text-white truncate max-w-[250px]">{{ $ak->siswa->nama_lengkap }}</div>
                             <div class="text-[10px] text-slate-400 font-mono">{{ $ak->siswa->nis_lokal }}</div>
                        </td>
                        <td class="px-4 py-3 text-center border-r border-slate-100 dark:border-slate-800 font-bold text-slate-500">{{ $ak->siswa->jenis_kelamin }}</td>

                        <!-- Grades -->
                        @foreach($mapels as $mapel)
                        @php
                            $grade = $grades->get($ak->id_siswa . '-' . $mapel->id);
                            $score = $grade ? $grade->nilai_akhir : 0;
                            if($grade) {
                                $totalScore += $score;
                                $countMapel++;
                            }
                            $kkmLocal = $kkm[$mapel->id] ?? 70;
                            $isBelowKKM = $score < $kkmLocal && $grade;
                        @endphp
                        <td class="px-4 py-3 text-center border-r border-slate-100 dark:border-slate-800 {{ $isBelowKKM ? 'bg-rose-50/30' : '' }}">
                            <span class="font-bold {{ $isBelowKKM ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $grade ? number_format($score, 0) : '-' }}
                            </span>
                        </td>
                        @endforeach

                        <!-- Summary -->
                        <td class="px-4 py-3 text-center border-l border-slate-200 dark:border-slate-700 bg-indigo-50/10">
                            <span class="font-black text-indigo-600">{{ $totalScore > 0 ? number_format($totalScore, 0) : '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center bg-emerald-50/10">
                            <span class="font-black text-emerald-600">{{ $countMapel > 0 ? number_format($totalScore / $countMapel, 2) : '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center bg-amber-50/10">
                             <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-black text-xs shadow-sm">
                                {{ $index + 1 }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden flex flex-col gap-4">
        @foreach($students as $index => $ak)
        @php
            $totalScore = 0;
            $countMapel = 0;
            // Calculate Total first for Card Summary
            foreach($mapels as $mapel) {
                $grade = $grades->get($ak->id_siswa . '-' . $mapel->id);
                if($grade) {
                    $totalScore += $grade->nilai_akhir;
                    $countMapel++;
                }
            }
            $avg = $countMapel > 0 ? number_format($totalScore / $countMapel, 2) : 0;
        @endphp
        <div class="card-boss !p-0 overflow-hidden" x-data="{ expanded: false }">
            <!-- Header Summary (Always Visible) -->
            <div class="p-4 flex flex-col gap-4" @click="expanded = !expanded">
                <div class="flex items-center gap-4">
                     <!-- Rank Badge -->
                     <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-amber-100 to-amber-200 text-amber-700 rounded-xl flex flex-col items-center justify-center font-black shadow-inner border border-amber-300">
                        <span class="text-[8px] uppercase tracking-wider opacity-70">Rank</span>
                        <span class="text-xl leading-none">#{{ $index + 1 }}</span>
                     </div>
                     <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-lg">{{ $ak->siswa->nama_lengkap }}</h4>
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-bold mt-1">
                            <span class="bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-slate-600 dark:text-slate-300">{{ $ak->siswa->nis_lokal }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span>{{ $ak->siswa->jenis_kelamin }}</span>
                        </div>
                     </div>
                     <button class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center text-slate-400 transition-transform duration-200" :class="expanded ? 'rotate-180 bg-primary/10 text-primary' : ''">
                        <span class="material-symbols-outlined">expand_more</span>
                     </button>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-[10px] text-indigo-600 dark:text-indigo-300 font-bold uppercase tracking-wider">Total Nilai</span>
                        <span class="font-black text-indigo-700 dark:text-indigo-200 text-lg">{{ number_format($totalScore, 0) }}</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-300 font-bold uppercase tracking-wider">Rata-Rata</span>
                        <span class="font-black text-emerald-700 dark:text-emerald-200 text-lg">{{ $avg }}</span>
                    </div>
                </div>
            </div>

            <!-- Detail Accordion (Hidden by default) -->
            <div x-show="expanded" x-collapse style="display: none;" class="border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-4">
                <div class="flex flex-col gap-2">
                    <div class="grid grid-cols-12 text-[10px] font-bold text-slate-400 uppercase mb-1 px-3">
                        <div class="col-span-8">Mata Pelajaran</div>
                        <div class="col-span-2 text-center">KKM</div>
                        <div class="col-span-2 text-right">Nilai</div>
                    </div>
                    @foreach($mapels as $mapel)
                    @php
                        $grade = $grades->get($ak->id_siswa . '-' . $mapel->id);
                        $score = $grade ? $grade->nilai_akhir : 0;
                        $kkmLocal = $kkm[$mapel->id] ?? 70;
                        $isFail = $score < $kkmLocal && $grade;
                    @endphp
                    <div class="grid grid-cols-12 items-center p-3 rounded-lg border {{ $isFail ? 'bg-rose-50 border-rose-100 dark:bg-rose-900/20 dark:border-rose-800' : 'bg-white border-slate-200 dark:bg-slate-800 dark:border-slate-700' }} shadow-sm">
                        <div class="col-span-8 flex flex-col pr-2">
                            <span class="text-xs font-bold leading-tight {{ $isFail ? 'text-rose-800 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200' }}">{{ $mapel->nama_mapel }}</span>
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="text-xs font-mono {{ $isFail ? 'text-rose-500' : 'text-slate-400' }}">{{ $kkmLocal }}</span>
                        </div>
                        <div class="col-span-2 text-right">
                            <span class="text-sm font-black {{ $isFail ? 'text-rose-600' : 'text-slate-800 dark:text-white' }}">
                                {{ $grade ? number_format($score, 0) : '-' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    /* Custom Scrollbar for Leger Table */
    .custom-scrollbar::-webkit-scrollbar {
        height: 12px;
        width: 12px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 6px;
        border: 3px solid #f1f5f9;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    @media print {
        @page {
            size: landscape;
            margin: 5mm;
        }
        body * {
            visibility: hidden;
        }
        .bg-white.rounded-xl.shadow-sm.overflow-hidden, .bg-white.rounded-xl.shadow-sm.overflow-hidden * {
            visibility: visible;
        }
        .bg-white.rounded-xl.shadow-sm.overflow-hidden {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: auto;
            overflow: visible !important;
            border: none;
            box-shadow: none;
        }
        /* Hide scrollbars/sticky shadows in print */
        .sticky { position: static !important; box-shadow: none !important; }
        .overhead, button, a { display: none !important; }

        /* Ensure table fits */
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd !important; padding: 4px !important; font-size: 10px !important; }
        thead th { background-color: #f8fafc !important; color: #000 !important; font-weight: bold; }
    }
</style>
@endif
@endsection

