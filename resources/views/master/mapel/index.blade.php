@extends('layouts.app')

@section('title', 'Master Data Mapel')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-8" x-data="{
    // Modal States
    createModalOpen: false,
    deleteAllModalOpen: false,
    importModalOpen: false,

    // Form Data
    isEdit: false,
    editId: null,
    formData: {
        nama_mapel: '',
        nama_kitab: '',
        kode_mapel: '',
        kategori: 'UMUM',
        target_jenjang: 'SEMUA'
    },

    // Actions
    openCreateModal() {
        this.isEdit = false;
        this.editId = null;
        this.formData = {
            nama_mapel: '',
            nama_kitab: '',
            kode_mapel: '',
            kategori: 'UMUM',
            target_jenjang: 'SEMUA'
        };
        this.createModalOpen = true;
    },

    openEditModal(mapel) {
        this.isEdit = true;
        this.editId = mapel.id;
        this.formData = {
            nama_mapel: mapel.nama_mapel,
            nama_kitab: mapel.nama_kitab || '',
            kode_mapel: mapel.kode_mapel,
            kategori: mapel.kategori,
            target_jenjang: mapel.target_jenjang
        };
        this.createModalOpen = true;
    },

    closeCreateModal() {
        this.createModalOpen = false;
    },

    // Utilities
    getFormAction() {
        if (this.isEdit) {
            return `{{ url('master/mapel') }}/${this.editId}`;
        }
        return `{{ route('master.mapel.store') }}`;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="flex flex-col gap-1 max-w-2xl">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">menu_book</span>
                Master Mata Pelajaran
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Kelola kurikulum, daftar pelajaran, dan kategori muatan lokal.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('master.mapel.plotting') }}" class="btn-boss btn-secondary">
                <span class="material-symbols-outlined text-[20px]">dataset_linked</span>
                <span>Plotting Massal</span>
            </a>
            <button @click="importModalOpen = true" class="btn-boss btn-secondary">
                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                <span>Import</span>
            </button>
            <button @click="deleteAllModalOpen = true" class="btn-boss bg-red-50 text-red-600 border-red-200 hover:bg-red-100 shadow-none">
                <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                <span>Reset</span>
            </button>
            <button @click="openCreateModal()" class="btn-boss btn-primary">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Mapel Baru</span>
            </button>
        </div>
    </div>

    <!-- Delete All Modal -->
    <div x-show="deleteAllModalOpen" class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             x-show="deleteAllModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="deleteAllModalOpen = false"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="deleteAllModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark text-left shadow-xl transition-all sm:w-full sm:max-w-lg border border-slate-100 dark:border-slate-800">
                    <form action="{{ route('master.mapel.destroy-all') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-6 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-6">
                                <span class="material-symbols-outlined text-3xl text-red-600">warning</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Hapus SEMUA Data Mapel?</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                                Tindakan ini akan menghapus <strong>SELURUH</strong> data Mata Pelajaran, serta data terkait seperti Nilai Siswa, KKM, dan Pengajar Mapel.
                                <br><span class="font-bold text-red-600 mt-2 block">Data yang dihapus TIDAK DAPAT dikembalikan!</span>
                            </p>
                            <div class="flex gap-3 justify-center">
                                <button type="button" class="btn-boss btn-secondary w-full justify-center" @click="deleteAllModalOpen = false">
                                    Batal
                                </button>
                                <button type="submit" class="btn-boss bg-red-600 text-white hover:bg-red-700 w-full justify-center shadow-red-600/20">
                                    Ya, Hapus Semua
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="importModalOpen" class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             x-show="importModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="importModalOpen = false"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="importModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark text-left shadow-xl transition-all sm:w-full sm:max-w-lg border border-slate-100 dark:border-slate-800">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                             <span class="material-symbols-outlined text-primary">upload_file</span>
                             Import Data Mapel
                        </h3>
                        <form action="{{ route('master.mapel.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase">File CSV/Excel</label>
                                <input type="file" name="file" accept=".csv, .txt" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all border border-slate-200 dark:border-slate-700 rounded-xl">
                                <p class="text-xs text-slate-400">Gunakan template import untuk hasil terbaik.</p>
                            </div>
                            <div class="pt-4 flex gap-3">
                                <a href="{{ route('master.mapel.template') }}" class="btn-boss btn-secondary flex-1 justify-center">
                                    <span class="material-symbols-outlined text-[18px]">download</span> Template
                                </a>
                                <button type="submit" class="btn-boss btn-primary flex-1 justify-center">
                                    Import Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card-boss overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-head">
                    <tr>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Nama Mapel</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Jenjang Target</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($mapels as $mapel)
                    <tr class="table-row">
                        <td class="table-cell font-mono font-bold text-slate-500">{{ $mapel->kode_mapel ?? '-' }}</td>
                        <td class="table-cell">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $mapel->nama_mapel }}</span>
                                @if($mapel->nama_kitab)
                                    <span class="text-xs font-arabic text-slate-500 dark:text-slate-400 mt-0.5">{{ $mapel->nama_kitab }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="table-cell">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border
                                {{ $mapel->kategori == 'AGAMA' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' :
                                   ($mapel->kategori == 'MULOK' ? 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800' : 'bg-primary/5 text-primary border-primary/10 dark:bg-primary/20 dark:border-primary/30') }}">
                                {{ $mapel->kategori }}
                            </span>
                        </td>
                        <td class="table-cell">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border
                                {{ $mapel->target_jenjang == 'MI' ? 'bg-sky-50 text-sky-600 border-sky-100 dark:bg-sky-900/30 dark:text-sky-400 dark:border-sky-800' :
                                   ($mapel->target_jenjang == 'MTS' ? 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800' : 'bg-slate-50 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700') }}">
                                {{ $mapel->target_jenjang }}
                            </span>
                        </td>
                        <td class="table-cell text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button @click='openEditModal(@json($mapel))' class="p-2 rounded-lg hover:bg-amber-50 text-slate-400 hover:text-amber-600 transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                </button>
                                <form action="{{ route('master.mapel.destroy', $mapel->id) }}" method="POST"
                                      data-confirm-delete="true"
                                      data-title="Hapus Mapel?"
                                      data-message="Mapel ini akan dihapus permanen.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-600 transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="p-4 bg-slate-50 rounded-full">
                                    <span class="material-symbols-outlined text-4xl text-slate-300">menu_book</span>
                                </div>
                                <p class="font-medium text-slate-900">Belum ada mata pelajaran</p>
                                <button @click="openCreateModal()" class="text-primary hover:underline text-sm">Tambah mapel baru sekarang</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="createModalOpen" class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             x-show="createModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeCreateModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="createModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-slate-100 dark:border-slate-800">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white/50 dark:bg-surface-dark/50 backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="isEdit ? 'Edit Mapel' : 'Tambah Mapel'"></h3>
                        <button @click="closeCreateModal()" class="size-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>

                    <form :action="getFormAction()" method="POST" class="p-6">
                        @csrf
                        <!-- Handle Method Spoofing for Edit -->
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Nama Mata Pelajaran</label>
                                <input type="text" name="nama_mapel" x-model="formData.nama_mapel" required class="input-boss" placeholder="Contoh: Matematika">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Nama Arab / Kitab (Opsional)</label>
                                <input type="text" name="nama_kitab" x-model="formData.nama_kitab" placeholder="Contoh: Ø§Ù„Ù„ØºØ© Ø§Ù„Ø¹Ø±Ø¨ÙŠØ©" class="input-boss font-arabic text-right">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Kode Mapel</label>
                                    <input type="text" name="kode_mapel" x-model="formData.kode_mapel" placeholder="Contoh: MTK-01" class="input-boss">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Kategori</label>
                                    <input type="text" name="kategori" x-model="formData.kategori" list="kategori_list" required placeholder="Pilih/Ketik..." class="input-boss">
                                    <datalist id="kategori_list">
                                        <option value="UMUM">
                                        <option value="AGAMA">
                                        <option value="MULOK">
                                        <option value="KELOMPOK A">
                                        <option value="KELOMPOK B">
                                        <option value="PEMINATAN">
                                    </datalist>
                                </div>
                            </div>
                             <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Target Jenjang</label>
                                <select name="target_jenjang" x-model="formData.target_jenjang" required class="input-boss">
                                    <option value="SEMUA">SEMUA (MI & MTs)</option>
                                    <option value="MI">Khusus MI</option>
                                    <option value="MTS">Khusus MTs</option>
                                </select>
                                <p class="mt-1 text-[10px] text-slate-400 ml-1">Pilih 'SEMUA' jika mapel diajarkan di kedua jenjang.</p>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" @click="closeCreateModal()" class="btn-boss btn-secondary">Batal</button>
                            <button type="submit" class="btn-boss btn-primary">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
