@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">account_circle</span>
                Profil Saya
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">Kelola informasi akun dan keamanan Anda.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-2 shadow-sm" role="alert">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="card-boss !p-6 flex flex-col items-center text-center h-full">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary to-indigo-600 p-1 shadow-xl shadow-primary/20 mb-4">
                    <div class="w-full h-full rounded-full bg-white dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                        @if(auth()->user()->profile_photo_url)
                             <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-5xl font-black text-primary">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ auth()->user()->name }}</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-bold mb-4">{{ auth()->user()->email }}</p>

                <div class="w-full mt-auto pt-6 border-t border-slate-100 dark:border-slate-700">
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-slate-500">Role</span>
                        <span class="font-bold text-slate-800 dark:text-white uppercase">{{ auth()->user()->role ?? 'User' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Bergabung</span>
                        <span class="font-bold text-slate-800 dark:text-white">{{ auth()->user()->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <div class="card-boss !p-8">
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-3 mb-6">
                         <span class="material-symbols-outlined text-slate-400">badge</span>
                        Informasi Dasar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- Name -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors">person</span>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-boss !pl-10" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors">mail</span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-boss !pl-10" required>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-3 mb-6 pt-4">
                        <span class="material-symbols-outlined text-slate-400">lock</span>
                        Keamanan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password (Optional) -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Password Baru</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors">key</span>
                                <input type="password" name="password" class="input-boss !pl-10" placeholder="Kosongkan jika tidak ubah">
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Konfirmasi Password</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-primary transition-colors">check_circle</span>
                                <input type="password" name="password_confirmation" class="input-boss !pl-10" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button type="submit" class="btn-boss bg-primary hover:bg-primary-dark text-white px-8 py-3 flex items-center gap-2 shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
