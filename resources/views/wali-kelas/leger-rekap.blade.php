@extends('layouts.app')

@section('title', 'Leger Rekap Tahunan - ' . $kelas->nama_kelas)

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
             <div class="flex items-center gap-2 text-sm font-bold text-slate-500 mb-2">
                <a href="{{ route('walikelas.dashboard') }}" class="hover:text-primary transition-colors">Dashboard Wali Kelas</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-primary">Leger Rekap</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">history</span>
                Rekap Tahunan {{ $kelas->nama_kelas }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
                Tahun Ajaran: <span class="text-slate-800 dark:text-white font-bold">{{ $kelas->tahun_ajaran->nama_tahun }}</span> &bull; Total Siswa: <span class="text-slate-800 dark:text-white font-bold">{{ $students->count() }}</span>
            </p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
             <a href="{{ route('walikelas.leger', ['kelas_id' => $kelas->id]) }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm px-4 py-2.5 flex items-center justify-center gap-2 w-full md:w-auto">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                <span class="font-bold">Kembali</span>
            </a>
             <a href="{{ route('walikelas.leger.rekap.export', ['kelas_id' => $kelas->id, 'year_id' => $kelas->id_tahun_ajaran]) }}" target="_blank" class="btn-boss bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/20 px-4 py-2.5 flex items-center justify-center gap-2 w-full md:w-auto">
                <span class="material-symbols-outlined text-[20px]">download</span>
                <span class="font-bold">Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Desktop Leger Container -->
    <div class="hidden md:block card-boss !p-0 overflow-hidden flex flex-col h-[75vh]">
        <div class="overflow-auto flex-1 relative custom-scrollbar">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 sticky top-0 z-20 shadow-sm">
                    <tr>
                        <th rowspan="2" class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 sticky left-0 bg-slate-50 dark:bg-slate-800 z-30 w-12 text-center shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">No</th>
                        <th rowspan="2" class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 sticky left-[48px] bg-slate-50 dark:bg-slate-800 z-30 min-w-[250px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Nama Siswa</th>
                        <th rowspan="2" class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 w-16 text-center">L/P</th>

                        @foreach($mapels as $mapel)
                        <th colspan="{{ $periods->count() + 1 }}" class="px-4 py-3 border-b border-l border-slate-200 dark:border-slate-700 text-center text-slate-700 dark:text-slate-300">
                            <div class="truncate max-w-[150px] mx-auto" title="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</div>
                            <div class="text-[10px] text-slate-400 font-normal">KKM: {{ $kkm[$mapel->id] ?? 70 }}</div>
                        </th>
                        @endforeach

                        <th rowspan="2" class="px-4 py-3 border-b border-l border-slate-200 dark:border-slate-700 min-w-[80px] text-center bg-primary/10 text-primary">Rata-rata<br>Total</th>
                    </tr>
                    <tr>
                        <!-- Sub Columns for Periods -->
                        @foreach($mapels as $mapel)
                            @foreach($periods as $periode)
                            <th class="px-2 py-2 border-b border-l border-slate-200 dark:border-slate-700 min-w-[50px] text-center text-[10px] bg-white dark:bg-slate-800/50">
                                {{ substr($periode->nama_periode, 0, 1) }}{{ filter_var($periode->nama_periode, FILTER_SANITIZE_NUMBER_INT) }}
                            </th>
                            @endforeach
                            <th class="px-2 py-2 border-b border-l border-slate-200 dark:border-slate-700 min-w-[60px] text-center text-[10px] bg-amber-50 dark:bg-amber-900/20 text-amber-700 font-bold">RATA</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-white dark:bg-[#1a2332]">
                    @foreach($students as $index => $ak)
                    @php
                        $studentGradesAll = $grades[$ak->id_siswa] ?? collect([]);
                        $grandTotalAvg = 0;
                        $countMapelAvg = 0;
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-4 py-3 border-r border-slate-100 dark:border-slate-800 sticky left-0 bg-white dark:bg-[#1a2332] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] text-center font-bold text-slate-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border-r border-slate-100 dark:border-slate-800 sticky left-[48px] bg-white dark:bg-[#1a2332] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                            <div class="font-bold text-slate-900 dark:text-white truncate max-w-[250px]">{{ $ak->siswa->nama_lengkap }}</div>
                        </td>
                        <td class="px-4 py-3 text-center border-r border-slate-100 dark:border-slate-800 font-bold text-slate-500">{{ $ak->siswa->jenis_kelamin }}</td>

                        <!-- Grades Per Mapel -->
                        @foreach($mapels as $mapel)
                            @php
                                $mapelTotal = 0;
                                $mapelCount = 0;
                                $kkmLocal = $kkm[$mapel->id] ?? 70;
                            @endphp

                            @foreach($periods as $periodeId => $periodeObj)
                                @php
                                    // Search in collection
                                    $grade = $studentGradesAll->where('id_periode', $periodeId)->where('id_mapel', $mapel->id)->first();
                                    $score = $grade ? $grade->nilai_akhir : 0;
                                    if($grade) {
                                        $mapelTotal += $score;
                                        $mapelCount++;
                                    }
                                    $isBelowKKM = $score < $kkmLocal && $grade;
                                @endphp
                                <td class="px-2 py-3 text-center border-l border-slate-100 dark:border-slate-800 {{ $isBelowKKM ? 'bg-rose-50/50 text-rose-600 font-bold' : 'text-slate-600 dark:text-slate-400' }}">
                                    {{ $grade ? number_format($score, 0) : '-' }}
                                </td>
                            @endforeach

                            <!-- Average Per Mapel -->
                            @php
                                $avgMapel = $mapelCount > 0 ? $mapelTotal / $mapelCount : 0;
                                if ($mapelCount > 0) {
                                    $grandTotalAvg += $avgMapel;
                                    $countMapelAvg++;
                                }
                            @endphp
                            <td class="px-2 py-3 text-center font-black text-amber-600 bg-amber-50/20 border-l border-slate-200 dark:border-slate-700">
                                {{ $mapelCount > 0 ? number_format($avgMapel, 2) : '-' }}
                            </td>
                        @endforeach

                        <!-- Grand Average -->
                        @php
                            $finalAvg = $countMapelAvg > 0 ? $grandTotalAvg / $countMapelAvg : 0;
                        @endphp
                        <td class="px-4 py-3 text-center font-black text-primary bg-primary/10 border-l border-slate-200 dark:border-slate-700">
                            {{ $countMapelAvg > 0 ? number_format($finalAvg, 2) : '-' }}
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
            $studentGradesAll = $grades[$ak->id_siswa] ?? collect([]);
            $grandTotalAvg = 0;
            $countMapelAvg = 0;

            // Pre-calculate Grand Average for Card Summary
            foreach($mapels as $mapel) {
                 $mapelTotal = 0;
                 $mapelCount = 0;
                 foreach($periods as $periodeId => $periodeObj) {
                     $grade = $studentGradesAll->where('id_periode', $periodeId)->where('id_mapel', $mapel->id)->first();
                     if($grade) {
                         $mapelTotal += $grade->nilai_akhir;
                         $mapelCount++;
                     }
                 }
                 if ($mapelCount > 0) {
                     $grandTotalAvg += ($mapelTotal / $mapelCount);
                     $countMapelAvg++;
                 }
            }
            $finalAvg = $countMapelAvg > 0 ? $grandTotalAvg / $countMapelAvg : 0;
        @endphp
        <div class="card-boss !p-0 overflow-hidden" x-data="{ expanded: false }">
            <!-- Header Summary (Always Visible) -->
            <div class="p-4 flex flex-col gap-4" @click="expanded = !expanded">
                <div class="flex items-center gap-4">
                     <!-- Rank Badge -->
                     <div class="w-12 h-12 flex-shrink-0 bg-primary/10 text-primary rounded-xl flex flex-col items-center justify-center font-black shadow-inner border border-primary/20">
                        <span class="text-[8px] uppercase tracking-wider opacity-70">No</span>
                        <span class="text-xl leading-none">{{ $index + 1 }}</span>
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
                <div class="bg-primary/5 border border-primary/10 rounded-xl p-3 flex justify-between items-center">
                    <span class="text-[10px] text-primary font-bold uppercase tracking-wider">Rata-Rata Total</span>
                    <span class="font-black text-primary text-xl">{{ number_format($finalAvg, 2) }}</span>
                </div>
            </div>

            <!-- Detail Accordion (Hidden by default) -->
            <div x-show="expanded" x-collapse style="display: none;" class="border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-4">
                <div class="grid grid-cols-1 gap-3">
                    @foreach($mapels as $mapel)
                        @php
                            $mapelTotal = 0;
                            $mapelCount = 0;
                            foreach($periods as $periodeId => $periodeObj) {
                                $grade = $studentGradesAll->where('id_periode', $periodeId)->where('id_mapel', $mapel->id)->first();
                                if($grade) {
                                    $mapelTotal += $grade->nilai_akhir;
                                    $mapelCount++;
                                }
                            }
                            $avgMapel = $mapelCount > 0 ? $mapelTotal / $mapelCount : 0;
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-slate-100 dark:border-slate-700 shadow-sm">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $mapel->nama_mapel }}</span>
                                <span class="text-xs font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-100">Avg: {{ $mapelCount > 0 ? number_format($avgMapel, 2) : '-' }}</span>
                            </div>
                            <!-- Period Breakdown -->
                            <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                                 @foreach($periods as $periodeId => $periodeObj)
                                    @php
                                        $grade = $studentGradesAll->where('id_periode', $periodeId)->where('id_mapel', $mapel->id)->first();
                                    @endphp
                                    <div class="flex flex-col items-center min-w-[40px] p-1.5 rounded-lg border {{ $grade ? 'bg-slate-50 border-slate-200' : 'bg-slate-50/50 border-slate-100 text-slate-300' }}">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase mb-0.5">{{ substr($periodeObj->nama_periode, 0, 3) }}</span>
                                        <span class="text-xs font-bold {{ $grade ? 'text-slate-700' : 'text-slate-300' }}">{{ $grade ? $grade->nilai_akhir : '-' }}</span>
                                    </div>
                                 @endforeach
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
        @page { size: landscape; margin: 5mm; }
        body * { visibility: hidden; }
        .bg-white.rounded-xl.shadow-sm.overflow-hidden, .bg-white.rounded-xl.shadow-sm.overflow-hidden * { visibility: visible; }
        .bg-white.rounded-xl.shadow-sm.overflow-hidden { position: absolute; left: 0; top: 0; width: 100%; height: auto; overflow: visible !important; border: none; box-shadow: none; }
        .sticky { position: static !important; box-shadow: none !important; }
        .overhead, button, a { display: none !important; }

        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px !important; font-size: 8px !important; border: 1px solid #ddd !important; }
        thead th { background-color: #f8fafc !important; color: #000 !important; font-weight: bold; }
    }
</style>
@endsection
