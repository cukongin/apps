<div id="detail-section" class="flex flex-col gap-4 mt-4 animate-fade-in-up p-6">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">expand_circle_down</span>
        <h3 class="text-xl font-bold text-[#111418] dark:text-white">Daftar Santri: {{ $selectedClass->nama }}</h3>
        <button onclick="window.location.href='{{ route('keuangan.tabungan.index') }}'" class="ml-auto px-4 py-2 bg-gray-100 dark:bg-[#233827] text-gray-600 dark:text-gray-300 rounded-lg text-sm font-bold hover:bg-gray-200 dark:hover:bg-[#2f4b35]">Tutup</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($selectedClass->filtered_santris as $santri)
        <a href="{{ route('keuangan.tabungan.show', $santri->id) }}" class="group bg-white dark:bg-[#1e3a24] rounded-xl p-4 border border-[#dbe6dd] dark:border-[#2a3a2d] hover:border-primary dark:hover:border-primary hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="size-12 rounded-full bg-gray-100 dark:bg-[#2a3a2d] overflow-hidden">
                    @if($santri->foto)
                        <img src="{{ asset('storage/' . $santri->foto) }}" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-gray-400 text-3xl flex items-center justify-center h-full">person</span>
                    @endif
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-[#111812] dark:text-white group-hover:text-primary transition-colors">{{ $santri->nama }}</h4>
                    <p class="text-xs text-[#618968] dark:text-[#a0c2a7]">{{ $santri->nis }}</p>
                    <div class="mt-2 flex items-center gap-1">
                         <span class="material-symbols-outlined text-[14px] text-[#618968]">account_balance_wallet</span>
                         <span class="text-sm font-black text-[#111812] dark:text-white">Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-10 text-gray-500">
            Tidak ada santri ditemukan.
        </div>
        @endforelse
    </div>
</div>

