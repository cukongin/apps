<x-app-layout>
    <x-slot name="header">
        Riwayat Transaksi
    </x-slot>

    <div class="max-w-[1440px] mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-black text-[#111812] dark:text-white tracking-tight">Riwayat Pembayaran</h2>
                <p class="text-[#618968] dark:text-[#a0c2a7] mt-1">Daftar semua transaksi pembayaran yang tercatat dalam sistem.</p>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('keuangan.transaksi.history') }}" class="flex flex-wrap items-center gap-3 bg-white dark:bg-[#1a2e1d] p-2 rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm">

                <!-- Date Filter -->
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-[#233827] px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-gray-400 text-sm">date_range</span>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="bg-transparent border-none text-xs font-bold w-24 p-0 focus:ring-0 text-[#111812] dark:text-white">
                    <span class="text-gray-400 text-xs">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" class="bg-transparent border-none text-xs font-bold w-24 p-0 focus:ring-0 text-[#111812] dark:text-white">
                </div>

                <!-- Search -->
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 text-sm">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / ID..." class="pl-9 pr-4 py-1.5 rounded-lg border-none bg-gray-50 dark:bg-[#233827] text-xs font-bold focus:ring-2 focus:ring-primary w-40 md:w-56">
                </div>

                <button type="submit" class="bg-primary text-[#111812] p-1.5 rounded-lg hover:shadow-lg shadow-primary/20 transition-all flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">filter_list</span>
                </button>

                @if(request()->has('search') || request()->has('start_date'))
                    <a href="{{ route('keuangan.transaksi.history') }}" class="text-xs font-bold text-red-500 hover:text-red-600 px-2">Reset</a>
                @endif
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-2xl shadow-sm border border-[#e0e8e1] dark:border-[#2a3a2d] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-[#fcfdfc] dark:bg-[#1e3a24] border-b border-[#e0e8e1] dark:border-[#2a3a2d]">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#618968] dark:text-[#a0c2a7]">Info Transaksi</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#618968] dark:text-[#a0c2a7]">Santri & Kelas</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#618968] dark:text-[#a0c2a7]">Jenis Pembayaran</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#618968] dark:text-[#a0c2a7] text-right">Nominal</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#618968] dark:text-[#a0c2a7] text-center">Metode</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#618968] dark:text-[#a0c2a7] text-center">Struk</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#618968] dark:text-[#a0c2a7] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f4f1] dark:divide-[#2a3a2d]">
                        @php
                            // Group transactions by simple unique key (Time + Santri)
                            // We use the collection from the paginator
                            $groups = $transaksis->groupBy(function($item) {
                                return $item->created_at->format('YmdHi') . '-' . $item->tagihan->santri_id;
                            });
                        @endphp

                        @forelse($groups as $group)
                            @foreach($group as $index => $t)
                            <tr class="hover:bg-[#f6f8f6] dark:hover:bg-[#233827] transition-colors group {{ $index === 0 ? 'border-t border-[#e0e8e1] dark:border-[#2a3a2d]' : '' }}">

                                <!-- 1. Info (Merged) -->
                                @if($index === 0)
                                <td rowspan="{{ $group->count() }}" class="px-6 py-4 align-middle border-r border-[#f0f4f1] dark:border-[#2a3a2d]">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-mono text-gray-400">#{{ $t->id }}</span>
                                        <span class="text-sm font-bold text-[#111812] dark:text-white">{{ $t->created_at->format('d M Y') }}</span>
                                        <span class="text-xs text-gray-400">{{ $t->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                @endif

                                <!-- 2. Santri (Merged) -->
                                @if($index === 0)
                                <td rowspan="{{ $group->count() }}" class="px-6 py-4 align-middle border-r border-[#f0f4f1] dark:border-[#2a3a2d]">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-[#111812] dark:text-white">{{ $t->tagihan->santri->nama ?? 'Hamba Allah' }}</span>
                                        <span class="text-xs text-[#618968] dark:text-[#a0c2a7]">{{ $t->tagihan->santri->kelas->nama ?? '-' }}</span>
                                    </div>
                                </td>
                                @endif

                                <!-- 3. Item (Individual) -->
                                <td class="px-6 py-4 align-middle">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ $t->tagihan->jenisBiaya->nama ?? 'Biaya Lain' }}
                                    </span>
                                    @if($t->keterangan)
                                        <p class="text-[10px] text-gray-400 mt-1 max-w-[150px] truncate" title="{{ $t->keterangan }}">{{ $t->keterangan }}</p>
                                    @endif
                                </td>

                                <!-- 4. Nominal (Individual) -->
                                <td class="px-6 py-4 text-right align-middle">
                                    <span class="text-sm font-black text-primary">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</span>
                                </td>

                                <!-- 5. Metode (Merged) -->
                                @if($index === 0)
                                <td rowspan="{{ $group->count() }}" class="px-6 py-4 text-center align-middle border-l border-[#f0f4f1] dark:border-[#2a3a2d]">
                                    @if($t->metode_pembayaran == 'tabungan')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold uppercase bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-900/20 dark:border-purple-800 dark:text-purple-300">
                                            <span class="material-symbols-outlined text-[12px]">savings</span> Tabungan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold uppercase bg-green-50 text-green-700 border border-green-100 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
                                            <span class="material-symbols-outlined text-[12px]">payments</span> Tunai
                                        </span>
                                    @endif
                                </td>
                                @endif

                                <!-- 6. Struk (Merged) -->
                                @if($index === 0)
                                <td rowspan="{{ $group->count() }}" class="px-6 py-4 text-center align-middle">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="window.open('{{ route('transaksi.print-thermal', $t->id) }}', 'PrintThermal', 'width=400,height=600')" class="size-8 rounded-lg bg-gray-100 dark:bg-[#324b36] hover:bg-primary/20 hover:text-primary flex items-center justify-center text-gray-500 transition-all" title="Cetak Struk Thermal (Batch)">
                                            <span class="material-symbols-outlined text-lg">receipt_long</span>
                                        </button>
                                        <a href="{{ route('transaksi.receipt', $t->id) }}" target="_blank" class="size-8 rounded-lg bg-gray-100 dark:bg-[#324b36] hover:bg-primary/20 hover:text-primary flex items-center justify-center text-gray-500 transition-all" title="Cetak Kuitansi A4">
                                            <span class="material-symbols-outlined text-lg">print</span>
                                        </a>
                                    </div>
                                </td>
                                @endif

                                <!-- 7. Aksi Delete (Individual - per item cancellation) -->
                                <td class="px-6 py-4 text-center align-middle">
                                    <form action="{{ route('pembayaran.destroy', $t->id) }}" method="POST"
                                          data-confirm-delete="true"
                                          data-title="Batalkan Transaksi?"
                                          data-message="Apakah Anda yakin ingin membatalkan transaksi ini? Saldo/status tagihan akan dikembalikan."
                                          data-confirm-text="Ya, Batalkan">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="size-8 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 text-red-500 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100" title="Batalkan Item Ini">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl opacity-50">receipt_long</span>
                                    <p class="text-sm font-medium">Belum ada data transaksi yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-[#e0e8e1] dark:border-[#2a3a2d] bg-gray-50 dark:bg-[#233827]">
                {{ $transaksis->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</x-app-layout>

