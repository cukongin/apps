@extends('layouts.app')

@section('title', 'Monitoring Guru')

@section('content')
<div class="flex flex-col space-y-6">
    <!-- Page Heading & Actions -->
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-4xl text-primary">analytics</span>
                Monitoring Input Nilai
            </h1>
            @if($kelas)
            <p class="text-slate-500 dark:text-slate-400 text-base font-medium">Pantau progres input nilai Guru Mata Pelajaran untuk Kelas <span class="bg-primary/10 text-primary px-2 py-0.5 rounded font-bold">{{ $kelas->nama_kelas }}</span>.</p>
            @else
            <p class="text-slate-500 dark:text-slate-400 text-base">Pilih filter untuk menampilkan data perwalian.</p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <!-- Filter Form -->
            <form method="GET" class="flex flex-wrap gap-2 bg-slate-50 dark:bg-slate-800 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                <!-- Jenjang Toggle Buttons -->
                <div class="flex bg-slate-200 dark:bg-slate-700 p-1 rounded-lg" x-data="{ jenjang: '{{ request('jenjang') }}' }">
                    <input type="hidden" name="jenjang" :value="jenjang">

                    <button type="button" @click="jenjang = 'MI'; $nextTick(() => $el.closest('form').submit())" class="px-3 py-1.5 text-xs font-bold rounded-md transition-all" :class="jenjang === 'MI' ? 'bg-white dark:bg-slate-800 text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'">
                        MI
                    </button>
                    <button type="button" @click="jenjang = 'MTS'; $nextTick(() => $el.closest('form').submit())" class="px-3 py-1.5 text-xs font-bold rounded-md transition-all" :class="jenjang === 'MTS' ? 'bg-white dark:bg-slate-800 text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'">
                        MTS
                    </button>
                </div>
                @if($kelas)
                <div class="w-px h-6 bg-slate-300 dark:bg-slate-600 my-auto mx-1"></div>
                <div class="relative group">
                    <select name="kelas_id" class="appearance-none bg-transparent pl-2 pr-8 py-1.5 text-sm font-bold text-slate-700 dark:text-white rounded-lg focus:outline-none cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition" onchange="this.form.submit()">
                        @foreach($allClasses as $c)
                            <option value="{{ $c->id }}" {{ $kelas->id == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                     <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>
                @endif
            </form>

            @if($kelas)
            <div class="flex gap-2">
                <a href="{{ route('grade.import.index', $kelas->id) }}" class="btn-boss btn-primary flex items-center gap-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                    <span class="hidden sm:inline">Import Kolektif</span>
                </a>
                <button class="btn-boss bg-amber-500 hover:bg-amber-600 text-white border-transparent shadow-lg shadow-amber-500/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">notifications_active</span>
                    <span class="hidden sm:inline">Ingatkan Semua</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    @if(!$kelas)
    <!-- EMPTY STATE -->
    <div class="flex flex-col items-center justify-center py-20 card-boss border-dashed !bg-slate-50/50 dark:!bg-slate-800/50">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-full mb-4 shadow-sm animate-bounce">
            <span class="material-symbols-outlined text-4xl text-slate-400">school</span>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tidak Ada Data Kelas</h3>
        <p class="text-slate-500 dark:text-slate-400 text-center max-w-sm mt-2">
            Belum ada kelas perwalian yang ditemukan untuk filter jenjang
            <span class="font-bold text-primary bg-primary/10 px-2 py-0.5 rounded">{{ request('jenjang') ?? 'ini' }}</span>.
        </p>
    </div>
    @else
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Total -->
        <div class="card-boss !p-6 flex flex-col justify-between h-36 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute -right-4 -top-4 opacity-10 rotate-12 group-hover:scale-110 transition-transform duration-500 bg-slate-900/5 dark:bg-white/5 rounded-full p-4">
                <span class="material-symbols-outlined text-[100px] text-slate-800 dark:text-white">library_books</span>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-xs">Total Mapel</p>
            <div class="flex items-baseline gap-2 relative z-10">
                <h3 class="text-5xl font-black text-slate-900 dark:text-white">{{ count($monitoringData) }}</h3>
                <span class="text-sm text-slate-500 dark:text-slate-500 font-bold">Mata Pelajaran</span>
            </div>
        </div>

        <!-- Card 2: Selesai -->
        <div class="card-boss !p-6 flex flex-col justify-between h-36 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300 !border-primary/20">
            <div class="absolute -right-4 -top-4 opacity-10 text-primary rotate-12 group-hover:scale-110 transition-transform duration-500 bg-primary/10 rounded-full p-4">
                <span class="material-symbols-outlined text-[100px]">check_circle</span>
            </div>
            <p class="text-primary font-bold uppercase tracking-wider text-xs">Sudah Selesai</p>
            <div class="flex items-baseline gap-2 relative z-10">
                <h3 class="text-5xl font-black text-primary">{{ $finishedCount }}</h3>
                <span class="text-sm text-primary/80 font-bold">Guru Mapel</span>
            </div>
        </div>

        <!-- Card 3: Belum (Pending) -->
        <div class="card-boss !p-6 flex flex-col justify-between h-36 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300 !border-orange-200 dark:!border-orange-900">
             <div class="absolute -right-4 -top-4 opacity-10 text-orange-500 rotate-12 group-hover:scale-110 transition-transform duration-500 bg-orange-100 dark:bg-orange-900 rounded-full p-4">
                <span class="material-symbols-outlined text-[100px]">pending</span>
            </div>
            <p class="text-orange-500 dark:text-orange-400 font-bold uppercase tracking-wider text-xs">Belum Selesai</p>
            <div class="flex items-baseline gap-2 relative z-10">
                <h3 class="text-5xl font-black text-orange-500">{{ $notStartedCount }}</h3>
                <span class="text-sm text-orange-500/80 font-bold">Perlu diingatkan</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card-boss !p-4 flex flex-col sm:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-slate-400">search</span>
            </div>
            <input class="input-boss w-full !pl-10 !py-2.5" placeholder="Cari Guru atau Mata Pelajaran..." type="text"/>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 font-bold text-sm shadow-sm">
                <span class="material-symbols-outlined text-[20px] mr-2 text-slate-500">filter_list</span>
                Filter Status
            </button>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800" id="monitoringTable">
                <thead class="bg-slate-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50 dark:bg-slate-800 sticky left-0 z-10 w-16 text-center" scope="col">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider" scope="col">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider" scope="col">Guru Pengampu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/4" scope="col">Status Progres</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider" scope="col">Analisa Nilai</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider" scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-[#1a2332]">
                    @forelse($monitoringData as $index => $data)
                        @php
                            $isDone = $data->progress >= 100;
                            $inProgress = $data->progress > 0 && !$isDone;

                            $badgeClass = $isDone
                                ? 'bg-primary/10 text-primary border-primary/20'
                                : ($inProgress ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-rose-50 text-rose-700 border-rose-200');

                            $barClass = $isDone ? 'bg-primary' : ($inProgress ? 'bg-orange-500' : 'bg-rose-500');

                            // Data Attribute for filtering
                            $statusFilter = $isDone ? 'finished' : 'pending';
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors search-item group" data-status="{{ $statusFilter }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-bold bg-white dark:bg-[#1a2332] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50 sticky left-0 z-10 text-center">{{ sprintf('%02d', $index + 1) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl {{ $isDone ? 'bg-primary/20 text-primary' : ($inProgress ? 'bg-orange-100 text-orange-600' : 'bg-rose-100 text-rose-600') }} flex items-center justify-center shadow-sm">
                                        <span class="material-symbols-outlined text-[20px]">
                                            {{ $isDone ? 'check_circle' : ($inProgress ? 'calculate' : 'pending') }}
                                        </span>
                                    </div>
                                    <div class="font-bold text-slate-900 dark:text-white search-text text-base">{{ $data->nama_mapel }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-black text-slate-500 border border-slate-200 dark:border-slate-600">
                                        {{ substr($data->nama_guru, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-medium text-slate-600 dark:text-slate-300 search-text">{{ $data->nama_guru }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex justify-between mb-2 items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold border {{ $badgeClass }}">
                                        {{ $data->status_label }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500">{{ round($data->progress) }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden border border-slate-200 dark:border-slate-600">
                                    <div class="{{ $barClass }} h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $data->progress > 5 ? $data->progress : 5 }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($data->katrol_status === 'Perlu Katrol')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800 animate-pulse">
                                        <span class="material-symbols-outlined text-[16px]">warning</span> Perlu Katrol (Min: {{ $data->min_score }})
                                    </span>
                                @elseif($data->katrol_status === 'Aman')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800">
                                        <span class="material-symbols-outlined text-[16px]">check_small</span> Aman
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @if($isDone)
                                    <button type="button" class="text-slate-300 cursor-not-allowed p-2 rounded-full hover:bg-slate-50 transition" title="Fitur Katrol Nilai (Segera Hadir)">
                                        <span class="material-symbols-outlined">visibility_off</span>
                                    </button>
                                @else
                                    <button class="group inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/5 text-primary hover:bg-primary hover:text-white rounded-lg transition-all text-xs font-bold border border-primary/20 hover:border-primary shadow-sm">
                                        <span class="material-symbols-outlined text-[16px] group-hover:animate-swing">notifications</span>
                                        Ingatkan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="inline-block p-4 rounded-full bg-slate-50 dark:bg-slate-800 mb-2">
                                    <span class="material-symbols-outlined text-4xl text-slate-300">assignment_late</span>
                                </div>
                                <p class="font-bold text-slate-600 dark:text-slate-400">Tidak ada data mata pelajaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Menampilkan <span class="font-bold text-slate-900 dark:text-white" id="startShow">0</span> sampai <span class="font-bold text-slate-900 dark:text-white" id="endShow">0</span> dari <span class="font-bold text-slate-900 dark:text-white" id="totalShow">0</span> hasil
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-xl shadow-sm -space-x-px" aria-label="Pagination" id="paginationControls">
                        <!-- Controls generated by JS -->
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[placeholder="Cari Guru atau Mata Pelajaran..."]');
    const filterBtn = document.querySelector('button.border-slate-200');
    const rows = Array.from(document.querySelectorAll('.search-item')); // Convert to Array

    // UI Elements
    const elStart = document.getElementById('startShow');
    const elEnd = document.getElementById('endShow');
    const elTotal = document.getElementById('totalShow');
    const elControls = document.getElementById('paginationControls');

    // State
    const state = {
        filter: 'all',
        search: '',
        page: 1,
        limit: 10
    };

    function init() {
        if (!searchInput) return;

        // Event Listeners
        searchInput.addEventListener('input', (e) => {
            state.search = e.target.value.toLowerCase();
            state.page = 1; // Reset to page 1 on search
            render();
        });

        if (filterBtn) {
            filterBtn.addEventListener('click', () => {
                 // Cycle Filter
                 if (state.filter === 'all') {
                    state.filter = 'pending';
                    filterBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] mr-2 text-orange-500">pending</span> Belum Selesai';
                    filterBtn.classList.replace('bg-white', 'bg-orange-50');
                    filterBtn.classList.replace('border-slate-200', 'border-orange-200');
                    filterBtn.classList.replace('text-slate-700', 'text-orange-700');
                } else if (state.filter === 'pending') {
                    state.filter = 'finished';
                    filterBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] mr-2 text-primary">check_circle</span> Selesai';
                    filterBtn.classList.replace('bg-orange-50', 'bg-green-50');
                    filterBtn.classList.replace('border-orange-200', 'border-green-200');
                    filterBtn.classList.replace('text-orange-700', 'text-green-700');
                } else {
                    state.filter = 'all';
                    filterBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] mr-2 text-slate-500">filter_list</span> Filter Status';
                    filterBtn.classList.replace('bg-green-50', 'bg-white');
                    filterBtn.classList.replace('border-green-200', 'border-slate-200');
                    filterBtn.classList.replace('text-green-700', 'text-slate-700');
                }
                state.page = 1; // Reset page
                render();
            });
        }

        render();
    }

    function render() {
        // 1. Filter Data
        const filteredRows = rows.filter(row => {
            const mapel = row.querySelector('.search-text').innerText.toLowerCase();
            const guruElement = row.querySelectorAll('.search-text')[1];
            const guru = guruElement ? guruElement.innerText.toLowerCase() : '';
            const status = row.getAttribute('data-status');

            const matchesSearch = mapel.includes(state.search) || guru.includes(state.search);
            let matchesFilter = true;
            if (state.filter === 'pending') matchesFilter = status === 'pending';
            if (state.filter === 'finished') matchesFilter = status === 'finished';

            return matchesSearch && matchesFilter;
        });

        // 2. Paginate Data
        const total = filteredRows.length;
        const totalPages = Math.ceil(total / state.limit);
        const start = (state.page - 1) * state.limit;
        const end = start + state.limit;
        const pagedRows = filteredRows.slice(start, end);

        // 3. Update Table
        rows.forEach(r => r.style.display = 'none'); // Hide all first
        let indexCounter = start + 1;
        pagedRows.forEach(row => {
            row.style.display = '';
            // Update "No" column number
            row.querySelector('.px-6').innerText = String(indexCounter++).padStart(2, '0');
        });

        // 4. Update Stats
        if(elStart) elStart.innerText = total > 0 ? start + 1 : 0;
        if(elEnd) elEnd.innerText = Math.min(end, total);
        if(elTotal) elTotal.innerText = total;

        // 5. Render Pagination Controls
        if(elControls) renderControls(totalPages);
    }

    function renderControls(totalPages) {
        let html = '';

        // Previous
        const prevDisabled = state.page === 1;
        html += `<button onclick="changePage(${state.page - 1})" ${prevDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium ${prevDisabled ? 'text-slate-300 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}">
                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                 </button>`;

        // Numbered Pages
        for (let i = 1; i <= totalPages; i++) {
            const isActive = i === state.page;
            if (isActive) {
                html += `<button aria-current="page" class="z-10 bg-primary text-white border-primary relative inline-flex items-center px-4 py-2 border text-sm font-bold shadow-md">${i}</button>`;
            } else {
                html += `<button onclick="changePage(${i})" class="bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors">${i}</button>`;
            }
        }

        // Next
        const nextDisabled = state.page >= totalPages || totalPages === 0;
        html += `<button onclick="changePage(${state.page + 1})" ${nextDisabled ? 'disabled' : ''} class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium ${nextDisabled ? 'text-slate-300 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}">
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                 </button>`;

        elControls.innerHTML = html;
    }

    // Expose Function to Global Scope for OnClick
    window.changePage = function(newPage) {
        state.page = newPage;
        render();
    };

    init();
});
</script>
@endpush
