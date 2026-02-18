@extends('layouts.app')

@section('title', 'Import Leger (Unified)')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
        <a href="{{ route('teacher.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-slate-900 dark:text-white font-medium">Import Leger</span>
    </div>

    <div class="card-boss !p-6">
        <div class="flex flex-col gap-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-3xl">cloud_sync</span>
                    Import Leger Lengkap (One Click)
                </h1>

                <div class="flex items-center gap-3 mt-3">
                    <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-bold border border-primary/20">
                        {{ $jenjangLabel }}
                    </span>
                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-bold border border-slate-200 dark:border-slate-600">
                        Periode: {{ $periodName }}
                    </span>
                </div>

                <p class="text-slate-500 dark:text-slate-400 mt-4 leading-relaxed max-w-3xl">
                    Fitur ini memungkinkan Anda mengimpor <b class="text-slate-900 dark:text-white">Nilai, Absensi, dan Sikap</b> sekaligus dari satu file Excel (CSV).
                    Sangat cocok untuk pengisian rapor massal (Leger) di akhir semester.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-2">
                <!-- Step 1: Download -->
                <div class="card-boss !p-8 border-dashed flex flex-col gap-4 relative overflow-hidden group hover:border-primary/50 transition-colors">
                    <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity">
                         <span class="material-symbols-outlined text-[120px] text-primary">download</span>
                    </div>

                    <div class="flex items-center gap-4 text-slate-900 dark:text-white z-10">
                        <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-2xl">download</span>
                        </div>
                        <h3 class="font-bold text-lg">Langkah 1: Download Template</h3>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 z-10">
                        Download template leger sesuai periode aktif. Template sudah berisi nama siswa dan kolom mapel yang sesuai.
                    </p>
                    <a href="{{ route('unified.import.template', $kelas->id) }}" class="mt-auto btn-boss btn-primary flex items-center justify-center gap-2 shadow-lg shadow-primary/20 z-10">
                        <span class="material-symbols-outlined">download</span>
                        Download Template Leger (.csv)
                    </a>
                </div>

                <!-- Step 2: Upload -->
                <div class="card-boss !p-8 flex flex-col gap-4 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-4 text-slate-900 dark:text-white">
                        <div class="size-12 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300">
                            <span class="material-symbols-outlined text-2xl">upload_file</span>
                        </div>
                        <h3 class="font-bold text-lg">Langkah 2: Upload Leger</h3>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Upload file yang sudah diisi. Pastikan format <b>.CSV (Comma Delimited)</b>.
                    </p>

                    <form action="{{ route('unified.import.process', $kelas->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4 mt-auto">
                        @csrf
                        <input type="file" name="file" required class="block w-full text-sm text-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl
                            file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 dark:file:bg-slate-700 file:text-slate-600 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-600 transition-colors cursor-pointer">

                        <button type="submit" class="btn-boss btn-primary w-full flex items-center justify-center gap-2 shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined">cloud_upload</span>
                            Proses Import
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 text-amber-800 dark:text-amber-400 text-xs border border-amber-200 dark:border-amber-800/30">
                <strong class="flex items-center gap-1 mb-2 text-sm"><span class="material-symbols-outlined text-[18px]">info</span> Catatan Penting:</strong>
                <ul class="list-disc pl-5 space-y-1 opacity-90">
                    <li>Jangan mengubah <b>Header Kolom</b> (Baris 1 & 2) pada file template.</li>
                    <li>Sistem akan mencocokkan Mapel berdasarkan ID yang ada di header (contoh: <code>[12]</code>).</li>
                    <li>Untuk Absensi, isi dengan angka (jumlah hari).</li>
                    <li>Untuk Sikap, saat ini sistem hanya menerima input manual di aplikasi (Kolom sikap di excel akan diabaikan sementara menunggu update struktur database). *Fokus Mapel & Absensi dulu*.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
