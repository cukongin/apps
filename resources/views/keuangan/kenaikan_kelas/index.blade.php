<x-app-layout>
    <x-slot name="header">
        Manajemen Kenaikan Kelas
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ mode: '{{ request('level_id') ? 'magic' : 'manual' }}' }">
        
        <!-- Strategy Tip -->
        <div class="bg-[#f0f4f1] border-l-4 border-[#618968] p-4 mb-8 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-[#618968]">info</span>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-[#111812]">Strategi Kenaikan Kelas (PENTING!)</h3>
                    <div class="mt-2 text-sm text-[#4a5568]">
                        <p>Agar data siswa tidak bercampur, mohon lakukan proses kenaikan dari <strong>KELAS PALING TINGGI</strong> turun ke bawah.</p>
                        <ul class="list-disc list-inside mt-1">
                            <li><strong>Mode Otomatis:</strong> Sistem akan otomatis mengurutkan dari kelas tertinggi untuk mencegah data tertimpa.</li>
                            <li><strong>Mode Manual:</strong> Mohon eksekusi manual mulai dari kelas akhir (Luluskan dulu), baru naikkan adik kelasnya.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mode Switcher -->
        <div class="flex justify-center mb-8">
            <div class="bg-white dark:bg-[#1a2e1d] p-1 rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] inline-flex">
                <button @click="mode = 'magic'" :class="mode === 'magic' ? 'bg-[#618968] text-white shadow' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">auto_fix</span> Mode Otomatis
                </button>
                <button @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-[#618968] text-white shadow' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">edit_square</span> Mode Manual
                </button>
                <button @click="mode = 'history'" :class="mode === 'history' ? 'bg-[#618968] text-white shadow' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">history</span> Riwayat & Undo
                </button>
            </div>
        </div>

        <!-- MAGIC MODE -->
        <div x-show="mode === 'magic'" class="transition-all duration-300">
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">1. Pilih Jenjang / Tingkatan</h2>
                <form action="{{ route('keuangan.kenaikan-kelas.index') }}" method="GET" class="flex gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenjang</label>
                        <select name="level_id" class="w-full rounded-lg border-gray-300 focus:ring-[#618968] focus:border-[#618968]" required>
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl->id }}" {{ request('level_id') == $lvl->id ? 'selected' : '' }}>
                                    {{ $lvl->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-[#618968] text-white px-6 py-2.5 rounded-lg font-bold hover:bg-[#4d6f54] transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined">analytics</span> Analisa Kenaikan
                    </button>
                </form>
            </div>

            @if(!empty($promotionPlan))
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">2. Preview Rencana Kenaikan Otomatis</h2>
                
                <div class="overflow-hidden border rounded-lg border-gray-200 mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-[#233827]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas Asal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Target</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Siswa</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#1a2e1d] divide-y divide-gray-200 dark:divide-[#2a452e]">
                            @foreach($promotionPlan as $plan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $plan['source']->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($plan['type'] == 'promote')
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                            Naik Kelas
                                        </span>
                                    @elseif($plan['type'] == 'graduate')
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                            Lulus / Alumni
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800">
                                            Manual Check
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    @if($plan['target'])
                                        {{ $plan['target']->nama }}
                                    @elseif($plan['type'] == 'graduate')
                                        -
                                    @else
                                        <span class="text-red-500 italic">Tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $plan['student_count'] }} Siswa
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-sm text-yellow-700">
                    <p><strong>Perhatian:</strong> Sistem akan memproses urutan dari atas ke bawah (Kelas Tertinggi dulu) untuk mencegah data tertimpa.</p>
                </div>

                <form action="{{ route('keuangan.kenaikan-kelas.magic') }}" method="POST" class="flex justify-end">
                    @csrf
                    <input type="hidden" name="level_id" value="{{ request('level_id') }}">
                    <button type="button" onclick="confirmMagic(this)" class="bg-gradient-to-r from-[#618968] to-[#4d6f54] hover:from-[#4d6f54] hover:to-[#3e5944] text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition hover:scale-105 flex items-center gap-2">
                        <span class="material-symbols-outlined">auto_fix</span> Eksekusi Ajaib
                    </button>
                </form>
            </div>
            @elseif(request('level_id'))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-r-lg">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-yellow-500 text-3xl mr-4">sentiment_dissatisfied</span>
                    <div>
                        <h3 class="text-lg font-bold text-yellow-800">Tidak Ada Data Ditemukan</h3>
                        <p class="text-yellow-700 mt-1">Sistem tidak menemukan kelas atau siswa aktif di jenjang ini yang bisa diproses otomatis. Pastikan ada kelas dengan format nama yang benar (misal: "1 Ula A", "2 Aliya").</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- HISTORY MODE -->
        <div x-show="mode === 'history'" class="transition-all duration-300">
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">Riwayat Kenaikan Kelas Terakhir</h2>
                
                @if($historyBatches->count() > 0)
                <div class="overflow-hidden border rounded-lg border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-[#233827]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Oleh</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jml Siswa</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#1a2e1d] divide-y divide-gray-200 dark:divide-[#2a452e]">
                            @foreach($historyBatches as $batch)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $batch->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $batch->batch_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $batch->user->name ?? 'System' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $batch->details_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <form action="{{ route('kenaikan-kelas.undo', $batch->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Aksi ini akan membatalkan SEMUA perubahan pada siswa di batch ini. Status dan Kelas siswa akan dikembalikan ke posisi sebelumnya. Lanjutkan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-bold bg-red-50 hover:bg-red-100 px-3 py-1 rounded text-xs transition-colors">
                                            Batalkan (Undo)
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-gray-500">
                    Belum ada riwayat kenaikan kelas.
                </div>
                @endif
            </div>
        </div>

        <!-- MANUAL MODE -->
        <div x-show="mode === 'manual'" class="transition-all duration-300">
            <!-- Step 1: Select Source Class -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 mb-8">
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">1. Pilih Kelas Asal (Manual)</h2>
                <form action="{{ route('keuangan.kenaikan-kelas.index') }}" method="GET" class="flex gap-4 items-end">
                    <!-- Hidden input to switch tab back to manual on submit -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas Asal</label>
                        <select name="source_class_id" class="w-full rounded-lg border-gray-300 focus:ring-[#618968] focus:border-[#618968]" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($allClasses as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('source_class_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama }} ({{ $kelas->level->nama ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-[#1f2937] text-white px-6 py-2.5 rounded-lg font-bold hover:bg-[#374151] transition-colors">
                        Tampilkan Siswa
                    </button>
                </form>
            </div>
        </div>

        @if(isset($santris) && $santris->count() > 0)
        <!-- Step 2: Select Students & Target -->
        <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6" x-data="{ action: 'promote', selectAll: true }">
            <form action="{{ route('keuangan.kenaikan-kelas.process') }}" method="POST">
                @csrf
                <input type="hidden" name="source_class_id" value="{{ request('source_class_id') }}">
                
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">2. Pilih Kenaikan / Kelulusan</h2>
                
                <!-- Action Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 dark:bg-[#233827] rounded-lg">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Aksi</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio text-[#618968]" name="action_type" value="promote" x-model="action">
                                <span class="ml-2">Naik Kelas / Pindah Kelas</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio text-red-600" name="action_type" value="graduate" x-model="action">
                                <span class="ml-2 text-red-600 font-bold">Luluskan (Alumni)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div x-show="action === 'promote'">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kelas Tujuan</label>
                        <select name="target_class_id" class="w-full rounded-lg border-gray-300 focus:ring-[#618968] focus:border-[#618968]">
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach($allClasses as $kelas)
                                <!-- Exclude current class -->
                                @if(request('source_class_id') != $kelas->id)
                                <option value="{{ $kelas->id }}">
                                    {{ $kelas->nama }} ({{ $kelas->level->nama ?? '-' }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih kelas tujuan (misal: naik ke tingkat selanjutnya).</p>
                    </div>

                    <div x-show="action === 'graduate'" class="hidden" :class="{ 'hidden': action !== 'graduate' }">
                         <div class="p-3 bg-red-50 text-red-700 rounded text-sm border border-red-200">
                             <span class="font-bold">Info:</span> Siswa yang dipilih akan diubah statusnya menjadi <strong>Lulus (Alumni)</strong> dan tidak memiliki kelas lagi.
                         </div>
                    </div>
                </div>

                <!-- Student List -->
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-bold">Daftar Siswa ({{ $santris->count() }})</h3>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-[#618968] shadow-sm focus:border-[#618968] focus:ring focus:ring-[#618968] focus:ring-opacity-50" x-model="selectAll" @change="$el.closest('form').querySelectorAll('.santri-checkbox').forEach(el => el.checked = selectAll)">
                        <span class="ml-2 text-sm text-gray-600">Pilih Semua</span>
                    </label>
                </div>

                <div class="overflow-x-auto border rounded-lg border-gray-200 mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">Pilih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($santris as $santri)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="santri_ids[]" value="{{ $santri->id }}" class="santri-checkbox rounded border-gray-300 text-[#618968] shadow-sm focus:border-[#618968] focus:ring focus:ring-[#618968] focus:ring-opacity-50" checked>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $santri->nis }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $santri->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-[#618968] hover:bg-[#4d6f54] text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition hover:scale-105" onclick="return confirm('Apakah Anda yakin ingin memproses data ini?')">
                        Proses Sekarang
                    </button>
                </div>
            </form>
        </div>
        @elseif(request('source_class_id'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-yellow-400">warning</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Tidak ada siswa aktif ditemukan di kelas ini.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmMagic(btn) {
            Swal.fire({
                title: 'Siap Melakukan Keajaiban? ✨',
                text: "Sistem akan menyusun rencana kenaikan kelas secara otomatis. Tenang, Anda bisa me-review hasilnya sebelum disimpan!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#618968',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Tunjukkan Keajaiban!',
                cancelButtonText: 'Nanti Dulu',
                background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#1f2937'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        }
    </script>
</x-app-layout>

