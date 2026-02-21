@extends('layouts.app')

@section('title', 'Input Nilai Ekskul' . ($kelas ? ' - ' . $kelas->nama_kelas : ''))

@section('content')
@if(!$kelas)
<div class="mb-6 space-y-4 shrink-0">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-primary/10 rounded-xl text-primary">
            <span class="material-symbols-outlined text-3xl">sports_soccer</span>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Input Nilai Ekstrakurikuler</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Pilih kelas untuk menginput nilai ekstrakurikuler siswa.</p>
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
                <span class="text-primary">Ekskul</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">sports_soccer</span>
                Input Nilai Ekstrakurikuler
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
                Pilih kegiatan dan berikan nilai untuk siswa <span class="text-slate-800 dark:text-white font-bold">{{ $kelas->nama_kelas }}</span>.
            </p>
        </div>
        <button type="submit" form="ekskulForm" class="btn-boss btn-primary px-6 py-2.5 shadow-lg shadow-primary/30 flex items-center gap-2">
            <span class="material-symbols-outlined">save</span> <span class="font-bold">Simpan Perubahan</span>
        </button>
    </div>

    <!-- Admin / TU Action & Filter -->
    @if(auth()->user()->isAdmin() || auth()->user()->isTu())
    <div class="card-boss !p-4 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50 dark:bg-slate-800/50">
        <!-- Back Button -->
        <a href="{{ route('ekskul.index') }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            <span>Kembali ke Pilihan Kelas</span>
        </a>

        <!-- Period Selector -->
        <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row w-full md:w-auto gap-3">
             <input type="hidden" name="kelas_id" value="{{ request('kelas_id') ?: ($kelas->id ?? '') }}">
             <div class="relative group w-full md:w-auto">
                <select name="periode_id" class="input-boss appearance-none !bg-none !pl-9 !pr-8 w-full md:min-w-[200px]" onchange="this.form.submit()">
                    @if(isset($allPeriods) && $allPeriods->count() > 0)
                        @foreach($allPeriods as $p)
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

    <!-- Form Table -->
    <div class="card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <form id="ekskulForm" action="{{ route('walikelas.ekskul.store') }}" method="POST">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 w-10 text-center">No</th>
                            <th class="px-6 py-4 min-w-[250px]">Nama Siswa</th>
                            <th class="px-6 py-4 text-center bg-primary/5 border-l border-r border-slate-200 dark:border-slate-700">Kegiatan 1</th>
                            <th class="px-6 py-4 text-center bg-indigo-50/50 border-r border-slate-200 dark:border-slate-700">Kegiatan 2</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 bg-white dark:bg-[#1a2332]">
                        @foreach($students as $index => $ak)
                        @php
                            $nilai = $ekskulRows[$ak->id_siswa] ?? collect([]);
                            $ekskul1 = $nilai->get(0);
                            $ekskul2 = $nilai->get(1);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-4 text-slate-500 text-center font-bold align-top pt-6">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 align-top pt-6">
                                <div class="font-bold text-slate-900 dark:text-white text-base">{{ $ak->siswa->nama_lengkap }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $ak->siswa->nis_lokal }}</div>
                            </td>

                            <!-- Kegiatan 1 -->
                            <td class="px-4 py-4 bg-primary/5 border-l border-r border-slate-100 dark:border-slate-700 align-top">
                                <div class="flex flex-col gap-2 p-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">sports_tennis</span>
                                        </span>
                                        <input type="text" list="ekskulList" name="ekskul[{{ $ak->id_siswa }}][0][nama_ekskul]" value="{{ optional($ekskul1)->nama_ekskul }}" placeholder="Nama Kegiatan (ex: Pramuka)" class="input-boss w-full !pl-8 !text-sm">
                                    </div>

                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="col-span-1 relative">
                                            <select name="ekskul[{{ $ak->id_siswa }}][0][nilai]" class="input-boss w-full !text-sm !pl-2 !pr-6 cursor-pointer">
                                                <option value="">Nilai</option>
                                                <option value="A" {{ optional($ekskul1)->nilai == 'A' ? 'selected' : '' }}>A (Sangat Baik)</option>
                                                <option value="B" {{ optional($ekskul1)->nilai == 'B' ? 'selected' : '' }}>B (Baik)</option>
                                                <option value="C" {{ optional($ekskul1)->nilai == 'C' ? 'selected' : '' }}>C (Cukup)</option>
                                            </select>
                                        </div>
                                        <div class="col-span-2">
                                            <input type="text" name="ekskul[{{ $ak->id_siswa }}][0][keterangan]" value="{{ optional($ekskul1)->keterangan }}" placeholder="Keterangan..." class="input-boss w-full !text-sm">
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kegiatan 2 -->
                            <td class="px-4 py-4 bg-indigo-50/30 border-r border-slate-100 dark:border-slate-700 align-top">
                                <div class="flex flex-col gap-2 p-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                         <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">sports_soccer</span>
                                        </span>
                                        <input type="text" list="ekskulList" name="ekskul[{{ $ak->id_siswa }}][1][nama_ekskul]" value="{{ optional($ekskul2)->nama_ekskul }}" placeholder="Nama Kegiatan (ex: Futsal)" class="input-boss w-full !pl-8 !text-sm">
                                    </div>

                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="col-span-1 relative">
                                            <select name="ekskul[{{ $ak->id_siswa }}][1][nilai]" class="input-boss w-full !text-sm !pl-2 !pr-6 cursor-pointer">
                                                <option value="">Nilai</option>
                                                <option value="A" {{ optional($ekskul2)->nilai == 'A' ? 'selected' : '' }}>A (Sangat Baik)</option>
                                                <option value="B" {{ optional($ekskul2)->nilai == 'B' ? 'selected' : '' }}>B (Baik)</option>
                                                <option value="C" {{ optional($ekskul2)->nilai == 'C' ? 'selected' : '' }}>C (Cukup)</option>
                                            </select>
                                        </div>
                                         <div class="col-span-2">
                                            <input type="text" name="ekskul[{{ $ak->id_siswa }}][1][keterangan]" value="{{ optional($ekskul2)->keterangan }}" placeholder="Keterangan..." class="input-boss w-full !text-sm">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<datalist id="ekskulList">
    @foreach($ekskulOptions as $opt)
        <option value="{{ $opt }}">
    @endforeach
</datalist>
@endif

@endsection

