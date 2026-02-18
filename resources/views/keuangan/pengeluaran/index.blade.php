<x-app-layout>
    <x-slot name="header">
        Manajemen Pengeluaran
    </x-slot>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Input -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-[#111812] dark:text-white mb-4">Input Pengeluaran Baru</h3>

                    <form action="{{ route('keuangan.pengeluaran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <!-- Judul (Full Width) -->
                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Judul Pengeluaran</label>
                                <input type="text" name="judul" required class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white" placeholder="Contoh: Beli ATK/Sarpras">
                            </div>

                            <!-- Tanggal & Kategori (2 Columns) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Tanggal</label>
                                    <input type="date" name="tanggal_pengeluaran" required value="{{ date('Y-m-d') }}" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-[#618968] mb-1 flex justify-between items-center">
                                        Kategori
                                        <button type="button" onclick="document.getElementById('modalKategori').classList.remove('hidden')" class="text-[10px] text-blue-600 hover:underline">+ Kelola</button>
                                    </label>
                                    <select name="kategori" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white">
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        @foreach($kategoriList as $kat)
                                        <option value="{{ $kat->nama }}">{{ $kat->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Input Mode Toggle -->
                            <div class="mb-4 bg-gray-50 dark:bg-[#1a2e1e] p-3 rounded-lg border border-[#dbe6dd] dark:border-[#2a3a2d] flex justify-between items-center">
                                <span class="text-xs font-bold uppercase text-[#618968]">Mode Input</span>
                                <label class="inline-flex items-center cursor-pointer">
                                    <span class="mr-3 text-xs font-bold text-gray-600 dark:text-gray-300">Total Langsung</span>
                                    <input type="checkbox" id="modeToggle" class="sr-only peer" onchange="toggleInputMode()">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    <span class="ml-3 text-xs font-bold text-gray-600 dark:text-gray-300">Rinci</span>
                                </label>
                            </div>

                            <!-- Simple Input (Total Only) -->
                            <div id="simple-input-container">
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Total Nominal (Rp)</label>
                                <input type="number" id="simple_nominal" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-lg font-bold text-[#111812] dark:text-white" placeholder="0">
                            </div>

                            <!-- Dynamic Items Table -->
                            <div id="detail-input-container" class="hidden">
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Rincian Belanja</label>
                                <div class="bg-[#f6f8f6] dark:bg-[#1e3a24] rounded-lg p-3 space-y-3" id="itemsContainer">
                                    <!-- Rows added via JS -->
                                </div>
                                <button type="button" id="addItemBtn" onclick="addRow()" class="mt-2 w-full py-2 border-2 border-dashed border-[#618968]/30 hover:border-[#618968] text-[#618968] font-bold text-xs rounded-lg transition-colors flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                    TAMBAH BARANG
                                </button>

                                <div id="grandTotalContainer" class="flex justify-end items-center mt-3 px-1 border-t border-dashed border-gray-300 pt-2">
                                    <span class="text-xs font-bold text-[#618968] mr-2">Total Estimasi:</span>
                                    <span class="text-lg font-black text-red-600" id="grandTotalDisplay">Rp 0</span>
                                </div>
                            </div>

                            <!-- Upload Nota -->
                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Upload Nota/Struk (Opsional)</label>
                                <input type="file" name="bukti_foto" accept="image/*" class="w-full text-xs bg-[#f6f8f6] dark:bg-[#1e3a24] rounded-lg text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#e8ece9] file:text-[#618968] hover:file:bg-[#dbe6dd]">
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Catatan Tambahan</label>
                                <textarea name="deskripsi" rows="2" class="w-full bg-[#f6f8f6] dark:bg-[#1e3a24] border-none rounded-lg focus:ring-2 focus:ring-primary text-sm font-bold text-[#111812] dark:text-white"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-3 rounded-xl shadow-lg shadow-red-500/20 transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">save</span>
                                SIMPAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Table -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-end">
                        <div class="flex-1">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white">Daftar Pengeluaran</h2>
                            <p class="text-sm text-slate-500 mt-1">Riwayat belanja operasional madrasah.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500 uppercase font-bold">Total Pengeluaran</p>
                            <p class="text-2xl font-black text-red-600">Rp {{ number_format(\App\Keuangan\Models\Pengeluaran::sum('jumlah'), 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Jumlah</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($pengeluarans as $p)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-white whitespace-nowrap">
                                        {{ $p->tanggal_pengeluaran->format('d M Y') }}
                                        <div class="text-[10px] text-slate-400 font-normal">{{ $p->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-slate-700 dark:text-white">{{ $p->judul }}</p>
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 mt-1">{{ $p->kategori }}</span>
                                        @if($p->deskripsi)
                                            <p class="text-xs text-slate-500 mt-1 italic">{{Str::limit($p->deskripsi, 50)}}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-red-600 text-right">
                                        Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" onclick="showDetail({{ $p }})" class="size-8 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Detail & Nota">
                                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                            </button>

                                            @if(auth()->user()->role == 'admin_utama' || $p->user_id == auth()->id())
                                            <form action="{{ route('keuangan.pengeluaran.destroy', $p->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Hapus Pengeluaran?', 'Data pengeluaran ini akan dihapus permanen!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="size-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">receipt_long</span>
                                        <p>Belum ada data pengeluaran.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                        {{ $pengeluarans->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Kelola Kategori -->
    <div id="modalKategori" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modalKategori').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-[#1a2e1d] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" id="modal-title">
                        Kelola Kategori Pengeluaran
                    </h3>

                    <!-- Form Tambah -->
                    <form action="{{ route('keuangan.kategori-pengeluaran.store') }}" method="POST" class="mb-6 p-4 bg-gray-50 dark:bg-[#1e3a24] rounded-lg border border-gray-200 dark:border-gray-700">
                        @csrf
                        <label class="block text-xs font-bold uppercase text-[#618968] mb-1">Tambah Kategori Baru</label>
                        <div class="flex gap-2">
                            <input type="text" name="nama" required class="flex-1 rounded-md border-gray-300 dark:bg-[#1a2e1e] dark:text-white dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50" placeholder="Nama Kategori">
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold uppercase rounded hover:bg-green-700 transition">
                                Tambah
                            </button>
                        </div>
                    </form>

                    <!-- List Kategori -->
                    <div class="max-h-60 overflow-y-auto border-t border-gray-200 dark:border-gray-700 pt-4">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($kategoriList as $k)
                                <tr>
                                    <td class="py-2 text-sm text-gray-700 dark:text-gray-300 flex justify-between items-center group">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-gray-400 text-[18px]">folder</span>
                                            {{ $k->nama }}
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button" onclick="toggleEdit('{{ $k->id }}', '{{ $k->nama }}')" class="text-blue-600 hover:text-blue-800 p-1">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                            </button>
                                            <form action="{{ route('keuangan.kategori-pengeluaran.destroy', $k->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Hapus Kategori?', 'Hapus kategori ini? Data yang terkait tidak akan ikut terhapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Hidden Edit Form Row -->
                                <tr id="edit-row-{{ $k->id }}" class="hidden bg-blue-50 dark:bg-blue-900/20">
                                    <td colspan="2" class="p-2">
                                        <form action="{{ route('keuangan.kategori-pengeluaran.update', $k->id) }}" method="POST" class="flex gap-2">
                                            @csrf
                                            @method('PUT')
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
                    <button type="button" onclick="document.getElementById('modalKategori').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pengeluaran -->
    <div id="modalDetail" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modalDetail').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-[#1a2e1d] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <div class="bg-white dark:bg-[#1a2e1d] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-detail-title">Detail Pengeluaran</h3>
                            <div class="mt-4">
                                <!-- Image Preview -->
                                <div id="detail-image-container" class="mb-4 hidden">
                                    <p class="text-xs font-bold text-[#618968] mb-1">Nota/Struk:</p>
                                    <img id="detail-image" src="" class="max-w-full h-auto rounded-lg border border-gray-200" alt="Bukti Struk">
                                </div>

                                <!-- Items Table -->
                                <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-[#1e3a24]">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-[#618968] uppercase tracking-wider">Barang</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-[#618968] uppercase tracking-wider">Qty</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-[#618968] uppercase tracking-wider">Harga</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-[#618968] uppercase tracking-wider">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail-items-body" class="bg-white dark:bg-[#1a2e1e] divide-y divide-gray-200 dark:divide-gray-700">
                                            <!-- JS Populated -->
                                        </tbody>
                                        <tfoot class="bg-gray-50 dark:bg-[#1e3a24]">
                                            <tr>
                                                <td colspan="3" class="px-6 py-3 text-right text-xs font-bold text-[#618968] uppercase">Total</td>
                                                <td class="px-6 py-3 text-right text-sm font-black text-red-600" id="detail-total">Rp 0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <p id="detail-deskripsi" class="mt-4 text-sm text-gray-500 italic"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-[#1e3a24] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="document.getElementById('modalDetail').classList.add('hidden')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Datalist for Satuan Suggestions -->
    <datalist id="listSatuan">
        @foreach($satuanList as $s)
            <option value="{{ $s }}"></option>
        @endforeach
    </datalist>

    <script>
        // --- Input Logic ---
        let rowCount = 0;
        let isSimpleMode = true; // Default to simple (Total Langsung)

        function toggleInputMode() {
            const toggle = document.getElementById('modeToggle');
            const simpleContainer = document.getElementById('simple-input-container');
            const detailContainer = document.getElementById('detail-input-container');
            const simpleNominal = document.getElementById('simple_nominal');
            const detailInputs = detailContainer.querySelectorAll('input');
            const addButton = document.getElementById('addItemBtn');
            const totalDisplay = document.getElementById('grandTotalContainer');

            // If toggle is CHECKED, logic is "Rinci" (Detailed). If Unchecked, logic is "Simple" (Total Langsung).
            isSimpleMode = !toggle.checked;

            if (isSimpleMode) {
                // Show Simple, Hide Detail
                simpleContainer.classList.remove('hidden');
                detailContainer.classList.add('hidden');
                if(addButton) addButton.style.display = 'none'; // Hide Add Button
                if(totalDisplay) totalDisplay.style.display = 'none'; // Hide Total Display

                // Enable Simple, Disable Detail Inputs
                simpleNominal.disabled = false;
                simpleNominal.required = true;

                detailInputs.forEach(input => input.disabled = true);
            } else {
                // Show Detail, Hide Simple
                simpleContainer.classList.add('hidden');
                detailContainer.classList.remove('hidden');
                if(addButton) addButton.style.display = 'flex'; // Show Add Button
                if(totalDisplay) totalDisplay.style.display = 'flex'; // Show Total Display

                // Disable Simple
                simpleNominal.disabled = true;
                simpleNominal.required = false;

                // Enable Detail Inputs
                detailInputs.forEach(input => input.disabled = false);
            }
        }

        // Hook into Form Submit to inject data if Simple Mode
        document.querySelector('form[action="{{ route('keuangan.pengeluaran.store') }}"]').addEventListener('submit', function(e) {
            if (isSimpleMode) {
                const judul = document.querySelector('input[name="judul"]').value;
                const nominal = document.getElementById('simple_nominal').value;

                // Create hidden inputs to mimic the details array validation
                const hiddenContainer = document.createElement('div');
                hiddenContainer.innerHTML = `
                    <input type="hidden" name="details[0][nama_barang]" value="${judul}">
                    <input type="hidden" name="details[0][jumlah]" value="1">
                    <input type="hidden" name="details[0][satuan]" value="-">
                    <input type="hidden" name="details[0][harga_satuan]" value="${nominal}">
                `;
                this.appendChild(hiddenContainer);
            }
        });

        // Initialize Mode
        document.addEventListener('DOMContentLoaded', () => {
             // Ensure correct initial state
             toggleInputMode();

             // Also add the first row for detail mode
             if (document.querySelectorAll('.item-row').length === 0) {
                 addRow();
             }
        });

        function addRow() {
            const container = document.getElementById('itemsContainer');
            const rowCount = container.children.length;

            const div = document.createElement('div');
            div.className = 'item-row bg-white dark:bg-[#1a2e1e] p-3 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 relative group';
            div.innerHTML = `
                <div class="mb-2">
                    <label class="block text-[10px] uppercase text-gray-400 font-bold mb-1">Nama Barang</label>
                    <input type="text" name="details[${rowCount}][nama_barang]" class="detail-input w-full text-sm font-bold bg-gray-50 dark:bg-[#2a3a2d] border-none rounded focus:ring-1 focus:ring-green-500 dark:text-white placeholder-gray-400" placeholder="Contoh: Beras 5kg">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-2">
                    <div>
                        <label class="block text-[10px] uppercase text-gray-400 font-bold mb-1">Qty</label>
                        <input type="number" name="details[${rowCount}][jumlah]" min="1" value="1" oninput="calculateTotal()" class="detail-input w-full text-sm font-bold text-center bg-gray-50 dark:bg-[#2a3a2d] border-none rounded focus:ring-1 focus:ring-green-500 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase text-gray-400 font-bold mb-1">Satuan</label>
                        <input type="text" name="details[${rowCount}][satuan]" list="listSatuan" class="detail-input w-full text-xs font-bold text-center bg-gray-50 dark:bg-[#2a3a2d] border-none rounded focus:ring-1 focus:ring-green-500 dark:text-white" placeholder="Pcs">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] uppercase text-gray-400 font-bold mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="details[${rowCount}][harga_satuan]" min="0" oninput="calculateTotal()" class="detail-input w-full text-sm font-bold text-right bg-gray-50 dark:bg-[#2a3a2d] border-none rounded focus:ring-1 focus:ring-green-500 dark:text-white" placeholder="0">
                </div>

                <button type="button" onclick="this.closest('.item-row').remove(); calculateTotal()" class="absolute -top-2 -right-2 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;
            container.appendChild(div);

            // Apply disabled state if currently in simple mode
            if (isSimpleMode) {
                 div.querySelectorAll('input').forEach(inp => inp.disabled = true);
            }
        }

        function calculateTotal() {
            let total = 0;
            const rows = document.querySelectorAll('.item-row');
            rows.forEach(row => {
                const qtyInput = row.querySelector('input[name*="[jumlah]"]');
                const priceInput = row.querySelector('input[name*="[harga_satuan]"]');
                if (qtyInput && priceInput && !qtyInput.disabled) { // Only calc enabled inputs
                    const qty = parseFloat(qtyInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;
                    total += qty * price;
                }
            });
            document.getElementById('grandTotalDisplay').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        // Re-attach detail modal and edit toggle logic...
        function showDetail(pengeluaran) {
            // Populate Modal
            document.getElementById('modal-detail-title').innerText = pengeluaran.judul + ' (' + new Date(pengeluaran.tanggal_pengeluaran).toLocaleDateString() + ')';

            // Image
            const imgContainer = document.getElementById('detail-image-container');
            const img = document.getElementById('detail-image');
            if (pengeluaran.bukti_foto) {
                img.src = '/storage/' + pengeluaran.bukti_foto;
                imgContainer.classList.remove('hidden');
            } else {
                imgContainer.classList.add('hidden');
            }

            // Description
            document.getElementById('detail-deskripsi').innerText = pengeluaran.deskripsi || '';

            // Items
            const tbody = document.getElementById('detail-items-body');
            tbody.innerHTML = '';
            let total = 0;

            if (pengeluaran.details && pengeluaran.details.length > 0) {
                pengeluaran.details.forEach(item => {
                    const subtotal = item.jumlah * item.harga_satuan;
                    total += parseFloat(subtotal);
                    const row = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3b25]">
                            <td class="px-6 py-2 text-sm text-gray-900 dark:text-gray-100">${item.nama_barang}</td>
                            <td class="px-6 py-2 text-xs text-right text-gray-500 dark:text-gray-400 font-mono">${item.jumlah}</td>
                            <td class="px-6 py-2 text-xs text-right text-gray-500 dark:text-gray-400 font-mono">Rp ${new Intl.NumberFormat('id-ID').format(item.harga_satuan)}</td>
                            <td class="px-6 py-2 text-sm text-right font-bold text-gray-900 dark:text-white">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                // Compatibility for old records without details
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 italic">Total Pengeluaran (Manual)</td>
                        <td class="px-6 py-4 text-right text-sm font-bold">Rp ${new Intl.NumberFormat('id-ID').format(pengeluaran.jumlah)}</td>
                    </tr>
                `;
                total = pengeluaran.jumlah;
            }

            document.getElementById('detail-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);

            document.getElementById('modalDetail').classList.remove('hidden');
        }

        function toggleEdit(id, name) {
            const row = document.getElementById('edit-row-' + id);
            if (row.classList.contains('hidden')) {
                document.querySelectorAll('[id^="edit-row-"]').forEach(el => el.classList.add('hidden'));
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>

