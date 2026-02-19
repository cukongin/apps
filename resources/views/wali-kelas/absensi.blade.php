@extends('layouts.app')

@section('title', 'Input Absensi - ' . $kelas->nama_kelas)

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
                    <span class="text-primary">Absensi & Kepribadian</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-3xl">fact_check</span>
                    Input Ketidakhadiran & Kepribadian
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    Kelas: <span class="text-slate-800 dark:text-white font-bold">{{ $kelas->nama_kelas }}</span> &bull; Periode: <span class="text-slate-800 dark:text-white font-bold">{{ $periode->nama_periode }}</span>
                </p>
            </div>

            <!-- Actions Toolbar -->
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <!-- Template & Import -->
                <div class="flex items-center gap-2 border-r border-slate-200 dark:border-slate-700 pr-4 mr-2">
                    <a href="{{ route('walikelas.absensi.template') }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm px-3 py-2 text-xs flex flex-col md:flex-row items-center gap-1 md:gap-2">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span class="hidden md:inline">Template</span>
                    </a>

                    <form action="{{ route('walikelas.absensi.import') }}" method="POST" enctype="multipart/form-data" class="inline-block relative">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="file" name="file_absensi" id="file_absensi" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" onchange="this.form.submit()">
                        <button type="button" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm px-3 py-2 text-xs flex flex-col md:flex-row items-center gap-1 md:gap-2">
                            <span class="material-symbols-outlined text-[18px]">upload</span>
                            <span class="hidden md:inline">Import</span>
                        </button>
                    </form>
                </div>

                <button type="button" onclick="setNihil()" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm px-4 py-2 text-xs font-bold flex flex-col md:flex-row items-center gap-1 md:gap-2">
                    <span class="material-symbols-outlined">restart_alt</span>
                    <span class="hidden md:inline">Set Nihil (0)</span>
                    <span class="md:hidden">Reset</span>
                </button>
                <button type="submit" form="absensiForm" class="btn-boss btn-primary px-6 py-2.5 shadow-lg shadow-primary/30 flex flex-col md:flex-row items-center gap-1 md:gap-2">
                    <span class="material-symbols-outlined">save</span>
                    <span class="hidden md:inline">Simpan Perubahan</span>
                    <span class="md:hidden">Simpan</span>
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

                 <!-- Period Selector -->
                <div class="relative group w-full md:w-auto">
                    <select name="periode_id" class="input-boss appearance-none !bg-none !pl-9 !pr-8 w-full md:min-w-[200px]" onchange="this.form.submit()">
                        @foreach($allPeriods as $p)
                            <option value="{{ $p->id }}" {{ $periode->id == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    </div>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>

    <!-- Form Table -->
    <div class="card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <form id="absensiForm" action="{{ route('walikelas.absensi.store') }}" method="POST">
            @csrf
            <!-- Important: Pass Class ID for Admin context -->
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
            <input type="hidden" name="periode_id" value="{{ $periode->id }}">

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 w-10 text-center">No</th>
                            <th class="px-6 py-4 min-w-[250px]">Nama Siswa</th>
                            <th class="px-4 py-4 text-center w-24 bg-primary/5 text-primary border-r border-slate-100 dark:border-slate-700">Sakit</th>
                            <th class="px-4 py-4 text-center w-24 bg-amber-50/50 text-amber-700 border-r border-slate-100 dark:border-slate-700">Izin</th>
                            <th class="px-4 py-4 text-center w-24 bg-rose-50/50 text-rose-700 border-r border-slate-100 dark:border-slate-700">Alpa</th>
                            <th class="px-4 py-4 text-center w-24 font-black">Total</th>
                            <th class="px-4 py-4 text-center w-32 border-l border-slate-200 dark:border-slate-700">Kelakuan</th>
                            <th class="px-4 py-4 text-center w-32">Kerajinan</th>
                            <th class="px-4 py-4 text-center w-32">Kebersihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 bg-white dark:bg-[#1a2332]">
                        @foreach($students as $index => $ak)
                        @php
                            $absensi = $absensiRows[$ak->id_siswa] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-3 text-slate-500 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="px-6 py-3">
                                <div class="font-bold text-slate-800 dark:text-white truncate max-w-[250px]">{{ $ak->siswa->nama_lengkap }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $ak->siswa->nis_lokal }}</div>
                            </td>

                            <!-- Inputs -->
                            <td class="px-2 py-2 bg-primary/5 border-r border-slate-100 dark:border-slate-700">
                                <input type="number" name="absensi[{{ $ak->id_siswa }}][sakit]" value="{{ $absensi->sakit ?? 0 }}" min="0" class="w-full text-center font-bold text-primary bg-transparent border-0 focus:ring-0 p-0 abs-input text-lg" data-target="total-{{ $ak->id }}">
                            </td>
                            <td class="px-2 py-2 bg-amber-50/30 border-r border-slate-100 dark:border-slate-700">
                                <input type="number" name="absensi[{{ $ak->id_siswa }}][izin]" value="{{ $absensi->izin ?? 0 }}" min="0" class="w-full text-center font-bold text-amber-600 bg-transparent border-0 focus:ring-0 p-0 abs-input text-lg" data-target="total-{{ $ak->id }}">
                            </td>
                            <td class="px-2 py-2 bg-rose-50/30 border-r border-slate-100 dark:border-slate-700">
                                <input type="number" name="absensi[{{ $ak->id_siswa }}][alpa]" value="{{ $absensi->tanpa_keterangan ?? 0 }}" min="0" class="w-full text-center font-bold text-rose-600 bg-transparent border-0 focus:ring-0 p-0 abs-input text-lg" data-target="total-{{ $ak->id }}">
                            </td>

                            <!-- Total (Calculated) -->
                            <td class="px-4 py-3 text-center font-black text-slate-800 dark:text-white text-lg" id="total-{{ $ak->id }}">
                                {{ ($absensi->sakit ?? 0) + ($absensi->izin ?? 0) + ($absensi->tanpa_keterangan ?? 0) }}
                            </td>

                            <!-- Personality Inputs -->
                            <td class="px-2 py-2 border-l border-slate-200 dark:border-slate-700">
                                <select name="absensi[{{ $ak->id_siswa }}][kelakuan]" class="w-full text-xs font-bold border-none bg-transparent focus:ring-0 text-center cursor-pointer text-slate-700 dark:text-slate-300">
                                    <option value="Baik" {{ ($absensi->kelakuan ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup" {{ ($absensi->kelakuan ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="Kurang" {{ ($absensi->kelakuan ?? '') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <select name="absensi[{{ $ak->id_siswa }}][kerajinan]" class="w-full text-xs font-bold border-none bg-transparent focus:ring-0 text-center cursor-pointer text-slate-700 dark:text-slate-300">
                                    <option value="Baik" {{ ($absensi->kerajinan ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup" {{ ($absensi->kerajinan ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="Kurang" {{ ($absensi->kerajinan ?? '') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <select name="absensi[{{ $ak->id_siswa }}][kebersihan]" class="w-full text-xs font-bold border-none bg-transparent focus:ring-0 text-center cursor-pointer text-slate-700 dark:text-slate-300">
                                    <option value="Baik" {{ ($absensi->kebersihan ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup" {{ ($absensi->kebersihan ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="Kurang" {{ ($absensi->kebersihan ?? '') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden flex flex-col gap-4 p-4 bg-slate-50 dark:bg-slate-900/50">
                @foreach($students as $index => $ak)
                @php
                    $absensi = $absensiRows[$ak->id_siswa] ?? null;
                    $total = ($absensi->sakit ?? 0) + ($absensi->izin ?? 0) + ($absensi->tanpa_keterangan ?? 0);
                @endphp
                <div class="card-boss !p-0 overflow-hidden flex flex-col gap-0 shadow-sm relative">
                    <!-- Student Info Header -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-white dark:bg-[#1a2332]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-xs">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex flex-col">
                                <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-sm">{{ $ak->siswa->nama_lengkap }}</h4>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $ak->siswa->nis_lokal }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                             <span class="text-[9px] text-slate-400 uppercase font-black tracking-wider">Total</span>
                             <span class="text-xl font-black text-slate-800 dark:text-white" id="total-mob-{{ $ak->id }}">{{ $total }}</span>
                        </div>
                    </div>

                    <!-- Attendance Grid -->
                    <div class="grid grid-cols-3 divide-x divide-slate-100 dark:divide-slate-700 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex flex-col p-2 bg-primary/5">
                            <label class="text-[9px] font-bold text-primary uppercase text-center mb-1">Sakit</label>
                            <!-- NOTE: NAME ATTRIBUTE REMOVED TO PREVENT DUPLICATE SUBMISSION -->
                            <input type="number" data-sync-name="absensi[{{ $ak->id_siswa }}][sakit]" value="{{ $absensi->sakit ?? 0 }}" min="0" class="w-full text-center font-bold text-primary bg-transparent border-none focus:ring-0 p-0 text-lg abs-input-mobile" data-target="total-mob-{{ $ak->id }}">
                        </div>
                        <div class="flex flex-col p-2 bg-amber-50/50">
                            <label class="text-[9px] font-bold text-amber-600 uppercase text-center mb-1">Izin</label>
                            <input type="number" data-sync-name="absensi[{{ $ak->id_siswa }}][izin]" value="{{ $absensi->izin ?? 0 }}" min="0" class="w-full text-center font-bold text-amber-600 bg-transparent border-none focus:ring-0 p-0 text-lg abs-input-mobile" data-target="total-mob-{{ $ak->id }}">
                        </div>
                        <div class="flex flex-col p-2 bg-rose-50/50">
                            <label class="text-[9px] font-bold text-rose-600 uppercase text-center mb-1">Alpa</label>
                            <input type="number" data-sync-name="absensi[{{ $ak->id_siswa }}][alpa]" value="{{ $absensi->tanpa_keterangan ?? 0 }}" min="0" class="w-full text-center font-bold text-rose-600 bg-transparent border-none focus:ring-0 p-0 text-lg abs-input-mobile" data-target="total-mob-{{ $ak->id }}">
                        </div>
                    </div>

                    <!-- Personality Stack -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/10 grid grid-cols-1 gap-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kepribadian</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="relative">
                                <label class="block text-[9px] text-slate-400 mb-0.5 text-center">Kelakuan</label>
                                <select data-sync-name="absensi[{{ $ak->id_siswa }}][kelakuan]" class="w-full text-xs font-bold rounded-lg border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 py-1.5 pl-2 pr-6 appearance-none focus:ring-primary focus:border-primary">
                                    <option value="Baik" {{ ($absensi->kelakuan ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup" {{ ($absensi->kelakuan ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="Kurang" {{ ($absensi->kelakuan ?? '') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </div>
                            <div class="relative">
                                <label class="block text-[9px] text-slate-400 mb-0.5 text-center">Kerajinan</label>
                                <select data-sync-name="absensi[{{ $ak->id_siswa }}][kerajinan]" class="w-full text-xs font-bold rounded-lg border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 py-1.5 pl-2 pr-6 appearance-none focus:ring-primary focus:border-primary">
                                    <option value="Baik" {{ ($absensi->kerajinan ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup" {{ ($absensi->kerajinan ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="Kurang" {{ ($absensi->kerajinan ?? '') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </div>
                            <div class="relative">
                                <label class="block text-[9px] text-slate-400 mb-0.5 text-center">Kebersihan</label>
                                <select data-sync-name="absensi[{{ $ak->id_siswa }}][kebersihan]" class="w-full text-xs font-bold rounded-lg border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 py-1.5 pl-2 pr-6 appearance-none focus:ring-primary focus:border-primary">
                                    <option value="Baik" {{ ($absensi->kebersihan ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup" {{ ($absensi->kebersihan ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="Kurang" {{ ($absensi->kebersihan ?? '') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </form>
    </div>
</div>

<script>
    function setNihil() {
        Swal.fire({
            title: 'Reset Absensi?',
            text: "Yakin ingin mereset semua data absensi menjadi 0 (Nihil)? Data yang belum disimpan akan hilang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Determine visible inputs or just reset ALL Desktop inputs (Masters)
                // Resetting Masters triggers sync to Proxies
                const masterInputs = document.querySelectorAll('.abs-input');
                masterInputs.forEach(input => {
                    input.value = 0;
                    input.dispatchEvent(new Event('input', { bubbles: true })); // Trigger Logic
                });

                Swal.fire(
                    'Reset Berhasil!',
                    'Semua input telah di-set ke 0. Jangan lupa Simpan Perubahan.',
                    'success'
                )
            }
        });
    }

    // ROBUST SYNC LOGIC: Desktop (Master) <-> Mobile (Proxy)
    function initSync() {
        const desktopInputs = document.querySelectorAll('[name^="absensi["]');
        const mobileInputs = document.querySelectorAll('[data-sync-name]');

        // 1. Sync Logic for Desktop Inputs (Masters)
        desktopInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                const name = e.target.name;
                const val = e.target.value;
                // Find matching proxy
                const proxy = document.querySelector(`[data-sync-name="${name}"]`);
                if (proxy) proxy.value = val;

                // Calc Total if applicable
                if (e.target.classList.contains('abs-input')) updateTotal(e.target);
            });
            // Handle Select Change
            input.addEventListener('change', (e) => {
                 const name = e.target.name;
                 const val = e.target.value;
                 const proxy = document.querySelector(`[data-sync-name="${name}"]`);
                 if (proxy) proxy.value = val;
            });
        });

        // 2. Sync Logic for Mobile Inputs (Proxies)
        mobileInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                const name = e.target.getAttribute('data-sync-name');
                const val = e.target.value;
                // Find matching master
                const master = document.querySelector(`[name="${name}"]`);
                if (master) {
                    master.value = val;
                    // Trigger input event on Master to allow bubbling/other listeners if any (optional)
                    // master.dispatchEvent(new Event('input'));
                    // No need to dispatch if we handle totals here directly or via shared 'updateTotal'
                    if (master.classList.contains('abs-input')) updateTotal(master);
                }
            });
             input.addEventListener('change', (e) => {
                const name = e.target.getAttribute('data-sync-name');
                const val = e.target.value;
                const master = document.querySelector(`[name="${name}"]`);
                if (master) master.value = val;
            });
        });
    }

    function updateTotal(input) {
        // Calculate Total based on the CONTAINER of the input
        // Since we have Master and Proxy, calculating on Master is safer if validation needed.
        // But for UI, we need to update the total displayed next to the input.

        let container, targetId;

        // Check if Master or Proxy
        if (input.hasAttribute('data-sync-name')) {
             // It's a Proxy (Mobile)
             container = input.closest('.grid'); // Mobile Grid
             targetId = input.dataset.target; // total-mob-ID
        } else {
             // It's a Master (Desktop)
             container = input.closest('tr');
             targetId = input.dataset.target; // total-ID
        }

        if (!container) return;

        // Find Siblings
        // If master: .abs-input inside TR
        // If proxy: .abs-input-mobile inside GRID
        const selector = input.classList.contains('abs-input-mobile') ? '.abs-input-mobile' : '.abs-input';
        const siblings = container.querySelectorAll(selector);

        let total = 0;
        siblings.forEach(inp => total += parseInt(inp.value) || 0);

        // Update BOTH Total Displays (Desktop & Mobile)
        // Extract ID
        if (targetId) {
             const idPart = targetId.replace('total-', '').replace('total-mob-', '');
             const desktopTotal = document.getElementById('total-' + idPart);
             const mobileTotal = document.getElementById('total-mob-' + idPart);

             if (desktopTotal) desktopTotal.textContent = total;
             if (mobileTotal) mobileTotal.textContent = total;
        }
    }

    // Run init
    document.addEventListener('DOMContentLoaded', initSync);
</script>
@endsection
