<x-app-layout>
    <x-slot name="header">
        Laporan Rekapitulasi Subsidi
    </x-slot>

    <div class="max-w-[1440px] w-full mx-auto py-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div>
                <h2 class="text-3xl font-black text-[#111812] dark:text-white mb-2">Laporan Subsidi & Beasiswa</h2>
                <p class="text-[#618968] dark:text-gray-400 text-base max-w-2xl">
                    Rekapitulasi total bantuan biaya pendidikan yang diberikan kepada siswa.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-orange-50 dark:bg-orange-900/20 px-4 py-2 rounded-lg border border-orange-100 dark:border-orange-800">
                    <span class="text-xs text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider">Total Subsidi Diberikan</span>
                    <p class="text-xl font-black text-orange-600 dark:text-orange-400">Rp {{ number_format($totalSubsidiAll, 0, ',', '.') }}</p>
                </div>
                <button onclick="window.print()" class="flex items-center gap-2 bg-white dark:bg-[#1a2e1d] border border-gray-200 dark:border-[#2f4532] text-[#111812] dark:text-white rounded-lg px-5 py-2.5 shadow-sm hover:bg-gray-50 dark:hover:bg-[#1f3622] transition-all">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                    <span class="text-sm font-bold">Cetak</span>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-gray-200 dark:border-[#2f4532] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-[#132015] border-b border-gray-200 dark:border-[#2f4532]">
                        <tr>
                            <th class="px-6 py-4 font-bold text-[#111812] dark:text-white w-10">No</th>
                            <th class="px-6 py-4 font-bold text-[#111812] dark:text-white">Siswa / NIS</th>
                            <th class="px-6 py-4 font-semibold text-[#618968]">Kelas</th>
                            <th class="px-6 py-4 font-semibold text-[#618968]">Status</th>
                            <th class="px-6 py-4 font-bold text-orange-500">Total Subsidi</th>
                            <th class="px-6 py-4 font-semibold text-[#618968]">Rincian Transaksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#2f4532]">
                        @forelse($students as $data)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1f3622] transition-colors valign-top">
                            <td class="px-6 py-4 font-medium text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-[#111812] dark:text-white">{{ $data['siswa']->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $data['siswa']->nis }}</div>
                            </td>
                            <td class="px-6 py-4 text-[#111812] dark:text-white">
                                {{ $data['siswa']->kelas->level->nama ?? '-' }} {{ $data['siswa']->kelas->nama_kelas ?? '' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $status = $data['siswa']->status;
                                    $color = match($status) {
                                        'Aktif' => 'bg-green-100 text-green-700',
                                        'Lulus' => 'bg-blue-100 text-blue-700',
                                        'Keluar' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $color }}">{{ $status }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-orange-500 text-lg">
                                Rp {{ number_format($data['total_subsidi'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <ul class="space-y-1">
                                    @foreach($data['history']->take(5) as $history)
                                    <li class="text-xs text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[10px] text-gray-400">circle</span>
                                        <span>{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y') }} - </span>
                                        <span class="font-semibold">Rp {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</span>
                                        <span class="italic text-gray-400 truncate max-w-[200px]" title="{{ $history->keterangan }}">({{ $history->keterangan }})</span>
                                    </li>
                                    @endforeach
                                    @if($data['history']->count() > 5)
                                    <li class="text-xs text-primary font-bold pt-1 cursor-pointer">
                                        + {{ $data['history']->count() - 5 }} transaksi lainnya...
                                    </li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data subsidi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
