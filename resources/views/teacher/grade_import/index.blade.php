@extends('layouts.app')

@section('title', 'Import Nilai Kolektif')

@section('content')
<div class="flex flex-col gap-6">
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('teacher.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="font-bold text-slate-800 dark:text-white">Import Kolektif</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Import Nilai Kolektif Kelas {{ $kelas->nama_kelas }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Isi nilai untuk <span class="font-bold text-primary">{{ $mapelCount }}</span> mata pelajaran sekaligus dalam satu file Excel.
            </p>
        </div>
        <a href="{{ route('teacher.dashboard') }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span> Kembali
        </a>
    </div>

    <!-- Step 1: Template -->
    <div class="card-boss !p-8 bg-primary/5 dark:bg-primary/10 border-dashed !border-primary/20 flex flex-col md:flex-row items-start gap-6 relative overflow-hidden group">
        <div class="absolute -right-10 -top-10 opacity-5 group-hover:opacity-10 transition-opacity">
            <span class="material-symbols-outlined text-[150px] text-primary">download</span>
        </div>

        <div class="size-16 bg-primary/10 dark:bg-primary/20 rounded-2xl flex items-center justify-center flex-shrink-0 z-10">
            <span class="font-black text-2xl text-primary">1</span>
        </div>

        <div class="flex-1 z-10">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Langkah 1: Download Template Master</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm mb-6 leading-relaxed max-w-2xl">
                Download template khusus kelas ini. Template berisi kolom untuk semua mapel yang diajarkan di kelas <span class="font-bold">{{ $kelas->nama_kelas }}</span>.
                <br><span class="inline-block mt-2 px-3 py-1 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-bold border border-rose-100 dark:border-rose-800"><span class="material-symbols-outlined text-[14px] align-text-bottom">warning</span> PENTING: Jangan mengubah ID di header kolom (misal: [123]).</span>
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('grade.import.template', $kelas->id) }}" class="btn-boss btn-primary flex items-center gap-2 shadow-lg shadow-primary/30">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    Download Template (Periode Aktif)
                </a>
                <a href="{{ route('grade.import.template', ['kelas' => $kelas->id, 'type' => 'tahunan']) }}" class="btn-boss bg-white dark:bg-slate-800 text-primary border border-primary/20 hover:bg-primary/5 dark:hover:bg-primary/10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                    Download Template Tahunan (3 Periode)
                </a>
            </div>
        </div>
    </div>

    <!-- Step 2: Upload -->
    <div class="card-boss !p-8 flex flex-col md:flex-row items-start gap-6">
        <div class="size-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center flex-shrink-0">
            <span class="font-black text-2xl text-slate-500 dark:text-slate-400">2</span>
        </div>

        <div class="flex-1">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Langkah 2: Upload File & Validasi</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm mb-6 max-w-2xl">
                Upload file Excel yang sudah diisi. Sistem akan memvalidasi data sebelum disimpan.
            </p>

            <form action="{{ route('grade.import.preview', $kelas->id) }}" method="POST" enctype="multipart/form-data" class="max-w-xl p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                @csrf
                <div class="space-y-6">
                    <div>
                       <label class="block text-sm font-bold mb-3 text-slate-700 dark:text-slate-300">Pilih File (.csv, .xlsx)</label>
                       <input type="file" name="file" required class="block w-full text-sm text-slate-500
                          file:mr-4 file:py-3 file:px-6
                          file:rounded-xl file:border-0
                          file:text-sm file:font-bold
                          file:bg-primary file:text-white
                          hover:file:bg-primary-dark
                          file:cursor-pointer cursor-pointer
                          border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800
                        "/>
                    </div>

                    <div class="pt-2 flex flex-col gap-3">
                        <button type="submit" class="btn-boss btn-primary w-full flex justify-center items-center gap-2 shadow-lg shadow-primary/30">
                            <span class="material-symbols-outlined">table_view</span>
                            Preview & Edit Data
                        </button>
                        <p class="text-xs text-center text-slate-500 dark:text-slate-400">
                            Anda akan masuk ke halaman preview untuk mengecek data.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
