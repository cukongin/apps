@extends('layouts.app')

@section('title', 'Manajemen Data Siswa')

@section('content')
<div class="max-w-[1200px] mx-auto flex flex-col gap-8">

    <!-- Heading -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="flex flex-col gap-1 max-w-2xl">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">groups</span>
                Manajemen Siswa
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Kelola data siswa, status akademik, dan riwayat pendidikan.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <button type="submit" form="bulkDeleteForm" id="bulkDeleteBtn" class="hidden btn-boss bg-red-600 hover:bg-red-700 text-white shadow-red-600/20 animate-pulse">
                <span class="material-symbols-outlined text-[20px]">delete</span>
                <span>Hapus</span>
            </button>
            <button onclick="openModal('importModal')" class="btn-boss btn-secondary">
                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                <span>Impor</span>
            </button>
            <button onclick="openModal('addStudentModal')" class="btn-boss btn-primary">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Siswa Baru</span>
            </button>
        </div>
    </div>

    <div class="flex flex-col gap-6">
        <!-- Tabs & Filters Card -->
        <div class="card-boss p-1 ">
            <div class="flex flex-col md:flex-row gap-4 p-4 items-center justify-between">
                <!-- Tabs -->
                <div class="flex bg-slate-100 dark:bg-slate-800/50 p-1 rounded-xl w-full md:w-auto overflow-x-auto">
                    <a href="{{ route('master.students.index', ['tab' => 'active', 'level_id' => request('level_id')]) }}"
                       class="px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all {{ request('tab', 'active') == 'active' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        Aktif
                        <span class="bg-slate-200 text-slate-600 text-[10px] px-1.5 py-0.5 rounded ml-1">{{ $stats['all_active'] }}</span>
                    </a>
                    <a href="{{ route('master.students.index', ['tab' => 'new', 'level_id' => request('level_id')]) }}"
                       class="px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all {{ request('tab') == 'new' ? 'bg-white text-amber-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        <span class="material-symbols-outlined text-[16px]">fiber_new</span>
                        Baru
                        <span class="bg-slate-200 text-slate-600 text-[10px] px-1.5 py-0.5 rounded ml-1">{{ $stats['new'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('master.students.index', ['tab' => 'inactive', 'level_id' => request('level_id')]) }}"
                       class="px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all {{ request('tab') == 'inactive' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        <span class="material-symbols-outlined text-[16px]">do_not_disturb_on</span>
                        Arsip
                        <span class="bg-slate-200 text-slate-600 text-[10px] px-1.5 py-0.5 rounded ml-1">{{ $stats['inactive'] }}</span>
                    </a>
                </div>

                <!-- Filters -->
                <form action="{{ route('master.students.index') }}" method="GET" class="flex flex-1 md:justify-end gap-3 w-full md:w-auto">
                    <input type="hidden" name="tab" value="{{ request('tab', 'active') }}">

                    <select name="level_id" onchange="this.form.submit()" class="input-boss w-auto">
                        <option value="all">Semua Jenjang</option>
                        @foreach($levels as $lvl)
                        <option value="{{ $lvl->id }}" {{ request('level_id') == $lvl->id ? 'selected' : '' }}>{{ $lvl->nama }}</option>
                        @endforeach
                    </select>

                    <div class="relative w-full md:w-64">
                         <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">search</span>
                        <input name="search" value="{{ request('search') }}" class="input-boss pl-10" placeholder="Cari Siswa..." type="text"/>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <form id="bulkDeleteForm" action="{{ route('master.students.bulk_destroy') }}" method="POST" class="card-boss overflow-hidden flex flex-col"
              data-confirm-delete="true"
              data-title="Hapus Siswa Terpilih?"
              data-message="Data siswa yang dihapus tidak dapat dikembalikan."
              data-confirm-text="Ya, Hapus Massal"
              data-confirm-color="#ef4444">
            @csrf
            @method('DELETE')
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="table-head">
                        <tr>
                            <th class="py-4 px-6 w-10 text-center">
                                <input class="rounded border-slate-300 text-primary focus:ring-primary cursor-pointer" type="checkbox" onchange="toggleAll(this)"/>
                            </th>
                            <th class="py-4 px-6">Identitas Siswa</th>
                            <th class="py-4 px-6">Lembaga</th>
                            <th class="py-4 px-6">Kelas</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($students as $student)
                        <tr class="table-row group">
                            <td class="table-cell w-10 text-center">
                                <input class="rounded border-slate-300 text-primary focus:ring-primary cursor-pointer student-checkbox" type="checkbox" name="ids[]" value="{{ $student->id }}"/>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-4">
                                    <div class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 font-bold overflow-hidden border border-slate-200 dark:border-slate-700">
                                        @if($student->foto)
                                            <img src="{{ asset($student->foto) }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($student->nama, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="{{ route('master.students.show', $student->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-primary transition-colors text-sm">
                                            {{ $student->nama }}
                                        </a>
                                        <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
                                            <span>{{ $student->nis }}</span>
                                            <span class="text-slate-300">•</span>
                                            <span>{{ $student->nisn ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ optional($student->jenjang)->kode == 'MI' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }} w-fit">
                                        {{ optional($student->jenjang)->nama ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="table-cell">
                                @if($student->kelas)
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $student->kelas->nama_kelas }}</span>
                                @else
                                    <span class="text-xs italic text-slate-400">- Belum Masuk Kelas -</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $student->status == 'AKTIF' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="table-cell text-right">
                                <div class="flex items-center justify-end gap-1 opacity-100">
                                    <a href="{{ route('master.students.show', $student->id) }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-primary transition-colors" title="Lihat Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a href="{{ route('master.students.edit', $student->id) }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-amber-600 transition-colors" title="Edit Data">
                                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                    </a>
                                    @if(request('tab') == 'inactive' && $student->status_siswa != 'lulus')
                                    <button type="button" onclick="restoreStudent({{ $student->id }})" class="p-2 rounded-lg hover:bg-green-50 text-slate-500 hover:text-green-600 transition-colors" title="Restore">
                                        <span class="material-symbols-outlined text-[18px]">restore_from_trash</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                 <div class="flex flex-col items-center gap-3">
                                    <div class="p-4 bg-slate-50 rounded-full">
                                        <span class="material-symbols-outlined text-4xl text-slate-300">person_search</span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <h3 class="text-slate-900 font-bold">Tidak ada data siswa</h3>
                                        <p class="text-slate-500 text-sm">Coba ubah filter atau kata kunci pencarian Anda.</p>
                                    </div>
                                 </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 gap-4">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wide">
                    Menampilkan <span class="text-slate-900 dark:text-white">{{ $students->firstItem() ?? 0 }}</span> - <span class="text-slate-900 dark:text-white">{{ $students->lastItem() ?? 0 }}</span> dari <span class="text-slate-900 dark:text-white">{{ $students->total() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    {{ $students->links('pagination::simple-tailwind') }}
                </div>
            </div>
        </form>
    </div>
    </form>
</div>

<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    toggleDeleteBtn();
}

function toggleRow() {
    toggleDeleteBtn();
}

function toggleDeleteBtn() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const deleteBtn = document.getElementById('bulkDeleteBtn');
    if (checkboxes.length > 0) {
        deleteBtn.classList.remove('hidden');
        deleteBtn.classList.add('flex');
    } else {
        deleteBtn.classList.add('hidden');
        deleteBtn.classList.remove('flex');
    }
}

function openStatusModal(id, nama) {
    document.getElementById('statusStudentName').innerText = nama;

    let url = "{{ route('master.students.updateStatus', ':id') }}";
    url = url.replace(':id', id);
    document.getElementById('statusForm').action = url;

    openModal('statusModal');
}
function closeStatusModal() {
    closeModal('statusModal');
}

function restoreStudent(id) {
    Swal.fire({
        title: 'Restore Siswa?',
        text: "Siswa akan dikembalikan menjadi AKTIF dan masuk kembali ke kelas terakhirnya.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Restore!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            let url = "{{ route('master.students.restore', ':id') }}";
            url = url.replace(':id', id);
            let form = document.getElementById('restoreForm');
            form.action = url;
            form.submit();
        }
    });
}
</script>

<!-- Status Change Modal -->
<x-modal name="statusModal" maxWidth="md">
    <form id="statusForm" method="POST"
            data-confirm-delete="true"
            data-title="Ubah Status Siswa?"
            data-message="Status siswa akan diperbarui. Siswa non-aktif tidak akan muncul di presensi."
            data-confirm-text="Ya, Simpan Status"
            data-confirm-color="#d97706"
            data-icon="question">
        @csrf
        <!-- Route defined in JS -->

        <div class="bg-white dark:bg-surface-dark px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-amber-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600">input</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Ubah Status Siswa</h3>
                    <p class="text-xs text-slate-500" id="statusStudentName">Nama Siswa</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-white mb-1">Pilih Status Baru</label>
                    <select name="status" required class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-amber-500 sm:text-sm sm:leading-6">
                        <option value="">-- Pilih Status --</option>
                        <option value="active">Aktif (Batalkan Keluar)</option>
                        <option value="mutasi">Mutasi (Pindah Sekolah)</option>
                        <option value="keluar">Keluar (Drop Out / Berhenti)</option>
                        <option value="lulus">Lulus (Alumni)</option>
                        <option value="meninggal">Meninggal Dunia</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-white mb-1">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="2" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-amber-500 sm:text-sm sm:leading-6" placeholder="Alasan pindah/berhenti..."></textarea>
                </div>

                <div class="text-xs text-amber-600 bg-amber-50 p-2 rounded border border-amber-100">
                    <b>Perhatian:</b> Siswa yang berstatus Mutasi/Keluar/Lulus tidak akan muncul di daftar Absensi, Penilaian, atau Kenaikan Kelas.
                </div>
            </div>
        </div>
        <div class="bg-slate-50 dark:bg-black/20 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-amber-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 sm:ml-3 sm:w-auto">Simpan Status</button>
            <button type="button" onclick="closeModal('statusModal')" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
        </div>
    </form>
</x-modal>

<!-- Modal Tambah Siswa (Buku Induk Style) -->
<x-modal name="addStudentModal" maxWidth="4xl">
    <form action="{{ route('master.students.store') }}" method="POST">
        @csrf
        <div class="bg-white dark:bg-surface-dark px-6 py-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">Tambah Siswa Baru (Buku Induk)</h3>
        </div>

        <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Section: Identitas -->
                <div class="space-y-4">
                    <h4 class="font-bold text-primary text-sm uppercase tracking-wider border-b border-slate-100 pb-2">Identitas Diri</h4>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">NIS Lokal</label>
                            <input type="text" name="nis_lokal" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                            <input type="text" name="nisn" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">NIK</label>
                            <input type="text" name="nik" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2 bg-white">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2"></textarea>
                    </div>
                </div>

                <!-- Section: Data Tambahan -->
                <div class="space-y-4">
                    <h4 class="font-bold text-primary text-sm uppercase tracking-wider border-b border-slate-100 pb-2">Orang Tua & Akademik</h4>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">No. Telepon Ortu</label>
                        <input type="text" name="no_telp_ortu" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                    </div>

                    <hr class="border-slate-200">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenjang</label>
                            <select name="id_jenjang" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2 bg-white">
                                @foreach($levels as $lvl)
                                <option value="{{ $lvl->id }}">{{ $lvl->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tahun Masuk</label>
                            <input type="number" name="tahun_masuk" value="{{ date('Y') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 text-sm focus:ring-primary focus:border-primary px-3 py-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 dark:bg-black/20 px-6 py-4 sm:flex sm:flex-row-reverse">
            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-green-600 sm:ml-3 sm:w-auto">Simpan Data</button>
            <button type="button" onclick="closeModal('addStudentModal')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
        </div>
    </form>
</x-modal>

<!-- Import Modal -->
<x-modal name="importModal" maxWidth="lg">
    <div class="bg-white dark:bg-surface-dark px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <span class="material-symbols-outlined text-green-600">upload_file</span>
            </div>
            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white" id="modal-title">Impor Data Siswa</h3>
                <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    <p class="mb-4">Upload file CSV/Excel untuk menambahkan siswa secara massal. Gunakan template yang disediakan agar format sesuai.</p>

                    <a href="{{ route('master.students.template') }}" class="inline-flex items-center gap-2 text-primary hover:text-green-700 font-medium mb-4 p-2 bg-green-50 rounded-lg w-full border border-green-100 justify-center">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Download Template CSV
                    </a>

                    <form action="{{ route('master.students.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="mt-2">
                            <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-white mb-2">Pilih File CSV</label>
                            <input type="file" name="file" accept=".csv, .txt" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-green-500"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-slate-50 dark:bg-black/20 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
        <button type="submit" form="importForm" class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark sm:ml-3 sm:w-auto">Upload & Proses</button>
        <button type="button" onclick="closeModal('importModal')" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
    </div>
</x-modal>

<!-- Hidden Restore Form -->
<form id="restoreForm" method="POST" class="hidden">
    @csrf
</form>


@endsection

