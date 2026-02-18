<x-app-layout>
    <x-slot name="header">
        Transaksi Pembayaran SPP
    </x-slot>

    <div class="max-w-[1400px] mx-auto">
        <!-- Breadcrumb -->
        <div class="flex flex-wrap gap-2 pb-6">
            <a class="text-[#618968] dark:text-[#8ab391] text-sm md:text-base font-medium leading-normal hover:underline" href="#">Keuangan</a>
            <span class="text-[#618968] dark:text-[#8ab391] text-sm md:text-base font-medium leading-normal">/</span>
            <span class="text-[#111812] dark:text-white text-sm md:text-base font-medium leading-normal">Transaksi SPP</span>
        </div>

        <!-- Page Heading -->
        <div class="flex flex-wrap justify-between items-end gap-4 mb-8">
            <div class="flex flex-col gap-2">
                <h1 class="text-[#111812] dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Transaksi Pembayaran SPP</h1>
                <p class="text-[#618968] dark:text-[#8ab391] text-base font-normal leading-normal">Kelola pembayaran SPP tunai atau via tabungan santri.</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-[#618968] bg-white dark:bg-[#1a2c1d] px-4 py-2 rounded-lg border border-[#e0e0e0] dark:border-[#2a3c2d]">
                <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                <span>Hari ini: <span class="font-bold text-[#111812] dark:text-white">{{ now()->format('d F Y') }}</span></span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: Input Form -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                <div class="bg-white dark:bg-[#1a2c1d] rounded-xl p-6 shadow-sm border border-[#e0e0e0] dark:border-[#2a3c2d]">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">edit_document</span>
                        Input Pembayaran
                    </h2>
                    <form class="flex flex-col gap-6" onsubmit="event.preventDefault();">
                        <!-- Search Santri -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[#111812] dark:text-white text-base font-medium leading-normal">Cari Santri</label>
                            <div class="relative flex w-full items-center">
                                <input class="w-full rounded-lg border border-[#dbe6dd] dark:border-[#2a3c2d] bg-white dark:bg-[#132215] h-14 pl-4 pr-12 text-base text-[#111812] dark:text-white placeholder:text-[#618968] focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" placeholder="Ketik Nama atau NIS Santri..." type="text" value="Ahmad Fulan (NIS: 12345)"/>
                                <div class="absolute right-4 flex items-center justify-center text-[#618968]">
                                    <span class="material-symbols-outlined">search</span>
                                </div>
                            </div>
                            <!-- Selected Santri Card -->
                            <div class="mt-2 flex items-center gap-3 p-3 bg-background-light dark:bg-[#132215] rounded-lg border border-dashed border-[#dbe6dd] dark:border-[#2a3c2d]">
                                <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">AF</div>
                                <div>
                                    <p class="text-sm font-bold text-[#111812] dark:text-white">Ahmad Fulan</p>
                                    <p class="text-xs text-[#618968]">Kelas 10A • Asrama Putra 1</p>
                                </div>
                                <button class="ml-auto text-xs text-red-500 hover:text-red-700 font-medium">Ubah</button>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Bulan Tagihan -->
                            <div class="flex-1 flex flex-col gap-2">
                                <label class="text-[#111812] dark:text-white text-base font-medium leading-normal">Bulan Tagihan</label>
                                <div class="relative">
                                    <select class="w-full appearance-none rounded-lg border border-[#dbe6dd] dark:border-[#2a3c2d] bg-white dark:bg-[#132215] h-14 px-4 text-base text-[#111812] dark:text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none cursor-pointer">
                                        <option>Oktober {{ date('Y') }}</option>
                                        <option>September {{ date('Y') }}</option>
                                        <option>Agustus {{ date('Y') }}</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#618968]">
                                        <span class="material-symbols-outlined">expand_more</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Nominal -->
                            <div class="flex-1 flex flex-col gap-2">
                                <label class="text-[#111812] dark:text-white text-base font-medium leading-normal">Nominal SPP (Rp)</label>
                                <div class="relative flex w-full items-center">
                                    <div class="absolute left-4 text-[#618968] font-medium">Rp</div>
                                    <input class="w-full rounded-lg border border-[#dbe6dd] dark:border-[#2a3c2d] bg-white dark:bg-[#132215] h-14 pl-12 pr-4 text-base font-bold text-[#111812] dark:text-white placeholder:text-[#618968] focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" type="text" value="150.000"/>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="flex flex-col gap-3 pt-2">
                            <label class="text-[#111812] dark:text-white text-base font-medium leading-normal">Metode Pembayaran</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative cursor-pointer group">
                                    <input class="peer sr-only" name="payment_method" type="radio"/>
                                    <div class="flex items-center gap-4 p-4 rounded-xl border border-[#dbe6dd] dark:border-[#2a3c2d] bg-white dark:bg-[#132215] hover:bg-gray-50 dark:hover:bg-[#1e3322] peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full">
                                        <div class="h-12 w-12 rounded-full bg-gray-100 dark:bg-[#2a3c2d] peer-checked:bg-primary peer-checked:text-white text-[#111812] dark:text-white flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined">payments</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-[#111812] dark:text-white">Bayar Tunai</span>
                                            <span class="text-sm text-[#618968]">Pembayaran langsung</span>
                                        </div>
                                        <div class="ml-auto opacity-0 peer-checked:opacity-100 text-primary transition-opacity">
                                            <span class="material-symbols-outlined">check_circle</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input checked="" class="peer sr-only" name="payment_method" type="radio"/>
                                    <div class="flex items-center gap-4 p-4 rounded-xl border border-[#dbe6dd] dark:border-[#2a3c2d] bg-white dark:bg-[#132215] hover:bg-gray-50 dark:hover:bg-[#1e3322] peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full">
                                        <div class="h-12 w-12 rounded-full bg-gray-100 dark:bg-[#2a3c2d] peer-checked:bg-primary peer-checked:text-[#111812] text-[#111812] dark:text-white flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined">account_balance_wallet</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-[#111812] dark:text-white">Ambil dari Tabungan</span>
                                            <span class="text-sm text-[#618968]">Potong saldo santri</span>
                                        </div>
                                        <div class="ml-auto opacity-0 peer-checked:opacity-100 text-primary transition-opacity">
                                            <span class="material-symbols-outlined">check_circle</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Savings Info -->
                        <div class="flex flex-col bg-[#f0fdf4] dark:bg-[#0f2915] border border-primary/20 rounded-xl p-4 gap-3">
                            <div class="flex items-center gap-2 text-primary font-medium">
                                <span class="material-symbols-outlined text-[20px]">info</span>
                                <span>Informasi Saldo Tabungan</span>
                            </div>
                            <div class="h-px bg-primary/20 w-full"></div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-[#618968] dark:text-[#8ab391]">Saldo Saat Ini</span>
                                <span class="font-bold text-[#111812] dark:text-white">Rp 500.000</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-[#618968] dark:text-[#8ab391]">Akan Dipotong</span>
                                <span class="font-bold text-red-500">- Rp 150.000</span>
                            </div>
                            <div class="flex justify-between items-center text-sm pt-2 border-t border-primary/10">
                                <span class="font-bold text-[#111812] dark:text-white">Sisa Saldo</span>
                                <span class="font-bold text-primary text-base">Rp 350.000</span>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-[#132215] rounded-lg border border-[#dbe6dd] dark:border-[#2a3c2d]">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-[#25D366]/10 flex items-center justify-center text-[#25D366]">
                                    <svg class="bi bi-whatsapp" fill="currentColor" height="20" viewBox="0 0 16 16" width="20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"></path>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#111812] dark:text-white">Kirim Notifikasi WA</span>
                                    <span class="text-xs text-[#618968]">Kirim bukti bayar ke 0812-xxxx-xxxx</span>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input checked="" class="sr-only peer" type="checkbox" value=""/>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-[#2a3c2d] peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-[#25D366]"></div>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button class="mt-4 w-full h-14 rounded-lg bg-primary hover:bg-[#0fdb30] active:scale-[0.99] text-[#052e0d] text-lg font-bold flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined">check</span>
                            Proses Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Student Info & History -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- Info Card -->
                <div class="bg-white dark:bg-[#1a2c1d] rounded-xl overflow-hidden shadow-sm border border-[#e0e0e0] dark:border-[#2a3c2d]">
                    <div class="h-24 bg-gradient-to-r from-primary/80 to-primary/40 relative">
                        <div class="absolute -bottom-8 left-6 h-20 w-20 rounded-full border-4 border-white dark:border-[#1a2c1d] overflow-hidden bg-gray-200">
                            <img alt="Student portrait" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC-imqDBA5di7kU7A4IklDFKUobdwaQ0PWlUALviAEQzZVADLka0EkHgFom3jGf2Podv47AisbKEhOh6mQf9WiXK_OBDvJHTZPUl8_M8yM7WSRwE9W6MMvAchIxcHZWbIY3BjV2AvOboVw7HXEoiIm-8bbSPccY7EtW_BMWt_ZG8nJ8QWEsVVoU5zM3PcTiVH0VmUya-Tb9FhSBcIWok2XNUr_W-_A_Ill1jr0WFgyrQoxOHrr8WBk0shvkkcOpnkKVMwW_1sQHsx5V"/>
                        </div>
                    </div>
                    <div class="pt-10 px-6 pb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-[#111812] dark:text-white">Ahmad Fulan</h3>
                                <p class="text-sm text-[#618968]">NIS: 12345</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs font-bold">Aktif</span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div class="flex flex-col p-3 rounded-lg bg-background-light dark:bg-[#132215]">
                                <span class="text-xs text-[#618968]">Kelas</span>
                                <span class="font-bold text-[#111812] dark:text-white">10 IPA 1</span>
                            </div>
                            <div class="flex flex-col p-3 rounded-lg bg-background-light dark:bg-[#132215]">
                                <span class="text-xs text-[#618968]">Tunggakan</span>
                                <span class="font-bold text-red-500">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History -->
                <div class="bg-white dark:bg-[#1a2c1d] rounded-xl p-6 shadow-sm border border-[#e0e0e0] dark:border-[#2a3c2d] flex-1">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-[#111812] dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">history</span>
                            Riwayat Terakhir
                        </h3>
                        <a class="text-xs font-bold text-primary hover:underline" href="#">Lihat Semua</a>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4 py-2 border-b border-[#f0f4f1] dark:border-[#2a3c2d] last:border-0">
                            <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">check</span>
                            </div>
                            <div class="flex flex-col flex-1">
                                <span class="text-sm font-bold text-[#111812] dark:text-white">SPP September</span>
                                <span class="text-xs text-[#618968]">10 Sep 2023 • Tunai</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-[#111812] dark:text-white">150rb</span>
                            </div>
                        </div>
                        <!-- More history items... -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

