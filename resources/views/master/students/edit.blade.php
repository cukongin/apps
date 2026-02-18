@extends('layouts.app')

@section('title', 'Edit Data Siswa')

@section('content')
<div class="max-w-[1000px] mx-auto flex flex-col gap-8">

    <!-- Heading -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="flex flex-col gap-1 max-w-2xl">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">edit_square</span>
                Edit Data Siswa
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Perbarui informasi biodata, akademik, dan orang tua siswa.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('master.students.index') }}" class="btn-boss btn-secondary">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                <span>Kembali</span>
            </a>
            <button type="submit" form="editStudentForm" class="btn-boss btn-primary">
                <span class="material-symbols-outlined text-[20px]">save</span>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card-boss p-8">
        <form id="editStudentForm" action="{{ route('master.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column: Photo & Basic Identity -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative group">
                            <div class="size-40 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border-2 border-slate-200 dark:border-slate-700 shadow-sm">
                                @if($student->foto)
                                    <img src="{{ asset($student->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-6xl font-black text-slate-300">{{ substr($student->nama_lengkap, 0, 1) }}</div>
                                @endif

                                <!-- Overlay Upload -->
                                <label for="foto_upload" class="absolute inset-0 bg-slate-900/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                    <span class="material-symbols-outlined text-white text-3xl">cloud_upload</span>
                                </label>
                            </div>
                            <input type="file" id="foto_upload" name="foto" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <p class="text-xs text-slate-500 text-center">Klik gambar untuk mengganti foto.<br>Max: 2MB (JPG, PNG)</p>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                         <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status Siswa</label>
                            <select name="status_siswa" class="input-boss">
                                <option value="aktif" {{ $student->status_siswa == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="lulus" {{ $student->status_siswa == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="mutasi" {{ $student->status_siswa == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                                <option value="keluar" {{ $student->status_siswa == 'keluar' ? 'selected' : '' }}>Keluar</option>
                                <option value="non-aktif" {{ $student->status_siswa == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenjang</label>
                            <select name="id_jenjang" class="input-boss">
                                @foreach($levels as $lvl)
                                <option value="{{ $lvl->id }}" {{ $student->id_jenjang == $lvl->id ? 'selected' : '' }}>{{ $lvl->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun Masuk</label>
                            <input type="number" name="tahun_masuk" value="{{ $student->tahun_masuk }}" class="input-boss">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Identitas -->
                    <div class="space-y-4">
                        <h4 class="font-bold text-slate-900 dark:text-white text-lg border-b border-slate-100 dark:border-slate-800 pb-2 flex items-center gap-2">
                             <span class="material-symbols-outlined text-primary">badge</span>
                             Identitas Diri
                        </h4>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ $student->nama_lengkap }}" required class="input-boss font-bold text-lg">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NIS Lokal</label>
                                <input type="text" name="nis_lokal" value="{{ $student->nis_lokal }}" class="input-boss">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NISN</label>
                                <input type="text" name="nisn" value="{{ $student->nisn }}" class="input-boss">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NIK</label>
                                <input type="text" name="nik" value="{{ $student->nik }}" class="input-boss">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="input-boss">
                                    <option value="L" {{ $student->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $student->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ $student->tempat_lahir }}" class="input-boss">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->format('Y-m-d') : '' }}" class="input-boss">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" rows="3" class="input-boss">{{ $student->alamat_lengkap }}</textarea>
                        </div>
                    </div>

                    <!-- Orang Tua -->
                    <div class="space-y-4">
                        <h4 class="font-bold text-slate-900 dark:text-white text-lg border-b border-slate-100 dark:border-slate-800 pb-2 flex items-center gap-2">
                             <span class="material-symbols-outlined text-primary">family_restroom</span>
                             Data Orang Tua
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Ayah</label>
                                <input type="text" name="nama_ayah" value="{{ $student->nama_ayah }}" class="input-boss">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" value="{{ $student->pekerjaan_ayah }}" class="input-boss">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Ibu</label>
                                <input type="text" name="nama_ibu" value="{{ $student->nama_ibu }}" class="input-boss">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" value="{{ $student->pekerjaan_ibu }}" class="input-boss">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">No. Telepon Ortu / WA</label>
                            <input type="text" name="no_telp_ortu" value="{{ $student->no_telp_ortu }}" class="input-boss">
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection
