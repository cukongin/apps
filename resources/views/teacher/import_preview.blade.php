@extends('layouts.app')

@section('title', 'Preview Import Nilai')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
        <a href="{{ route('teacher.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('teacher.input-nilai', ['kelas' => $assignment->id_kelas, 'mapel' => $assignment->id_mapel]) }}" class="hover:text-primary transition-colors">Input Nilai</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-slate-900 dark:text-white font-medium">Preview Import</span>
    </div>

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Hasil Validasi Import</h1>
            <p class="text-slate-500 dark:text-slate-400">Periksa kembali data sebelum diproses masuk ke sistem.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('teacher.input-nilai', ['kelas' => $assignment->id_kelas, 'mapel' => $assignment->id_mapel]) }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm">Batal</a>

            @if(count($validData) > 0)
            <form action="{{ route('teacher.input-nilai.process') }}" method="POST">
                @csrf
                <input type="hidden" name="import_key" value="{{ $importKey }}">
                <button type="submit" class="btn-boss btn-primary flex items-center gap-2 shadow-lg shadow-primary/30">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Proses {{ count($validData) }} Data Valid
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-boss !p-4 flex flex-col justify-center">
            <span class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">Total Data</span>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ count($validData) + count($importErrors) }} Baris</p>
        </div>
        <div class="card-boss !p-4 flex flex-col justify-center border-l-4 !border-l-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/10">
            <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider">Valid</span>
            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-1">{{ count($validData) }}</p>
        </div>
        <div class="card-boss !p-4 flex flex-col justify-center border-l-4 !border-l-rose-500 bg-rose-50/50 dark:bg-rose-900/10">
            <span class="text-rose-600 dark:text-rose-400 text-xs font-bold uppercase tracking-wider">Error</span>
            <p class="text-2xl font-bold text-rose-700 dark:text-rose-400 mt-1">{{ count($importErrors) }}</p>
        </div>
    </div>

    @if(count($importErrors) > 0)
    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl p-4">
        <h3 class="font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined">warning</span> Data Error (Tidak akan diproses)
        </h3>
        <ul class="list-disc list-inside text-sm text-rose-700 dark:text-rose-400 space-y-1">
            @foreach($importErrors as $err)
            <li>Baris {{ $err['row'] }}: {{ $err['message'] }}</li>
            @endforeach
        </ul>
        <p class="mt-3 text-xs text-rose-600 dark:text-rose-400 italic">Silakan perbaiki data di file Excel dan upload ulang.</p>
    </div>
    @endif

    <div class="card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 bg-slate-50/50 dark:bg-slate-800/50">
            <h3 class="font-bold text-slate-900 dark:text-white">Preview Data Valid</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3">NIS</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3 text-center">Harian</th>
                        <th class="px-6 py-3 text-center">
                            {{ $assignment->kelas->jenjang->kode == 'MI' ? 'Ujian Cawu' : 'PTS' }}
                        </th>
                        @if($assignment->kelas->jenjang->kode !== 'MI')
                        <th class="px-6 py-3 text-center">EHB</th>
                        @endif
                        <th class="px-6 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($validData as $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $row['nis'] }}</td>
                        <td class="px-6 py-3 font-medium text-slate-900 dark:text-white">{{ $row['nama'] }}</td>
                        <td class="px-6 py-3 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['harian'] }}</td>
                        <td class="px-6 py-3 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['uts'] }}</td>
                        @if($assignment->kelas->jenjang->kode !== 'MI')
                        <td class="px-6 py-3 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['uas'] }}</td>
                        @endif
                        <td class="px-6 py-3 text-slate-500 italic">{{ $row['catatan'] ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Tidak ada data valid untuk ditampilkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
