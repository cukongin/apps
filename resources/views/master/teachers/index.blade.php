@extends('layouts.app')

@section('title', 'Manajemen Data Guru')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="flex flex-col gap-1 max-w-2xl">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">school</span>
                Manajemen Guru
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Kelola pendidik, staf pengajar, dan penugasan akademik.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <button @click="openModal('createTeacherModal')" class="btn-boss btn-primary">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Guru Baru</span>
            </button>
            <button @click="openModal('importModal')" class="btn-boss btn-secondary">
                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                <span>Impor</span>
            </button>
            <form action="{{ route('master.teachers.destroy-all') }}" method="POST"
                  data-confirm-delete="true"
                  data-title="Hapus SEMUA Guru?"
                  data-message="AWAS: Semua data guru dan akun loginnya akan DIHAPUS PERMANEN. Tindakan ini tidak bisa dibatalkan.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-boss bg-red-100 hover:bg-red-200 text-red-700 border-red-200 shadow-none">
                    <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                    <span>Reset</span>
                </button>
            </form>

            <!-- IMPORT MODAL -->
            <x-modal name="importModal" maxWidth="md">
                 <div class="bg-white dark:bg-surface-dark px-6 py-6 border border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">upload_file</span>
                        Import Data Guru
                    </h3>

                    <form action="{{ route('master.teachers.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                             <label class="block text-xs font-bold text-slate-500 uppercase">File CSV/Excel</label>
                             <input type="file" name="file" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all border border-slate-200 dark:border-slate-700 rounded-xl">
                             <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg text-[10px] text-amber-800 dark:text-amber-200 space-y-1 border border-amber-100 dark:border-amber-800">
                                <p class="font-bold">Format Wajib (11 Kolom):</p>
                                <p>1.NIK*, 2.Nama, 3.Gender, 4.Tempat Lhr, 5.Tgl Lhr, 6.Alamat, 7.NPWP*, 8.Pendidikan, 9.Pesantren, 10.Mapel, 11.Email</p>
                             </div>
                        </div>
                        <div class="pt-2 flex gap-3">
                             <a href="{{ route('master.teachers.template') }}" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-center text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">Download Template</a>
                             <button type="submit" class="flex-1 py-2.5 bg-primary text-white font-bold rounded-xl text-sm hover:bg-primary-dark shadow-lg shadow-primary/20 transition-all">Upload</button>
                        </div>
                    </form>
                 </div>
            </x-modal>

            <!-- CREATE MODAL -->
            <x-modal name="createTeacherModal" maxWidth="2xl">
                 <div class="flex flex-col max-h-[90vh]">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white/50 dark:bg-surface-dark/50 backdrop-blur-sm z-10">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">person_add</span>
                                Tambah Guru Baru
                            </h3>
                        </div>
                        <button @click="closeModal('createTeacherModal')" class="size-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="p-6 overflow-y-auto custom-scrollbar">
                        <form action="{{ route('master.teachers.store') }}" method="POST" id="createTeacherForm" class="space-y-6" enctype="multipart/form-data">
                            @csrf

                            <!-- Section: Akun Login -->
                            <div class="bg-slate-50 dark:bg-slate-800/30 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="p-1.5 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">lock</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">Akun Login</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Nama Lengkap & Gelar</label>
                                        <input type="text" name="name" required placeholder="Contoh: Ahmad Dahlan, S.Pd.I" class="input-boss">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Email (Username)</label>
                                        <input type="email" name="email" required placeholder="email@sekolah.sch.id" class="input-boss">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Password Default</label>
                                        <input type="text" value="123456" disabled class="input-boss bg-slate-100 text-slate-500 cursor-not-allowed border-transparent">
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Data Pribadi -->
                            <div>
                                <div class="flex items-center gap-2 mb-4 mt-2">
                                    <div class="p-1.5 bg-primary/10 text-primary rounded-lg">
                                        <span class="material-symbols-outlined text-[18px]">person</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">Identitas & Riwayat</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Photo Upload -->
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Foto Profil</label>
                                        <input type="file" name="foto" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all border border-slate-200 dark:border-slate-700 rounded-xl">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">NIK</label>
                                        <input type="text" name="nik" placeholder="Ketik NIK..." class="input-boss">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">NPWP</label>
                                        <input type="text" name="npwp" class="input-boss">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" required class="input-boss">
                                            <option value="">Pilih...</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1 md:col-span-2 space-y-3" x-data="{ education: [] }">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-xs font-bold text-slate-500 uppercase">Riwayat Pendidikan</label>
                                            <button type="button" @click="education.push({id: Date.now(), jenjang: '', nama: '', masuk: '', lulus: ''})" class="text-xs flex items-center gap-1 text-primary font-bold hover:bg-primary/5 px-2 py-1 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-[16px]">add_circle</span> Tambah
                                            </button>
                                        </div>

                                        <!-- List Pendidikan -->
                                        <template x-for="(item, index) in education" :key="item.id">
                                            <div class="grid grid-cols-12 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 items-end">
                                                <div class="col-span-3">
                                                    <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Jenjang</label>
                                                    <select :name="'pendidikan['+index+'][jenjang]'" class="input-boss text-xs py-1.5">
                                                        <option value="SD">SD/MI</option>
                                                        <option value="SMP">SMP/Mts</option>
                                                        <option value="SMA">SMA/SMK</option>
                                                        <option value="S1">S1</option>
                                                        <option value="S2">S2</option>
                                                        <option value="Pesantren">Pesantren</option>
                                                    </select>
                                                </div>
                                                <div class="col-span-4">
                                                    <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Instansi</label>
                                                    <input type="text" :name="'pendidikan['+index+'][nama_instansi]'" class="input-boss text-xs py-1.5">
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Masuk</label>
                                                    <input type="number" :name="'pendidikan['+index+'][tahun_masuk]'" class="input-boss text-xs py-1.5">
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Lulus</label>
                                                    <input type="number" :name="'pendidikan['+index+'][tahun_lulus]'" class="input-boss text-xs py-1.5">
                                                </div>
                                                <div class="col-span-1 text-center">
                                                     <button type="button" @click="education = education.filter(i => i.id !== item.id)" class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <div x-show="education.length === 0" class="text-center py-6 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 text-xs hover:border-slate-300 transition-colors cursor-pointer" @click="education.push({id: Date.now(), jenjang: '', nama: '', masuk: '', lulus: ''})">
                                            Tap disini untuk tambah pendidikan
                                        </div>
                                    </div>

                                     <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Mapel Ajar</label>
                                        <input type="text" name="mapel_ajar_text" placeholder="Contoh: Matematika, Fiqih" class="input-boss">
                                    </div>

                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Alamat Lengkap</label>
                                        <textarea name="alamat" rows="2" class="input-boss"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 rounded-b-2xl">
                         <button type="button" @click="closeModal('createTeacherModal')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                             Batal
                         </button>
                         <button type="submit" form="createTeacherForm" class="px-5 py-2.5 bg-primary text-white font-bold rounded-xl text-sm hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                             <span class="material-symbols-outlined text-[18px]">check_circle</span>
                             Simpan Data
                         </button>
                    </div>
                </div>
            </x-modal>
        </div>
    </div>

    <!-- Error Alert -->
    @if(session('import_errors'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative flex flex-col gap-2" role="alert">
        <strong class="font-bold flex items-center gap-2">
            <span class="material-symbols-outlined">error</span>
            Import Selesai dengan Catatan:
        </strong>
        <ul class="list-disc list-inside text-sm pl-6">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table -->
    <div class="card-boss overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                    <thead class="table-head">
                        <tr>
                            <th scope="col" class="px-6 py-4">Nama Guru</th>
                            <th scope="col" class="px-6 py-4">NIK / Identitas</th>
                            <th scope="col" class="px-6 py-4">Mapel Ajar</th>
                            <th scope="col" class="px-6 py-4">No. HP</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($teachers as $teacher)
                        <tr class="table-row">
                            <td class="table-cell font-medium text-slate-900 dark:text-white whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 font-bold overflow-hidden border border-slate-200 dark:border-slate-700">
                                        @if($teacher->data_guru && $teacher->data_guru->foto)
                                            <img src="{{ asset($teacher->data_guru->foto) }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($teacher->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('master.teachers.show', $teacher->id) }}" class="font-bold hover:text-primary transition-colors text-sm">{{ $teacher->name }}</a>
                                        <div class="text-xs text-slate-500 font-mono">{{ $teacher->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="table-cell">
                                @if($teacher->data_guru)
                                    <div class="space-y-1">
                                        @if($teacher->data_guru->nik)
                                            <div class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 text-[10px] inline-block font-mono tracking-wider">
                                                {{ $teacher->data_guru->nik }}
                                            </div>
                                        @else
                                            <div class="text-xs italic text-slate-400 opacity-50">- No NIK -</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-xs">Data Profil Kosong</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($teacher->data_guru && $teacher->data_guru->mapel_ajar_text)
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 block truncate max-w-[150px]" title="{{ $teacher->data_guru->mapel_ajar_text }}">{{ $teacher->data_guru->mapel_ajar_text }}</span>
                                    @if($teacher->data_guru->pendidikan_terakhir)
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $teacher->data_guru->pendidikan_terakhir }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-sm text-slate-600 dark:text-slate-400">
                                {{ $teacher->data_guru->no_hp ?? '-' }}
                            </td>
                            <td class="table-cell">
                                <span class="px-2.5 py-1 font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-full dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400 text-[10px]">
                                    AKTIF
                                </span>
                            </td>
                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('master.teachers.show', $teacher->id) }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-primary transition-colors" title="Detail">
                                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                    </a>
                                    <form action="{{ route('master.teachers.destroy', $teacher->id) }}" method="POST"
                                          data-confirm-delete="true"
                                          data-title="Hapus Guru Ini?"
                                          data-message="Data profil dan akun login guru ini akan dihapus.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-red-500 transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="p-4 bg-slate-50 rounded-full">
                                        <span class="material-symbols-outlined text-4xl text-slate-300">school</span>
                                    </div>
                                    <p class="font-medium text-slate-900">Belum ada data guru</p>
                                    <button @click="openModal('createTeacherModal')" class="text-primary hover:underline text-sm">Tambah guru baru sekarang</button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>
        <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
            {{ $teachers->links('pagination::simple-tailwind') }}
        </div>
    </div>
</div>

<!-- Credential Modal -->
@if(session('generated_credential'))
<x-modal name="credentialModal" maxWidth="sm" :show="true">
    <div class="bg-white dark:bg-surface-dark px-6 py-6 border border-slate-200 dark:border-slate-800">
        <div class="text-center mb-6">
            <div class="bg-green-100 text-green-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Akun Berhasil Digenerate!</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Silakan catat atau bagikan kredensial berikut kepada guru yang bersangkutan.</p>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-lg border border-slate-100 dark:border-slate-800 space-y-3 mb-6">
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Nama Guru</label>
                <div class="font-medium text-slate-900 dark:text-white">{{ session('generated_credential')['name'] }}</div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Email Login</label>
                <div class="font-mono text-lg font-bold text-primary">{{ session('generated_credential')['email'] }}</div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Password Baru</label>
                <div class="font-mono text-lg font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-800 px-3 py-1 rounded border border-slate-200 dark:border-slate-700">
                    {{ session('generated_credential')['password'] }}
                </div>
            </div>
        </div>

        <button onclick="closeModal('credentialModal')" class="w-full py-2.5 bg-slate-900 dark:bg-slate-700 text-white font-bold rounded-lg hover:bg-slate-800 transition-all">
            Tutup
        </button>
    </div>
</x-modal>
@endif
@endsection

