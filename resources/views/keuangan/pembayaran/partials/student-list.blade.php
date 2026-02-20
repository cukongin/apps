<form id="bulkResetForm" action="{{ route('keuangan.pembayaran.bulk-reset') }}" method="POST" class="relative">
    @csrf
    <div id="detail-section" class="flex flex-col gap-4 mt-4 animate-fade-in-up">
        <div class="flex items-center gap-2 justify-between">
            <div class="flex items-center gap-2">
                @if(isset($isSearch) && $isSearch)
                    <span class="material-symbols-outlined text-blue-600">person_search</span>
                    <h3 class="text-xl font-bold text-[#111418] dark:text-white">Daftar Santri ({{ $siswas->count() }})</h3>
                @else
                    <span class="material-symbols-outlined text-primary">expand_circle_down</span>
                    <h3 class="text-xl font-bold text-[#111418] dark:text-white">Detail Tunggakan: {{ $selectedClass->nama ?? 'Kelas' }}</h3>
                @endif
            </div>

            <div class="flex items-center gap-2">
                 <!-- Select All Toggle -->
                 <label class="flex items-center gap-2 cursor-pointer select-none text-sm font-bold text-[#617589] dark:text-[#a0c2a7]">
                    <input type="checkbox" id="selectAllInfo" onchange="toggleSelectAll(this)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                    Pilih Semua
                 </label>

                @if(!isset($isSearch))
                <button type="button" onclick="closeDetail()" class="px-4 py-2 bg-gray-100 dark:bg-[#233827] text-gray-600 dark:text-gray-300 rounded-lg text-sm font-bold hover:bg-gray-200 dark:hover:bg-[#2f4b35]">Tutup Detail</button>
                @endif
            </div>
        </div>

        <!-- Mobile Card View (Visible on small screens) -->
        <div class="md:hidden space-y-3">
            @forelse($siswas as $santri)
                <div class="bg-white dark:bg-[#1a2e1d] border border-[#dbe0e6] dark:border-[#2a452e] rounded-xl p-4 shadow-sm relative overflow-hidden group">
                    <!-- Checkbox Overlay -->
                    <div class="absolute top-4 right-4 z-20">
                        <input type="checkbox" name="student_ids[]" value="{{ $santri->id }}" class="student-checkbox w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" onchange="updateBulkActionUI()">
                    </div>

                    <!-- Status Stripe -->
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $santri->total_tunggakan > 0 ? 'bg-red-500' : 'bg-green-500' }}"></div>

                    <div class="flex justify-between items-start pl-3 mb-3 pr-8">
                        <div>
                            <h4 class="font-bold text-[#111418] dark:text-white text-lg">{{ $santri->nama }}</h4>
                            <p class="text-xs text-[#617589] dark:text-[#a0c2a7] font-medium">{{ $santri->nis }} • {{ $santri->kelas_saat_ini->kelas->nama ?? ($santri->kelas->nama ?? '-') }}</p>
                        </div>
                    </div>

                    <div class="pl-3 flex flex-col gap-2">
                         <!-- Finance Stats -->
                        <div class="flex justify-between items-center bg-gray-50 dark:bg-[#203623] p-2 rounded-lg">
                            <span class="text-xs text-[#617589] dark:text-[#a0c2a7]">Total Tunggakan</span>
                            <span class="font-black text-base {{ $santri->total_tunggakan > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $santri->total_tunggakan > 0 ? 'Rp ' . number_format($santri->total_tunggakan, 0, ',', '.') : 'Lunas' }}
                            </span>
                        </div>

                        <!-- Action Buttons as Full Width Grid -->
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <a href="https://wa.me/{{ $santri->no_hp_wali ?? '' }}?text=Assalamu'alaikum%2C%20Wali%20Santri%20*{{ urlencode($santri->nama) }}*.%0A%0ABerikut%20informasi%20tunggakan%20SPP%2FBiaya%20Lain%3A%0ATotal%20Tunggakan%3A%20*Rp%20{{ number_format($santri->total_tunggakan, 0, ',', '.') }}*%0A%0AMohon%20segera%20diselesaikan.%20Terima%20kasih." target="_blank"
                               class="flex items-center justify-center gap-1 py-2.5 bg-green-50 text-green-700 font-bold rounded-lg text-sm border border-green-200">
                                <span class="material-symbols-outlined text-lg">chat</span>
                                WA
                            </a>
                            <a href="{{ route('keuangan.santri.keuangan.history', $santri->id) }}" class="flex items-center justify-center gap-1 py-2.5 bg-orange-50 text-orange-700 font-bold rounded-lg text-sm border border-orange-200">
                                 <span class="material-symbols-outlined text-lg">history</span>
                                 Riwayat
                            </a>
                            <button type="button" onclick="openPaymentModal({{ $santri->id }}, '{{ addslashes($santri->nama) }}')"
                                    class="col-span-2 flex items-center justify-center gap-1 py-2.5 bg-primary/10 text-primary font-bold rounded-lg text-sm border border-primary/20">
                                <span class="material-symbols-outlined text-lg">payments</span>
                                Bayar
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500 bg-white dark:bg-[#1a2e1d] rounded-xl border border-dashed border-gray-300">
                    Tidak ada data santri ditemukan.
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block bg-white dark:bg-[#1a2e1d] border border-[#dbe0e6] dark:border-[#2a452e] rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#233827] text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider">
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" id="selectAllTable" onchange="toggleSelectAll(this)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                        </th>
                        <th class="px-6 py-3">Nama Santri</th>
                        <th class="px-6 py-3">Kelas</th>
                        <th class="px-6 py-3">Jenis Tunggakan</th>
                        <th class="px-6 py-3 text-right">Sisa Bayar</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dbe0e6] dark:divide-[#2a452e]">
                    @forelse($siswas as $santri)
                    <tr class="hover:bg-[#f8f9fa] dark:hover:bg-[#233827] transition-colors group">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="student_ids[]" value="{{ $santri->id }}" class="student-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" onchange="updateBulkActionUI()">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-[#111418] dark:text-white">{{ $santri->nama }}</span>
                                <span class="text-xs text-[#617589] dark:text-[#a0c2a7]">{{ $santri->nis }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                             <span class="text-sm font-medium text-[#617589] dark:text-[#a0c2a7]">
                                {{ $santri->kelas_saat_ini->kelas->nama ?? ($santri->kelas->nama ?? '-') }}
                             </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @if($santri->total_tunggakan > 0)
                                    @foreach($santri->tagihans->where('status', '!=', 'lunas')->take(3) as $t)
                                        <span class="px-2 py-0.5 rounded bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-[10px] font-bold">
                                            {{ Str::limit($t->jenisBiaya->nama, 15) }}
                                        </span>
                                    @endforeach
                                    @if($santri->tagihans->where('status', '!=', 'lunas')->count() > 3)
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-bold">+{{ $santri->tagihans->where('status', '!=', 'lunas')->count() - 3 }}</span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded bg-green-100 text-green-600 text-[10px] font-bold">Lunas</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($santri->total_tunggakan > 0)
                                <span class="font-bold text-red-500">Rp {{ number_format($santri->total_tunggakan, 0, ',', '.') }}</span>
                            @else
                                <span class="font-bold text-green-500">Lunas</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <a href="https://wa.me/{{ $santri->no_hp_wali ?? '' }}?text=Assalamu'alaikum%2C%20Wali%20Santri%20*{{ urlencode($santri->nama) }}*.%0A%0ABerikut%20informasi%20tunggakan%20SPP%2FBiaya%20Lain%3A%0ATotal%20Tunggakan%3A%20*Rp%20{{ number_format($santri->total_tunggakan, 0, ',', '.') }}*%0A%0AMohon%20segera%20diselesaikan.%20Terima%20kasih." target="_blank" class="flex items-center gap-1 px-3 py-1.5 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg text-xs font-bold transition-all" title="Japri Tagihan WA">
                                <span class="material-symbols-outlined text-sm">chat</span>
                                Chat
                            </a>
                            <a href="{{ route('keuangan.santri.keuangan.history', $santri->id) }}" class="flex items-center gap-1 px-3 py-1.5 text-orange-600 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg text-xs font-bold transition-all" title="Lihat Riwayat">
                                <span class="material-symbols-outlined text-sm">history</span>
                                Riwayat
                            </a>
                            <button type="button" onclick="openPaymentModal({{ $santri->id }}, '{{ addslashes($santri->nama) }}')" class="flex items-center gap-1 px-3 py-1.5 text-white bg-primary hover:bg-primary/90 rounded-lg text-xs font-bold transition-all shadow-sm shadow-primary/30" title="Bayar Cepat">
                                <span class="material-symbols-outlined text-sm">payments</span>
                                Bayar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">Tidak ada santri ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sticky Bulk Action Bar -->
    <div id="bulkActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-[#111812] text-white px-6 py-3 rounded-full shadow-2xl z-50 flex items-center gap-4 transition-all duration-300 translate-y-24 opacity-0 border border-gray-700">
        <span class="font-bold text-sm" id="selectedCount">0 Santri Terpilih</span>
        <div class="h-4 w-px bg-gray-600"></div>
        <button type="submit" onclick="return confirm('ANDA YAKIN? \n\nSemua riwayat pembayaran dari santri yang dipilih akan DIHAPUS dan tagihan akan kembali menjadi BELUM LUNAS. \n\nJika menggunakan tabungan, saldo akan dikembalikan.')" class="flex items-center gap-2 text-red-400 hover:text-red-300 font-bold text-sm">
            <span class="material-symbols-outlined text-lg">delete_sweep</span>
            Hapus Pembayaran
        </button>
        <button type="button" onclick="deselectAll()" class="ml-2 text-gray-400 hover:text-white">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
</form>



