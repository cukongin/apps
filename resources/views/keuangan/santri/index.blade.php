<x-app-layout>
    <x-slot name="header">
        Master Data Siswa
    </x-slot>

    <div class="max-w-[1200px] mx-auto">
        <!-- Page Heading -->
        <div class="flex flex-wrap justify-between items-end gap-4 mb-6">
            <div class="flex flex-col gap-2">
                <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Master Data Siswa</h1>
                <p class="text-[#617589] dark:text-gray-400 text-base font-normal">Kelola database siswa secara terpadu.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('keuangan.santri.export') }}" class="flex items-center justify-center rounded-lg h-10 px-4 bg-[#f0f2f4] dark:bg-[#1f262e] text-[#111418] dark:text-white text-sm font-bold hover:bg-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-base mr-2">download</span>
                    Ekspor Data
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
        @endif

        <!-- Tabs Container -->
        <div class="bg-white dark:bg-[#111418] rounded-xl border border-[#dbe0e6] dark:border-[#2a3038] overflow-hidden">
            <div class="px-6 pt-2 border-b border-[#dbe0e6] dark:border-[#2a3038] flex gap-8">
                <a class="flex flex-col items-center justify-center border-b-[3px] border-primary text-primary pb-3 pt-4 font-bold text-sm" href="{{ route('keuangan.santri.index') }}">
                    Daftar Siswa
                </a>
                <!-- Manajemen Kelas Removed as per request -->
            </div>
            <!-- Toolbar / Filters -->
            <form method="GET" action="{{ route('keuangan.santri.index') }}" class="p-6 flex flex-wrap items-center justify-between gap-4">
                <div class="grid grid-cols-2 md:flex md:flex-wrap md:items-center gap-3 w-full">
                    <!-- Search -->
                    <div class="col-span-2 md:flex-1 relative min-w-[200px]">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#617589] dark:text-gray-400">search</span>
                        <input name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3038] bg-white dark:bg-[#1a212a] focus:ring-1 focus:ring-primary focus:border-primary text-sm transition-all outline-none" placeholder="Cari nama, NIS..." type="text"/>
                    </div>

                    <!-- Filter Level -->
                    <div class="col-span-1 min-w-[120px] md:min-w-[150px]">
                        <select name="level_id" onchange="this.form.submit()" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3038] bg-white dark:bg-[#1a212a] focus:ring-1 focus:ring-primary focus:border-primary text-sm appearance-none outline-none cursor-pointer">
                            <option value="">Semua Tingkatan</option>
                                @foreach($levels as $lvl)
                                    <option value="{{ $lvl->id }}" {{ request('level_id') == $lvl->id ? 'selected' : '' }}>{{ $lvl->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Class -->
                        <div class="col-span-1 min-w-[120px] md:min-w-[180px]">
                            <select name="kelas_id" onchange="this.form.submit()" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3038] bg-white dark:bg-[#1a212a] focus:ring-1 focus:ring-primary focus:border-primary text-sm appearance-none outline-none cursor-pointer">
                            <option value="">Semua Kelas</option>
                            <option value="no_class" {{ request('kelas_id') == 'no_class' ? 'selected' : '' }}>Belum Ada Kelas</option>
                            @foreach($levels as $lvl)
                                    @if(request('level_id') && request('level_id') != $lvl->id) @continue @endif
                                    <optgroup label="{{ $lvl->nama }}">
                                        @foreach($lvl->kelas as $cls)
                                            <option value="{{ $cls->id }}" {{ request('kelas_id') == $cls->id ? 'selected' : '' }}>{{ $cls->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <!-- Filter Status -->
                        <div class="col-span-2 md:w-auto min-w-[150px]">
                            <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3038] bg-white dark:bg-[#1a212a] focus:ring-1 focus:ring-primary focus:border-primary text-sm appearance-none outline-none cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus (Alumni)</option>
                                <option value="Pindah" {{ request('status') == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                                <option value="Berhenti" {{ request('status') == 'Berhenti' ? 'selected' : '' }}>Berhenti</option>
                            </select>
                        </div>
                </div>

            </form>
            <!-- Table Container -->
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                        <thead class="bg-[#f8f9fa] dark:bg-[#1a212a] text-[#617589] dark:text-gray-400 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="hidden md:table-cell px-6 py-4 border-b border-[#dbe0e6] dark:border-[#2a3038] w-10">No</th>
                                <th class="hidden md:table-cell px-6 py-4 border-b border-[#dbe0e6] dark:border-[#2a3038]">NIS</th>
                                <th class="px-6 py-4 border-b border-[#dbe0e6] dark:border-[#2a3038]">Nama Lengkap</th>
                                <th class="px-6 py-4 border-b border-[#dbe0e6] dark:border-[#2a3038]">Kelas</th>
                                <th class="hidden md:table-cell px-6 py-4 border-b border-[#dbe0e6] dark:border-[#2a3038]">Gender</th>
                                <th class="hidden md:table-cell px-6 py-4 border-b border-[#dbe0e6] dark:border-[#2a3038]">Status</th>
                                <th class="hidden md:table-cell px-6 py-4 border-b border-[#dbe0e6] dark:border-[#2a3038] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#dbe0e6] dark:divide-[#2a3038]">
                            @foreach($santris as $s)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1a212a] transition-colors group cursor-pointer" onclick="window.location='{{ route('keuangan.santri.show', $s['id']) }}'">
                                <td class="hidden md:table-cell px-6 py-4 text-center">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-sm font-medium">
                                    <a href="{{ route('keuangan.santri.show', $s['id']) }}" class="hover:text-primary transition-colors hover:underline">
                                        {{ $s['nis'] }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('keuangan.santri.show', $s['id']) }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                                        @if($s['foto'])
                                            <img src="{{ asset('storage/' . $s['foto']) }}" alt="" class="size-8 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="size-8 rounded-full bg-{{ $s['color'] }}/10 text-{{ $s['color'] }} flex items-center justify-center text-xs font-bold">{{ $s['initial'] }}</div>
                                        @endif
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-sm font-bold text-[#111418] dark:text-white hover:text-primary transition-colors hover:underline">{{ $s['nama'] }}</span>
                                                <!-- Mobile Status Indicator -->
                                                <span class="md:hidden size-2 rounded-full
                                                    {{ $s['status'] == 'Aktif' ? 'bg-green-500' : '' }}
                                                    {{ $s['status'] == 'Lulus' ? 'bg-blue-500' : '' }}
                                                    {{ $s['status'] == 'Pindah' ? 'bg-orange-500' : '' }}
                                                    {{ $s['status'] == 'Berhenti' ? 'bg-red-500' : '' }}
                                                " title="{{ $s['status'] }}"></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-[10px] text-slate-400">{{ $s['level'] }}</span>
                                                <span class="md:hidden text-[10px] text-slate-300">• {{ $s['nis'] }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold">{{ $s['kelas'] }}</span>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-sm text-[#617589] dark:text-gray-400">{{ $s['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td class="hidden md:table-cell px-6 py-4">
                                    <span class="flex items-center gap-1.5 text-xs font-bold
                                        {{ $s['status'] == 'Aktif' ? 'text-green-600 dark:text-green-400' : '' }}
                                        {{ $s['status'] == 'Lulus' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                        {{ $s['status'] == 'Pindah' ? 'text-orange-600 dark:text-orange-400' : '' }}
                                        {{ $s['status'] == 'Berhenti' ? 'text-red-600 dark:text-red-400' : '' }}
                                    ">
                                        <span class="size-2 rounded-full
                                            {{ $s['status'] == 'Aktif' ? 'bg-green-500' : '' }}
                                            {{ $s['status'] == 'Lulus' ? 'bg-blue-500' : '' }}
                                            {{ $s['status'] == 'Pindah' ? 'bg-orange-500' : '' }}
                                            {{ $s['status'] == 'Berhenti' ? 'bg-red-500' : '' }}
                                        "></span> {{ $s['status'] }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-right" onclick="event.stopPropagation()">
                                    <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('keuangan.santri.show', $s['id']) }}" class="p-1.5 hover:bg-primary/10 hover:text-primary rounded-lg transition-colors" title="Lihat Profil Keuangan">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>



            <!-- Pagination -->
            <div class="p-4 border-t border-[#dbe0e6] dark:border-[#2a3038] flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span class="font-bold">{{ $santrisPaginator->firstItem() }}</span> - <span class="font-bold">{{ $santrisPaginator->lastItem() }}</span> dari <span class="font-bold">{{ $santrisPaginator->total() }}</span> Data
                </div>
                <div class="mt-4">
                    {{ $santrisPaginator->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Popup for Tambah Siswa (Simulated) -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#111418]/60 backdrop-blur-sm pointer-events-none opacity-0 invisible">
        <!-- Content of modal skipped for cleanliness, can be added later or enabled -->
    </div>
</x-app-layout>

