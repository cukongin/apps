<x-app-layout>
    <x-slot name="header">
        Data Keuangan & Tagihan Santri
    </x-slot>

    <div class="max-w-[1200px] mx-auto px-6 py-8">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 mb-6">
            <a class="text-[#618968] dark:text-[#a0c0a5] text-sm font-medium hover:underline" href="#">Keuangan</a>
            <span class="text-[#618968] text-sm font-medium">/</span>
            <span class="text-[#111812] dark:text-white text-sm font-bold">Rincian Keuangan Santri</span>
        </div>

        <!-- Student Profile Card -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">
                <div class="flex gap-6 items-center">
                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full h-24 w-24 border-4 border-primary/20" style='background-image: url("{{ $santri->foto ? asset($santri->foto) : "https://ui-avatars.com/api/?name=" . urlencode($santri->nama) . "&background=random" }}");'>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h1 class="text-[#111812] dark:text-white text-2xl font-extrabold tracking-tight">{{ $santri->nama }}</h1>
                        <p class="text-[#618968] dark:text-[#a0c0a5] text-base font-medium">NIS: {{ $santri->nis }} • <span class="text-primary font-bold">Kelas: {{ $santri->kelas->nama ?? 'Belum Ada Kelas' }}</span></p>
                        <p class="text-[#618968] dark:text-[#a0c0a5] text-sm">Wali Santri: {{ $santri->nama_wali ?? '-' }} • Telp: {{ $santri->no_hp ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('keuangan.santri.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 dark:bg-transparent dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                        Kembali
                    </a>


                </div>
            </div>
        </div>

        <!-- Financial Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#1a2e1d] border border-[#dbe6dd] dark:border-[#2a3a2d] shadow-sm relative overflow-hidden group">
                <div class="absolute right-[-10px] top-[-10px] text-primary/10 rotate-12">
                    <span class="material-symbols-outlined text-7xl">savings</span>
                </div>
                <p class="text-[#618968] dark:text-[#a0c0a5] text-sm font-bold uppercase tracking-wider">Saldo Tabungan</p>
                <div class="flex items-baseline gap-2">
                    <p class="text-[#111812] dark:text-white text-3xl font-black">Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}</p>
                    <p class="text-[#078825] dark:text-[#26d44c] text-sm font-bold bg-[#078825]/10 px-2 py-0.5 rounded">Aktif</p>
                </div>
                <a href="{{ route('keuangan.tabungan.show', $santri->id) }}" class="text-primary text-sm font-bold mt-2 flex items-center gap-1 hover:underline">
                    Lihat Riwayat <span class="material-symbols-outlined text-xs">arrow_forward_ios</span>
                </a>
            </div>
            <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#1a2e1d] border border-[#dbe6dd] dark:border-[#2a3a2d] shadow-sm relative overflow-hidden">
                <div class="absolute right-[-10px] top-[-10px] text-red-500/10 rotate-12">
                    <span class="material-symbols-outlined text-7xl">payments</span>
                </div>
                <p class="text-[#618968] dark:text-[#a0c0a5] text-sm font-bold uppercase tracking-wider">Sisa Tagihan</p>
                <p class="text-[#111812] dark:text-white text-3xl font-black">Rp {{ number_format($sisa_tagihan, 0, ',', '.') }}</p>
                <p class="text-[#618968] dark:text-[#a0c0a5] text-xs font-medium mt-2 italic">*Total tagihan belum lunas</p>
            </div>
            <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#1a2e1d] border border-[#dbe6dd] dark:border-[#2a3a2d] shadow-sm relative overflow-hidden">
                <div class="absolute right-[-10px] top-[-10px] text-primary/10 rotate-12">
                    <span class="material-symbols-outlined text-7xl">verified_user</span>
                </div>
                <p class="text-[#618968] dark:text-[#a0c0a5] text-sm font-bold uppercase tracking-wider">Status Keuangan</p>
                <p class="text-[#111812] dark:text-white text-3xl font-black">{{ $sisa_tagihan > 0 ? 'Ada Tagihan' : 'Lancar' }}</p>
                <div class="flex gap-1 mt-2">
                   @if($sisa_tagihan > 0)
                        <span class="h-2 w-full bg-red-500 rounded-full"></span>
                   @else
                        <span class="h-2 w-full bg-primary rounded-full"></span>
                   @endif
                </div>
            </div>
        </div>

        <!-- Section Header & Billing List -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] overflow-hidden">
            <!-- Rincian Biaya Wajib -->
            <div class="px-6 py-6 border-b border-[#dbe6dd] dark:border-[#2a3a2d]">
                <h3 class="text-[#111812] dark:text-white text-lg font-bold mb-4">Rincian Biaya Wajib</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($biayaWajib as $biaya)
                        @php
                            // Cek apakah biaya ini sudah lunas (Tidak ada tagihan 'belum'/'cicilan' DAN ada setidaknya 1 'lunas')
                            // Untuk bulanan: hijau jika tidak ada tunggakan. Untuk sekali: hijau jika sudah lunas.
                            $hasUnpaid = $santri->tagihans->where('jenis_biaya_id', $biaya->id)->where('status', '!=', 'lunas')->isNotEmpty();
                            $hasPaid   = $santri->tagihans->where('jenis_biaya_id', $biaya->id)->where('status', 'lunas')->isNotEmpty();
                            $isLunas   = !$hasUnpaid && $hasPaid;
                        @endphp
                    <div class="p-4 rounded-xl border transition-all group relative overflow-hidden {{ $isLunas ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-[#dbe6dd] dark:border-[#2a3a2d] bg-white dark:bg-[#1a2e1e] hover:border-primary/50' }}">

                        @if($isLunas)
                        <div class="absolute right-0 top-0 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg">
                            LUNAS
                        </div>
                        @endif

                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold {{ $isLunas ? 'text-green-800 dark:text-green-300' : 'text-[#111812] dark:text-white' }}">{{ $biaya->nama }}</h4>
                                <span class="text-xs {{ $isLunas ? 'text-green-600 dark:text-green-400' : 'text-[#618968] dark:text-[#a0c0a5]' }}">{{ ucfirst($biaya->tipe) }} &bull; {{ $biaya->kategori }}</span>
                            </div>
                            <span class="p-1.5 rounded-lg {{ $isLunas ? 'bg-green-200 text-green-700 dark:bg-green-800 dark:text-green-300' : 'bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white' }} transition-colors">
                                <span class="material-symbols-outlined text-sm">attach_money</span>
                            </span>
                        </div>
                        <div class="mt-2">
                            <span class="text-lg font-black {{ $isLunas ? 'text-green-700 dark:text-green-400' : 'text-[#111812] dark:text-white' }}">Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}</span>
                            @if($biaya->tipe == 'bulanan' && $biaya->recurring_day)
                                <p class="text-xs {{ $isLunas ? 'text-green-600' : 'text-[#618968]' }} mt-1">Tagihan tiap tgl {{ $biaya->recurring_day }}</p>
                            @elseif($biaya->tipe == 'sekali' && $biaya->due_date)
                                <p class="text-xs {{ $isLunas ? 'text-green-600' : 'text-red-500' }} mt-1">Batas: {{ \Carbon\Carbon::parse($biaya->due_date)->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8 text-gray-500 border border-dashed border-[#dbe6dd] rounded-xl">
                        Tidak ada biaya wajib yang berlaku untuk santri ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between px-6 py-5 border-b border-[#dbe6dd] dark:border-[#2a3a2d] bg-[#fcfdfc] dark:bg-[#1a2e1e]/30 gap-4">
                <h2 class="text-[#111812] dark:text-white text-xl font-bold">Daftar Tagihan</h2>
                <div class="flex flex-wrap items-center gap-2 overflow-x-auto max-w-full pb-1 md:pb-0">
                    <!-- Generate Button -->
                    <!-- Generate Annual Bills -->
                    <form action="{{ route('keuangan.tagihan.generate-future', $santri->id) }}" method="POST" class="flex flex-wrap items-center gap-1 bg-green-50 dark:bg-green-900/10 p-1 rounded-lg border border-green-100 dark:border-green-800 shrink-0" onsubmit="return confirm('Generate tagihan bulanan (SPP dll) sesuai konfigurasi?');">
                        @csrf
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-green-700 dark:text-green-400 font-bold pl-1">Mulai:</span>
                            <select name="start_month" class="text-[10px] py-1.5 px-3 border-none bg-white dark:bg-[#1e3a24] rounded text-[#111812] dark:text-white focus:ring-0 cursor-pointer min-w-[60px]">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == date('n')+1 ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('M') }}</option>
                                @endforeach
                            </select>
                            <select name="start_year" class="text-[10px] py-1.5 px-3 border-none bg-white dark:bg-[#1e3a24] rounded text-[#111812] dark:text-white focus:ring-0 cursor-pointer min-w-[60px]">
                                @foreach(range(date('Y'), date('Y')+1) as $y)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-1 pl-1 border-l border-green-200 dark:border-green-800">
                             <input type="number" name="months" value="5" class="w-12 text-[10px] py-1.5 px-1 border-none bg-white dark:bg-[#1e3a24] rounded text-center text-[#111812] dark:text-white focus:ring-0" min="1" max="24">
                             <span class="text-[10px] text-green-700 dark:text-green-400 font-bold">Bln</span>
                        </div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded transition-colors ml-1 flex items-center justify-center shadow-sm h-full" title="Generate">
                            <span class="text-[10px] font-bold mr-1">GO</span>
                            <span class="material-symbols-outlined text-[10px]">play_arrow</span>
                        </button>
                    </form>

                    <!-- Reset Button (Safety Mechanism) -->
                    <form action="{{ route('keuangan.tagihan.reset', $santri->id) }}" method="POST" onsubmit="return confirm('Hapus semua tagihan yang BELUM LUNAS? \n\nData yang sudah lunas/dicicil TIDAK akan dihapus.');">
                        @csrf
                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 p-1.5 rounded-lg transition-colors flex items-center justify-center shrink-0" title="Reset / Hapus Tagihan Belum Lunas">
                            <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                        </button>
                    </form>
                    <div class="h-6 w-px bg-gray-300 mx-1 hidden md:block"></div>
                    <div class="flex gap-1">
                        <button class="px-3 py-1.5 hover:bg-[#f0f4f1] dark:hover:bg-[#1a2e1e] text-[#618968] text-xs font-bold rounded whitespace-nowrap">Semua</button>
                        <button class="px-3 py-1.5 hover:bg-[#f0f4f1] dark:hover:bg-[#1a2e1e] text-[#618968] text-xs font-bold rounded whitespace-nowrap">Belum Lunas</button>
                        <button class="px-3 py-1.5 hover:bg-[#f0f4f1] dark:hover:bg-[#1a2e1e] text-[#618968] text-xs font-bold rounded whitespace-nowrap">Lunas</button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-background-light dark:bg-[#1a2e1e]">
                            <th class="px-6 py-4 text-[#618968] text-xs font-bold uppercase tracking-wider">Jenis Tagihan</th>
                            <th class="px-6 py-4 text-[#618968] text-xs font-bold uppercase tracking-wider">Periode/Item</th>
                            <th class="px-6 py-4 text-[#618968] text-xs font-bold uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-4 text-[#618968] text-xs font-bold uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-[#618968] text-xs font-bold uppercase tracking-wider text-right">Aksi</th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                        @forelse($santri->tagihans as $tagihan)
                        <tr class="hover:bg-primary/5 transition-colors text-[#111812] dark:text-white">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-9 rounded bg-[#f0f4f1] dark:bg-[#1a2e1e] flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined">
                                            {{ optional($tagihan->jenisBiaya)->nama == 'SPP Bulanan' ? 'calendar_month' : 'payments' }}
                                        </span>
                                    </div>
                                    <span class="font-bold text-sm">
                                        {{ optional($tagihan->jenisBiaya)->nama ?? 'Tanpa Nama' }}
                                        @if(optional($tagihan->jenisBiaya)->status == 'inactive')
                                            <span class="text-[10px] text-red-500 bg-red-50 dark:bg-red-900/20 px-1 py-0.5 rounded ml-1 font-bold">(Nonaktif)</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#618968] dark:text-[#a0c0a5]">{{ $tagihan->keterangan }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-sm block">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
                                @if($tagihan->terbayar > 0 && $tagihan->status != 'lunas')
                                    <span class="text-xs text-red-500 font-bold block mt-1">Sisa: Rp {{ number_format($tagihan->jumlah - $tagihan->terbayar, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-green-600 block">Terbayar: Rp {{ number_format($tagihan->terbayar, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($tagihan->status == 'lunas')
                                    <span class="px-2 py-1 rounded text-[10px] font-black uppercase bg-primary/20 text-[#078825] dark:text-primary">Lunas</span>
                                @elseif($tagihan->status == 'sebagian')
                                    <span class="px-2 py-1 rounded text-[10px] font-black uppercase bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Dicicil / Subsidi</span>
                                @else
                                    <span class="px-2 py-1 rounded text-[10px] font-black uppercase bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                    @if($tagihan->status != 'belum')
                                    <div class="flex items-center justify-end gap-2">
                                        @if($tagihan->jenisBiaya->status == 'inactive' && $tagihan->status != 'lunas')
                                            <!-- Waive Button for Inactive Fees -->
                                            <form action="{{ route('keuangan.tagihan.waive', $tagihan->id) }}" method="POST" onsubmit="return confirm('Putihkan sisa tagihan ini? Status akan menjadi Lunas dan tidak ada tagihan lagi.');">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors flex items-center gap-1 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800" title="Putihkan Sisa">
                                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                                    <span class="text-[10px] font-bold uppercase hidden md:inline">Putihkan</span>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('keuangan.tagihan.edit', $tagihan->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Tagihan">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <form action="{{ route('keuangan.tagihan.destroy', $tagihan->id) }}" method="POST" onsubmit="return confirm('Hapus tagihan ini? Data pembayaran terkait mungkin akan error.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Tagihan">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada tagihan untuk santri ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination (Mock for now, removed for simplicity if not paginated) -->
        </div>

        <!-- Simple Transaction History -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] overflow-hidden mt-8">
            <div class="px-6 py-4 border-b border-[#dbe6dd] dark:border-[#2a3a2d] bg-[#fcfdfc] dark:bg-[#1a2e1e]/30">
                <h2 class="text-[#111812] dark:text-white text-lg font-bold">Riwayat Pembayaran Terakhir</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-background-light dark:bg-[#1a2e1e]">
                            <th class="px-6 py-3 text-[#618968] text-xs font-bold uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-[#618968] text-xs font-bold uppercase tracking-wider">Pembayaran</th>
                            <th class="px-6 py-3 text-[#618968] text-xs font-bold uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3 text-[#618968] text-xs font-bold uppercase tracking-wider text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dbe6dd] dark:divide-[#2a3a2d]">
                        @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-primary/5 transition-colors text-[#111812] dark:text-white">
                            <td class="px-6 py-3 text-sm">
                                {{ $trx->created_at->format('d M Y') }}
                                <span class="text-xs text-gray-400 block">{{ $trx->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-3 text-sm font-medium">
                                {{ optional(optional($trx->tagihan)->jenisBiaya)->nama ?? 'Tagihan dihapus' }}
                                <span class="text-xs text-gray-500 block">{{ $trx->keterangan }}</span>
                            </td>
                            <td class="px-6 py-3 text-sm capitalize">
                                {{ $trx->metode_pembayaran }}
                            </td>
                            <td class="px-6 py-3 text-sm font-bold text-right text-[#078825]">
                                + Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-gray-500 text-sm italic">
                                Belum ada riwayat pembayaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Footer -->

    </div>
</x-app-layout>

