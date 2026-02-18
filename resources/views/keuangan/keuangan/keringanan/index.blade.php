<x-app-layout>
    <x-slot name="header">
        Master Data Keringanan (Beasiswa/Subsidi)
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Form Create -->
            <div class="w-full md:w-1/3">
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">add_circle</span>
                        Buat Kategori Baru
                    </h2>
                    <form action="{{ route('keuangan.keringanan.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-[#618968] mb-1">Nama Kategori</label>
                                <input type="text" name="nama" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-[#233827] text-sm focus:ring-primary focus:border-primary" placeholder="Contoh: Yatim Piatu" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#618968] mb-1">Deskripsi (Opsional)</label>
                                <textarea name="deskripsi" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-[#233827] text-sm focus:ring-primary focus:border-primary" rows="3" placeholder="Contoh: Diskon khusus anak yatim"></textarea>
                            </div>
                            <button type="submit" class="w-full py-2.5 rounded-lg bg-primary text-[#111812] font-bold shadow-lg shadow-primary/20 hover:brightness-110 transition-all">
                                Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Data -->
            <div class="w-full md:w-2/3">
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-[#233827] text-xs uppercase text-gray-500 font-bold border-b border-[#e0e8e1] dark:border-[#2a3a2d]">
                            <tr>
                                <th class="px-6 py-4">Nama Kategori</th>
                                <th class="px-6 py-4">Aturan Diskon</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#2a3a2d]">
                            @forelse($kategoris as $k)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#233827] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-[#111812] dark:text-white">{{ $k->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $k->deskripsi }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($k->aturanDiskons as $rule)
                                            <span class="px-2 py-1 rounded bg-blue-50 text-blue-600 border border-blue-100 text-[10px] font-bold">
                                                {{ $rule->jenisBiaya->nama }}:
                                                @if($rule->tipe_diskon == 'percentage')
                                                    {{ $rule->jumlah }}%
                                                @else
                                                    Rp {{ number_format($rule->jumlah, 0, ',', '.') }}
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Belum ada aturan diskon</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('keuangan.keringanan.edit', $k->id) }}" class="p-2 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors" title="Setting Diskon">
                                            <span class="material-symbols-outlined text-sm">settings</span>
                                        </a>
                                        <form action="{{ route('keuangan.keringanan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Siswa yang terkait akan kehilangan status subsidi.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada kategori subsidi. Silakan buat baru.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

