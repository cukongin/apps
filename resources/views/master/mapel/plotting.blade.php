@extends('layouts.app')

@section('title', 'Plotting Paket Mapel')

@section('content')
<div class="flex flex-col gap-8" x-data="{
    selectedJenjang: '',
    selectedTingkat: '',
    selectedMapels: [],
    targetClasses: [],
    loading: false,

    // Magic Copy State
    sourceJenjang: '',
    sourceTingkat: '',
    copying: false,

    async fetchExisting() {
        if (!this.selectedJenjang || !this.selectedTingkat) return;

        this.loading = true;
        try {
            let url = `{{ route('master.mapel.get-plotting-data') }}?id_jenjang=${this.selectedJenjang}&tingkat_kelas=${this.selectedTingkat}`;
            let res = await fetch(url);
            let data = await res.json();

            // NEW STRUCTURE: { activeMapelIds: [...], classes: [...] }
            this.selectedMapels = data.activeMapelIds.map(String);
            this.targetClasses = data.classes;
            if (this.targetClasses.length === 0) {
                 // Optional: Show warning or empty state
            }
        } catch(e) {
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    // Data Grades
    grades: {
        1: [ // MI
            {val: '1', label: 'Kelas 1'},
            {val: '2', label: 'Kelas 2'},
            {val: '3', label: 'Kelas 3'},
            {val: '4', label: 'Kelas 4'},
            {val: '5', label: 'Kelas 5'},
            {val: '6', label: 'Kelas 6'},
        ],
        2: [ // MTS
            {val: '7', label: 'Kelas 7'},
            {val: '8', label: 'Kelas 8'},
            {val: '9', label: 'Kelas 9'},
        ],
        3: [ // TPQ
            {val: '1', label: 'Jilid/Kelas 1'},
            {val: '2', label: 'Jilid/Kelas 2'},
            {val: '3', label: 'Jilid/Kelas 3'},
            {val: '4', label: 'Jilid/Kelas 4'},
            {val: '5', label: 'Jilid/Kelas 5'},
            {val: '6', label: 'Jilid/Kelas 6'},
        ]
    },

    updateTingkatOptions() {
        this.selectedTingkat = '';
        let select = this.$refs.tingkatSelect;
        select.innerHTML = '<option value=\'\'>-- Pilih Tingkat --</option>';

        if (!this.selectedJenjang || !this.grades[this.selectedJenjang]) return;

        this.grades[this.selectedJenjang].forEach(g => {
            let opt = document.createElement('option');
            opt.value = g.val;
            opt.textContent = g.label;
            select.appendChild(opt);
        });
    },

    toggleAll(category) {
        let checkboxes = document.querySelectorAll(`input[data-category='${category}']`);
        // Check if all are currently checked
        let allChecked = Array.from(checkboxes).every(cb => this.selectedMapels.includes(cb.value));

        checkboxes.forEach(cb => {
            // Check visibility based on Jenjang before toggling!
            if(cb.closest('label').style.display === 'none') return;

            let val = cb.value;
            if (allChecked) {
                // Remove
                this.selectedMapels = this.selectedMapels.filter(id => id !== val);
            } else {
                // Add if not exists
                if (!this.selectedMapels.includes(val)) {
                    this.selectedMapels.push(val);
                }
            }
        });
    },

    isMapelVisible(target) {
        if (!this.selectedJenjang) return true; // Default: Show all if none select? Or hide? Let's show all for overview, but filter when selecting.

        if (target === 'SEMUA') return true;

        // Map IDs to Codes (Hardcoded based on Standard Seeder)
        // 1: MI, 2: MTS, 3: TPQ
        let code = '';
        if (this.selectedJenjang == 1) code = 'MI';
        if (this.selectedJenjang == 2) code = 'MTS';
        if (this.selectedJenjang == 3) code = 'TPQ';

        return target === code;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="flex flex-col gap-1 max-w-2xl">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('master.mapel.index') }}" class="hover:text-primary transition-colors">Data Mapel</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="text-slate-900 dark:text-white font-medium">Plotting Massal</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">dataset_linked</span>
                Plotting Paket Mapel
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Atur mata pelajaran untuk satu tingkat sekaligus. Perubahan akan diterapkan ke <b>SEMUA KELAS</b> di tingkat tersebut.
            </p>
        </div>
        <div>
             <button type="button" onclick="openCloneModal()" class="btn-boss btn-secondary">
                <span class="material-symbols-outlined text-[20px]">content_copy</span>
                <span>Salin ke Kelas Lain</span>
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card-boss p-6 relative min-h-[400px]">

        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 z-50 bg-white/80 dark:bg-surface-dark/80 flex flex-col gap-3 items-center justify-center rounded-2xl backdrop-blur-sm transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <span class="material-symbols-outlined animate-spin text-primary text-5xl">autorenew</span>
            <p class="text-slate-500 font-bold animate-pulse">Memuat Data...</p>
        </div>

        @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 border border-red-100 dark:border-red-800 flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        @endif

        @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl mb-6 border border-emerald-100 dark:border-emerald-800 flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        <form action="{{ route('master.mapel.save-plotting') }}" method="POST">
            @csrf

            <!-- Step 1: Target Selection -->
            <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-xl border border-slate-100 dark:border-slate-800 mb-8">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">filter_alt</span> Filter Tingkat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Jenjang Sekolah</label>
                        <select name="id_jenjang" x-model="selectedJenjang" @change="updateTingkatOptions(); fetchExisting()" required class="input-boss">
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($jenjangs as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }} ({{ $j->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Tingkat Kelas</label>
                        <select name="tingkat_kelas" x-model="selectedTingkat" x-ref="tingkatSelect" @change="fetchExisting()" required class="input-boss" :disabled="!selectedJenjang">
                            <option value="">-- Pilih Tingkat --</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Subject Selection -->
            <div x-show="selectedJenjang && selectedTingkat" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">

                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">checklist</span> Pilih Mata Pelajaran
                    </h3>
                    <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-bold" x-text="selectedMapels.length + ' Mapel Dipilih'"></span>
                </div>

                @foreach($mapels as $category => $list)
                <div class="border rounded-2xl border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-5 py-3 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            @if($category == 'AGAMA') <span class="material-symbols-outlined text-emerald-600 text-[18px]">mosque</span>
                            @elseif($category == 'MULOK') <span class="material-symbols-outlined text-amber-600 text-[18px]">local_library</span>
                            @else <span class="material-symbols-outlined text-blue-600 text-[18px]">menu_book</span>
                            @endif
                            {{ $category }}
                        </h3>
                        <button type="button" @click="toggleAll('{{ $category }}')" class="px-3 py-1 text-xs font-bold text-primary bg-primary/5 hover:bg-primary/10 rounded-lg transition-colors">
                            Pilih Semua
                        </button>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-white dark:bg-surface-dark">
                        @foreach($list as $mapel)
                        <label x-show="isMapelVisible('{{ $mapel->target_jenjang }}')"
                               class="group relative flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 hover:border-primary/30 dark:border-slate-800 dark:hover:bg-slate-800/50 cursor-pointer transition-all">
                            <input type="checkbox" name="mapel_ids[]" value="{{ $mapel->id }}" x-model="selectedMapels" data-category="{{ $category }}"
                                   class="mt-1 w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary transition-all">
                            <div class="flex-1">
                                <div class="font-bold text-sm text-slate-800 dark:text-white group-hover:text-primary transition-colors">
                                    {{ $mapel->nama_mapel }}
                                </div>
                                @if($mapel->nama_kitab)
                                    <div class="font-arabic text-slate-500 dark:text-slate-400 text-xs mt-1">{{ $mapel->nama_kitab }}</div>
                                @endif
                                <div class="flex gap-2 mt-2">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-100 dark:bg-slate-700 inline-block px-1.5 py-0.5 rounded">
                                        {{ $mapel->kode_mapel }}
                                    </span>
                                    <span class="text-[10px] uppercase font-bold text-white px-1.5 py-0.5 rounded"
                                          :class="{
                                              'bg-emerald-500': '{{ $mapel->target_jenjang }}' === 'MI',
                                              'bg-blue-500': '{{ $mapel->target_jenjang }}' === 'MTS',
                                              'bg-slate-400': '{{ $mapel->target_jenjang }}' === 'SEMUA'
                                          }">
                                        {{ $mapel->target_jenjang }}
                                    </span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <!-- Static Action Bar -->
                <div class="mt-8 flex justify-end gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <a href="{{ route('master.mapel.index') }}" class="btn-boss btn-secondary">Batal</a>
                    <button type="submit" class="btn-boss btn-primary px-8">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            <!-- Empty State Helper -->
            <div x-show="!selectedJenjang || !selectedTingkat" class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-800/30">
                 <div class="p-4 bg-white dark:bg-slate-800 rounded-full shadow-sm mb-4">
                     <span class="material-symbols-outlined text-4xl text-slate-300">touch_app</span>
                 </div>
                 <h3 class="text-lg font-bold text-slate-900 dark:text-white">Belum ada Tingkat Dipilih</h3>
                 <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs mx-auto">Silakan pilih Jenjang dan Tingkat Kelas di bagian atas untuk mulai mengatur mapel.</p>
            </div>
        </form>
    </div>
</div>

<!-- Clone Modal -->
<div id="cloneModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeCloneModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark text-left shadow-2xl transition-all sm:w-full sm:max-w-2xl border border-slate-100 dark:border-slate-800" x-data="{
                sourceJenjang: '',
                sourceTingkat: ''
            }">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white/50 dark:bg-surface-dark/50 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-primary/10 rounded-lg text-primary">
                            <span class="material-symbols-outlined">content_copy</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Salin Plotting Massal</h3>
                    </div>
                    <button onclick="closeCloneModal()" class="size-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form action="{{ route('master.mapel.copy-plotting') }}" method="POST" class="p-6"
                      data-confirm-delete="true"
                      data-title="Salin Plotting?"
                      data-message="Data mapel di kelas target akan DIHAPUS dan digantikan dengan data dari sumber."
                      data-confirm-text="Ya, Timpa Data"
                      data-confirm-color="#f59e0b"
                      data-icon="warning">
                    @csrf
                    <p class="text-sm text-slate-500 mb-6 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                        Fitur ini akan menyalin daftar mapel dari satu tingkat (Sumber) ke tingkat lain (Target). <br>
                        <span class="font-bold text-red-500">PERHATIAN: Setting mapel di target akan ditimpa!</span>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                        <!-- Arrow Connector (Desktop) -->
                        <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-10 bg-white dark:bg-surface-dark p-1 rounded-full border border-slate-200 dark:border-slate-600 shadow-sm text-slate-400">
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </div>

                        <!-- Source -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500 text-[18px]">input</span> Sumber Data
                            </h4>
                            <div class="space-y-3 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Jenjang</label>
                                    <select name="source_jenjang" x-model="sourceJenjang" class="input-boss text-xs">
                                        <option value="">-- Pilih --</option>
                                        @foreach($jenjangs as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Tingkat</label>
                                    <select name="source_tingkat" x-model="sourceTingkat" class="input-boss text-xs" :disabled="!sourceJenjang">
                                        <option value="">-- Pilih --</option>
                                        <template x-if="sourceJenjang == 1">
                                            <optgroup label="Tingkat MI">
                                                <option value="1">Kelas 1</option>
                                                <option value="2">Kelas 2</option>
                                                <option value="3">Kelas 3</option>
                                                <option value="4">Kelas 4</option>
                                                <option value="5">Kelas 5</option>
                                                <option value="6">Kelas 6</option>
                                            </optgroup>
                                        </template>
                                        <template x-if="sourceJenjang == 2">
                                            <optgroup label="Tingkat MTS">
                                                <option value="7">Kelas 7</option>
                                                <option value="8">Kelas 8</option>
                                                <option value="9">Kelas 9</option>
                                            </optgroup>
                                        </template>
                                        <template x-if="sourceJenjang == 3">
                                           <optgroup label="Tingkat MA">
                                               <option value="10">Kelas 10</option>
                                               <option value="11">Kelas 11</option>
                                               <option value="12">Kelas 12</option>
                                           </optgroup>
                                       </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Target -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[18px]">output</span> Target Copy
                            </h4>
                            <div class="bg-primary/5 dark:bg-primary/20 p-4 rounded-xl border border-primary/10 dark:border-primary/30 h-[200px] overflow-y-auto custom-scrollbar">
                                <div class="grid grid-cols-2 gap-x-4 gap-y-6">
                                    <!-- MI Targets -->
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">JENJANG MI</p>
                                        @for($i=1; $i<=6; $i++)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" name="targets[]" value="1-{{ $i }}" class="rounded text-primary focus:ring-primary w-4 h-4 border-slate-300">
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors">Kelas {{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                    <!-- MTS Targets -->
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">JENJANG MTS</p>
                                        @for($i=7; $i<=9; $i++)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" name="targets[]" value="2-{{ $i }}" class="rounded text-primary focus:ring-primary w-4 h-4 border-slate-300">
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors">Kelas {{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                    <!-- MA Targets -->
                                    <div class="space-y-1 col-span-2 mt-2">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">JENJANG MA</p>
                                        <div class="grid grid-cols-2 gap-4">
                                            @for($i=10; $i<=12; $i++)
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="checkbox" name="targets[]" value="3-{{ $i }}" class="rounded text-primary focus:ring-primary w-4 h-4 border-slate-300">
                                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors">Kelas {{ $i }}</span>
                                            </label>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" onclick="closeCloneModal()" class="btn-boss btn-secondary">Batal</button>
                        <button type="submit" class="btn-boss btn-primary shadow-lg shadow-primary/20">
                            Terapkan Plotting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCloneModal() {
        document.getElementById('cloneModal').classList.remove('hidden');
    }

    function closeCloneModal() {
        document.getElementById('cloneModal').classList.add('hidden');
    }
</script>
@endpush
