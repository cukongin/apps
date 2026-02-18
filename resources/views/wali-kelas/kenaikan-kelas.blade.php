@extends('layouts.app')

@section('title', $pageContext['title'])

@section('content')
<div class="flex flex-col gap-8" x-data="promotionPage()">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-2 text-sm font-bold text-slate-500 mb-1">
                <a href="{{ route('walikelas.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-slate-400">{{ $pageContext['title'] }}</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-violet-600 flex items-center justify-center shadow-lg shadow-primary/30 text-white">
                    <span class="material-symbols-outlined text-2xl">
                        {{ $pageContext['type'] == 'graduation' ? 'school' : 'upgrade' }}
                    </span>
                </div>
                <span>{{ $pageContext['title'] }}</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium text-base">
                Manajemen kenaikan kelas untuk <span class="text-slate-900 dark:text-white font-bold">{{ $kelas->nama_kelas }}</span>
                @if(auth()->user()->isAdmin())
                   <span class="inline-flex items-center gap-1 text-[10px] ml-2 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border dark:border-slate-700 text-slate-500">
                       <span class="w-1.5 h-1.5 rounded-full {{ $isFinalPeriod ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                       Final: {{ $isFinalPeriod ? 'YES' : 'NO' }}
                   </span>
                @endif
                @if(isset($isLocked) && $isLocked)
                    <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="material-symbols-outlined text-[16px] mr-1">lock</span> Mode Baca
                    </span>
                @endif
            </p>
        </div>

        <div class="flex flex-col items-end gap-3 w-full md:w-auto">
             <!-- Admin Controls -->
             @if(auth()->user()->isAdmin() || auth()->user()->isTu())
                @php
                        $anyLocked = collect($studentStats)->contains(fn($s) => $s->is_locked_by_admin ?? false);
                        $allLocked = collect($studentStats)->every(fn($s) => $s->is_locked_by_admin ?? false) && count($studentStats) > 0;
                @endphp
                <div class="flex items-center gap-2">
                     @if(!$allLocked)
                        <form action="{{ route('walikelas.kenaikan.lock') }}" method="POST" onsubmit="return confirm('KONFIRMASI KUNCI KELAS:\n\nKeputusan akan dikunci permanen oleh Admin.\nWali Kelas tidak akan bisa mengubah data lagi.\n\nLanjutkan?')">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $kelas->id }}">
                            <button type="submit" class="btn-boss bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 !px-4 !py-2 shadow-sm">
                                <span class="material-symbols-outlined text-[18px] text-amber-500">lock</span>
                                <span>Kunci Kelas</span>
                            </button>
                        </form>
                     @endif

                     @if($anyLocked)
                        <form action="{{ route('walikelas.kenaikan.unlock') }}" method="POST" onsubmit="return confirm('KONFIRMASI BUKA KUNCI:\n\nPERHATIAN: Semua perubahan manual akan DIRESET ke Rekomendasi Sistem saat dibuka kembali.\n\nLanjutkan?')">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $kelas->id }}">
                            <button type="submit" class="btn-boss bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 !px-4 !py-2 shadow-sm">
                                <span class="material-symbols-outlined text-[18px] text-emerald-500">lock_open</span>
                                <span>Buka Kunci</span>
                            </button>
                        </form>
                     @endif
                </div>

                <!-- Filter Dropdowns -->
                <div x-data="{ open: false }" class="relative z-20">
                    <button @click="open = !open" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 !px-4 !py-2.5 shadow-sm w-full md:w-auto justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">filter_alt</span>
                            <span>Filter Data</span>
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 top-full mt-2 w-full md:w-80 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 p-4 grid gap-3">

                        <form action="{{ route('walikelas.kenaikan.index') }}" method="GET" class="contents">
                            <!-- Tahun Ajaran -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Tahun Ajaran</label>
                                <select name="year_id" onchange="this.form.submit()" class="input-boss w-full !text-sm">
                                    @foreach($years as $y)
                                        <option value="{{ $y->id }}" {{ $activeYear->id == $y->id ? 'selected' : '' }}>{{ $y->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jenjang -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Jenjang</label>
                                <select name="jenjang" onchange="this.form.submit()" class="input-boss w-full !text-sm">
                                    <option value="">Semua</option>
                                    @foreach($jenjangs as $j)
                                        <option value="{{ $j->kode }}" {{ request('jenjang') == $j->kode || ($kelas && $kelas->jenjang->kode == $j->kode) ? 'selected' : '' }}>{{ $j->kode }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Kelas -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Kelas</label>
                                <select name="kelas_id" onchange="this.form.submit()" class="input-boss w-full !text-sm">
                                    @foreach($allClasses as $c)
                                        <option value="{{ $c->id }}" {{ $kelas->id == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Periode -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Periode</label>
                                <select name="period_id" onchange="this.form.submit()" class="input-boss w-full !text-sm">
                                    @foreach($periods as $p)
                                        <option value="{{ $p->id }}" {{ isset($activePeriod) && $activePeriod->id == $p->id ? 'selected' : '' }}>{{ $p->nama_periode }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
             @endif

             @php
                $allDecisionsLocked = collect($studentStats)->every(fn($s) => $s->is_locked);
                $isUserAdmin = auth()->user()->isAdmin() || auth()->user()->isTu();
            @endphp

            @if(isset($isLocked) && $isLocked)
                <!-- Already shown in title -->
            @elseif($allDecisionsLocked && !$isUserAdmin)
                <div class="px-4 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-500 font-bold text-xs flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">verified</span> Keputusan Final
                </div>
            @endif
        </div>
    </div>

    <!-- Criteria Info (Collapsible) -->
    @if(isset($gradingSettings))
    <div x-data="{ expanded: false }" class="bg-white dark:bg-[#1a2332] rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">info</span>
                </div>
                <div class="text-left">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm">Syarat Kenaikan / Kelulusan</h3>
                    <p class="text-xs text-slate-500">Klik untuk melihat detail aturan penilaian</p>
                </div>
            </div>
            <span class="material-symbols-outlined text-slate-400 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''">expand_more</span>
        </button>

        <div x-show="expanded" x-collapse class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 p-4">
             <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Min. Kehadiran</span>
                    <span class="font-black text-slate-800 dark:text-white text-lg">{{ $gradingSettings->promotion_min_attendance }}%</span>
                </div>
                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Max. Mapel < KKM</span>
                    <span class="font-black text-slate-800 dark:text-white text-lg">{{ $gradingSettings->promotion_max_kkm_failure }} Mapel</span>
                </div>
                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Min. Sikap</span>
                    <span class="font-black text-slate-800 dark:text-white text-lg">Minimal {{ $gradingSettings->promotion_min_attitude }}</span>
                </div>
                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">KKM Default</span>
                    <span class="font-black text-slate-800 dark:text-white text-lg">{{ $gradingSettings->kkm_default }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Santri -->
        <div class="relative bg-white dark:bg-[#1a2332] rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-black/20 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-slate-100 dark:bg-slate-800 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Santri</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-4xl font-black text-slate-900 dark:text-white">{{ $summary['total'] }}</h3>
                    <div class="flex items-center gap-1 text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg text-xs font-bold">
                        <span class="material-symbols-outlined text-[16px]">groups</span>
                        <span>Siswa</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Siap Naik/Lulus -->
        <div class="relative bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-6 shadow-xl shadow-emerald-500/20 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
             <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
             <div class="relative z-10 text-white">
                <p class="text-xs font-bold text-emerald-100 uppercase tracking-widest mb-2">Siap {{ $pageContext['success_label'] }}</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-4xl font-black">{{ $summary['promote'] }}</h3>
                     <div class="flex items-center gap-1 text-emerald-100 bg-white/20 px-2 py-1 rounded-lg text-xs font-bold backdrop-blur-sm">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span>Memenuhi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perlu Peninjauan -->
        <div class="relative bg-white dark:bg-[#1a2332] rounded-3xl p-6 border border-l-4 border-slate-100 dark:border-slate-800 border-l-amber-500 shadow-xl shadow-slate-200/50 dark:shadow-black/20 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
             <div class="absolute right-0 top-0 w-32 h-full bg-gradient-to-l from-amber-50/50 to-transparent"></div>
             <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Perlu Peninjauan</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-4xl font-black text-amber-500">{{ $summary['review'] + $summary['retain'] }}</h3>
                     <div class="flex items-center gap-1 text-amber-600 bg-amber-50 px-2 py-1 rounded-lg text-xs font-bold">
                        <span class="material-symbols-outlined text-[16px]">warning</span>
                        <span>{{ $isFinalYear ? 'Tidak Lulus' : 'Tinggal' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student List Section -->
    <div class="bg-white dark:bg-[#1a2332] rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-black/20 overflow-hidden relative">
        <!-- Toolbar -->
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-[#1a2332]">
            <h2 class="font-black text-xl text-slate-800 dark:text-white flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600">
                    <span class="material-symbols-outlined">table_chart</span>
                </span>
                Daftar Rekomendasi
            </h2>
            <div class="relative w-full md:w-auto group">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors text-lg">search</span>
                <input type="text" x-model="search" placeholder="Cari nama santri..." class="input-boss !pl-10 pr-4 py-2.5 w-full md:w-72 bg-slate-50 dark:bg-slate-900 border-slate-200 focus:bg-white transition-all rounded-xl">
            </div>
        </div>

        <!-- Mobile Architecture (Cards) -->
        <div class="md:hidden p-4 space-y-4 bg-slate-50 dark:bg-slate-900/50">
            <!-- Select All (Mobile) -->
            @if(isset($isFinalPeriod) && $isFinalPeriod)
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <label class="flex items-center gap-3 cursor-pointer w-full select-none">
                     <div class="relative flex items-center">
                        <input type="checkbox" @change="toggleAll($event)" class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-slate-300 transition-all checked:border-primary checked:bg-primary">
                        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100">
                            <span class="material-symbols-outlined text-[16px]">check</span>
                        </span>
                     </div>
                     <span class="font-bold text-slate-700 dark:text-slate-200 text-sm">Pilih Semua ({{ count($studentStats) }})</span>
                </label>
            </div>
            @endif

            @foreach($studentStats as $index => $stat)
                @php
                    $status = $stat->final_status ?: 'pending';
                    $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                    $badgeLabel = 'BELUM DITENTUKAN';
                    $badgeIcon = 'help_outline';

                    if(in_array($status, ['promoted', 'promote', 'graduated', 'graduate'])) {
                        $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        $badgeLabel = $pageContext['success_label'] ?? 'NAIK KELAS';
                        $badgeIcon = 'check_circle';
                    } elseif(in_array($status, ['retained', 'retain', 'not_graduated', 'not_graduate'])) {
                        $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                        $badgeLabel = $pageContext['fail_label'] ?? 'TINGGAL KELAS';
                        $badgeIcon = 'cancel';
                    } elseif($status == 'conditional') {
                         $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                         $badgeLabel = 'NAIK BERSYARAT';
                         $badgeIcon = 'warning';
                    }

                    $studentJson = json_encode([
                        'id' => $stat->student->id,
                        'name' => $stat->student->nama_lengkap,
                        'current_status' => $status,
                        'class_id' => $kelas->id
                    ]);
                @endphp

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden"
                 data-name="{{ strtolower($stat->student->nama_lengkap) }}"
                 x-show="matchesSearch($el.dataset.name)">

                <!-- Card Header -->
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-black text-lg shadow-inner shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <div>
                             <h3 class="font-bold text-slate-900 dark:text-white leading-tight text-lg">{{ $stat->student->nama_lengkap }}</h3>
                             <p class="text-xs text-slate-500 font-mono mt-0.5">NIS: {{ $stat->student->nis_lokal ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-2 text-center mb-4">
                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2 rounded-xl border border-slate-100 dark:border-slate-600">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Rata-rata</span>
                        <span class="font-black text-slate-800 dark:text-white text-lg">{{ $stat->avg_yearly }}</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2 rounded-xl border border-slate-100 dark:border-slate-600">
                         <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Sikap</span>
                         <span class="font-black text-lg {{ $stat->attitude == 'A' ? 'text-emerald-600' : ($stat->attitude == 'C' ? 'text-rose-600' : 'text-slate-700') }}">{{ $stat->attitude }}</span>
                    </div>
                     <div class="bg-slate-50 dark:bg-slate-700/50 p-2 rounded-xl border border-slate-100 dark:border-slate-600">
                         <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Kehadiran</span>
                         <span class="font-black text-lg {{ $stat->attendance_pct < 85 ? 'text-rose-600' : 'text-slate-700' }}">{{ $stat->attendance_pct }}%</span>
                    </div>
                </div>

                <!-- Warnings/Notes -->
                @if(($stat->under_kkm > 0) || !empty($stat->fail_reasons) || $stat->ijazah_note)
                <div class="mb-4 space-y-2">
                     @if($stat->under_kkm > 0)
                        <div class="inline-flex items-center gap-1.5 text-xs bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg border border-amber-100 font-bold w-full">
                            <span class="material-symbols-outlined text-[16px]">warning</span>
                            {{ $stat->under_kkm }} Mapel < KKM
                        </div>
                     @endif

                     @if(!empty($stat->fail_reasons))
                        <div class="text-xs text-rose-600 bg-rose-50 p-3 rounded-lg border border-rose-100 space-y-1">
                            @foreach($stat->fail_reasons as $reason)
                                <div class="flex items-start gap-1.5 font-medium">
                                    <span class="material-symbols-outlined text-[14px] mt-0.5">error</span>
                                    <span>{{ $reason }}</span>
                                </div>
                            @endforeach
                        </div>
                     @endif

                     @if($stat->ijazah_note)
                        <div class="text-xs font-bold text-emerald-600 bg-emerald-50 p-3 rounded-lg border border-emerald-100 flex items-center gap-2">
                             <span class="material-symbols-outlined text-[16px]">verified</span>
                            {{ $stat->ijazah_note }}
                        </div>
                     @endif
                </div>
                @endif

                <!-- Footer Action -->
                @if(isset($isFinalPeriod) && $isFinalPeriod)
                <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-700 gap-3">
                    <span class="flex-1 px-3 py-2 rounded-xl text-[10px] font-black uppercase border flex items-center justify-center gap-2 {{ $badgeClass }}">
                        <span class="material-symbols-outlined text-[16px]">{{ $badgeIcon }}</span>
                        {{ $badgeLabel }}
                    </span>

                    <div class="flex items-center gap-2">
                        @if((!isset($isLocked) || !$isLocked) && (!$stat->is_locked_by_admin || $isUserAdmin))
                            <button @click="openModal({{ $studentJson }})" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                        @elseif($stat->is_locked_by_admin)
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center cursor-not-allowed" title="Dikunci Admin">
                                 <span class="material-symbols-outlined text-[20px]">lock_person</span>
                            </div>
                        @endif

                         <div class="relative flex items-center">
                            <input type="checkbox" value="{{ $stat->student->id }}" x-model="selectedIds" {{ ($stat->is_locked_by_admin && !$isUserAdmin) ? 'disabled' : '' }} class="peer h-6 w-6 cursor-pointer appearance-none rounded-lg border-2 border-slate-300 transition-all checked:border-primary checked:bg-primary disabled:opacity-50 disabled:cursor-not-allowed checked:scale-90">
                            <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity">
                                <span class="material-symbols-outlined text-[18px]">check</span>
                            </span>
                         </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 uppercase text-xs font-bold text-slate-500 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        @if(isset($isFinalPeriod) && $isFinalPeriod)
                        <th class="p-4 w-12 text-center">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" @change="toggleAll($event)" class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border-2 border-slate-300 transition-all checked:border-primary checked:bg-primary checked:scale-90">
                                <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 pointer-events-none">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                </span>
                            </div>
                        </th>
                        @endif
                        <th class="px-6 py-4">Nama Santri</th>
                        <th class="px-6 py-4 text-center">Rata-Rata<br>Tahun</th>
                        <th class="px-6 py-4 text-center">Mapel<br>< KKM</th>
                        <th class="px-6 py-4 text-center">Nilai<br>Sikap</th>
                        <th class="px-6 py-4 text-center">Kehadiran<br>(%)</th>
                        <th class="px-6 py-4 text-center">Rekomendasi<br>Sistem</th>
                        <th class="px-6 py-4 text-left w-64">Catatan<br>Sistem</th>
                        @if(isset($isFinalPeriod) && $isFinalPeriod)
                        <th class="px-6 py-4 text-right w-48">Status Akhir</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 bg-white dark:bg-[#1a2332]">
                    @foreach($studentStats as $index => $stat)
                    <tr data-name="{{ strtolower($stat->student->nama_lengkap) }}"
                        x-show="matchesSearch($el.dataset.name)"
                        class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">

                        @if(isset($isFinalPeriod) && $isFinalPeriod)
                        <td class="w-12 p-4 text-center">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" value="{{ $stat->student->id }}" x-model="selectedIds" {{ ($stat->is_locked_by_admin && !$isUserAdmin) ? 'disabled' : '' }} class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border-2 border-slate-300 transition-all checked:border-primary checked:bg-primary disabled:opacity-50 disabled:cursor-not-allowed checked:scale-90">
                                <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 pointer-events-none">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                </span>
                            </div>
                        </td>
                        @endif

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-black text-sm shadow-inner">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white text-base">{{ $stat->student->nama_lengkap }}</div>
                                    <div class="text-xs text-slate-500 font-mono">NIS: {{ $stat->student->nis_lokal ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-black text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-lg">{{ $stat->avg_yearly }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($stat->under_kkm > 0)
                                <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-lg text-xs font-bold">{{ $stat->under_kkm }} Mapel</span>
                            @else
                                <span class="text-slate-300 font-bold block">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-black text-lg {{ $stat->attitude == 'A' ? 'text-emerald-600' : ($stat->attitude == 'C' ? 'text-rose-600' : 'text-slate-700') }}">
                            {{ $stat->attitude }}
                        </td>
                        <td class="px-6 py-4 text-center">
                             <div class="flex flex-col items-center gap-1">
                                <span class="font-bold {{ $stat->attendance_pct < 85 ? 'text-rose-600' : 'text-slate-700' }}">{{ $stat->attendance_pct }}%</span>
                                <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $stat->attendance_pct < 85 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $stat->attendance_pct }}%"></div>
                                </div>
                             </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                             @if($stat->system_status == 'promote' || $stat->system_status == 'graduate')
                                <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[10px] font-black border border-emerald-200 uppercase">
                                     <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                    {{ $stat->recommendation }}
                                </span>
                                @if($stat->system_status != $stat->final_status)
                                    <div class="text-[9px] text-slate-400 text-center mt-1 font-medium">
                                        (Diabaikan Manual)
                                    </div>
                                @endif
                            @else
                                <span class="{{ $stat->system_status == 'review' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-rose-100 text-rose-700 border-rose-200' }} inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black border uppercase">
                                    <span class="material-symbols-outlined text-[10px]">{{ $stat->system_status == 'review' ? 'warning' : 'close' }}</span>
                                    {{ $stat->recommendation }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 leading-snug break-words">
                            @if(!empty($stat->fail_reasons))
                                <div class="text-rose-600 mb-1 space-y-1">
                                    @foreach($stat->fail_reasons as $reason)
                                        <div class="flex items-center gap-1 max-w-[200px]"><span class="material-symbols-outlined text-[12px] shrink-0">error</span> {{ $reason }}</div>
                                    @endforeach
                                </div>
                            @endif
                            @if($stat->ijazah_note)
                                <div class="text-[11px] {{ $stat->ijazah_class ?? 'text-emerald-600' }} flex items-start gap-1 leading-tight">
                                    <span class="material-symbols-outlined text-[14px] top-[1px] relative shrink-0">{{ str_contains($stat->ijazah_note, 'Remedial') || str_contains($stat->ijazah_note, 'PERHATIAN') ? 'warning' : 'verified' }}</span>
                                    <span>{{ $stat->ijazah_note }}</span>
                                </div>
                            @endif
                            @if($stat->manual_note) <div class="italic text-slate-500 mt-1">"{{ $stat->manual_note }}"</div> @endif
                            @if(empty($stat->fail_reasons) && !$stat->ijazah_note && !$stat->manual_note) <span class="text-slate-300">-</span> @endif
                        </td>

                        @if(isset($isFinalPeriod) && $isFinalPeriod)
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <!-- SERVER SIDE RENDERED BADGE -->
                                @php
                                    $status = $stat->final_status ?: 'pending';
                                    $badgeClass = 'bg-slate-50 text-slate-600 border-slate-200';
                                    $badgeLabel = 'BELUM DITENTUKAN';
                                    $badgeIcon = 'help_outline';

                                    if(in_array($status, ['promoted', 'promote', 'graduated', 'graduate'])) {
                                        $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        $badgeLabel = $pageContext['success_label'] ?? 'NAIK KELAS';
                                        $badgeIcon = 'check_circle';
                                    } elseif(in_array($status, ['retained', 'retain', 'not_graduated', 'not_graduate'])) {
                                        $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                        $badgeLabel = $pageContext['fail_label'] ?? 'TINGGAL KELAS';
                                        $badgeIcon = 'cancel';
                                    } elseif($status == 'conditional') {
                                        $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                        $badgeLabel = 'NAIK BERSYARAT';
                                        $badgeIcon = 'warning';
                                    }

                                    $studentJson = json_encode([
                                        'id' => $stat->student->id,
                                        'name' => $stat->student->nama_lengkap,
                                        'current_status' => $status,
                                        'class_id' => $kelas->id
                                    ]);
                                @endphp

                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase border shadow-sm flex items-center gap-1.5 {{ $badgeClass }}">
                                            <span class="material-symbols-outlined text-[14px]">{{ $badgeIcon }}</span>
                                            <span>{{ $badgeLabel }}</span>
                                    </span>

                                    @if($stat->system_status != $stat->final_status)
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[10px]">edit</span> Manual
                                        </span>
                                    @endif
                                </div>

                                @if((!isset($isLocked) || !$isLocked) && (!$stat->is_locked_by_admin || $isUserAdmin))
                                <button @click="openModal({{ $studentJson }})"
                                        class="w-8 h-8 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 text-slate-400 transition-all shadow-sm flex items-center justify-center transform hover:scale-105"
                                        title="Ubah Keputusan">
                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                </button>
                                @elseif($stat->is_locked_by_admin)
                                <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center cursor-not-allowed" title="Dikunci Admin">
                                    <span class="material-symbols-outlined text-[18px]">lock_person</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Bulk Toolbar -->
    <div x-show="selectedIds.length > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-20 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-20 opacity-0"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 w-full max-w-lg px-4"
         style="display: none;">

        <div class="bg-slate-900/90 dark:bg-slate-800/90 backdrop-blur-xl border border-white/10 dark:border-slate-700/50 rounded-2xl shadow-2xl p-2 pl-3 flex items-center justify-between gap-3 text-white ring-1 ring-black/5">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-500/20 text-indigo-300 px-3 py-1.5 rounded-lg flex items-center gap-2 border border-indigo-500/30">
                    <span class="font-black text-sm" x-text="selectedIds.length"></span>
                    <span class="text-[10px] uppercase font-bold tracking-wider opacity-75">Terpilih</span>
                </div>
                <div class="h-8 w-px bg-white/10"></div>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth">
                @if($isFinalYear)
                    <button @click="bulkUpdate('graduated')" class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-lg shadow-emerald-500/20 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[16px]">school</span>
                        <span>LULUS</span>
                    </button>
                    <button @click="bulkUpdate('not_graduated')" class="flex items-center gap-1.5 bg-rose-600 hover:bg-rose-500 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-lg shadow-rose-500/20 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>TIDAK</span>
                    </button>
                @else
                    <button @click="bulkUpdate('promoted')" class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-lg shadow-emerald-500/20 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[16px]">upgrade</span>
                        <span>NAIK</span>
                    </button>
                    <button @click="bulkUpdate('retained')" class="flex items-center gap-1.5 bg-rose-600 hover:bg-rose-500 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-lg shadow-rose-500/20 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[16px]">history</span>
                        <span>TINGGAL</span>
                    </button>
                @endif
            </div>

            <button @click="selectedIds = []" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/10 text-slate-400 hover:text-white transition-colors shrink-0">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
    </div>

    <!-- Edit Decision Modal -->
    <div x-show="showModal" style="display: none;" class="relative z-[99]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="closeModal()"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#1a2332] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800">

                    <!-- Header -->
                    <div class="bg-indigo-600 dark:bg-indigo-900/50 px-4 py-6 sm:px-6 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shadow-inner">
                                <span class="material-symbols-outlined text-white text-2xl">edit_square</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white leading-tight">Ubah Keputusan</h3>
                                <p class="text-indigo-100 text-sm mt-0.5">Override manual status akhir siswa.</p>
                            </div>
                        </div>
                        <button @click="closeModal()" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-4 py-6 sm:px-6 space-y-6">
                        <!-- Student Info -->
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 font-bold shrink-0">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nama Santri</span>
                                <span class="font-bold text-slate-800 dark:text-white text-base" x-text="editData.name"></span>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Akhir</label>
                                <div class="relative">
                                    <select x-model="editData.new_status" class="input-boss w-full appearance-none">
                                        @if($isFinalYear)
                                            <option value="graduated">LULUS (Tamat Belajar)</option>
                                            <option value="not_graduated">TIDAK LULUS</option>
                                            <option value="pending">Belum Ada Keputusan</option>
                                        @else
                                            <option value="promoted">NAIK KELAS (Lanjut ke Tingkat Berikutnya)</option>
                                            <option value="retained">TINGGAL KELAS (Mengulang)</option>
                                            <option value="pending">Belum Ada Keputusan</option>
                                        @endif
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                        <span class="material-symbols-outlined">expand_more</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 rounded-lg bg-amber-50 text-amber-700 text-xs border border-amber-100 flex gap-2">
                                <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">warning</span>
                                <span>Perubahan manual akan mengabaikan rekomendasi sistem. Pastikan keputusan ini sudah final.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-white dark:bg-[#1a2332] px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-800 gap-3">
                        <button type="button"
                                @click="saveDecision()"
                                :disabled="saving"
                                class="btn-boss bg-indigo-600 hover:bg-indigo-500 text-white w-full sm:w-auto shadow-lg shadow-indigo-500/30 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="saving" class="material-symbols-outlined animate-spin text-[18px]">sync</span>
                            <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                        <button type="button"
                                @click="closeModal()"
                                class="btn-boss bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 w-full sm:w-auto mt-3 sm:mt-0 shadow-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function promotionPage() {
        return {
            selectedIds: [],
            search: '',
            showModal: false,
            saving: false,
            editData: {
                id: null,
                name: '',
                new_status: 'pending',
                class_id: null
            },

            matchesSearch(name) {
                if(!this.search) return true;
                return name.includes(this.search.toLowerCase());
            },

            toggleAll(e) {
                if(e.target.checked) {
                    this.selectedIds = [
                        @foreach($studentStats as $stat)
                            {{ $stat->student->id }},
                        @endforeach
                    ];
                } else {
                    this.selectedIds = [];
                }
            },

            openModal(studentData) {
                this.editData = {
                    id: studentData.id,
                    name: studentData.name,
                    new_status: studentData.current_status,
                    class_id: studentData.class_id
                };
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            async saveDecision() {
                this.saving = true;
                try {
                    const res = await fetch("{{ route('promotion.update') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            student_id: this.editData.id,
                            class_id: this.editData.class_id,
                            status: this.editData.new_status
                        })
                    });

                    if (res.ok) {
                        this.showModal = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status kenaikan kelas berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        const data = await res.json();
                        Swal.fire('Gagal', data.message || 'Gagal menyimpan', 'error');
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.saving = false;
                }
            },

            async bulkUpdate(status) {
                 const result = await Swal.fire({
                    title: 'Konfirmasi Massal',
                    text: `Yakin ubah status ${this.selectedIds.length} santri terpilih?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                 });

                 if (!result.isConfirmed) return;

                 // Show loading
                 Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                 });

                 try {
                     const res = await fetch("{{ route('promotion.bulk_update') }}", {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                         },
                         body: JSON.stringify({
                            student_ids: this.selectedIds,
                            class_id: {{ $kelas->id }},
                            status: status
                        })
                     });

                     if (res.ok) {
                         const data = await res.json();
                         Swal.fire({
                            icon: 'success',
                            title: 'Selesai!',
                            text: data.message || 'Update massal berhasil.',
                            timer: 1500,
                            showConfirmButton: false
                         }).then(() => {
                             window.location.reload();
                         });
                     } else {
                         const data = await res.json();
                         Swal.fire('Gagal', data.message || 'Gagal melakukan update massal', 'error');
                     }
                 } catch(e) {
                     console.error(e);
                     Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
                 }
            }
        }
    }
</script>
@endsection
