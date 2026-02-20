<x-app-layout>
    <x-slot name="header">
        Pratinjau Kuitansi
    </x-slot>

    <div class="flex flex-col h-[calc(100vh-64px)]">
        <!-- Breadcrumbs & Heading -->
        <section class="no-print px-4 py-4 md:px-10 lg:px-20 bg-white dark:bg-[#1a2e1d] border-b border-gray-200 dark:border-[#2f4532]">
            <div class="flex flex-wrap items-center gap-2 mb-2 text-sm text-gray-500 dark:text-gray-400">
                <a class="hover:text-primary" href="#">Keuangan</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <a class="hover:text-primary" href="#">Transaksi</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-gray-900 dark:text-white font-medium">Pratinjau Kuitansi</span>
            </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-[#111812] dark:text-white">Pratinjau Kuitansi PDF</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tinjau detail pembayaran sebelum mengunduh atau mencetak kuitansi digital.</p>
                </div>
                <button onclick="window.history.back()" class="no-print flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-sm font-bold rounded-lg hover:bg-gray-200 transition-colors text-[#111812] dark:text-white">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Kembali
                </button>
            </div>
        </section>

@php
    $isPreview = !isset($transaksiCollection) && !isset($transaksi); // Fallback for direct view check

    if ($isPreview) {
        $receiptNo = '#KWT-2023100501';
        $date = \Carbon\Carbon::create(2023, 10, 5, 9, 45);
        $payerName = 'Ahmad Fauzi Bin Sulaiman';
        $payerId = 'SAN-00293';
        $payerClass = 'VII-A (Tahfidz)';
        $totalAmount = 500000;
        $items = [
            [
                'desc' => 'SPP (Syahriah) - Bulan Juni 2023',
                'note' => 'Iuran rutin operasional madrasah',
                'amount' => 250000
            ],
            [
                'desc' => 'Uang Gedung (Cicilan 1)',
                'note' => 'Pembayaran awal tahun',
                'amount' => 250000
            ]
        ];
    } else {
        // Handle Collection
        $data = $transaksiCollection->first(); // Get first item for Header Info

        // Generate Receipt No based on the FIRST transaction ID in this batch
        // Format: #KWT-Ymd-ID
        $receiptNo = '#KWT-' . $data->created_at->format('Ymd') . '-' . str_pad($data->id, 4, '0', STR_PAD_LEFT);

        $date = $data->created_at;
        $payerName = $data->tagihan->santri->nama;
        $payerId = $data->tagihan->santri->nis ? $data->tagihan->santri->nis : '-';
        $payerClass = $data->tagihan->santri->kelas->nama ?? 'Belum Ada Kelas';

        $totalAmount = 0;
        $items = [];

        foreach($transaksiCollection as $trx) {
            $items[] = [
                'desc' => $trx->tagihan->jenisBiaya->nama,
                'note' => $trx->keterangan ?? $trx->tagihan->jenisBiaya->deskripsi,
                'amount' => $trx->jumlah_bayar
            ];
            $totalAmount += $trx->jumlah_bayar;
        }

        // WhatsApp Logic
        $noHp = $data->tagihan->santri->no_hp ?? '';

        // Normalize Phone Number (08 -> 62)
        if (substr($noHp, 0, 1) == '0') {
            $noHp = '62' . substr($noHp, 1);
        }

        $message = "Assalamualaikum, berikut adalah kuitansi pembayaran untuk *$payerName* ($payerId).\n\n";
        $message .= "No. Kuitansi: *$receiptNo*\n";
        $message .= "Tanggal: " . $date->translatedFormat('d F Y H:i') . "\n";
        $message .= "Total: *Rp " . number_format($totalAmount, 0, ',', '.') . "*\n\n";

        // Generate Public Link
        // Ensure this works for both Local and Production
        $link = route('keuangan.transaksi.receipt', $data->id);

        $message .= "Link Download PDF:\n$link\n\n";
        $message .= "Terima kasih.";

        $waUrl = "https://wa.me/$noHp?text=" . urlencode($message);
    }

    // Preview Dummy WA
    if ($isPreview) {
        $waUrl = "#";
    }
@endphp

        <!-- Content Area: Split View -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Left: Document Preview Section -->
            <section class="flex-1 bg-gray-100 dark:bg-[#132015] overflow-y-auto p-8 flex justify-center print:p-0 print:overflow-visible">
                <div class="print-area bg-white text-black shadow-2xl w-full max-w-[210mm] min-h-[297mm] p-[15mm] print:p-0 print:shadow-none print:max-w-none print:w-auto flex flex-col relative" id="receipt-document">
                    <!-- Receipt Header -->
                    <x-kop-laporan />

                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold uppercase tracking-wider text-gray-900 decoration-2 underline-offset-4 underline">KUITANSI</h2>
                        <p class="text-base font-bold text-gray-500 mt-1">{{ $receiptNo }}</p>
                    </div>
                     <!-- Payer Information -->
                    <div class="grid grid-cols-2 gap-8 mb-8">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Diterima Dari:</p>
                            <h3 class="text-xl font-bold">{{ $payerName }}</h3>
                            <p class="text-gray-600">ID Santri: {{ $payerId }}</p>
                            <p class="text-gray-600">Kelas: {{ $payerClass }}</p>
                        </div>
                         <div class="text-right">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Transaksi:</p>
                            <p class="text-lg font-medium">{{ $date->translatedFormat('d F Y') }}</p>
                            <p class="text-gray-600">Jam: {{ $date->format('H:i') }} WIB</p>
                        </div>
                    </div>
                     <!-- Payment Items Table -->
                    <div class="mb-8 flex-grow">
                        <table class="w-full text-left">
                            <thead class="border-b-2 border-gray-100">
                                <tr>
                                    <th class="py-3 font-bold text-sm uppercase">Deskripsi Pembayaran</th>
                                    <th class="py-3 font-bold text-sm uppercase text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($items as $item)
                                <tr>
                                    <td class="py-4">
                                        <p class="font-bold">{{ $item['desc'] }}</p>
                                        <p class="text-sm text-gray-500 italic">{{ $item['note'] }}</p>
                                    </td>
                                    <td class="py-4 text-right font-medium">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                             <tfoot>
                                <tr class="border-t-2 border-gray-200">
                                    <td class="py-4 text-right font-bold uppercase text-gray-500">Total Pembayaran</td>
                                    <td class="py-4 text-right text-2xl font-extrabold text-primary">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Right: Settings Sidebar -->
            <aside class="no-print w-80 bg-white dark:bg-[#1a2e1d] border-l border-gray-200 dark:border-[#2f4532] p-6 flex flex-col gap-8 overflow-y-auto">
                <div class="flex flex-col gap-3">
                    <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 py-3 bg-primary text-white rounded-xl font-bold hover:bg-blue-600 transition-all shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined">picture_as_pdf</span>
                        Simpan sebagai PDF
                    </button>
                     <button class="w-full flex items-center justify-center gap-2 py-3 bg-gray-900 dark:bg-white dark:text-[#1a2e1d] text-white rounded-xl font-bold hover:opacity-90 transition-all" onclick="window.print()">
                        <span class="material-symbols-outlined">print</span>
                        Cetak Langsung
                    </button>

                    <a href="{{ $waUrl }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-3 bg-[#25D366] text-white rounded-xl font-bold hover:bg-[#20bd5a] transition-all shadow-lg shadow-[#25D366]/20">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Kirim ke WA
                    </a>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>

