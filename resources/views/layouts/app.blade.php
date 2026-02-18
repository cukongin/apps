<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\GlobalSetting::val('app_name', 'Madrasah Integrated System') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/> <!-- Arabic Support -->
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet"/> <!-- Local Fonts (LPMQ) -->

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    html {
        scroll-behavior: smooth;
    }
    .material-symbols-outlined {
      font-family: 'Material Symbols Outlined';
      font-weight: normal;
      font-style: normal;
      font-size: 24px;  /* Preferred icon size */
      display: inline-block;
      line-height: 1;
      text-transform: none;
      letter-spacing: normal;
      word-wrap: normal;
      white-space: nowrap;
      direction: ltr;
    }
    .material-symbols-outlined.filled {
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    [x-cloak] { display: none !important; }
    .fouc-cloak { opacity: 0; transition: opacity 0.2s ease-in-out; }

    /* Standardized Print Styles */
    @media print {
        /* Reset all backgrounds and shadows */
        *, *::before, *::after {
            background-color: transparent !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }

        /* Force white background for main containers and table elements */
        html, body, main, table, thead, tbody, tfoot, tr, th, td, h1, h2, h3, h4, p, span, div {
            background-color: white !important;
            color: black !important;
            font-size: 11pt !important;
            line-height: 1.2 !important; /* Compact line height for 11pt */
        }

        /* Hide Scrollbars */
        ::-webkit-scrollbar {
            display: none;
        }

        /* Print-specific table borders */
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        th, td {
            border: 1px solid #000 !important;
            padding: 4px 6px !important;
            vertical-align: middle !important;
        }

        /* Autofit logic */
        td.text-right, th.text-right,
        td.text-center, th.text-center {
            white-space: nowrap !important;
            width: 1%;
        }

        /* Explicitly handle Tailwind utility classes often used for backgrounds */
        .bg-gray-50, .bg-blue-50, .bg-green-50, .bg-red-50, .bg-amber-50,
        .bg-background-light, .bg-background-dark,
        .hover\:bg-gray-50, .hover\:bg-blue-50, .hover\:bg-green-50, .hover\:bg-red-50, .hover\:bg-amber-50,
        [class*="bg-"][class*="-50"], [class*="bg-"][class*="-100"],
        [class*="hover:bg-"] {
            background-color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Ensure text visibility */
        h1, h2, h3, h4, h5, h6, p, span, div, a {
            color: black !important;
        }

        /* Hide non-print elements */
        .no-print, header, nav, aside, footer, .fab-container, .print\:hidden,
        [x-cloak], .hidden-print {
            display: none !important;
        }

        /* Layout adjustments */
        body {
            margin: 0;
            padding: 0;
            overflow: visible !important;
        }

        main {
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
        }

        @page {
            margin: 0.5cm 1.5cm;
            size: auto;
        }

        .break-inside-avoid {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .break-before-page {
            page-break-before: always !important;
            break-before: page !important;
            display: block !important;
            clear: both !important;
        }

        tr {
            page-break-inside: avoid;
        }
    }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Compiled CSS (Backup) -->
    <!-- <link href="{{ mix('css/app.css') }}" rel="stylesheet"> -->

    <!-- Tailwind CDN (HOTFIX: Ensures Styling Loads) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: 'rgb(var(--color-primary) / <alpha-value>)',
                        'primary-dark': 'rgb(var(--color-primary-dark) / <alpha-value>)',
                        secondary: 'rgb(var(--color-secondary) / <alpha-value>)',
                        'surface-dark': 'rgb(var(--color-surface-dark) / <alpha-value>)',
                        'background-dark': 'rgb(var(--color-background-dark) / <alpha-value>)',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        arabic: ['Amiri', 'serif'],
                    },
                    keyframes: {
                        'fade-in-up': {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        'bounce-subtle': {
                            '0%, 100%': { transform: 'translateY(-5%)', animationTimingFunction: 'cubic-bezier(0.8, 0, 1, 1)' },
                            '50%': { transform: 'translateY(0)', animationTimingFunction: 'cubic-bezier(0, 0, 0.2, 1)' },
                        }
                    },
                    animation: {
                        'fade-in-up': 'fade-in-up 0.5s ease-out',
                        'bounce-subtle': 'bounce-subtle 2s infinite',
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer components {
            .card-boss {
                @apply bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm transition-all duration-300;
            }
            .card-boss:hover {
                @apply shadow-lg border-primary/30 transform scale-[1.005];
            }
            .table-container {
                @apply w-full overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm;
            }
            .table-head {
                @apply bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap;
            }
            .table-row {
                @apply hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0;
            }
            .table-cell {
                @apply px-6 py-4 text-sm text-slate-600 dark:text-slate-300;
            }
            .input-boss {
                @apply w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary transition-all shadow-sm;
            }
            .btn-boss {
                @apply px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 active:scale-95 justify-center;
            }
            .btn-primary {
                @apply bg-primary hover:bg-opacity-90 text-white shadow-primary/20;
            }
            .btn-secondary {
                @apply bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700;
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar { width: 8px; height: 8px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            .dark ::-webkit-scrollbar-thumb { background: #334155; }
        }
    </style>

    {{-- Dynamic Theme Injection --}}
    @php
        $themeKey = \App\Models\GlobalSetting::val('app_theme', 'emerald');
        $themes = [
            'emerald' => [
                '--color-primary' => '0 62 41',
                '--color-primary-dark' => '0 35 24',
                '--color-secondary' => '70 112 97',
                '--color-background-dark' => '0 42 28',
                '--color-surface-dark' => '26 46 34',
            ],
            'blue' => [
                '--color-primary' => '30 58 138',
                '--color-primary-dark' => '23 37 84',
                '--color-secondary' => '96 165 250',
                '--color-background-dark' => '15 23 42',
                '--color-surface-dark' => '30 41 59',
            ],
            'purple' => [
                '--color-primary' => '88 28 135',
                '--color-primary-dark' => '59 7 100',
                '--color-secondary' => '192 132 252',
                '--color-background-dark' => '19 7 35',
                '--color-surface-dark' => '45 20 70',
            ],
            'crimson' => [
                '--color-primary' => '153 27 27',
                '--color-primary-dark' => '69 10 10',
                '--color-secondary' => '248 113 113',
                '--color-background-dark' => '25 10 10',
                '--color-surface-dark' => '60 20 20',
            ],
            'teal' => [
                '--color-primary' => '17 94 89',
                '--color-primary-dark' => '4 47 46',
                '--color-secondary' => '45 212 191',
                '--color-background-dark' => '2 25 25',
                '--color-surface-dark' => '10 50 50',
            ],
            'tosca' => [
                '--color-primary' => '3 127 122',     // #037F7A
                '--color-primary-dark' => '2 95 91',
                '--color-secondary' => '243 104 53',  // #F36835
                '--color-background-dark' => '1 40 38',
                '--color-surface-dark' => '2 60 58',
            ],
        ];
        $activeTheme = $themes[$themeKey] ?? $themes['emerald'];
    @endphp
    <style>
        :root {
            @foreach($activeTheme as $key => $val)
                {{ $key }}: {{ $val }};
            @endforeach
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.remove('fouc-cloak');
        });
    </script>
</head>
<body class="fouc-cloak bg-slate-50/50 dark:bg-background-dark text-slate-800 dark:text-slate-100 antialiased overflow-hidden bg-[url('https://grainy-gradients.vercel.app/noise.svg')] bg-fixed">
    <div class="flex h-screen w-full" x-data="{ sidebarOpen: false }">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-20 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'" class="fixed lg:relative inset-y-0 left-0 w-72 bg-white/80 dark:bg-surface-dark/90 backdrop-blur-xl border-r border-slate-200/60 dark:border-slate-800/60 flex flex-col z-30 transition-transform duration-300 ease-out lg:transform-none">
            <div class="flex items-center justify-between px-6 py-6 border-b border-slate-100 dark:border-slate-800/50">
                <div class="flex items-center gap-3">
                    @if(\App\Models\GlobalSetting::val('app_logo'))
                         <img src="{{ asset('public/' . \App\Models\GlobalSetting::val('app_logo')) }}" class="h-10 w-auto object-contain">
                    @else
                        <div class="bg-primary/10 rounded-xl p-2">
                            <span class="material-symbols-outlined text-primary text-3xl">mosque</span>
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <h1 class="text-lg font-bold leading-tight tracking-tight">{{ \App\Models\GlobalSetting::val('app_name', 'Madrasah Admin') }}</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-medium">{{ \App\Models\GlobalSetting::val('app_tagline', 'Integrated System') }}</p>
                    </div>
                </div>
                <!-- Close Button Mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- IMPERSONATION ALERT -->
            @if(session('impersonator_id'))
            <div class="px-4 pt-4">
                <form action="{{ route('impersonate.leave') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-500 text-white p-3 rounded-xl flex items-center justify-center gap-2 hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20 animate-pulse">
                        <span class="material-symbols-outlined">no_accounts</span>
                        <div class="flex flex-col text-left">
                            <span class="text-[10px] font-bold uppercase opacity-80">Mode Penyamaran</span>
                            <span class="text-xs font-bold">KEMBALI KE ADMIN</span>
                        </div>
                    </button>
                </form>
            </div>
            @endif

            <nav class="flex flex-col gap-1 px-4 py-6 flex-1 overflow-y-auto" x-data="{ activeGroup: '{{ Request::is('master*') || Request::is('classes*') ? 'master' : (Request::is('settings*') ? 'settings' : (Request::is('walikelas*') || Request::is('reports*') ? 'walikelas' : (Request::is('teacher*') ? 'teacher' : (Request::is('keuangan*') ? 'keuangan' : '')))) }}' }">

                @if(isset($sidebarMenus))
                    @foreach($sidebarMenus as $menu)
                        @php
                            $allowedRolesArray = $menu->roles->pluck('role')->toArray();
                            $user = Auth::user();
                            $hasAccess = false;

                            // 1. Check by Role directly
                            if ($user->hasRole($allowedRolesArray)) {
                                $hasAccess = true;
                            }
                            // 2. Check Wali Kelas (Special Case: Teacher with assigned class)
                            elseif (in_array('walikelas', $allowedRolesArray) && $user->isWaliKelas()) {
                                $hasAccess = true;
                            }

                            // Skip if no access
                            if (!$hasAccess) continue;

                            // Handle Section Labels
                            if ($menu->type == 'label') {
                                echo '<div class="px-4 mt-6 mb-2">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">'.$menu->title.'</p>
                                      </div>';
                                continue;
                            }

                            // Handle Items
                            $hasChildren = $menu->children->isNotEmpty();
                            $isActiveGroup = false;

                            if ($hasChildren) {
                                foreach($menu->children as $child) {
                                    if (request()->url() == url($child->url) || ($child->route && request()->routeIs($child->route))) {
                                        $isActiveGroup = true;
                                        break;
                                    }
                                }
                            }
                        @endphp

                        @if(!$hasChildren)
                            <!-- Single Menu -->
                            <a class="flex items-center gap-3 px-4 py-3 rounded-xl {{ (request()->url() == url($menu->url) || ($menu->route && request()->routeIs($menu->route))) ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }} transition-all group" href="{{ $menu->getSafeUrl() }}">
                                <span class="material-symbols-outlined filled group-hover:scale-110 transition-transform">{{ $menu->icon }}</span>
                                <span class="font-semibold text-sm">{{ $menu->title }}</span>
                            </a>
                        @else
                            <!-- Dropdown Menu -->
                            <div class="space-y-1">
                                <button @click="activeGroup = (activeGroup === '{{ $menu->id }}' ? '' : '{{ $menu->id }}')"
                                    :class="{ 'text-primary': activeGroup === '{{ $menu->id }}' || '{{ $isActiveGroup }}' }"
                                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all font-semibold text-sm group">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined group-hover:text-primary transition-colors group-hover:scale-110">{{ $menu->icon }}</span>
                                        <span>{{ $menu->title }}</span>
                                    </div>
                                    <span class="material-symbols-outlined text-slate-400 text-sm transition-transform duration-200" :class="{ 'rotate-180': activeGroup === '{{ $menu->id }}' }">expand_more</span>
                                </button>

                                <div x-show="activeGroup === '{{ $menu->id }}' || (activeGroup === '' && '{{ $isActiveGroup }}')" x-collapse class="pl-4 space-y-1 border-l-2 border-slate-100 dark:border-slate-800 ml-6">
                                    @foreach($menu->children as $child)
                                        @php
                                            $childAllowed = $child->roles->pluck('role')->toArray();
                                            $childAccess = false;

                                            // Child Access Check
                                            if ($user->hasRole($childAllowed)) {
                                                $childAccess = true;
                                            } elseif (in_array('walikelas', $childAllowed) && $user->isWaliKelas()) {
                                                $childAccess = true;
                                            }
                                        @endphp

                                        @if($childAccess)
                                        <a class="flex items-center gap-3 px-4 py-2 rounded-lg {{ (request()->url() == url($child->url) || ($child->route && request()->routeIs($child->route))) ? 'text-primary bg-primary/5 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:translate-x-1' }} transition-all" href="{{ $child->getSafeUrl() }}">
                                            <span class="material-symbols-outlined text-[18px]">{{ $child->icon }}</span>
                                            <span class="text-sm">{{ $child->title }}</span>
                                        </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    @endforeach
                @endif
            </nav>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800/50">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="h-10 w-10 rounded-full object-cover shadow-sm">
                    <div class="flex flex-col overflow-hidden flex-1">
                        <span class="text-sm font-bold truncate">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {{ Auth::user()->role == 'admin' ? 'Administrator' : (Auth::user()->role == 'teacher' ? 'Guru' : (Auth::user()->role == 'walikelas' ? 'Wali Kelas' : 'User')) }}
                        </span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST"
                          data-confirm-delete="true"
                          data-title="Keluar Aplikasi?"
                          data-message="Sesi Anda akan diakhiri."
                          data-confirm-text="Ya, Keluar"
                          data-icon="question">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors" title="Logout">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden relative">
            <header class="h-18 min-h-[72px] bg-white/80 dark:bg-[#002a1c]/80 backdrop-blur-md border-b border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0 transition-all">
                <div class="flex items-center gap-4">
                     <!-- Mobile Toggle -->
                     <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary">
                        <span class="material-symbols-outlined">menu</span>
                     </button>

                     <div class="flex flex-col">
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-medium">
                            <span class="hidden md:inline">Portal</span>
                            <span class="material-symbols-outlined text-[10px] hidden md:inline">chevron_right</span>
                            <span class="text-slate-800 dark:text-slate-200 font-bold">@yield('title', 'Dashboard')</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <!-- Notifications (Mockup/Real) -->
                    <button class="relative p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 dark:text-slate-400">
                        <span class="material-symbols-outlined">notifications</span>
                        @if(\App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->exists())
                            <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-white dark:border-background-dark animate-pulse"></span>
                        @endif
                    </button>

                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700 mx-1 hidden md:block"></div>

                    <!-- User Profile Dropdown -->
                    <div class="flex items-center gap-3 pl-1" x-data="{ open: false }">
                         <div class="relative">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 focus:outline-none group">
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="size-9 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 group-hover:border-primary transition-colors">
                                <div class="flex flex-col text-left hidden md:flex">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white leading-none group-hover:text-primary transition-colors">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase mt-0.5">{{ Auth::user()->role }}</span>
                                </div>
                                <span class="material-symbols-outlined text-slate-400 text-sm group-hover:text-primary transition-colors hidden md:block">expand_more</span>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                 style="display: none;"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-surface-dark rounded-xl shadow-lg border border-slate-100 dark:border-slate-800 py-1 focus:outline-none z-50">

                                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 md:hidden">
                                     <span class="block text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</span>
                                     <span class="block text-xs text-slate-500">{{ Auth::user()->email }}</span>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">person</span> Profil Saya
                                </a>
                                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">settings</span> Pengaturan
                                </a>
                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}"
                                      data-confirm-delete="true"
                                      data-title="Keluar Aplikasi?"
                                      data-message="Sesi Anda akan diakhiri."
                                      data-confirm-text="Ya, Keluar"
                                      data-icon="question">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">logout</span> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6 lg:p-10 pb-20">
                <!-- Notifications Area -->
                @auth
                    @php
                        $unreadNotifs = \App\Models\Notification::where('user_id', auth()->id())
                                        ->where('is_read', false)
                                        ->latest()
                                        ->get();
                    @endphp
                    @if($unreadNotifs->count() > 0)
                        <div class="mb-6 space-y-2">
                            @foreach($unreadNotifs as $notif)
                            <div class="rounded-lg p-4 flex justify-between items-start shadow-sm border-l-4 {{ $notif->type == 'warning' ? 'bg-orange-50 border-orange-400 dark:bg-orange-900/20 dark:border-orange-600' : ($notif->type == 'info' ? 'bg-primary/5 border-primary dark:bg-primary/20 dark:border-primary' : 'bg-primary/5 border-primary') }}">
                                <div class="flex gap-3">
                                    <span class="material-symbols-outlined {{ $notif->type == 'warning' ? 'text-orange-600 dark:text-orange-500' : 'text-primary' }}">
                                        {{ $notif->type == 'warning' ? 'warning' : 'info' }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-bold uppercase {{ $notif->type == 'warning' ? 'text-orange-800 dark:text-orange-200' : 'text-primary' }}">
                                            {{ $notif->type == 'warning' ? 'PERINGATAN' : ($notif->type == 'info' ? 'INFORMASI' : 'PENGINGAT') }}
                                        </p>
                                        <p class="text-sm {{ $notif->type == 'warning' ? 'text-orange-700 dark:text-orange-300' : 'text-slate-600 dark:text-slate-300' }}">
                                            {{ $notif->message }}
                                        </p>
                                    </div>
                                </div>
                                <form action="{{ route('dashboard.notification.read', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="{{ $notif->type == 'warning' ? 'text-orange-600 hover:text-orange-800' : 'text-primary hover:text-primary-dark' }} text-xs font-bold underline">OKE</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    @endif
                @endauth

                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')

    <!-- Global SweetAlert Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Toast Mixin Configuration
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                padding: '1em',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // 2. Session: Success (Toast)
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}",
                    background: '#ecfdf5', // emerald-50
                    color: '#065f46', // emerald-900
                    iconColor: '#10b981' // emerald-500
                });
            @endif

            // 3. Session: Error (Modal - More prominent)
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Waduh...',
                    html: `<div class="text-sm text-slate-600 dark:text-slate-300">{{ session('error') }}</div>`,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#ef4444', // red-500
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                });
            @endif

            // 4. Validation Errors (Modal)
            @if($errors->any())
                let errorHtml = '<div class="text-left text-sm space-y-2 bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">';
                @foreach($errors->all() as $error)
                    errorHtml += '<div class="flex items-start gap-2 text-red-700 dark:text-red-300"><span class="material-symbols-outlined text-sm mt-0.5 transform scale-90">circle</span><span>{{ $error }}</span></div>';
                @endforeach
                errorHtml += '</div>';

                Swal.fire({
                    icon: 'warning',
                    title: 'Periksa Kembali Input',
                    html: errorHtml,
                    confirmButtonText: 'Saya Perbaiki',
                    confirmButtonColor: '#f59e0b', // amber-500
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                });
            @endif

            // 5. Global Confirmation Handler
            // Usage: <form ... data-confirm-delete="true" data-title="..." data-message="..." data-confirm-text="Ya, Simpan!" data-confirm-color="#10b981">
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.getAttribute('data-confirm-delete') === 'true') {
                    e.preventDefault();

                    const message = form.getAttribute('data-message') || 'Data yang dihapus tidak dapat dikembalikan!';
                    const title = form.getAttribute('data-title') || 'Yakin Hapus?';
                    const confirmText = form.getAttribute('data-confirm-text') || 'Ya, Hapus!';
                    const cancelText = form.getAttribute('data-cancel-text') || 'Batal';
                    const confirmColor = form.getAttribute('data-confirm-color') || '#ef4444'; // Default Red
                    const iconType = form.getAttribute('data-icon') || 'warning';

                    Swal.fire({
                        title: title,
                        text: message,
                        icon: iconType,
                        showCancelButton: true,
                        confirmButtonColor: confirmColor,
                        cancelButtonColor: '#64748b', // slate-500
                        confirmButtonText: confirmText,
                        cancelButtonText: cancelText,
                        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });

             // 6. Global Modal Helpers (Bridge to Alpine x-modal)
            window.openModal = function(name) {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: name }));
            };
            window.closeModal = function(name) {
                window.dispatchEvent(new CustomEvent('close-modal', { detail: name }));
            };
        });
    </script>
</body>
</html>
