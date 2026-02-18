<x-app-layout>
    <x-slot name="header">
        Setting Diskon: {{ $keringanan->nama }}
    </x-slot>

    <div class="max-w-4xl mx-auto p-6">
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('keuangan.keringanan.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-gray-600 dark:text-gray-300">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-[#111812] dark:text-white">Edit Kategori: {{ $keringanan->nama }}</h1>
                <p class="text-sm text-gray-500">Atur besaran diskon dan kelola siswa penerima.</p>
            </div>
        </div>

        <form action="{{ route('keuangan.keringanan.update', $keringanan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Base Info -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] p-6 shadow-sm mb-6">
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-4">Informasi Dasar</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#618968] mb-1">Nama Kategori</label>
                        <input type="text" name="nama" value="{{ $keringanan->nama }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-[#233827] text-sm focus:ring-primary focus:border-primary" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#618968] mb-1">Deskripsi</label>
                        <input type="text" name="deskripsi" value="{{ $keringanan->deskripsi }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-[#233827] text-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <!-- Rules Config -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] p-0 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-[#e0e8e1] dark:border-[#2a3a2d]">
                    <h2 class="text-lg font-bold text-[#111812] dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">percent</span>
                        Aturan Diskon per Biaya
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Tentukan berapa besar potongan untuk setiap jenis biaya.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-[#233827] text-xs uppercase text-gray-500 font-bold">
                            <tr>
                                <th class="px-6 py-4">Jenis Biaya</th>
                                <th class="px-6 py-4">Harga Normal</th>
                                <th class="px-6 py-4">Tipe Potongan</th>
                                <th class="px-6 py-4">Besar Potongan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#2a3a2d]">
                            @foreach($jenisBiayas as $biaya)
                                @php
                                    $rule = $keringanan->aturanDiskons->where('jenis_biaya_id', $biaya->id)->first();
                                    $tipe = $rule ? $rule->tipe_diskon : 'percentage';
                                    $jumlah = $rule ? $rule->jumlah : '';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#233827] transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-[#111812] dark:text-white">{{ $biaya->nama }}</div>
                                        <div class="mt-1">
                                            @if($biaya->target_type == 'all')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800">
                                                    Semua Jenjang
                                                </span>
                                            @elseif($biaya->target_type == 'level')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                                    Level: {{ $biaya->target_value }}
                                                </span>
                                            @elseif($biaya->target_type == 'class')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800">
                                                    Kelas: {{ $biaya->target_value }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="rules[{{ $biaya->id }}][tipe]" class="w-full rounded-lg border-gray-300 dark:border-gray-700 text-sm focus:ring-primary focus:border-primary">
                                            <option value="percentage" {{ $tipe == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                            <option value="nominal" {{ $tipe == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="rules[{{ $biaya->id }}][jumlah]" value="{{ $jumlah }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 text-sm focus:ring-primary focus:border-primary" placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-[#233827] flex justify-end gap-3">
                    <a href="{{ route('keuangan.keringanan.index') }}" class="px-6 py-2.5 rounded-lg font-bold text-gray-500 hover:bg-gray-200 transition-all">Batal</a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-[#111812] font-bold shadow-lg shadow-primary/20 hover:brightness-110 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <!-- MEMBER MANAGEMENT SECTION -->
        <div class="mt-8 bg-white dark:bg-[#1a2e1d] rounded-xl border border-[#e0e8e1] dark:border-[#2a3a2d] shadow-sm overflow-hidden">
            <div class="p-6 border-b border-[#e0e8e1] dark:border-[#2a3a2d] flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold text-[#111812] dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">groups</span>
                        Daftar Siswa Penerima ({{ count($members) }})
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola siswa yang masuk dalam kategori ini.</p>
                </div>

                <!-- Filter/Add Section -->
                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto items-end">

                    <!-- Class Selector -->
                    <div class="w-full md:w-64">
                        <form method="GET">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Tambah / Filter Siswa</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-lg pointer-events-none">filter_list</span>
                                <select name="kelas_id" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#233827] text-sm font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-all appearance-none cursor-pointer" onchange="this.form.submit()">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($levels as $lvl)
                                        <optgroup label="{{ $lvl->nama }}">
                                            @foreach($lvl->kelas as $cls)
                                                <option value="{{ $cls->id }}" {{ $selectedKelasId == $cls->id ? 'selected' : '' }}>
                                                    {{ $cls->nama }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-lg pointer-events-none">expand_more</span>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- Content Area: Either Candidates List or Members List -->
            <!-- We show Members by default.
                 BUT user wants to ADD.
                 So maybe we split the view?
                 Let's keep the Member List below, but insert the "Candidate List" above it IF a class is selected.
            -->

            @if($selectedKelasId && count($candidates) > 0)
            <div class="bg-blue-50 dark:bg-[#1f2f3a] p-4 border-b border-blue-100 dark:border-blue-900">
                <h3 class="font-bold text-blue-800 dark:text-blue-300 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined">person_add</span>
                    Kandidat dari Kelas Terpilih
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($candidates as $c)
                    <div class="bg-white dark:bg-[#1a2e1d] p-3 rounded-lg border border-blue-100 dark:border-blue-800 flex items-center justify-between shadow-sm">
                        <div class="truncate mr-2">
                            <p class="font-bold text-sm text-[#111812] dark:text-white truncate">{{ $c->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $c->nis }}</p>
                            @if($c->kategori_keringanan_id)
                                <p class="text-[10px] text-orange-500">Pindah dari: {{ optional($c->kategoriKeringanan)->nama }}</p>
                            @endif
                        </div>
                        <form action="{{ route('keuangan.keringanan.members.add', $keringanan->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="siswa_id" value="{{ $c->id }}">
                            <button type="submit" class="size-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Tambahkan">
                                <span class="material-symbols-outlined text-lg">add</span>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @elseif($selectedKelasId)
            <div class="p-6 text-center text-gray-500 border-b border-[#e0e8e1]">
                Semua siswa di kelas ini sudah masuk kategori ini (atau tidak ada siswa aktif).
            </div>
            @endif

            <div class="p-4 bg-gray-50 dark:bg-[#233827] border-b border-[#e0e8e1] dark:border-[#2a3a2d]">
                <h3 class="text-xs font-bold uppercase text-gray-500">Daftar Anggota Saat Ini</h3>
            </div>

            <!-- Existing Members Table -->

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-[#233827] text-xs uppercase text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center">No</th>
                            <th class="px-6 py-4">Nama Siswa</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#2a3a2d]">
                        @forelse($members as $index => $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#233827] transition-colors group">
                            <td class="px-6 py-4 text-center text-gray-400 text-sm">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-[#111812] dark:text-white">{{ $m->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $m->nis }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-blue-50 text-blue-600 border border-blue-100 text-[10px] font-bold">
                                    {{ optional($m->kelas)->nama ?? 'Belum Ada Kelas' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('keuangan.keringanan.members.remove', [$keringanan->id, $m->id]) }}" method="POST" onsubmit="return confirm('Keluarkan siswa ini dari kategori {{ $keringanan->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100" title="Keluarkan">
                                        <span class="material-symbols-outlined text-lg">block</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl opacity-50">group_off</span>
                                    <p class="text-sm">Belum ada siswa di kategori ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

