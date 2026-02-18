<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Rapor Madrasah</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Compiled CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background Decor -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-primary/20 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-500/10 rounded-full blur-[120px] animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-[30%] left-[30%] w-[30%] h-[30%] bg-emerald-400/10 rounded-full blur-[80px] animate-float" style="animation-delay: 4s;"></div>
    </div>

    <!-- Main Content -->
    <div class="w-full max-w-sm glass-card rounded-3xl shadow-2xl shadow-slate-200/50 relative z-10 overflow-hidden">
        <!-- Top Banner -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-primary via-indigo-500 to-emerald-400"></div>

        <div class="p-8 pb-6">
            <div class="flex flex-col items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-primary to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary/30 transform rotate-3 hover:rotate-6 transition-transform duration-300">
                    <span class="material-symbols-outlined text-white text-5xl">school</span>
                </div>
                <div class="text-center">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">E-Rapor Madrasah</h1>
                    <p class="text-sm font-medium text-slate-500 mt-1">Sistem Informasi Akademik Terpadu</p>
                </div>
            </div>

            @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-600 text-sm p-4 rounded-xl flex items-start gap-3 shadow-sm">
                <span class="material-symbols-outlined text-xl shrink-0">error</span>
                <ul class="list-disc list-inside text-xs font-bold space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('login.admin.post') }}" method="POST" class="flex flex-col gap-5">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Email Address</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors">mail</span>
                        <input type="email" name="email" class="w-full bg-slate-50 rounded-xl border-slate-200 pl-10 pr-4 py-2.5 text-sm font-bold text-slate-800 placeholder-slate-400 focus:border-primary focus:ring-primary focus:bg-white transition-all shadow-sm" placeholder="admin@madrasah.com" value="{{ old('email') }}" required>
                    </div>
                </div>

                <!-- Honeypot for Bots (Hidden) -->
                <div class="hidden">
                    <label>Don't fill this out if you're human: <input type="text" name="website" value="{{ old('website') }}" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors">lock</span>
                        <input type="password" name="password" class="w-full bg-slate-50 rounded-xl border-slate-200 pl-10 pr-4 py-2.5 text-sm font-bold text-slate-800 placeholder-slate-400 focus:border-primary focus:ring-primary focus:bg-white transition-all shadow-sm" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" class="peer h-4 w-4 cursor-pointer appearance-none rounded border border-slate-300 transition-all checked:border-primary checked:bg-primary">
                            <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 pointer-events-none">
                                <span class="material-symbols-outlined text-[12px]">check</span>
                            </span>
                        </div>
                        <span class="text-xs font-bold text-slate-500 group-hover:text-primary transition-colors">Ingat Saya</span>
                    </label>
                    <a href="#" class="text-xs font-bold text-primary hover:text-indigo-600 transition-colors">Lupa Password?</a>
                </div>

                <button type="submit" class="mt-2 bg-gradient-to-r from-primary to-indigo-600 hover:from-primary-dark hover:to-indigo-700 text-white font-black py-3.5 rounded-xl shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2.5 group">
                    <span class="material-symbols-outlined group-hover:animate-pulse">login</span>
                    MASUK ADMIN
                </button>
            </form>

            <div class="mt-8 text-center relative">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-2 text-slate-400 font-bold tracking-wider uppercase">Atau masuk dengan</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 font-bold py-3 rounded-xl transition-all hover:shadow-md group">
                    <div class="w-6 h-6 bg-white rounded-md border border-slate-200 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-slate-800 text-sm">key</span>
                    </div>
                    <span>Kode Akses / PIN</span>
                </a>
            </div>
        </div>
        <div class="bg-slate-50/50 border-t border-slate-100 p-4 text-center backdrop-blur-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">&copy; {{ date('Y') }} Integrated System MI & MTs</p>
        </div>
    </div>

</body>
</html>
