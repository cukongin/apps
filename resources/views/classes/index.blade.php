@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="flex flex-col gap-8" x-data="{ openCreate: false, openPromote: false, openEdit: false, openDeleteAll: false }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="flex flex-col gap-1 max-w-2xl">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">meeting_room</span>
                Manajemen Kelas
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Kelola rombongan belajar, wali kelas, dan kenaikan tingkat.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <form action="{{ route('classes.reset') }}" method="POST"
                  data-confirm-delete="true"
                  data-title="RESET KELAS TOTAL?"
                  data-message="BAHAYA: Aksi ini akan MENGHAPUS SEMUA KELAS di tahun ajaran aktif ini. Data tidak dapat dikembalikan."
                  data-confirm-text="Ya, Reset Total!"
                  data-confirm-color="#ef4444"
                  data-icon="warning">
                @csrf
                <button type="submit" class="btn-boss bg-red-50 text-red-600 border-red-200 hover:bg-red-100 shadow-none">
                    <span class="material-symbols-outlined text-[20px]">restart_alt</span>
                    <span>Reset</span>
                </button>
            </form>
            <button @click="openPromote = true" class="btn-boss bg-amber-500 text-white hover:bg-amber-600 shadow-amber-500/20 shadow-lg border-transparent">
                <span class="material-symbols-outlined text-[20px]">upgrade</span>
                <span>Naik Kelas</span>
            </button>
            <button @click="openCreate = true" class="btn-boss btn-primary">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Kelas Baru</span>
            </button>
        </div>
    </div>

    <!-- Stats & Filters -->
    <div class="card-boss p-1">
        <div class="flex flex-col md:flex-row gap-4 p-4 items-center justify-between">
            <!-- Tabs / Levels -->
            <div class="flex bg-slate-100 dark:bg-slate-800/50 p-1 rounded-xl w-full md:w-auto overflow-x-auto">
                <a href="{{ route('classes.index', ['search' => request('search')]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all {{ !request('id_jenjang') ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <span class="material-symbols-outlined text-[16px]">apps</span>
                    Semua
                    <span class="bg-slate-200 text-slate-600 text-[10px] px-1.5 py-0.5 rounded ml-1">{{ $stats['total_classes'] ?? 0 }}</span>
                </a>
                @foreach($levels as $lvl)
                <a href="{{ route('classes.index', ['id_jenjang' => $lvl->id, 'search' => request('search')]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all {{ request('id_jenjang') == $lvl->id ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <span class="material-symbols-outlined text-[16px]">school</span>
                    {{ $lvl->nama ?? $lvl->kode }}
                    <span class="bg-slate-200 text-slate-600 text-[10px] px-1.5 py-0.5 rounded ml-1">
                        {{ $stats['jenjang_' . $lvl->id] ?? 0 }}
                    </span>
                </a>
                @endforeach
            </div>

            <!-- Search -->
            <form action="{{ route('classes.index') }}" method="GET" class="relative w-full md:w-64">
                @if(request('id_jenjang'))
                <input type="hidden" name="id_jenjang" value="{{ request('id_jenjang') }}">
                @endif
                <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">search</span>
                <input name="search" value="{{ request('search') }}" class="input-boss pl-10" placeholder="Cari Kelas / Wali..." type="text"/>
            </form>
        </div>
    </div>

    <!-- Grid List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-20">
        @forelse($classes as $class)
        <div class="group card-boss p-5 hover:border-primary/50 transition-all duration-300 flex flex-col justify-between h-[240px] relative overflow-hidden">
            <!-- Decorative gradient top -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700 group-hover:from-primary group-hover:via-blue-400 group-hover:to-primary transition-all"></div>

            <div class="flex justify-between items-start z-10">
                <div class="flex flex-col gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $class->jenjang->kode == 'MI' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }} w-fit">
                        {{ $class->jenjang->nama_jenjang }}
                    </span>
                    <a href="{{ route('classes.show', $class->id) }}" class="text-2xl font-black text-slate-900 dark:text-white hover:text-primary transition-colors mt-1 before:absolute before:inset-0">
                        {{ $class->nama_kelas }}
                    </a>
                </div>

                <!-- Actions Dropdown -->
                <div class="relative z-20" x-data="{ open: false }">
                    <button @click.prevent="open = !open" @click.away="open = false" class="size-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">more_vert</span>
                    </button>
                    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-surface-dark rounded-xl shadow-xl py-1 border border-slate-100 dark:border-slate-800 z-50 animate-in fade-in zoom-in-95 duration-200">
                        <a href="#"
                           data-id="{{ $class->id }}"
                           data-nama="{{ $class->nama_kelas }}"
                           data-jenjang="{{ $class->id_jenjang }}"
                           data-tingkat="{{ $class->tingkat_kelas }}"
                           data-wali="{{ $class->id_wali_kelas }}"
                           onclick="event.preventDefault(); openEditModalFromEl(this)"
                           class="block px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit Data
                        </a>
                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST"
                              data-confirm-delete="true"
                              data-title="Hapus Kelas?"
                              data-message="Data kelas dan anggota di dalamnya akan terhapus.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus Kelas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-4 pointer-events-none relative z-10">
                @if($class->wali_kelas)
                    @if($class->wali_kelas->data_guru && $class->wali_kelas->data_guru->foto)
                        <img src="{{ asset('public/' . $class->wali_kelas->data_guru->foto) }}" class="size-10 rounded-full object-cover ring-2 ring-white dark:ring-surface-dark shadow-sm">
                    @else
                        <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold ring-2 ring-white dark:ring-surface-dark shadow-sm text-sm">
                            {{ substr($class->wali_kelas->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Wali Kelas</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-1">{{ $class->wali_kelas->name }}</span>
                    </div>
                @else
                    <div class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 ring-2 ring-white dark:ring-surface-dark">
                        <span class="material-symbols-outlined text-[20px]">person_off</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Wali Kelas</span>
                        <span class="text-xs font-bold text-red-500 italic">Belum Ditentukan</span>
                    </div>
                @endif
            </div>

            <div class="mt-auto pt-4 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between pointer-events-none relative z-10">
                <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/50 px-2 py-1 rounded-lg">
                        <span class="material-symbols-outlined text-[16px]">group</span>
                        <span class="text-xs font-bold">{{ $class->anggota_kelas_count }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/50 px-2 py-1 rounded-lg">
                        <span class="material-symbols-outlined text-[16px]">menu_book</span>
                        <span class="text-xs font-bold">{{ $class->pengajar_mapel_count }}</span>
                    </div>
                </div>
            </div>

            <!-- Bg Pattern -->
            <div class="absolute -bottom-6 -right-6 opacity-5 pointer-events-none">
                <span class="material-symbols-outlined text-9xl">meeting_room</span>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center">
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-full size-20 mx-auto flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-4xl text-slate-300">meeting_room</span>
            </div>
            <h3 class="text-slate-900 dark:text-white font-bold text-lg">Belum ada kelas</h3>
            <p class="text-slate-500 text-sm mb-6">Silakan buat kelas baru untuk memulai.</p>
            <button @click="openCreate = true" class="btn-boss btn-primary">
                Buat Kelas Baru
            </button>
        </div>
        @endforelse

        <!-- Create New Card -->
        <button @click="openCreate = true" class="group bg-transparent border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl p-5 hover:border-primary hover:bg-slate-50 dark:hover:bg-white/5 transition-all duration-300 flex flex-col items-center justify-center gap-4 h-[240px]">
            <div class="size-14 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all text-slate-400 shadow-sm">
                <span class="material-symbols-outlined text-[28px]">add</span>
            </div>
            <span class="text-slate-500 dark:text-slate-400 group-hover:text-primary font-bold text-sm">Buat Kelas Lain</span>
        </button>
    </div>

    <!-- Create Class Modal -->
    <div x-show="openCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
         x-transition.opacity x-cloak>
         <div @click.outside="openCreate = false" class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl w-full max-w-md p-6 border border-slate-100 dark:border-slate-800 transform transition-all"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-95">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">add_circle</span>
                    Tambah Kelas Baru
                </h3>
                <button @click="openCreate = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('classes.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id_tahun_ajaran" value="{{ $academicYears->first()->id ?? 1 }}">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Kelas</label>
                    <input type="text" name="nama_kelas" required placeholder="Contoh: 1-A" class="input-boss">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenjang (Level)</label>
                    <select name="id_jenjang" required class="input-boss">
                        @foreach($levels as $lvl)
                        <option value="{{ $lvl->id }}">{{ $lvl->nama_jenjang }} ({{ $lvl->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tingkat / Jilid (Angka)</label>
                    <input type="number" name="tingkat_kelas" required min="1" max="12" class="input-boss">
                    <p class="text-[10px] text-slate-400 mt-1 italic">Untuk TPQ: Isi sesuai Jilid (1-6) atau Level.</p>
                </div>
                <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Wali Kelas</label>
                    <select name="id_wali_kelas" class="input-boss">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($teachers as $t)
                        @php $isTaken = in_array($t->id, $takenTeachers ?? []); @endphp
                        <option value="{{ $t->id }}" class="{{ $isTaken ? 'bg-slate-100 text-slate-400' : '' }}" {{ $isTaken ? 'disabled' : '' }}>
                            {{ $t->name }} {{ $isTaken ? '(Sudah Ada)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="openCreate = false" class="flex-1 btn-boss btn-secondary justify-center">Batal</button>
                    <button type="submit" class="flex-1 btn-boss btn-primary justify-center">Simpan</button>
                </div>
            </form>
         </div>
    </div>

    <!-- Edit Class Modal (Simplified Trigger via JS) -->
    <div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
        <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl w-full max-w-md p-6 border border-slate-100 dark:border-slate-800 transform transition-all">
             <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">edit_square</span>
                    Edit Data Kelas
                </h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Kelas</label>
                    <input type="text" id="edit_nama_kelas" name="nama_kelas" required class="input-boss">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenjang</label>
                    <select id="edit_id_jenjang" name="id_jenjang" required class="input-boss">
                        @foreach($levels as $lvl)
                        <option value="{{ $lvl->id }}">{{ $lvl->nama_jenjang }} ({{ $lvl->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tingkat / Jilid (Angka)</label>
                    <input type="number" id="edit_tingkat_kelas" name="tingkat_kelas" required class="input-boss">
                    <p class="text-[10px] text-slate-400 mt-1 italic">Untuk TPQ: Isi sesuai Jilid (1-6) atau Level.</p>
                </div>
                <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Wali Kelas</label>
                    <select id="edit_id_wali_kelas" name="id_wali_kelas" class="input-boss">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($teachers as $t)
                        @php $isTaken = in_array($t->id, $takenTeachers ?? []); @endphp
                        <option value="{{ $t->id }}" data-taken="{{ $isTaken ? 'true' : 'false' }}">
                             {{ $t->name }} {{ $isTaken ? '(Sudah Ada)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeEditModal()" class="flex-1 btn-boss btn-secondary justify-center">Batal</button>
                    <button type="submit" class="flex-1 btn-boss btn-primary justify-center">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Promote Modal -->
    <div x-show="openPromote" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
         x-transition.opacity x-cloak>
         <div @click.outside="openPromote = false" class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl w-full max-w-md p-6 border border-slate-100 dark:border-slate-800 transform transition-all"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="size-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined">upgrade</span>
                    </div>
                    Proses Kenaikan Kelas
                </h3>
                <button @click="openPromote = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('classes.bulk-promote') }}" method="POST" class="space-y-4">
                @csrf
                <div class="text-xs text-slate-500 bg-amber-50 border border-amber-100 p-4 rounded-xl font-medium leading-relaxed">
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Siswa <b>NAIK KELAS</b> akan dipindahkan ke tingkat selanjutnya (Misal: 1A -> 2A).</li>
                        <li>Kelas di Tahun Aktif akan otomatis dibuat jika belum ada.</li>
                        <li>Kelas Akhir yang Lulus akan ditandai.</li>
                    </ul>
                </div>

                <div class="flex items-start gap-3 bg-red-50 p-4 rounded-xl border border-red-100">
                    <input id="reset_first" name="reset_first" type="checkbox" value="1" checked class="mt-1 h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-600">
                    <div class="text-xs leading-relaxed">
                        <label for="reset_first" class="font-bold text-red-700 block mb-1">Hapus Data Tahun Ini Dulu (Wajib)</label>
                        <p class="text-red-600/80">Menghapus semua kelas di tahun aktif sebelum memindahkan siswa agar tidak duplikat.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Pilih Jenjang (Wajib)</label>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach($levels as $lvl)
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="id_jenjang" value="{{ $lvl->id }}" class="peer sr-only" required>
                            <div class="p-4 rounded-xl border-2 border-slate-200 bg-white hover:border-emerald-400 transition-all peer-checked:border-emerald-600 peer-checked:bg-emerald-50 shadow-sm group-hover:shadow-md">
                                <!-- Forced Color Black for Visibility -->
                                <div class="font-bold text-center text-lg uppercase tracking-wider" style="color: #000000 !important;">
                                    {{ $lvl->kode }}
                                </div>

                                @if($lvl->kode === 'TPQ')
                                <div class="text-[11px] text-center mt-1 font-medium" style="color: #DC2626 !important;">(Tanpa Syarat Nilai)</div>
                                @else
                                <div class="text-[11px] text-center mt-1 font-medium" style="color: #64748B !important;">(Berdasarkan Hasil Rapor)</div>
                                @endif
                            </div>
                            <!-- Checkmark Icon -->
                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <p class="text-[10px] text-slate-400 mt-2 italic">*Pilih jenjang untuk memproses kenaikan kelas.</p>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="openPromote = false" class="flex-1 btn-boss btn-secondary justify-center">Batal</button>
                    <button type="submit" class="flex-1 btn-boss bg-amber-500 text-white hover:bg-amber-600 justify-center shadow-amber-500/20 shadow-lg border-transparent">Proses Sekarang</button>
                </div>
            </form>
         </div>
    </div>

</div>

@push('scripts')
<script>
function openEditModalFromEl(el) {
    let id = el.getAttribute('data-id');
    let nama = el.getAttribute('data-nama');
    let jenjang = el.getAttribute('data-jenjang');
    let tingkat = el.getAttribute('data-tingkat');
    let wali = el.getAttribute('data-wali');

    document.getElementById('edit_nama_kelas').value = nama;
    document.getElementById('edit_id_jenjang').value = jenjang;
    document.getElementById('edit_tingkat_kelas').value = tingkat;
    document.getElementById('edit_id_wali_kelas').value = wali || "";

    let url = "{{ route('classes.update', ':id') }}";
    document.getElementById('editForm').action = url.replace(':id', id);

    updateTakenTeachersState(wali);
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex'); // Ensure flex for centering
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

function updateTakenTeachersState(currentWaliId) {
    let select = document.getElementById('edit_id_wali_kelas');
    for (let i = 0; i < select.options.length; i++) {
        let opt = select.options[i];
        let isTaken = opt.getAttribute('data-taken') === 'true';
        let isCurrent = opt.value == currentWaliId;

        if (isTaken && !isCurrent) {
            opt.disabled = true;
            opt.classList.add('text-slate-300', 'bg-slate-50');
        } else {
            opt.disabled = false;
            opt.classList.remove('text-slate-300', 'bg-slate-50');
        }
    }
}
</script>
@endpush
@endsection
