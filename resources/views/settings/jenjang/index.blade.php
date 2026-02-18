@extends('layouts.app')

@section('title', 'Manajemen Jenjang & Rapor')

@section('content')

<div class="space-y-8" x-data="{ showAddModal: false }">

    <!-- Header Section -->
    <div class="relative bg-white dark:bg-surface-dark rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2">Manajemen Jenjang</h1>
                <p class="text-slate-500 dark:text-slate-400 max-w-xl text-lg">
                    Kelola tingkat pendidikan (MI, MTS, dll) dan konfigurasi raportnya di sini.
                </p>
            </div>
            <button @click="showAddModal = true"
                class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-6 py-3 rounded-xl font-bold hover:scale-105 active:scale-95 transition-all shadow-xl shadow-slate-900/20 flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span>
                Tambah Jenjang
            </button>
        </div>

        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
    </div>

    <!-- Stats / Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-primary/10 rounded-2xl p-6 text-primary shadow-sm border border-primary/20 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-primary font-medium mb-1">Total Jenjang</div>
                <div class="text-4xl font-black text-primary">{{ $jenjangs->count() }}</div>
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-[100px] text-primary/10 group-hover:scale-110 transition-transform">school</span>
        </div>

        <!-- Card 2 -->
        <div class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <div class="text-slate-500 text-sm font-bold">Rapor Aktif</div>
                <div class="text-2xl font-black text-slate-800 dark:text-white">{{ $jenjangs->where('has_rapor', true)->count() }} <span class="text-xs font-normal text-slate-400">Jenjang</span></div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center gap-4 cursor-pointer hover:border-primary/50 transition-colors" @click="showAddModal = true">
             <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-2xl">post_add</span>
            </div>
            <div>
                <div class="text-slate-500 text-sm font-bold">Butuh Jenjang Baru?</div>
                <div class="text-sm font-bold text-primary">Klik untuk tambah</div>
            </div>
        </div>
    </div>

    <!-- Main Content List -->
    <div class="grid grid-cols-1 gap-4">
        @foreach($jenjangs as $j)
        <div class="group bg-white dark:bg-surface-dark rounded-2xl p-1 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200 dark:border-slate-800 hover:border-primary/30 flex flex-col md:flex-row items-center gap-6 overflow-hidden">

            <!-- Left: Icon & Info -->
            <div class="p-6 flex-1 flex items-center gap-6 w-full">
                <div class="w-16 h-16 rounded-2xl {{ $j->has_rapor ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-3xl">{{ $j->has_rapor ? 'menu_book' : 'domain_disabled' }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-black bg-slate-900 text-white dark:bg-white dark:text-slate-900">{{ $j->kode }}</span>
                        @if($j->has_rapor)
                        <span class="flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Rapor Aktif
                        </span>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white block">{{ $j->nama }}</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        {{ $j->has_rapor ? 'Konfigurasi aktif untuk rapor & akademik.' : 'Identitas level siswa.' }}
                    </p>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="p-4 md:border-l border-slate-100 dark:border-slate-800 flex items-center gap-3 w-full md:w-auto bg-slate-50/50 dark:bg-slate-800/10 h-full justify-end">
                @if($j->has_rapor)
                <a href="{{ route('settings.jenjang.settings', $j->id) }}"
                   class="px-5 py-2.5 rounded-xl bg-white dark:bg-surface-dark border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm hover:border-primary hover:text-primary transition-all flex items-center gap-2 shadow-sm whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">settings</span>
                    Konfigurasi
                </a>
                @endif

                @if($j->kode !== 'TPQ' && !\App\Models\Kelas::where('id_jenjang', $j->id)->exists())
                <form action="{{ route('settings.jenjang.destroy', $j->id) }}" method="POST"
                      data-confirm-delete="true"
                      data-title="Hapus Jenjang?"
                      data-message="Aksi ini akan menghapus data jenjang namum TIDAK menghapus kelas/siswa terkait (hanya referensinya).">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Hapus Jenjang">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </form>
                @endif
            </div>

        </div>
        @endforeach
    </div>

    <!-- Modal Add -->
    <div x-show="showAddModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;"
         x-transition.opacity x-cloak>

        <div class="bg-white dark:bg-surface-dark rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative"
             @click.away="showAddModal = false"
             x-show="showAddModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100">

            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white">Jenjang Baru</h3>
                        <p class="text-slate-500 text-sm mt-1">Tambahkan tingkat pendidikan baru.</p>
                    </div>
                    <button @click="showAddModal = false" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('settings.jenjang.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kode</label>
                        <input type="text" name="kode" class="input-boss w-full uppercase font-bold tracking-widest" placeholder="SD/MI/SMP" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" class="input-boss w-full" placeholder="Sekolah Dasar" required>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800/50">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="has_rapor" value="1" checked
                                   class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <div>
                                <span class="font-bold text-indigo-900 dark:text-indigo-300 text-sm block">Aktifkan Fitur Rapor</span>
                                <span class="text-xs text-indigo-600/80 dark:text-indigo-400">Centang jika jenjang ini menerbitkan rapor.</span>
                            </div>
                        </label>
                    </div>

                    <div class="pt-2 flex gap-3">
                        <button type="button" @click="showAddModal = false" class="flex-1 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition-colors">Batal</button>
                        <button type="submit" class="flex-1 py-3 rounded-xl bg-slate-900 text-white font-bold hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-slate-900/20">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
