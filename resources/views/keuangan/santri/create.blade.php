<x-app-layout>
    <x-slot name="header">
        Tambah Data Siswa
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('keuangan.santri.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold">Tambah Siswa Baru</h1>
        </div>

        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm p-6 md:p-8">
            <form action="{{ route('keuangan.santri.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIS -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">NIS (Nomor Induk Santri) <span class="text-red-500">*</span></label>
                        <input type="text" name="nis" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" placeholder="Contoh: 2023001" required>
                    </div>
                    
                    <!-- Nama Lengkap -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" placeholder="Nama lengkap siswa" required>
                    </div>

                    <!-- Kelas (Includes Level Logic via OptGroups) -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Kelas <span class="text-red-500">*</span></label>
                        <select name="kelas_id" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium">
                            <option value="">Belum Ada Kelas</option>
                            @foreach($levels as $levelName => $classes)
                                <optgroup label="{{ $levelName }}">
                                    @foreach($classes as $id => $nama)
                                        <option value="{{ $id }}">{{ $nama }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Level Input Removed: Extracted from Class automatically -->

                    <!-- Jenis Kelamin -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="gender" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                            <option value="">Pilih Gender</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <!-- Kategori Keringanan -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Kategori Keringanan (Beasiswa)</label>
                        <select name="kategori_keringanan_id" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium">
                            <option value="">Tidak Ada (Bayar Penuh)</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400">Pilih jika siswa mendapat subsidi (Misal: Yatim, Anak Guru).</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-[#dbe0e6] dark:border-[#2a452e] grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                         <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Foto Profil Santri</label>
                         
                         <div class="mb-3 hidden" id="preview-container">
                             <img id="preview-img" src="#" alt="Preview" class="h-24 w-24 rounded-full object-cover border-2 border-primary/20 shadow-sm">
                         </div>

                         <input type="file" name="foto" accept="image/*" onchange="
                            const preview = document.getElementById('preview-img');
                            const container = document.getElementById('preview-container');
                            if(this.files[0]) {
                                preview.src = window.URL.createObjectURL(this.files[0]);
                                container.classList.remove('hidden');
                            }
                         " 
                         class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3a2d] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                         <p class="text-xs text-slate-400">Format: JPG, PNG, JPEG. Maksimal 2MB.</p>
                    </div>
                </div>

                <!-- Info Wali -->
                <div class="pt-6 border-t border-[#dbe0e6] dark:border-[#2a452e] grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Nama Wali Murid</label>
                        <input type="text" name="nama_wali" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" placeholder="Nama orang tua/wali">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Nomor WhatsApp</label>
                        <input type="text" name="no_hp" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" placeholder="Format: 628xxx">
                        <p class="text-xs text-slate-400">Gunakan format internasional (62) untuk fitur kirim WA.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6">
                    <button type="reset" class="px-6 py-2.5 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] text-[#617589] dark:text-gray-300 font-bold hover:bg-slate-50 dark:hover:bg-[#233827] transition-all">
                        Reset
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold shadow-lg shadow-primary/20 hover:brightness-110 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

