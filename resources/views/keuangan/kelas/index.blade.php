<x-app-layout>
    <x-slot name="header">
        Manajemen Kelas
    </x-slot>

    <div class="max-w-[1200px] mx-auto">
        <!-- Mass Action Form Wrapper -->
        <form action="{{ route('keuangan.kelas.bulk-generate') }}" method="POST" id="bulkForm" onsubmit="return confirm('Generate tagihan untuk kelas terpilih?');">
            @csrf

            <!-- Page Heading & Actions -->
            <div class="flex flex-wrap justify-between items-end gap-4 mb-6">
                <div class="flex flex-col gap-2">
                    <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Manajemen Kelas</h1>
                    <p class="text-[#617589] dark:text-gray-400 text-base font-normal">Kelola struktur kelas & Generate Tagihan Massal.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Bulk Tools -->
                    <div class="flex items-center gap-1 bg-white dark:bg-[#1a2e1d] p-1.5 rounded-xl border border-[#dbe0e6] dark:border-[#2a3038] shadow-sm">
                        <span class="text-[10px] font-bold uppercase text-[#617589] pl-2 hidden md:inline">Generate:</span>
                        <select name="start_month" class="text-xs py-1.5 pl-2 pr-6 border-none bg-slate-100 dark:bg-[#233827] rounded-lg font-bold cursor-pointer focus:ring-0 min-w-[70px]">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $m == date('n')+1 ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('M') }}</option>
                            @endforeach
                        </select>
                        <select name="start_year" class="text-xs py-1.5 pl-2 pr-6 border-none bg-slate-100 dark:bg-[#233827] rounded-lg font-bold cursor-pointer focus:ring-0 min-w-[70px]">
                            @foreach(range(date('Y'), date('Y')+1) as $y)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <div class="flex items-center bg-slate-100 dark:bg-[#233827] rounded-lg px-2">
                            <input type="number" name="months" value="5" class="w-12 text-xs py-1.5 border-none bg-transparent text-center font-bold focus:ring-0" min="1" max="12">
                            <span class="text-[10px] font-bold text-[#617589]">Bln</span>
                        </div>
                        <button type="submit" class="flex items-center gap-1 px-4 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-sm">play_circle</span>
                            <span class="text-xs font-bold">Generate</span>
                        </button>
                    </div>

                </div>
            </div>

        <!-- Navigation Tabs -->
        <div class="bg-white dark:bg-[#111418] rounded-xl border border-[#dbe0e6] dark:border-[#2a3038] overflow-hidden mb-6">
            <div class="px-6 pt-2 border-b border-[#dbe0e6] dark:border-[#2a3038] flex gap-8">
                <a class="flex flex-col items-center justify-center border-b-[3px] border-transparent text-[#617589] dark:text-gray-400 pb-3 pt-4 font-bold text-sm hover:text-[#111418] dark:hover:text-white transition-colors" href="{{ route('keuangan.santri.index') }}">
                    Daftar Siswa
                </a>
                <a class="flex flex-col items-center justify-center border-b-[3px] border-primary text-primary pb-3 pt-4 font-bold text-sm" href="#">
                    Manajemen Kelas
                </a>
            </div>
        </div>

        <!-- Levels Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach($levels as $levelName => $classes)
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 bg-slate-50 dark:bg-[#233827] border-b border-[#dbe0e6] dark:border-[#2a452e] flex justify-between items-center">
                    <h3 class="font-bold text-lg text-[#111418] dark:text-white">{{ $levelName }}</h3>
                    <div class="flex items-center gap-2">
                         <label class="flex items-center gap-1 text-xs font-bold text-[#617589] cursor-pointer bg-white dark:bg-[#1a2e1d] px-2 py-1 rounded border border-[#dbe0e6] dark:border-[#2a452e]">
                            <input type="checkbox" onclick="toggleLevel(this)" class="rounded text-primary focus:ring-0 w-3 h-3">
                            Pilih Semua
                        </label>
                        <span class="px-2 py-1 rounded bg-slate-200 dark:bg-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300">{{ count($classes) }} Kelas</span>
                    </div>
                </div>
                <div class="flex-1 p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white dark:bg-[#1a2e1d] text-xs font-bold text-[#617589] uppercase tracking-wider border-b border-[#dbe0e6] dark:border-[#2a452e]">
                            <tr>
                                <th class="px-4 py-3 w-8">#</th>
                                <th class="px-6 py-3">Nama Kelas</th>
                                <th class="px-6 py-3">Wali Kelas</th>
                                <th class="px-6 py-3 text-right">Jumlah</th>
                                <th class="px-6 py-3 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#dbe0e6] dark:divide-[#2a452e]">
                            @foreach($classes as $kelas)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#233827]/50 transition-colors">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="class_ids[]" value="{{ $kelas['id'] }}" class="level-checkbox rounded text-primary focus:ring-0 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="px-6 py-3 font-bold text-sm text-[#111418] dark:text-white">{{ $kelas['nama'] }}</td>
                                <td class="px-6 py-3 text-sm text-[#617589] dark:text-[#a0c2a7]">{{ $kelas['wali'] }}</td>
                                <td class="px-6 py-3 text-sm text-right font-mono">{{ $kelas['jumlah'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="deleteForm" method="POST" action="" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function toggleLevel(source) {
                const table = source.closest('.flex-col').querySelector('table');
                const checkboxes = table.querySelectorAll('.level-checkbox');
                checkboxes.forEach(c => c.checked = source.checked);
            }

            function confirmDelete(url) {
                if(confirm('Yakin ingin menghapus kelas ini?')) {
                    const form = document.getElementById('deleteForm');
                    form.action = url;
                    form.submit();
                }
            }
        </script>
    </div>
</x-app-layout>

