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
                            Tarik Data Sekarang
                        </button>
                    </form>

                    <!-- PUSH BUTTON (New) -->
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 w-full">
                        <p class="text-xs text-slate-500 mb-2">Pusat Data: Laptop (Kirim data offline ke server)</p>
                        <form action="{{ route('settings.sync.push') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-boss btn-secondary w-full md:w-auto" data-confirm-html="Ini akan <b>mengirim semua data keuangan</b> dari laptop ini ke server online.<br>Pastikan internet stabil.">
                                <span class="material-symbols-outlined">upload</span>
                                Kirim Data ke Server
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
            <li>Sinkronisasi ini bersifat <strong>Satu Arah (Online ke Offline)</strong>.</li>
            <li>Data yang ditarik meliputi: <strong>Siswa, Guru, Kelas, Mapel, dan Nilai/Absensi</strong>.</li>
            <li>Data di laptop ini akan diperbarui sesuai data di server. Data lokal yang tidak ada di server <strong>tidak akan dihapus</strong> (hanya update/insert).</li>
            <li>Gunakan fitur ini secara berkala, misalnya seminggu sekali atau saat musim ujian.</li>
        </ul>
    </div>
</div>
@endsection
