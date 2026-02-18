@extends('layouts.app')

@section('title', 'Preview Import Nilai')

@section('content')
<div class="flex flex-col gap-6 h-[calc(100vh-120px)] overflow-hidden">
    <div class="card-boss !p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">edit_note</span>
                Validasi & Edit Data Import
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Preview data sebelum disimpan ke database.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('grade.import.index', $kelas->id) }}" class="btn-boss bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-semibold flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Batal
            </a>
            <button type="submit" form="importForm" class="btn-boss btn-primary flex items-center gap-2 shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined">save</span> Simpan Semua
            </button>
        </div>
    </div>

    @if(!empty($importErrors))
    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl p-4 shrink-0">
        <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400 font-bold mb-2">
            <span class="material-symbols-outlined">error</span> File Anda mengandung kesalahan:
        </div>
        <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-300 space-y-1">
            @foreach($importErrors as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <div class="mt-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 italic">Data yang error tidak ditampilkan. Silakan perbaiki file Excel dan upload ulang jika data tersebut penting.</p>
        </div>
    </div>
    @endif

    <form action="{{ route('grade.import.store') }}" method="POST" id="importForm" class="flex flex-col flex-1 overflow-hidden">
        @csrf
        <input type="hidden" name="import_key" value="{{ $importKey }}">

        <div class="card-boss !p-0 flex flex-col flex-1 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    Data Terbaca: <span class="bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-2 py-0.5 rounded ml-1">{{ count($parsedData) }} Siswa</span>
                </h3>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-auto custom-scrollbar relative flex-1">
                <table class="w-full text-left font-sm border-separate border-spacing-0">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 sticky top-0 z-30 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 min-w-[50px] bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky left-0 z-40" rowspan="2">No</th>
                            <th class="px-4 py-3 min-w-[250px] sticky left-[50px] z-40 bg-slate-50 dark:bg-slate-800 border-b border-r border-slate-200 dark:border-slate-700 shadow-[2px_0_5px_rgba(0,0,0,0.05)]" rowspan="2">Nama Siswa</th>

                            @php
                                $structGrades = $structure['grades'] ?? [];
                                $structNonAcademic = $structure['non_academic'] ?? [];
                            @endphp

                            {{-- Mapel Headers --}}
                            @foreach($structGrades as $pId => $mapels)
                                @foreach($mapels as $mId => $meta)
                                    <th class="px-2 py-3 text-center border-l border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 min-w-[150px]" colspan="{{ $jenjang == 'MTS' ? 3 : 2 }}">
                                        @php
                                            $pName = count($structGrades) > 1 ? "(P$pId) " : "";
                                        @endphp
                                        <div class="text-[10px] text-slate-400">{{ $pName }}</div>
                                        <span class="font-arabic text-slate-800 dark:text-white">{{ $meta['nama_mapel'] }}</span>
                                    </th>
                                @endforeach
                            @endforeach

                            {{-- Non-Academic Headers --}}
                            @foreach($structNonAcademic as $pId => $fields)
                                <th class="px-2 py-3 text-center border-l border-b border-slate-200 dark:border-slate-700 bg-primary/5 dark:bg-primary/10 min-w-[200px]" colspan="{{ count($fields) }}">
                                     @php $pName = count($structNonAcademic) > 1 ? "(P$pId) " : ""; @endphp
                                     <div class="text-[10px] text-primary/60 dark:text-primary/60">{{ $pName }}</div>
                                     <span class="text-primary dark:text-primary">Kehadiran & Sikap</span>
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            <!-- Sub Headers Mapel -->
                            @foreach($structGrades as $pId => $mapels)
                                @foreach($mapels as $mId => $meta)
                                    <th class="px-1 py-1 text-center bg-white dark:bg-slate-700 text-[10px] border-l border-b border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-300">H</th>
                                    <th class="px-1 py-1 text-center bg-white dark:bg-slate-700 text-[10px] border-b border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-300">{{ $jenjang == 'MI' ? 'Cawu' : 'PTS' }}</th>
                                    @if($jenjang == 'MTS')
                                    <th class="px-1 py-1 text-center bg-white dark:bg-slate-700 text-[10px] border-b border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-300">PAS</th>
                                    @endif
                                @endforeach
                            @endforeach

                            <!-- Sub Headers Non-Academic -->
                            @foreach($structNonAcademic as $pId => $fields)
                                @foreach($fields as $field)
                                    @php
                                        $label = ucfirst($field);
                                        $widthClass = 'min-w-[100px]'; // Default for Personality

                                        if($field == 'sakit') { $label = 'S'; $widthClass = 'min-w-[50px]'; }
                                        elseif($field == 'izin') { $label = 'I'; $widthClass = 'min-w-[50px]'; }
                                        elseif($field == 'tanpa_keterangan') { $label = 'A'; $widthClass = 'min-w-[50px]'; }
                                        elseif($field == 'kelakuan') $label = 'Kelakuan';
                                        elseif($field == 'kerajinan') $label = 'Kerajinan';
                                        elseif($field == 'kebersihan') $label = 'Kebersihan';
                                    @endphp
                                    <th class="px-1 py-1 text-center bg-white dark:bg-slate-700 text-[10px] border-l border-b border-slate-200 dark:border-slate-600 {{ $widthClass }} text-slate-500 dark:text-slate-300" title="{{ ucfirst(str_replace('_', ' ', $field)) }}">{{ $label }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($parsedData as $idx => $row)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-2 text-center text-slate-500 font-bold sticky left-0 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-20">{{ $idx + 1 }}</td>
                                <td class="px-4 py-2 font-bold text-slate-800 dark:text-white sticky left-[50px] bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/30 z-20 border-r border-slate-100 dark:border-slate-700 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                    {{ $row['siswa']->nama_lengkap }}
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $row['siswa']->nis_lokal }}</div>
                                </td>

                                {{-- Grades Inputs --}}
                                @foreach($structGrades as $pId => $mapels)
                                    @foreach($mapels as $mId => $meta)
                                        @php
                                            $g = $row['grades'][$pId][$mId] ?? ['harian'=>null, 'uts'=>null, 'uas'=>null];
                                        @endphp
                                        <!-- Harian -->
                                        <td class="p-1 border-l border-slate-100 dark:border-slate-700">
                                            <input type="number"
                                                   name="grades[{{ $row['siswa']->id }}][{{ $pId }}][{{ $mId }}][harian]"
                                                   value="{{ $g['harian'] }}"
                                                   class="w-full text-center text-xs font-bold text-slate-700 dark:text-slate-300 border-0 bg-transparent focus:ring-2 focus:ring-primary rounded py-1 px-0 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                                                   min="0" max="100">
                                        </td>
                                        <!-- UTS -->
                                        <td class="p-1">
                                            <input type="number"
                                                   name="grades[{{ $row['siswa']->id }}][{{ $pId }}][{{ $mId }}][uts]"
                                                   value="{{ $g['uts'] }}"
                                                   class="w-full text-center text-xs font-bold text-slate-700 dark:text-slate-300 border-0 bg-transparent focus:ring-2 focus:ring-primary rounded py-1 px-0 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                                                   min="0" max="100">
                                        </td>
                                        <!-- UAS -->
                                        @if($jenjang == 'MTS')
                                        <td class="p-1">
                                            <input type="number"
                                                   name="grades[{{ $row['siswa']->id }}][{{ $pId }}][{{ $mId }}][uas]"
                                                   value="{{ $g['uas'] }}"
                                                   class="w-full text-center text-xs font-bold text-slate-700 dark:text-slate-300 border-0 bg-transparent focus:ring-2 focus:ring-primary rounded py-1 px-0 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                                                   min="0" max="100">
                                        </td>
                                        @endif
                                    @endforeach
                                @endforeach

                                {{-- Non-Academic Inputs --}}
                                @foreach($structNonAcademic as $pId => $fields)
                                    @foreach($fields as $field)
                                        @php
                                            $val = $row['non_academic'][$pId][$field] ?? '';
                                            $displayVal = $val !== null ? (string)$val : '';
                                        @endphp
                                        <td class="p-1 border-l border-slate-100 dark:border-slate-700 bg-primary/5 dark:bg-primary/10">
                                            <input type="text"
                                                   name="non_academic[{{ $row['siswa']->id }}][{{ $pId }}][{{ $field }}]"
                                                   value="{{ $displayVal }}"
                                                   class="w-full text-center text-xs font-bold text-primary border-0 bg-transparent focus:ring-2 focus:ring-primary rounded py-1 px-0 hover:bg-primary/10 transition-colors"
                                                   placeholder="-">
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (optimized with x-data) -->
            <div class="md:hidden flex flex-col gap-4 p-4 overflow-y-auto">
                <p class="text-xs text-slate-500 dark:text-slate-400 italic text-center mb-2">Tap kartu untuk melihat detail nilai</p>
                @foreach($parsedData as $idx => $row)
                <div class="card-boss !p-0 overflow-hidden" x-data="{ expanded: false }">
                    <div class="p-4 flex items-center gap-4 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" @click="expanded = !expanded">
                         <div class="size-10 flex-shrink-0 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center font-bold text-sm">
                             {{ $idx + 1 }}
                         </div>
                         <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-900 dark:text-white truncate">{{ $row['siswa']->nama_lengkap }}</h4>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $row['siswa']->nis_lokal }}</div>
                         </div>
                         <div class="text-slate-400 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''">
                            <span class="material-symbols-outlined">expand_more</span>
                         </div>
                    </div>

                    <div x-show="expanded" x-collapse style="display: none;" class="border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 p-4">
                        <div class="flex flex-col gap-4">
                            {{-- Grades Inputs --}}
                            @foreach($structGrades as $pId => $mapels)
                                @foreach($mapels as $mId => $meta)
                                    @php
                                        $g = $row['grades'][$pId][$mId] ?? ['harian'=>null, 'uts'=>null, 'uas'=>null];
                                    @endphp
                                    <div class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-slate-200 dark:border-slate-700 shadow-sm">
                                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mb-2 border-b border-slate-100 dark:border-slate-700 pb-2 font-arabic flex justify-between">
                                            <span>{{ $meta['nama_mapel'] }}</span>
                                            <span class="text-slate-400 font-normal font-sans text-[10px]">(P{{ $pId }})</span>
                                        </div>
                                        <div class="grid grid-cols-{{ $jenjang == 'MTS' ? '3' : '2' }} gap-3">
                                            <div class="flex flex-col gap-1">
                                                <label class="text-[9px] text-slate-400 uppercase font-bold text-center">Harian</label>
                                                <input type="number" name="grades[{{ $row['siswa']->id }}][{{ $pId }}][{{ $mId }}][harian]" value="{{ $g['harian'] }}" class="input-boss text-center p-1 text-sm h-9" min="0" max="100">
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <label class="text-[9px] text-slate-400 uppercase font-bold text-center">{{ $jenjang == 'MI' ? 'Cawu' : 'PTS' }}</label>
                                                <input type="number" name="grades[{{ $row['siswa']->id }}][{{ $pId }}][{{ $mId }}][uts]" value="{{ $g['uts'] }}" class="input-boss text-center p-1 text-sm h-9" min="0" max="100">
                                            </div>
                                            @if($jenjang == 'MTS')
                                            <div class="flex flex-col gap-1">
                                                <label class="text-[9px] text-slate-400 uppercase font-bold text-center">PAS</label>
                                                <input type="number" name="grades[{{ $row['siswa']->id }}][{{ $pId }}][{{ $mId }}][uas]" value="{{ $g['uas'] }}" class="input-boss text-center p-1 text-sm h-9" min="0" max="100">
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach

                            {{-- Non-Academic Inputs --}}
                            @if(count($structNonAcademic) > 0)
                            <div class="bg-primary/5 rounded-xl p-3 border border-primary/10">
                                <span class="text-xs font-bold text-primary block mb-2 text-center uppercase tracking-wider">Kehadiran & Sikap</span>
                                @foreach($structNonAcademic as $pId => $fields)
                                    <div class="grid grid-cols-2 gap-3">
                                    @foreach($fields as $field)
                                        @php
                                            $val = $row['non_academic'][$pId][$field] ?? '';
                                            $displayVal = $val !== null ? (string)$val : '';
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <label class="text-[9px] text-slate-500 dark:text-slate-400 uppercase text-center font-bold">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                            <input type="text" name="non_academic[{{ $row['siswa']->id }}][{{ $pId }}][{{ $field }}]" value="{{ $displayVal }}" class="input-boss text-center p-1 text-sm h-9 border-primary/20 focus:border-primary" placeholder="-">
                                        </div>
                                    @endforeach
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="h-20"></div> <!-- Spacer for scrolling -->
            </div>
        </div>
    </form>
</div>
@endsection
