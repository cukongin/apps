@extends('layouts.app')

@section('title', 'Detail Data Siswa')

@section('content')
<div class="max-w-7xl mx-auto flex flex-col gap-6 font-sans" x-data="{ isEditing: false }">

    <!-- Top Configuration / Back -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 no-print">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('master.students.index') }}" class="hover:text-primary transition-colors flex items-center gap-1 font-medium">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Data Siswa
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 dark:text-white font-medium">Detail Buku Induk</span>
        </div>
        <div class="flex gap-3">
             <button onclick="window.print()" class="btn-boss btn-secondary">
                <span class="material-symbols-outlined text-[20px]">print</span>
                <span class="hidden sm:inline">Cetak PDF</span>
            </button>
            <template x-if="!isEditing">
                <button type="button" @click="isEditing = true" class="btn-boss btn-primary">
                    <span class="material-symbols-outlined text-[20px]">edit</span> Edit Data
                </button>
            </template>
            <template x-if="isEditing">
                 <button type="button" @click="location.reload()" class="btn-boss btn-secondary bg-red-50 text-red-600 hover:bg-red-100 border-red-200">
                    Batal
                </button>
            </template>
        </div>
    </div>

    <form action="{{ route('master.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Header Card: Identity -->
        <div class="card-boss p-6 md:p-8 flex flex-col md:flex-row gap-8 relative overflow-hidden group">
             <!-- Decorative Top Bar -->
             <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary to-emerald-400 group-hover:h-2 transition-all"></div>

             <!-- Photo Section -->
             <div class="flex-shrink-0 relative self-center md:self-start">
                <div class="w-40 h-52 bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden border-4 border-white dark:border-slate-700 shadow-lg flex items-center justify-center relative">
                    @if($student->foto)
                        <img src="{{ asset('public/' . $student->foto) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->nama_lengkap) }}&background=003e29&color=fff&size=200&bold=true" class="w-full h-full object-cover">
                    @endif

                    <!-- Edit Photo Overlay -->
                    <div x-show="isEditing" x-transition.opacity class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white cursor-pointer backdrop-blur-[2px]">
                        <span class="material-symbols-outlined text-3xl mb-1 animate-bounce">upload</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Ganti Foto</span>
                        <input type="file" name="foto" class="absolute inset-0 opacity-0 cursor-pointer text-[0px]" accept="image/*">
                    </div>
                </div>

                <!-- Active Badge -->
                <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 whitespace-nowrap">
                     <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border-4 border-white dark:border-surface-dark shadow-sm {{ $student->status_siswa == 'aktif' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $student->status_siswa }}
                    </span>
                </div>
             </div>

             <!-- Main Info -->
             <div class="flex-1 flex flex-col pt-2">
                 <div class="flex justify-between items-start mb-6">
                     <div class="w-full">
                         <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                            <span x-show="!isEditing">{{ $student->nama_lengkap }}</span>
                            <input x-show="isEditing" type="text" name="nama_lengkap" value="{{ $student->nama_lengkap }}" class="w-full border-b-2 border-primary bg-transparent text-3xl md:text-4xl font-black focus:outline-none placeholder-slate-300">
                         </h1>
                         <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mt-2">
                             <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                                 <span class="material-symbols-outlined text-[18px]">badge</span>
                                 <span>NIS: <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $student->nis_lokal ?? '-' }}</span></span>
                             </div>
                             <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                                 <span class="material-symbols-outlined text-[18px]">fingerprint</span>
                                 <span>NISN:
                                     <span x-show="!isEditing" class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $student->nisn ?? '-' }}</span>
                                     <input x-show="isEditing" type="text" name="nisn" value="{{ $student->nisn }}" class="w-24 bg-transparent border-b border-primary text-primary focus:outline-none font-bold">
                                 </span>
                             </div>
                             <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                                 <span class="material-symbols-outlined text-[18px]">wc</span>
                                 <span x-show="!isEditing">{{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                 <select x-show="isEditing" name="jenis_kelamin" class="bg-transparent border-b border-primary text-primary text-xs focus:outline-none py-0">
                                    <option value="L" {{ $student->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $student->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                             </div>
                         </div>
                     </div>

                     <!-- Save Button (Floating) -->
                     <div x-show="isEditing" x-transition class="fixed bottom-6 right-6 z-50 animate-bounce">
                        <button type="submit" class="btn-boss btn-primary px-6 py-3 rounded-full shadow-xl shadow-primary/30 text-base">
                            <span class="material-symbols-outlined">save</span> Simpan Perubahan
                        </button>
                     </div>
                 </div>

                 <!-- Grid Stats -->
                 <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-auto">
                     <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                         <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Kelas Saat Ini</span>
                         <span class="text-lg font-bold text-primary">{{ $student->kelas_saat_ini->kelas->nama_kelas ?? '-' }}</span>
                     </div>
                     <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Tahun Masuk</span>
                        <div class="text-lg font-bold text-slate-800 dark:text-white">
                            <span x-show="!isEditing">{{ $student->tahun_masuk ?? date('Y') }}</span>
                            <input x-show="isEditing" type="number" name="tahun_masuk" value="{{ $student->tahun_masuk }}" class="w-full bg-white border border-slate-200 rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                     <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Wali Kelas</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white truncate block" title="{{ $student->kelas_saat_ini->kelas->wali_kelas->name ?? '-' }}">
                            {{ Str::limit($student->kelas_saat_ini->kelas->wali_kelas->name ?? '-', 15) }}
                        </span>
                    </div>
                     <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Jenjang</span>
                        <span class="text-lg font-bold text-slate-800 dark:text-white">{{ optional($student->jenjang)->kode ?? '-' }}</span>
                    </div>
                 </div>
             </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-20">
            <!-- Section: Biodata Pribadi -->
            <div class="card-boss p-6 lg:p-8 h-full">
                 <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                     <div class="size-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                        <span class="material-symbols-outlined">person_book</span>
                     </div>
                     <h2 class="text-lg font-bold text-slate-900 dark:text-white">Biodata Pribadi</h2>
                 </div>

                 <div class="space-y-6">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                             <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tempat, Tanggal Lahir</label>
                             <div x-show="!isEditing" class="text-slate-800 dark:text-white font-medium text-base">
                                {{ $student->tempat_lahir }}, {{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->translatedFormat('d F Y') : '' }}
                             </div>
                             <div x-show="isEditing" class="flex gap-2">
                                <input type="text" name="tempat_lahir" value="{{ $student->tempat_lahir }}" placeholder="Tempat" class="input-boss">
                                <input type="date" name="tanggal_lahir" value="{{ $student->tanggal_lahir }}" class="input-boss">
                             </div>
                         </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">NIK / No. KTP</label>
                            <div x-show="!isEditing" class="text-slate-800 dark:text-white font-medium text-base font-mono">
                               {{ $student->nik ?? '-' }}
                            </div>
                            <input x-show="isEditing" type="text" name="nik" value="{{ $student->nik }}" class="input-boss font-mono">
                        </div>
                     </div>

                     <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alamat Lengkap</label>
                        <div x-show="!isEditing" class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                           {{ $student->alamat_lengkap ?? '-' }}
                        </div>
                        <textarea x-show="isEditing" name="alamat_lengkap" rows="3" class="input-boss">{{ $student->alamat_lengkap }}</textarea>
                    </div>
                 </div>
            </div>

            <!-- Section: Data Orang Tua -->
            <div class="card-boss p-6 lg:p-8 h-full">
                 <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                     <div class="size-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600">
                        <span class="material-symbols-outlined">family_restroom</span>
                     </div>
                     <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Orang Tua</h2>
                 </div>

                 <div class="space-y-6">
                    <!-- Ayah -->
                    <div class="flex gap-4 items-start">
                        <div class="w-8 pt-1 text-center">
                            <span class="material-symbols-outlined text-purple-500">man</span>
                            <span class="text-[10px] font-bold text-purple-500 block uppercase">Ayah</span>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-4">
                             <div>
                                <label class="text-[10px] text-slate-400 block mb-1">Nama Lengkap</label>
                                <div x-show="!isEditing" class="font-bold text-slate-800 dark:text-white">{{ $student->nama_ayah ?? '-' }}</div>
                                <input x-show="isEditing" type="text" name="nama_ayah" value="{{ $student->nama_ayah }}" class="input-boss">
                            </div>
                            <div>
                                 <label class="text-[10px] text-slate-400 block mb-1">Pekerjaan</label>
                                 <div x-show="!isEditing" class="font-medium text-slate-600 dark:text-slate-400">{{ $student->pekerjaan_ayah ?? '-' }}</div>
                                 <input x-show="isEditing" type="text" name="pekerjaan_ayah" value="{{ $student->pekerjaan_ayah }}" class="input-boss">
                            </div>
                        </div>
                    </div>

                    <!-- Ibu -->
                    <div class="flex gap-4 items-start">
                        <div class="w-8 pt-1 text-center">
                            <span class="material-symbols-outlined text-pink-500">woman</span>
                            <span class="text-[10px] font-bold text-pink-500 block uppercase">Ibu</span>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-4">
                             <div>
                                <label class="text-[10px] text-slate-400 block mb-1">Nama Lengkap</label>
                                <div x-show="!isEditing" class="font-bold text-slate-800 dark:text-white">{{ $student->nama_ibu ?? '-' }}</div>
                                 <input x-show="isEditing" type="text" name="nama_ibu" value="{{ $student->nama_ibu }}" class="input-boss">
                            </div>
                             <div>
                                 <label class="text-[10px] text-slate-400 block mb-1">Pekerjaan</label>
                                 <div x-show="!isEditing" class="font-medium text-slate-600 dark:text-slate-400">{{ $student->pekerjaan_ibu ?? '-' }}</div>
                                 <input x-show="isEditing" type="text" name="pekerjaan_ibu" value="{{ $student->pekerjaan_ibu }}" class="input-boss">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-4">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kontak Orang Tua / Wali</label>
                        <div class="flex items-center gap-2">
                             <span class="material-symbols-outlined text-green-500">call</span>
                             <div x-show="!isEditing" class="text-lg font-bold text-slate-800 dark:text-white">{{ $student->no_telp_ortu ?? '-' }}</div>
                             <input x-show="isEditing" type="text" name="no_telp_ortu" value="{{ $student->no_telp_ortu }}" class="input-boss w-64">
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </form>

    <!-- Section: Rekap Nilai Tahunan -->
    <div class="card-boss p-6 lg:p-8 break-inside-avoid">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="size-10 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined">table_chart</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Rekapitulasi Akademik</h2>
                    <p class="text-xs text-slate-500">Riwayat kelas dan nilai rata-rata per tahun ajaran.</p>
                </div>
            </div>

            <div class="table-container">
                <table class="w-full text-sm text-left">
                    <thead class="table-head">
                        <tr>
                            <th scope="col" class="px-6 py-4">Tahun Ajaran</th>
                            <th scope="col" class="px-6 py-4">Tingkat Kelas</th>
                            <th scope="col" class="px-6 py-4 text-center">Rapor Semester</th>
                            <th scope="col" class="px-6 py-4 text-right">Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($student->riwayat_kelas as $index => $riwayat)
                        <!-- Row Clickable to Report Card (New Tab) -->
                        <tr class="table-row group cursor-pointer" onclick="window.open('{{ route('reports.print', ['student' => $student->id, 'year_id' => $riwayat->kelas->id_tahun_ajaran]) }}', '_blank')">

                            <td class="table-cell">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors">{{ $riwayat->kelas->tahun_ajaran->nama ?? '-' }}</span>
                                    <span class="text-xs text-slate-400">{{ $loop->iteration }} - Semester Ganjil & Genap</span>
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="font-bold text-slate-700 dark:text-slate-300">
                                    {{ $riwayat->kelas->nama_kelas }}
                                </div>
                                <span class="text-xs text-slate-500">Wali: {{ $riwayat->kelas->wali_kelas->name ?? '-' }}</span>
                            </td>
                            <td class="table-cell text-center">
                                @if(isset($gradeHistory[$riwayat->id_kelas]['periods']) && count($gradeHistory[$riwayat->id_kelas]['periods']) > 0)
                                    <div class="flex gap-2 justify-center">
                                        @foreach($gradeHistory[$riwayat->id_kelas]['periods'] as $period => $val)
                                            <div class="px-3 py-1 rounded bg-slate-100 dark:bg-slate-700 text-xs font-bold {{ $val < 70 ? 'text-red-500' : 'text-slate-700 dark:text-slate-300' }}">
                                                {{ $val }}
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Belum ada nilai</span>
                                @endif
                            </td>
                            <td class="table-cell text-right">
                                @php
                                    $displayStatus = $riwayat->status;
                                    // Fetch Active Year ID (Global)
                                    // Cache logic to avoid N+1 query
                                    static $activeYearIdCached = null;
                                    if ($activeYearIdCached === null) {
                                        $activeYearIdCached = \App\Models\TahunAjaran::where('status', 'aktif')->value('id');
                                    }

                                    $isPast = $riwayat->kelas->id_tahun_ajaran != $activeYearIdCached;

                                    if ($displayStatus == 'aktif' && $isPast) {
                                        $grade = (int) filter_var($riwayat->kelas->nama_kelas, FILTER_SANITIZE_NUMBER_INT);
                                        $displayStatus = in_array($grade, [6, 9, 12]) ? 'lulus' : 'naik_kelas';
                                    }
                                @endphp

                                @if($displayStatus == 'aktif')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        <span class="size-1.5 rounded-full bg-blue-500 animate-pulse"></span> AKTIF
                                    </span>
                                @elseif($displayStatus == 'lulus')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                        <span class="material-symbols-outlined text-[14px]">school</span> LULUS
                                    </span>
                                @elseif($displayStatus == 'naik_kelas')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="material-symbols-outlined text-[14px]">trending_up</span> NAIK KELAS
                                    </span>
                                @elseif($displayStatus == 'tinggal_kelas')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                        <span class="material-symbols-outlined text-[14px]">trending_down</span> TINGGAL
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-slate-500">{{ strtoupper(str_replace('_', ' ', $displayStatus)) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-4xl mb-2 opacity-30">history_edu</span>
                                    <span>Belum ada data riwayat pendidikan.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    </div>

    <!-- Graduation Status -->
    <div class="mt-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-6 border border-emerald-100 dark:border-emerald-800 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <div class="size-12 rounded-full bg-white dark:bg-emerald-900 text-emerald-600 flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <h3 class="font-bold text-emerald-900 dark:text-emerald-100">Status Kelulusan Akhir</h3>
                <p class="text-sm text-emerald-700 dark:text-emerald-300 opacity-80">Menandakan santri telah menyelesaikan seluruh jenjang pendidikan.</p>
            </div>
        </div>
        <div class="text-right">
            @if($student->status_siswa == 'lulus')
                 <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">LULUS</span>
            @else
                 <span class="text-sm font-bold text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-lg">Belum Lulus</span>
            @endif
        </div>
    </div>

    <div class="text-center text-slate-400 text-xs mt-8 pb-8 no-print font-mono">
         ID: {{ $student->id }} &bull; {{ date('Y') }} &copy; Madrasah Digital System
    </div>

</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .card-boss { box-shadow: none !important; border: 1px solid #eee !important; }
        .bg-slate-50 { background-color: #f8fafc !important; }
    }
</style>
@endsection
