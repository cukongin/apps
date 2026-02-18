@extends('layouts.app')

@section('title', 'Simulasi Tagihan')

@section('content')
<div class="flex flex-col gap-8 w-full">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="material-symbols-outlined text-4xl text-primary">science</span>
                Simulasi Tagihan
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium text-lg">Generate tagihan SPP untuk periode mendatang tanpa menunggu tanggal berjalan.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('keuangan.dashboard') }}" class="btn-boss bg-white text-slate-600 border-slate-200 shadow-sm hover:bg-slate-50 px-6 py-3 rounded-xl flex items-center gap-2 font-bold transition-all">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="card-boss !p-8 max-w-2xl mx-auto w-full relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
             <span class="material-symbols-outlined text-[150px] text-primary">rocket_launch</span>
        </div>

        <div class="relative z-10">
            <div class="mb-8">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Pilih Periode Simulasi</h3>
                <p class="text-slate-500 text-sm">Sistem akan mengecek dan membuat tagihan SPP bulanan untuk semua siswa aktif pada periode yang dipilih.</p>
            </div>

            <form action="{{ route('keuangan.simulation.run') }}" method="POST" class="space-y-6" data-confirm-delete="true" data-title="Jalankan Simulasi?" data-message="Tagihan akan dibuat untuk semua siswa aktif. Pastikan periode benar!" data-confirm-text="Ya, Jalankan" data-icon="rocket_launch">
                @csrf

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Bulan</label>
                        <select name="month" class="input-boss w-full font-bold text-lg">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Tahun</label>
                        <select name="year" class="input-boss w-full font-bold text-lg">
                            @foreach(range(date('Y'), date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 text-xs rounded-xl flex gap-3 items-start">
                    <span class="material-symbols-outlined shrink-0 text-lg">warning</span>
                    <p class="leading-relaxed">
                        <b>Perhatian:</b> Fitur ini akan membuat tagihan <b>REAL</b> di database. <br>
                        Gunakan dengan bijak untuk melihat proyeksi pendapatan atau menyiapkan tagihan lebih awal.
                        <br>Tagihan yang sudah dibuat tidak akan diduplikasi jika dijalankan ulang.
                    </p>
                </div>

                <button type="submit" class="btn-boss btn-primary w-full justify-center !py-4 shadow-xl shadow-primary/30 hover:shadow-primary/50 hover:scale-[1.02] transition-all">
                    <span class="material-symbols-outlined">play_circle</span> Jalankan Simulasi
                </button>
            </form>
        </div>
        </div>
    </div>

    <!-- Reset Section -->
    <div class="card-boss !p-8 max-w-2xl mx-auto w-full border-red-200 dark:border-red-900/30 bg-red-50/50 dark:bg-red-900/10">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-red-700 dark:text-red-400 mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined">delete_forever</span>
                Reset Data Simulasi
            </h3>
            <p class="text-slate-500 text-sm">Menghapus semua tagihan <b>belum lunas</b> untuk periode tertentu. Data yang sudah dibayar tidak akan terhapus.</p>
        </div>

        <form action="{{ route('keuangan.simulation.reset') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end" data-confirm-delete="true" data-title="Hapus Data Simulasi?" data-message="Semua tagihan belum lunas pada periode ini akan dihapus permanen!" data-confirm-text="Ya, Hapus" data-icon="delete_forever">
            @csrf

            <div class="w-full md:w-auto flex-1 grid grid-cols-2 gap-4">
                 <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Bulan</label>
                    <select name="month" class="input-boss w-full font-bold text-sm bg-white">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase">Tahun</label>
                    <select name="year" class="input-boss w-full font-bold text-sm bg-white">
                        @foreach(range(date('Y'), date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-boss bg-red-100 text-red-700 hover:bg-red-200 border-red-200 w-full md:w-auto whitespace-nowrap">
                Hapus Tagihan
            </button>
        </form>
    </div>
</div>
@endsection
