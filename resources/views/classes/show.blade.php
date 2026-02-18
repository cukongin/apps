@extends('layouts.app')

@section('title', 'Detail Kelas ' . $class->nama_kelas)

@section('content')
<div class="flex flex-col gap-6" x-data="studentManager({{ $class->id }})">
    <!-- MOVE MODAL (Relocated to ensure Scope) -->
    <div x-show="isMoveModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="closeMoveModal"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-surface-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-800">
                <form action="{{ route('classes.move-students', $class->id) }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-surface-dark px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-500">transfer_within_a_station</span>
                            Pindahkan Santri / Naik Jilid Manual
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-[400px]">
                            <!-- Left: Select Students -->
                            <div class="flex flex-col h-full border rounded-lg border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="bg-slate-50 dark:bg-slate-800 p-2 text-xs font-bold text-slate-500 uppercase text-center border-b border-slate-200 dark:border-slate-700">
                                    Pilih Santri (Dari Kelas Ini)
                                </div>
                                <div class="overflow-y-auto p-2 space-y-1 custom-scrollbar bg-white dark:bg-surface-dark flex-1">
                                    <template x-for="student in enrolled" :key="student.id">
                                        <label class="flex items-center gap-3 p-2 rounded hover:bg-slate-50 cursor-pointer border border-transparent hover:border-slate-100 transition-colors">
                                            <input type="checkbox" name="student_ids[]" :value="student.id" @change="toggleMoveSelection(student.id)" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="student.nama_lengkap"></span>
                                                <span class="text-[10px] text-slate-400 font-mono" x-text="student.nis"></span>
                                            </div>
                                        </label>
                                    </template>
                                    <div x-show="enrolled.length === 0" class="text-center p-4 text-slate-400 text-xs italic">
                                        Kelas kosong.
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Target Class -->
                            <div class="flex flex-col gap-4">
                                <div class="p-4 rounded-lg bg-amber-50 border border-amber-100">
                                    <label class="block text-xs font-bold text-amber-800 uppercase mb-2">Pindah Ke Kelas mana?</label>
                                    <select name="target_class_id" required class="block w-full rounded-md border-0 py-2 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6">
                                        <option value="">-- Pilih Kelas Tujuan --</option>
                                        @foreach($allClasses as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama_kelas }} ({{ $c->jumlah_anggota ?? 0 }} siswa)</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-amber-700 mt-2 leading-relaxed">
                                        *Santri yang dipilih akan <b>langsung dipindahkan</b> ke kelas tujuan saat ini juga.
                                        <br>*Status siswa tetap 'Aktif'.
                                    </p>
                                </div>

                                <div class="mt-auto">
                                    <div class="text-center mb-2">
                                        <span class="text-3xl font-black text-amber-600" x-text="selectedToMove.length">0</span>
                                        <span class="text-xs text-slate-500 font-bold uppercase block">Santri Dipilih</span>
                                    </div>
                                    <button type="submit" :disabled="selectedToMove.length === 0" class="w-full btn-boss bg-amber-600 text-white hover:bg-amber-700 shadow-lg shadow-amber-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                        PROSES PINDAH SEKARANG
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-black/20 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" @click="closeMoveModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('classes.index') }}" class="hover:text-primary transition-colors font-medium">Manajemen Kelas</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-slate-900 dark:text-white font-medium">{{ $class->nama_kelas }}</span>
    </div>

    <!-- Header Card -->
    <div class="card-boss p-6 relative overflow-hidden">
        <!-- Decorative bg -->
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
             <span class="material-symbols-outlined text-9xl">meeting_room</span>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-3">
                    <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">{{ $class->nama_kelas }}</h2>
                    <span class="px-3 py-1 rounded-lg bg-primary/10 text-primary text-xs font-black uppercase tracking-widest border border-primary/20">{{ $class->jenjang->nama_jenjang }}</span>
                    <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-black uppercase tracking-widest border border-slate-200 dark:border-slate-600">
                        {{ $class->tahun_ajaran->nama }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm font-medium">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    Wali Kelas: <span class="text-slate-900 dark:text-white font-bold">{{ $class->wali_kelas->name ?? 'Belum ditentukan' }}</span>
                </div>
            </div>

            <!-- Readiness Widget -->
            <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700">
                <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">menu_book</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-black text-slate-900 dark:text-white">{{ $class->pengajar_mapel->count() }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mata Pelajaran</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Dual Pane Manager -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Enrolled Students -->
        <div class="card-boss p-0 flex flex-col h-[600px]"
             @dragover.prevent
             @drop="dropHandler($event)"
             :class="{'ring-2 ring-primary ring-offset-2': false}"> <!-- Dynamic Class if needed later -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-surface-dark rounded-t-2xl">
                <div>
                     <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 text-lg">
                        <span class="material-symbols-outlined text-primary">groups</span>
                        Santri Kelas Ini
                    </h3>
                    <p class="text-xs text-slate-500">Daftar siswa yang aktif di kelas ini.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-sm font-black text-slate-900 dark:text-white" x-text="enrolled.length">0</span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center gap-2">
                <div class="relative flex-1">
                     <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                     <input type="text" x-model="searchEnrolled" placeholder="Cari santri..." class="input-boss pl-10 bg-white dark:bg-surface-dark w-full">
                </div>

                <!-- Manual Move Button (Only show if TPQ) -->
                @if(optional($class->jenjang)->kode == 'TPQ')
                <button type="button" @click="openMoveModal()" class="btn-boss bg-amber-100 text-amber-700 hover:bg-amber-200 border-amber-200 text-xs px-3" title="Pindah Kelas / Naik Jilid Manual">
                    <span class="material-symbols-outlined text-[18px]">transfer_within_a_station</span>
                    <span class="hidden sm:inline">Pindah / Naik</span>
                </button>
                @endif
            </div>

            <!-- Drop Hint Overlay (Optional, can impl later) -->

            <div class="overflow-y-auto flex-1 p-4 space-y-3 custom-scrollbar">
                <template x-for="student in filteredEnrolled" :key="student.id">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-surface-dark shadow-sm border border-slate-100 dark:border-slate-800 hover:border-primary/50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 text-primary flex items-center justify-center font-black text-sm" x-text="student.initial"></div>
                            <div>
                                <p class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-primary transition-colors" x-text="student.nama_lengkap"></p>
                                <p class="text-xs text-slate-500 font-mono" x-text="student.nis"></p>
                            </div>
                        </div>
                        <button @click="removeStudent(student.id)" class="size-8 rounded-full hover:bg-red-50 text-slate-300 hover:text-red-500 transition-all flex items-center justify-center" title="Keluarkan">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </div>
                </template>
                <div x-show="filteredEnrolled.length === 0" class="flex flex-col items-center justify-center h-full text-slate-400 text-sm italic opacity-50">
                    <span class="material-symbols-outlined text-4xl mb-2">group_off</span>
                    <span>Tidak ada santri di kelas ini.</span>
                </div>
            </div>
        </div>

        <!-- Right: Available Students -->
        <div class="card-boss p-0 flex flex-col h-[600px] border-dashed border-2 bg-slate-50/50 dark:bg-slate-800/20">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-surface-dark rounded-t-2xl">
                 <div>
                     <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 text-lg">
                        <span class="material-symbols-outlined text-slate-400">person_add</span>
                        Gudang Santri
                    </h3>
                    <p class="text-xs text-slate-500">Santri yang belum memiliki kelas.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button x-show="selectedCandidates.length > 0" @click="addSelected()" class="btn-boss btn-primary px-3 py-1.5 text-xs animate-pulse">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                        Tambah (<span x-text="selectedCandidates.length"></span>)
                    </button>
                    <button @click="loadCandidates()" class="btn-boss btn-secondary px-3 py-1.5 text-xs">
                        <span class="material-symbols-outlined text-[16px]">refresh</span>
                    </button>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" x-model="searchCandidate" @input.debounce.300ms="loadCandidates()" placeholder="Cari santri..." class="input-boss pl-10 bg-white dark:bg-surface-dark">
                </div>
                <!-- Select All Checkbox -->
                <div class="flex items-center gap-2" x-show="candidates.length > 0">
                    <input type="checkbox" @change="toggleSelectAll()" :checked="isAllSelected" class="rounded border-slate-300 text-primary focus:ring-primary size-5 cursor-pointer" title="Pilih Semua">
                </div>
            </div>

            <div class="overflow-y-auto flex-1 p-4 space-y-3 relative custom-scrollbar">
                <!-- Loading State -->
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-surface-dark/50 z-20 backdrop-blur-sm">
                    <div class="flex flex-col items-center">
                        <span class="material-symbols-outlined animate-spin text-primary text-4xl mb-2">progress_activity</span>
                        <span class="text-xs font-bold text-slate-500 animate-pulse">Memuat Data...</span>
                    </div>
                </div>

                <template x-for="student in candidates" :key="student.id">
                    <div draggable="true"
                         @dragstart="dragStart($event, student.id)"
                         @dragend="dragEnd($event)"
                         class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-surface-dark border transition-all group cursor-move select-none"
                         :class="selectedCandidates.includes(student.id) ? 'border-primary ring-1 ring-primary bg-primary/5' : 'border-slate-200 dark:border-slate-700 hover:border-primary hover:shadow-md'">

                        <!-- Checkbox -->
                        <div class="mr-3" @click.stop>
                            <input type="checkbox" :value="student.id" x-model="selectedCandidates" class="rounded border-slate-300 text-primary focus:ring-primary size-4 cursor-pointer">
                        </div>

                        <div class="size-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 group-hover:bg-primary group-hover:text-white flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-[20px]">drag_indicator</span>
                        </div>

                        <div class="flex items-center gap-4 text-right justify-end flex-1 ml-3" @click="toggleSelection(student.id)">
                            <div class="flex flex-col items-end">
                                <p class="font-bold text-sm text-slate-800 dark:text-gray-200" x-text="student.nama_lengkap"></p>
                                <p class="text-xs text-slate-500 font-mono" x-text="student.nis_lokal"></p>
                            </div>
                             <div class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center text-xs font-bold">
                                <span x-text="student.nama_lengkap.charAt(0)"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="candidates.length === 0 && !loading" class="flex flex-col items-center justify-center h-full text-slate-400 text-sm italic text-center p-6 opacity-60">
                    <span class="material-symbols-outlined text-4xl mb-2">person_off</span>
                    <span>Tidak ada data santri tersedia.<br>Pastikan jenjang sesuai.</span>
                </div>
            </div>
        </div>
    </div>


    <!-- Subject Assignments -->
    <div class="card-boss p-0 min-h-[500px]">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                 <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="size-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">library_books</span>
                    </div>
                    Konfigurasi Mapel
                </h3>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- NEW: Trigger Pull Modal -->
                <button onclick="openPullModal()" class="btn-boss bg-slate-700 text-white hover:bg-slate-800 shadow-slate-700/20">
                    <span class="material-symbols-outlined text-[20px]">cloud_download</span>
                    <span class="hidden sm:inline">Tarik Siswa</span>
                </button>

                <form action="{{ route('classes.auto-assign-subjects', $class->id) }}" method="POST"
                      data-confirm-delete="true"
                      data-title="Buat Paket Mapel?"
                      data-message="Sistem akan otomatis menambahkan mapel sesuai jenjang. Mapel yang sudah ada tidak diduplikasi."
                      data-confirm-text="Ya, Buatkan!"
                      data-confirm-color="#10b981"
                      data-icon="info">
                    @csrf
                    <button type="submit" class="btn-boss btn-secondary">
                        <span class="material-symbols-outlined text-[20px]">auto_fix_high</span>
                        <span class="hidden sm:inline">Auto Generate</span>
                    </button>
                </form>

                <button onclick="openAssignModal()" class="btn-boss btn-primary">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>Assign Mapel</span>
                </button>

                <form action="{{ route('classes.reset-subjects', $class->id) }}" method="POST"
                      data-confirm-delete="true"
                      data-title="Hapus SEMUA Mapel?"
                      data-message="Semua mapel dan guru pengampu di kelas ini akan DIHAPUS.">
                    @csrf
                    <button type="submit" class="btn-boss bg-red-50 text-red-600 border-red-200 hover:bg-red-100 shadow-none px-3" title="Hapus Semua Mapel">
                        <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="table-container border-0 rounded-none rounded-b-2xl shadow-none">
            <table class="w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-6 py-4">Mata Pelajaran</th>
                        <th class="px-6 py-4">Guru Pengampu</th>
                        <th class="px-6 py-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($class->pengajar_mapel as $pm)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold text-xs ring-1 ring-slate-200 dark:ring-slate-700">
                                    {{ $pm->mapel->kode_mapel }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $pm->mapel->nama_mapel }}</p>
                                    <p class="text-xs text-slate-500 font-mono">{{ $pm->mapel->kategori ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">
                            <div x-data="{
                                loading: false,
                                currentGuru: '{{ $pm->id_guru }}',
                                updateGuru(e) {
                                    this.loading = true;
                                    let newId = e.target.value;
                                    fetch('{{ route('classes.update-subject-teacher', $class->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ id_mapel: {{ $pm->id_mapel }}, id_guru: newId })
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        this.loading = false;
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'Guru pengampu diperbarui'
                                        });
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        this.loading = false;
                                        Toast.fire({
                                            icon: 'error',
                                            title: 'Gagal update guru'
                                        });
                                    });
                                }
                            }">
                                <div class="relative max-w-xs">
                                    <select @change="updateGuru" x-model="currentGuru" class="input-boss py-2 pl-3 pr-8 text-xs">
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                    <div x-show="loading" class="absolute right-8 top-1/2 -translate-y-1/2 pointer-events-none">
                                        <span class="material-symbols-outlined animate-spin text-primary text-sm">autorenew</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell text-right">
                            <button class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Hapus Mapel Ini">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-3xl mb-2 opacity-30">library_books</span>
                                <span>Belum ada mata pelajaran yang ditambahkan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Modal (Mapel) -->
