@extends('layouts.app')

@section('title', 'Smart Katrol Nilai')

@section('content')
<div class="flex flex-col gap-6" x-data="katrolSimulation()">
    <!-- Header -->
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
             <div class="flex items-center gap-2 text-sm font-bold text-slate-500 mb-2">
                <a href="{{ route('walikelas.monitoring') }}" class="hover:text-primary transition-colors">Monitoring Nilai</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-primary">Smart Katrol</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">upgrade</span>
                Smart Katrol Nilai
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
                Simulasi dan sesuaikan nilai siswa sebelum disimpan ke rapor.
            </p>
        </div>

        <!-- Filter Context -->
        <form method="GET" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
             <div class="relative group w-full md:w-auto">
                <select name="kelas_id" onchange="this.form.submit()" class="input-boss !pl-9 !pr-8 w-full md:min-w-[150px] !text-xs">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $kelasId == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[16px]">class</span>
                </div>
            </div>

            <div class="relative group w-full md:w-auto">
                <select name="periode_id" onchange="this.form.submit()" class="input-boss !pl-9 !pr-8 w-full md:min-w-[150px] !text-xs">
                    @foreach($allPeriods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodeId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_periode }} {{ $p->status == 'aktif' ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                </div>
            </div>

            <div class="relative group w-full md:w-auto">
                <select name="mapel_id" onchange="this.form.submit()" class="input-boss !pl-9 !pr-8 w-full md:min-w-[200px] !text-xs">
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ $mapelId == $s->id ? 'selected' : '' }}>{{ $s->nama_mapel }}</option>
                    @endforeach
                </select>
                 <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 group-hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[16px]">menu_book</span>
                </div>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-2 shadow-sm" role="alert">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- CONTROL PANEL (Left) -->
        <div class="lg:col-span-1 space-y-6">
            <form action="{{ route('walikelas.katrol.store') }}" method="POST" class="card-boss !p-6 sticky top-6">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                <input type="hidden" name="mapel_id" value="{{ $mapelId }}">
                <input type="hidden" name="periode_id" value="{{ $selectedPeriodeId }}">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-black text-lg text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">tune</span>
                        Konfigurasi
                    </h3>
                    <div class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 py-1 px-3 rounded-lg border border-slate-200 dark:border-slate-600 text-xs font-mono font-bold">
                        KKM: <span class="text-slate-900 dark:text-white">{{ $currentKkm }}</span>
                    </div>
                </div>

                <!-- Mode Selection -->
                <div class="space-y-3 mb-6">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Metode Katrol</label>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer group">
                            <input type="radio" name="method_type_ui" value="kkm" class="peer sr-only" x-model="staging.mode">
                            <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-600 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all hover:bg-slate-50 dark:hover:bg-slate-700 group-hover:border-slate-300">
                                <span class="material-symbols-outlined block mb-1 text-2xl">vertical_align_bottom</span>
                                <span class="text-[10px] font-black uppercase">Standard KKM</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="method_type_ui" value="points" class="peer sr-only" x-model="staging.mode">
                            <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-600 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all hover:bg-slate-50 dark:hover:bg-slate-700 group-hover:border-slate-300">
                                <span class="material-symbols-outlined block mb-1 text-2xl">add</span>
                                <span class="text-[10px] font-black uppercase">Tambah Poin</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="method_type_ui" value="linear_scale" class="peer sr-only" x-model="staging.mode">
                            <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-600 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all hover:bg-slate-50 dark:hover:bg-slate-700 group-hover:border-slate-300">
                                <span class="material-symbols-outlined block mb-1 text-2xl">linear_scale</span>
                                <span class="text-[10px] font-black uppercase">Interpolasi</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="method_type_ui" value="percentage" class="peer sr-only" x-model="staging.mode">
                            <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-600 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all hover:bg-slate-50 dark:hover:bg-slate-700 group-hover:border-slate-300">
                                <span class="material-symbols-outlined block mb-1 text-2xl">percent</span>
                                <span class="text-[10px] font-black uppercase">Persentase</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Dynamic Controls -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 mb-6 border border-slate-100 dark:border-slate-700/50 min-h-[160px]">

                    <!-- KKM Mode -->
                    <div x-show="staging.mode === 'kkm'" x-transition>
                        <div class="flex justify-between items-center mb-2">
                             <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Target Minimal (KKM)</label>
                             <span class="text-lg font-black text-primary" x-text="staging.kkmVal"></span>
                        </div>
                         <p class="text-[10px] text-slate-500 mb-4 bg-white dark:bg-slate-700 p-2 rounded-lg border border-slate-100 dark:border-slate-600">
                            Semua nilai di bawah KKM akan diubah menjadi sama dengan KKM. Nilai di atas KKM tetap.
                        </p>
                        <input type="range" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary" min="0" max="100" x-model.number="staging.kkmVal">
                    </div>

                    <!-- Points Mode -->
                    <div x-show="staging.mode === 'points'" x-transition style="display: none;">
                        <div class="flex justify-between items-center mb-2">
                             <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Tambah Poin (+)</label>
                             <span class="text-lg font-black text-primary" x-text="'+' + staging.boostPoints"></span>
                        </div>
                        <p class="text-[10px] text-slate-500 mb-4 bg-white dark:bg-slate-700 p-2 rounded-lg border border-slate-100 dark:border-slate-600">
                            Menambahkan poin tetap ke setiap nilai siswa.
                        </p>
                        <input type="range" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary mb-4" min="0" max="50" x-model.number="staging.boostPoints">

                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Batas Atas (Max)</label>
                        <input type="number" class="input-boss w-full" min="0" max="100" x-model.number="staging.maxCeiling">
                    </div>

                    <!-- Percent Mode -->
                    <div x-show="staging.mode === 'percentage'" x-transition style="display: none;">
                        <div class="flex justify-between items-center mb-2">
                             <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Naikkan Persentase (%)</label>
                             <span class="text-lg font-black text-primary" x-text="staging.boostPercent + '%'"></span>
                        </div>
                        <p class="text-[10px] text-slate-500 mb-4 bg-white dark:bg-slate-700 p-2 rounded-lg border border-slate-100 dark:border-slate-600">
                            Menaikkan nilai sebesar persentase dari nilai asli.
                        </p>
                        <input type="range" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary" min="0" max="50" x-model.number="staging.boostPercent">
                    </div>

                    <!-- Linear Scale Mode -->
                    <div x-show="staging.mode === 'linear_scale'" x-transition style="display: none;">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Range Target Baru</label>
                        <p class="text-[10px] text-slate-500 mb-4 bg-white dark:bg-slate-700 p-2 rounded-lg border border-slate-100 dark:border-slate-600">
                            Memetakan ulang jangkauan nilai terendah dan tertinggi ke range baru.
                        </p>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="text-[10px] text-slate-500 font-bold mb-1 block">Min Target</label>
                                <input type="number" class="input-boss w-full text-center" min="0" max="100" x-model.number="staging.targetMin">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 font-bold mb-1 block">Max Target</label>
                                <input type="number" class="input-boss w-full text-center" min="0" max="100" x-model.number="staging.targetMax">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 opacity-60">
                             <div>
                                <label class="text-[10px] text-slate-500 font-bold mb-1 block">Data Min (Asli)</label>
                                <input type="number" class="input-boss w-full text-center bg-slate-100" x-model.number="staging.dataMin" readonly>
                            </div>
                             <div>
                                <label class="text-[10px] text-slate-500 font-bold mb-1 block">Data Max (Asli)</label>
                                <input type="number" class="input-boss w-full text-center bg-slate-100" x-model.number="staging.dataMax" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden Inputs -->
                <input type="hidden" name="method_type" :value="staging.mode">
                <input type="hidden" name="min_threshold" :value="staging.kkmVal">
                <input type="hidden" name="boost_points" :value="staging.boostPoints">
                <input type="hidden" name="max_ceiling" :value="staging.maxCeiling">
                <input type="hidden" name="boost_percent" :value="staging.boostPercent">
                <input type="hidden" name="target_min" :value="staging.targetMin">
                <input type="hidden" name="target_max" :value="staging.targetMax">
                <input type="hidden" name="data_min" :value="staging.dataMin">
                <input type="hidden" name="data_max" :value="staging.dataMax">

                <!-- Live Stats -->
                <div class="space-y-3 mb-6 bg-white dark:bg-slate-700/30 rounded-xl p-4 border border-slate-100 dark:border-slate-700">
                    <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                        <span class="text-xs font-bold uppercase">Rata-rata Awal</span>
                        <span class="font-black text-slate-800 dark:text-white">{{ round($grades->avg('nilai_akhir_asli') ?? $grades->avg('nilai_akhir') ?? 0, 1) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-primary">
                        <span class="text-xs font-bold uppercase">Rata-rata Baru (Preview)</span>
                        <span class="font-black text-lg" x-text="simAuth.avg">0</span>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" @click="applyPreview()" class="btn-boss bg-indigo-600 hover:bg-indigo-700 text-white w-full !py-3 flex justify-center items-center gap-2 shadow-lg shadow-indigo-500/30">
                        <span class="material-symbols-outlined">play_circle</span>
                        <span>Hitung / Preview</span>
                    </button>

                    <div class="flex gap-2">
                        <button type="submit" class="btn-boss bg-emerald-600 hover:bg-emerald-700 text-white flex-1 !py-2.5 flex justify-center items-center gap-2 shadow-lg shadow-emerald-600/20">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan Perubahan</span>
                        </button>
                         <button type="submit" name="method_type" value="reset" class="btn-boss bg-slate-100 hover:bg-slate-200 text-slate-600 !py-2.5 !px-3 shadow-none border border-slate-200" title="Reset ke Nilai Asli">
                             <span class="material-symbols-outlined">restart_alt</span>
                         </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- SIMULATION TABLE (Right) -->
        <div class="lg:col-span-2">
            <div class="card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 flex flex-col h-full max-h-[800px]">
                <div class="overflow-x-auto overflow-y-auto flex-1 custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-xs font-bold sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="p-4 w-10 text-center">No</th>
                                <th class="p-4">Nama Siswa</th>
                                <th class="p-4 text-center">Asli</th>
                                <th class="p-4 text-center">Baru</th>
                                <th class="p-4 text-center w-24">Selisih</th>
                                <th class="p-4">Visualisasi Perubahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-[#1a2332]">
                            @foreach($grades as $index => $grade)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group"
                                x-data="{
                                    original: {{ $grade->nilai_akhir_asli ?? $grade->nilai_akhir ?? 0 }},
                                    current: {{ $grade->nilai_akhir ?? 0 }},
                                    note: '{{ $grade->katrol_note ?? '' }}',
                                    get calculated() {
                                        return this.hasPreviewed ? this.calculateGrade(this.original) : this.current;
                                    },
                                    get diff() {
                                        return this.calculated - this.original;
                                    },
                                    get isModified() {
                                        return this.diff !== 0;
                                    }
                                }"
                            >
                                <td class="p-4 text-slate-400 font-bold text-center">{{ $index+1 }}</td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $grade->siswa->nama_lengkap }}</div>
                                    <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $grade->siswa->nis_lokal }}</div>
                                </td>

                                <!-- Nilai Asli -->
                                <td class="p-4 text-center">
                                    <span class="font-bold font-mono text-base" :class="original < {{ $currentKkm }} ? 'text-amber-500' : 'text-slate-400'">
                                        <span x-text="original"></span>
                                    </span>
                                </td>

                                <!-- Nilai Baru -->
                                <td class="p-4 text-center">
                                    <template x-if="!isModified">
                                        <span class="text-slate-300 font-bold">-</span>
                                    </template>
                                    <template x-if="isModified">
                                        <span class="font-black font-mono text-xl"
                                              :class="calculated < {{ $currentKkm }} ? 'text-rose-500' : (diff > 0 ? 'text-emerald-500' : 'text-slate-800 dark:text-white')"
                                              x-text="calculated"></span>
                                    </template>
                                </td>

                                <!-- Selisih -->
                                <td class="p-4 text-center">
                                    <template x-if="isModified">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black shadow-sm"
                                              :class="diff > 0 ? 'bg-emerald-100 text-emerald-700' : (diff < 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600')"
                                              x-text="diff > 0 ? '+' + diff : diff">
                                        </span>
                                    </template>
                                    <template x-if="!isModified">
                                        <span class="text-slate-300 text-xs font-bold">-</span>
                                    </template>
                                </td>

                                <!-- Visual -->
                                <td class="p-4 align-middle w-1/3 min-w-[200px]">
                                    <div class="flex flex-col gap-1">
                                        <div class="h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden relative shadow-inner">
                                            <!-- Original Marker -->
                                            <div class="absolute h-full transition-all duration-300 rounded-full opacity-50"
                                                 :class="original < {{ $currentKkm }} ? 'bg-amber-400' : 'bg-slate-400'"
                                                 :style="`width: ${original}%`"></div>

                                            <!-- New Marker (Shows if modified) -->
                                            <div class="absolute h-full transition-all duration-500 ease-out rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"
                                                 :class="calculated < {{ $currentKkm }} ? 'bg-rose-500' : 'bg-emerald-500'"
                                                 :style="`width: ${calculated}%`"
                                                 x-show="isModified"></div>
                                        </div>

                                        <!-- Note Label underneath -->
                                        <div class="flex justify-between text-[9px] font-bold uppercase text-slate-400 tracking-wider">
                                            <span>0</span>
                                            <span x-show="note && !hasPreviewed" x-text="note" class="text-slate-500 bg-slate-100 px-1 rounded"></span>
                                            <span x-show="hasPreviewed && isModified" class="text-emerald-500">Preview</span>
                                            <span>100</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ALPINE LOGIC -->
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function katrolSimulation() {
        return {
            // State Flags
            hasPreviewed: false,

            // Staging (Input State)
            staging: {
                mode: 'kkm',
                kkmVal: {{ $currentKkm }},
                boostPoints: 5,
                boostPercent: 10,
                maxCeiling: 100,
                targetMin: {{ $currentKkm }},
                targetMax: 95,
                dataMin: 0,
                dataMax: 100
            },

            // Active (Calculation State) - Initially synced
            active: {
                mode: 'kkm',
                kkmVal: {{ $currentKkm }},
                boostPoints: 5,
                boostPercent: 10,
                maxCeiling: 100,
                targetMin: {{ $currentKkm }},
                targetMax: 95,
                dataMin: 0,
                dataMax: 100
            },

            simAuth: { avg: 0, count: 0 },

            // Raw Grades for calculations
            rawGrades: @json($grades->map(fn($g) => (float) ($g->nilai_akhir_asli ?? $g->nilai_akhir ?? 0))),

            init() {
                // Determine initial stats
                if (this.rawGrades.length > 0) {
                    let min = Math.min(...this.rawGrades);
                    let max = Math.max(...this.rawGrades);

                    // Set both Staging and Active
                    this.staging.dataMin = min;
                    this.staging.dataMax = max;
                    this.active.dataMin = min;
                    this.active.dataMax = max;
                }

                // Initial calculation based on default
                this.updateStats();
            },

            // ACTION: PREVIEW
            applyPreview() {
                // Enable Preview Flag
                this.hasPreviewed = true;

                // Copy Staging to Active
                this.active = { ...this.staging };
                this.updateStats();
            },

            // Compute Grade using ACTIVE config
            calculateGrade(original) {
                // If not previewed yet, show original
                if (!this.hasPreviewed) return parseFloat(original);

                let final = original;
                original = parseFloat(original);

                const cfg = this.active; // Use Active Config

                if (cfg.mode === 'kkm') {
                    if (original < cfg.kkmVal) final = cfg.kkmVal;

                } else if (cfg.mode === 'points') {
                    if (original < cfg.maxCeiling) {
                        final = Math.min(cfg.maxCeiling, original + cfg.boostPoints);
                    }

                } else if (cfg.mode === 'percentage') {
                    let factor = 1 + (cfg.boostPercent / 100);
                    final = Math.min(100, Math.round(original * factor));

                } else if (cfg.mode === 'linear_scale') {
                    if (cfg.dataMax === cfg.dataMin) {
                        final = cfg.targetMax;
                    } else {
                        let ratio = (original - cfg.dataMin) / (cfg.dataMax - cfg.dataMin);
                        let range = cfg.targetMax - cfg.targetMin;
                        final = cfg.targetMin + (ratio * range);
                        final = Math.min(100, Math.round(final));
                    }
                    // Prevent Downgrade: If calculated is lower than original, keep original
                    final = Math.max(final, original);
                }
                return isNaN(final) ? original : final;
            },

            // Recalculate Totals
            updateStats() {
                let total = 0;
                let count = 0;
                let changed = 0;

                this.rawGrades.forEach(g => {
                    let newG = this.calculateGrade(g);
                    total += newG;
                    count++;
                    if (newG !== g) changed++;
                });

                this.simAuth.avg = count > 0 ? (total / count).toFixed(1) : 0;
                this.simAuth.count = changed;
            }
        }
    }
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection
