@extends('layouts.app')

@section('title', 'Leger Nilai')

@section('content')
<div class="flex flex-col h-[calc(100vh-80px)] overflow-hidden">
    <!-- Header & Filters Stack -->
    <div class="mb-6 space-y-4 shrink-0">
        <!-- Header Title -->
        <div class="flex items-center gap-3">
            <div class="p-3 bg-primary/10 rounded-xl text-primary">
                <span class="material-symbols-outlined text-3xl">table_view</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Leger Nilai</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Lihat dan cetak Leger Nilai siswa untuk arsip dan evaluasi.</p>
            </div>
        </div>

        <!-- Filters Toolbar (Responsive Grid) -->
        <div class="flex flex-col gap-3">
            <div class="flex flex-wrap items-center gap-3">

                <!-- Year Selector -->
                @if(isset($years) && count($years) > 0)
                <form action="{{ route('reports.leger') }}" method="GET" class="w-full md:w-auto">
                    <div class="relative group">
                        <select name="year_id" class="input-boss !pl-4 !pr-10 py-2.5 min-w-[200px]" onchange="this.form.submit()">
                            @foreach($years as $y)
                                <option value="{{ $y->id }}" {{ isset($selectedYear) && $selectedYear->id == $y->id ? 'selected' : '' }}>
                                    {{ $y->nama }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </div>
                    </div>
                </form>
                @endif

                <!-- Jenjang Selector (Converted to Dropdown) -->
                <form action="{{ route('reports.leger') }}" method="GET" class="w-full md:w-auto">
                    <input type="hidden" name="year_id" value="{{ $selectedYear->id }}">
                    <div class="relative group">
                        <select name="jenjang" class="input-boss !pl-4 !pr-10 py-2.5 min-w-[100px]" onchange="this.form.submit()">
                            @foreach(['MI', 'MTS'] as $jenjang)
                            <option value="{{ $jenjang }}" {{ (isset($selectedJenjang) && $selectedJenjang == $jenjang) ? 'selected' : '' }}>
                                {{ $jenjang }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </div>
                    </div>
                </form>

                <!-- Class Selector -->
                <form action="{{ route('reports.leger') }}" method="GET" class="w-full md:w-auto flex flex-1">
                    @if(isset($selectedYear))
                    <input type="hidden" name="year_id" value="{{ $selectedYear->id }}">
                    <input type="hidden" name="jenjang" value="{{ $selectedJenjang ?? 'MI' }}">
                    @endif

                    @if(count($classes) > 1)
                    <div class="relative group w-full md:w-auto">
                        <select name="class_id" class="input-boss !pl-10 !pr-10 py-2.5 min-w-[200px]" onchange="this.form.submit()">
                            <option value="">Pilih Kelas...</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ isset($selectedClass) && $selectedClass->id == $c->id ? 'selected' : '' }}>
                                    {{ $c->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">class</span>
                        </div>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </div>
                    </div>
                    @elseif(count($classes) == 1)
                         <div class="flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-slate-700 bg-slate-100/50 border-2 border-slate-200 rounded-xl dark:bg-slate-800 dark:text-white dark:border-slate-700 w-full md:w-auto justify-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">class</span>
                            {{ $classes->first()->nama_kelas }}
                         </div>
                         <input type="hidden" name="class_id" value="{{ $classes->first()->id }}">
                    @else
                        <div class="px-4 py-2 text-sm text-rose-500 font-medium bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-center gap-2 w-full">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            Tidak Ada Kelas
                        </div>
                    @endif
                </form>

                <!-- Period Selector -->
                @if($selectedClass && isset($periodes) && count($periodes) > 0)
                <form action="{{ route('reports.leger') }}" method="GET" class="w-full md:w-auto">
                    <input type="hidden" name="year_id" value="{{ $selectedYear->id }}">
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                    <input type="hidden" name="jenjang" value="{{ $selectedJenjang ?? 'MI' }}">
                    @if(request('show_original'))
                    <input type="hidden" name="show_original" value="1">
                    @endif

                    <div class="relative group w-full">
                        <select name="period_id" class="input-boss !pl-10 !pr-10 py-2.5 min-w-[200px]" onchange="this.form.submit()">
                            @foreach($periodes as $p)
                                <option value="{{ $p->id }}" {{ isset($periode) && $periode->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_periode }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">date_range</span>
                        </div>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </div>
                    </div>
                </form>
                @endif
            </div>

            <!-- X-Ray Toggle -->
            @if($selectedClass && isset($periodes))
            <form action="{{ route('reports.leger') }}" method="GET" class="flex justify-end">
                <input type="hidden" name="year_id" value="{{ $selectedYear->id }}">
                <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                <input type="hidden" name="jenjang" value="{{ $selectedJenjang ?? 'MI' }}">
                <input type="hidden" name="period_id" value="{{ $periode->id ?? '' }}">

                <button type="submit" name="show_original" value="{{ $showOriginal ? '0' : '1' }}"
                    class="btn-boss text-xs flex items-center gap-2 {{ $showOriginal ? 'bg-amber-100 border-amber-400 text-amber-800' : 'bg-white border-slate-200 text-slate-500 hover:border-primary/50' }}">
                    <span class="material-symbols-outlined {{ $showOriginal ? 'text-amber-600' : 'text-slate-400' }} text-[18px]">visibility</span>
                    {{ $showOriginal ? 'Mode Nilai Asli (Guru): ON' : 'Lihat Nilai Asli (Guru)' }}
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Leger Content -->
    <div class="card-boss !p-0 flex-1 overflow-hidden flex flex-col shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 h-full">
        @if($selectedClass && isset($periode))
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50/50 dark:bg-slate-800/50 shrink-0">
                <div class="flex flex-col">
                    <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">analytics</span>
                        Leger Kelas {{ $selectedClass->nama_kelas }}
                    </span>
                    <span class="text-xs text-slate-500 font-mono mt-0.5">Periode: {{ $periode->nama_periode }}</span>
                </div>

                <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                    <a href="{{ route('reports.leger.rekap.export', ['year_id' => $selectedYear->id, 'class_id' => $selectedClass->id]) }}" target="_blank" class="btn-boss btn-primary flex items-center justify-center gap-2 shadow-lg shadow-primary/20 w-full md:w-auto text-xs py-2">
                        <span class="material-symbols-outlined text-[18px]">table_view</span>
                        Export Rekap Tahunan
                    </a>
                    <a href="{{ route('reports.leger.export', ['year_id' => $selectedYear->id, 'class_id' => $selectedClass->id, 'period_id' => $periode->id, 'show_original' => $showOriginal]) }}" target="_blank" class="btn-boss flex items-center justify-center gap-2 shadow-lg w-full md:w-auto text-xs py-2 {{ $showOriginal ? 'bg-amber-500 hover:bg-amber-600 text-white border-transparent shadow-amber-500/20' : 'bg-emerald-500 hover:bg-emerald-600 text-white border-transparent shadow-emerald-500/20' }}">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Export Excel ({{ $showOriginal ? 'Nilai Murni' : 'Semester' }})
                    </a>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-auto flex-1 relative custom-scrollbar">
                <div class="min-w-max pb-20"> <!-- Container to allow full width for wide table -->
                    <table class="w-full text-left text-sm border-separate border-spacing-0">
                        <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 dark:text-slate-400 sticky top-0 z-20 shadow-sm">
                            <tr>
                                <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 sticky left-0 bg-slate-50 dark:bg-slate-800 z-30 min-w-[50px] shadow-[2px_0_5px_rgba(0,0,0,0.05)] text-center">No</th>
                                <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 sticky left-[50px] bg-slate-50 dark:bg-slate-800 z-30 min-w-[250px] shadow-[2px_0_5px_rgba(0,0,0,0.05)]">Nama Siswa</th>
                                <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 min-w-[60px] text-center border-r border-slate-100 dark:border-slate-700">L/P</th>

                                @foreach($mapels as $mapel)
                                <th class="px-2 py-3 border-b border-r border-slate-200 dark:border-slate-700 min-w-[100px] text-center relative group cursor-help" title="{{ $mapel->nama_mapel }}">
                                    <div class="truncate max-w-[100px] font-bold text-slate-700 dark:text-slate-300">{{ $mapel->nama_mapel }}</div>
                                    <span class="text-[9px] text-slate-400 font-mono">KKM: {{ $kkm[$mapel->id] ?? 70 }}</span>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-slate-800 text-white text-[10px] px-2 py-1 rounded shadow-lg whitespace-nowrap z-50">
                                        {{ $mapel->nama_mapel }}
                                    </div>
                                </th>
                                @endforeach

                                <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 min-w-[80px] text-center bg-blue-50/50 text-blue-800 font-extrabold border-r border-blue-100">Total</th>
                                <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 min-w-[80px] text-center bg-emerald-50/50 text-emerald-800 font-extrabold border-r border-emerald-100">Rata2</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            @foreach($students as $index => $ak)
                            @php
                                $totalScore = 0;
                                $countMapel = 0;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-4 py-3 border-b border-r border-slate-100 dark:border-slate-700 sticky left-0 bg-white dark:bg-[#1a2332] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.05)] text-center font-bold text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 border-b border-r border-slate-100 dark:border-slate-700 sticky left-[50px] bg-white dark:bg-[#1a2332] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.05)] font-bold text-slate-800 dark:text-white truncate max-w-[250px]">
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('reports.student.analytics', $ak->siswa->id) }}" target="_blank" class="hover:text-primary hover:underline truncate" title="{{ $ak->siswa->nama_lengkap }}">
                                            {{ $ak->siswa->nama_lengkap }}
                                        </a>
                                        <a href="{{ route('reports.student.analytics', $ak->siswa->id) }}" target="_blank" class="text-slate-300 hover:text-primary transition-colors ml-2" title="Lihat Analitik Siswa">
                                            <span class="material-symbols-outlined text-[16px]">monitoring</span>
                                        </a>
                                    </div>
                                     <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $ak->siswa->nis_lokal }}</div>
                                </td>
                                <td class="px-4 py-3 text-center border-b border-r border-slate-100 dark:border-slate-700 text-slate-500 font-bold text-xs">{{ $ak->siswa->jenis_kelamin }}</td>

                                <!-- Grades -->
                                @foreach($mapels as $mapel)
                                @php
                                    $grade = $grades->get($ak->id_siswa . '-' . $mapel->id);

                                    // Default values
                                    $score = 0;
                                    $original = 0;
                                    $isKatrol = false;

                                    if ($grade) {
                                        $score = $grade->nilai_akhir;
                                        // Use database column if available, else fallback to current score (assuming no change)
                                        $original = $grade->nilai_akhir_asli ?? $grade->nilai_akhir;

                                        // Detect if adjustment happened
                                        // Use explicit flag OR value difference
                                        $isKatrol = ($score != $original) || ($grade->is_katrol ?? false);
                                    }

                                    // Determine Replaced Value
                                    $displayScore = ($showOriginal && $grade) ? $original : $score;

                                    if ($grade) {
                                        $totalScore += $displayScore;
                                        $countMapel++;
                                    }

                                    $kkmLocal = $kkm[$mapel->id] ?? 70;
                                    $isBelowKkm = $displayScore < $kkmLocal;
                                @endphp
                                <td class="px-2 py-3 text-center border-b border-r border-slate-100 dark:border-slate-700 text-sm
                                    {{ ($showOriginal && $isKatrol) ? 'bg-amber-50 text-amber-900 font-bold' : '' }}
                                    {{ $isBelowKkm ? 'text-rose-600 font-bold bg-rose-50/30' : (($showOriginal && $isKatrol) ? '' : 'text-slate-700 dark:text-slate-300') }}
                                    "
                                    title="{{ $showOriginal && $isKatrol ? 'Nilai Rapor: ' . round($score) . ' (Asli: ' . $original . ')' : '' }}">

                                    @if($grade)
                                        {{ number_format($displayScore, 0) }}
                                    @else
                                        <span class="text-slate-200">-</span>
                                    @endif

                                    @if($showOriginal && $isKatrol)
                                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500 mx-auto mt-1"></div>
                                    @endif
                                </td>
                                @endforeach

                                <!-- Summary -->
                                <td class="px-4 py-3 text-center font-black text-blue-600 bg-blue-50/10 border-b border-r border-slate-100 dark:border-slate-800">
                                    {{ $totalScore > 0 ? number_format($totalScore, 0) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center font-black text-emerald-600 bg-emerald-50/10 border-b border-r border-slate-100 dark:border-slate-800">
                                    {{ $countMapel > 0 ? number_format($totalScore / $countMapel, 2) : '-' }}
                                </td>
                            </tr>
                            @endforeach
                            <!-- Total Row Space -->
                            <tr class="h-20"><td colspan="100%"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden flex flex-col gap-4 p-4 overflow-y-auto flex-1">
                @foreach($students as $index => $ak)
                @php
                    $totalScore = 0;
                    $countMapel = 0;

                    // Pre-calculate Loop for Summary
                     foreach($mapels as $mapel) {
                        $grade = $grades->get($ak->id_siswa . '-' . $mapel->id);
                        $score = 0;
                        $original = 0;
                        if ($grade) {
                             $score = $grade->nilai_akhir;
                             $original = $grade->nilai_akhir_asli ?? $grade->nilai_akhir;
                        }
                        $displayScore = ($showOriginal && $grade) ? $original : $score;
                        if ($grade) {
                             $totalScore += $displayScore;
                             $countMapel++;
                        }
                     }
                     $avg = $countMapel > 0 ? $totalScore / $countMapel : 0;
                @endphp
                <div class="card-boss !p-0 overflow-hidden" x-data="{ expanded: false }">
                    <!-- Header Summary (Always Visible) -->
                    <div class="p-4 flex flex-col gap-3 cursor-pointer" @click="expanded = !expanded">
                        <div class="flex items-center gap-4">
                             <!-- Rank Badge (Placeholder) -->
                             <div class="size-10 flex-shrink-0 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center font-bold border border-slate-200 dark:border-slate-600 shadow-sm">
                                {{ $index + 1 }}
                             </div>
                             <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-base">{{ $ak->siswa->nama_lengkap }}</h4>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                    <span class="font-mono">{{ $ak->siswa->nis_lokal }}</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="font-bold">{{ $ak->siswa->jenis_kelamin }}</span>
                                </div>
                             </div>
                             <div class="flex items-center gap-2">
                                <a href="{{ route('reports.student.analytics', $ak->siswa->id) }}" class="size-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800" title="Analisis Siswa" @click.stop>
                                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                                </a>
                                <button class="size-8 rounded-full bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center text-slate-400 transition-transform duration-200" :class="expanded ? 'rotate-180 bg-primary/10 border-primary/20 text-primary' : ''">
                                    <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                </button>
                             </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="flex gap-3 mt-1">
                            <div class="flex-1 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-lg p-2 flex justify-between items-center">
                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider">Total</span>
                                <span class="font-black text-blue-700 dark:text-blue-300">{{ number_format($totalScore, 0) }}</span>
                            </div>
                            <div class="flex-1 bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30 rounded-lg p-2 flex justify-between items-center">
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider">Rata-Rata</span>
                                <span class="font-black text-emerald-700 dark:text-emerald-300">{{ number_format($avg, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Accordion (Hidden by default) -->
                    <div x-show="expanded" x-collapse style="display: none;" class="border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 p-4">
                        <div class="grid grid-cols-1 gap-2">
                            <div class="grid grid-cols-12 text-[10px] font-bold text-slate-400 uppercase mb-1 px-2">
                                <div class="col-span-8">Mapel</div>
                                <div class="col-span-2 text-center">KKM</div>
                                <div class="col-span-2 text-right">Nilai</div>
                            </div>
                            @foreach($mapels as $mapel)
                            @php
                                $grade = $grades->get($ak->id_siswa . '-' . $mapel->id);
                                $score = 0;
                                $original = 0;
                                $isKatrol = false;
                                if ($grade) {
                                    $score = $grade->nilai_akhir;
                                    $original = $grade->nilai_akhir_asli ?? $grade->nilai_akhir;
                                    $isKatrol = ($score != $original) || ($grade->is_katrol ?? false);
                                }
                                $displayScore = ($showOriginal && $grade) ? $original : $score;
                                $kkmLocal = $kkm[$mapel->id] ?? 70;
                                $isFail = $displayScore < $kkmLocal && $grade;
                            @endphp
                            <div class="grid grid-cols-12 items-center p-2 rounded-lg
                                {{ $isFail ? 'bg-rose-50 border border-rose-100' : 'bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700' }}
                                {{ ($showOriginal && $isKatrol) ? 'bg-amber-50 border-amber-100' : '' }}">
                                <div class="col-span-8 flex flex-col">
                                    <span class="text-xs font-bold {{ $isFail ? 'text-rose-800' : 'text-slate-700 dark:text-slate-300' }} {{ ($showOriginal && $isKatrol) ? 'text-amber-900' : '' }}">{{ $mapel->nama_mapel }}</span>
                                    @if($showOriginal && $isKatrol)
                                        <span class="text-[9px] text-amber-600 mt-0.5">Rapor: {{ round($score) }} (Asli: {{ $original }})</span>
                                    @endif
                                </div>
                                <div class="col-span-2 text-center">
                                    <span class="text-xs font-medium {{ $isFail ? 'text-rose-500' : 'text-slate-400' }}">{{ $kkmLocal }}</span>
                                </div>
                                <div class="col-span-2 text-right">
                                    <span class="text-sm font-black {{ $isFail ? 'text-rose-700' : 'text-slate-800 dark:text-white' }} {{ ($showOriginal && $isKatrol) ? 'text-amber-700' : '' }}">
                                        {{ $grade ? number_format($displayScore, 0) : '-' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center flex-1 p-8 text-center cursor-pointer bg-slate-50/50 dark:bg-slate-800/10">
                <div class="bg-slate-100 dark:bg-slate-800 p-6 rounded-full mb-4 animate-pulse">
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600">table_view</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Belum Ada Kelas Dipilih</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-sm">Silakan pilih Tahun Ajaran dan Kelas di atas untuk melihat Leger Nilai.</p>
            </div>
        @endif
    </div>
</div>
@endsection
