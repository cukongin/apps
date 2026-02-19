@extends('layouts.app')

@section('title', 'Cetak Rapor')

@section('content')
<div class="flex flex-col h-[calc(100vh-80px)] overflow-hidden">
    <!-- Header & Filters Stack -->
    <div class="mb-6 space-y-4 shrink-0">
        <!-- Header Title -->
        <div class="flex items-center gap-3">
            <div class="p-3 bg-primary/10 rounded-xl text-primary">
                <span class="material-symbols-outlined text-3xl">print</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Cetak Rapor</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Pilih siswa untuk mencetak Rapor Capaian Kompetensi.</p>
            </div>
        </div>

        <!-- Filters Toolbar (Left Aligned) -->
        <div class="flex flex-wrap items-center gap-3">

            <!-- Year Selector (Admin/TU) -->
            @if(isset($years) && count($years) > 1)
            <form action="{{ route('reports.index') }}" method="GET">
                <div class="relative group">
                    <select name="year_id" class="input-boss appearance-none !bg-none !pl-10 !pr-8 py-2.5 min-w-[200px]" onchange="this.form.submit()">
                        @foreach($years as $y)
                            <option value="{{ $y->id }}" {{ isset($selectedYear) && $selectedYear->id == $y->id ? 'selected' : '' }}>
                                {{ $y->nama }} {{ $y->status == 'aktif' ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>
            </form>
            @elseif(isset($years) && count($years) == 1)
                <div class="flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-slate-700 bg-slate-100/50 border-2 border-slate-200 rounded-xl dark:bg-slate-800 dark:text-white dark:border-slate-700">
                    <span class="material-symbols-outlined text-slate-400">calendar_month</span>
                    {{ $years->first()->nama }}
                </div>
            @endif


            <!-- Class Selector -->
            <form action="{{ route('reports.index') }}" method="GET" class="flex gap-2">
                @if(isset($selectedYear))
                <input type="hidden" name="year_id" value="{{ $selectedYear->id }}">
                @endif

                @if(count($classes) > 1)
                <div class="relative group">
                    <select name="class_id" class="input-boss appearance-none !bg-none !pl-10 !pr-8 py-2.5 min-w-[220px]" onchange="this.form.submit()">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ isset($selectedClass) && $selectedClass->id == $c->id ? 'selected' : '' }}>
                                {{ $c->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[20px]">class</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>
                @elseif(count($classes) == 1)
                     <div class="flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-slate-700 bg-slate-100/50 border-2 border-slate-200 rounded-xl dark:bg-slate-800 dark:text-white dark:border-slate-700">
                        <span class="material-symbols-outlined text-slate-400">class</span>
                        {{ $classes->first()->nama_kelas }}
                     </div>
                @else
                    <div class="px-4 py-2 text-sm text-rose-500 font-medium bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        Tidak Ada Kelas
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Student List -->
    <div class="card-boss !p-0 flex-1 overflow-hidden flex flex-col shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 h-full">
        @if($selectedClass)
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50 shrink-0">
                <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400">groups</span>
                    Daftar Siswa <span class="bg-slate-200 dark:bg-slate-700 px-2 py-0.5 rounded text-xs ml-1">{{ $students->count() }}</span>
                </span>

                @if($students->count() > 0)
                <a href="{{ route('reports.print.all', $selectedClass->id) }}" target="_blank" class="btn-boss btn-primary flex items-center gap-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    Download Semua
                </a>
                @endif
            </div>

            <div class="overflow-auto custom-scrollbar flex-1 relative">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-10 font-bold">
                        <tr>
                            <th class="px-6 py-4">NIS / NISN</th>
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $member)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4 font-mono text-slate-500 text-xs">{{ $member->siswa->nis_lokal }} / {{ $member->siswa->nisn }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $member->siswa->nama_lengkap }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('reports.print.cover', ['student' => $member->siswa->id, 'year_id' => $selectedYear->id ?? null]) }}" target="_blank" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs py-1.5 px-3 h-auto min-h-0" title="Cetak Cover">
                                        Cover
                                    </a>
                                    <a href="{{ route('reports.print.biodata', ['student' => $member->siswa->id, 'year_id' => $selectedYear->id ?? null]) }}" target="_blank" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs py-1.5 px-3 h-auto min-h-0" title="Cetak Biodata">
                                        Biodata
                                    </a>

                                    <a href="{{ route('reports.print', ['student' => $member->siswa->id, 'year_id' => $selectedYear->id ?? null]) }}" target="_blank" class="btn-boss bg-emerald-500 hover:bg-emerald-600 text-white border-none shadow-lg shadow-emerald-500/20 text-xs py-1.5 px-3 h-auto min-h-0 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">print</span>
                                        Rapor
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 italic">
                                Tidak ada siswa di kelas ini.
                            </td>
                        </tr>
                        @endforelse
                        <tr class="h-10 border-none"><td colspan="3"></td></tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center flex-1 p-8 text-center cursor-pointer bg-slate-50/50 dark:bg-slate-800/10">
                <div class="bg-slate-100 dark:bg-slate-800 p-6 rounded-full mb-4 animate-pulse">
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600">print_disabled</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Belum Ada Kelas Dipilih</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-sm">Silakan pilih kelas di atas untuk mulai mencetak rapor siswa.</p>
            </div>
        @endif
    </div>
</div>
@endsection
