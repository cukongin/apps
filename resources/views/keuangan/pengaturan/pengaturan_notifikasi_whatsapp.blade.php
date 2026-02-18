<x-app-layout>
    <x-slot name="header">
        Pengaturan Notifikasi WhatsApp
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Info Alert -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mb-8 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-400">info</span>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300">Informasi API Gateway</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                        <p>Sistem ini menggunakan integrasi API pihak ketiga (Default: <strong>Fonnte</strong>). Pastikan Anda sudah memiliki akun dan Token API yang aktif.</p>
                        <p class="mt-1">Untuk menggunakan provider lain (Wablas, dll), cukup sesuaikan URL endpoint-nya selama format request-nya mendukung parameter <code class="bg-blue-100 px-1 rounded">target</code> dan <code class="bg-blue-100 px-1 rounded">message</code>.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Left: Config Config -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 h-fit">
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#618968]">settings</span> Konfigurasi API
                </h2>

                <form action="{{ route('keuangan.pengaturan.whatsapp.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Metode Pengiriman</label>
                        <select name="wa_mode" x-data="{ mode: '{{ $wa_mode ?? 'api' }}' }" x-model="mode" @change="$dispatch('mode-change', mode)" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#233827] dark:text-white focus:ring-[#618968] focus:border-[#618968]">
                            <option value="api">API Gateway (Otomatis Penuh - Fonnte/Wablas)</option>
                            <option value="web">WhatsApp Web (Gratis - Redirect Manual)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            <strong>API:</strong> Kirim di background (perlu Token). 
                            <strong>WA Web:</strong> Membuka tab baru di browser (perlu klik 'Kirim' manual).
                        </p>
                    </div>

                    <div x-show="mode === 'api'" x-data="{ mode: '{{ $wa_mode ?? 'api' }}' }" @mode-change.window="mode = $event.detail">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">API URL Endpoint</label>
                            <input type="url" name="wa_api_url" value="{{ old('wa_api_url', $wa_api_url) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#233827] dark:text-white focus:ring-[#618968] focus:border-[#618968]" placeholder="https://api.fonnte.com/send">
                            <p class="text-xs text-gray-500 mt-1">Default Fonnte: https://api.fonnte.com/send</p>
                        </div>
    
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">API Token / Authorization Key</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" name="wa_api_token" value="{{ old('wa_api_token', $wa_api_token) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#233827] dark:text-white focus:ring-[#618968] focus:border-[#618968]" placeholder="Masukkan Token API Anda">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                                    <span class="material-symbols-outlined text-sm" x-text="show ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 border-t border-dashed border-gray-300 pt-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Template Pesan Pembayaran</label>
                        <p class="text-xs text-gray-500 mb-2">Gunakan placeholder berikut untuk data dinamis:</p>
                        <div class="flex flex-wrap gap-2 mb-3 text-xs">
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded border border-gray-200 dark:border-gray-600 font-mono">{nama}</span>
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded border border-gray-200 dark:border-gray-600 font-mono">{nominal}</span>
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded border border-gray-200 dark:border-gray-600 font-mono">{tanggal}</span>
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded border border-gray-200 dark:border-gray-600 font-mono">{rincian}</span>
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded border border-gray-200 dark:border-gray-600 font-mono">{metode}</span>
                        </div>
                        
                        <textarea name="wa_payment_template" rows="8" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#233827] dark:text-white focus:ring-[#618968] focus:border-[#618968] font-mono text-sm">{{ old('wa_payment_template', $wa_payment_template ?? "*PEMBAYARAN DITERIMA* 💰\n\nTerima kasih, pembayaran SPP/Biaya a.n. *{nama}* telah kami terima.\n\n📅 Tanggal: {tanggal}\n💵 Nominal: Rp {nominal}\n💳 Metode: {metode}\n\nRincian:{rincian}\n\n_Pesan ini dikirim otomatis oleh Sistem Keuangan Sekolah._") }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#111812] dark:bg-black text-white px-6 py-2.5 rounded-lg font-bold hover:bg-gray-800 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined">save</span> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Test Connection -->
            <div class="bg-white dark:bg-[#1a2e1d] rounded-xl shadow-sm border border-[#dbe6dd] dark:border-[#2a3a2d] p-6 h-fit">
                <h2 class="text-lg font-bold text-[#111812] dark:text-white mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#618968]">send_to_mobile</span> Tes Koneksi
                </h2>

                <form action="{{ route('keuangan.pengaturan.whatsapp.test') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor Tujuan (HP)</label>
                        <input type="text" name="target" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#233827] dark:text-white focus:ring-[#618968] focus:border-[#618968]" placeholder="081234567890" required>
                        <p class="text-xs text-gray-500 mt-1">Pastikan diawali 08... atau 62...</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Pesan Tes (Opsional)</label>
                        <textarea name="message" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#233827] dark:text-white focus:ring-[#618968] focus:border-[#618968]" placeholder="Halo, ini tes notifikasi..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#618968] text-white px-6 py-2.5 rounded-lg font-bold hover:bg-[#4d6f54] transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined">send</span> Kirim Pesan Tes
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Guide Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>Butuh bantuan? Kunjungi dokumentasi resmi Provider API Anda.</p>
        </div>

    </div>
    </div>

    @if(session('wa_test_url'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.open("{!! session('wa_test_url') !!}", '_blank');
            }, 500);
        });
    </script>
    @endif
</x-app-layout>

