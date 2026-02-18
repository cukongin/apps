<x-app-layout>
    <x-slot name="header">
        Data Tabungan
    </x-slot>

    <div class="max-w-[1200px] mx-auto p-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-[#111812] dark:text-white">Data Tabungan Santri</h1>
                <p class="text-[#618968] dark:text-[#a0c2a7] text-sm mt-1">Cari santri untuk melihat detail tabungan atau transaksi.</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative max-w-2xl mx-auto mb-10 w-full">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-gray-400 text-2xl">search</span>
            </div>
            <form action="{{ route('keuangan.tabungan.index') }}" method="GET" id="searchForm">
                <input type="text"
                       name="search_global"
                       value="{{ request('search_global') }}"
                       class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-[#1a2e1d] border border-[#dbe6dd] dark:border-[#2a3a2d] rounded-2xl leading-5 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-lg shadow-sm transition-shadow hover:shadow-md"
                       placeholder="Cari nama santri, NIS, atau kelas..."
                       autofocus
                       oninput="debounceSearch()">
            </form>
        </div>

        <!-- Content Area -->
        <div id="results" class="space-y-8">
            @if(request()->has('search_global') && $classes->count() > 0)
                <!-- Search Results -->
                <div class="flex items-center gap-2 mb-4">
                     <span class="material-symbols-outlined text-primary">filter_list</span>
                     <h2 class="text-lg font-bold text-[#111812] dark:text-white">Hasil Pencarian</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($classes as $kelas)
                        @foreach($kelas->filtered_santris as $santri)
                        <a href="{{ route('keuangan.tabungan.show', $santri->id) }}" class="group bg-white dark:bg-[#1a2e1d] rounded-2xl p-5 border border-[#dbe6dd] dark:border-[#2a3a2d] hover:border-primary dark:hover:border-primary hover:shadow-lg transition-all relative overflow-hidden">
                            <!-- Hover Effect Bg -->
                            <div class="absolute inset-0 bg-primary/0 group-hover:bg-primary/5 transition-colors"></div>

                            <div class="flex items-center gap-4 relative z-10">
                                <div class="size-14 rounded-full bg-gray-100 dark:bg-[#233827] flex-shrink-0 overflow-hidden border-2 border-white dark:border-[#2a3a2d] shadow-sm">
                                    @if($santri->foto)
                                        <img src="{{ asset('storage/' . $santri->foto) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <span class="material-symbols-outlined text-2xl">person</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-[#111812] dark:text-white text-lg truncate group-hover:text-primary transition-colors">{{ $santri->nama }}</h3>
                                    <p class="text-sm text-[#618968] dark:text-[#a0c2a7]">{{ $santri->kelas->nama }} &bull; NIS: {{ $santri->nis }}</p>
                                    <div class="mt-2 flex items-center gap-1">
                                         <span class="material-symbols-outlined text-[16px] text-primary">account_balance_wallet</span>
                                         <span class="text-sm font-black text-[#111812] dark:text-white">Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-gray-300 group-hover:text-primary transition-colors">chevron_right</span>
                            </div>
                        </a>
                        @endforeach
                    @endforeach
                </div>

            @elseif(request()->has('search_global'))
                <div class="text-center py-16">
                    <div class="bg-gray-100 dark:bg-[#1a2e1d] rounded-full size-20 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-gray-400 text-4xl">search_off</span>
                    </div>
                    <h3 class="text-lg font-bold text-[#111812] dark:text-white">Siswa Tidak Ditemukan</h3>
                    <p class="text-[#618968] mt-1">Coba kata kunci lain atau pastikan ejaan benar.</p>
                </div>
            @else
                <!-- Default View: Show Classes Quick Links or Recent -->
                <h3 class="text-lg font-bold text-[#111812] dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">groups</span>
                    Daftar Kelas
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($classes as $kelas)
                        @if($kelas->siswas->count() > 0)
                        <div onclick="searchByClass('{{ $kelas->nama }}')" class="cursor-pointer bg-white dark:bg-[#1a2e1d] p-4 rounded-xl border border-[#dbe6dd] dark:border-[#2a3a2d] hover:border-primary hover:shadow-md transition-all text-center group">
                            <h4 class="font-black text-xl text-[#111812] dark:text-white group-hover:text-primary">{{ $kelas->nama }}</h4>
                            <p class="text-xs text-[#618968] mt-1">{{ $kelas->siswas->count() }} Santri</p>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        let timeout = null;
        function debounceSearch() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.getElementById('searchForm').submit();
            }, 600);
        }

        function searchByClass(className) {
            const input = document.querySelector('input[name="search_global"]');
            input.value = className;
            document.getElementById('searchForm').submit();
        }
    </script>
</x-app-layout>

