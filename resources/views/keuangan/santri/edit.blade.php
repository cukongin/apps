<x-app-layout>
    <x-slot name="header">
        Edit Data Siswa
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('keuangan.santri.show', $santri->id) }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold">Edit Data Siswa</h1>
        </div>

        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm p-6 md:p-8">
            <form action="{{ route('keuangan.santri.update', $santri->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIS -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">NIS (Nomor Induk Santri)</label>
                        <input type="text" name="nis" value="{{ $santri->nis }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ $santri->nama }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                    </div>

                    <!-- Kelas -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Kelas</label>
                        <select name="kelas_id" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                            <option value="">Belum Ada Kelas</option>
                            @foreach($levels as $levelName => $classes)
                                <optgroup label="{{ $levelName }}">
                                    @foreach($classes as $id => $nama)
                                        <option value="{{ $id }}" {{ $santri->kelas_id == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                            <option value="L" {{ $santri->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $santri->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- Status Santri -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Status Siswa</label>
                        <select name="status" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                            <option value="Aktif" {{ $santri->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Lulus" {{ $santri->status == 'Lulus' ? 'selected' : '' }}>Lulus (Alumni)</option>
                            <option value="Pindah" {{ $santri->status == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="Berhenti" {{ $santri->status == 'Berhenti' ? 'selected' : '' }}>Berhenti/Drop Out</option>
                        </select>
                    </div>

                <div class="pt-6 border-t border-[#dbe0e6] dark:border-[#2a452e] grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                         <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Foto Profil Santri</label>

                         <div class="mb-3">
                             <img id="preview-img" src="{{ $santri->foto ? asset('storage/' . $santri->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($santri->nama) . '&background=random' }}"
                                  alt="Preview" class="h-24 w-24 rounded-full object-cover border-2 border-primary/20 shadow-sm">
                         </div>

                         <input type="file" name="foto" accept="image/*" onchange="document.getElementById('preview-img').src = window.URL.createObjectURL(this.files[0])"
                                class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                         <p class="text-xs text-slate-400">Biarkan kosong jika tidak ingin mengubah foto.</p>
                    </div>
                </div>
                </div>

                <!-- Info Wali -->
                <div class="pt-6 border-t border-[#dbe0e6] dark:border-[#2a452e] grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Nama Wali Murid</label>
                        <input type="text" name="nama_wali" value="{{ $santri->nama_wali }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Nomor WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ $santri->no_hp }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6">
                    <a href="{{ route('keuangan.santri.index') }}" class="px-6 py-2.5 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] text-[#617589] dark:text-gray-300 font-bold hover:bg-slate-50 dark:hover:bg-[#233827] transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold shadow-lg shadow-primary/20 hover:brightness-110 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

