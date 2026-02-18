@extends('layouts.app')

@section('title', 'Input Nilai - ' . $assignment->mapel->nama_mapel . ($assignment->mapel->nama_kitab ? ' (' . $assignment->mapel->nama_kitab . ')' : ''))

@section('content')
<div class="flex flex-col gap-6 h-full overflow-hidden">
    <!-- Header info -->
    <div class="card-boss !p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('teacher.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span>{{ $assignment->kelas->nama_kelas }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                Input Nilai: <span class="font-arabic text-primary">{{ $assignment->mapel->nama_mapel }}</span>
                @if($assignment->mapel->nama_kitab)
                    <span class="text-lg font-normal text-slate-500 font-arabic">({{ $assignment->mapel->nama_kitab }})</span>
                @endif
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Periode: <strong class="text-slate-800 dark:text-slate-300">{{ $periode->nama_periode }}</strong> &bull;
                KKM: <strong class="text-rose-500 font-bold bg-rose-50 dark:bg-rose-900/20 px-1.5 py-0.5 rounded">{{ $nilaiKkm }}</strong>
            </p>
        </div>
        <div class="flex gap-2 flex-wrap md:flex-nowrap">
            <!-- Back Button -->
            <a href="{{ str_contains(url()->previous(), 'monitoring') ? route('walikelas.monitoring', ['kelas_id' => $assignment->id_kelas, 'periode_id' => $periode->id]) : route('teacher.dashboard') }}"
               class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Kembali
            </a>

            @php
                // Check if any grade is already final
                $isFinal = $grades->contains('status', 'final');
            @endphp

            @if($isFinal)
                <div class="flex items-center gap-2">
                    <div class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-4 py-2 rounded-xl font-bold flex items-center gap-2 border border-emerald-200 dark:border-emerald-800">
                        <span class="material-symbols-outlined">lock</span> Nilai Terkunci (Final)
                    </div>

                    {{-- Show Unlock Button for Admin, Wali Kelas, OR Assigned Teacher (Controller will check deadline) --}}
                    @if(Auth::user()->role === 'admin' || $assignment->kelas->id_wali_kelas === Auth::id() || $assignment->id_guru === Auth::id())
                    <form action="{{ route('teacher.unlock-nilai') }}" method="POST" onsubmit="return confirm('Buka kunci nilai? Status akan kembali menjadi DRAFT.')">
                        @csrf
                        <input type="hidden" name="id_kelas" value="{{ $assignment->id_kelas }}">
                        <input type="hidden" name="id_mapel" value="{{ $assignment->id_mapel }}">
                        <input type="hidden" name="id_periode" value="{{ $periode->id }}">

                        <button type="submit" class="btn-boss bg-amber-100 text-amber-700 hover:bg-amber-200 border border-amber-200 flex items-center gap-2 transition-colors" title="Buka Kunci (Revisi)">
                            <span class="material-symbols-outlined">lock_open</span> Buka Kunci
                        </button>
                    </form>
                    @endif
                </div>
            @else
                <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                    <span class="material-symbols-outlined">upload_file</span> Import Excel
                </button>
                <button type="submit" name="action" value="draft" form="nilaiForm" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                    <span class="material-symbols-outlined">save_as</span> Simpan Draft
                </button>
                <button type="submit" name="action" value="finalize" form="nilaiForm" onclick="return confirm('Apakah Anda yakin ingin memfinalisasi nilai? Data akan dikunci dan tidak bisa diubah.')" class="btn-boss btn-primary flex items-center gap-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined">check_circle</span> Finalisasi Nilai
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Info Bobot -->
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl p-4 text-sm flex flex-wrap gap-4 text-indigo-700 dark:text-indigo-300 items-center justify-center shrink-0">
        <span class="font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">scale</span> Bobot Penilaian:</span>
        <span class="px-2 py-0.5 bg-white dark:bg-indigo-900/40 rounded border border-indigo-200 dark:border-indigo-800">Harian: <b>{{ $bobot->bobot_harian }}%</b></span>
        <span class="px-2 py-0.5 bg-white dark:bg-indigo-900/40 rounded border border-indigo-200 dark:border-indigo-800">{{ $periode->lingkup_jenjang == 'MI' ? 'Ujian Cawu' : 'PTS' }}: <b>{{ $bobot->bobot_uts_cawu }}%</b></span>
        @if($periode->lingkup_jenjang == 'MTS')
        <span class="px-2 py-0.5 bg-white dark:bg-indigo-900/40 rounded border border-indigo-200 dark:border-indigo-800">PAS/PAT: <b>{{ $bobot->bobot_uas }}%</b></span>
        @endif
    </div>

    <!-- Grading Table -->
    <div class="card-boss !p-0 flex flex-col flex-1 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
        <form id="nilaiForm" action="{{ route('teacher.store-nilai') }}" method="POST" class="flex flex-col h-full overflow-hidden">
            @csrf

            <input type="hidden" name="id_kelas" value="{{ $assignment->id_kelas }}">
            <input type="hidden" name="id_mapel" value="{{ $assignment->id_mapel }}">
            <input type="hidden" name="id_periode" value="{{ $periode->id }}">
            <input type="hidden" name="bobot_harian" value="{{ $bobot->bobot_harian }}">
            <input type="hidden" name="bobot_uts" value="{{ $bobot->bobot_uts_cawu }}">
            <input type="hidden" name="bobot_uas" value="{{ $bobot->bobot_uas }}">

            <div class="overflow-auto flex-1 custom-scrollbar relative">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center bg-slate-50 dark:bg-slate-800 sticky left-0 z-30">No</th>
                            <th class="px-4 py-3 min-w-[250px] bg-slate-50 dark:bg-slate-800 sticky left-[40px] z-30 border-r border-slate-200 dark:border-slate-700 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">Nama Santri</th>

                            @if($blueprint['harian'])
                            <th class="px-2 py-3 text-center w-28">Harian</th>
                            @endif

                            @if($blueprint['uts'])
                            <th class="px-2 py-3 text-center w-28">{{ $blueprint['label_uts'] }}</th>
                            @endif

                            @if($blueprint['uas'])
                            <th class="px-2 py-3 text-center w-28">{{ $blueprint['label_uas'] }}</th>
                            @endif

                            <th class="px-2 py-3 text-center w-24 bg-slate-100 dark:bg-slate-700/50">Akhir</th>
                            <th class="px-2 py-3 text-center w-20 bg-slate-100 dark:bg-slate-700/50">Predikat</th>
                            <th class="px-4 py-3 min-w-[250px]">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($students as $index => $ak)
                        @php
                            $nilai = $grades[$ak->id_siswa] ?? null;
                            $disabled = isset($isFinal) && $isFinal ? 'disabled' : ''; // Variable defined in header block
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-4 py-3 text-center text-slate-500 font-bold sticky left-0 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-20">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-slate-800 dark:text-white sticky left-[40px] bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-20 border-r border-slate-100 dark:border-slate-700 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">{{ $ak->siswa->nama_lengkap }}</td>

                            <!-- Input Harian -->
                            @if($blueprint['harian'])
                            <td class="px-2 py-2 text-center">
                                @php
                                    $hVal = ($bobot->bobot_harian == 0 && ($nilai->nilai_harian ?? null) === null) ? 0 : ($nilai->nilai_harian ?? '');
                                    if(is_numeric($hVal)) $hVal = (int) round($hVal);
                                @endphp
                                <input {{ $disabled }} type="number" step="1" name="grades[{{ $ak->id_siswa }}][harian]" value="{{ $hVal }}" min="0" max="100" class="w-full text-center rounded-lg border-slate-200 dark:bg-slate-800 dark:border-slate-700 py-1.5 focus:ring-primary focus:border-primary font-bold text-slate-700 dark:text-slate-300 nilai-input {{ $disabled ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '' }}" data-weight="{{ $bobot->bobot_harian }}">
                            </td>
                            @else
                                <input type="hidden" name="grades[{{ $ak->id_siswa }}][harian]" value="0" class="nilai-input" data-weight="0">
                            @endif

                            <!-- Input UTS/Cawu -->
                            @if($blueprint['uts'])
                            <td class="px-2 py-2 text-center">
                                @php
                                    $tVal = ($bobot->bobot_uts_cawu == 0 && ($nilai->nilai_uts_cawu ?? null) === null) ? 0 : ($nilai->nilai_uts_cawu ?? '');
                                    if(is_numeric($tVal)) $tVal = (int) round($tVal);
                                @endphp
                                <input {{ $disabled }} type="number" step="1" name="grades[{{ $ak->id_siswa }}][uts]" value="{{ $tVal }}" min="0" max="100" class="w-full text-center rounded-lg border-slate-200 dark:bg-slate-800 dark:border-slate-700 py-1.5 focus:ring-primary focus:border-primary font-bold text-slate-700 dark:text-slate-300 nilai-input {{ $disabled ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '' }}" data-weight="{{ $bobot->bobot_uts_cawu }}">
                            </td>
                            @else
                                <input type="hidden" name="grades[{{ $ak->id_siswa }}][uts]" value="0" class="nilai-input" data-weight="0">
                            @endif

                            <!-- Input UAS (Only MTs) -->
                            @if($blueprint['uas'])
                            <td class="px-2 py-2 text-center">
                                @php
                                    $aVal = ($bobot->bobot_uas == 0 && ($nilai->nilai_uas ?? null) === null) ? 0 : ($nilai->nilai_uas ?? '');
                                    if(is_numeric($aVal)) $aVal = (int) round($aVal);
                                @endphp
                                <input {{ $disabled }} type="number" step="1" name="grades[{{ $ak->id_siswa }}][uas]" value="{{ $aVal }}" min="0" max="100" class="w-full text-center rounded-lg border-slate-200 dark:bg-slate-800 dark:border-slate-700 py-1.5 focus:ring-primary focus:border-primary font-bold text-slate-700 dark:text-slate-300 nilai-input {{ $disabled ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '' }}" data-weight="{{ $bobot->bobot_uas }}">
                            </td>
                            @else
                                <input type="hidden" name="grades[{{ $ak->id_siswa }}][uas]" value="0" class="nilai-input" data-weight="0">
                            @endif

                            <!-- Calc Result -->
                            <td class="px-2 py-3 text-center font-black text-lg bg-slate-50 dark:bg-slate-800/50">
                                <span class="nilai-akhir">{{ $nilai->nilai_akhir ?? 0 }}</span>
                            </td>
                            <td class="px-2 py-3 text-center font-bold bg-slate-50 dark:bg-slate-800/50">
                                <span class="predikat px-2 py-1 rounded bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 shadow-sm">{{ $nilai->predikat ?? '-' }}</span>
                            </td>

                            <td class="px-4 py-2">
                                <input {{ $disabled }} type="text" name="grades[{{ $ak->id_siswa }}][catatan]" value="{{ $nilai->catatan ?? '' }}" placeholder="Tambahkan catatan..." class="w-full text-sm rounded-lg border-slate-200 dark:bg-slate-800 dark:border-slate-700 py-1.5 focus:ring-primary focus:border-primary placeholder:text-slate-400 {{ $disabled ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '' }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    // Live Calculation Script
    const predicateRules = @json($predicateRules);

    // Initial Calculation on Load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('tbody tr').forEach(row => {
            // Trigger calculation for the first input of each row to init values
            const firstInput = row.querySelector('.nilai-input');
            if (firstInput) {
                // Create a fake event object
                const evt = { target: firstInput };
                calculateRow(evt);
            }
        });
    });

    document.querySelectorAll('.nilai-input').forEach(input => {
        input.addEventListener('input', calculateRow);
    });

    function calculateRow(e) {
        const row = e.target.closest('tr');
        const inputs = row.querySelectorAll('.nilai-input');
        let totalScore = 0;

        inputs.forEach(inp => {
            const val = parseFloat(inp.value) || 0;
            const weight = parseFloat(inp.getAttribute('data-weight')) || 0;
            totalScore += val * (weight / 100);
        });

        // Rounding Logic based on Settings
        const roundingEnable = {{ isset($gradingSettings) && $gradingSettings->rounding_enable ? 'true' : 'false' }};
        let finalScore = totalScore;

        if (roundingEnable) {
            finalScore = Math.round(totalScore);
        } else {
            finalScore = Math.round(totalScore * 100) / 100;
        }

        // --- KKM Coloring Only ---
        const kkm = {{ $nilaiKkm }};

        // Update Akhir
        const akhirCell = row.querySelector('.nilai-akhir');
        akhirCell.innerText = finalScore;

        // Update Color
        if (finalScore < kkm) {
            akhirCell.classList.add('text-rose-500');
            akhirCell.classList.remove('text-slate-900', 'dark:text-white');
        } else {
            akhirCell.classList.remove('text-rose-500');
            akhirCell.classList.add('text-slate-900', 'dark:text-white');
        }

        // Dynamic Rule Check
        let predikat = 'D';
        for (const rule of predicateRules) {
            if (finalScore >= rule.min_score) {
                predikat = rule.grade;
                break; // Because sorted by min_score desc
            }
        }

        const predikatSpan = row.querySelector('.predikat');
        if (predikatSpan) {
            predikatSpan.innerText = predikat;
        }
    }
