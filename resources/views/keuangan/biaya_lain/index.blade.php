<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-6 py-6">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-sm text-[#617589]">
            <a class="hover:text-primary" href="{{ route('keuangan.dashboard') }}">Keuangan</a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-[#111418] dark:text-white font-medium">Konfigurasi Biaya Dinamis</span>
        </div>

        <!-- Page Heading -->
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-[#111418] dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">Konfigurasi Biaya Dinamis</h1>
                <p class="text-[#617589] text-base font-normal max-w-2xl">Kelola kategori biaya, nominal, dan target siswa secara mandiri. Perubahan akan langsung diterapkan pada tagihan siswa.</p>
            </div>
            <a href="{{ route('keuangan.biaya-lain.create') }}" class="flex items-center gap-2 rounded-lg h-12 px-6 bg-primary text-white font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined">add</span>
                <span>Tambah Kategori Biaya</span>
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-900 flex flex-col gap-2 rounded-xl p-6 border border-[#dbe0e6] dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <p class="text-[#617589] text-sm font-medium leading-normal">Total Kategori</p>
                    <span class="material-symbols-outlined text-primary">category</span>
                </div>
                <p class="text-[#111418] dark:text-white tracking-light text-3xl font-bold">{{ $biayas->count() }}</p>
                <p class="text-[#078838] text-sm font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">trending_up</span> {{ $biayas->where('created_at', '>=', now()->subMonth())->count() }} kategori baru bulan ini
                </p>
            </div>
            <div class="bg-white dark:bg-gray-900 flex flex-col gap-2 rounded-xl p-6 border border-[#dbe0e6] dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <p class="text-[#617589] text-sm font-medium leading-normal">Kategori Aktif</p>
                    <span class="material-symbols-outlined text-green-500">check_circle</span>
                </div>
                <p class="text-[#111418] dark:text-white tracking-light text-3xl font-bold">{{ $biayas->where('status', 'active')->count() }}</p>
                <p class="text-[#617589] text-sm font-medium">{{ $biayas->where('status', 'inactive')->count() }} Kategori non-aktif</p>
            </div>
            <div class="bg-white dark:bg-gray-900 flex flex-col gap-2 rounded-xl p-6 border border-[#dbe0e6] dark:border-gray-800 shadow-sm">
                <div class="flex justify-between items-center">
                    <p class="text-[#617589] text-sm font-medium leading-normal">Estimasi Biaya (Total)</p>
                    <span class="material-symbols-outlined text-orange-500">payments</span>
                </div>
                <p class="text-[#111418] dark:text-white tracking-light text-2xl font-black">Rp {{ number_format($biayas->sum('jumlah'), 0, ',', '.') }}</p>
                <p class="text-[#617589] text-xs font-medium italic mt-1">*Jika 1 siswa membayar semua item</p>
            </div>
        </div>

        <!-- Dynamic Table -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-[#dbe0e6] dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light/50 dark:bg-gray-800/50">
                            <th class="px-6 py-4 text-sm font-bold text-[#111418] dark:text-white uppercase tracking-wider">Nama Biaya</th>
                            <th class="px-6 py-4 text-sm font-bold text-[#111418] dark:text-white uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-sm font-bold text-[#111418] dark:text-white uppercase tracking-wider">Target Siswa</th>
                            <th class="px-6 py-4 text-sm font-bold text-[#111418] dark:text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-sm font-bold text-[#111418] dark:text-white uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f2f4] dark:divide-gray-800">
                        @forelse($biayas as $biaya)
                        <tr class="hover:bg-background-light/30 dark:hover:bg-gray-800/20 transition-colors {{ $biaya->status == 'inactive' ? 'opacity-70 bg-gray-50 dark:bg-gray-800/40' : '' }}">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-[#111418] dark:text-white font-bold">{{ $biaya->nama }}</span>
                                    <span class="text-xs text-[#617589]">Kategori: {{ $biaya->kategori ?? 'Umum' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-[#111418] dark:text-white font-bold">Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                @if($biaya->target_type == 'all')
                                    <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full border border-primary/20">Semua Siswa</span>
                                @elseif($biaya->target_type == 'class')
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-bold rounded-full">Kelas {{ $biaya->target_value }}</span>
                                @elseif($biaya->target_type == 'level')
                                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-full">Tingkat {{ $biaya->target_value }}</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded-full">Custom</span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <button onclick="toggleStatus({{ $biaya->id }}, this)" class="group relative flex items-center gap-2 px-2 py-1 rounded-full border transition-all hover:shadow-md {{ $biaya->status == 'active' ? 'bg-primary/10 border-primary/20 hover:bg-primary/20' : 'bg-gray-100 border-gray-200 hover:bg-gray-200' }}">
                                    @if($biaya->status == 'active')
                                        <div class="w-8 h-4 bg-primary rounded-full relative transitions-colors">
                                            <div class="absolute right-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow-sm transition-transform"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-primary uppercase tracking-wider">Aktif</span>
                                    @else
                                        <div class="w-8 h-4 bg-gray-300 rounded-full relative transitions-colors">
                                            <div class="absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow-sm transition-transform"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Non-Aktif</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('keuangan.biaya-lain.edit', $biaya->id) }}" class="p-2 text-[#617589] hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    <form action="{{ route('keuangan.biaya-lain.destroy', $biaya->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Hapus Kategori Biaya?', 'Data ini akan dihapus permanen! Pastikan tidak ada tagihan aktif terkait kategori ini.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-[#617589] hover:text-red-500 hover:bg-red-50/50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data biaya. Klik tombol "Tambah Kategori Biaya" untuk memulai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>

            <div class="px-6 py-4 border-t border-[#dbe0e6] dark:border-gray-800">
                {{ $biayas->onEachSide(1)->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function toggleStatus(id, btn) {
        // Optimistic UI Update (Optional, but let's wait for server for safety or add loading state)
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-gray-500 text-sm">progress_activity</span>';
        btn.disabled = true;

        fetch(`{{ url('/keuangan/biaya-lain') }}/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            if (!response.ok) {
                throw new Error(response.status + ' ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                // Update UI based on new status
                if (data.new_status === 'active') {
                    btn.className = "group relative flex items-center gap-2 px-2 py-1 rounded-full border transition-all hover:shadow-md bg-primary/10 border-primary/20 hover:bg-primary/20";
                    btn.innerHTML = `
                        <div class="w-8 h-4 bg-primary rounded-full relative transitions-colors">
                            <div class="absolute right-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow-sm transition-transform"></div>
                        </div>
                        <span class="text-[10px] font-black text-primary uppercase tracking-wider">Aktif</span>
                    `;
                    // Update row visual (remove gray)
                    const row = btn.closest('tr');
                    if(row) row.classList.remove('opacity-70', 'bg-gray-50', 'dark:bg-gray-800/40');
                } else {
                    btn.className = "group relative flex items-center gap-2 px-2 py-1 rounded-full border transition-all hover:shadow-md bg-gray-100 border-gray-200 hover:bg-gray-200";
                    btn.innerHTML = `
                        <div class="w-8 h-4 bg-gray-300 rounded-full relative transitions-colors">
                            <div class="absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow-sm transition-transform"></div>
                        </div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Non-Aktif</span>
                    `;
                    // Update row visual (add gray)
                    const row = btn.closest('tr');
                    if(row) row.classList.add('opacity-70', 'bg-gray-50', 'dark:bg-gray-800/40');
                }

                // Show Toast
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                toast.fire({
                    icon: 'success',
                    title: data.message
                });

            } else {
                btn.innerHTML = originalHtml;
                alert('Gagal: ' + (data.message || 'Error tidak diketahui'));
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            alert('Terjadi kesalahan: ' + err.message);
        });
    }
</script>

