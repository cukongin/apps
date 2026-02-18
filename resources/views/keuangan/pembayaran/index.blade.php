<x-app-layout>
    <x-slot name="header">
        Rekapitulasi Tunggakan Per Kelas
    </x-slot>

    <div class="max-w-[1200px] mx-auto space-y-6 py-6">
        <!-- Top Stats & Filter -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 md:px-0">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-black tracking-tight text-[#111418] dark:text-white">Rekapitulasi Tunggakan Per Kelas</h1>
                <p class="text-[#617589] dark:text-slate-400 text-base">Monitoring sisa pembayaran SPP, Seragam, dan Biaya lainnya.</p>
            </div>
            <!-- Global Actions -->
            <div class="flex gap-3">
                <button class="flex items-center gap-2 px-4 h-11 bg-white dark:bg-[#1a2e1d] border border-[#dbe0e6] dark:border-[#2a452e] rounded-lg text-sm font-bold shadow-sm hover:bg-[#f0f2f4] dark:hover:bg-[#2a452e] transition-all text-[#111812] dark:text-white">
                    <span class="material-symbols-outlined text-xl">picture_as_pdf</span>
                    <span>Unduh PDF Rekap</span>
                </button>
                <button class="flex items-center gap-2 px-4 h-11 bg-primary text-white rounded-lg text-sm font-bold shadow-md shadow-primary/20 hover:bg-primary/90 transition-all">
                    <span class="material-symbols-outlined text-xl">share</span>
                    <span>Bagikan Laporan</span>
                </button>
            </div>
        </div>

        <!-- Top Stats (Preserved) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-6 md:px-0">
            <!-- Total Tunggakan -->
            <div class="bg-white dark:bg-[#1a2e1d] flex flex-col gap-2 rounded-xl p-6 border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-[#617589] dark:text-[#a0c2a7] text-sm font-medium leading-normal">Total Tunggakan Global</p>
                    <span class="material-symbols-outlined text-red-500 opacity-20 text-3xl">payments</span>
                </div>
                <p class="text-red-500 tracking-tight text-3xl font-extrabold">Rp {{ number_format($globalTotalTunggakan, 0, ',', '.') }}</p>
                <div class="flex items-center gap-1">
                    <span class="text-[#617589] dark:text-[#a0c2a7] text-xs font-medium">Semua Kelas</span>
                </div>
            </div>
            <!-- Santri Belum Lunas -->
            <div class="bg-white dark:bg-[#1a2e1d] flex flex-col gap-2 rounded-xl p-6 border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-[#617589] dark:text-[#a0c2a7] text-sm font-medium leading-normal">Santri Belum Lunas</p>
                    <span class="material-symbols-outlined text-orange-500 opacity-20 text-3xl">person_alert</span>
                </div>
                <p class="text-[#111418] dark:text-white tracking-tight text-3xl font-extrabold">{{ $globalStudentsWithArrears }} <span class="text-lg font-medium text-[#617589] dark:text-[#a0c2a7]">Santri</span></p>
                <div class="flex items-center gap-1 text-orange-500">
                    <span class="text-sm font-bold">Perlu Ditagih</span>
                </div>
            </div>
            <!-- Persentase Pelunasan (Mockup Logic for now based on classes) -->
            @php
                $totalS = isset($classes) ? $classes->sum('total_students') : $siswas->count(); // In search mode, total students is result count? Or 0?
                // Actually, for search mode, 'Global Progress' doesn't make much sense or should be based on search results.
                // Let's safe guard it.
                $totalS = isset($classes) ? $classes->sum('total_students') : 0;
                $totalPaid = $totalS - $globalStudentsWithArrears;
                $globalProgress = $totalS > 0 ? round(($totalPaid / $totalS) * 100, 1) : 0;
            @endphp
            <div class="bg-white dark:bg-[#1a2e1d] flex flex-col gap-2 rounded-xl p-6 border border-[#dbe0e6] dark:border-[#2a452e] shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-[#617589] dark:text-[#a0c2a7] text-sm font-medium leading-normal">Persentase Pelunasan</p>
                    <span class="material-symbols-outlined text-primary opacity-20 text-3xl">verified</span>
                </div>
                <p class="text-primary tracking-tight text-3xl font-extrabold">{{ $globalProgress }}%</p>
                <div class="flex items-center gap-1 text-primary">
                    <span class="text-sm font-bold">Global</span>
                </div>
            </div>
        </div>

        <!-- ToolBar & Filter -->
        <div class="px-6 md:px-0">
            <form id="filterForm" action="{{ route('keuangan.pembayaran.index') }}" method="GET" class="bg-white dark:bg-[#1a2e1d] border border border-[#dbe0e6] dark:border-[#2a452e] rounded-xl shadow-sm p-4 flex flex-col xl:flex-row justify-between gap-4">
                <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'total_tunggakan') }}">
                <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 flex-1">
                    <div class="flex gap-3">
                        <label class="flex flex-col gap-1 flex-1">
                            <span class="text-[10px] font-bold text-[#617589] dark:text-[#a0c2a7] uppercase ml-1">Tingkat / Level</span>
                            <select name="level_id" onchange="this.form.submit()" class="form-select h-10 border-[#dbe0e6] dark:border-[#2a452e] bg-[#f0f2f4] dark:bg-[#233827] rounded-lg text-sm font-semibold focus:ring-primary focus:border-primary text-[#111812] dark:text-white w-full">
                                <option value="all">Semua Level</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>{{ $level->nama }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="flex flex-col gap-1 flex-1">
                            <span class="text-[10px] font-bold text-[#617589] dark:text-[#a0c2a7] uppercase ml-1">Kategori Biaya</span>
                            <select name="category_id" onchange="this.form.submit()" class="form-select h-10 border-[#dbe0e6] dark:border-[#2a452e] bg-[#f0f2f4] dark:bg-[#233827] rounded-lg text-sm font-semibold focus:ring-primary focus:border-primary text-[#111812] dark:text-white w-full">
                                <option value="all">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <!-- Search Class Inline -->
                    <div class="relative pt-6 flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 mt-1 -translate-y-1/2 text-gray-400 text-sm">search</span>
                        <input name="search_global" value="{{ request('search_global') }}" onkeyup="return debounceSearch()" placeholder="Cari santri/kelas/level..." class="w-full pl-9 pr-4 h-10 text-sm rounded-lg border border-[#dbe0e6] dark:border-[#2a452e] bg-[#f0f2f4] dark:bg-[#233827] focus:ring-1 focus:ring-primary outline-none dark:text-white">
                    </div>
                </div>
                <div class="flex items-center gap-3 self-end w-full md:w-auto">
                    <a href="{{ route('keuangan.pembayaran.index') }}" class="flex items-center justify-center size-10 bg-[#f0f2f4] dark:bg-[#233827] rounded-lg text-[#617589] dark:text-white hover:bg-gray-200 dark:hover:bg-[#2f4b35] transition-colors shrink-0" title="Reset Filter">
                        <span class="material-symbols-outlined">restart_alt</span>
                    </a>
                    <button type="button" onclick="alert('Fitur Kirim Tagihan Kolektif akan segera hadir.')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 h-10 bg-green-600/50 text-white rounded-lg text-sm font-bold cursor-not-allowed whitespace-nowrap" title="Segera Hadir">
                        <span class="material-symbols-outlined text-xl">chat</span>
                        <span>Kirim Tagihan WA</span>
                    </button>
                </div>
            </form>
        </div>

        <script>
            let timeout = null;
            function debounceSearch() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    document.getElementById('filterForm').submit();
                }, 800); // Wait 800ms after last keystroke
            }

            function sortTable(column) {
                const currentSort = document.querySelector('input[name="sort_by"]').value;
                const currentOrder = document.querySelector('input[name="sort_order"]').value;

                let newOrder = 'desc';
                if (currentSort === column && currentOrder === 'desc') {
                    newOrder = 'asc';
                }

                document.querySelector('input[name="sort_by"]').value = column;
                document.querySelector('input[name="sort_order"]').value = newOrder;
                document.getElementById('filterForm').submit();
            }
        </script>

        <!-- SEARCH MODE: Show Student List Directly -->
        @if(isset($isSearchMode) && $isSearchMode)
            <div class="px-6 md:px-0 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">search</span>
                        <div>
                            <p class="text-sm font-bold text-blue-800 dark:text-blue-300">Hasil Pencarian: "{{ $searchQuery }}"</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">Ditemukan {{ $siswas->count() }} santri.</p>
                        </div>
                    </div>
                    <a href="{{ route('keuangan.pembayaran.index') }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">Reset Pencarian</a>
                </div>
            </div>

            <!-- Reuse Student List Partial -->
            @include('keuangan.pembayaran.partials.student-list', ['siswas' => $siswas, 'selectedClass' => null, 'isSearch' => true])

        @else
            <!-- NORMAL MODE: Class List -->
            <div class="px-6 md:px-0">
                <div class="bg-white dark:bg-[#1a2e1d] border border-[#dbe0e6] dark:border-[#2a452e] rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto py-2">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-[#233827] border-b border-[#dbe0e6] dark:border-[#2a452e]">
                                <th onclick="sortTable('nama')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-[#2f4b35] px-4 md:px-6 py-4 text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider">
                                    Kelas {!! request('sort_by') == 'nama' ? (request('sort_order') == 'asc' ? '↑' : '↓') : '' !!}
                                </th>
                                <th class="hidden md:table-cell px-6 py-4 text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider">Total Santri</th>
                                <th class="hidden md:table-cell px-6 py-4 text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider">Santri Lunas</th>
                                <th onclick="sortTable('total_tunggakan')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-[#2f4b35] px-4 md:px-6 py-4 text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider">
                                    Nominal {!! request('sort_by') == 'total_tunggakan' ? (request('sort_order') == 'asc' ? '↑' : '↓') : '' !!}
                                </th>
                                <th onclick="sortTable('paid_percentage')" class="hidden md:table-cell cursor-pointer hover:bg-gray-100 dark:hover:bg-[#2f4b35] px-6 py-4 text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider">
                                    Progress {!! request('sort_by') == 'paid_percentage' ? (request('sort_order') == 'asc' ? '↑' : '↓') : '' !!}
                                </th>
                                <th class="px-4 md:px-6 py-4 text-xs font-bold text-[#617589] dark:text-[#a0c2a7] uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#dbe0e6] dark:divide-[#2a452e]">
                            @forelse($classes as $class)
                            <tr onclick="loadClassDetails({{ $class->id }})" data-class-id="{{ $class->id }}" data-class-row
                                class="transition-colors cursor-pointer group {{ $selectedClass && $selectedClass->id == $class->id ? 'bg-orange-50 dark:bg-orange-900/10' : 'hover:bg-[#f8f9fa] dark:hover:bg-[#233827]' }}">
                                <td class="px-4 md:px-6 py-4">
                                    <span class="font-bold text-[#111418] dark:text-white">{{ $class->nama }}</span>
                                    <!-- Mobile Subtext for Student Count -->
                                    <p class="md:hidden text-[10px] text-[#617589] dark:text-[#a0c2a7] mt-0.5">{{ $class->total_students }} Santri</p>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-sm font-medium text-[#617589] dark:text-[#a0c2a7]">
                                    {{ $class->total_students }} Santri
                                </td>
                                <td class="hidden md:table-cell px-6 py-4">
                                    @php
                                        $paidCount = $class->total_students - $class->students_with_arrears;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        {{ $paidCount }} Santri
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-4">
                                    <span class="font-bold {{ $class->total_tunggakan > 0 ? 'text-red-500' : 'text-primary' }}">
                                        Rp {{ number_format($class->total_tunggakan, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4">
                                    <div class="w-full bg-slate-100 dark:bg-[#233827] rounded-full h-2 max-w-[150px]">
                                        <div class="bg-primary h-2 rounded-full" style="width: {{ $class->paid_percentage }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-primary mt-1 block">{{ $class->paid_percentage }}%</span>
                                </td>
                                <td class="px-4 md:px-6 py-4 text-right">
                                    <span class="material-symbols-outlined text-gray-400 group-hover:text-primary transition-colors">chevron_right</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500 text-base">Kelas tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Section: Detail Preview (Dynamic Container) -->
            <div id="student-list-container" class="px-6 md:px-0">
                @if($selectedClass)
                    @include('keuangan.pembayaran.partials.student-list')
                @endif
            </div>

            <script>
                function loadClassDetails(classId) {
                    window.currentClassDetailsId = classId;
                    const container = document.getElementById('student-list-container');
                    // Loading State
                    container.innerHTML = `
                        <div class="flex justify-center items-center p-8 mt-4 bg-white dark:bg-[#1a2e1d] border border-[#dbe0e6] dark:border-[#2a452e] rounded-xl shadow-sm">
                            <span class="material-symbols-outlined animate-spin text-primary text-3xl">progress_activity</span>
                        </div>
                    `;

                    // Highlight Active Row
                    document.querySelectorAll('tr[data-class-row]').forEach(row => {
                        row.classList.remove('bg-orange-50', 'dark:bg-orange-900/10');
                        row.classList.add('hover:bg-[#f8f9fa]', 'dark:hover:bg-[#233827]');
                    });
                    const activeRow = document.querySelector(`tr[data-class-id="${classId}"]`);
                    if(activeRow) {
                        activeRow.classList.remove('hover:bg-[#f8f9fa]', 'dark:hover:bg-[#233827]');
                        activeRow.classList.add('bg-orange-50', 'dark:bg-orange-900/10');
                    }

                    // Construct URL with current filters
                    const formData = new FormData(document.getElementById('filterForm'));
                    const params = new URLSearchParams(formData);
                    params.set('class_id', classId);

                    const url = "{{ route('keuangan.pembayaran.index') }}?" + params.toString();

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        document.getElementById('detail-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    })
                    .catch(error => {
                        container.innerHTML = '<div class="p-4 text-center text-red-500">Gagal memuat data.</div>';
                    });
                }

                function closeDetail() {
                    document.getElementById('student-list-container').innerHTML = '';
                    // Remove active Highlight
                    document.querySelectorAll('tr[data-class-row]').forEach(row => {
                        row.classList.remove('bg-orange-50', 'dark:bg-orange-900/10');
                        row.classList.add('hover:bg-[#f8f9fa]', 'dark:hover:bg-[#233827]');
                    });
                }
            </script>
        @endif
    </div>

    <!-- Quick Payment Modal -->
    <div id="paymentModalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-[#1a2e1d] w-full max-w-4xl max-h-[90vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <div class="p-4 border-b border-[#dbe6dd] dark:border-[#2a3a2d] flex justify-between items-center bg-[#fcfdfc] dark:bg-[#1e3a24]">
                <div class="flex flex-col">
                    <h3 class="text-lg font-black text-[#111812] dark:text-white">Form Pembayaran Cepat</h3>
                    <p class="text-xs text-[#618968] dark:text-[#a0c2a7]" id="modalSantriName">Santri</p>
                </div>
                <button onclick="closePaymentModal()" class="size-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-[#2a3a2d] flex items-center justify-center text-gray-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div id="modalContent" class="p-6 overflow-y-auto custom-scrollbar flex-1 relative">
                <div class="flex justify-center py-10">
                    <span class="material-symbols-outlined animate-spin text-4xl text-primary">progress_activity</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(santriId, santriName) {
            const overlay = document.getElementById('paymentModalOverlay');
            const content = document.getElementById('modalContent');
            const nameLabel = document.getElementById('modalSantriName');

            nameLabel.innerText = santriName;
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent bg scrolling

            // Reset Content
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center py-10 gap-2">
                    <span class="material-symbols-outlined animate-spin text-4xl text-primary">progress_activity</span>
                    <p class="text-xs text-gray-500">Memuat data tagihan...</p>
                </div>`;

            // Fetch Form
            // Use route() helper to ensure correct prefix (/keuangan)
            const baseUrl = "{{ route('keuangan.pembayaran.index') }}";
            // We need to replace the index route to point to create, or simpler just hardcode the correct prefix
            fetch(`{{ url('/keuangan/pembayaran') }}/${santriId}/proses?mode=modal`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                content.innerHTML = html;

                // Re-execute scripts
                const scripts = content.querySelectorAll("script");
                scripts.forEach(oldScript => {
                    const newScript = document.createElement("script");
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });

                // Attach AJAX Submit Listener
                const form = content.querySelector('#paymentForm');
                if(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        Swal.fire({
                            title: 'Konfirmasi Pembayaran',
                            text: "Pastikan nominal dan biaya yang dipilih sudah benar.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#13ec37',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Bayar Sekarang!',
                            cancelButtonText: 'Batal',
                            background: document.documentElement.classList.contains('dark') ? '#1a2e1d' : '#fff',
                            color: document.documentElement.classList.contains('dark') ? '#fff' : '#111812'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show Loading
                                const btn = form.querySelector('button[type="submit"]');
                                const originalBtnText = btn.innerHTML;
                                btn.disabled = true;
                                btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Memproses...';

                                const formData = new FormData(form);

                                fetch(form.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if(data.success) {
                                        closePaymentModal();

                                        // Custom Premium Notification (Refactored to Global Toast)
                                        window.Toast.fire({
                                            icon: 'success',
                                            title: 'Pembayaran Berhasil!',
                                            background: '#ecfdf5', // emerald-50
                                            color: '#065f46', // emerald-900
                                            iconColor: '#10b981' // emerald-500
                                        });

                                        if(typeof currentClassDetailsId !== 'undefined') {
                                            loadClassDetails(currentClassDetailsId);
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: data.error || 'Terjadi kesalahan.',
                                            confirmButtonColor: '#d33'
                                        });
                                        btn.disabled = false;
                                        btn.innerHTML = originalBtnText;
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error Koneksi',
                                        text: 'Gagal memproses pembayaran.',
                                        confirmButtonColor: '#d33'
                                    });
                                    btn.disabled = false;
                                    btn.innerHTML = originalBtnText;
                                });
                            }
                        });
                    });
                }
            })
            .catch(err => {
                content.innerHTML = '<p class="text-center text-red-500 py-10">Gagal memuat form pembayaran.</p>';
            });
        }

        function closePaymentModal() {
            const overlay = document.getElementById('paymentModalOverlay');
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close on escape
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closePaymentModal();
            }
        });
    </script>
</x-app-layout>

