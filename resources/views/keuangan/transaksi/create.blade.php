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

                    @include('keuangan.transaksi.partials.form')
        </section>

        <!-- Right Side History (Visible on Desktop) -->
        <aside class="w-full lg:w-80 border-l border-[#f0f4f1] dark:border-[#2a3a2d] bg-[#f6f8f6] dark:bg-[#1e3a24]/50 flex flex-col p-6 overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-[#111812] dark:text-white">Riwayat Terakhir</h3>
                <span class="material-symbols-outlined text-[#618968] text-sm">history</span>
            </div>
            <div class="space-y-4">
                @forelse($recentTransactions as $history)
                <div class="bg-white dark:bg-[#1e3a24] p-4 rounded-2xl border border-slate-100 dark:border-[#2a3a2d] shadow-sm hover:shadow-md transition-all group relative overflow-hidden">

                    <!-- Decorative Background for Method -->
                    <div class="absolute top-0 right-0 p-16 opacity-[0.03] pointer-events-none">
                        @if($history->metode_pembayaran == 'tunai')
                            <span class="material-symbols-outlined text-9xl">payments</span>
                        @else
                            <span class="material-symbols-outlined text-9xl">savings</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-start mb-3 relative z-10">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-0.5">{{ $history->created_at->format('d M Y, H:i') }}</span>
                            <span class="font-bold text-[#111812] dark:text-white leading-tight line-clamp-2">{{ $history->tagihan->jenisBiaya->nama }}</span>
                            @if($history->tagihan->jenisBiaya->tipe == 'bulanan')
                                <span class="text-[10px] text-slate-500 font-medium">{{ $history->tagihan->created_at->locale('id')->isoFormat('MMMM Y') }}</span>
                            @endif
                        </div>
                        @if($history->tagihan->status == 'lunas')
                            <div class="flex items-center justify-center size-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400" title="Lunas">
                                <span class="material-symbols-outlined text-sm">check</span>
                            </div>
                        @else
                             <div class="flex items-center justify-center size-6 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400" title="Cicilan">
                                <span class="material-symbols-outlined text-sm">timelapse</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-end relative z-10">
                        <div>
                             <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nominal Bayar</p>
                             <p class="text-lg font-black text-primary">Rp {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</p>
                        </div>

                        <div class="flex items-center gap-1 bg-slate-50 dark:bg-[#1a2e1d] px-2 py-1 rounded-lg border border-slate-100 dark:border-[#2a3a2d]">
                            @if($history->metode_pembayaran == 'tunai')
                                <span class="material-symbols-outlined text-[14px] text-slate-500">payments</span>
                                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300">Tunai</span>
                            @else
                                <span class="material-symbols-outlined text-[14px] text-green-500">savings</span>
                                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300">Tabungan</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Overlay (Boss Style) -->
                    <div class="absolute inset-0 bg-white/90 dark:bg-[#1e3a24]/90 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-20 backdrop-blur-[2px]">
                        <a href="{{ route('keuangan.transaksi.print-thermal', $history->id) }}" class="size-10 flex items-center justify-center bg-white dark:bg-[#2a3a2d] border border-slate-200 dark:border-[#3f5242] rounded-xl text-slate-600 dark:text-slate-300 hover:text-primary hover:border-primary transition-all shadow-sm" title="Cetak Struk" onclick="window.open(this.href, 'PrintThermal', 'width=400,height=600'); return false;">
                            <span class="material-symbols-outlined">receipt_long</span>
                        </a>
                        <a href="{{ route('keuangan.transaksi.receipt', $history->id) }}" class="size-10 flex items-center justify-center bg-white dark:bg-[#2a3a2d] border border-slate-200 dark:border-[#3f5242] rounded-xl text-slate-600 dark:text-slate-300 hover:text-primary hover:border-primary transition-all shadow-sm" title="Cetak Kuitansi" target="_blank">
                            <span class="material-symbols-outlined">print</span>
                        </a>
                        <a href="{{ route('keuangan.pembayaran.edit', $history->id) }}" class="size-10 flex items-center justify-center bg-white dark:bg-[#2a3a2d] border border-slate-200 dark:border-[#3f5242] rounded-xl text-slate-600 dark:text-slate-300 hover:text-orange-500 hover:border-orange-500 transition-all shadow-sm" title="Edit Pembayaran">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center p-8 text-center border-2 border-dashed border-slate-200 dark:border-[#2a3a2d] rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">history</span>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada riwayat.</p>
                </div>
                @endforelse
            </div>
            <button onclick="window.location.href='{{ route('keuangan.santri.keuangan.history', $santri->id) }}'" class="mt-8 w-full py-3 border border-dashed border-[#dbe6dd] dark:border-[#2a3a2d] rounded-xl text-xs font-bold text-[#618968] hover:bg-white dark:hover:bg-[#1e3a24] hover:text-primary hover:border-primary transition-all">
                LIHAT SEMUA RIWAYAT
            </button>
        </aside>
    </div>
</x-app-layout>

