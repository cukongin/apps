@extends('layouts.app')

@section('title', 'Diagnosa Import')

@section('content')
<div class="card-boss !p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-full text-red-600 dark:text-red-400">
            <span class="material-symbols-outlined text-2xl">bug_report</span>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Diagnosa Gagal Import</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Sistem gagal membaca data siswa. Berikut adalah hasil analisa file Anda.</p>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <div class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Jenis File Terdeteksi</div>
            <div class="font-bold text-lg text-slate-900 dark:text-white mt-1">{{ $fileType }}</div>
        </div>
        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <div class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Header Kolom</div>
            <div class="font-bold text-lg {{ $headerFound ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400' }} mt-1">
                {{ $headerFound ? 'DITEMUKAN' : 'TIDAK DITEMUKAN' }}
            </div>
            @if($headerFound)
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-1">Baris ke-{{ $headerRowIndex + 1 }}</div>
            @endif
        </div>
        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <div class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Total Baris Dibaca</div>
            <div class="font-bold text-lg text-slate-900 dark:text-white mt-1">{{ $totalRows }}</div>
        </div>
    </div>

    <!-- Mapping Info -->
    <div class="mb-6 card-boss !p-4 !bg-slate-50/50 dark:!bg-slate-800/50 border-dashed">
        <h3 class="font-bold text-slate-900 dark:text-white mb-2 text-sm uppercase tracking-wider">Mapping Kolom (Otomatis)</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($columnMapping as $col => $idx)
            <span class="px-2 py-1 rounded bg-primary/10 text-primary text-xs font-bold border border-primary/20">
                {{ $col }}: Index {{ $idx }}
            </span>
            @endforeach
        </div>
    </div>

    <!-- Data Preview -->
    <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">preview</span> Sample 5 Baris Data Pertama
    </h3>
    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700 mb-6 custom-scrollbar">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-100 dark:bg-slate-800 font-bold text-slate-600 dark:text-slate-400 uppercase text-xs">
                <tr>
                    <th class="p-3 border-b border-slate-200 dark:border-slate-700">Row #</th>
                    <th class="p-3 border-b border-slate-200 dark:border-slate-700">Raw Content (Cols Extracted)</th>
                    <th class="p-3 border-b border-slate-200 dark:border-slate-700">Status Validasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-surface-dark">
                @foreach($sampleRows as $row)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="p-3 border-r border-slate-100 dark:border-slate-700 font-mono text-xs text-slate-500">{{ $row['index'] }}</td>
                    <td class="p-3 border-r border-slate-100 dark:border-slate-700">
                        <div class="flex flex-wrap gap-1">
                            @foreach($row['cols'] as $k => $v)
                            <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-xs border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-mono" title="Index {{ $k }}">
                                [{{ $k }}] {{ Str::limit($v, 20) }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="p-3 text-xs">
                        @if($row['status'] == 'OK')
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded">OK - Valid</span>
                        @else
                            <span class="text-rose-500 dark:text-rose-400 font-bold bg-rose-50 dark:bg-rose-900/20 px-2 py-1 rounded">{{ $row['status'] }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(isset($validationErrors) && count($validationErrors) > 0)
    <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800 rounded-xl">
        <h3 class="font-bold text-rose-700 dark:text-rose-400 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">warning</span> Detail Error Validasi:
        </h3>
        <ul class="list-disc pl-5 text-sm text-rose-600 dark:text-rose-300 space-y-1">
            @foreach(array_slice($validationErrors, 0, 10) as $err)
                <li>
                    <b>Baris {{ $err['row'] ?? '?' }}:</b> {{ $err['message'] ?? 'Error tidak diketahui' }}
                </li>
            @endforeach
            @if(count($validationErrors) > 10)
                <li class="italic font-bold mt-2">... dan {{ count($validationErrors) - 10 }} error lainnya.</li>
            @endif
        </ul>
    </div>
    @endif

    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
        <a href="{{ url()->previous() }}" class="btn-boss bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700">Kembali</a>
    </div>
</div>
@endsection