<div id="assignModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl w-full max-w-md p-6 border border-slate-100 dark:border-slate-800 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">post_add</span>
                Assign Mata Pelajaran
            </h3>
            <button onclick="closeAssignModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('classes.assign-subject', $class->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mata Pelajaran</label>
                <div class="select-wrapper">
                    <select name="id_mapel" required class="input-boss">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $subj)
                        <option value="{{ $subj->id }}">[{{ $subj->kode_mapel }}] {{ $subj->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                 <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Guru Pengampu</label>
                <select name="id_guru" class="input-boss">
                    <option value="">-- Pilih Guru (Opsional) --</option>
                    @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeAssignModal()" class="flex-1 btn-boss btn-secondary justify-center">Batal</button>
                <button type="submit" class="flex-1 btn-boss btn-primary justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Pull Data Modal -->
<div id="pullModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl w-full max-w-md p-6 border border-slate-100 dark:border-slate-800 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">cloud_download</span>
                Tarik Data Santri
            </h3>
            <button onclick="closePullModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="pullLoading" class="text-center py-10">
            <span class="material-symbols-outlined animate-spin text-primary text-4xl">autorenew</span>
            <p class="text-sm font-bold text-slate-600 mt-2">Mencari kelas sumber...</p>
        </div>

        <div id="pullContent" class="hidden space-y-5">
            <div class="bg-primary/5 text-primary text-xs p-4 rounded-xl font-medium border border-primary/10">
                <p class="leading-relaxed">Fitur ini menarik santri dari Tahun Ajaran sebelumnya (<span id="prevYearName" class="font-black"></span>) yang berstatus <b>NAIK KELAS</b> ke tingkat ini.</p>
            </div>

            <div>
                 <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Kelas Sumber</label>
                <select id="sourceClassSelect" class="input-boss">
                    <!-- Options populated via JS -->
                </select>
                <p class="text-[10px] text-slate-400 mt-1.5 font-medium">*Hanya kelas Tingkat <span id="targetGradeDisplay" class="text-slate-900 font-bold"></span> yang muncul.</p>
            </div>
        </div>

        <div id="pullError" class="hidden bg-red-50 text-red-600 text-xs p-4 rounded-xl border border-red-100 font-bold text-center"></div>

        <div class="pt-6 flex gap-3">
            <button type="button" onclick="closePullModal()" class="flex-1 btn-boss btn-secondary justify-center">Batal</button>
             <button type="button" onclick="executePull()" id="btnPullAction" disabled class="flex-1 btn-boss btn-primary justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                Tarik Data
            </button>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #475569;
    }
</style>
@endpush

@push('scripts')
<script>
    function studentManager(classId) {
        return {
            classId: classId,
            enrolled: @json($enrolledStudents),
            candidates: [],
            searchEnrolled: '',
            searchCandidate: '',
            loading: false,
            selectedCandidates: [],
            selectedToMove: [], // For Move Modal
            isMoveModalOpen: false,

            init() {
                this.loadCandidates();
            },

            get filteredEnrolled() {
                if (this.searchEnrolled === '') return this.enrolled;
                return this.enrolled.filter(s => s.nama_lengkap.toLowerCase().includes(this.searchEnrolled.toLowerCase()));
            },

            // ... Existing Candidate Logic ...

            async loadCandidates() {
                this.loading = true;
                try {
                    let url = "{{ route('classes.candidates', ':id') }}";
                    url = url.replace(':id', this.classId);
                    if (this.searchCandidate) {
                        url += `?search=${this.searchCandidate}`;
                    }
                    const response = await fetch(url);
                    this.candidates = await response.json();
                } catch (error) {
                    console.error('Error loading candidates:', error);
                } finally {
                    this.loading = false;
                }
            },
             dragStart(event, studentId) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', JSON.stringify({id: studentId}));
                event.target.classList.add('opacity-50');
            },

            dragEnd(event) {
                 event.target.classList.remove('opacity-50');
            },

            dropHandler(event) {
                const data = event.dataTransfer.getData('text/plain');
                if(data) {
                    const student = JSON.parse(data);
                    this.addStudent([student.id]);
                }
            },

            toggleSelectAll() {
                if (this.isAllSelected) {
                    this.selectedCandidates = [];
                } else {
                    this.selectedCandidates = this.candidates.map(c => c.id);
                }
            },

            get isAllSelected() {
                return this.candidates.length > 0 && this.selectedCandidates.length === this.candidates.length;
            },

            async addSelected() {
                if (this.selectedCandidates.length === 0) return;
                await this.addStudent(this.selectedCandidates);
                this.selectedCandidates = []; // Reset
            },

            async addStudent(studentIds) {
                try {
                     const response = await fetch("{{ route('classes.add-student', ':id') }}".replace(':id', this.classId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ student_ids: studentIds })
                    });

                    if (response.ok) {
                        window.location.reload(); // Reload to refresh list
                    } else {
                        alert('Gagal menambahkan siswa.');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan server.');
                }
            },

            async removeStudent(studentId) {
                const result = await Swal.fire({
                    title: 'Keluarkan Santri?',
                    text: 'Santri ini akan dikeluarkan dari kelas, namun data santri tetap ada.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Keluarkan!',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;

                try {
                    let url = "{{ route('classes.remove-student', ['class' => ':classId', 'studentId' => ':studentId']) }}";
                    url = url.replace(':classId', this.classId).replace(':studentId', studentId);

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        this.enrolled = this.enrolled.filter(s => s.id !== studentId);
                        Toast.fire({
                            icon: 'success',
                            title: 'Santri berhasil dikeluarkan'
                        });
                    } else {
                        Swal.fire('Gagal!', 'Gagal mengeluarkan siswa.', 'error');
                    }
                } catch(e) {
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                }
            },

            // --- MOVE MODAL LOGIC ---
            openMoveModal() {
                console.log('Open Move Modal Clicked');
                this.selectedToMove = []; // Reset choice
                this.isMoveModalOpen = true;
            },
            closeMoveModal() {
                this.isMoveModalOpen = false;
            },
            toggleMoveSelection(id) {
                if (this.selectedToMove.includes(id)) {
                    this.selectedToMove = this.selectedToMove.filter(sid => sid !== id);
                } else {
                    this.selectedToMove.push(id);
                }
            }
        }
    }
