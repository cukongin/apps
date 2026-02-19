@extends('layouts.app')

@section('title', 'Pilih Kelas - DKN Ijazah')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-hidden">
    <!-- Header -->
    <div class="bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 sticky top-0 z-20">
        <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 md:py-6 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white">Kelola Nilai Ijazah (DKN)</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs md:text-sm mt-1">Pilih kelas tingkat akhir untuk mengelola nilai ujian dan ijazah.</p>
            </div>
            <a href="{{ route('tu.dashboard') }}" class="btn-boss bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 flex items-center justify-center gap-2 w-full md:w-auto">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth custom-scrollbar">
        <div class="max-w-6xl mx-auto">

            <div class="flex items-center gap-3 mb-6">
                 <div class="bg-primary/10 text-primary p-2 rounded-lg">
                    <span class="material-symbols-outlined">school</span>
                 </div>
                 <h2 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Kelas Akhir (MI 6 / MTs 9)</h2>
            </div>

            @if($finalClasses->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
                    <div class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-full mb-4">
                        <span class="material-symbols-outlined text-4xl text-orange-400">warning</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tidak Ada Kelas Akhir Ditemukan</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-center max-w-md mt-2">
                        System tidak menemukan kelas akhir (6, 9, 12, atau 3) pada Tahun Ajaran aktif ini.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($finalClasses as $kelas)
                        <div class="card-boss relative overflow-hidden group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <!-- Background Decoration -->
                            <div class="absolute -top-4 -right-4 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500 rotate-12">
                                <span class="material-symbols-outlined text-9xl text-primary">school</span>
                            </div>

                            <div class="relative z-10 flex flex-col h-full p-6">
                                <span class="inline-block px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-bold uppercase tracking-wider w-fit mb-3 border border-primary/20">
                                    {{ $kelas->jenjang->nama_jenjang ?? $kelas->tingkat_kelas }}
                                </span>

                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                                    {{ $kelas->nama_kelas }}
                                </h3>

                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">person</span>
                                    {{ $kelas->wali_kelas ? $kelas->wali_kelas->name : 'Tanpa Wali Kelas' }}
                                </p>

                                <div class="mt-auto flex flex-col gap-3">
                                    {{-- Input Nilai link removed per user request --}}

                                    <a href="{{ route('tu.dkn.archive', $kelas->id) }}" class="btn-boss bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 w-full flex items-center justify-center gap-2 shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">inventory_2</span> Lihat Arsip Lengkap
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
