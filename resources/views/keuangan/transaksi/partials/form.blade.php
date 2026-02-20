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

            <!-- Reset Bills Button (Delete All) -->
            <button type="button" onclick="deleteAllBills()" class="size-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-colors shrink-0" title="Hapus Semua Tagihan (Reset)">
                <span class="material-symbols-outlined">delete_forever</span>
            </button>

            <!-- Generate Bills Button -->
             <button type="button" onclick="generateBills()" class="size-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-[#233827] text-primary hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors shrink-0" title="Cek & Generate Tagihan Otomatis">
                <span class="material-symbols-outlined">sync</span>
            </button>

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
                            <div class="flex items-center gap-2">
                                @if(!$isLunas)
                                    <button type="button" onclick="openEditBillModal({{ $tagihan->id }}, '{{ addslashes($tagihan->jenisBiaya->nama) }}', {{ $tagihan->jumlah }})" class="size-6 flex items-center justify-center rounded-full text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Edit Nominal">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                    <button type="button" onclick="deleteBill({{ $tagihan->id }})" class="size-6 flex items-center justify-center rounded-full text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Hapus Tagihan">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                @endif

                                @if($isLunas)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 uppercase tracking-wide">Lunas</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 uppercase tracking-wide">
                                        {{ $tagihan->terbayar > 0 ? 'Cicilan' : 'Belum' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($isBulanan)
                            <div class="flex flex-wrap items-center gap-1 mt-1">
                                <span class="text-[10px] font-bold text-white bg-primary/80 px-2 py-0.5 rounded-md uppercase tracking-wide shadow-sm">
                                    {{ $tagihan->created_at->locale('id')->isoFormat('MMMM Y') }}
                                </span>
                                <!-- Discount Badge -->
                                @php
                                    $subsidy = $tagihan->transaksis->where('metode_pembayaran', 'Subsidi')->first();
                                @endphp

                                @if($subsidy)
                                    @php
                                        // Parse Info from Transaction Keterangan: "Otomatis: (Disc. 100% - Yatim Piatu)"
                                        $info = $subsidy->keterangan;
                                        // Extract content inside parenthesis if exists
                                        if (Str::contains($info, 'Disc')) {
                                            $raw = Str::after($info, 'Disc.');
                                            $raw = trim(str_replace(['(', ')'], '', $raw)); // "100% - Yatim Piatu"
                                            $parts = explode('-', $raw, 2);
                                            $amount = trim($parts[0]);
                                            $reason = isset($parts[1]) ? trim($parts[1]) : 'Subsidi';
                                        } else {
                                            $amount = 'Subsidi';
                                            $reason = $info;
                                        }
                                    @endphp
                                    <div class="flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide cursor-help" title="{{ $reason }}">
                                        <span class="material-symbols-outlined text-[10px]">percent</span>
                                        <span>{{ $amount }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex flex-wrap items-center gap-1 mt-1">
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-[#2a3a2d] dark:text-slate-300 px-2 py-0.5 rounded-md uppercase tracking-wide">
                                    {{ $tagihan->created_at->locale('id')->isoFormat('D MMMM Y') }}
                                </span>

                                <!-- Discount Badge -->
                                @php
                                    $subsidy = $tagihan->transaksis->where('metode_pembayaran', 'Subsidi')->first();
                                @endphp

                                @if($subsidy)
                                    @php
                                        $info = $subsidy->keterangan;
                                        if (Str::contains($info, 'Disc')) {
                                            $raw = Str::after($info, 'Disc.');
                                            $raw = trim(str_replace(['(', ')'], '', $raw));
                                            $parts = explode('-', $raw, 2);
                                            $amount = trim($parts[0]);
                                            $reason = isset($parts[1]) ? trim($parts[1]) : 'Subsidi';
                                        } else {
                                            $amount = 'Subsidi';
                                            $reason = $info;
                                        }
                                    @endphp
                                    <div class="flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide cursor-help" title="{{ $reason }}">
                                        <span class="material-symbols-outlined text-[10px]">percent</span>
                                        <span>{{ $amount }}</span>
                                    </div>
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
        <div class="flex flex-col gap-6">

            <!-- Payment Methods (Boss Style) -->
            <div>
                <label class="text-sm font-bold text-[#111812] dark:text-white mb-2 block">Metode Pembayaran</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tunai Card -->
                    <label class="cursor-pointer relative group">
                        <input type="radio" name="metode" value="tunai" checked class="peer sr-only" onchange="updateMethodUI()">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 dark:border-[#2a3a2d] bg-white dark:bg-[#1a2e1d] hover:border-slate-300 dark:hover:border-[#3f5242] peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full flex flex-col justify-between">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="size-10 rounded-full bg-slate-100 dark:bg-[#233827] flex items-center justify-center text-slate-600 dark:text-slate-400 peer-checked:bg-primary peer-checked:text-white transition-colors">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>
                                <div>
                                    <p class="font-bold text-[#111812] dark:text-white text-base">Tunai</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Bayar langsung di loket</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-4 right-4 text-primary opacity-0 peer-checked:opacity-100 transition-opacity">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                    </label>

                    <!-- Tabungan Card -->
                    <label class="cursor-pointer relative group" id="method-tabungan-card" data-balance="{{ $santri->saldo_tabungan }}">
                        <input type="radio" name="metode" value="tabungan" class="peer sr-only" onchange="updateMethodUI()">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 dark:border-[#2a3a2d] bg-white dark:bg-[#1a2e1d] hover:border-slate-300 dark:hover:border-[#3f5242] peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full flex flex-col justify-between" id="tabungan-card-bg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="size-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 peer-checked:bg-primary peer-checked:text-white transition-colors">
                                    <span class="material-symbols-outlined">savings</span>
                                </div>
                                <div>
                                    <p class="font-bold text-[#111812] dark:text-white text-base">Tabungan Santri</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400" id="saldo-text-display">Saldo: Rp {{ number_format($santri->saldo_tabungan, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <!-- Insufficient Balance Warning (Hidden by default) -->
                            <div id="saldo-error-msg" class="hidden mt-2 p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">error</span>
                                Saldo Tidak Cukup!
                            </div>
                        </div>
                        <div class="absolute top-4 right-4 text-primary opacity-0 peer-checked:opacity-100 transition-opacity">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Total & Action -->
            <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8 bg-slate-50 dark:bg-[#1a2e1d]/50 p-6 rounded-2xl border border-dashed border-slate-200 dark:border-[#2a3a2d]">
                <div class="flex-1 w-full flex justify-between items-center md:block">
                     <div>
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Pembayaran</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-[#111812] dark:text-white" id="totalPaymentDisplay">Rp 0</span>
                        </div>
                     </div>
                </div>

                <button type="submit" id="btn-pay" class="w-full md:w-auto px-10 py-4 bg-primary hover:bg-primary/90 text-white rounded-xl font-bold shadow-xl shadow-primary/20 active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span>BAYAR SEKARANG</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>

        </div>
    </div>
</form>

<script>
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const tabunganRadio = document.querySelector('input[name="metode"][value="tabungan"]');
        if (tabunganRadio.checked) {
            const tabunganCard = document.getElementById('method-tabungan-card');
            const currentBalance = parseInt(tabunganCard.dataset.balance) || 0;
            const totalText = document.getElementById('totalPaymentDisplay').innerText;
            const totalPayment = parseInt(totalText.replace(/\D/g, '')) || 0;

            if (totalPayment > currentBalance) {
                e.preventDefault();
                Swal.fire('Gagal', 'Saldo Tabungan tidak mencukupi.', 'error');
                return false;
            }
        }
    });

    // ... existing functions ...

    function formatRupiahInput(input) {
        let value = input.value.replace(/\D/g, ''); // Remove non-digits
        if (value === "") {
             input.value = "";
             // calculateTotal(); // Trigger recalc if empty?
             return;
        }
        let max = parseInt(input.dataset.original);
        let numVal = parseInt(value);

        if(numVal > max) numVal = max; // Cap at max

        input.value = numVal.toLocaleString('id-ID');
        // calculateTotal(); // Call manually in oninput
    }

    function updateMethodUI() {
        const tabunganRadio = document.querySelector('input[name="metode"][value="tabungan"]');
        const isTabungan = tabunganRadio.checked;
        const tabunganCard = document.getElementById('method-tabungan-card');
        const tabunganBg = document.getElementById('tabungan-card-bg');
        const errorMsg = document.getElementById('saldo-error-msg');
        const btnPay = document.getElementById('btn-pay');
        const saldoText = document.getElementById('saldo-text-display');

        // Parse Balance
        const currentBalance = parseInt(tabunganCard.dataset.balance) || 0;

        // Parse Total Payment
        const totalText = document.getElementById('totalPaymentDisplay').innerText;
        const totalPayment = parseInt(totalText.replace(/\D/g, '')) || 0;

        // Reset Styles
        tabunganCard.classList.remove('opacity-50', 'cursor-not-allowed');
        tabunganBg.classList.remove('bg-red-50', 'dark:bg-red-900/10', 'border-red-200', 'dark:border-red-900');
        errorMsg.classList.add('hidden');
        saldoText.classList.remove('text-red-500');
        btnPay.disabled = false;
        btnPay.classList.remove('opacity-50', 'cursor-not-allowed');

        // Logic if Tabungan Selected
        if (isTabungan) {
            if (totalPayment > currentBalance) {
                // Insufficient Funds
                tabunganBg.classList.add('bg-red-50', 'dark:bg-red-900/10', 'border-red-200', 'dark:border-red-900');
                errorMsg.classList.remove('hidden');
                saldoText.classList.add('text-red-500');

                // Disable Pay Button
                btnPay.disabled = true;
                btnPay.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Also check if Total is 0, maybe disable button?
        if (totalPayment <= 0) {
             btnPay.disabled = true;
             btnPay.classList.add('opacity-50', 'cursor-not-allowed');
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

        // Re-validate Method
        updateMethodUI();
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

    // Initial Check
    calculateTotal();

    function deleteBill(id) {
        Swal.fire({
            title: 'Hapus Tagihan?',
            text: "Tagihan ini akan dihapus permanen dari sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show Loading
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
                });

                fetch(`{{ url('/keuangan/tagihan') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.redirected) {
                        return { success: true }; // Controller redirects back(), treat as success
                    }
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json().catch(() => ({ success: true })); // Handle non-JSON response
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Tagihan telah dihapus.',
                        timer: 1500,
                        showConfirmButton: false,
                        background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
                    }).then(() => {
                         // Reload the modal content to refresh list
                         // We are inside the modal content script scope.
                         // But openPaymentModal is global in index.blade.php.
                         // We can try to reload the modal for the SAME student.
                         // We need santri ID.
                         const santriId = {{ $santri->id }};
                         const santriName = "{{ addslashes($santri->nama) }}";

                         // Check if openPaymentModal exists (it should be in parent scope)
                         if (typeof openPaymentModal === 'function') {
                             openPaymentModal(santriId, santriName);
                         } else {
                             location.reload();
                         }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menghapus tagihan.',
                        background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
                    });
                });
            }
        });
    }
    function generateBills() {
        // Show Loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengecek dan membuat tagihan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
        });

        fetch(`{{ route('keuangan.santri.generate-bills', $santri->id) }}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                return { success: true }; // Controller redirects back(), treat as success
            }
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json().catch(() => ({ success: true })); // Handle non-JSON response
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Selesai!',
                text: 'Daftar tagihan telah diperbarui.',
                timer: 1500,
                showConfirmButton: false,
                background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
            }).then(() => {
                 // Reload the modal content to refresh list
                 const santriId = {{ $santri->id }};
                 const santriName = "{{ addslashes($santri->nama) }}";

                 // Check if openPaymentModal exists (it should be in parent scope)
                 if (typeof openPaymentModal === 'function') {
                     openPaymentModal(santriId, santriName);
                 } else {
                     location.reload();
                 }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat generate tagihan.',
                background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
            });
        });
    }
    function deleteAllBills() {
        Swal.fire({
            title: 'Hapus SEMUA Tagihan?',
            text: "Tindakan ini akan menghapus SELURUH tagihan (Status: Lunas/Belum) beserta riwayat transaksinya untuk siswa ini. Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset Tagihan!',
            cancelButtonText: 'Batal',
            background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show Loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menghapus semua tagihan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
                });

                const formData = new FormData();
                formData.append('_method', 'DELETE');
                formData.append('_token', '{{ csrf_token() }}');

                fetch(`{{ route('keuangan.santri.destroy-all-bills', $santri->id) }}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        return { success: true };
                    }
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json().catch(() => ({ success: true }));
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Semua tagihan telah dihapus.',
                        timer: 1500,
                        showConfirmButton: false,
                        background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
                    }).then(() => {
                         // Reload the modal content to refresh list
                         const santriId = {{ $santri->id }};
                         const santriName = "{{ addslashes($santri->nama) }}";

                         if (typeof openPaymentModal === 'function') {
                             openPaymentModal(santriId, santriName);
                         } else {
                             location.reload();
                         }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menghapus tagihan.',
                        background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
                    });
                });
            }
        });
    }
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