</script>


@endpush

@push('scripts')
<script>
    // Global Access
    let currentClassId = {{ $class->id }};

    // Define Toast Mixin
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });



    function openAssignModal() {
        document.getElementById('assignModal').classList.remove('hidden');
        document.getElementById('assignModal').classList.add('flex');
    }
    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
        document.getElementById('assignModal').classList.remove('flex');
    }

    // PULL DATA LOGIC
    function openPullModal() {
        document.getElementById('pullModal').classList.remove('hidden');
        document.getElementById('pullModal').classList.add('flex');
        document.getElementById('pullLoading').classList.remove('hidden');
        document.getElementById('pullContent').classList.add('hidden');
        document.getElementById('pullError').classList.add('hidden');
        document.getElementById('btnPullAction').disabled = true;

        fetch(`{{ url('classes') }}/${currentClassId}/sources`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('pullLoading').classList.add('hidden');
                if (data.error) {
                    showPullError(data.error);
                    return;
                }
                document.getElementById('pullContent').classList.remove('hidden');
                document.getElementById('prevYearName').innerText = data.year;
                document.getElementById('targetGradeDisplay').innerText = data.target_grade;

                const select = document.getElementById('sourceClassSelect');
                select.innerHTML = '<option value="">-- Pilih Kelas Sumber --</option>';
                if (data.sources.length === 0) {
                    select.innerHTML += '<option disabled>Tidak ada kelas yang cocok.</option>';
                } else {
                    data.sources.forEach(cls => {
                        select.innerHTML += `<option value="${cls.id}">${cls.name} (${cls.count} Siswa)</option>`;
                    });
                }

                select.onchange = (e) => {
                    document.getElementById('btnPullAction').disabled = !e.target.value;
                };
            })
            .catch(err => {
                document.getElementById('pullLoading').classList.add('hidden');
                showPullError("Gagal mengambil data kelas sumber.");
                console.error(err);
            });
    }

    function closePullModal() {
        document.getElementById('pullModal').classList.add('hidden');
        document.getElementById('pullModal').classList.remove('flex');
    }

    function showPullError(msg) {
        const el = document.getElementById('pullError');
        el.innerText = msg;
        el.classList.remove('hidden');
    }

    function executePull() {
        const sourceId = document.getElementById('sourceClassSelect').value;
        if (!sourceId) return;

        const btn = document.getElementById('btnPullAction');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">autorenew</span> Memproses...';

        fetch(`{{ url('classes') }}/${currentClassId}/pull`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ source_class_id: sourceId })
        })
        .then(res => res.json())
        .then(data => {
             if (data.message) {
                 Swal.fire({
                     icon: 'success',
                     title: 'Berhasil!',
                     text: data.message,
                     confirmButtonColor: '#10b981'
                 }).then(() => {
                     window.location.reload();
                 });
             }
        })
        .catch(err => {
            console.error(err);
             Swal.fire({
                 icon: 'error',
                 title: 'Oops...',
                 text: 'Terjadi kesalahan saat menarik data.',
             });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            closePullModal();
        });
    }
</script>
@endpush
