<x-app-layout>
    <x-slot name="header">
        Riwayat Transaksi Keuangan
    </x-slot>

    <div class="max-w-[1024px] mx-auto px-6 py-8">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 mb-6 text-sm">
            <a class="text-[#618968] dark:text-[#a0c2a7] font-medium hover:text-primary" href="{{ route('keuangan.dashboard') }}">Beranda</a>
            <span class="text-[#618968] dark:text-[#a0c2a7]"><span class="material-symbols-outlined text-xs">chevron_right</span></span>
            <a class="text-[#618968] dark:text-[#a0c2a7] font-medium hover:text-primary" href="{{ route('keuangan.santri.keuangan.index', ['id' => $santri->id]) }}">Daftar Tagihan</a>
            <span class="text-[#618968] dark:text-[#a0c2a7]"><span class="material-symbols-outlined text-xs">chevron_right</span></span>
            <span class="text-[#111812] dark:text-white font-bold">Riwayat Transaksi Keuangan</span>
        </div>

        <!-- Page Heading -->
        <div class="flex flex-col md:flex-row justify-between items-end gap-4 bg-white dark:bg-[#1a2e1d] p-6 rounded-xl shadow-sm border border-[#e0e8e1] dark:border-[#2a3a2d] mb-6">
            <div class="flex flex-col gap-2">
                <h1 class="text-[#111812] dark:text-white text-2xl md:text-3xl font-black leading-tight tracking-tight">Riwayat Transaksi Keuangan</h1>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#618968] text-xl">person</span>
                    <p class="text-[#618968] dark:text-[#a0c2a7] text-base font-semibold">{{ $santri->nama }} (NIS: {{ $santri->nis ?? '-' }})</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="flex items-center gap-2 px-4 h-10 rounded-lg bg-white dark:bg-[#233827] border border-[#e0e8e1] dark:border-[#2a3a2d] text-[#111812] dark:text-white text-sm font-bold hover:bg-gray-50 dark:hover:bg-[#2d4632] transition-colors">
                    <span class="material-symbols-outlined text-lg">print</span>
                    <span>Cetak Riwayat</span>
                </button>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl shadow-sm border border-[#e0e8e1] dark:border-[#2a3a2d]">
                <p class="text-[#618968] dark:text-[#a0c2a7] text-xs font-bold uppercase tracking-wider mb-1">Total Tabungan (Saldo)</p>
                <h3 class="text-2xl font-black text-[#111812] dark:text-white">Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white dark:bg-[#1a2e1d] p-6 rounded-xl shadow-sm border border-[#e0e8e1] dark:border-[#2a3a2d]">
                <p class="text-[#618968] dark:text-[#a0c2a7] text-xs font-bold uppercase tracking-wider mb-1">Total Transaksi Tercatat</p>
                <h3 class="text-2xl font-black text-[#111812] dark:text-white">{{ $history->count() }} Transaksi</h3>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#e0e8e1] dark:border-[#2a3a2d] overflow-hidden mb-6">
            <div class="p-6 border-b border-[#e0e8e1] dark:border-[#2a3a2d] flex justify-between items-center bg-gray-50 dark:bg-[#233827] print:hidden">
                <h3 class="text-[#111812] dark:text-white text-lg font-bold">Log Aktivitas Keuangan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-[#142617] text-[#618968] dark:text-[#a0c2a7] text-xs uppercase font-bold tracking-wider print:bg-white">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Tipe Transaksi</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">Metode/Detail</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center print:hidden">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e0e8e1] dark:divide-[#2a3a2d]">
                        @forelse($history as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#233827] transition-colors print:bg-white">
                            <td class="px-6 py-4 text-sm font-medium text-[#111812] dark:text-white whitespace-nowrap">
                                {{ $item['date']->format('d M Y') }}
                                <div class="text-[10px] text-gray-400 font-normal">{{ $item['date']->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item['type'] == 'pembayaran')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-bold">
                                        <span class="material-symbols-outlined text-[16px]">receipt_long</span> Pembayaran
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-xs font-bold">
                                        <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span> Tabungan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-[#111812] dark:text-white">
                                {{ $item['description'] }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="capitalize text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    {{ $item['details'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold {{ $item['type'] == 'pembayaran' || $item['details'] == 'tarik' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $item['type'] == 'pembayaran' || $item['details'] == 'tarik' ? '-' : '+' }} Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center print:hidden">
                                @if($item['type'] == 'pembayaran')
                                <a href="{{ route('keuangan.transaksi.receipt', $item['reference']) }}" class="text-gray-400 hover:text-primary transition-colors" title="Cetak Kuitansi">
                                    <span class="material-symbols-outlined">print</span>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-4xl mb-2 opacity-50">history_edu</span>
                                <p>Belum ada riwayat transaksi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        <div class="p-6 bg-gray-50 dark:bg-[#142617] flex justify-between items-center text-sm print:hidden">
            <p class="text-[#618968] dark:text-[#a0c2a7]">Menampilkan {{ $history->count() }} aktivitas terbaru</p>
        </div>
    </div>

        <!-- Footer Help -->
        <div class="flex flex-col md:flex-row p-6 bg-white dark:bg-[#1a2e1d] rounded-xl border border-dashed border-[#618968]/50 items-center gap-4 text-center md:text-left">
            <div class="size-12 min-w-[48px] rounded-full bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">info</span>
            </div>
            <div class="flex-1">
                <p class="text-[#111812] dark:text-white font-bold">Butuh bantuan transaksi?</p>
                <p class="text-[#618968] dark:text-[#a0c2a7] text-sm md:max-w-xl">Hubungi bagian administrasi keuangan Madrasah jika terdapat kesalahan data pada riwayat cicilan santri.</p>
            </div>
            <div class="flex gap-4">
                <a class="text-primary text-sm font-bold flex items-center gap-1 hover:underline" href="#">
                    <span class="material-symbols-outlined text-sm">support_agent</span> CS Keuangan
                </a>
                <a class="text-primary text-sm font-bold flex items-center gap-1 hover:underline" href="#">
                    <span class="material-symbols-outlined text-sm">description</span> Panduan Pelunasan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

