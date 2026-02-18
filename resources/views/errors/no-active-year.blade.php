@extends('layouts.app')

@section('title', 'Tahun Ajaran Tidak Aktif')

@section('content')
<div class="min-h-[calc(100vh-100px)] flex items-center justify-center p-4">
    <div class="card-boss w-full max-w-lg p-8 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] dark:[mask-image:linear-gradient(0deg,rgba(255,255,255,0.1),rgba(255,255,255,0.5))] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center">
            <div class="size-20 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-6 animate-pulse">
                <span class="material-symbols-outlined text-4xl text-red-500">calendar_off</span>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-3">Tahun Ajaran Belum Aktif</h1>
            <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                Sistem tidak dapat menemukan <strong>Tahun Ajaran Aktif</strong>.
                <br>Mohon hubungi Administrator untuk melakukan konfigurasi.
            </p>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('settings.index') }}" class="btn-boss btn-primary w-full justify-center">
                <span class="material-symbols-outlined text-[20px]">settings_suggest</span>
                Ke Pengaturan
            </a>
            @else
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="btn-boss btn-secondary w-full justify-center text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 border-red-200 dark:border-red-900/30">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Keluar Aplikasi
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

