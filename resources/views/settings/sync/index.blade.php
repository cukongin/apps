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
        @if(session('sync_summary'))
        <div class="card-boss !p-0 overflow-hidden mt-4 mb-6 animate-fade-in-up">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">analytics</span>
                    Ringkasan Data
                </h4>
                <span class="text-xs font-bold px-2 py-1 rounded bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-500">
                    {{ now()->format('d M Y H:i') }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50 dark:bg-slate-700/20 border-b border-slate-100 dark:border-slate-700">
                            <th class="px-6 py-3">Jenis Data</th>
                            <th class="px-6 py-3 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach(session('sync_summary') as $key => $count)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                            <td class="px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors"></div>
                                {{ $key }}
                            </td>
                            <td class="px-6 py-3 text-sm text-right">
                                <span class="inline-flex items-center justify-center min-w-[30px] px-2 py-0.5 rounded-full text-xs font-bold {{ $count > 0 ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-slate-100 text-slate-400 border border-slate-200 dark:bg-slate-800 dark:border-slate-700' }}">
                                    {{ $count }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-700 text-xs text-slate-500 text-center">
                Data di atas telah berhasil diproses oleh sistem.
            </div>
        </div>
        @endif
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
                <b>Gagal:</b> {{ session('error') }}
            </div>
        </div>
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
                    <span class="material-symbols-outlined text-4xl text-blue-500 mb-2">cloud_download</span>
                    <h4 class="font-bold text-slate-800 dark:text-white mb-2">Siap Sinkronisasi</h4>
                    <p class="text-sm text-slate-500 mb-4">Pastikan internet lancar sebelum menarik data.</p>

                    <form action="{{ route('settings.sync.pull') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-boss btn-primary w-full md:w-auto" data-confirm-html="Ini akan <b>menimpa data lokal</b> dengan data terbaru dari server online.<br>Lanjutkan?">
                            <span class="material-symbols-outlined">download</span>
                            Tarik FULL DATABASE Sekarang
                        </button>
                    </form>

                    <!-- PUSH BUTTON (New) -->
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 w-full">
                        <p class="text-xs text-slate-500 mb-2">Pusat Data: Laptop (Kirim data offline ke server)</p>
                        <form action="{{ route('settings.sync.push') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-boss btn-secondary w-full md:w-auto" data-confirm-html="Ini akan <b>mengirim SELURUH DATABASE</b> dari laptop ini ke server online.<br>Pastikan internet stabil.">
                                <span class="material-symbols-outlined">upload</span>
                                Kirim FULL DATABASE ke Server
                            </button>
                        </form>
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
