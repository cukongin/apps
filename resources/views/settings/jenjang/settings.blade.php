@extends('layouts.app')

@section('title', 'Pengaturan - ' . $jenjang->nama)

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'identitas' }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-surface-dark p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">
                <a href="{{ route('settings.jenjang.index') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
                </a>
                <span>/</span>
                <span>{{ $jenjang->kode }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">tune</span>
                Pengaturan {{ $jenjang->nama }}
            </h1>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 rounded-xl bg-primary/10 text-primary border border-primary/20 font-bold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                <span>TA: {{ $activeYear->nama }}</span>
            </div>
            @if($isLocked)
            <div class="bg-amber-100 text-amber-800 px-4 py-2 rounded-xl border border-amber-200 font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">lock</span> Mode Baca
            </div>
            @endif
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left Sidebar (Navigation) -->
        <div class="lg:col-span-3 sticky top-6">
            <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
                <div class="p-4 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-bold text-slate-400 uppercase text-xs tracking-wider">Menu Pengaturan</h3>
                </div>
                <nav class="p-2 space-y-1">
                    <button @click="activeTab = 'identitas'"
                        :class="activeTab === 'identitas' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left">
                        <span class="material-symbols-outlined text-[20px]">domain</span>
                        Identitas & Kop
                    </button>

                    <button @click="activeTab = 'akademik'"
                        :class="activeTab === 'akademik' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left">
                        <span class="material-symbols-outlined text-[20px]">school</span>
                        Akademik & Kenaikan
                    </button>

                    <button @click="activeTab = 'bobot'"
                        :class="activeTab === 'bobot' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left">
                        <span class="material-symbols-outlined text-[20px]">balance</span>
                        Bobot Penilaian
                    </button>

                    <button @click="activeTab = 'predikat'"
                        :class="activeTab === 'predikat' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left">
                        <span class="material-symbols-outlined text-[20px]">grade</span>
                        Interval Predikat
                    </button>

                    <button @click="activeTab = 'kkm'"
                        :class="activeTab === 'kkm' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left">
                        <span class="material-symbols-outlined text-[20px]">analytics</span>
                        Target KKM Mapel
                    </button>

                    <button @click="activeTab = 'dates'"
                        :class="activeTab === 'dates' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left">
                        <span class="material-symbols-outlined text-[20px]">event</span>
                        Tanggal Rapor
                    </button>
                    <button @click="activeTab = 'dkn'"
                        :class="activeTab === 'dkn' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left">
                        <span class="material-symbols-outlined text-[20px]">history_edu</span>
                        DKN & Ijazah
                    </button>
                </nav>
            </div>

            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-800/50">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 mt-0.5">info</span>
                    <p class="text-xs text-blue-800 dark:text-blue-200 leading-relaxed">
                        Pengaturan ini berlaku untuk Tahun Ajaran <strong>{{ $activeYear->nama }}</strong>. Perubahan akan langsung berdampak pada rapor siswa.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="lg:col-span-9">
            <form action="{{ route('settings.jenjang.updateSettings', $jenjang->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Sticky Save Button (Mobile & Desktop) -->
                @if(!$isLocked)
                <div class="fixed bottom-6 right-6 z-50">
                    <button type="submit" class="bg-slate-900 dark:bg-primary text-white shadow-2xl shadow-slate-900/30 rounded-full px-6 py-4 flex items-center gap-3 hover:scale-105 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-2xl">save</span>
                        <span class="font-bold text-lg">Simpan Pengaturan</span>
                    </button>
                </div>
                @endif

                <!-- TAB CONTENT -->
                <div class="min-h-[500px]">

                    <!-- 1. IDENTITAS -->
                    <div x-show="activeTab === 'identitas'" class="space-y-6 animate-fade-in-up" x-cloak>
                        <div class="card-boss !p-8">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">domain</span> Identitas & Kop Surat
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-5">
                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Sekolah (Kop)</label>
                                        <input type="text" name="nama_sekolah" value="{{ $identity->nama_sekolah ?? '' }}" class="input-boss font-bold text-lg" {{ $isLocked ? 'disabled' : '' }}>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">NSM</label>
                                            <input type="text" name="nsm" value="{{ $identity->nsm ?? '' }}" class="input-boss" {{ $isLocked ? 'disabled' : '' }}>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">NPSN</label>
                                            <input type="text" name="npsn" value="{{ $identity->npsn ?? '' }}" class="input-boss" {{ $isLocked ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Alamat Lengkap</label>
                                        <textarea name="alamat" class="input-boss h-24" {{ $isLocked ? 'disabled' : '' }}>{{ $identity->alamat ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="space-y-5">
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-100 dark:border-slate-700">
                                        <label class="text-xs font-bold text-indigo-500 uppercase tracking-wide mb-3 block">Kepala Madrasah</label>
                                        <div class="space-y-3">
                                            <div>
                                                <input type="text" name="kepala_madrasah" value="{{ $identity->kepala_madrasah ?? '' }}" class="input-boss font-black" placeholder="Nama Lengkap & Gelar" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                            <div>
                                                <input type="text" name="nip_kepala" value="{{ $identity->nip_kepala ?? '' }}" class="input-boss text-sm" placeholder="NIP / NIPPPK (Opsional)" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2 block">Logo Sekolah</label>
                                        <div class="flex items-center gap-4">
                                            @if($identity->logo)
                                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center p-2 border border-slate-200">
                                                <img src="{{ asset($identity->logo) }}" alt="Logo" class="max-w-full max-h-full">
                                            </div>
                                            @endif
                                            <input type="file" name="logo" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" {{ $isLocked ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. AKADEMIK -->
                    <div x-show="activeTab === 'akademik'" class="space-y-6 animate-fade-in-up" x-cloak>
                        <div class="card-boss !p-8">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">school</span> Akademik & Kenaikan Kelas
                            </h2>

                            <!-- KKM & Hari Efektif -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="bg-emerald-50/50 p-5 rounded-2xl border border-emerald-100">
                                    <label class="text-xs font-bold text-emerald-700 uppercase tracking-wide mb-2 block">KKM Standar</label>
                                    <input type="number" name="kkm_default" value="{{ $settings['kkm_default_' . strtolower($jenjang->kode)] ?? 70 }}" class="input-boss text-center font-black text-2xl text-emerald-700 !border-emerald-200 focus:!ring-emerald-500" {{ $isLocked ? 'disabled' : '' }}>
                                    <p class="text-xs text-emerald-600 mt-2">Batas minimal lulus (default).</p>
                                </div>
                                <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
                                    <label class="text-xs font-bold text-blue-700 uppercase tracking-wide mb-2 block">Hari Efektif / Tahun</label>
                                    <input type="number" name="total_effective_days" value="{{ $settings['total_effective_days_' . strtolower($jenjang->kode)] ?? 200 }}" class="input-boss text-center font-black text-2xl text-blue-700 !border-blue-200 focus:!ring-blue-500" {{ $isLocked ? 'disabled' : '' }}>
                                    <p class="text-xs text-blue-600 mt-2">Total hari belajar dalam setahun.</p>
                                </div>
                            </div>

                            <!-- Syarat Kenaikan -->
                            <h3 class="font-bold text-slate-700 mb-4">Syarat Kenaikan Kelas</h3>
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-100 dark:border-slate-700 space-y-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-slate-800">Maksimal Mapel di Bawah KKM</div>
                                        <div class="text-xs text-slate-500">Batas toleransi nilai merah.</div>
                                    </div>
                                    <div class="w-24">
                                        <input type="number" name="promotion_max_kkm_failure" value="{{ $settings['promotion_max_kkm_failure_' . strtolower($jenjang->kode)] ?? 3 }}" class="input-boss text-center font-bold" {{ $isLocked ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                <hr class="border-slate-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-slate-800">Minimal Kehadiran (%)</div>
                                        <div class="text-xs text-slate-500">Persentase kehadiran minimal siswa.</div>
                                    </div>
                                    <div class="w-24 relative">
                                        <input type="number" name="promotion_min_attendance" value="{{ $settings['promotion_min_attendance_' . strtolower($jenjang->kode)] ?? 75 }}" class="input-boss text-center font-bold pr-8" {{ $isLocked ? 'disabled' : '' }}>
                                        <span class="absolute right-3 top-2.5 font-bold text-slate-400">%</span>
                                    </div>
                                </div>
                                <hr class="border-slate-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-slate-800">Minimal Nilai Akhlak</div>
                                        <div class="text-xs text-slate-500">Predikat sikap minimal.</div>
                                    </div>
                                    <div class="w-24">
                                        <select name="promotion_min_attitude" class="input-boss font-bold text-center" {{ $isLocked ? 'disabled' : '' }}>
                                            @foreach(['A','B','C','D'] as $gr)
                                            <option value="{{ $gr }}" {{ ($settings['promotion_min_attitude_' . strtolower($jenjang->kode)] ?? 'C') == $gr ? 'selected' : '' }}>{{ $gr }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr class="border-slate-200">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div>
                                        <div class="font-bold text-slate-800">Wajib Tuntas Semua Periode</div>
                                        <div class="text-xs text-slate-500">Siswa harus memiliki nilai lengkap di semua semester/cawu.</div>
                                    </div>
                                    <input type="checkbox" name="promotion_requires_all_periods" class="w-6 h-6 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" {{ ($settings['promotion_requires_all_periods_' . strtolower($jenjang->kode)] ?? 0) ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                                </label>
                            </div>

                             <!-- Graduation Rules -->
                             <h3 class="font-bold text-slate-700 mt-8 mb-4">Aturan Kelulusan (Tingkat Akhir)</h3>
                             <div class="bg-orange-50/50 dark:bg-orange-900/10 rounded-2xl p-6 border border-orange-100 dark:border-orange-800/30 space-y-4">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-xs font-bold text-orange-700 uppercase tracking-wide mb-2 block">Kelas Tingkat Akhir</label>
                                        <input type="number" name="final_grade" value="{{ $settings['final_grade_' . strtolower($jenjang->kode)] ?? ($jenjang->kode == 'MI' ? 6 : 9) }}" class="input-boss" placeholder="cth: 6" {{ $isLocked ? 'disabled' : '' }}>
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-orange-700 uppercase tracking-wide mb-2 block">Rentang Kelas Ijazah</label>
                                        <input type="text" name="ijazah_range" value="{{ $settings['ijazah_range_' . strtolower($jenjang->kode)] ?? ($jenjang->kode == 'MI' ? '4,5,6' : '7,8,9') }}" class="input-boss" placeholder="cth: 4,5,6" {{ $isLocked ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                <p class="text-xs text-orange-600 italic">*Rentang kelas digunakan untuk kalkulasi rata-rata nilai Ijazah.</p>
                             </div>
                        </div>
                    </div>

                    <!-- 3. BOBOT -->
                    <div x-show="activeTab === 'bobot'" class="space-y-6 animate-fade-in-up" x-cloak>
                         <div class="card-boss !p-8">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">balance</span> Bobot Penilaian
                            </h2>

                            <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-xl mb-6">
                                <span class="material-symbols-outlined text-slate-400">info</span>
                                <p class="text-sm text-slate-600">Pastikan total bobot jika dijumlahkan logis sesuai kebijakan sekolah (Lazimnya Harian > UTS/UAS).</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                                    <div class="text-slate-500 text-xs font-bold uppercase mb-3">Nilai Harian</div>
                                    <input type="number" name="bobot_harian" value="{{ $bobot->bobot_harian }}" class="input-boss text-center text-3xl font-black text-slate-800 mb-2" {{ $isLocked ? 'disabled' : '' }}>
                                    <span class="text-xs text-slate-400 font-bold block">Faktor Kali (X)</span>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                                    <div class="text-slate-500 text-xs font-bold uppercase mb-3">Nilai UTS</div>
                                    <input type="number" name="bobot_uts_cawu" value="{{ $bobot->bobot_uts_cawu }}" class="input-boss text-center text-3xl font-black text-slate-800 mb-2" {{ $isLocked ? 'disabled' : '' }}>
                                    <span class="text-xs text-slate-400 font-bold block">Faktor Kali (X)</span>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                                    <div class="text-slate-500 text-xs font-bold uppercase mb-3">Nilai UAS</div>
                                    <input type="number" name="bobot_uas" value="{{ $bobot->bobot_uas }}" class="input-boss text-center text-3xl font-black text-slate-800 mb-2" {{ $isLocked ? 'disabled' : '' }}>
                                    <span class="text-xs text-slate-400 font-bold block">Faktor Kali (X)</span>
                                </div>
                            </div>

                             <div class="mt-6 text-center">
                                <p class="text-sm font-bold text-slate-600">Rumus Nilai Akhir:</p>
                                <code class="bg-slate-100 px-3 py-1 rounded text-pink-600 font-mono text-sm mt-2 inline-block">
                                    ( (Harian x {{ $bobot->bobot_harian }}) + (UTS x {{ $bobot->bobot_uts_cawu }}) + (UAS x {{ $bobot->bobot_uas }}) ) / {{ $bobot->bobot_harian + $bobot->bobot_uts_cawu + $bobot->bobot_uas }}
                                </code>
                             </div>
                        </div>
                    </div>

                    <!-- 4. PREDIKAT -->
                    <div x-show="activeTab === 'predikat'" class="space-y-6 animate-fade-in-up" x-cloak>
                        <div class="card-boss !p-8">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-500">grade</span> Interval Predikat
                            </h2>

                            <div class="overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200">
                                        <tr>
                                            <th class="px-6 py-4 font-bold text-slate-700">Grade</th>
                                            <th class="px-6 py-4 font-bold text-slate-700 text-center">Min Score</th>
                                            <th class="px-6 py-4 font-bold text-slate-700 text-center">Max Score</th>
                                            <th class="px-6 py-4 font-bold text-slate-700">Deskripsi (Untuk Rapor)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($predicates as $p)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-3">
                                                <span class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-700 font-black flex items-center justify-center">{{ $p->grade }}</span>
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                <input type="number" name="predikat[{{ $p->grade }}][min]" value="{{ $p->min_score }}" class="input-boss text-center w-24" {{ $isLocked ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                <input type="number" name="predikat[{{ $p->grade }}][max]" value="{{ $p->max_score }}" class="input-boss text-center w-24" {{ $isLocked ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-6 py-3">
                                                <input type="text" name="predikat[{{ $p->grade }}][deskripsi]" value="{{ $p->deskripsi ?? '' }}" class="input-boss w-full" placeholder="Deskripsi capaian..." {{ $isLocked ? 'disabled' : '' }}>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 5. KKM MAPEL -->
                    <div x-show="activeTab === 'kkm'" class="space-y-6 animate-fade-in-up" x-cloak>
                        <div class="card-boss !p-8">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">analytics</span> Target KKM Mata Pelajaran
                            </h2>

                            <div class="flex flex-col gap-4">
                                <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl border border-yellow-100 text-sm">
                                    <strong>Note:</strong> Jika dikosongkan, akan menggunakan KKM Standar ({{ $settings['kkm_default_' . strtolower($jenjang->kode)] ?? 70 }}).
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                    @foreach($mapels as $mapel)
                                    <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                        <div>
                                            <span class="font-bold text-slate-700 block">{{ $mapel->nama_mapel }}</span>
                                            <span class="text-xs text-slate-400">{{ $mapel->kode_mapel }}</span>
                                        </div>
                                        <input type="number" name="kkm[{{ $mapel->id }}]"
                                               value="{{ $rawKkms[$mapel->id]->nilai_kkm ?? '' }}"
                                               placeholder="{{ $settings['kkm_default_' . strtolower($jenjang->kode)] ?? 70 }}"
                                               class="input-boss w-24 text-center font-bold" {{ $isLocked ? 'disabled' : '' }}>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. DATES -->
                    <div x-show="activeTab === 'dates'" class="space-y-6 animate-fade-in-up" x-cloak>
                        <div class="card-boss !p-8">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">event</span> Tanggal Rapor & Transkrip
                            </h2>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Rapor -->
                                <div class="space-y-6">
                                    <h4 class="font-bold text-slate-500 text-sm uppercase tracking-wider border-b border-slate-100 pb-2">Rapor (Semester/Cawu)</h4>

                                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">A</span>
                                            <span class="font-bold text-slate-700">Tanggal Rapor</span>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Tempat / Kota</label>
                                            <input type="text" name="titimangsa_tempat" value="{{ $settings['titimangsa_tempat_' . strtolower($jenjang->kode)] ?? '' }}" class="input-boss font-bold" placeholder="Contoh: Bangkalan" {{ $isLocked ? 'disabled' : '' }}>
                                        </div>
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 mb-1">Tanggal Masehi</label>
                                                <input type="text" name="titimangsa" value="{{ $settings['titimangsa_' . strtolower($jenjang->kode)] ?? '' }}" class="input-boss" placeholder="Contoh: 15 Juli 2024" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 mb-1">Tanggal Hijriyah</label>
                                                <input type="text" name="titimangsa_hijriyah" value="{{ $settings['titimangsa_hijriyah_' . strtolower($jenjang->kode)] ?? '' }}" class="input-boss" placeholder="Contoh: 1 Muharram 1445 H" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transkrip -->
                                <div class="space-y-6">
                                    <h4 class="font-bold text-slate-500 text-sm uppercase tracking-wider border-b border-slate-100 pb-2">Transkrip Nilai (Lulusan)</h4>

                                     <div class="bg-gradient-to-br from-orange-50 to-orange-100/50 p-5 rounded-2xl border border-orange-100 space-y-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs">B</span>
                                            <span class="font-bold text-orange-800">Tanggal Transkrip</span>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-orange-800 mb-1">Tempat / Kota</label>
                                            <input type="text" name="titimangsa_transkrip_tempat" value="{{ $settings['titimangsa_transkrip_tempat_' . strtolower($jenjang->kode)] ?? '' }}" class="input-boss border-orange-200 focus:ring-orange-500 font-bold" placeholder="Samakan dengan Rapor" {{ $isLocked ? 'disabled' : '' }}>
                                        </div>
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-orange-800 mb-1">Tanggal Masehi</label>
                                                <input type="text" name="titimangsa_transkrip" value="{{ $settings['titimangsa_transkrip_' . strtolower($jenjang->kode)] ?? '' }}" class="input-boss border-orange-200 focus:ring-orange-500" placeholder="Contoh: 20 Juni 2024" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-orange-800 mb-1">Tanggal Hijriyah</label>
                                                <input type="text" name="titimangsa_transkrip_hijriyah" value="{{ $settings['titimangsa_transkrip_hijriyah_' . strtolower($jenjang->kode)] ?? '' }}" class="input-boss border-orange-200 focus:ring-orange-500" placeholder="Contoh: 20 Dzulhijjah 1445 H" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 7. DKN / IJAZAH -->
                    <div x-show="activeTab === 'dkn'" class="space-y-6 animate-fade-in-up" x-cloak>
                        <div class="card-boss !p-8">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-purple-600">history_edu</span> Pengaturan DKN & Ijazah
                            </h2>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div class="bg-purple-50 p-5 rounded-2xl border border-purple-100">
                                        <label class="text-xs font-bold text-purple-700 uppercase tracking-wide mb-2 block">Minimal Kelulusan (NA)</label>
                                        <input type="number" step="0.01" name="ijazah_min_lulus" value="{{ $settings['ijazah_min_lulus_' . strtolower($jenjang->kode)] ?? 60 }}" class="input-boss text-center font-black text-2xl text-purple-700 !border-purple-200 focus:!ring-purple-500" {{ $isLocked ? 'disabled' : '' }}>
                                        <p class="text-xs text-purple-600 mt-2">Nilai Rata-rata Akhir minimal untuk Lulus.</p>
                                    </div>

                                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                                        <h4 class="font-bold text-slate-700 mb-4">Bobot Nilai Akhir</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Rata Rapor (%)</label>
                                                <input type="number" name="ijazah_bobot_rapor" value="{{ $settings['ijazah_bobot_rapor_' . strtolower($jenjang->kode)] ?? 60 }}" class="input-boss text-center font-bold" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                            <div>
                                                <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Ujian Madrasah (%)</label>
                                                <input type="number" name="ijazah_bobot_ujian" value="{{ $settings['ijazah_bobot_ujian_' . strtolower($jenjang->kode)] ?? 40 }}" class="input-boss text-center font-bold" {{ $isLocked ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-2 italic">Total harus 100%.</p>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100 dark:border-blue-800/30">
                                        <h4 class="font-bold text-blue-700 mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined">date_range</span> Periode Rata-Rata Rapor
                                        </h4>
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Jenis Periode</label>
                                                    <select name="ijazah_period_label" class="input-boss font-bold" {{ $isLocked ? 'disabled' : '' }}>
                                                        <option value="Semester" {{ ($settings['ijazah_period_label_' . strtolower($jenjang->kode)] ?? ($jenjang->kode == 'MTS' ? 'Semester' : '')) == 'Semester' ? 'selected' : '' }}>Semester</option>
                                                        <option value="Catur Wulan" {{ ($settings['ijazah_period_label_' . strtolower($jenjang->kode)] ?? ($jenjang->kode == 'MI' ? 'Catur Wulan' : '')) == 'Catur Wulan' ? 'selected' : '' }}>Catur Wulan</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Jml Periode/Tahun</label>
                                                    <input type="number" name="ijazah_period_count" value="{{ $settings['ijazah_period_count_' . strtolower($jenjang->kode)] ?? ($jenjang->kode == 'MI' ? 3 : 2) }}" class="input-boss font-bold" min="1" max="4" {{ $isLocked ? 'disabled' : '' }}>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Rentang Kelas (Pisahkan Koma)</label>
                                                <input type="text" name="ijazah_range" value="{{ $settings['ijazah_range_' . strtolower($jenjang->kode)] ?? ($jenjang->kode == 'MI' ? '4,5,6' : '7,8,9') }}" class="input-boss font-bold" placeholder="Contoh: 4,5,6" {{ $isLocked ? 'disabled' : '' }}>
                                                <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
                                                    Tentukan tingkat kelas mana saja yang nilainya akan diambil untuk perhitungan <strong>Rata-Rata Rapor (RR)</strong>.<br>
                                                    <span class="text-blue-600 font-bold">Info:</span> Pastikan data nilai tersedia untuk setiap periode yang dipilih.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- MAPEL SELECTION FOR IJAZAH (FULL WIDTH) -->
                            <div class="bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100 dark:border-indigo-800/30 mt-6">
                                <h4 class="font-bold text-indigo-700 mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined">checklist</span> Mata Pelajaran Ijazah
                                </h4>
                                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                                    Pilih mata pelajaran yang akan <strong>ditampilkan nilainya</strong> pada form leger Ijazah / DKN.
                                </p>

                                @php
                                    $selectedMapels = explode(',', $settings['ijazah_mapels_' . strtolower($jenjang->kode)] ?? '');
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($mapels as $mapel)
                                    <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 cursor-pointer transition-colors group">
                                        <input type="checkbox" name="ijazah_mapels[]" value="{{ $mapel->id }}"
                                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5 group-hover:scale-110 transition-transform"
                                            {{ in_array($mapel->id, $selectedMapels) ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                                        <div class="flex-1">
                                            <div class="font-bold text-slate-700 dark:text-slate-200 text-sm group-hover:text-indigo-700 transition-colors">{{ $mapel->nama_mapel }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $mapel->kode_mapel }} • {{ $mapel->kategori ?? 'Umum' }}</div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
