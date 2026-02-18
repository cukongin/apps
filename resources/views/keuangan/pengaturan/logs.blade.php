<x-app-layout>
    <x-slot name="header">
        Jejak Aktivitas Sistem (Audit Logs)
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1a2e1d] overflow-hidden shadow-sm sm:rounded-lg border border-[#e0e8e1] dark:border-[#2a3a2d]">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-[#111812] dark:text-white">Rekaman Aktivitas Pengguna</h3>
                        <a href="{{ route('keuangan.pengaturan.index') }}" class="text-sm text-[#618968] hover:text-primary">Kembali ke Pengaturan</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-[#233827] dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Waktu</th>
                                    <th class="px-6 py-3">User</th>
                                    <th class="px-6 py-3">Aksi</th>
                                    <th class="px-6 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr class="bg-white border-b dark:bg-[#1a2e1d] dark:border-[#2a3a2d] hover:bg-gray-50 dark:hover:bg-[#233827]">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $log->created_at->format('d M Y H:i:s') }}
                                        <div class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-[#111812] dark:text-white">
                                        {{ $log->user->name ?? 'Guest/System' }}
                                        <div class="text-[10px] text-gray-400">{{ $log->user->role ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $color = 'text-gray-600';
                                            if(Str::contains($log->action, 'DELETE')) $color = 'text-red-600 bg-red-50 px-2 py-1 rounded font-bold';
                                            if(Str::contains($log->action, 'create')) $color = 'text-green-600 bg-green-50 px-2 py-1 rounded font-bold';
                                            if(Str::contains($log->action, 'LOGIN')) $color = 'text-blue-600';
                                        @endphp
                                        <span class="{{ $color }}">{{ $log->action }}</span>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate" title="{{ $log->description }}">
                                        {{ Str::limit($log->description, 50) }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        {{ $log->ip_address }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">Belum ada aktivitas terekam.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $logs->onEachSide(1)->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

