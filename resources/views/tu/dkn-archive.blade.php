@extends('layouts.app')

@section('title', 'Detail DKN - ' . $kelas->nama_kelas)

@section('content')

@php
    // Determine Structure based on Jenjang (Unified Logic)
    $jenjang = $kelas->jenjang->kode ?? ($kelas->tingkat_kelas > 6 ? 'MTS' : 'MI');

    $startLvl = min($targetLevels);
    $endLvl = max($targetLevels);
    // periods and periodLabel are passed from controller
    $headerRange = 'Kelas ' . implode(', ', $targetLevels);

    // Calculate Total Rowspan (Data Rows + 3 Summary Rows)
    // Note: We iterate targetLevels, not range(start, end) potentially?
    // Service returns ARRAY of targetLevels. View iterates $startLvl to $endLvl?
    // Service iterates targetLevels array. View used loop: for($lvl = $startLvl; $lvl <= $endLvl; $lvl++)
    // We should change View to iterate foreach($targetLevels as $lvl)

    $totalRowSpan = (count($targetLevels) * count($periods)) + 3;
@endphp

<!-- ========================================== -->
<!-- 1. SCREEN VIEW (Visible on Screen, Hidden on Print) -->
<!-- ========================================== -->
<div class="space-y-6 print:hidden">

    <!-- Top Bar -->
    <div class="print:hidden px-4 md:px-6 py-4 md:py-5 bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 flex flex-col md:flex-row justify-between md:items-center gap-4 relative">
        <div class="flex items-center gap-4">
            <a href="{{ route('tu.dkn.index') }}" class="btn-boss bg-white dark:bg-slate-800 text-slate-500 hover:text-primary dark:hover:text-primary border border-slate-200 dark:border-slate-700 p-2.5 !rounded-xl">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3 tracking-tight">
                    <span class="bg-gradient-to-r from-primary to-emerald-500 text-transparent bg-clip-text">Arsip DKN</span>
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-primary/10 text-primary border border-primary/20 dark:bg-primary/20 dark:text-primary dark:border-primary/20">
                        {{ $kelas->nama_kelas }}
                    </span>
                    <span class="text-xs text-slate-400">&bull; {{ $school->nama_sekolah }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
             <a href="{{ route('tu.dkn.export_excel', $kelas->id) }}" class="btn-boss bg-emerald-500 hover:bg-emerald-600 text-white border-emerald-600 flex items-center gap-2 shadow-sm shadow-emerald-200 w-full md:w-auto justify-center">
                <span class="material-symbols-outlined text-[18px]">table_view</span> Download Excel
            </a>
            {{-- Print Button (Trigger Browser Print) --}}
            <button onclick="window.print()" class="btn-boss bg-white border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center gap-2 shadow-sm w-full md:w-auto justify-center">
                 <span class="material-symbols-outlined text-[18px]">print</span> Cetak
            </button>
        </div>
    </div>

    @php
        $bRapor = \App\Models\GlobalSetting::val('ijazah_bobot_rapor', 60);
        $bUjian = \App\Models\GlobalSetting::val('ijazah_bobot_ujian', 40);
        $minLulus = \App\Models\GlobalSetting::val('ijazah_min_lulus', 60);
    @endphp

    <!-- Stats Section Removed (Empty) -->

    <!-- ========================================== -->
    <!-- MOBILE VIEW (Simple Cards) - Block on Mobile, Hidden on Desktop -->
    <!-- ========================================== -->
    <div class="block md:hidden space-y-4 px-4 pb-10">
        @foreach($dknData as $index => $row)
            @php
                // Calculate Logic
                $summary = $row['summary'];
                $avgNA = $summary['averages']['na'] ?? 0;

                // Status Logic
                $finalStatus = 'LULUS';
                $isPass = $avgNA >= $minLulus;

                // Veto Logic
                $sId = $row['student']->id;
                $promoRecord = $promotionDecisions[$sId] ?? null;
                $promoStatus = $promoRecord->final_decision ?? null;
                $isVetoed = in_array($promoStatus, ['not_graduated', 'retained']);

                if ($isVetoed) {
                    $isPass = false;
                    $finalStatus = 'TIDAK LULUS';
                } elseif (!$isPass) {
                    $finalStatus = 'TIDAK LULUS';
                }
            @endphp

            <div x-data="{ open: false }" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-all duration-300">
                <!-- Card Header (Clickable) -->
                <div @click="open = !open" class="p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-500 dark:text-slate-400">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white line-clamp-1">{{ $row['student']->nama_lengkap }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                 <div class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isPass ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                    {{ $finalStatus }}
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Rata-rata: <strong>{{ number_format($avgNA, 2) }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <!-- Chevron -->
                    <span class="material-symbols-outlined text-slate-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                </div>

                <!-- Card Body (Details) -->
                <div x-show="open" x-collapse class="bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 p-4">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Detail Nilai Akhir (Rapor + Ujian)</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($mapels as $mapel)
                            @php
                                $score = $row['summary']['na'][$mapel->id] ?? 0;
                                $isGood = $score >= $minLulus;
                            @endphp
                            <div class="flex justify-between items-center p-2 rounded bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <span class="text-xs text-slate-600 dark:text-slate-300 line-clamp-1 truncate mr-2" title="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</span>
                                <span class="text-xs font-bold {{ $isGood ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ number_format($score, 0) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($isVetoed)
                        <div class="mt-4 p-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-900/30 rounded-lg">
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-bold">⚠️ Catatan Kelulusan:</p>
                            <p class="text-xs text-rose-500 dark:text-rose-400 mt-1">Siswa ini dinyatakan TIDAK LULUS melalui keputusan rapat dewan guru (Veto).</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- ========================================== -->
    <!-- DESKTOP VIEW (Full Table) - Hidden on Mobile, Block on Desktop -->
    <!-- ========================================== -->
    <div class="hidden md:block card-boss !p-0 overflow-hidden flex flex-col">
        <div class="overflow-auto relative max-h-[75vh] custom-scrollbar">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/80 uppercase text-[10px] font-bold text-slate-600 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-3 border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 w-10 text-center">NO</th>
                        <th class="px-3 py-3 border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 min-w-[200px]">NAMA SISWA</th>
                        <th class="px-3 py-3 border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 min-w-[120px]">KELAS / {{ strtoupper($periodLabel) }}</th>
                        @foreach($mapels as $mapel)
                        <th class="px-2 py-2 border-b border-slate-200 dark:border-slate-700 text-center min-w-[60px]">{{ $mapel->nama_mapel }}</th>
                        @endforeach
                        <th class="px-2 py-3 border-b border-slate-200 dark:border-slate-700 text-center w-16 bg-slate-100 dark:bg-slate-700/50">RATA-RATA</th>
                        <th class="px-2 py-3 border-b border-slate-200 dark:border-slate-700 text-center w-24 bg-slate-100 dark:bg-slate-700/50">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 block md:table-row-group">
                    @php $no = 1; @endphp
                    @foreach($dknData as $row)
                        @php
                            // 1. Academic Status
                             $naValues = array_filter($row['summary']['na']);
                            $naAvg = count($naValues) > 0 ? array_sum($naValues) / count($naValues) : 0;
                            $academicStatus = $naAvg >= $minLulus;

                            // 2. Veto Status (From Promotion Decisions)
                            $sId = $row['student']->id;
                            $promoRecord = $promotionDecisions[$sId] ?? null; // Now an Object
                            $promoStatus = $promoRecord->final_decision ?? null;
                            $promoNote = $promoRecord->notes ?? '';

                            // Check for both 'retained' (intermediate) and 'not_graduated' (final)
                            $isVetoed = in_array($promoStatus, ['retained', 'not_graduated']);

                            if ($isVetoed) {
                                $status = 'Tidak Lulus';
                            } elseif ($academicStatus) {
                                $status = 'Lulus';
                            } else {
                                $status = 'Tidak Lulus';
                            }

                            $isFirst = true;
                        @endphp

                        <!-- Student Block -->
                        <!-- Row 1 -->
                        <tr class="bg-white dark:bg-slate-800 group hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors border-t border-slate-200 dark:border-slate-700">
                            <td rowspan="{{ $totalRowSpan }}" class="px-3 py-3 border-r border-slate-100 dark:border-slate-700 text-center align-top font-bold sticky left-0 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/80 shadow-[1px_0_0_0_rgba(0,0,0,0.05)] text-slate-500">{{ $no++ }}</td>
                            <td rowspan="{{ $totalRowSpan }}" class="px-3 py-3 border-r border-slate-100 dark:border-slate-700 align-top font-bold sticky left-[40px] bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/80 w-[200px] shadow-[1px_0_0_0_rgba(0,0,0,0.05)]">
                                <div class="truncate max-w-[190px] text-slate-800 dark:text-white">{{ $row['student']->nama_lengkap }}</div>
                                <div class="text-[10px] font-normal text-slate-500 mt-1">NIS: {{ $row['student']->nis_lokal ?? $row['student']->nis ?? $row['student']->nisn ?? '-' }}</div>
                            </td>

                        @foreach($targetLevels as $lvl)
                            @foreach($periods as $period)
                                @if(!$isFirst) <tr class="bg-white dark:bg-slate-800 group hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors"> @endif

                                @php
                                    // Calculate Display Label (Absolute -> Relative for MTS)
                                    $displayLvl = $lvl;
                                    if ($jenjang === 'MTS') $displayLvl = $lvl - 6;

                                    // Suffix
                                    $lvlSuffix = ($jenjang === 'MTS') ? (' ' . $jenjang) : '';

                                    // Period Label Logic (Match Excel)
                                    if ($periodLabel == 'Smt' || $periodLabel == 'Semester') {
                                        $pDisplay = ($period % 2 != 0) ? 'Ganjil' : 'Genap';
                                    } else {
                                        $pDisplay = $periodLabel . ' ' . $period;
                                    }
                                @endphp

                                <td class="px-3 py-2 border-r border-slate-100 dark:border-slate-700 text-slate-500 text-xs whitespace-nowrap sticky left-[240px] bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/80 shadow-[1px_0_0_0_rgba(0,0,0,0.05)]">
                                    <span class="font-bold text-primary">{{ $displayLvl }}{{ $lvlSuffix }}</span> <span class="text-slate-300 mx-1">|</span> {{ $pDisplay }}
                                </td>

                                @foreach($mapels as $mapel)
                                    @php
                                        // Try fetching with current level.
                                        $score = $row['data'][$lvl][$period][$mapel->id] ?? null;

                                        // Fallback: If data is missing at absolute level, try relative
                                        if ($score === null && ($jenjang === 'MTS')) {
                                            $relativeLvl = $lvl - 6;
                                            $score = $row['data'][$relativeLvl][$period][$mapel->id] ?? null;
                                        }
                                    @endphp
                                    <td class="px-2 py-1 text-center text-xs text-slate-600 dark:text-slate-400">
                                        {{ $score ? number_format($score, 0) : '-' }}
                                    </td>
                                @endforeach

                                @php
                                    $rowScores = [];
                                    foreach($mapels as $m) {
                                        // Re-replicate fetch logic for Average
                                        $sc = $row['data'][$lvl][$period][$m->id] ?? null;
                                        if ($sc === null && ($jenjang === 'MTS')) {
                                             $relativeLvl = $lvl - 6;
                                             $sc = $row['data'][$relativeLvl][$period][$m->id] ?? null;
                                        }
                                        if($sc !== null) $rowScores[] = $sc;
                                    }
                                    $rowAvg = count($rowScores) > 0 ? array_sum($rowScores) / count($rowScores) : 0;
                                @endphp
                                <td class="px-2 py-1 text-center font-bold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50">
                                    {{ $rowAvg > 0 ? number_format($rowAvg, 2) : '-' }}
                                </td>

                                @if($isFirst)
                                    <td rowspan="{{ $totalRowSpan }}" class="px-2 py-2 text-center align-middle font-bold {{ $status == 'Lulus' ? 'text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-900/20' : 'text-red-600 bg-red-50 dark:text-red-400 dark:bg-red-900/20' }}">
                                        {{ $status }}
                                        @if($status == 'Tidak Lulus' && !empty($promoNote))
                                            <div class="text-[9px] text-red-800 dark:text-red-300 font-normal mt-1 border-t border-red-200 dark:border-red-800 pt-1">
                                                {{ $promoNote }}
                                            </div>
                                        @endif
                                    </td>
                                @endif
                                </tr>
                                @php $isFirst = false; @endphp
                            @endforeach
                        @endforeach

                        <!-- Summaries -->
                        <tr class="bg-yellow-50/50 dark:bg-yellow-900/10">
                            <td class="px-3 py-2 text-right font-bold text-xs sticky left-[240px] bg-yellow-50 dark:bg-yellow-900/20 z-10 border-r border-yellow-100 dark:border-yellow-900/30 text-slate-600 dark:text-slate-300 shadow-[1px_0_0_0_rgba(0,0,0,0.05)]">Rata-Rapor (RR)</td>
                            @foreach($mapels as $mapel)
                                <td class="px-2 py-2 text-center font-bold text-xs text-slate-700 dark:text-slate-300">{{ isset($row['summary']['rr'][$mapel->id]) && $row['summary']['rr'][$mapel->id] != 0 ? number_format($row['summary']['rr'][$mapel->id], 2) : '-' }}</td>
                            @endforeach
                            <td class="bg-slate-100 dark:bg-slate-800"></td>
                        </tr>
                        <tr class="bg-primary/5 dark:bg-primary/10">
                            <td class="px-3 py-2 text-right font-bold text-xs sticky left-[240px] bg-primary/5 dark:bg-primary/10 z-10 border-r border-primary/10 dark:border-primary/20 text-slate-600 dark:text-slate-300 shadow-[1px_0_0_0_rgba(0,0,0,0.05)]">Ujian Mdr (UM)</td>
                            @foreach($mapels as $mapel)
                                <td class="px-2 py-2 text-center font-bold text-xs text-slate-700 dark:text-slate-300">{{ isset($row['summary']['um'][$mapel->id]) && $row['summary']['um'][$mapel->id] != 0 ? number_format($row['summary']['um'][$mapel->id]) : '-' }}</td>
                            @endforeach
                            <td class="bg-slate-100 dark:bg-slate-800"></td>
                        </tr>
                        <tr class="bg-emerald-50/50 dark:bg-emerald-900/10 border-b-[3px] border-slate-200 dark:border-slate-700">
                            <td class="px-3 py-2 text-right font-bold text-xs sticky left-[240px] bg-emerald-50/50 dark:bg-emerald-900/10 z-10 border-r border-emerald-100 dark:border-emerald-900/30 text-slate-600 dark:text-slate-300 shadow-[1px_0_0_0_rgba(0,0,0,0.05)]">Nilai Akhir (NA)</td>
                            @foreach($mapels as $mapel)
                                <td class="px-2 py-2 text-center font-bold text-xs text-emerald-700 dark:text-emerald-400">{{ isset($row['summary']['na'][$mapel->id]) && $row['summary']['na'][$mapel->id] != 0 ? number_format($row['summary']['na'][$mapel->id], 2) : '-' }}</td>
                            @endforeach
                            <td class="bg-slate-100 dark:bg-slate-800"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. PRINT VIEW (Hidden on Screen, Visible on Print) -->
<!-- ========================================== -->
<div id="printable-dkn" class="hidden print:block font-sans text-black">
    <!-- Print Header -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold uppercase leading-tight">DAFTAR KUMPULAN NILAI (DKN) IJAZAH</h1>
        <h2 class="text-lg font-bold uppercase leading-tight">{{ $school->nama_sekolah ?? 'NAMA SEKOLAH' }}</h2>
        <div class="mt-1 text-xs">
            <p>Tahun Pelajaran {{ $kelas->active_year_name ?? date('Y') }}</p>
            <p>Kelas: {{ $kelas->nama_kelas }}</p>
        </div>
    </div>

    <!-- Print Table (Simple Black Border) -->
    <table class="w-full text-left text-[10px] border-collapse border border-black">
        <thead class="bg-gray-100 text-black uppercase font-bold text-center">
            <tr>
                <th class="px-1 py-1 border border-black w-8">NO</th>
                <th class="px-2 py-1 border border-black w-[100px]">NAMA SISWA</th>
                <th class="px-2 py-1 border border-black w-[80px]">KELAS / PERIODE</th>
                @foreach($mapels as $mapel)
                <th class="px-1 py-1 border border-black min-w-[40px]">{{ $mapel->nama_mapel }}</th>
                @endforeach
                <th class="px-1 py-1 border border-black w-12 bg-gray-200">RATA2</th>
                <th class="px-1 py-1 border border-black w-16 bg-gray-200">KET.</th>
            </tr>
        </thead>
        </thead>
        <!-- Loop Students -->
        @php $no = 1; @endphp
        @foreach($dknData as $row)
        <tbody style="page-break-inside: avoid; border-bottom: 1px solid #000;">
            @php
                $rowSpan = $totalRowSpan;
                $naValues = array_filter($row['summary']['na']);
                $naAvg = count($naValues) > 0 ? array_sum($naValues) / count($naValues) : 0;

                // Logic Sync with Screen View
                $academicStatus = $naAvg >= $minLulus;
                $sId = $row['student']->id;
                $promoRecord = $promotionDecisions[$sId] ?? null;
                $promoStatus = $promoRecord->final_decision ?? null;
                $promoNote = $promoRecord->notes ?? '';

                $isVetoed = in_array($promoStatus, ['retained', 'not_graduated']);

                if ($isVetoed) {
                    $status = 'Tidak Lulus';
                } elseif ($academicStatus) {
                    $status = 'Lulus';
                } else {
                    $status = 'Tidak Lulus';
                }
            @endphp

            <!-- Row 1 -->
            <tr>
                <td rowspan="{{ $totalRowSpan }}" class="px-1 py-1 border border-black text-center align-middle font-bold">{{ $no++ }}</td>
                <td rowspan="{{ $totalRowSpan }}" class="px-2 py-1 border border-black align-middle font-bold">
                    <div class="truncate max-w-[190px]">{{ $row['student']->nama_lengkap }}</div>
                    <div class="text-[9px] font-normal mt-1">NIS: {{ $row['student']->nis_lokal ?? $row['student']->nis ?? $row['student']->nisn ?? '-' }}</div>
                </td>

            @php $isFirst = true; @endphp
            @foreach($targetLevels as $lvl)
                @foreach($periods as $period)
                    @if(!$isFirst) <tr> @endif

                    @php
                        // Calculate Display Label (Absolute -> Relative for MTS)
                        $displayLvl = $lvl;
                        if ($jenjang === 'MTS') $displayLvl = $lvl - 6;

                        // Suffix
                        $lvlSuffix = ($jenjang === 'MTS') ? (' ' . $jenjang) : '';

                        // Period Label Logic (Match Excel)
                        if ($periodLabel == 'Smt' || $periodLabel == 'Semester') {
                            $pDisplay = ($period % 2 != 0) ? 'Ganjil' : 'Genap';
                        } else {
                            $pDisplay = $periodLabel . ' ' . $period;
                        }
                    @endphp

                    <td class="px-2 py-1 border border-black text-primary font-bold text-[9px] whitespace-nowrap">
                        {{ $displayLvl }}{{ $lvlSuffix }} | {{ $pDisplay }}
                    </td>

                    @foreach($mapels as $mapel)
                        @php
                            $score = $row['data'][$lvl][$period][$mapel->id] ?? null;
                            if ($score === null && ($jenjang === 'MTS')) {
                                $relativeLvl = $lvl - 6;
                                $score = $row['data'][$relativeLvl][$period][$mapel->id] ?? null;
                            }
                        @endphp
                        <td class="px-1 py-1 border border-black text-center text-[9px]">
                            {{ $score ? number_format($score, 0) : '-' }}
                        </td>
                    @endforeach

                    @php
                        $rowScores = [];
                        foreach($mapels as $m) {
                            $sc = $row['data'][$lvl][$period][$m->id] ?? null;
                            if ($sc === null && ($jenjang === 'MTS')) {
                                    $relativeLvl = $lvl - 6;
                                    $sc = $row['data'][$relativeLvl][$period][$m->id] ?? null;
                            }
                            if($sc !== null) $rowScores[] = $sc;
                        }
                        $rowAvg = count($rowScores) > 0 ? array_sum($rowScores) / count($rowScores) : 0;
                    @endphp
                    <td class="px-1 py-1 border border-black text-center bg-gray-50 font-bold">
                        {{ $rowAvg > 0 ? number_format($rowAvg, 2) : '-' }}
                    </td>

                    @if($isFirst)
                        <td rowspan="{{ $totalRowSpan }}" class="px-1 py-1 border border-black text-center align-middle font-bold {{ $status == 'Lulus' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $status }}
                            @if($status == 'Tidak Lulus' && !empty($promoNote))
                                <br><span class="text-[8px] font-normal">({{ $promoNote }})</span>
                            @endif
                        </td>
                    @endif
                    </tr>
                    @php $isFirst = false; @endphp
                @endforeach
            @endforeach

            <!-- Summary Rows -->
            <tr class="bg-yellow-50">
                <td class="px-2 py-1 border border-black text-left font-bold text-[9px]">Nilai RR</td>
                @foreach($mapels as $mapel)
                    <td class="px-1 py-1 border border-black text-center font-bold">
                        {{ isset($row['summary']['rr'][$mapel->id]) && $row['summary']['rr'][$mapel->id] != 0 ? number_format($row['summary']['rr'][$mapel->id], 2) : '-' }}
                    </td>
                @endforeach
                <td class="border border-black bg-gray-200"></td>
            </tr>
            <tr class="bg-primary/5">
                <td class="px-2 py-1 border border-black text-left font-bold text-[9px]">Nilai UM</td>
                @foreach($mapels as $mapel)
                    <td class="px-1 py-1 border border-black text-center font-bold">
                        {{ isset($row['summary']['um'][$mapel->id]) && $row['summary']['um'][$mapel->id] != 0 ? number_format($row['summary']['um'][$mapel->id]) : '-' }}
                    </td>
                @endforeach
                <td class="border border-black bg-gray-200"></td>
            </tr>
            <tr class="bg-secondary/10">
                <td class="px-2 py-1 border border-black text-left font-bold text-[9px]">Nilai NA</td>
                @foreach($mapels as $mapel)
                    <td class="px-1 py-1 border border-black text-center font-bold text-secondary-dark">
                        {{ isset($row['summary']['na'][$mapel->id]) && $row['summary']['na'][$mapel->id] != 0 ? number_format($row['summary']['na'][$mapel->id], 2) : '-' }}
                    </td>
                @endforeach
                <td class="border border-black bg-gray-200"></td>
            </tr>
        </tbody>
        @endforeach
    </table>

    <!-- Legend & Signature -->
    <div class="mt-4 text-[10px] break-inside-avoid print:break-inside-avoid">
        <div class="flex justify-between items-end">
            <div class="mb-4 max-w-[60%]">
                <strong>Keterangan:</strong><br>
                1. Rata-Rapor (RR) diambil dari Rata-rata Nilai Rapor semester/kelas yang ditentukan.<br>
                2. Rumus Nilai Akhir: <strong>NA = (Rapor × {{ $bRapor }}%) + (Ujian × {{ $bUjian }}%)</strong>.<br>
                3. Kriteria Kelulusan: Rata-rata Nilai Akhir minimal <strong>{{ number_format($minLulus, 2) }}</strong>.
            </div>

            <div class="text-center w-[250px] pr-6">
                @php
                    $hmTitle = 'Kepala Madrasah';
                    if ($jenjang === 'MI') $hmTitle = 'Kepala Madrasah Ibtidaiyah';
                    if ($jenjang === 'MTS') $hmTitle = 'Kepala Madrasah Tsanawiyah';

                    $key = strtolower($jenjang);

                    // Fetch Specific Identity for this Jenjang (MTS vs MI)
                    $identity = \App\Models\IdentitasSekolah::where('jenjang', $jenjang)->first();

                    if (!$identity) {
                        $identity = $school;
                    }

                    $hmName = $identity->kepala_madrasah ?? '......................';
                    $hmNip = $identity->nip_kepala ?? '-';

                    // Legacy/Fallback Logic
                    if (empty($hmName) || $hmName == '......................') {
                         if ($jenjang == 'MTS' && !empty($school->kepala_madrasah_mts)) {
                            $hmName = $school->kepala_madrasah_mts;
                            $hmNip = $school->nip_kepala_mts ?? $hmNip;
                         }
                    }

                    // Simple Date (Masehi ONLY for DKN)
                    $dateNow = \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y');
                    $place = \App\Models\GlobalSetting::val('titimangsa_tempat_' . $key) ?? $school->kabupaten ?? $school->kota ?? 'Tempat';
                @endphp

                <p class="mb-1">{{ $place }}, {{ $dateNow }}</p>
                <p class="mb-16">{{ $hmTitle }},</p>
                <p class="font-bold underline">{{ $hmName }}</p>
                <p>NIP. {{ $hmNip }}</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: landscape; margin: 5mm; }

        /* RESET LAYOUT FOR MULTI-PAGE PRINTING */
        html, body, .h-screen, .overflow-hidden, .flex, .flex-col {
            height: auto !important;
            min-height: 0 !important;
            overflow: visible !important;
            position: static !important;
            display: block !important; /* Break flexbox locking */
            background-color: white !important; /* Remove Gray Dashboard BG */
            background: white !important;
        }

        /* ISOLATION TRICK: Hide everything, then show only #printable-dkn */
        body * {
            visibility: hidden;
        }

        #printable-dkn, #printable-dkn * {
            visibility: visible;
        }

        #printable-dkn {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: white !important;
            z-index: 99999;
        }

        /* Ensure Table Fonts for Print */
        table {
            font-size: 8pt !important; /* Reduced from 9pt */
            font-family: Arial, sans-serif !important;
            line-height: 1; /* Tighter lines */
            border-collapse: collapse !important;
            border: 0.5px solid #000 !important; /* Thinner Outer Border */
        }

        td, th {
            padding: 1px 2px !important;
            vertical-align: middle;
            border: 0.5px solid #000 !important; /* Thinner Inner Borders */
        }

        /* Force background colors */
        .bg-yellow-50 { background-color: #fefce8 !important; -webkit-print-color-adjust: exact; }
        .bg-primary\/5 { background-color: #eff6ff !important; -webkit-print-color-adjust: exact; } /* Fallback Light Blue/Gray */
        .bg-secondary\/10 { background-color: #dcfce7 !important; -webkit-print-color-adjust: exact; } /* Fallback Light Green */
        .bg-gray-100 { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
