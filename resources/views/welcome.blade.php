<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Rapor Madrasah - Integrated System</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-pattern {
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236366f1' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .text-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <span class="material-symbols-rounded text-white text-2xl">school</span>
                    </div>
                    <span class="font-black text-xl tracking-tight text-slate-900">E-Rapor<span class="text-indigo-600">Madrasah</span></span>
                </div>
                <div class="hidden md:flex items-center gap-8 font-bold text-sm text-slate-600">
                    <a href="#features" class="hover:text-indigo-600 transition-colors">Fitur Unggulan</a>
                    <a href="#modules" class="hover:text-indigo-600 transition-colors">Modul Sistem</a>
                    <a href="#about" class="hover:text-indigo-600 transition-colors">Tentang</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/home') }}" class="font-bold text-slate-700 hover:text-indigo-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full font-bold text-sm text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                            Masuk Siswa/Guru
                        </a>
                        <a href="{{ route('login.admin') }}" class="px-5 py-2.5 rounded-full font-bold text-sm text-white bg-slate-900 hover:bg-black transition-all shadow-lg shadow-slate-900/20">
                            Login Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="hero-pattern absolute inset-0 z-0"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -left-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs uppercase tracking-wider mb-6 animate-fade-in-up">
                Sistem Informasi Akademik Terintegrasi Version 2.0
            </span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 leading-tight mb-6 tracking-tight">
                Kelola Madrasah <br>
                <span class="text-gradient">Lebih Cerdas & Efisien</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                Platform all-in-one untuk manajemen akademik, keuangan, tabungan, dan pelaporan nilai siswa. Dirancang khusus untuk modernisasi Madrasah Diniyah.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-600 text-white font-black text-lg shadow-xl shadow-indigo-600/30 hover:shadow-2xl hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded">rocket_launch</span>
                    Mulai Sekarang
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white text-slate-700 font-bold text-lg border border-slate-200 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded">play_circle</span>
                    Pelajari Fitur
                </a>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-20 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-slate-900 mb-4">Fitur Unggulan</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Solusi komprehensif untuk menjawab kebutuhan administrasi madrasah modern.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-500/5 transition-all group">
                    <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-rounded text-3xl">auto_stories</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Manajemen Akademik</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Pengelolaan data siswa, guru, kelas, dan mata pelajaran yang terstruktur. Mendukung kurikulum Diniyah Salafiyah & Modern.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-emerald-100 hover:shadow-xl hover:shadow-emerald-500/5 transition-all group">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-rounded text-3xl">payments</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Keuangan & SPP</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Pencatatan pembayaran SPP, infaq, dan tagihan lainnya secara otomatis. Laporan keuangan transparan dan real-time.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-amber-100 hover:shadow-xl hover:shadow-amber-500/5 transition-all group">
                    <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-rounded text-3xl">savings</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Tabungan Santri</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Sistem tabungan digital untuk santri. Memudahkan pencatatan setoran dan penarikan dengan riwayat mutasi yang jelas.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="py-20 bg-slate-50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-white to-transparent opacity-50"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="w-full md:w-1/2">
                    <span class="text-indigo-600 font-black tracking-wider uppercase text-sm mb-2 block">Modul Terintegrasi</span>
                    <h2 class="text-4xl font-black text-slate-900 mb-6 leading-tigher">
                        Satu Platform untuk <br> Semua Kebutuhan
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-rounded text-emerald-500 text-2xl mt-1">check_circle</span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">E-Rapor Digital</h4>
                                <p class="text-slate-600 mt-1">Cetak rapor otomatis dengan format yang dapat disesuaikan. Mendukung tanda tangan digital.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-rounded text-emerald-500 text-2xl mt-1">check_circle</span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">Leger & Rekapitulasi</h4>
                                <p class="text-slate-600 mt-1">Pantau perkembangan nilai siswa melalui leger otomatis dan grafik statistik.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-rounded text-emerald-500 text-2xl mt-1">check_circle</span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">Presensi & Jurnal</h4>
                                <p class="text-slate-600 mt-1">Catat kehadiran dan jurnal mengajar guru secara digital dan real-time.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <div class="relative">
                        <div class="absolute insert-0 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-3xl transform rotate-3 scale-105 opacity-20 blur-lg"></div>
                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 relative">
                            <!-- Mockup UI Element -->
                            <div class="bg-slate-900 p-4 flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <div class="p-8 space-y-4">
                                <div class="flex gap-4 mb-4">
                                    <div class="w-1/3 h-24 bg-indigo-50 rounded-xl"></div>
                                    <div class="w-1/3 h-24 bg-emerald-50 rounded-xl"></div>
                                    <div class="w-1/3 h-24 bg-amber-50 rounded-xl"></div>
                                </div>
                                <div class="h-4 w-3/4 bg-slate-100 rounded-full"></div>
                                <div class="h-4 w-1/2 bg-slate-100 rounded-full"></div>
                                <div class="h-4 w-5/6 bg-slate-100 rounded-full"></div>
                                <div class="h-32 bg-slate-50 rounded-xl mt-4 border border-slate-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-900 rounded-[3rem] p-12 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

                <h2 class="text-3xl md:text-4xl font-black text-white mb-6 relative z-10">Siap Modernisasi Madrasah Anda?</h2>
                <p class="text-slate-400 max-w-xl mx-auto mb-10 text-lg relative z-10">Bergabunglah dengan transformasi digital pendidikan Islam. Kelola data lebih mudah, akurat, dan aman.</p>

                <div class="flex flex-col sm:flex-row justify-center gap-4 relative z-10">
                    <a href="{{ route('login.admin') }}" class="px-8 py-4 rounded-xl bg-white text-slate-900 font-black hover:bg-slate-100 transition-colors">
                        Login Administrator
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-rounded text-indigo-600 text-3xl">school</span>
                        <span class="font-black text-xl text-slate-900">E-Rapor</span>
                    </div>
                    <p class="text-slate-500 max-w-sm">
                        Sistem Informasi Akademik & Keuangan Terintegrasi untuk Madrasah Diniyah.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Modul</h4>
                    <ul class="space-y-2 text-slate-500 text-sm">
                        <li><a href="#" class="hover:text-indigo-600">Akademik</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Keuangan</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Tabungan</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Laporan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-slate-500 text-sm">
                        <li><a href="#" class="hover:text-indigo-600">Panduan Pengguna</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Hubungi Support</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Tentang Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-400 text-sm font-medium">&copy; {{ date('Y') }} Integrated System. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors"><span class="material-symbols-rounded">public</span></a>
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors"><span class="material-symbols-rounded">mail</span></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
