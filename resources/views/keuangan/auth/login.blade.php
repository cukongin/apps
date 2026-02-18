<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#f4f7f5] dark:bg-[#111812] relative overflow-hidden">
        
        <!-- Decorative Background Elements -->
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-[#078825]/5 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-[0%] -right-[10%] w-[40%] h-[40%] rounded-full bg-[#078825]/10 blur-3xl pointer-events-none"></div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white dark:bg-[#1a2e1d] shadow-xl shadow-[#078825]/5 overflow-hidden sm:rounded-2xl border border-[#dbe6dd] dark:border-[#2a3a2d] relative z-10">
            
            <!-- Header / Logo -->
            <div class="flex flex-col items-center mb-8">
                <a href="/" class="flex flex-col items-center gap-2 group">
                    @php
                        $logo = \App\Models\Setting::get('logo');
                        $nama_sistem = \App\Models\Setting::get('nama_sistem', 'Sistem Pesantren');
                    @endphp

                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="h-16 w-auto object-contain drop-shadow-lg group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="size-16 bg-[#078825] rounded-2xl flex items-center justify-center shadow-lg shadow-[#078825]/30 group-hover:scale-105 transition-transform duration-300">
                            <span class="material-symbols-outlined text-white text-4xl">school</span>
                        </div>
                    @endif
                    
                    <h1 class="text-2xl font-black text-[#111812] dark:text-white mt-4 tracking-tight text-center">{{ $nama_sistem }}</h1>
                    <p class="text-sm text-[#618968] dark:text-[#a0c0a5] font-medium">Masuk untuk mengelola data</p>
                </a>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-bold text-[#111812] dark:text-gray-200 mb-2 pl-1">Email / Username</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#618968]">mail</span>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#dbe6dd] dark:border-[#2a3a2d] bg-[#fcfdfc] dark:bg-[#1a2e1e] text-[#111812] dark:text-white placeholder-[#618968]/50 focus:border-[#078825] focus:ring-2 focus:ring-[#078825]/20 transition-all shadow-sm"
                            placeholder="admin@pesantren.com">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2 pl-1">
                        <label for="password" class="block text-sm font-bold text-[#111812] dark:text-gray-200">Password</label>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#618968]">lock</span>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#dbe6dd] dark:border-[#2a3a2d] bg-[#fcfdfc] dark:bg-[#1a2e1e] text-[#111812] dark:text-white placeholder-[#618968]/50 focus:border-[#078825] focus:ring-2 focus:ring-[#078825]/20 transition-all shadow-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#078825] shadow-sm focus:border-[#078825] focus:ring focus:ring-[#078825]/20" name="remember">
                        <span class="ml-2 text-sm text-[#618968] group-hover:text-[#078825] transition-colors">{{ __('Ingat Saya') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm font-bold text-[#078825] hover:underline" href="{{ route('password.request') }}">
                            Lupa Password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="w-full py-3 bg-[#078825] hover:bg-[#06701f] text-white font-black rounded-xl shadow-lg shadow-[#078825]/20 hover:shadow-[#078825]/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-2">
                    <span>Masuk Aplikasi</span>
                    <span class="material-symbols-outlined text-lg">login</span>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-xs text-[#618968] dark:text-[#a0c0a5]">
                    &copy; {{ date('Y') }} Sistem Informasi Manajemen Pesantren
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>

