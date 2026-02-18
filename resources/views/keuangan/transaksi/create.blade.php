<x-app-layout>
    <x-slot name="header">
        Transaksi Pembayaran
    </x-slot>

    <div class="flex flex-col lg:flex-row h-[calc(100vh-65px)] overflow-hidden">
        <!-- Sidebar Local Removed (User Request) -->

        <!-- Main Content Section -->
        <section class="flex-1 flex flex-col bg-white dark:bg-[#1a2e1d] overflow-y-auto custom-scrollbar">
            <div class="p-6">
                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2 mb-4 text-xs font-semibold uppercase tracking-wider">
                    <a class="text-[#618968] dark:text-[#a0c2a7]" href="{{ route('keuangan.dashboard') }}">Keuangan</a>
                    <span class="text-[#618968] dark:text-[#a0c2a7]">/</span>
                    <span class="text-primary">Transaksi Cicilan</span>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-black tracking-tight text-[#111812] dark:text-white">Proses Pembayaran & Potong Tabungan</h2>
                    <p class="text-[#618968] dark:text-[#a0c2a7] text-sm mt-1">Dukungan fitur bayar sebagian / cicilan untuk fleksibilitas administrasi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <!-- Search Student -->
                    <!-- Back Button -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-bold text-[#111812] dark:text-white">Navigasi</label>
                        <a href="{{ route('keuangan.pembayaran.index') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-white dark:bg-[#1e3a24] border border-[#dbe6dd] dark:border-[#2a3a2d] rounded-xl hover:bg-[#f0f4f1] dark:hover:bg-[#2a3a2d] transition-all text-[#111812] dark:text-white font-bold">
                            <span class="material-symbols-outlined">arrow_back</span>
                            <span>Kembali ke Rekap</span>
                        </a>
                    </div>
                    <!-- Selected Student Info -->
                    <div class="bg-primary/5 dark:bg-primary/10 rounded-xl p-4 border border-primary/20 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-primary/20 flex items-center justify-center overflow-hidden">
                                @if($santri->foto)
                                    <img src="{{ asset('storage/' . $santri->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-primary">person</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#111812] dark:text-white">{{ $santri->nama }} ({{ $santri->nis }})</p>
                                <p class="text-xs text-[#618968] dark:text-[#a0c2a7]">{{ $santri->kelas->nama ?? 'Belum Ada Kelas' }} • {{ $santri->status }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold text-[#618968] dark:text-[#a0c2a7]">Saldo Tabungan</p>
                            <p class="text-lg font-black text-primary">Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-[#111812] dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined">receipt_long</span>
                            Daftar Tagihan & Cicilan
                        </h3>
                        <button class="text-xs font-bold text-primary flex items-center gap-1 hover:underline" type="button" onclick="toggleAllBills()">
                            <span class="material-symbols-outlined text-sm">select_all</span>
                            Pilih Semua
                        </button>
                    </div>

                    @include('transaksi.partials.form')
        </section>

        <!-- Right Side History (Visible on Desktop) -->
        <aside class="w-full lg:w-80 border-l border-[#f0f4f1] dark:border-[#2a3a2d] bg-[#f6f8f6] dark:bg-[#1e3a24]/50 flex flex-col p-6 overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-[#111812] dark:text-white">Riwayat Terakhir</h3>
                <span class="material-symbols-outlined text-[#618968] text-sm">history</span>
            </div>
            <div class="space-y-4">
                @forelse($recentTransactions as $history)
                <div class="bg-white dark:bg-[#1e3a24] p-4 rounded-xl border border-[#f0f4f1] dark:border-[#2a3a2d] relative group">
                    <div class="flex justify-between items-start mb-2">
                        @if($history->tagihan->status == 'lunas')
                            <span class="text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded uppercase">Lunas</span>
                        @else
                            <span class="text-[10px] font-bold text-white bg-orange-500 px-2 py-0.5 rounded uppercase">Cicilan</span>
                        @endif
                        <span class="text-[10px] font-medium text-[#618968] dark:text-[#a0c2a7]">{{ $history->created_at->format('d M Y') }}</span>
                    </div>
                    <p class="text-xs font-bold mb-1 text-[#111812] dark:text-white">{{ $history->tagihan->jenisBiaya->nama }}</p>
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-black text-primary">Rp {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</p>
                        <span class="text-[10px] font-medium text-[#618968] flex items-center gap-1">
                            @if($history->metode_pembayaran == 'tunai')
                                <span class="material-symbols-outlined text-[12px]">payments</span> Tunai
                            @else
                                <span class="material-symbols-outlined text-[12px]">savings</span> Tabungan
                            @endif
                        </span>
                    </div>
                    <!-- Actions -->
                    <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('transaksi.print-thermal', $history->id) }}" class="p-1.5 bg-gray-100 dark:bg-[#2a3a2d] rounded-lg text-gray-500 hover:text-primary transition-colors" title="Cetak Struk Thermal" onclick="window.open(this.href, 'PrintThermal', 'width=400,height=600'); return false;">
                            <span class="material-symbols-outlined text-sm">receipt_long</span>
                        </a>
                        <a href="{{ route('transaksi.receipt', $history->id) }}" class="p-1.5 bg-gray-100 dark:bg-[#2a3a2d] rounded-lg text-gray-500 hover:text-primary transition-colors" title="Cetak Kuitansi A4" target="_blank">
                            <span class="material-symbols-outlined text-sm">print</span>
                        </a>
                        <a href="{{ route('pembayaran.edit', $history->id) }}" class="p-1.5 bg-gray-100 dark:bg-[#2a3a2d] rounded-lg text-gray-500 hover:text-primary transition-colors" title="Edit Pembayaran">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400 text-xs">
                    Belum ada riwayat pembayaran.
                </div>
                @endforelse
            </div>
            <button class="mt-8 w-full py-3 border border-dashed border-[#dbe6dd] dark:border-[#2a3a2d] rounded-xl text-xs font-bold text-[#618968] hover:bg-white dark:hover:bg-[#1e3a24] transition-all">
                LIHAT SEMUA RIWAYAT
            </button>
        </aside>
    </div>
</x-app-layout>

