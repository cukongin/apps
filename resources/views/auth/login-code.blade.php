<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kode Akses - E-Rapor Madrasah</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Compiled CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .animate-float {
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 antialiased h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background Decor -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] bg-emerald-500/10 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[60%] h-[60%] bg-indigo-500/10 rounded-full blur-[120px] animate-float" style="animation-delay: 2s;"></div>

        <!-- Grid Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-20"></div>
    </div>

    <!-- Main Content -->
    <div class="w-full max-w-sm glass-card !bg-slate-800/80 !border-slate-700/50 rounded-3xl shadow-2xl shadow-black/50 relative z-10 overflow-hidden" x-data="{ code: '', isLoading: false }">
        <!-- Top Banner -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 via-teal-500 to-cyan-500"></div>

        <div class="p-8 pb-6">
            <div class="flex flex-col items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-700 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 mb-2">
                    <span class="material-symbols-outlined text-white text-5xl">lock_open</span>
                </div>
                <div class="text-center">
                    <h1 class="text-2xl font-black text-white tracking-tight">Akses Cepat</h1>
                    <p class="text-sm font-medium text-slate-400 mt-1">Masukkan 6-digit kode akses Anda</p>
                </div>
            </div>

            @if ($errors->any())
            <div class="mb-6 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm p-4 rounded-xl flex items-start gap-3 shadow-sm backdrop-blur-sm">
                <span class="material-symbols-outlined text-xl shrink-0">error</span>
                <ul class="list-disc list-inside text-xs font-bold space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" @submit="isLoading = true" class="flex flex-col gap-6">
                @csrf
                <div class="space-y-2">
                    <div class="relative">
                        <input type="text" name="access_code" x-model="code"
                               class="w-full bg-slate-900/50 rounded-2xl border-2 border-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 text-center text-3xl font-mono font-bold tracking-[0.5em] text-white placeholder-slate-600 py-4 transition-all shadow-inner outline-none uppercase"
                               placeholder="******" maxlength="6" autofocus required autocomplete="off">

                        <div class="absolute right-4 top-1/2 -translate-y-1/2">
                            <span class="material-symbols-outlined text-emerald-500 animate-pulse" x-show="code.length === 6">check_circle</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-center text-slate-500 font-bold uppercase tracking-wider">Kode Akses Rahasia</p>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black py-4 rounded-xl shadow-lg shadow-emerald-500/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2 group disabled:opacity-70 disabled:cursor-not-allowed"
                        :disabled="isLoading">
                    <span x-show="!isLoading" class="material-symbols-outlined group-hover:animate-bounce">login</span>
                    <span x-show="isLoading" class="material-symbols-outlined animate-spin">sync</span>
                    <span x-text="isLoading ? 'MEMVERIFIKASI...' : 'MASUK SEKARANG'"></span>
                </button>
            </form>

            <div class="mt-8 text-center relative">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-700"></div></div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-slate-800 px-3 text-slate-500 font-bold tracking-wider uppercase backdrop-blur-xl rounded-full">Opsi Lain</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('login.admin') }}" class="flex items-center justify-center gap-2 w-full bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 text-slate-300 font-bold py-3 rounded-xl transition-all hover:shadow-lg hover:text-white group">
                    <span class="material-symbols-outlined text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    <span>Login sebagai Admin</span>
                </a>
            </div>
        </div>
        <div class="bg-slate-900/50 border-t border-slate-700/50 p-4 text-center backdrop-blur-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">&copy; {{ date('Y') }} Integrated System</p>
        </div>
    </div>

</body>
</html>
