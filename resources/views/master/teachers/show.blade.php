@extends('layouts.app')

@section('title', 'Detail Guru - ' . $teacher->name)

@section('content')
<div class="flex flex-col gap-6" x-data="{ activeTab: 'profil' }">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('master.teachers.index') }}" class="hover:text-primary transition-colors font-medium">Data Guru</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-slate-900 dark:text-white font-medium">Detail Profile</span>
    </div>

    <!-- Header Card -->
    <div class="card-boss p-6 flex flex-col md:flex-row items-center gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
            <span class="material-symbols-outlined text-9xl">school</span>
        </div>

        <div class="size-24 rounded-2xl bg-gradient-to-br from-primary to-emerald-600 flex items-center justify-center text-white text-4xl font-bold overflow-hidden shadow-lg shadow-primary/30 ring-4 ring-white dark:ring-surface-dark z-10">
            @if($teacher->data_guru->foto)
                <img src="{{ asset('public/' . $teacher->data_guru->foto) }}" class="w-full h-full object-cover">
            @else
                {{ substr($teacher->name, 0, 1) }}
            @endif
        </div>
        <div class="flex-1 text-center md:text-left z-10">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $teacher->name }}</h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium mb-3">{{ $teacher->email }}</p>
            <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                @if($teacher->kelas_wali)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">star</span>
                    Wali Kelas {{ $teacher->kelas_wali->nama_kelas }}
                </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                    NIP: {{ $teacher->data_guru->nip ?? '-' }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    NUPTK: {{ $teacher->data_guru->nuptk ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
        <template x-for="tab in ['profil', 'beban', 'riwayat', 'keamanan']">
            <button @click="activeTab = tab"
                :class="activeTab === tab
                    ? 'bg-primary text-white shadow-lg shadow-primary/20 ring-1 ring-primary'
                    : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-700'"
                class="px-5 py-2.5 rounded-full font-bold text-sm transition-all whitespace-nowrap capitalize flex items-center gap-2">
                <span x-show="tab === 'profil'" class="material-symbols-outlined text-[18px]">person</span>
                <span x-show="tab === 'beban'" class="material-symbols-outlined text-[18px]">school</span>
                <span x-show="tab === 'riwayat'" class="material-symbols-outlined text-[18px]">history</span>
                <span x-show="tab === 'keamanan'" class="material-symbols-outlined text-[18px]">lock</span>
                <span x-text="tab === 'beban' ? 'Beban Mengajar' : (tab === 'riwayat' ? 'Riwayat Ajar' : tab)"></span>
            </button>
        </template>
    </div>

    <!-- Tab Contents -->

    <!-- 1. Profil -->
    <div x-data="{ isEditing: false }" x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="card-boss p-6 lg:p-8">
        <div class="flex justify-between items-center mb-8 border-b border-slate-100 dark:border-slate-800 pb-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-lg flex items-center gap-2">
                <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[20px]">id_card</span>
                </div>
                Informasi Pribadi
            </h3>
            <button x-show="!isEditing" @click="isEditing = true" class="btn-boss btn-primary py-1.5 px-3">
                <span class="material-symbols-outlined text-[18px]">edit</span> Edit
            </button>
        </div>

        <!-- View Mode -->
        <dl x-show="!isEditing" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-8 text-sm">
            @foreach([
                'Nama Lengkap' => $teacher->name,
                'Email' => $teacher->email,
                'NIP' => $teacher->data_guru->nip ?? '-',
                'NUPTK' => $teacher->data_guru->nuptk ?? '-',
                'Jenis Kelamin' => ($teacher->data_guru->jenis_kelamin ?? '') == 'L' ? 'Laki-laki' : 'Perempuan',
                'No HP' => $teacher->data_guru->no_hp ?? '-',
                'Tempat Tanggal Lahir' => ($teacher->data_guru->tempat_lahir ?? '') . ', ' . ($teacher->data_guru->tanggal_lahir ? \Carbon\Carbon::parse($teacher->data_guru->tanggal_lahir)->translatedFormat('d F Y') : '-'),
            ] as $label => $value)
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                <dt class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-1">{{ $label }}</dt>
                <dd class="font-bold text-slate-800 dark:text-white">{{ $value }}</dd>
            </div>
            @endforeach
            <div class="md:col-span-2 lg:col-span-3 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                <dt class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-1">Alamat Lengkap</dt>
                <dd class="font-medium text-slate-700 dark:text-slate-300">{{ $teacher->data_guru->alamat ?? '-' }}</dd>
            </div>
        </dl>

        <!-- Edit Mode -->
        <form x-show="isEditing" action="{{ route('master.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6"
              x-data="{
                  education: {{ $teacher->data_guru && $teacher->data_guru->riwayat_pendidikan ? $teacher->data_guru->riwayat_pendidikan->toJson() : '[]' }}
              }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Foto Profil</label>
                    <input type="file" name="foto" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $teacher->name }}" required class="input-boss">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $teacher->email }}" required class="input-boss">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">NIK</label>
                    <input type="text" name="nik" value="{{ $teacher->data_guru->nik ?? '' }}" class="input-boss">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">NPWP</label>
                    <input type="text" name="npwp" value="{{ $teacher->data_guru->npwp ?? '' }}" class="input-boss">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="input-boss">
                        <option value="">- Pilih -</option>
                        <option value="L" {{ ($teacher->data_guru->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ ($teacher->data_guru->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">No HP</label>
                    <input type="text" name="no_hp" value="{{ $teacher->data_guru->no_hp ?? '' }}" class="input-boss">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ $teacher->data_guru->tempat_lahir ?? '' }}" class="input-boss">
                </div>
                <div>
                     <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ $teacher->data_guru->tanggal_lahir ? \Carbon\Carbon::parse($teacher->data_guru->tanggal_lahir)->format('Y-m-d') : '' }}" class="input-boss">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Pendidikan Terakhir (Text)</label>
                    <input type="text" name="pendidikan_terakhir" value="{{ $teacher->data_guru->pendidikan_terakhir ?? '' }}" class="input-boss">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Mapel Ajar (Text Referensi)</label>
                    <input type="text" name="mapel_ajar_text" value="{{ $teacher->data_guru->mapel_ajar_text ?? '' }}" class="input-boss">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Riwayat Pesantren (Text)</label>
                    <textarea name="riwayat_pesantren" rows="2" class="input-boss">{{ $teacher->data_guru->riwayat_pesantren ?? '' }}</textarea>
                </div>

                 <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="input-boss">{{ $teacher->data_guru->alamat ?? '' }}</textarea>
                </div>
            </div>

            <!-- Dynamic Education History -->
            <div class="space-y-4 pt-6 mt-6 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">history_edu</span> Riwayat Pendidikan Terstruktur
                    </label>
                    <button type="button" @click="education.push({id: Date.now(), jenjang: '', nama_instansi: '', tahun_masuk: '', tahun_lulus: ''})" class="text-xs flex items-center gap-1 text-primary font-bold hover:bg-primary/10 px-3 py-1.5 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span> Tambah Data
                    </button>
                </div>

                <template x-for="(item, index) in education" :key="index">
                    <div class="grid grid-cols-12 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 items-end relative group">
                        <div class="col-span-3">
                            <label class="text-[10px] text-slate-400 block mb-1 font-bold uppercase">Jenjang</label>
                            <select :name="'pendidikan['+index+'][jenjang]'" x-model="item.jenjang" class="input-boss text-xs py-1.5">
                                <option value="SD">SD/MI</option>
                                <option value="SMP">SMP/Mts</option>
                                <option value="SMA">SMA/MA/SMK</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="Pesantren">Pesantren</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-span-5">
                            <label class="text-[10px] text-slate-400 block mb-1 font-bold uppercase">Nama Instansi</label>
                            <input type="text" :name="'pendidikan['+index+'][nama_instansi]'" x-model="item.nama_instansi" placeholder="Nama Sekolah..." class="input-boss text-xs py-1.5">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] text-slate-400 block mb-1 font-bold uppercase">Masuk</label>
                            <input type="text" :name="'pendidikan['+index+'][tahun_masuk]'" x-model="item.tahun_masuk" placeholder="Thn" class="input-boss text-xs py-1.5">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] text-slate-400 block mb-1 font-bold uppercase">Lulus</label>
                            <div class="flex items-center gap-2">
                                <input type="text" :name="'pendidikan['+index+'][tahun_lulus]'" x-model="item.tahun_lulus" placeholder="Thn" class="input-boss text-xs py-1.5">
                                <button type="button" @click="education.splice(index, 1)" class="text-slate-400 hover:text-red-500 transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="education.length === 0" class="text-center py-8 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 text-xs">
                    <span class="material-symbols-outlined text-3xl mb-2 opacity-30">school</span> <br>
                    Belum ada data pendidikan.
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
                <button type="button" @click="isEditing = false" class="btn-boss btn-secondary">Batal</button>
                <button type="submit" class="btn-boss btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <!-- 2. Beban Mengajar -->
    <div x-show="activeTab === 'beban'" x-transition class="card-boss min-h-[500px]">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Beban Mengajar Aktif</h3>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider font-bold">Tahun Ajaran: <span class="text-primary">{{ $activeYear->nama }}</span></p>
            </div>
            <div class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-bold border border-green-100 flex items-center gap-2">
                <span class="size-2 rounded-full bg-green-500 animate-pulse"></span> Semester Berjalan
            </div>
        </div>

        <div class="table-container border-0 rounded-none rounded-b-2xl shadow-none">
            <table class="w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-6 py-4">Mata Pelajaran</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4 text-center">Jumlah Jam</th>
                        <th class="px-6 py-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($teacher->mapel_ajar->where('kelas.id_tahun_ajaran', $activeYear->id) as $assignment)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                                    <span class="material-symbols-outlined text-[20px]">menu_book</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $assignment->mapel->nama_mapel }}</p>
                                    <p class="text-[10px] uppercase font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 rounded inline-block mt-0.5">{{ $assignment->mapel->kode_mapel }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">
                             <div class="font-bold text-slate-900 dark:text-white">{{ $assignment->kelas?->nama_kelas ?? '-' }}</div>
                             <span class="text-xs text-slate-500">{{ $assignment->kelas?->jenjang?->kode ?? '-' }}</span>
                        </td>
                        <td class="table-cell text-center">
                            <span class="text-slate-500 font-mono">2 JTM</span> <!-- Placeholder if no JTM data -->
                        </td>
                        <td class="table-cell text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase text-white bg-emerald-500 shadow-sm shadow-emerald-200">
                                Aktif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                            <div class="flex flex-col items-center">
                                <div class="bg-slate-50 rounded-full p-4 mb-3">
                                    <span class="material-symbols-outlined text-3xl opacity-50">event_busy</span>
                                </div>
                                <span>Tidak ada beban mengajar di tahun aktif ini.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. Riwayat -->
    <div x-show="activeTab === 'riwayat'" x-transition class="card-boss min-h-[500px]">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Arsip Riwayat Mengajar</h3>
            <p class="text-sm text-slate-500">Rekam jejak pengajaran di tahun-tahun sebelumnya.</p>
        </div>

        <div class="table-container border-0 rounded-none rounded-b-2xl shadow-none">
            <table class="w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-6 py-4">Tahun Ajaran</th>
                        <th class="px-6 py-4">Mata Pelajaran</th>
                        <th class="px-6 py-4">Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($teacher->mapel_ajar->where('kelas.id_tahun_ajaran', '!=', $activeYear->id)->sortByDesc('kelas.id_tahun_ajaran') as $assignment)
                    <tr class="table-row group">
                        <td class="table-cell">
                             <span class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 group-hover:bg-white group-hover:shadow-sm transition-all">
                                {{ $assignment->kelas?->tahun_ajaran?->nama ?? '-' }}
                             </span>
                        </td>
                        <td class="table-cell">
                            <span class="font-medium text-slate-900 dark:text-white">{{ $assignment->mapel->nama_mapel }}</span>
                        </td>
                        <td class="table-cell">
                            <span class="text-slate-600">{{ $assignment->kelas?->nama_kelas ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">
                             Belum ada riwayat mengajar arsip.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Keamanan -->
    <div x-show="activeTab === 'keamanan'" x-transition class="card-boss p-8 max-w-2xl mx-auto w-full">
        <div class="text-center mb-8">
            <div class="size-16 rounded-2xl bg-red-50 text-red-500 mx-auto flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-3xl">lock_reset</span>
            </div>
            <h3 class="font-bold text-slate-900 dark:text-white text-xl">Update Keamanan</h3>
            <p class="text-sm text-slate-500">Ubah password akun guru ini untuk keamanan.</p>
        </div>

        <form action="{{ route('master.teachers.password', $teacher->id) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Password Baru</label>
                <div class="relative">
                    <input type="password" name="password" required class="input-boss pl-10">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400">key</span>
                </div>
                <p class="mt-2 text-xs text-slate-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">info</span>
                    Minimal 6 karakter.
                </p>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" required class="input-boss pl-10">
                     <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400">lock</span>
                </div>
            </div>

            <button type="submit" class="w-full btn-boss btn-primary py-3 justify-center shadow-red-500/20 bg-red-600 hover:bg-red-700 border-none text-white">
                Update Password
            </button>
        </form>
    </div>

</div>
@endsection
