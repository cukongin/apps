<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaksiCollection->first()->kode_transaksi }}</title>
    <style>
        @page {
            margin: 0;
            size: 58mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            width: 53mm;
            margin: 0 auto;
            padding: 5px 0;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .header {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .items {
            width: 100%;
            margin: 5px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
        }
        .total-row {
            margin-top: 5px;
            border-top: 1px dashed #000;
            padding-top: 5px;
            font-weight: bold;
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body onload="window.print()">

    <!-- Header -->
    <div class="header text-center">
        @if(\App\Models\Setting::get('logo'))
             <img src="{{ asset('storage/' . \App\Models\Setting::get('logo')) }}" style="height: 40px; filter: grayscale(100%); margin-bottom: 4px;">
        @endif
        <div class="font-bold uppercase">{{ \App\Models\Setting::get('nama_sistem', 'Madrasah') }}</div>
        <div style="font-size: 8pt;">{{ \App\Models\Setting::get('alamat', '-') }}</div>
    </div>

    <!-- Meta -->
    @php
        $first = $transaksiCollection->first();
        $total = $transaksiCollection->sum('jumlah_bayar');
    @endphp
    <div style="font-size: 9pt;">
        <div>Resi: #{{ $first->kode_transaksi }}</div> <!-- Using first code or timestamp -->
        <div>Tgl : {{ $first->created_at->format('d/m/y H:i') }}</div>
        <div>Siswa: {{ $first->tagihan->santri->nama ?? 'Umum' }}</div>
        <div>Kelas: {{ $first->tagihan->santri->kelas->nama ?? '-' }}</div>
    </div>

    <div class="divider"></div>

    <!-- Items -->
    <div class="items">
        @foreach($transaksiCollection as $item)
        <div class="item-row">
            <span style="flex: 2;">{{ $item->tagihan->jenisBiaya->nama ?? 'Biaya' }}</span>
        </div>
        <div class="item-row text-right">
            <span style="flex: 1;">1 x {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</span>
        </div>
        @endforeach
    </div>

    <div class="divider"></div>

    <!-- Totals -->
    <div class="total-row item-row">
        <span>TOTAL</span>
        <span>{{ number_format($total, 0, ',', '.') }}</span>
    </div>
    
    <div class="item-row" style="font-size: 9pt; margin-top: 2px;">
        <span>Tunai/Metode</span>
        <span>{{ ucfirst($first->metode_pembayaran ?? 'Tunai') }}</span>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Terima Kasih</p>
        <p>Simpan struk ini sebagai<br>bukti pembayaran yang sah.</p>
        <p style="text-align: center; margin-top: 5px;">--- *** ---</p>
    </div>

</body>
</html>

