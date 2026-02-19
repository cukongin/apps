@extends('layouts.app')

@section('title', 'Sinkronisasi Data')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Sinkronisasi Data</h1>
            <p class="text-slate-500 text-sm mt-1">Tarik data terbaru dari Server Online ke Aplikasi Desktop.</p>
        </div>
    </div>

    {{-- Alert Block --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg flex items-start gap-3 shadow-sm">
            <span class="material-symbols-outlined filled text-emerald-600">check_circle</span>
            <div>
                <strong class="font-bold block">Sukses!</strong>
                <span class="text-sm">{{ session('success') }}</span>
            </div>
        </div>

        {{-- Data Summary Table --}}
        @if(session()->has('sync_summary') || session()->has('smart_sync_summary'))
        <div class="card-boss !p-0 overflow-hidden mt-4 mb-6 animate-fade-in-up">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">analytics</span>
                    Ringkasan Data
                </h4>
                <span class="text-xs font-bold px-2 py-1 rounded bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-500">
                    {{ now()->format('d M Y H:i:s') }}
                </span>
            </div>
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-100 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                            <th class="px-6 py-3">Jenis Data</th>
                            @if(session('smart_sync_summary'))
                                <th class="px-6 py-3 text-center w-24">Terkirim (Upload)</th>
                                <th class="px-6 py-3 text-center w-24">Diterima (Download)</th>
                            @else
                                <th class="px-6 py-3 text-center w-24">Baru</th>
                                <th class="px-6 py-3 text-center w-24">Update</th>
                                <th class="px-6 py-3 text-right w-24"><span class="opacity-50">Tetap</span></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @php $hasChanges = false; @endphp

                        {{-- Handle Smart Sync Summary --}}
                        @if(session('smart_sync_summary'))
                            @foreach(session('smart_sync_summary') as $key => $stats)
                                @php $hasChanges = true; @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group bg-blue-50/10">
                                    <td class="px-6 py-3 text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                        {{ $key }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-center">
                                        @if($stats['sent'] > 0)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-600 border border-indigo-200 flex items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-[10px]">arrow_upward</span>{{ $stats['sent'] }}
                                            </span>
                                        @else - @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-center">
                                        @if($stats['received'] > 0)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-600 border border-emerald-200 flex items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-[10px]">arrow_downward</span>{{ $stats['received'] }}
                                            </span>
                                        @else - @endif
                                    </td>
                                </tr>
                            @endforeach

                        {{-- Handle Full Sync Summary (Legacy) --}}
                        @elseif(session('sync_summary'))
                            @foreach(session('sync_summary') as $key => $stats)
                                {{-- Check if array (new format) or int (legacy format fallback) --}}
                                @if(is_array($stats))
                                    @php
                                        $inserted = $stats['inserted'] ?? 0;
                                        $updated = $stats['updated'] ?? 0;
                                        $unchanged = $stats['unchanged'] ?? 0;
                                        $totalChanged = $inserted + $updated;
                                        if($totalChanged > 0) $hasChanges = true;
                                    @endphp
                                    @if($totalChanged > 0)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group bg-emerald-50/10">
                                        <td class="px-6 py-3 text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                            {{ $key }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-center">
                                            @if($inserted > 0)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-600 border border-emerald-200">
                                                    +{{ $inserted }}
                                                </span>
                                            @else - @endif
                                        </td>
                                        <td class="px-6 py-3 text-sm text-center">
                                            @if($updated > 0)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-600 border border-amber-200">
                                                    {{ $updated }}
                                                </span>
                                            @else - @endif
                                        </td>
                                        <td class="px-6 py-3 text-sm text-right text-slate-400">
                                            {{ $unchanged }}
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            @endforeach
                        @endif

                        {{-- Show "No Changes" Row if empty --}}
                        @if(!$hasChanges)
                        <tr>
                            <td colspan="@if(session('smart_sync_summary')) 3 @else 4 @endif" class="px-6 py-8 text-center text-slate-500 italic">
                                <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">check_circle</span><br>
                                Tidak ada perubahan data. Semua data sudah sinkron.
                            </td>
                        </tr>
                        @endif

                        {{-- Collapse/Show All Toggle (Implementation simplified: Just showing changed items for now as requested) --}}
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-700 text-xs text-slate-500 flex justify-between">
                <span>Hanya menampilkan data yang mengalami perubahan.</span>
                <span>
                    @if(session('smart_sync_summary')) Total Update: {{ count(session('smart_sync_summary')) }}
                    @elseif(session('sync_summary')) Total Update: {{ count(session('sync_summary')) }} @endif
                </span>
            </div>
        </div>
        @endif
    @endif



    <!-- Connection Status Card -->
    <div class="card-boss !p-6">
        <h3 class="font-bold text-lg mb-4 text-slate-800 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">link</span>
            Status Koneksi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Info Config -->
            <div class="flex flex-col gap-4">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Server URL</p>
                    <p class="font-mono text-slate-700 dark:text-slate-300 select-all">{{ config('app.sync_base_url', env('SYNC_BASE_URL', 'Belum Diatur')) }}</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Sync Token</p>
                    <div class="flex items-center justify-between">
                        <p class="font-mono text-slate-700 dark:text-slate-300 truncate max-w-[200px]">
                            {{ Str::mask(config('app.sync_token', env('SYNC_TOKEN', 'Belum Diatur')), '*', 3) }}
                        </p>
                        <span class="text-xs {{ env('SYNC_TOKEN') ? 'text-emerald-500' : 'text-rose-500' }} font-bold bg-white dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                            {{ env('SYNC_TOKEN') ? 'Active' : 'Missing' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Area -->
            <div class="flex flex-col justify-center items-center bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800 p-6 text-center">
                @if(!env('SYNC_BASE_URL') || !env('SYNC_TOKEN'))
                    <span class="material-symbols-outlined text-4xl text-rose-500 mb-2">signal_disconnected</span>
                    <h4 class="font-bold text-slate-800 dark:text-white mb-2">Konfigurasi Belum Lengkap</h4>
                    <p class="text-sm text-slate-500 mb-4">Silakan atur <code>SYNC_BASE_URL</code> dan <code>SYNC_TOKEN</code> pada file <code>.env</code> aplikasi.</p>
                @else
                    <span class="material-symbols-outlined text-4xl text-blue-500 mb-2">sync_alt</span>
                    <h4 class="font-bold text-slate-800 dark:text-white mb-2">Smart Sync (Otomatis)</h4>
                    <p class="text-sm text-slate-500 mb-4">Satu tombol untuk kirim & tarik data sekaligus. Hanya data terbaru yang akan disimpan.</p>

                    <form action="{{ route('settings.sync.smart') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-boss btn-primary w-full md:w-auto text-lg px-8 py-3 shadow-lg hover:shadow-blue-500/30 transition-all" data-confirm-html="Proses ini akan <b>Menggabungkan Data</b> antara Laptop dan Server.<br>Data terbaru akan diambil (Last Update Wins).<br><br>Pastikan internet stabil.">
                            <span class="material-symbols-outlined text-2xl">published_with_changes</span>
                            Mulai Sinkronisasi Cerdas
                        </button>
                    </form>

                    <!-- Manual Options -->
                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700 w-full">
                        <button onclick="document.getElementById('manual-options').classList.toggle('hidden')" class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center justify-center gap-1 hover:text-slate-700 transition-colors">
                            Opsi Manual (Advanced) <span class="material-symbols-outlined text-sm">expand_more</span>
                        </button>

                        <div id="manual-options" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-xs text-slate-400 mb-2">Paksa Tarik (Server -> Laptop)</p>
                                <form action="{{ route('settings.sync.pull') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-boss btn-secondary w-full text-xs" data-confirm-html="Ini akan <b>menimpa data lokal</b> dengan data terbaru dari server online.<br>Lanjutkan?">
                                        <span class="material-symbols-outlined text-sm">download</span>
                                        Tarik FULL DATABASE
                                    </button>
                                </form>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 mb-2">Paksa Kirim (Laptop -> Server)</p>
                                <form action="{{ route('settings.sync.push') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-boss btn-secondary w-full text-xs" data-confirm-html="Ini akan <b>mengirim SELURUH DATABASE</b> dari laptop ini ke server online.<br>Pastikan internet stabil.">
                                        <span class="material-symbols-outlined text-sm">upload</span>
                                        Kirim FULL DATABASE
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Instruction Card -->
    <div class="card-boss !p-6">
        <h3 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Panduan Sinkronisasi</h3>
        <ul class="list-disc list-inside space-y-2 text-slate-600 dark:text-slate-400 text-sm">
            <li><strong>MODE: FULL DATABASE SYNC (Satu Database Utuh).</strong></li>
            <li>Sistem akan menyamakan <strong>SEMUA TABEL</strong> (Kecuali tabel sistem) antara Offline dan Online.</li>
            <li><strong>Tarik Data (Pull):</strong> Mengambil semua data dari Server Online ke Laptop ini.</li>
            <li><strong>Kirim Data (Push):</strong> Mengirim semua data dari Laptop ini ke Server Online.</li>
            <li>Proses ini mungkin memakan waktu tergantung kecepatan internet dan besarnya data.</li>
        </ul>
    </div>
</div>
@endsection
