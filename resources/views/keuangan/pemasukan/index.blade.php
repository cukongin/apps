<x-app-layout>
    <x-slot name="header">
        Manajemen Pemasukan Lain
    </x-slot>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Input -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-[#111812] dark:text-white mb-4">Input Pemasukan Baru</h3>
                    <p class="text-xs text-[#618968] mb-6">Catat pemasukan manual di luar pembayaran santri (otomatis).</p>

                    <form action="{{ route('keuangan.pemasukan.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Sumber Dana</label>
                                <input type="text" name="sumber" required class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white" placeholder="Contoh: Hamba Allah, Kas Kantin">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Nominal (Rp)</label>
                                <input type="number" name="jumlah" required min="0" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white" placeholder="0">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Tanggal Terima</label>
                                <input type="date" name="tanggal_pemasukan" required value="{{ date('Y-m-d') }}" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1 flex justify-between items-center">
                                    Kategori
                                    <button type="button" onclick="document.getElementById('modalKategori').classList.remove('hidden')" class="text-[10px] text-blue-600 hover:underline">
                                        + Kelola Kategori
                                    </button>
                                </label>
                                <select name="kategori" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach($kategoriList as $kat)
                                        <option value="{{ $kat->nama }}">{{ $kat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Keterangan</label>
                                <textarea name="keterangan" rows="3" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-3 rounded-xl shadow-lg shadow-green-500/20 transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">add_circle</span>
                                SIMPAN PEMASUKAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Table -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] overflow-hidden">
                    <div class="p-6 border-b border-[#dbe6dd] dark:border-[#2a3a2d] flex justify-between items-end">
                        <div class="flex-1">
                            <h2 class="text-xl font-black text-[#111812] dark:text-white">Riwayat Pemasukan Lain</h2>
                            <p class="text-sm text-[#618968] mt-1">Daftar uang masuk manual (Non-SPP).</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-[#618968] uppercase font-bold">Total Terkumpul</p>
                            <p class="text-2xl font-black text-green-600 mb-2">Rp {{ number_format(\App\Keuangan\Models\Pemasukan::sum('jumlah'), 0, ',', '.') }}</p>

                            <form action="{{ route('keuangan.pemasukan.destroy-all') }}" method="POST"
                                  data-confirm-delete="true"
                                  data-title="Hapus SEMUA Data?"
                                  data-message="PERINGATAN: Ini akan menghapus SELURUH riwayat pemasukan lain. Tindakan ini tidak dapat dibatalkan!"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-1 rounded border border-red-200 transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">delete_forever</span>
                                    HAPUS SEMUA DATA
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-[#1e3a24]">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Tanggal</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase">Sumber & Keterangan</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase text-right">Jumlah</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#618968] uppercase text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                                @forelse($pemasukans as $p)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3b25] transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-[#111812] dark:text-white whitespace-nowrap">
                                        {{ $p->tanggal_pemasukan->format('d M Y') }}
                                        <div class="text-[10px] text-[#618968] font-normal">{{ $p->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-[#111812] dark:text-white">{{ $p->sumber }}</p>
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#f0f4f1] text-[#618968] mt-1">{{ $p->kategori }}</span>
                                        @if($p->keterangan)
                                            <p class="text-xs text-gray-500 mt-1 italic">{{Str::limit($p->keterangan, 50)}}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-green-600 text-right">
                                        Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if(auth()->user()->role == 'admin_utama' || $p->user_id == auth()->id())
                                        <form action="{{ route('keuangan.pemasukan.destroy', $p->id) }}" method="POST"
                                              data-confirm-delete="true"
                                              data-title="Hapus Pemasukan?"
                                              data-message="Data pemasukan ini akan dihapus permanen!">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="size-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">account_balance_wallet</span>
                                        <p>Belum ada data pemasukan lain.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-[#dbe6dd] dark:border-[#2a3a2d]">
                        {{ $pemasukans->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Kelola Kategori -->
    <div id="modalKategori" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modalKategori').classList.add('hidden')"></div>
            <div class="inline-block align-bottom bg-white dark:bg-[#1a2e1d] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Kelola Kategori Pemasukan
                    </h3>

                    <form action="{{ route('keuangan.kategori-pemasukan.store') }}" method="POST" class="mb-6 p-4 bg-gray-50 dark:bg-[#1e3a24] rounded-lg border border-gray-200 dark:border-gray-700">
                        @csrf
                        <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Tambah Kategori Baru</label>
                        <div class="flex gap-2">
                            <input type="text" name="nama" required class="flex-1 rounded-md border-gray-300 dark:bg-[#1a2e1e] dark:text-white dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50" placeholder="Nama Kategori">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white text-xs font-bold uppercase rounded hover:bg-green-700 transition">
                                Tambah
                            </button>
                        </div>
                    </form>

                    <div class="max-h-60 overflow-y-auto border-t border-gray-200 dark:border-gray-700 pt-4">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($kategoriList as $k)
                                <tr>
                                    <td class="py-2 text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $k->nama }}</td>
                                    <td class="py-2 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="toggleEdit('{{ $k->id }}')" class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                                            <form action="{{ route('keuangan.kategori-pemasukan.destroy', $k->id) }}" method="POST"
                                                  data-confirm-delete="true"
                                                  data-title="Hapus Kategori?"
                                                  data-message="Hapus kategori ini?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="edit-row-{{ $k->id }}" class="hidden bg-blue-50 dark:bg-blue-900/20">
                                    <td colspan="2" class="p-2">
                                        <form action="{{ route('keuangan.kategori-pemasukan.update', $k->id) }}" method="POST" class="flex gap-2">
                                            @csrf @method('PUT')
                                            <input type="text" name="nama" value="{{ $k->nama }}" required class="flex-1 text-sm rounded-md border-gray-300 dark:bg-[#1a2e1e] dark:text-white">
                                            <button type="submit" class="text-xs bg-blue-600 text-white px-2 py-1 rounded">Simpan</button>
                                            <button type="button" onclick="toggleEdit('{{ $k->id }}')" class="text-xs text-gray-500 hover:text-gray-700">Batal</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-[#1e3a24] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="document.getElementById('modalKategori').classList.add('hidden')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEdit(id) {
            const row = document.getElementById('edit-row-' + id);
            if(row.classList.contains('hidden')) {
                document.querySelectorAll('[id^="edit-row-"]').forEach(el => el.classList.add('hidden'));
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>

