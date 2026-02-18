@extends('layouts.app')

@section('title', 'Analisa Prestasi - ' . $class->nama_kelas)

@section('content')
<div class="flex flex-col space-y-6">

    <!-- 1. Header & Filters -->
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('tu.monitoring.global') }}" class="text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    Analisa Prestasi
                    @if($isAnnual ?? false)
                        <span class="bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 text-xs font-bold px-2 py-0.5 rounded-full border border-purple-200 dark:border-purple-700 uppercase tracking-wider">Tahunan</span>
                    @endif
                </h1>
            </div>
            <p class="text-slate-500 dark:text-slate-400 ml-8 text-sm">Ranking detail dan analisa nilai siswa kelas <span class="font-bold text-primary">{{ $class->nama_kelas }}</span>.</p>
        </div>
    </div>

    <!-- Annual Mode Info Banner -->
    @if($isAnnual ?? false)
    <div class="card-boss !p-4 bg-purple-50 dark:bg-purple-900/20 border-purple-100 dark:border-purple-800 flex items-start gap-4 shadow-none">
        <div class="size-10 rounded-full bg-purple-100 dark:bg-purple-800 flex items-center justify-center shrink-0 text-purple-600 dark:text-purple-300">
            <span class="material-symbols-outlined">info</span>
        </div>
        <div>
            <h3 class="font-bold text-purple-800 dark:text-purple-300 text-sm">Mode Analisa Tahunan Aktif</h3>
            <p class="text-xs text-purple-600 dark:text-purple-400 mt-1 leading-relaxed">
                Data yang ditampilkan adalah <strong>akumulasi dari semua periode</strong> di tahun ajaran ini.
                <br>&bull; <strong>Total Rata-rata:</strong> Rata-rata dari nilai akhir setiap mapel (Lintas Periode).
                <br>&bull; <strong>Kehadiran:</strong> Total jumlah ketidakhadiran (Sakit/Izin/Alpa) selama satu tahun penuh.
            </p>
        </div>
    </div>
    @endif


    <!-- 2. Podium Section (Top 3) -->
    @if(count($podium) >= 1)
    <div class="relative pt-8 pb-4">
        <!-- Background Decoration -->
        <div class="absolute inset-x-0 top-1/2 -bottom-10 bg-gradient-to-b from-primary/5 to-transparent dark:from-primary/10 dark:to-transparent rounded-[3rem] -z-10"></div>

        <div class="flex justify-center items-end gap-4 md:gap-12 px-4">

            <!-- Rank 2 -->
            @if(isset($podium[1]))
            <div class="flex flex-col items-center group relative top-4 transition-transform hover:-translate-y-2 duration-300">
                <div class="relative mb-3">
                    <div class="size-20 md:size-28 rounded-full border-4 border-slate-300 dark:border-slate-600 overflow-hidden shadow-xl bg-white dark:bg-slate-800 ring-4 ring-white dark:ring-slate-900">
                        <div class="w-full h-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-3xl font-black text-slate-400 dark:text-slate-500">
                            {{ substr($podium[1]['student']->nama_lengkap, 0, 1) }}
                        </div>
                    </div>
                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-slate-500 text-white size-8 flex items-center justify-center rounded-full font-black border-4 border-slate-50 dark:border-[#121c16] shadow-md z-10 text-sm">
                        2
                    </div>
                </div>
                <div class="text-center mt-2">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm md:text-base line-clamp-1 max-w-[120px]">{{ $podium[1]['student']->nama_lengkap }}</h3>
                    <div class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-200 dark:bg-slate-700 px-3 py-1 rounded-full inline-block mt-1">
                        {{ number_format($podium[1]['total'], 2) }} {{ ($isAnnual ?? false) ? 'Avg' : 'Poin' }}
                    </div>
                </div>
                <!-- Podium Base -->
                <div class="h-24 md:h-32 w-24 md:w-32 bg-gradient-to-t from-slate-300 to-slate-100 dark:from-slate-700 dark:to-slate-600 rounded-t-2xl mt-4 shadow-inner opacity-90 mx-auto"></div>
            </div>
            @endif

            <!-- Rank 1 -->
            @if(isset($podium[0]))
            <div class="flex flex-col items-center group z-10 transition-transform hover:-translate-y-2 duration-300 scale-110">
                <div class="relative mb-3">
                    <div class="absolute -top-12 left-1/2 -translate-x-1/2 animate-bounce">
                        <span class="material-symbols-outlined text-5xl text-amber-400 drop-shadow-lg">emoji_events</span>
                    </div>
                    <div class="size-28 md:size-36 rounded-full border-4 border-amber-400 overflow-hidden shadow-2xl bg-white dark:bg-slate-800 ring-4 ring-amber-100 dark:ring-amber-900/30">
                         <div class="w-full h-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-5xl font-black text-amber-400">
                            {{ substr($podium[0]['student']->nama_lengkap, 0, 1) }}
                        </div>
                    </div>
                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-amber-500 text-white size-10 flex items-center justify-center rounded-full font-black border-4 border-slate-50 dark:border-[#121c16] shadow-lg z-10 text-xl">
                        1
                    </div>
                </div>
                 <div class="text-center mt-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-base md:text-xl line-clamp-1 max-w-[180px]">{{ $podium[0]['student']->nama_lengkap }}</h3>
                     <div class="text-sm font-bold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/40 px-4 py-1 rounded-full inline-block mt-1 shadow-sm">
                        {{ number_format($podium[0]['total'], 2) }} {{ ($isAnnual ?? false) ? 'Avg' : 'Poin' }}
                    </div>
                    @if(isset($podium[0]['tie_reason']))
                         <div class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 font-bold animate-pulse uppercase tracking-wide">
                            <span class="material-symbols-outlined text-sm align-middle">trophy</span> {{ $podium[0]['tie_reason'] }}
                        </div>
                    @endif
                </div>
                <!-- Podium Base -->
                <div class="h-32 md:h-44 w-32 md:w-44 bg-gradient-to-t from-amber-300 to-amber-100 dark:from-amber-700 dark:to-amber-500 rounded-t-2xl mt-4 shadow-lg relative overflow-hidden mx-auto">
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-white/20 skew-y-6 transform origin-bottom-left"></div>
                </div>
            </div>
            @endif

            <!-- Rank 3 -->
            @if(isset($podium[2]))
            <div class="flex flex-col items-center group relative top-8 transition-transform hover:-translate-y-2 duration-300">
                <div class="relative mb-3">
                    <div class="size-20 md:size-28 rounded-full border-4 border-orange-300 dark:border-orange-700 overflow-hidden shadow-xl bg-white dark:bg-slate-800 ring-4 ring-white dark:ring-slate-900">
                        <div class="w-full h-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-3xl font-black text-orange-300 dark:text-orange-600">
                            {{ substr($podium[2]['student']->nama_lengkap, 0, 1) }}
                        </div>
                    </div>
                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-orange-500 text-white size-8 flex items-center justify-center rounded-full font-black border-4 border-slate-50 dark:border-[#121c16] shadow-md z-10 text-sm">
                        3
                    </div>
                </div>
               <div class="text-center mt-2">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm md:text-base line-clamp-1 max-w-[120px]">{{ $podium[2]['student']->nama_lengkap }}</h3>
                     <div class="text-xs font-bold text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-900/40 px-3 py-1 rounded-full inline-block mt-1">
                        {{ number_format($podium[2]['total'], 2) }} {{ ($isAnnual ?? false) ? 'Avg' : 'Poin' }}
                    </div>
                </div>
                <!-- Podium Base -->
                <div class="h-20 md:h-28 w-24 md:w-32 bg-gradient-to-t from-orange-300 to-orange-100 dark:from-orange-800 dark:to-orange-700 rounded-t-2xl mt-4 shadow-inner opacity-90 mx-auto"></div>
            </div>
            @endif

        </div>
    </div>
    @endif

    <!-- 2.5 Advanced Analytics Dashboard (Mapel & Anomaly & Role Models) -->
    @if(isset($mapelAnalysis) || (isset($anomalies) && count($anomalies) > 0) || (isset($roleModels) && count($roleModels) > 0))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Comparison: Non-Anomaly Students -->
        @if(isset($roleModels) && count($roleModels) > 0)
        <div class="card-boss !p-5">
            <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-2">
                <span class="material-symbols-outlined text-primary">verified_user</span> Siswa Berprestasi (Normal)
            </h3>
            <div class="space-y-3">
                @foreach($roleModels as $goodStudent)
                <div onclick="showAnalyticsModal('insight', 'Siswa Berprestasi Normal âœ…', '{{ $goodStudent['student']->nama_lengkap }} adalah pembanding positif.', 'Rank #{{ $goodStudent['rank'] }} dengan {{ $goodStudent['alpha'] }} Alpha (Wajar).')"
                     class="group bg-primary/5 dark:bg-primary/10 p-3 rounded-xl border border-primary/20 dark:border-primary/30 flex justify-between items-center cursor-pointer hover:bg-primary/10 transition-colors">
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm group-hover:text-primary transition-colors">
                             Rank #{{ $goodStudent['rank'] }} - {{ $goodStudent['student']->nama_lengkap }}
                        </div>
                        <div class="text-xs text-primary dark:text-primary-light mt-0.5">
                             <strong>{{ $goodStudent['alpha'] }} Alpha</strong> (Non-Paradox)
                        </div>
                    </div>
                     <span class="material-symbols-outlined text-primary/70 text-lg group-hover:scale-110 transition-transform">check_circle</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Anomaly Detection -->
         @if(isset($anomalies) && count($anomalies) > 0)
        <div class="card-boss !p-5 bg-amber-50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-800/50 relative overflow-hidden">
             <!-- Background Warning Icon -->
            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[100px] text-amber-500/10 pointer-events-none">warning</span>

            <h3 class="font-bold text-amber-800 dark:text-amber-400 mb-4 flex items-center gap-2 border-b border-amber-200/50 dark:border-amber-800/50 pb-2">
                <span class="material-symbols-outlined">warning</span> Deteksi Anomali (Paradoks)
            </h3>
            <div class="space-y-3 relative z-10">
                @foreach($anomalies as $badStudent)
                <div onclick="showAnalyticsModal('anomaly', 'Deteksi Paradoks', '{{ $badStudent['student']->nama_lengkap }} ada di Top 5 tapi Alpha Tinggi.', 'Rank #{{ $badStudent['rank'] }} dengan {{ $badStudent['alpha'] }} Alpha. Cek alasan bolos!')"
                     class="group bg-white/60 dark:bg-slate-800/60 p-3 rounded-xl border border-amber-200/50 flex justify-between items-center cursor-pointer hover:bg-white hover:shadow-sm transition-all">
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">
                             Rank #{{ $badStudent['rank'] }} - {{ $badStudent['student']->nama_lengkap }}
                        </div>
                        <div class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                            Prestasi Tinggi tapi <strong>{{ $badStudent['alpha'] }} Alpha</strong>
                        </div>
                    </div>
                     <span class="material-symbols-outlined text-amber-500 animate-pulse group-hover:scale-110 transition-transform">priority_high</span>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <!-- Empty State for Anomalies (Good thing) -->
         <div class="card-boss !p-5 flex flex-col items-center justify-center text-center opacity-60 dashed-border">
             <span class="material-symbols-outlined text-5xl text-slate-300 mb-2">check_circle</span>
             <h3 class="text-sm font-bold text-slate-600 dark:text-slate-400">Tidak Ada Anomali</h3>
             <p class="text-xs text-slate-400">Semua siswa top disiplin.</p>
         </div>
        @endif
    </div>
    @endif

    <!-- 3. Ranking Table -->
    <div class="card-boss !p-0 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
             <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                 <span class="material-symbols-outlined text-primary">leaderboard</span>
                 Daftar Peringkat {{ ($isAnnual ?? false) ? 'Tahunan' : 'Periode' }}
            </h3>
             @if($isAnnual ?? false)
                 <span class="text-[10px] uppercase font-bold text-purple-600 bg-purple-50 dark:bg-purple-900/30 px-2 py-1 rounded border border-purple-100 dark:border-purple-800">Kumulatif</span>
             @endif
        </div>

        <!-- Mobile Card View (Visible only on mobile) -->
        <div class="md:hidden space-y-3 p-4 bg-slate-50/30 dark:bg-[#1e2837]/30">
             @foreach($rankingData as $data)
             <div class="card-boss !p-4 !shadow-sm flex flex-col gap-3 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div class="flex gap-4">
                         <!-- Rank Badge -->
                        <div class="flex flex-col items-center gap-2">
                             <div class="size-12 rounded-xl flex items-center justify-center font-black text-white shadow-lg border-2 border-white dark:border-slate-700 text-lg
                                {{ $data['rank'] == 1 ? 'bg-amber-500' : ($data['rank'] == 2 ? 'bg-slate-500' : ($data['rank'] == 3 ? 'bg-orange-600' : 'bg-primary')) }}">
                                #{{ $data['rank'] }}
                            </div>

                             <!-- Mobile Trend Indicator -->
                            @if(isset($data['trend_status']))
                                @if($data['trend_status'] == 'rising')
                                    <button onclick="showAnalyticsModal('rising', 'Rocket Star', '{{ $data['student']->nama_lengkap }} melesat naik {{ $data['trend_diff'] }} peringkat!', 'Dari Ranking #{{ $data['prev_rank'] }} ke #{{ $data['rank'] }}')"
                                        class="material-symbols-outlined text-emerald-500 text-xl animate-bounce">rocket_launch</button>
                                @elseif($data['trend_status'] == 'falling')
                                    <button onclick="showAnalyticsModal('falling', 'Perlu Evaluasi', '{{ $data['student']->nama_lengkap }} turun {{ abs($data['trend_diff']) }} peringkat!', 'Dari Ranking #{{ $data['prev_rank'] }} anjlok ke #{{ $data['rank'] }}')"
                                        class="material-symbols-outlined text-rose-500 text-xl">trending_down</button>
                                @elseif($data['trend_status'] == 'comeback')
                                    <button onclick="showAnalyticsModal('rising', 'Raja Comeback', '{{ $data['student']->nama_lengkap }} berhasil bangkit!', 'Awal: Rank #{{ $data['start_rank'] }} ➔ Akhir: Rank #{{ $data['end_rank'] }}')"
                                        class="material-symbols-outlined text-purple-500 text-xl animate-pulse">crown</button>
                                @elseif($data['trend_status'] == 'dropped')
                                    <button onclick="showAnalyticsModal('falling', 'Early Bird', '{{ $data['student']->nama_lengkap }} turun di akhir.', 'Awal: Rank #{{ $data['start_rank'] }} ➔ Akhir: Rank #{{ $data['end_rank'] }}')"
                                        class="material-symbols-outlined text-orange-500 text-xl">history_toggle_off</button>
                                @endif
                            @endif
                        </div>

                        <!-- Details -->
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white line-clamp-1 text-lg">{{ $data['student']->nama_lengkap }}</div>
                            <div class="text-xs text-slate-500 mb-2 font-mono">{{ $data['student']->nis_lokal }}</div>
                            <div class="flex items-center gap-2">
                                <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-lg border border-primary/20">
                                    {{ number_format($data['total'], 2) }} Poin
                                </span>
                                <span class="text-xs text-slate-500 font-medium">Avg: {{ number_format($data['avg'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Insight Badge (Full Width on Mobile) -->
                 @if(!empty($data['insight']))
                 <div class="pt-3 border-t border-slate-100 dark:border-slate-700">
                     @php
                        $historyHtml = '';
                        if(!empty($data['rank_journey'])) {
                            foreach($data['rank_journey'] as $j) {
                                 $historyHtml .= '<div class="flex justify-between items-center border-b border-dashed border-slate-200 dark:border-slate-700 last:border-0 pb-1 last:pb-0 mb-1"><span>'.($j['period'] ?? '?').'</span><span class="font-bold border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-1.5 rounded text-xs">#'.$j['rank'].'</span></div>';
                            }
                        }
                    @endphp
                     <div data-history="{{ $historyHtml }}"
                         onclick="showAnalyticsModal('insight', 'Detail Predikat Siswa', '{{ $data['insight'] }}', 'Total Nilai: {{ number_format($data['total'], 2) }} &bull; Rata-rata: {{ number_format($data['avg'], 2) }} &bull; Alpha: {{ $data['alpha'] }}', this.getAttribute('data-history'))"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold border cursor-pointer hover:brightness-95 transition-all
                        {{ str_contains($data['insight'], 'Kalah') || str_contains($data['insight'], 'Perhatian')
                            ? 'bg-rose-50 text-rose-700 border-rose-200'
                            : (str_contains($data['insight'], 'Menang') || str_contains($data['insight'], 'Juara') || str_contains($data['insight'], 'Sempurna') || str_contains($data['insight'], 'Raja') || str_contains($data['insight'], 'Dewa')
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                        @if(str_contains($data['insight'], 'Menang') || str_contains($data['insight'], 'Juara') || str_contains($data['insight'], 'Sempurna'))
                            <span class="material-symbols-outlined text-[18px]">verified</span>
                        @elseif(str_contains($data['insight'], 'Kalah') || str_contains($data['insight'], 'Perhatian'))
                            <span class="material-symbols-outlined text-[18px]">warning</span>
                        @else
                            <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                        @endif
                        {{ $data['insight'] }}
                    </div>
                 </div>
                 @endif

                 <!-- Absence Indicator (Absolute or inline) -->
                 @if($data['alpha'] > 0)
                 <div class="absolute top-4 right-4 flex items-center gap-1 text-[10px] font-bold text-rose-500 bg-rose-50 border border-rose-100 px-2 py-1 rounded-full">
                     <span class="material-symbols-outlined text-[14px]">cancel</span> {{ $data['alpha'] }} Alpha
                 </div>
                 @endif
             </div>
             @endforeach
        </div>

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 font-bold">
                    <tr>
                        <th class="px-6 py-4 text-center w-24">Rank</th>
                        <th class="px-6 py-4">Nama Siswa / NIS</th>
                        <th class="px-6 py-4 text-center">{{ ($isAnnual ?? false) ? 'Total Avg' : 'Total Nilai' }}</th>
                        <th class="px-6 py-4 text-center">Rata-rata</th>
                        <th class="px-6 py-4 text-center">Alpha</th>
                        <th class="px-6 py-4 text-center">Kepribadian</th>
                        <th class="px-6 py-4 text-center">Analisa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($rankingData as $data)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-3 relative">
                                <span class="flex size-8 items-center justify-center rounded-full font-bold text-xs
                                    {{ $data['rank'] <= 3 ? 'bg-primary text-white shadow-md shadow-primary/30' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                    #{{ $data['rank'] }}
                                </span>

                                <!-- Trend Indicator -->
                                @if(isset($data['trend_status']))
                                    @if($data['trend_status'] == 'rising')
                                        <button onclick="showAnalyticsModal('rising', 'Rocket Star', '{{ $data['student']->nama_lengkap }} melesat naik {{ $data['trend_diff'] }} peringkat!', 'Dari Ranking #{{ $data['prev_rank'] }} ke #{{ $data['rank'] }}')"
                                            class="material-symbols-outlined text-emerald-500 text-xl animate-bounce cursor-pointer hover:scale-125 transition-transform"
                                            title="Klik untuk detail">rocket_launch</button>
                                    @elseif($data['trend_status'] == 'falling')
                                        <button onclick="showAnalyticsModal('falling', 'Perlu Evaluasi', '{{ $data['student']->nama_lengkap }} turun {{ abs($data['trend_diff']) }} peringkat.', 'Dari Ranking #{{ $data['prev_rank'] }} anjlok ke #{{ $data['rank'] }}')"
                                            class="material-symbols-outlined text-rose-500 text-xl cursor-pointer hover:scale-125 transition-transform"
                                            title="Klik untuk detail">trending_down</button>

                                    {{-- Annual Trends --}}
                                    @elseif($data['trend_status'] == 'comeback')
                                         <button onclick="showAnalyticsModal('rising', 'Raja Comeback', '{{ $data['student']->nama_lengkap }} berhasil bangkit dari peringkat bawah!', 'Awal: Rank #{{ $data['start_rank'] }} ➔ Akhir: Rank #{{ $data['end_rank'] }}')"
                                            class="material-symbols-outlined text-purple-500 text-xl animate-pulse cursor-pointer hover:scale-125 transition-transform"
                                            title="Klik untuk detail">crown</button>
                                    @elseif($data['trend_status'] == 'dropped')
                                         <button onclick="showAnalyticsModal('falling', 'Early Bird', '{{ $data['student']->nama_lengkap }} mengalami penurunan performa di akhir tahun.', 'Awal: Rank #{{ $data['start_rank'] }} ➔ Akhir: Rank #{{ $data['end_rank'] }}')"
                                            class="material-symbols-outlined text-orange-500 text-xl cursor-pointer hover:scale-125 transition-transform"
                                            title="Klik untuk detail">history_toggle_off</button>
                                    @elseif($data['trend_status'] == 'stable_high')
                                         <button onclick="showAnalyticsModal('stable', 'Dewa Stabil', '{{ $data['student']->nama_lengkap }} konsisten di papan atas sepanjang tahun.', 'Selalu berada di Top Tier peringkat kelas.')"
                                            class="material-symbols-outlined text-blue-500 text-xl cursor-pointer hover:scale-125 transition-transform"
                                            title="Klik untuk detail">shield</button>

                                    @elseif($data['trend_status'] == 'stable')
                                         <button onclick="showAnalyticsModal('stable', 'Performa Stabil', '{{ $data['student']->nama_lengkap }} mempertahankan posisinya.', 'Tidak ada perubahan peringkat yang signifikan.')"
                                            class="material-symbols-outlined text-slate-300 text-xl cursor-pointer hover:scale-125 transition-transform"
                                            title="Klik untuk detail">remove</button>

                                    {{-- Minor Trends --}}
                                    @elseif($data['trend_status'] == 'up' || $data['trend_status'] == 'improved')
                                        <span class="material-symbols-outlined text-emerald-400 text-lg" title="Naik dari sebelumnya">arrow_upward</span>
                                    @elseif($data['trend_status'] == 'down')
                                        <span class="material-symbols-outlined text-rose-400 text-lg" title="Turun dari sebelumnya">arrow_downward</span>
                                    @endif
                                @endif

                                <!-- Journey Path (Annual Only) -->
                                @if(isset($data['rank_journey']) && count($data['rank_journey']) > 1)
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-white dark:bg-slate-700 text-[10px] font-mono font-bold text-slate-500 border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2 shadow-xl whitespace-nowrap z-50 hidden group-hover:block transition-all animate-fade-in pointer-events-none">
                                    <div class="text-[9px] text-slate-400 mb-1 border-b border-slate-100 dark:border-slate-600 pb-1 uppercase tracking-wide">Riwayat Ranking</div>
                                    <div class="flex items-center gap-1.5">
                                    @foreach($data['rank_journey'] as $j)
                                        <span class="{{ $loop->last ? 'text-primary font-black scale-110' : 'text-slate-400' }}">#{{ $j['rank'] }}</span>
                                        @if(!$loop->last) <span class="text-slate-300 dark:text-slate-600 text-[8px]">âžœ</span> @endif
                                    @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900 dark:text-white text-base">{{ $data['student']->nama_lengkap }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $data['student']->nis_lokal }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-black text-primary dark:text-primary-light text-lg bg-primary/5 dark:bg-primary/10 px-3 py-1 rounded-lg">
                                {{ number_format($data['total'], 2) }}
                            </span>
                            @if(!($isAnnual ?? false))
                            <div class="text-[10px] text-slate-400 mt-1">dari {{ $data['grades_count'] }} Mapel</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-slate-600 dark:text-slate-300">
                             {{ number_format($data['avg'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($data['alpha'] == 0)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <span class="material-symbols-outlined text-[14px]">check</span> Nihil
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 px-2 py-1 rounded-lg text-xs">
                                    {{ $data['alpha'] }} Alpha
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center max-w-[150px]">
                            <span class="text-xs text-slate-600 dark:text-slate-400 italic line-clamp-2" title="{{ $data['personality'] ?? '-' }}">
                                {{ $data['personality'] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(!empty($data['insight']))
                                @php
                                    $historyHtml = '';
                                    if(!empty($data['rank_journey'])) {
                                        foreach($data['rank_journey'] as $j) {
                                             $historyHtml .= '<div class="flex justify-between items-center border-b border-dashed border-slate-200 dark:border-slate-700 last:border-0 pb-1 last:pb-0 mb-1"><span>'.($j['period'] ?? '?').'</span><span class="font-bold border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-1.5 rounded text-xs">#'.$j['rank'].'</span></div>';
                                        }
                                    }
                                @endphp
                                <div data-history="{{ $historyHtml }}"
                                    onclick="showAnalyticsModal('insight', 'Detail Predikat Siswa', '{{ $data['insight'] }}', 'Total Nilai: {{ number_format($data['total'], 2) }} &bull; Alpha: {{ $data['alpha'] }} &bull; Sikap: {{ $data['personality'] ?? '-' }}', this.getAttribute('data-history'))"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold border max-w-[200px] leading-tight cursor-pointer hover:scale-105 transition-transform shadow-sm select-none
                                    {{ str_contains($data['insight'], 'Kalah') || str_contains($data['insight'], 'Perhatian') || str_contains($data['insight'], 'Awas')
                                        ? 'bg-rose-50 text-rose-700 border-rose-200'
                                        : (str_contains($data['insight'], 'Menang') || str_contains($data['insight'], 'Juara') || str_contains($data['insight'], 'Sempurna') || str_contains($data['insight'], 'Raja') || str_contains($data['insight'], 'Dewa')
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                            : 'bg-indigo-50 text-indigo-700 border-indigo-200') }}">
                                    @if(str_contains($data['insight'], 'Menang') || str_contains($data['insight'], 'Juara') || str_contains($data['insight'], 'Sempurna'))
                                        <span class="material-symbols-outlined text-[16px]">verified</span>
                                    @elseif(str_contains($data['insight'], 'Kalah') || str_contains($data['insight'], 'Perhatian') || str_contains($data['insight'], 'Awas'))
                                        <span class="material-symbols-outlined text-[16px]">warning</span>
                                    @else
                                        <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                                    @endif
                                    <span class="truncate">{{ $data['insight'] }}</span>
                                </div>
                            @else
                                <span class="text-slate-300 transform scale-x-150 inline-block">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    <!-- Analytics Detail Modal -->
    <div id="analyticsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all opacity-0 pointer-events-none" style="opacity: 1; pointer-events: auto;">
        <div class="bg-white dark:bg-[#1f2937] rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-100 transition-transform relative border border-slate-100 dark:border-slate-700">
            <button onclick="closeAnalyticsModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors bg-slate-100 dark:bg-slate-700 rounded-full p-1 hover:bg-slate-200">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="flex flex-col items-center">
                <div id="modalIconContainer" class="mb-2">
                        <span id="modalIcon" class="material-symbols-outlined text-5xl mb-4">info</span>
                </div>
                <h3 id="modalTitle" class="text-2xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">Detail Analisa</h3>
                <div id="modalBody" class="text-slate-600 dark:text-slate-300">
                    <!-- Content -->
                </div>

                <button onclick="closeAnalyticsModal()" class="mt-8 btn-boss btn-primary w-full shadow-lg shadow-primary/20">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showAnalyticsModal(type, title, message, subtext = '', historyHtml = '') {
        const modal = document.getElementById('analyticsModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const modalIcon = document.getElementById('modalIcon');

        // Reset Classes
        modalIcon.className = 'material-symbols-outlined text-5xl mb-4';

        // Content
        modalTitle.innerText = title;
        modalBody.innerHTML = `<p class="font-bold text-lg leading-snug">${message}</p><p class="text-slate-500 dark:text-slate-400 text-sm mt-2">${subtext}</p>`;

        if (historyHtml && historyHtml.trim() !== '') {
             modalBody.innerHTML += `<div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 w-full text-left">
                <p class="text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Perjalanan Ranking:</p>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-sm space-y-1 font-mono text-slate-600 dark:text-slate-300">
                    ${historyHtml}
                </div>
            </div>`;
        }

        // Styling based on Type
        if (type === 'rising') {
            modalIcon.classList.add('text-emerald-500');
            modalIcon.innerText = 'rocket_launch';
        } else if (type === 'falling') {
            modalIcon.classList.add('text-rose-500');
            modalIcon.innerText = 'trending_down';
        } else if (type === 'anomaly') {
            modalIcon.classList.add('text-amber-500');
            modalIcon.innerText = 'warning';
        } else if (type === 'stable') {
            modalIcon.classList.add('text-blue-500');
            modalIcon.innerText = 'shield';
        } else {
                modalIcon.classList.add('text-primary');
                modalIcon.innerText = 'info';
        }

        // Show
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Ensure visibility properties are active
        modal.style.opacity = '1';
        modal.style.pointerEvents = 'auto';
    }

    function closeAnalyticsModal() {
        const modal = document.getElementById('analyticsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Fix hidden class issue on load
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('analyticsModal');
        if(modal) {
            modal.classList.add('hidden');
            modal.style.removeProperty('opacity'); // Allow CSS to handle it or keep it visible logic-wise
            // Actually, we want it hidden initially.
            // The HTML has style="opacity: 1; pointer-events: auto;" which overrides class hidden if not careful with CSS.
            // But Tailwind 'hidden' is display: none.
        }
    });
</script>
@endpush
