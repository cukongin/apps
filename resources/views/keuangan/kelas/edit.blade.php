<x-app-layout>
    <x-slot name="header">
        Edit Data Kelas
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('keuangan.kelas.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold">Edit Data Kelas</h1>
        </div>

        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm p-6 md:p-8">
            <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kelas -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ $kelas->nama }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                    </div>

                    <!-- Tingkatan -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Tingkatan (Level) <span class="text-red-500">*</span></label>
                        <select name="level_id" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl->id }}" {{ $kelas->level_id == $lvl->id ? 'selected' : '' }}>{{ $lvl->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Info Wali -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">Wali Kelas</label>
                    <input type="text" name="wali" value="{{ $kelas->wali_kelas }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-slate-50 dark:bg-[#233827] focus:ring-2 focus:ring-primary focus:border-primary font-medium">
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 mt-4 border-t border-[#dbe0e6] dark:border-[#2a452e]">
                    <a href="{{ route('keuangan.kelas.index') }}" class="px-6 py-2.5 rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] text-[#617589] dark:text-gray-300 font-bold hover:bg-slate-50 dark:hover:bg-[#233827] transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold shadow-lg shadow-primary/20 hover:brightness-110 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Perbarui Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

