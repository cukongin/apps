@props(['classes', 'routeName', 'yearId' => request('year_id')])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-20 overflow-y-auto custom-scrollbar pr-2">
    @forelse($classes as $class)
    <a href="{{ route($routeName, ['kelas_id' => $class->id, 'year_id' => $yearId]) }}" class="group card-boss p-5 hover:border-primary/50 transition-all duration-300 flex flex-col justify-between h-[200px] relative overflow-hidden block">
        <!-- Decorative gradient top -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700 group-hover:from-primary group-hover:via-blue-400 group-hover:to-primary transition-all"></div>

        <div class="flex justify-between items-start z-10 w-full">
            <div class="flex flex-col gap-1 w-full">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ str_contains(strtolower($class->jenjang->nama_jenjang ?? ''), 'mi') ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }} w-fit">
                    {{ $class->jenjang->nama_jenjang ?? 'Jenjang' }}
                </span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white group-hover:text-primary transition-colors mt-1 truncate">
                    {{ $class->nama_kelas }}
                </h3>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4 relative z-10">
            @if($class->wali_kelas)
                @if($class->wali_kelas->data_guru && $class->wali_kelas->data_guru->foto)
                    <img src="{{ asset('public/' . $class->wali_kelas->data_guru->foto) }}" class="size-10 rounded-full object-cover ring-2 ring-white dark:ring-surface-dark shadow-sm shrink-0">
                @else
                    <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 shrink-0 flex items-center justify-center text-white font-bold ring-2 ring-white dark:ring-surface-dark shadow-sm text-sm">
                        {{ substr($class->wali_kelas->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex flex-col overflow-hidden">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Wali Kelas</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-1" title="{{ $class->wali_kelas->name }}">{{ $class->wali_kelas->name }}</span>
                </div>
            @else
                <div class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 shrink-0 flex items-center justify-center text-slate-400 ring-2 ring-white dark:ring-surface-dark">
                    <span class="material-symbols-outlined text-[20px]">person_off</span>
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Wali Kelas</span>
                    <span class="text-xs font-bold text-red-500 italic">Belum Ditentukan</span>
                </div>
            @endif
        </div>

        <div class="mt-auto pt-4 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between relative z-10 w-full">
            <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/50 px-2 py-1 rounded-lg">
                    <span class="material-symbols-outlined text-[16px]">group</span>
                    <span class="text-xs font-bold">{{ $class->anggota_kelas_count ?? $class->anggota_kelas()->count() }} Siswa</span>
                </div>
            </div>
            <div class="text-primary text-xs font-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                Pilih Kelas <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </div>
        </div>

        <!-- Bg Pattern -->
        <div class="absolute -bottom-6 -right-6 opacity-5 pointer-events-none">
            <span class="material-symbols-outlined text-9xl">class</span>
        </div>
    </a>
    @empty
    <div class="col-span-full flex flex-col items-center justify-center p-12 text-center bg-slate-50/50 dark:bg-slate-800/10 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700">
        <div class="bg-slate-100 dark:bg-slate-800 p-6 rounded-full mb-4 animate-pulse">
            <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600">domain_disabled</span>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Belum Ada Kelas</h3>
        <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-sm">Tidak ada kelas yang ditemukan pada pencarian ini.</p>
    </div>
    @endforelse
</div>
