<x-app-layout>
    <x-slot name="header">
        Manajemen Hak Akses
    </x-slot>

    <div class="max-w-[1440px] mx-auto flex flex-col lg:flex-row gap-6 px-6 py-8"
         x-data="{
            users: {{ Js::from($users) }},
            selectedUser: null,
            search: '',
            showCreateModal: {{ $errors->any() ? 'true' : 'false' }},
            activeTab: 'identity', // 'identity' or 'users'

            get filteredUsers() {
                if (this.search === '') return this.users;
                return this.users.filter(user => user.name.toLowerCase().includes(this.search.toLowerCase()) || user.email.toLowerCase().includes(this.search.toLowerCase()));
            },

            selectUser(user) {
                this.selectedUser = user;
                this.activeTab = 'users';
            },

            getRoleLabel(role) {
                const roles = {
                    'admin_utama': 'Admin Utama',
                    'kepala_madrasah': 'Kepala Madrasah',
                    'pengawas': 'Pengawas',
                    'bendahara': 'Bendahara (SPP & Keuangan)',
                    'teller_tabungan': 'Teller Tabungan',
                    'staf_keuangan': 'Staf Keuangan (Legacy)',
                    'staf_administrasi': 'Staf Administrasi'
                };
                return roles[role] || role;
            }
         }"
         x-init="
            if(users.length > 0) selectedUser = users[0];
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    text: 'Mohon periksa inputan Anda.',
                    confirmButtonColor: '#d33',
                    background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#545454'
                });
            @endif
         "
    >

        <!-- Left Sidebar: Menu & User List -->
        <aside class="w-full lg:w-80 flex flex-col gap-4 h-fit lg:sticky lg:top-24">

            <!-- Navigation Switcher -->
            <div class="bg-white dark:bg-[#1a2e1d] p-2 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm flex flex-col gap-2">
                <div class="flex gap-2">
                    <button @click="activeTab = 'identity'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition-all"
                        :class="activeTab === 'identity' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-[#233827]'">
                        <span class="material-symbols-outlined text-lg">settings_suggest</span> Identitas
                    </button>
                    <button @click="activeTab = 'users'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition-all"
                        :class="activeTab === 'users' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-[#233827]'">
                        <span class="material-symbols-outlined text-lg">group</span> Users
                    </button>
                </div>
                <a href="{{ route('keuangan.pengaturan.logs') }}" class="w-full py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-[#233827] border border-transparent hover:border-gray-200 transition-all">
                    <span class="material-symbols-outlined text-lg">history</span> Jejak Aktivitas
                </a>
                <a href="{{ route('keuangan.pengaturan.backup') }}" class="w-full py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 border border-transparent hover:border-green-200 transition-all">
                    <span class="material-symbols-outlined text-lg">database</span> Backup Database
                </a>

                <!-- Restore Section -->
                <div x-data="{ open: false }" class="w-full">
                    <button @click="open = !open" class="w-full py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 border border-transparent hover:border-red-200 transition-all">
                        <span class="material-symbols-outlined text-lg">restore</span> Restore Data
                    </button>

                    <div x-show="open" class="mt-2 p-3 bg-red-50 dark:bg-red-900/10 rounded-lg border border-red-100 dark:border-red-900/30 text-center" style="display: none;">
                        <p class="text-xs text-red-600 dark:text-red-400 mb-2 font-medium">⚠️ Data lama akan ditimpa!</p>
                        <form action="{{ route('keuangan.pengaturan.restore') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="backup_file" accept=".sql" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-red-100 file:text-red-700 hover:file:bg-red-200 mb-2" required>
                            <button type="submit" class="w-full bg-red-600 text-white text-xs font-bold py-1.5 rounded hover:bg-red-700 transition-colors" onclick="return confirm('Yakin ingin merestore? Data saat ini akan hilang permanen.')">
                                Upload & Restore
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Reset Database Section (Danger Zone) -->
                <div x-data="{ open: false }" class="w-full">
                    <button @click="open = !open" class="w-full py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 text-red-700 bg-red-100 hover:bg-red-200 border border-red-200 transition-all mt-1">
                        <span class="material-symbols-outlined text-lg">dangerous</span> Reset Database
                    </button>

                    <div x-show="open" class="mt-2 p-3 bg-red-50 dark:bg-red-900/10 rounded-lg border border-red-100 dark:border-red-900/30 text-center" style="display: none;">
                        <p class="text-xs text-red-600 dark:text-red-400 mb-2 font-bold uppercase">⚠️ PERINGATAN KERAS!</p>
                        <p class="text-[10px] text-red-500 mb-3 leading-tight">
                            Semua data Transaksi, Siswa, Kelas & Keuangan akan <b>DIHAPUS PERMANEN</b>.<br>
                            Data User (Login) & Pengaturan Aplikasi <b>TETAP AMAN</b>.
                        </p>
                        <form action="{{ route('keuangan.pengaturan.reset') }}" method="POST">
                            @csrf
                            <input type="text" name="confirm_reset" placeholder="Ketik RESET untuk konfirmasi" class="block w-full text-xs text-center border-red-300 focus:border-red-500 focus:ring-red-500 rounded-md mb-2 placeholder:text-gray-300" required>
                            <button type="submit" class="w-full bg-red-700 text-white text-xs font-bold py-1.5 rounded hover:bg-red-800 transition-colors shadow-sm">
                                YA, HAPUS SEMUA DATA
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Management Sidebar Content (Visible only when Users tab active) -->
            <div x-show="activeTab === 'users'" class="flex flex-col gap-4" x-transition>
                <!-- Search & Filter -->
                <div class="bg-white dark:bg-[#1a2e1d] p-4 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm">
                    <input type="text" x-model="search" placeholder="Cari user..." class="w-full rounded-lg border-none bg-gray-50 dark:bg-[#233827] text-sm focus:ring-2 focus:ring-primary h-10 px-4">
                    <button @click="showCreateModal = true" class="mt-3 w-full flex items-center justify-center gap-2 bg-primary text-[#111812] text-sm font-bold py-2.5 rounded-lg hover:opacity-90 transition-all shadow-md shadow-primary/20">
                        <span class="material-symbols-outlined text-lg">person_add</span> Tambah User
                    </button>
                </div>

                <!-- User List Container -->
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm overflow-hidden flex flex-col h-[500px]">
                    <div class="p-4 border-b border-[#e0e8e1] dark:border-[#2a3a2d] bg-gray-50 dark:bg-[#233827]">
                        <h3 class="font-bold text-xs text-[#618968] dark:text-[#a0c2a7] uppercase tracking-wider">Daftar Pengguna (<span x-text="filteredUsers.length"></span>)</h3>
                    </div>
                    <div class="overflow-y-auto flex-1 p-2 space-y-1">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <div @click="selectUser(user)"
                                class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all border border-transparent"
                                :class="selectedUser && selectedUser.id === user.id ? 'bg-primary/10 border-primary/20' : 'hover:bg-gray-50 dark:hover:bg-[#2a3a2d]'">

                                <div class="size-10 rounded-full bg-cover bg-center border border-gray-200 dark:border-gray-700 shrink-0"
                                     :style="user.foto ? `background-image: url('/storage/${user.foto}')` : 'background-image: url(\'https://ui-avatars.com/api/?name=' + user.name + '&background=random\')'">
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-[#111812] dark:text-white truncate" x-text="user.name"></p>
                                    <p class="text-xs text-[#618968] dark:text-[#a0c2a7] truncate" x-text="getRoleLabel(user.role)"></p>
                                </div>

                                <span x-show="selectedUser && selectedUser.id === user.id" class="material-symbols-outlined text-primary text-lg">chevron_right</span>
                            </div>
                        </template>

                        <div x-show="filteredUsers.length === 0" class="p-8 text-center text-gray-400">
                            <span class="material-symbols-outlined text-3xl mb-2 opacity-50">search_off</span>
                            <p class="text-sm">User tidak ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identity Info (Optional Preview or help text) -->
            <div x-show="activeTab === 'identity'" class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm text-center">
                <div class="size-20 mx-auto rounded-xl bg-gray-50 dark:bg-[#233827] flex items-center justify-center mb-3">
                     @if($setting['logo'])
                        <img src="{{ asset('storage/' . $setting['logo']) }}" class="max-w-full max-h-full rounded-lg" alt="Logo">
                     @else
                        <span class="material-symbols-outlined text-4xl text-gray-300">school</span>
                     @endif
                </div>
                <h3 class="font-bold text-[#111812] dark:text-white">{{ $setting['nama_sistem'] }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $setting['alamat'] ?: 'Alamat belum diatur' }}</p>
            </div>
        </aside>

        <!-- Main Content: Identity Form -->
        <main class="flex-1 flex flex-col gap-6" x-show="activeTab === 'identity'" x-transition>
            <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm">
                <h2 class="text-2xl font-black text-[#111812] dark:text-white leading-tight mb-2">Identitas Sistem</h2>
                <p class="text-[#618968] dark:text-[#a0c2a7] text-sm">Sesuaikan nama, logo, dan informasi instansi Anda.</p>

                <form action="{{ route('keuangan.pengaturan.identity.update') }}" method="POST" enctype="multipart/form-data" class="mt-8 flex flex-col gap-6">
                    @csrf

                    <div>
                        <x-label for="nama_sistem" value="Nama Aplikasi / Sekolah" />
                        <x-input id="nama_sistem" name="nama_sistem" type="text" class="mt-1 block w-full" value="{{ $setting['nama_sistem'] }}" required />
                        <p class="text-[10px] text-gray-400 mt-1">Muncul di Header dan Halaman Login</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                             <x-label for="no_telp" value="Nomor Telepon / WA" />
                             <x-input id="no_telp" name="no_telp" type="text" class="mt-1 block w-full" value="{{ $setting['no_telp'] }}" placeholder="08..." />
                        </div>
                        <div>
                             <x-label for="alamat" value="Alamat Lengkap" />
                             <textarea id="alamat" name="alamat" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary shadow-sm" rows="1">{{ $setting['alamat'] }}</textarea>
                        </div>
                    </div>

                    <div>
                        <x-label for="logo" value="Logo Instansi (Maks 2MB)" />
                        <div class="mt-2 flex items-center gap-4">
                            @if($setting['logo'])
                                <div class="size-16 rounded-lg border border-gray-200 p-1">
                                    <img src="{{ asset('storage/' . $setting['logo']) }}" class="w-full h-full object-contain rounded">
                                </div>
                            @endif
                            <input type="file" name="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all">
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('keuangan.pengaturan.storage.fix') }}" class="text-xs text-red-500 hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">build</span> Gambar tidak muncul di Hosting? Klik di sini untuk perbaiki.
                            </a>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                        <button type="submit" class="bg-primary text-[#111812] text-sm font-bold px-8 py-3 rounded-lg hover:shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined">save</span> Simpan Identitas
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Main Content: Edit User (Existing) -->
        <main class="flex-1 flex flex-col gap-6" x-show="activeTab === 'users' && selectedUser">
            <!-- Header -->
            <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm flex justify-between items-start">
            <!-- ... -->
                <div>
                    <h2 class="text-2xl font-black text-[#111812] dark:text-white leading-tight">Konfigurasi Perizinan</h2>
                    <p class="text-[#618968] dark:text-[#a0c2a7] text-sm mt-1">Kelola data dan hak akses untuk <span class="font-bold text-[#111812] dark:text-white" x-text="selectedUser.name"></span>.</p>
                </div>

                <form :action="'{{ route('keuangan.pengaturan.user.destroy', '') }}/' + selectedUser.id" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-sm transition-colors">
                        <span class="material-symbols-outlined text-lg">delete</span> Hapus User
                    </button>
                </form>
            </div>

            <!-- Edit Form -->
            <form :action="'{{ route('keuangan.pengaturan.user.update', '') }}/' + selectedUser.id" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                <!-- Profile Section -->
                <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm">
                    <h3 class="text-lg font-bold text-[#111812] dark:text-white mb-6 border-b border-[#e0e8e1] dark:border-[#2a3a2d] pb-4">Data Profil Pengguna</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2 flex items-center gap-6 mb-2">
                            <div class="size-20 rounded-full bg-cover bg-center border-2 border-primary/20 shadow-sm"
                                 :style="selectedUser.foto ? `background-image: url('/storage/${selectedUser.foto}')` : 'background-image: url(\'https://ui-avatars.com/api/?name=' + selectedUser.name + '&background=random\')'">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-[#111812] dark:text-white mb-1">Upload Foto Baru</label>
                                <input type="file" name="foto" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all">
                                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                            </div>
                        </div>

                        <div>
                            <x-label for="edit_name" value="Nama Lengkap" />
                            <x-input id="edit_name" name="name" type="text" class="mt-1 block w-full" x-model="selectedUser.name" required />
                        </div>

                        <div>
                            <x-label for="edit_email" value="Email Address" />
                            <x-input id="edit_email" name="email" type="email" class="mt-1 block w-full" x-model="selectedUser.email" required />
                        </div>

                        <div>
                            <x-label for="edit_password" value="Password Baru (Opsional)" />
                            <x-input id="edit_password" name="password" type="password" class="mt-1 block w-full" placeholder="Kosongkan jika tidak diubah" />
                        </div>

                         <div>
                            <x-label for="edit_role" value="Role / Jabatan" />
                            <select id="edit_role" name="role" x-model="selectedUser.role" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                                <option value="admin_utama">Admin Utama (Super Admin)</option>
                                <option value="kepala_madrasah">Kepala Madrasah</option>
                                <option value="bendahara">Bendahara (SPP & Keuangan)</option>
                                <option value="teller_tabungan">Teller Tabungan</option>
                                <option value="staf_administrasi">Staf Administrasi (TU)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Permission Visuals (Mapped to Role) -->
                <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm">
                     <div class="flex justify-between items-center border-b border-[#e0e8e1] dark:border-[#2a3a2d] pb-4 mb-6">
                        <h3 class="text-lg font-bold text-[#111812] dark:text-white">Akses Modul</h3>
                        <div class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-semibold border border-yellow-200">
                             Diatur otomatis berdasarkan Role
                        </div>
                    </div>

                    <!-- Permissions Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 opacity-75 pointer-events-none">
                        <!-- Module Laporan -->
                        <div class="p-4 rounded-lg border border-[#e0e8e1] dark:border-[#2a3a2d] bg-gray-50 dark:bg-[#233827]">
                            <div class="flex items-center gap-2 mb-3 text-primary">
                                <span class="material-symbols-outlined">analytics</span>
                                <h4 class="font-bold text-sm uppercase">Laporan & Analitik</h4>
                            </div>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" class="rounded text-primary focus:ring-primary" :checked="['admin_utama', 'bendahara', 'kepala_madrasah'].includes(selectedUser.role)" disabled>
                                    <span class="text-sm">Lihat Laporan Tahunan</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" class="rounded text-primary focus:ring-primary" :checked="['admin_utama', 'bendahara'].includes(selectedUser.role)" disabled>
                                    <span class="text-sm">Ekspor Data (PDF/Excel)</span>
                                </label>
                            </div>
                        </div>

                         <!-- Module Keuangan -->
                        <div class="p-4 rounded-lg border border-[#e0e8e1] dark:border-[#2a3a2d] bg-gray-50 dark:bg-[#233827]">
                            <div class="flex items-center gap-2 mb-3 text-primary">
                                <span class="material-symbols-outlined">payments</span>
                                <h4 class="font-bold text-sm uppercase">Keuangan & Transaksi</h4>
                            </div>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" class="rounded text-primary focus:ring-primary" :checked="['admin_utama', 'bendahara'].includes(selectedUser.role)" disabled>
                                    <span class="text-sm">Input Pembayaran SPP</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" class="rounded text-primary focus:ring-primary" :checked="['admin_utama', 'teller_tabungan'].includes(selectedUser.role)" disabled>
                                    <span class="text-sm">Input Tabungan</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                     <button type="submit" class="bg-primary text-[#111812] text-sm font-bold px-8 py-3 rounded-lg hover:shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span> Simpan Perubahan
                    </button>
                </div>
            </form>
        </main>

        <!-- Empty State (No User Selected) -->
        <main class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] border-dashed" x-show="activeTab === 'users' && !selectedUser">
             <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">person_search</span>
             <h3 class="text-xl font-bold text-gray-500">Pilih User</h3>
             <p class="text-gray-400">Pilih pengguna dari daftar di sebelah kiri untuk mengedit hak akses.</p>
        </main>
    <!-- Create User Modal -->
    <div x-show="showCreateModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition.opacity
         style="display: none;">

        <div @click.away="showCreateModal = false" class="bg-white dark:bg-[#1a2e1d] rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-[#e0e8e1] dark:border-[#2a3a2d] flex justify-between items-center">
                <h3 class="text-xl font-bold text-[#111812] dark:text-white">Tambah Pengguna Baru</h3>
                 <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('keuangan.pengaturan.user.store') }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-6 flex flex-col gap-4">
                @csrf
                <div>
                    <x-label for="name" value="Nama Lengkap" />
                    <x-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                </div>

                <div>
                    <x-label for="email" value="Email" />
                    <x-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                </div>

                 <div>
                    <x-label for="role" value="Role / Jabatan" />
                    <select id="role" name="role" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="admin_utama">Admin Utama (Super Admin)</option>
                        <option value="kepala_madrasah">Kepala Madrasah</option>
                        <option value="bendahara">Bendahara (SPP & Keuangan)</option>
                        <option value="teller_tabungan">Teller Tabungan</option>
                        <option value="staf_administrasi">Staf Administrasi (TU)</option>
                    </select>
                </div>

                <div>
                    <x-label for="password" value="Password" />
                    <x-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                </div>

                <div>
                    <x-label for="foto" value="Foto Profil (Opsional)" />
                     <input type="file" name="foto" id="foto" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all mt-1">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary text-[#111812] font-bold py-3 rounded-lg hover:opacity-90 transition-all shadow-md shadow-primary/20">
                        Simpan User Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>

