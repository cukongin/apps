<x-app-layout>
    <x-slot name="header">
        Preview Import Data Santri
    </x-slot>

    <div class="max-w-[1440px] mx-auto p-6">
        <form action="{{ route('keuangan.santri.import.execute') }}" method="POST" class="flex flex-col gap-6">
            @csrf
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-[#1a2e1d] p-6 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm">
                <div>
                    <h1 class="text-2xl font-black text-[#111812] dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">data_check</span>
                        Preview Import Data
                    </h1>
                    <p class="text-[#618968] dark:text-[#a0c2a7] mt-1">Silakan periksa dan edit data sebelum disimpan ke database.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('keuangan.santri.index') }}" class="px-6 py-2.5 rounded-lg font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-[#233827] transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-[#111812] font-bold shadow-lg shadow-primary/20 hover:brightness-110 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span>
                        Proses & Simpan ({{ count($data) }} Data)
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-[#233827] text-xs uppercase text-gray-500 font-bold sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-12 text-center">No</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-32">NIS</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] min-w-[200px]">Nama Lengkap</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-48">Kelas (Database)</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-24">Gender</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-48">Wali Murid</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-32">No. HP (WA)</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-24 text-center">Status</th>
                                <th class="px-4 py-3 border-b dark:border-[#2a3a2d] w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#2a3a2d]">
                            @foreach($data as $index => $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#233827] transition-colors">
                                <td class="px-4 py-3 text-center text-sm text-gray-400">{{ $index + 1 }}</td>
                                
                                <td class="px-4 py-3">
                                    <input type="text" name="data[{{ $index }}][nis]" value="{{ $row['nis'] }}" class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-primary rounded px-2 py-1 text-sm font-mono font-bold text-[#111812] dark:text-white transition-all" required>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <input type="text" name="data[{{ $index }}][nama]" value="{{ $row['nama'] }}" class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-primary rounded px-2 py-1 text-sm font-bold text-[#111812] dark:text-white transition-all" required>
                                </td>

                                <td class="px-4 py-3">
                                    <!-- Class Dropdown -->
                                    <select name="data[{{ $index }}][kelas_nama]" class="w-full bg-transparent border border-gray-200 dark:border-gray-700 focus:border-primary rounded px-2 py-1 text-xs font-bold text-[#111812] dark:text-white cursor-pointer">
                                        <option value="" class="text-gray-400">-- Pilih Kelas --</option>
                                        
                                        <!-- Option to Create New (from CSV) -->
                                        @if($row['kelas_nama'])
                                            <option value="{{ $row['kelas_nama'] }}" class="font-bold text-blue-600 bg-blue-50" selected>
                                                ✨ Buat Baru: {{ $row['kelas_nama'] }}
                                            </option>
                                        @endif

                                        <!-- Existing Database Classes -->
                                        @foreach($levels as $lvl)
                                            <optgroup label="{{ $lvl->nama }}">
                                                @foreach($lvl->kelas as $cls)
                                                    <option value="{{ $cls->nama }}" {{ $row['kelas_nama'] == $cls->nama ? 'selected' : '' }}>
                                                        {{ $cls->nama }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    
                                    <!-- Hidden Level Input (Can be ignored if using existing class, but kept for new class creation logic) -->
                                    <input type="hidden" name="data[{{ $index }}][level_nama]" value="{{ $row['level_nama'] }}">
                                </td>

                                <td class="px-4 py-3">
                                    <select name="data[{{ $index }}][gender]" class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-primary rounded px-2 py-1 text-xs font-bold text-[#111812] dark:text-white cursor-pointer">
                                        <option value="L" {{ $row['gender'] == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ $row['gender'] == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </td>

                                <td class="px-4 py-3">
                                    <input type="text" name="data[{{ $index }}][nama_wali]" value="{{ $row['nama_wali'] }}" class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-primary rounded px-2 py-1 text-xs text-[#111812] dark:text-white">
                                </td>

                                <td class="px-4 py-3">
                                    <input type="text" name="data[{{ $index }}][no_hp]" value="{{ $row['no_hp'] }}" class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-primary rounded px-2 py-1 text-xs font-mono text-[#111812] dark:text-white">
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @php
                                        $exists = \App\Models\Santri::where('nis', $row['nis'])->exists();
                                    @endphp
                                    @if($exists)
                                        <span class="px-2 py-1 rounded bg-orange-100 text-orange-600 text-[10px] font-bold border border-orange-200 uppercase">Update</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-600 text-[10px] font-bold border border-green-200 uppercase">Baru</span>
                                    @endif
                                </td>
                                
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="this.closest('tr').remove()" class="p-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Hapus Baris Ini">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

