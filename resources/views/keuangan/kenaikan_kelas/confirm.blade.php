<x-app-layout>
    <x-slot name="header">
        Konfirmasi Kenaikan Kelas Otomatis
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] overflow-hidden">
            <div class="p-6 border-b border-[#f0f4f1] dark:border-[#2a452e]">
                <h2 class="text-xl font-bold text-[#111812] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-yellow-500">warning</span>
                    Konfirmasi Eksekusi
                </h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Sistem akan memproses perpindahan siswa secara massal sesuai rencana berikut. 
                    Mohon periksa kembali apakah urutan dan jumlah siswa sudah sesuai.
                </p>
            </div>

            <div class="p-6">
                <div class="overflow-hidden border rounded-lg border-gray-200 mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-[#233827]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sumber (Kelas Lama)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tujuan (Kelas Baru)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Siswa</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#1a2e1d] divide-y divide-gray-200 dark:divide-[#2a452e]">
                            @forelse($plan as $index => $step)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $step['source']->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($step['type'] == 'promote')
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">Naik Kelas</span>
                                    @elseif($step['type'] == 'graduate')
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">Lulus</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800">Manual</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $step['target']->nama ?? '-' }}
                                    @if($step['type'] == 'graduate') (Alumni) @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $step['student_count'] }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data untuk diproses.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between mt-8">
                    <a href="{{ route('kenaikan-kelas.index', ['level_id' => $levelId]) }}" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                        Kembali / Batal
                    </a>
                    
                    <form action="{{ route('keuangan.kenaikan-kelas.magic.execute') }}" method="POST">
                        @csrf
                        <input type="hidden" name="level_id" value="{{ $levelId }}">
                        <button type="submit" class="bg-[#13ec37] text-white font-bold px-8 py-3 rounded-lg shadow-lg hover:brightness-110 flex items-center gap-2">
                             <span class="material-symbols-outlined">rocket_launch</span>
                             Ya, Eksekusi Sekarang!
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

