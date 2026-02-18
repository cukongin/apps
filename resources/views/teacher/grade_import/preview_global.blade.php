@extends('layouts.app')

@section('title', 'Preview Import Global')

@section('content')
<div class="space-y-6">
    <div class="card-boss !p-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">preview</span>
            Preview Import {{ $jenjang }}
        </h1>
        <a href="{{ route('grade.import.global.index') }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700">Batal</a>
    </div>

    @if(!empty($errors))
    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-xl shadow-sm">
        <strong class="font-bold flex items-center gap-2"><span class="material-symbols-outlined">error</span> Ditemukan {{ count($errors) }} Masalah:</strong>
        <ul class="mt-2 list-disc list-inside text-sm max-h-40 overflow-y-auto custom-scrollbar">
            @foreach($errors as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <p class="mt-2 font-bold text-xs uppercase tracking-wide">Data yang error (baris tersebut) akan DILEWATI. Data valid tetap akan diproses.</p>
    </div>
    @endif

    <div class="card-boss !p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="size-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
            </div>
            <div>
                <p class="text-slate-600 dark:text-slate-400">Total Data Valid: <strong class="text-slate-900 dark:text-white text-lg">{{ count($parsedData) }} Siswa</strong>.</p>
                <p class="text-sm text-slate-500 dark:text-slate-500">Klik "Proses Import Sekarang" untuk menyimpan data ke database.</p>
            </div>
        </div>

        <!-- Preview Table (Limit 10 rows) -->
        <div class="overflow-hidden border border-slate-200 dark:border-slate-700 rounded-xl mb-6 shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">Kelas</th>
                        <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">Nama Siswa</th>
                        <th class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 text-center">Jumlah Nilai Diupdate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-surface-dark">
                    @foreach(array_slice($parsedData, 0, 10) as $row)
                    @php
                        $totalGrades = 0;
                        if(isset($row['grades'])) {
                            foreach($row['grades'] as $periods) {
                                foreach($periods as $mapel) {
                                    $totalGrades++; // Simplification
                                }
                            }
                        }
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono text-xs">{{ $row['kelas_id'] }} (ID)</td>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ $row['siswa']->nama_lengkap }}</td>
                        <td class="px-4 py-3 text-center text-slate-600 dark:text-slate-300">
                            <span class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs font-bold border border-slate-200 dark:border-slate-700">{{ $totalGrades }} Mapel/Periode</span>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($parsedData) > 10)
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-center text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-800/50">... dan {{ count($parsedData) - 10 }} siswa lainnya.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <form action="{{ route('grade.import.global.store') }}" method="POST">
            @csrf
            <input type="hidden" name="import_key" value="{{ $importKey }}">
            <button type="submit" class="btn-boss btn-primary w-full md:w-auto flex items-center justify-center gap-2 shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined">save</span>
                Proses Import Sekarang
            </button>
        </form>
    </div>
</div>
@endsection