</script>


<!-- Import Nilai Modal -->
<div id="importModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800">
                <form action="{{ route('teacher.input-nilai.import', ['kelas' => $assignment->id_kelas, 'mapel' => $assignment->id_mapel]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="h-12 w-12 flex-shrink-0 rounded-full bg-primary/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-2xl">upload_file</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">Import Nilai Excel</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Upload format .csv, .xls, .xlsx untuk import nilai otomatis.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Instruksi:</h4>
                                <ol class="list-decimal list-inside text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                    <li>Download Template Excel terlebih dahulu.</li>
                                    <li>Isi nilai pada kolom yang tersedia (Harian, UTS/Cawu, UAS).</li>
                                    <li>Jangan ubah format kolom atau nama siswa.</li>
                                    <li>Upload file yang sudah diisi.</li>
                                </ol>
                                <div class="mt-3">
                                    <a href="{{ route('teacher.input-nilai.template', ['kelas' => $assignment->id_kelas, 'mapel' => $assignment->id_mapel]) }}" class="inline-flex items-center gap-2 text-white bg-emerald-500 hover:bg-emerald-600 px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-lg shadow-emerald-500/20">
                                        <span class="material-symbols-outlined text-[18px]">download</span> Download Template
                                    </a>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold leading-6 text-slate-900 dark:text-white mb-2">Upload File</label>
                                <input type="file" name="file" accept=".csv, .txt, .xls, .xlsx, .html" required class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2.5 file:px-4
                                  file:rounded-xl file:border-0
                                  file:text-xs file:font-bold
                                  file:bg-primary file:text-white
                                  hover:file:bg-primary-dark
                                  file:cursor-pointer cursor-pointer
                                  border border-slate-200 dark:border-slate-700 rounded-xl
                                "/>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="btn-boss btn-primary w-full sm:w-auto shadow-lg shadow-primary/20">Upload & Validasi</button>
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 w-full sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
