<form action="{{ route('keuangan.pembayaran.store', $santri->id) }}" method="POST" id="paymentForm" class="flex flex-col h-full">
    @csrf
    @if(request()->ajax() || request('mode') == 'modal')
        <input type="hidden" name="redirect_to" value="index">
    @endif

    <!-- Header & Bulk Tools -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 sticky top-0 bg-white dark:bg-[#1a2e1d] z-10 py-2 border-b border-dashed border-slate-200 dark:border-slate-800">
        <div>
             <h3 class="font-black text-[#111812] dark:text-white text-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">receipt_long</span>
                Daftar Tagihan
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pilih tagihan yang ingin dibayar.</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Bulk Pay Input -->
            <div class="flex items-center bg-slate-100 dark:bg-[#233827] rounded-xl overflow-hidden h-10 ring-1 ring-slate-200 dark:ring-slate-700 w-full md:w-auto">
                <span class="px-3 text-[10px] font-bold text-slate-500 dark:text-[#a0c2a7] bg-slate-200/50 dark:bg-slate-800/50 h-full flex items-center">BULAN</span>
                <input type="number" id="bulkMonths" class="w-16 h-full text-center text-sm font-bold bg-transparent border-none focus:ring-0 p-0 text-[#111812] dark:text-white" placeholder="0">
                <button type="button" onclick="payBulkMonths()" class="px-4 h-full bg-primary text-white hover:bg-primary/90 text-xs font-bold uppercase transition-colors shrink-0">
                    PILIH
                </button>
            </div>

            <button type="button" onclick="toggleAllBills()" class="size-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-[#233827] text-primary hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors shrink-0" title="Pilih Semua">
                <span class="material-symbols-outlined">select_all</span>
            </button>
        </div>
    </div>

    <!-- Scrollable Bill List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 pb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($santri->tagihans as $tagihan)
            @php
                $sisa = $tagihan->jumlah - $tagihan->terbayar;
                $isLunas = $tagihan->status == 'lunas';
                $isBulanan = $tagihan->jenisBiaya->tipe == 'bulanan';
                $monthName = $isBulanan ? $tagihan->created_at->locale('id')->isoFormat('MMMM Y') : '';
            @endphp

            <div class="group relative flex flex-col justify-between p-5 rounded-2xl border transition-all duration-200 {{ $isLunas ? 'bg-slate-50 dark:bg-[#1a2e1d]/50 border-slate-100 dark:border-slate-800 opacity-60 grayscale-[0.5]' : 'bg-white dark:bg-[#1e3a24] border-slate-200 dark:border-[#2a3a2d] shadow-sm hover:shadow-md hover:border-primary/30' }}">

                <!-- Card Header: Checkbox & Title -->
                <div class="flex items-start gap-4 mb-4">
                    @if(!$isLunas)
                        <div class="relative flex-shrink-0 mt-0.5">
                            <input type="checkbox" name="tagihan_id[]" value="{{ $tagihan->id }}"
                                class="peer bill-checkbox appearance-none size-6 border-2 border-slate-300 dark:border-slate-600 rounded-lg checked:bg-primary checked:border-primary cursor-pointer transition-all"
                                onchange="calculateTotal()"
                                data-id="{{ $tagihan->id }}"
                                data-amount="{{ $sisa }}">
                            <span class="material-symbols-outlined absolute top-0 left-0 text-white text-base pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity p-0.5">check</span>
                        </div>
                    @else
                        <div class="size-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-sm">check</span>
                        </div>
                    @endif

                    <div class="flex flex-col w-full">
                        <div class="flex items-center justify-between w-full">
                            <span class="font-bold text-[#111812] dark:text-white text-base leading-tight">{{ $tagihan->jenisBiaya->nama }}</span>
                            @if($isLunas)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 uppercase tracking-wide">Lunas</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 uppercase tracking-wide">
                                    {{ $tagihan->terbayar > 0 ? 'Cicilan' : 'Belum' }}
                                </span>
                            @endif
                        </div>
                        @if($isBulanan)
                            <div class="flex flex-wrap items-center gap-1 mt-1">
                                <span class="text-[10px] font-bold text-white bg-primary/80 px-2 py-0.5 rounded-md uppercase tracking-wide shadow-sm">
                                    {{ $tagihan->created_at->locale('id')->isoFormat('MMMM Y') }}
                                </span>
                                @if(Str::contains($tagihan->keterangan, 'Disc'))
                                    @php
                                        // content is like: " (Disc. 50% - Yatim Piatu)"
                                        $raw = Str::after($tagihan->keterangan, 'Disc.');
                                        $raw = trim(str_replace(['(', ')'], '', $raw)); // "50% - Yatim Piatu"

                                        $parts = explode('-', $raw, 2);
                                        $amount = trim($parts[0]);

                                        if(isset($parts[1]) && !empty(trim($parts[1]))) {
                                            $reason = trim($parts[1]);
                                        } elseif($tagihan->siswa && $tagihan->siswa->kategoriKeringanan) {
                                            $reason = $tagihan->siswa->kategoriKeringanan->nama;
                                        } else {
                                            $reason = 'Diskon Khusus';
                                        }
                                    @endphp
                                    <button type="button" data-action="show-info" data-title="Info Diskon" data-message="Kategori: {{ $reason }}" data-icon="info" class="text-[10px] font-bold text-white bg-indigo-500/80 hover:bg-indigo-600 px-2 py-0.5 rounded-md uppercase tracking-wide shadow-sm flex items-center gap-1 transition-colors cursor-pointer" title="{{ $reason }}">
                                        <span class="material-symbols-outlined text-[10px]">percent</span>
                                        {{ $amount }}
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="flex flex-wrap items-center gap-1 mt-1">
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-[#2a3a2d] dark:text-slate-300 px-2 py-0.5 rounded-md uppercase tracking-wide">
                                    {{ $tagihan->created_at->locale('id')->isoFormat('D MMMM Y') }}
                                </span>
                                @if(Str::contains($tagihan->keterangan, 'Disc'))
                                    @php
                                        // content is like: " (Disc. 50% - Yatim Piatu)"
                                        $raw = Str::after($tagihan->keterangan, 'Disc.');
                                        $raw = trim(str_replace(['(', ')'], '', $raw)); // "50% - Yatim Piatu"

                                        $parts = explode('-', $raw, 2);
                                        $amount = trim($parts[0]);

                                        if(isset($parts[1]) && !empty(trim($parts[1]))) {
                                            $reason = trim($parts[1]);
                                        } elseif($tagihan->siswa && $tagihan->siswa->kategoriKeringanan) {
                                            $reason = $tagihan->siswa->kategoriKeringanan->nama;
                                        } else {
                                            $reason = 'Diskon Khusus';
                                        }
                                    @endphp
                                    <button type="button" data-action="show-info" data-title="Info Diskon" data-message="Kategori: {{ $reason }}" data-icon="info" class="text-[10px] font-bold text-white bg-indigo-500/80 hover:bg-indigo-600 px-2 py-0.5 rounded-md uppercase tracking-wide shadow-sm flex items-center gap-1 transition-colors cursor-pointer" title="{{ $reason }}">
                                        <span class="material-symbols-outlined text-[10px]">percent</span>
                                        {{ $amount }}
                                    </button>
                                @endif
                            </div>
                        @endif
                        <div class="flex items-center justify-between mt-2 md:hidden">
                            <span class="text-xs text-slate-500">Sisa Tagihan</span>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Input Section -->
                <div class="pt-4 border-t border-dashed border-slate-100 dark:border-slate-800">
                    @if(!$isLunas)
                        <div class="flex items-center gap-2">
                             <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" name="bills[{{ $tagihan->id }}]"
                                    class="bill-input w-full pl-8 pr-3 py-2.5 bg-slate-50 dark:bg-[#1a2e1d] border border-slate-200 dark:border-[#2a3a2d] rounded-xl text-sm font-bold text-[#111812] dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-right"
                                    value="{{ $isLunas ? 0 : number_format($sisa,0,',','.') }}"
                                    data-original="{{ $sisa }}"
                                    oninput="formatRupiahInput(this); calculateTotal()"
                                    data-id="{{ $tagihan->id }}"
                                    placeholder="0">
                            </div>
                            <button type="button" onclick="payFull({{ $tagihan->id }}, {{ $sisa }})" class="size-10 flex items-center justify-center bg-primary/10 text-primary rounded-xl hover:bg-primary hover:text-white transition-colors flex-shrink-0" title="Bayar Penuh">
                                <span class="material-symbols-outlined text-xl">all_inclusive</span>
                            </button>
                        </div>
                    @else
                        <div class="flex justify-between items-center h-10">
                            <span class="text-xs font-bold text-slate-400">Total Terbayar</span>
                            <span class="text-sm font-black text-slate-400 strike-through decoration-slate-400">
                                 Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            @empty
                <div class="col-span-1 md:col-span-2 flex flex-col items-center justify-center py-12 text-center text-slate-400">
                    <span class="material-symbols-outlined text-6xl mb-2 opacity-20">check_circle</span>
                    <p>Tidak ada tagihan yang belum lunas.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Static Footer (No Sticky) -->
    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-[#2a3a2d]">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-4 md:gap-8">

            <!-- Methods -->
             <div class="flex items-center gap-3 w-full md:w-auto">
                <label class="cursor-pointer flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 has-[:checked]:bg-primary/10 has-[:checked]:border-primary has-[:checked]:text-primary transition-all flex-1 md:flex-none justify-center">
                    <input type="radio" name="metode" value="tunai" checked class="hidden peer" onchange="updateMethodUI()">
                    <span class="material-symbols-outlined text-lg">payments</span>
                    <span class="text-sm font-bold">Tunai</span>
                </label>
                <label class="cursor-pointer flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 has-[:checked]:bg-primary/10 has-[:checked]:border-primary has-[:checked]:text-primary transition-all flex-1 md:flex-none justify-center">
                     <input type="radio" name="metode" value="tabungan" class="hidden peer" onchange="updateMethodUI()">
                    <span class="material-symbols-outlined text-lg">savings</span>
                    <span class="text-sm font-bold">Saldo</span>
                </label>
            </div>

            <div class="flex-1 flex flex-col items-end md:items-start w-full">
                <span class="text-xs text-slate-500 font-bold uppercase">Total Bayar</span>
                <span class="text-2xl font-black text-primary" id="totalPaymentDisplay">Rp 0</span>
            </div>

            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-bold shadow-lg shadow-primary/30 active:scale-95 transition-all flex items-center justify-center gap-2">
                <span>BAYAR SEKARANG</span>
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        </div>
        <!-- Saldo Warning -->
        <div id="saldo-warning" class="mt-2 text-xs font-bold text-orange-500 hidden text-center md:text-left">
            <span class="material-symbols-outlined text-xs align-middle">warning</span>
            Sisa Saldo: Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}. Pastikan mencukupi!
        </div>
    </div>
</form>

<script>
    function formatRupiahInput(input) {
        let value = input.value.replace(/\D/g, ''); // Remove non-digits
        if (value === "") {
             input.value = "";
             return;
        }
        let max = parseInt(input.dataset.original);
        let numVal = parseInt(value);

        if(numVal > max) numVal = max; // Cap at max

        input.value = numVal.toLocaleString('id-ID');
    }

    function updateMethodUI() {
        const isTabungan = document.querySelector('input[name="metode"][value="tabungan"]').checked;
        const warning = document.getElementById('saldo-warning');
        if(isTabungan) {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    }

    function toggleAllBills() {
        const formScope = document.getElementById('paymentForm');
        const checkboxes = formScope.querySelectorAll('.bill-checkbox:not(:disabled)');
        const allChecked = Array.from(checkboxes).every(c => c.checked);

        checkboxes.forEach(c => {
            c.checked = !allChecked;
             // Auto-fill input if checking
            let id = c.dataset.id;
            let input = formScope.querySelector(`.bill-input[data-id="${id}"]`);
            if (!allChecked) {
                 // Paying full
                 let max = parseInt(input.dataset.original);
                 input.value = max.toLocaleString('id-ID'); // Format
            } else {
                 input.value = "0";
            }
        });

        calculateTotal();
    }

    function calculateTotal() {
        let totalPayment = 0;
        let itemCount = 0;

        const formScope = document.getElementById('paymentForm');
        formScope.querySelectorAll('.bill-checkbox').forEach(checkbox => {
            if (checkbox.checked) {
                itemCount++;
                let id = checkbox.dataset.id;
                let input = formScope.querySelector(`.bill-input[data-id="${id}"]`);

                let rawVal = input.value.replace(/\./g, ''); // Remove dots
                totalPayment += parseInt(rawVal) || 0;
            }
        });

        formScope.querySelector('#totalPaymentDisplay').innerText = 'Rp ' + totalPayment.toLocaleString('id-ID');
    }

    function payFull(id, amount) {
        const formScope = document.getElementById('paymentForm');
        const checkbox = formScope.querySelector(`.bill-checkbox[data-id="${id}"]`);
        const input = formScope.querySelector(`.bill-input[data-id="${id}"]`);

        if (input) {
            input.value = amount.toLocaleString('id-ID');
        }

        if (checkbox && !checkbox.checked) {
            checkbox.checked = true;
        }
        calculateTotal();
    }

    function payBulkMonths() {
        const months = parseInt(document.getElementById('bulkMonths').value) || 0;
        if (months <= 0) return;

        const formScope = document.getElementById('paymentForm');

        // Find ALL checkbox wrappers (cards)
        // We need to iterate over visible cards
        const cards = formScope.querySelectorAll('.group'); // The bill cards

        let count = 0;

        for (let card of cards) {
            if(count >= months) break;

            const checkbox = card.querySelector('.bill-checkbox');
            if(!checkbox) continue; // paid ones don't have checkbox
            if(checkbox.checked) continue; // already checked

            // Check name
            const nameEl = card.querySelector('.font-bold.text-base');
            const name = nameEl ? nameEl.innerText.toLowerCase() : '';

            if (name.includes('spp')) {
                let id = checkbox.dataset.id;
                let amount = parseInt(checkbox.dataset.amount);
                payFull(id, amount);
                count++;
            }
        }

        if (count == 0) {
            // alert('Tidak ditemukan tagihan SPP yg belum dipilih.');
            Swal.fire('Info', 'Tidak ditemukan tagihan SPP tambahan.', 'info');
        } else if (count < months) {
             Swal.fire('Info', `Hanya ditemukan ${count} bulan tagihan SPP.`, 'info');
        }
    }

    // Initialize formatting on load in case of re-render
    document.querySelectorAll('.bill-input').forEach(input => {
         let val = input.value.replace(/\./g, '');
         if(val && val != '0') {
             input.value = parseInt(val).toLocaleString('id-ID');
         }
    });

    updateMethodUI();
    calculateTotal();
</script>

<style>
    /* Custom Scrollbar for the list to allow footer to sit nice */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #334155;
    }

    /* Hide number arrows */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
