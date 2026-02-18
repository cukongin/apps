<x-app-layout>
    <x-slot name="header">
        Edit Jenis Biaya
    </x-slot>

    <div class="max-w-3xl mx-auto py-12">
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('keuangan.biaya-lain.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-[#111812] dark:text-white">Edit Jenis Biaya</h1>
        </div>

        <div class="bg-white dark:bg-[#152a15] rounded-xl border border-[#dbe6db] dark:border-[#2a3a2a] shadow-sm p-6 md:p-8">
            <form action="{{ route('keuangan.biaya-lain.update', $biaya->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#618961]">Nama Biaya</label>
                        <input type="text" name="nama" value="{{ old('nama', $biaya->nama) }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe6db] dark:border-[#2a3a2a] bg-slate-50 dark:bg-[#1e331e] text-[#111812] dark:text-white focus:ring-2 focus:ring-primary focus:border-primary font-medium" required placeholder="Contoh: Uang Gedung">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#618961]">Kategori</label>
                        <select name="kategori" class="w-full px-4 py-3 rounded-lg border border-[#dbe6db] dark:border-[#2a3a2a] bg-slate-50 dark:bg-[#1e331e] text-[#111812] dark:text-white focus:ring-2 focus:ring-primary focus:border-primary font-medium">
                            <option value="Operasional" {{ $biaya->kategori == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                            <option value="Pembangunan" {{ $biaya->kategori == 'Pembangunan' ? 'selected' : '' }}>Pembangunan</option>
                            <option value="Kebutuhan Awal" {{ $biaya->kategori == 'Kebutuhan Awal' ? 'selected' : '' }}>Kebutuhan Awal</option>
                            <option value="Akhir Tahun" {{ $biaya->kategori == 'Akhir Tahun' ? 'selected' : '' }}>Akhir Tahun</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#618961]">Tipe Penagihan</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 border {{ $biaya->tipe == 'sekali' ? 'border-primary bg-primary/5' : 'border-[#dbe6db] dark:border-[#2a3a2a]' }} rounded-xl cursor-pointer transition-all hover:border-primary">
                            <input type="radio" name="tipe" value="sekali" class="w-5 h-5 text-primary focus:ring-primary" {{ $biaya->tipe == 'sekali' ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-bold text-[#111812] dark:text-white">Sekali Bayar</span>
                                <span class="block text-xs text-gray-500">Contoh: Seragam, Gedung</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 border {{ $biaya->tipe == 'bulanan' ? 'border-primary bg-primary/5' : 'border-[#dbe6db] dark:border-[#2a3a2a]' }} rounded-xl cursor-pointer transition-all hover:border-primary">
                            <input type="radio" name="tipe" value="bulanan" class="w-5 h-5 text-primary focus:ring-primary" {{ $biaya->tipe == 'bulanan' ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-bold text-[#111812] dark:text-white">Bulanan (SPP)</span>
                                <span class="block text-xs text-gray-500">Tagihan per bulan</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#618961]">Status</label>
                    <div class="flex gap-4">
                         <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="active" class="text-primary focus:ring-primary" {{ $biaya->status == 'active' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-[#111812] dark:text-white">Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="inactive" class="text-gray-500 focus:ring-gray-500" {{ $biaya->status == 'inactive' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-[#111812] dark:text-white">Non-Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#618961]">Target Siswa</label>
                        <select name="target_type" id="targetType" class="w-full px-4 py-3 rounded-lg border border-[#dbe6db] bg-slate-50 text-[#111812] focus:ring-2 focus:ring-primary focus:border-primary font-medium">
                            <option value="all" {{ $biaya->target_type == 'all' ? 'selected' : '' }}>Semua Siswa</option>
                            <option value="level" {{ $biaya->target_type == 'level' ? 'selected' : '' }}>Berdasarkan Tingkat (Level)</option>
                            <option value="class" {{ $biaya->target_type == 'class' ? 'selected' : '' }}>Berdasarkan Kelas</option>
                        </select>
                    </div>
                    <div class="space-y-2" id="targetValueContainer">
                        <label class="text-sm font-bold text-[#618961]">Detail Target</label>

                        <!-- Input for Custom/Text -->
                        <input type="text" id="targetValueText" value="{{ $biaya->target_type == 'custom' ? $biaya->target_value : '' }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe6db] dark:border-[#2a3a2a] bg-slate-50 dark:bg-[#1e331e] text-[#111812] dark:text-white focus:ring-2 focus:ring-primary focus:border-primary font-medium" placeholder="Isi detail target" style="display: none;">

                        <!-- Select for Level -->
                        <select id="targetValueLevel" multiple class="w-full px-4 py-3 rounded-lg border border-[#dbe6db] bg-white text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary font-medium h-32" style="display: none; color: black !important; background-color: white !important;">
                            @php
                                $selectedLevels = json_decode($biaya->target_value, true) ?? [];
                                if (!is_array($selectedLevels)) $selectedLevels = [$biaya->target_value];
                            @endphp
                            @foreach($levels as $level)
                                <option value="{{ $level->nama }}" style="color: black !important; background-color: white !important;" {{ in_array($level->nama, $selectedLevels) ? 'selected' : '' }}>{{ $level->nama }}</option>
                            @endforeach
                        </select>
                        <p id="hintLevel" class="text-xs text-gray-500 mt-1" style="display:none;">* Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</p>

                        <!-- Select for Class -->
                        <select id="targetValueClass" multiple class="w-full px-4 py-3 rounded-lg border border-[#dbe6db] bg-white text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary font-medium h-32" style="display: none; color: black !important; background-color: white !important;">
                             @php
                                $selectedClasses = json_decode($biaya->target_value, true) ?? [];
                                if (!is_array($selectedClasses)) $selectedClasses = [$biaya->target_value];
                            @endphp
                            @foreach($kelas as $k)
                                <option value="{{ $k->nama }}" style="color: black !important; background-color: white !important;" {{ in_array($k->nama, $selectedClasses) ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        <p id="hintClass" class="text-xs text-gray-500 mt-1" style="display:none;">* Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</p>

                        <!-- Hidden Input to Store Final Value -->
                        <input type="hidden" name="target_value" id="finalTargetValue" value="{{ $biaya->target_value }}">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#618961]">Nominal Default (Rp)</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', $biaya->jumlah) }}" class="w-full px-4 py-3 rounded-lg border border-[#dbe6db] dark:border-[#2a3a2a] bg-slate-50 dark:bg-[#1e331e] text-[#111812] dark:text-white focus:ring-2 focus:ring-primary focus:border-primary font-medium" required>
                </div>

                <div class="pt-6 flex gap-4">
                    <button type="submit" class="flex-1 bg-primary text-[#111812] py-3 rounded-xl font-black text-sm hover:brightness-110 transition-all shadow-lg shadow-primary/20">
                        SIMPAN PERUBAHAN
                    </button>
                    <a href="{{ route('keuangan.biaya-lain.index') }}" class="px-6 py-3 rounded-xl border border-[#dbe6db] dark:border-[#2a3a2a] text-[#111812] dark:text-white font-bold text-sm hover:bg-gray-50 dark:hover:bg-[#1e331e] transition-colors">
                        BATAL
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const targetType = document.getElementById('targetType');
            const targetValueContainer = document.getElementById('targetValueContainer');

            const inputLevel = document.getElementById('targetValueLevel');
            const inputClass = document.getElementById('targetValueClass');
            const inputText = document.getElementById('targetValueText');
            const finalInput = document.getElementById('finalTargetValue');

            function updateVisibility() {
                // Reset display
                inputLevel.style.display = 'none';
                inputClass.style.display = 'none';
                inputText.style.display = 'none';
                document.getElementById('hintLevel').style.display = 'none';
                document.getElementById('hintClass').style.display = 'none';
                targetValueContainer.style.display = 'block';

                if (targetType.value === 'all') {
                    targetValueContainer.style.display = 'none';
                    finalInput.value = ''; // Clear value for 'all'
                } else if (targetType.value === 'level') {
                    inputLevel.style.display = 'block';
                    document.getElementById('hintLevel').style.display = 'block';
                } else if (targetType.value === 'class') {
                    inputClass.style.display = 'block';
                    document.getElementById('hintClass').style.display = 'block';
                } else {
                    inputText.style.display = 'block';
                }
            }

            function updateFinalValue() {
                if (targetType.value === 'level') {
                    const selected = Array.from(inputLevel.selectedOptions).map(option => option.value);
                    finalInput.value = JSON.stringify(selected);
                } else if (targetType.value === 'class') {
                    const selected = Array.from(inputClass.selectedOptions).map(option => option.value);
                    finalInput.value = JSON.stringify(selected);
                } else {
                    finalInput.value = inputText.value;
                }
            }

            // Event Listeners
            targetType.addEventListener('change', function() {
                updateVisibility();
                // Clear final value when switching types to avoid data mismatch
                if (this.value !== 'all') { // Optional: clear value when switching types
                     finalInput.value = '';
                     inputLevel.value = '';
                     inputClass.value = '';
                     inputText.value = '';
                     // Or better: don't clear, let user select. But 'all' clears it.
                }
            });
            inputLevel.addEventListener('change', updateFinalValue);
            inputClass.addEventListener('change', updateFinalValue);
            inputText.addEventListener('input', updateFinalValue);

            // Initial Run
            updateVisibility();
        });

        function toggleDateInput() {
            const isSekali = document.getElementById('tipeSekali').checked;
            const inputSekali = document.getElementById('inputSekali');
            const inputBulanan = document.getElementById('inputBulanan');

            if (isSekali) {
                inputSekali.style.display = 'block';
                inputBulanan.style.display = 'none';
            } else {
                inputSekali.style.display = 'none';
                inputBulanan.style.display = 'block';
            }
        }
    </script>
</x-app-layout>

