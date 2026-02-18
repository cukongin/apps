<x-app-layout>
    <x-slot name="header">
        Edit Tagihan
    </x-slot>

    <div class="max-w-xl mx-auto py-12 px-6">
        <div class="bg-white dark:bg-[#1a2e1d] shadow-sm rounded-xl p-6 border border-gray-200 dark:border-[#2a3a2d]">
            <h2 class="text-xl font-bold mb-4 text-[#111812] dark:text-white">Edit Tagihan Santri</h2>

            <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <p class="text-sm font-bold text-blue-800 dark:text-blue-300">Santri: {{ $tagihan->siswa->nama }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400">Tagihan: {{ $tagihan->jenisBiaya->nama ?? 'Custom' }}</p>
            </div>

            <form action="{{ route('keuangan.tagihan.update', $tagihan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nominal Tagihan (Rp)</label>
                    <input type="number" name="jumlah" value="{{ $tagihan->jumlah }}" class="w-full rounded-lg border-gray-300 dark:border-[#2a452e] dark:bg-[#1a2e1e] dark:text-white focus:ring-primary focus:border-primary">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan / Periode</label>
                    <input type="text" name="keterangan" value="{{ $tagihan->keterangan }}" class="w-full rounded-lg border-gray-300 dark:border-[#2a452e] dark:bg-[#1a2e1e] dark:text-white focus:ring-primary focus:border-primary">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-[#2a452e] dark:bg-[#1a2e1e] dark:text-white focus:ring-primary focus:border-primary">
                        <option value="belum" {{ $tagihan->status == 'belum' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="cicilan" {{ $tagihan->status == 'cicilan' ? 'selected' : '' }}>Cicilan (Sebagian)</option>
                        <option value="lunas" {{ $tagihan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('keuangan.santri.keuangan.index', $tagihan->siswa_id) }}" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#2a452e] rounded-lg font-bold text-sm">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-primary text-[#111812] rounded-lg font-bold text-sm hover:shadow-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

