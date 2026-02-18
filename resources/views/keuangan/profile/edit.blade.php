<x-app-layout>
    <x-slot name="header">
        Profil Saya
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">
        <!-- Update Profile Info & Photo -->
        <div class="p-6 bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#dbe6dd] dark:border-[#2a3a2d] shadow-sm">
            <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">Informasi Profil</h2>
            
            <form action="{{ route('keuangan.profile.update') }}" method="post" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('patch')

                <div class="flex items-center gap-6">
                    <div class="shrink-0">
                        <img id="preview-foto" class="h-20 w-20 object-cover rounded-full border border-gray-200" 
                             src="{{ $user->foto ? asset('storage/'.$user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                             alt="Current profile photo" />
                    </div>
                    <label class="block">
                        <span class="sr-only">Choose profile photo</span>
                        <input type="file" name="foto" onchange="document.getElementById('preview-foto').src = window.URL.createObjectURL(this.files[0])"
                               class="block w-full text-sm text-slate-500
                                 file:mr-4 file:py-2 file:px-4
                                 file:rounded-full file:border-0
                                 file:text-sm file:font-semibold
                                 file:bg-primary/10 file:text-primary
                                 hover:file:bg-primary/20
                               "/>
                    </label>
                </div>

                <div>
                    <x-label for="name" value="Nama Lengkap" />
                    <x-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
                </div>

                <div>
                    <x-label for="email" value="Email" />
                    <x-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-primary text-[#111812] font-bold py-2 px-4 rounded-lg shadow-md hover:brightness-110 transition-all">
                        Simpan Profil
                    </button>
                    @if (session('success') && !str_contains(session('success'), 'Password'))
                        <p class="text-sm text-green-600">{{ session('success') }}</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="p-6 bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#dbe6dd] dark:border-[#2a3a2d] shadow-sm">
            <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">Update Password</h2>
            
            <form action="{{ route('keuangan.profile.password') }}" method="post" class="space-y-4">
                @csrf
                @method('put')

                <div>
                    <x-label for="current_password" value="Password Saat Ini" />
                    <x-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div>
                    <x-label for="password" value="Password Baru" />
                    <x-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-label for="password_confirmation" value="Konfirmasi Password Baru" />
                    <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                     <button type="submit" class="bg-gray-800 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-gray-700 transition-all">
                        Ubah Password
                    </button>
                    @if (session('success') && str_contains(session('success'), 'Password'))
                        <p class="text-sm text-green-600">{{ session('success') }}</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

