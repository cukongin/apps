@extends('layouts.app')

    @section('title', 'Dashboard Guru')

    @section('content')
    <div class="max-w-6xl mx-auto flex flex-col gap-8 pt-6">

            <!-- Welcome Section (Admin Style) -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="flex flex-col gap-2">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                        Selamat Datang, {{ explode(' ', $user->name)[0] }}
                        <span class="material-symbols-outlined text-amber-500 animate-pulse">waving_hand</span>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400">
                        Pantau progres penilaian siswa Tahun Ajaran <span class="font-bold text-primary">{{ $activeYear->nama ?? '-' }}</span>.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200 shadow-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Portal Guru
                    </span>
                    <span class="text-slate-400 text-sm font-medium">{{ date('d F Y') }}</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Classes -->
                <div class="relative group bg-gradient-to-br from-blue-50 to-white dark:from-slate-800 dark:to-slate-900 p-6 rounded-2xl border border-blue-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                         <span class="material-symbols-outlined text-[100px] text-blue-500">class</span>
                    </div>
                    <div class="relative z-10 flex flex-col gap-4">
                        <div class="size-14 rounded-2xl bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-lg shadow-blue-100 dark:shadow-none">
                             <span class="material-symbols-outlined text-3xl">class</span>
                        </div>
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">Total Kelas</p>
                            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-1">{{ $totalClasses }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Completed -->
                <div class="relative group bg-gradient-to-br from-emerald-50 to-white dark:from-slate-800 dark:to-slate-900 p-6 rounded-2xl border border-emerald-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                         <span class="material-symbols-outlined text-[100px] text-emerald-500">check_circle</span>
                    </div>
                    <div class="relative z-10 flex flex-col gap-4">
                        <div class="size-14 rounded-2xl bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-100 dark:shadow-none">
                             <span class="material-symbols-outlined text-3xl">check_circle</span>
                        </div>
                        <div>
                             <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">Selesai Dinilai</p>
                            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-1">{{ $classesCompleted }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="relative group bg-gradient-to-br from-orange-50 to-white dark:from-slate-800 dark:to-slate-900 p-6 rounded-2xl border border-orange-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                         <span class="material-symbols-outlined text-[100px] text-orange-500">pending_actions</span>
                    </div>
                    <div class="relative z-10 flex flex-col gap-4">
                        <div class="size-14 rounded-2xl bg-white dark:bg-slate-800 text-orange-500 dark:text-orange-400 flex items-center justify-center shadow-lg shadow-orange-100 dark:shadow-none">
                             <span class="material-symbols-outlined text-3xl">pending_actions</span>
                        </div>
                        <div>
                             <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">Belum Selesai</p>
                            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-1">{{ $pendingClasses }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class Groups -->
            <div class="flex flex-col gap-10">
                @foreach($classProgress as $className => $subjects)
                <div class="flex flex-col gap-5">
                    <!-- Class Header with Divider -->
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col">
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">school</span>
                                {{ $className }}
                            </h3>
                            <span class="text-sm font-medium text-slate-400 ml-8">{{ count($subjects) }} Mata Pelajaran</span>
                        </div>
                        <div class="h-[1px] flex-1 bg-gradient-to-r from-slate-200 to-transparent dark:from-slate-700"></div>
                    </div>

                    <!-- Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($subjects as $item)
                            @php
                                $statusColor = 'slate';
                                $progressBarColor = 'bg-primary';
                                $btnText = 'Input Nilai';
                                $btnClass = 'bg-gradient-to-r from-slate-800 to-slate-900 text-white hover:from-slate-900 hover:to-black dark:from-white dark:to-slate-200 dark:text-slate-900 dark:hover:to-slate-300';
                                $isDisabled = false;
                                $statusIcon = 'edit';
                                $cardBorderClass = 'border-slate-200 dark:border-slate-700';

                               if ($item->status == 'completed') {
                                    $statusColor = 'emerald';
                                    $btnText = 'Lihat / Edit';
                                    $progressBarColor = 'bg-emerald-500';
                                    $statusIcon = 'check';
                                    $btnClass = 'bg-white text-slate-900 border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-white dark:border-slate-700 dark:hover:bg-slate-700';
                                    $cardBorderClass = 'border-emerald-100 dark:border-emerald-900/50';
                                } elseif ($item->status == 'in_progress') {
                                    $statusColor = 'primary';
                                    $btnText = 'Lanjutkan';
                                    $btnClass = 'bg-gradient-to-r from-primary to-primary-dark text-white hover:shadow-lg hover:shadow-primary/30';
                                    $cardBorderClass = 'border-primary/20 ring-1 ring-primary/10';
                                } elseif ($item->status == 'locked') {
                                     $statusColor = 'slate';
                                }

                                if ($item->status == 'locked') {
                                    $btnText = 'Terkunci';
                                    $isDisabled = true;
                                    $btnClass = 'bg-slate-100 text-slate-400 cursor-not-allowed dark:bg-slate-800 dark:text-slate-600';
                                }
                            @endphp

                            <div class="group relative bg-white dark:bg-slate-800 rounded-3xl border {{ $cardBorderClass }} p-1 flex flex-col gap-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                <div class="w-full h-full bg-slate-50/50 dark:bg-slate-800/50 rounded-[20px] p-5 flex flex-col">

                                    <!-- Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex gap-4 items-center">
                                            <div class="size-14 rounded-2xl bg-white dark:bg-{{ $statusColor }}-900/20 text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400 flex items-center justify-center font-black text-xl shadow-sm border border-slate-100 dark:border-slate-700">
                                                {{ substr($item->mapel, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-bold text-slate-900 dark:text-white line-clamp-1 leading-tight" title="{{ $item->mapel }}">
                                                    {{ $item->mapel }}
                                                </h4>
                                                <p class="text-sm font-medium text-slate-500 mt-1">{{ explode(' ', $item->nama_kelas)[0] }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progress -->
                                    <div class="flex flex-col gap-2 mb-6">
                                        <div class="flex justify-between items-end">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Progress</span>
                                            <span class="text-xl font-black text-slate-800 dark:text-white">{{ $item->percentage }}<span class="text-sm text-slate-400 ml-0.5">%</span></span>
                                        </div>
                                        <div class="h-2.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full {{ $progressBarColor }} rounded-full relative transition-all duration-1000" style="width: {{ $item->percentage }}%">
                                                <div class="absolute inset-0 bg-white/20"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action -->
                                    <div class="mt-auto">
                                        @if(!$isDisabled)
                                        <a href="{{ route('teacher.input-nilai', ['kelas' => $item->id_kelas, 'mapel' => $item->id_mapel]) }}" class="w-full py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all {{ $btnClass }}">
                                            {{ $btnText }} <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                        </a>
                                        @else
                                        <button disabled class="w-full py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 {{ $btnClass }}">
                                            {{ $btnText }} <span class="material-symbols-outlined text-[18px]">lock</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Footer Info -->
            <div class="text-center text-xs text-slate-400 font-medium py-4">
                <p>&copy; {{ date('Y') }} Rapor Madrasah. All rights reserved.</p>
            </div>
    </div>
@endsection
