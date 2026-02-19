@extends('layouts.app')

@section('title', 'Catatan Wali Kelas - ' . $kelas->nama_kelas)

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header & Filters Stack -->
    <div class="flex flex-col gap-4">
        <!-- Header -->
        <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                 <div class="flex items-center gap-2 text-sm font-bold text-slate-500 mb-2">
                    <a href="{{ route('walikelas.dashboard') }}" class="hover:text-primary transition-colors">Dashboard Wali Kelas</a>
                    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                    <span class="text-primary">Catatan Akademik</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-3xl">edit_note</span>
                    Catatan Wali Kelas
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    Berikan catatan motivasi untuk rapor siswa <span class="text-slate-800 dark:text-white font-bold">{{ $kelas->nama_kelas }}</span>.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openGeneratorModal()" class="btn-boss bg-indigo-500 hover:bg-indigo-600 text-white border-transparent shadow-lg shadow-indigo-500/20 flex flex-col md:flex-row items-center gap-1 md:gap-2 px-4 py-2.5">
                    <span class="material-symbols-outlined text-[20px]">auto_fix_high</span>
                    <span class="font-bold">Generate Otomatis</span>
                </button>
                <button type="submit" form="catatanForm" class="btn-boss btn-primary px-6 py-2.5 shadow-lg shadow-primary/30 flex flex-col md:flex-row items-center gap-1 md:gap-2">
                    <span class="material-symbols-outlined">save</span>
                    <span class="font-bold">Simpan Catatan</span>
                </button>
            </div>
        </div>

        <!-- Admin Filter (Only visible for Admin/TU) -->
        @if(auth()->user()->isAdmin() || auth()->user()->isTu())
        <div class="card-boss !p-4 flex flex-col md:flex-row items-center gap-4 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2 text-slate-500 font-bold text-xs uppercase tracking-wider min-w-fit">
                <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                Filter Admin
            </div>
            <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row w-full gap-3">
                 <!-- Jenjang Selector -->
                <div class="relative group w-full md:w-auto">
                    <select name="jenjang" class="input-boss appearance-none !bg-none !pl-9 !pr-8 w-full md:min-w-[100px]" onchange="this.form.submit()">
                        @foreach(['MI', 'MTS'] as $j)
                            <option value="{{ $j }}" {{ (request('jenjang') == $j || (empty(request('jenjang')) && $loop->first)) ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">school</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>

                <!-- Class Selector -->
                <div class="relative group w-full md:w-auto">
                    <select name="kelas_id" class="input-boss appearance-none !bg-none !pl-9 !pr-8 w-full md:min-w-[200px]" onchange="this.form.submit()">
                        @if(isset($allClasses) && $allClasses->count() > 0)
                            @foreach($allClasses as $kls)
                                <option value="{{ $kls->id }}" {{ isset($kelas) && $kelas->id == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama_kelas }}
                                </option>
                            @endforeach
                        @else
                            <option value="">Tidak ada kelas</option>
                        @endif
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">class</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>

    <!-- Form Grid -->
    <div>
        <form id="catatanForm" action="{{ route('walikelas.catatan.store') }}" method="POST">
            @csrf
            <!-- Important: Pass Class ID for Admin context -->
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($students as $ak)
                @php
                    $note = $catatanRows[$ak->id_siswa] ?? null;
                    $avg = $averages[$ak->id_siswa] ?? 0;

                    // Determine styling based on Average
                    $borderColor = 'border-slate-200 dark:border-slate-800';
                    $badgeColor = 'bg-slate-100 text-slate-600';

                    if($avg >= 90) {
                        $borderColor = 'border-emerald-200 dark:border-emerald-800';
                        $badgeColor = 'bg-emerald-100 text-emerald-700';
                    } elseif($avg < 70) {
                        $borderColor = 'border-rose-200 dark:border-rose-800';
                        $badgeColor = 'bg-rose-100 text-rose-700';
                    }
                @endphp
                <div class="card-boss !p-5 flex flex-col gap-4 student-card transition-all duration-300 hover:shadow-lg hover:-translate-y-1 {{ $borderColor }}" data-id="{{ $ak->id_siswa }}" data-avg="{{ $avg }}">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                             <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-black text-sm shadow-inner">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                 <h3 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-base">{{ $ak->siswa->nama_lengkap }}</h3>
                                 <span class="text-xs text-slate-500 font-mono">{{ $ak->siswa->nis_lokal }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-black px-2 py-1 rounded-lg {{ $badgeColor }} uppercase tracking-wider">
                            Avg: {{ number_format($avg, 0) }}
                        </span>
                    </div>

                    <div class="relative">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan / Motivasi</label>
                        <textarea name="catatan[{{ $ak->id_siswa }}]" rows="4" class="input-boss w-full !text-sm !p-3 min-h-[100px] resize-none note-input" placeholder="Tulis catatan motivasi untuk siswa ini...">{{ $note->catatan_akademik ?? '' }}</textarea>
                        <div class="absolute bottom-3 right-3 pointer-events-none text-slate-300">
                             <span class="material-symbols-outlined text-[18px]">edit</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </form>
    </div>
</div>

<!-- Generator Modal -->
<div id="generatorModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeGeneratorModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#1a2332] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-800">

                <!-- Header -->
                <div class="bg-indigo-600 px-4 py-6 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center">
                             <span class="material-symbols-outlined text-white">auto_fix_high</span>
                        </div>
                        <div>
                             <h3 class="text-xl font-bold leading-6 text-white" id="modal-title">
                                Magic Notes Generator
                            </h3>
                            <p class="text-sm text-indigo-100 mt-1">Generate catatan otomatis berdasarkan rata-rata nilai siswa.</p>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-4 py-6 sm:px-6 space-y-5">
                    <!-- Grade A -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                             <label class="block text-sm font-bold text-slate-700 dark:text-white">Grade A (91 - 100)</label>
                             <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Sangat Baik</span>
                        </div>
                        <textarea id="tpl_a" class="input-boss w-full !text-sm" rows="2">Prestasi yang luar biasa! Pertahankan semangat belajarmu dan jadilah inspirasi bagi teman-temanmu.</textarea>
                    </div>
                    <!-- Grade B -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                             <label class="block text-sm font-bold text-slate-700 dark:text-white">Grade B (81 - 90)</label>
                             <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Baik</span>
                        </div>
                        <textarea id="tpl_b" class="input-boss w-full !text-sm" rows="2">Hasil belajarmu sudah baik. Teruslah tekun dan tingkatkan lagi pencapaianmu di semester depan.</textarea>
                    </div>
                    <!-- Grade C -->
                    <div>
                         <div class="flex items-center justify-between mb-2">
                             <label class="block text-sm font-bold text-slate-700 dark:text-white">Grade C (70 - 80)</label>
                             <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Cukup</span>
                        </div>
                        <textarea id="tpl_c" class="input-boss w-full !text-sm" rows="2">Kamu memiliki potensi besar. Tingkatkan lagi fokus dan kedisiplinan dalam belajar agar hasilnya lebih maksimal.</textarea>
                    </div>
                    <!-- Grade D -->
                    <div>
                         <div class="flex items-center justify-between mb-2">
                             <label class="block text-sm font-bold text-slate-700 dark:text-white">Grade D (< 70)</label>
                             <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Perlu Bimbingan</span>
                        </div>
                        <textarea id="tpl_d" class="input-boss w-full !text-sm" rows="2">Jangan menyerah! Belajarlah lebih giat lagi, perbanyak latihan, dan jangan ragu bertanya kepada guru.</textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="applyGenerator()" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-all">
                        <span class="material-symbols-outlined text-[18px] mr-2">auto_fix_normal</span> Terapkan Catatan
                    </button>
                    <button type="button" onclick="closeGeneratorModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-slate-700 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 sm:mt-0 sm:w-auto transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openGeneratorModal() {
        document.getElementById('generatorModal').classList.remove('hidden');
    }

    function closeGeneratorModal() {
        document.getElementById('generatorModal').classList.add('hidden');
    }

    function applyGenerator() {
        Swal.fire({
            title: 'Generate Catatan Otomatis?',
            text: "Aksi ini akan menimpa Catatan siswa yang sudah ada. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1', // Indigo
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Generate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const tplA = document.getElementById('tpl_a').value;
                const tplB = document.getElementById('tpl_b').value;
                const tplC = document.getElementById('tpl_c').value;
                const tplD = document.getElementById('tpl_d').value;

                document.querySelectorAll('.student-card').forEach(card => {
                    const avg = parseFloat(card.dataset.avg) || 0;
                    const noteInput = card.querySelector('.note-input');

                    let message = '';

                    if (avg >= 91) { message = tplA; }
                    else if (avg >= 81) { message = tplB; }
                    else if (avg >= 70) { message = tplC; }
                    else {
                        message = tplD;
                    }

                    // Set Note
                    noteInput.value = message;
                });

                closeGeneratorModal();
                Swal.fire(
                    'Berhasil!',
                    'Catatan telah digenerate sesuai nilai rata-rata.',
                    'success'
                )
            }
        });
    }
</script>
@endsection
