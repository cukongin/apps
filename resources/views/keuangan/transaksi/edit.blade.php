<x-app-layout>
    <div class="flex flex-col h-full bg-[#f6f8f6] dark:bg-[#1a2e1d]">
        <!-- Header -->
        <header class="flex items-center justify-between px-6 py-4 bg-white dark:bg-[#1e3a24] border-b border-[#f0f4f1] dark:border-[#2a3a2d]">
            <div class="flex items-center gap-4">
                <a href="{{ route('pembayaran.create', $transaksi->tagihan->santri_id) }}" class="p-2 -ml-2 rounded-full hover:bg-gray-100 dark:hover:bg-[#2a3a2d] transition-colors">
                    <span class="material-symbols-outlined text-[#111812] dark:text-white">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-xl font-black text-[#111812] dark:text-white tracking-tight">Edit Pembayaran</h1>
                    <p class="text-xs font-bold text-[#618968] dark:text-[#a0c2a7]">Perbarui data transaksi</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('pembayaran.update', $transaksi->id) }}" method="POST" class="bg-white dark:bg-[#1e3a24] rounded-2xl shadow-sm border border-[#f0f4f1] dark:border-[#2a3a2d] p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Info Card -->
                    <div class="bg-primary/5 rounded-xl p-4 border border-primary/10 flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-[#618968]">Jenis Tagihan</span>
                            <span class="text-xs font-black text-primary">{{ $transaksi->tagihan->jenisBiaya->nama }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-[#618968]">Total Tagihan</span>
                            <span class="text-xs font-black text-[#111812] dark:text-white">Rp {{ number_format($transaksi->tagihan->jumlah, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-[#618968]">Santri</span>
                            <span class="text-xs font-black text-[#111812] dark:text-white">{{ $transaksi->tagihan->santri->nama }}</span>
                        </div>
                    </div>

                    <!-- Input Amount -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#111812] dark:text-white">Nominal Pembayaran</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-[#618968]">Rp</span>
                            <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar', $transaksi->jumlah_bayar) }}"
                                class="w-full pl-10 pr-4 py-3 bg-white dark:bg-[#1a2e1d] border border-[#dbe6dd] dark:border-[#2a3a2d] rounded-xl font-bold text-[#111812] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        </div>
                        <p class="text-xs text-[#618968]">Pastikan nominal tidak melebihi sisa tagihan yang seharusnya.</p>
                        @error('jumlah_bayar')
                            <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Keterangan -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#111812] dark:text-white">Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="3"
                            class="w-full px-4 py-3 bg-white dark:bg-[#1a2e1d] border border-[#dbe6dd] dark:border-[#2a3a2d] rounded-xl font-medium text-[#111812] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all resize-none">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 flex flex-col gap-3">
                        <div class="flex gap-3">
                            <a href="{{ route('pembayaran.create', $transaksi->tagihan->santri_id) }}" class="flex-1 py-3 text-center rounded-xl font-bold text-[#618968] hover:bg-gray-50 dark:hover:bg-[#2a3a2d] transition-colors border border-transparent hover:border-[#dbe6dd] dark:hover:border-[#2a3a2d]">
                                Batal
                            </a>
                            <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                                Simpan Perubahan
                            </button>
                        </div>
                </form>

                        <!-- Delete Button (Separate Form) -->
                        <form action="{{ route('pembayaran.destroy', $transaksi->id) }}" method="POST"
                              data-confirm-delete="true"
                              data-title="Hapus Pembayaran?"
                              data-message="Saldo tagihan akan dikembalikan ke status sebelumnya."
                              data-confirm-text="Ya, Hapus">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-3 bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-bold hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">delete</span>
                                Hapus Pembayaran
                            </button>
                        </form>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>

