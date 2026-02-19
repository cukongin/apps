<x-app-layout>
    <x-slot name="header">
        Detail Tabungan Santri
    </x-slot>

    <div class="max-w-[1280px] mx-auto p-4 lg:p-6" x-data="{
        modalOpen: false,
        modalType: 'setor', // setor, tarik
        modalTitle: 'Setor Tunai',
        submitText: 'Setor Sekarang',
        modalClass: 'bg-green-600 hover:bg-green-500',
        iconName: 'add',
        iconClass: 'text-green-600',
        iconBgClass: 'bg-green-100',

        openModal(type) {
            this.modalType = type;
            this.modalOpen = true;
            if (type === 'setor') {
                this.modalTitle = 'Setor Tunai';
                this.submitText = 'Setor Sekarang';
                this.modalClass = 'bg-green-600 hover:bg-green-500';
                this.iconName = 'add';
                this.iconClass = 'text-green-600';
                this.iconBgClass = 'bg-green-100';
            } else {
                this.modalTitle = 'Tarik Tunai';
                this.submitText = 'Tarik Sekarang';
                this.modalClass = 'bg-red-600 hover:bg-red-500';
                this.iconName = 'arrow_downward';
                this.iconClass = 'text-red-600';
                this.iconBgClass = 'bg-red-100';
            }
        },
        closeModal() {
            this.modalOpen = false;
        }
    }">
        <!-- Breadcrumbs -->
        <div class="flex flex-wrap items-center gap-2 mb-6 text-sm">
            <a class="text-[#618968] dark:text-[#8ab391] hover:text-primary transition-colors" href="{{ route('keuangan.tabungan.index') }}">Data Tabungan</a>
            <span class="text-[#618968] dark:text-[#8ab391] material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-[#111812] dark:text-white font-semibold">{{ $santri->nama }}</span>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800 rounded-xl flex items-center gap-3 text-green-800 dark:text-green-300">
                <span class="material-symbols-outlined">check_circle</span>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-xl flex items-center gap-3 text-red-800 dark:text-red-300">
                <span class="material-symbols-outlined">error</span>
                <p class="font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Profile Card -->
            <div class="col-span-1 lg:col-span-2 bg-white dark:bg-[#1a2c1d] rounded-xl p-6 shadow-sm border border-[#e0e5e0] dark:border-[#2a3c2d] relative overflow-hidden group">
                <!-- Decorative background accent -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -mr-8 -mt-8 pointer-events-none"></div>
                <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center relative z-10">
                    <div class="relative">
                        <div class="bg-center bg-no-repeat bg-cover rounded-full size-24 sm:size-28 border-4 border-white dark:border-[#1a2c1d] shadow-md overflow-hidden bg-gray-100">
                            @if($santri->foto)
                                <img src="{{ asset('storage/' . $santri->foto) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-[#2a3a2d]">
                                    <span class="material-symbols-outlined text-gray-400 text-4xl">person</span>
                                </div>
                            @endif
                        </div>
                        <div class="absolute bottom-1 right-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs font-bold px-2 py-0.5 rounded-full border border-green-200 dark:border-green-800">
                            {{ $santri->status }}
                        </div>
                    </div>
                    <div class="flex-1 space-y-1">
                        <div class="flex justify-between items-start w-full">
                            <div>
                                <h1 class="text-[#111812] dark:text-white text-2xl font-bold leading-tight">{{ $santri->nama }}</h1>
                                <p class="text-[#618968] dark:text-gray-400 text-sm font-medium mt-1 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">school</span>
                                    {{ $santri->kelas->nama ?? 'Tanpa Kelas' }} • NIS: {{ $santri->nis }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-dashed border-[#e0e5e0] dark:border-[#2a3c2d] w-full max-w-md">
                            <p class="text-[#618968] dark:text-[#8ab391] text-xs uppercase tracking-wider font-semibold">Total Saldo Tabungan</p>
                            <p class="text-primary text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="col-span-1 bg-white dark:bg-[#1a2c1d] rounded-xl p-6 shadow-sm border border-[#e0e5e0] dark:border-[#2a3c2d] flex flex-col justify-center gap-4">
                <h3 class="text-[#111812] dark:text-white font-bold text-lg mb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">bolt</span>
                    Aksi Cepat
                </h3>

                <button type="button" @click="openModal('setor')" class="w-full flex items-center justify-between px-6 py-4 bg-primary hover:bg-[#0fdb30] text-white rounded-lg transition-all shadow-md hover:shadow-lg group">
                    <div class="flex flex-col items-start">
                        <span class="text-base font-bold">Setor Tunai</span>
                        <span class="text-xs opacity-90">Tambah saldo tabungan</span>
                    </div>
                    <span class="material-symbols-outlined bg-white/20 rounded-full p-1 group-hover:scale-110 transition-transform">add</span>
                </button>

                <button type="button" @click="openModal('tarik')" class="w-full flex items-center justify-between px-6 py-4 bg-[#f0f4f1] dark:bg-[#233526] hover:bg-[#e1e8e2] dark:hover:bg-[#2f4233] text-[#111812] dark:text-white rounded-lg transition-all border border-[#d1d9d2] dark:border-[#3a4d3e] group">
                    <div class="flex flex-col items-start">
                        <span class="text-base font-bold">Tarik Tunai</span>
                        <span class="text-xs text-[#618968]">Ambil saldo tabungan</span>
                    </div>
                    <span class="material-symbols-outlined text-[#618968] group-hover:text-[#111812] dark:group-hover:text-white transition-colors">arrow_downward</span>
                </button>
            </div>
        </div>

        <!-- Transaction History Section -->
        <div class="bg-white dark:bg-[#1a2c1d] rounded-xl shadow-sm border border-[#e0e5e0] dark:border-[#2a3c2d] flex flex-col overflow-hidden">
            <!-- Header & Filters -->
            <div class="p-5 border-b border-[#e0e5e0] dark:border-[#2a3c2d] flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="text-[#111812] dark:text-white text-xl font-bold flex items-center gap-2 self-start md:self-auto">
                    <span class="material-symbols-outlined text-primary">receipt_long</span>
                    Riwayat Transaksi
                </h2>
                <!-- Optional: Add filters later -->
            </div>
            <!-- Table -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <th class="py-4 px-4 md:px-6 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Tanggal</th>
                            <th class="hidden md:table-cell py-4 px-6 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Keterangan</th>
                            <th class="py-4 px-4 md:px-6 text-xs font-bold uppercase tracking-wider text-slate-500 text-right whitespace-nowrap">Masuk</th>
                            <th class="py-4 px-4 md:px-6 text-xs font-bold uppercase tracking-wider text-slate-500 text-right whitespace-nowrap">Keluar</th>
                            <th class="hidden md:table-cell py-4 px-6 text-xs font-bold uppercase tracking-wider text-slate-500 text-right whitespace-nowrap">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($santri->tabungans as $transaksi)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="py-4 px-4 md:px-6 text-sm text-slate-700 dark:text-white whitespace-normal font-medium max-w-[100px] md:max-w-none">
                                {{ $transaksi->created_at->format('d M y') }}
                                <div class="md:hidden text-[10px] text-slate-500 mt-1 truncate">{{ $transaksi->keterangan }}</div>
                            </td>
                            <td class="hidden md:table-cell py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="size-8 rounded-full {{ $transaksi->tipe == 'setor' ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : 'bg-red-100 dark:bg-red-900/30 text-red-600' }} flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[18px]">{{ $transaksi->tipe == 'setor' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-white capitalize">{{ $transaksi->tipe == 'setor' ? 'Setoran' : 'Penarikan' }}</span>
                                        <span class="text-xs text-slate-500">{{ $transaksi->keterangan }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 md:px-6 text-sm text-right font-bold text-green-600">
                                {{ $transaksi->tipe == 'setor' ? number_format($transaksi->jumlah, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-4 px-4 md:px-6 text-sm font-bold text-red-600 dark:text-red-400 text-right">
                                {{ $transaksi->tipe == 'tarik' ? number_format($transaksi->jumlah, 0, ',', '.') : '-' }}
                            </td>
                            <td class="hidden md:table-cell py-4 px-6 text-sm font-bold text-slate-700 dark:text-white text-right">
                                Rp {{ number_format($transaksi->saldo_akhir, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">Belum ada transaksi tabungan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modals -->
        <div x-show="modalOpen" class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal()"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                 <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="modalOpen"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#1a2c1d] text-left shadow-xl transition-all w-full sm:my-8 sm:w-full sm:max-w-lg">
                        <form action="{{ route('keuangan.tabungan.store', $santri->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipe" x-model="modalType">

                            <div class="bg-white dark:bg-[#1a2c1d] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="iconBgClass">
                                        <span class="material-symbols-outlined" :class="iconClass" x-text="iconName"></span>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg font-bold leading-6 text-[#111812] dark:text-white" x-text="modalTitle"></h3>
                                        <div class="mt-4 space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-[#618968] mb-1">Nominal (Rp)</label>
                                                <input type="number" name="jumlah" class="w-full rounded-lg border border-[#dbe6dd] dark:border-[#2a3c2d] px-4 py-2 font-bold text-lg focus:ring-2 focus:ring-primary outline-none dark:bg-[#1e3a24] dark:text-white" placeholder="0" min="1000" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-[#618968] mb-1">Keterangan (Opsional)</label>
                                                <input type="text" name="keterangan" class="w-full rounded-lg border border-[#dbe6dd] dark:border-[#2a3c2d] px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none dark:bg-[#1e3a24] dark:text-white" placeholder="Contoh: Titipan orang tua">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-[#1e3a24] px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" class="inline-flex w-full justify-center rounded-lg px-3 py-2 text-sm font-bold text-white shadow-sm sm:ml-3 sm:w-auto" :class="modalClass" x-text="submitText"></button>
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-[#2a3c2d] px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="closeModal()">Batal</button>
                            </div>
                        </form>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</x-app-layout>
