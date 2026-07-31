<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Apotek') }} - @yield('title')</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981', // main
                            600: '#059669', // hover
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 20px rgba(16, 185, 129, 0.3)',
                    }
                }
            }
        }
    </script>
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
        /* Smooth scrolling */
        html { scroll-behavior: smooth; }
        /* Custom Scrollbar for a premium feel */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-primary-500 selection:text-white min-h-screen flex flex-col">
    <div id="app" class="flex-grow flex flex-col">
        <!-- Navigation -->
        <nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-200/60 shadow-sm transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 text-white shadow-glow group-hover:scale-105 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600">
                                E-Apotek
                            </span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    @auth
                    @php
                        $navItems = [
                                ['route' => 'dashboard', 'label' => 'Dashboard', 'pattern' => 'dashboard'],
                                ['route' => 'pos', 'label' => 'POS', 'pattern' => 'pos*'],
                                [
                                    'label' => 'Master',
                                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
                                    'pattern' => 'medicines.*|suppliers.*',
                                    'children' => [
                                        ['route' => 'medicines.index', 'label' => 'Obat', 'pattern' => 'medicines.*'],
                                        ['route' => 'suppliers.index', 'label' => 'Supplier', 'pattern' => 'suppliers.*'],
                                    ]
                                ],
                                [
                                    'label' => 'Inventori',
                                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                                    'pattern' => 'purchases.*|stock-opname.*|stock-card.*',
                                    'children' => [
                                        ['route' => 'purchases.index', 'label' => 'Pembelian', 'pattern' => 'purchases.*'],
                                        ['route' => 'stock-opname.index', 'label' => 'Stok Opname', 'pattern' => 'stock-opname.*'],
                                        ['route' => 'stock-card.index', 'label' => 'Kartu Stok', 'pattern' => 'stock-card.*'],
                                    ]
                                ],
                                ['route' => 'transactions.index', 'label' => 'Transaksi', 'pattern' => 'transactions.*'],
                                ['route' => 'reports.sales', 'label' => 'Laporan', 'pattern' => 'reports.*'],
                            ];
                        @endphp

                    <div class="flex items-center">
                        <div class="hidden md:flex items-center space-x-1">
                        @foreach($navItems as $item)
                            @if(isset($item['children']))
                                @php
                                    $isActive = request()->routeIs(explode('|', $item['pattern']));
                                @endphp
                                <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                                    <button class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 ease-in-out {{ $isActive ? 'text-primary-700 bg-primary-50/80 shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                                        {!! $item['icon'] !!}
                                        {{ $item['label'] }}
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                        @click.away="open = false"
                                        class="absolute top-full left-0 mt-1 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95">
                                        @foreach($item['children'] as $child)
                                            @php
                                                $childActive = request()->routeIs($child['pattern']);
                                            @endphp
                                            <a href="{{ route($child['route']) }}"
                                               class="flex items-center px-4 py-2.5 text-sm {{ $childActive ? 'text-primary-700 bg-primary-50 font-semibold' : 'text-slate-600 hover:text-slate-800 hover:bg-slate-50' }}">
                                                {{ $child['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @php
                                    $isActive = request()->routeIs($item['pattern']);
                                @endphp
                                <a href="{{ route($item['route']) }}" 
                                   class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 ease-in-out relative group {{ $isActive ? 'text-primary-700 bg-primary-50/80 shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                                    {{ $item['label'] }}
                                    @if($isActive)
                                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-4 h-1 bg-primary-500 rounded-t-full"></span>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                        </div>

                        <!-- Mobile Menu Toggle -->
                        <button @click="mobileOpen = !mobileOpen"
                                class="md:hidden p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-all duration-200"
                                aria-label="Toggle menu" :aria-expanded="mobileOpen">
                            <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @endauth

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="flex items-center gap-4">
                                <div class="hidden sm:flex flex-col items-end">
                                    <span class="text-sm font-semibold text-slate-700">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </div>
                                <div class="h-10 w-10 rounded-full bg-primary-100 border-2 border-primary-200 flex items-center justify-center text-primary-700 font-bold overflow-hidden shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="w-px h-8 bg-slate-200 mx-1"></div>
                                <a href="{{ route('logout') }}" 
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200"
                                    title="Logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>

                @auth
                <!-- Mobile Navigation Panel -->
                <div x-show="mobileOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="md:hidden pb-4 pt-2 border-t border-slate-200/60">
                    <div class="flex flex-col space-y-1">
                        @foreach($navItems as $item)
                            @if(isset($item['children']))
                                @php
                                    $isActive = request()->routeIs(explode('|', $item['pattern']));
                                @endphp
                                <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                                    <button @click="open = !open"
                                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ $isActive ? 'text-primary-700 bg-primary-50/80' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                                        <span class="flex items-center gap-1.5">
                                            {!! $item['icon'] !!}
                                            {{ $item['label'] }}
                                        </span>
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak class="pl-8 flex flex-col space-y-1 mt-1">
                                        @foreach($item['children'] as $child)
                                            @php
                                                $childActive = request()->routeIs($child['pattern']);
                                            @endphp
                                            <a href="{{ route($child['route']) }}"
                                               @click="mobileOpen = false"
                                               class="px-4 py-2 rounded-lg text-sm {{ $childActive ? 'text-primary-700 bg-primary-50 font-semibold' : 'text-slate-600 hover:text-slate-800 hover:bg-slate-50' }}">
                                                {{ $child['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @php
                                    $isActive = request()->routeIs($item['pattern']);
                                @endphp
                                <a href="{{ route($item['route']) }}"
                                   @click="mobileOpen = false"
                                   class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ $isActive ? 'text-primary-700 bg-primary-50/80' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endauth
            </div>
        </nav>

        <!-- Flash Messages with animations -->
        <div class="fixed top-24 right-4 z-50 flex flex-col gap-2 pointer-events-none" id="flash-message-container">
            @if(session()->has('success'))
                <div class="pointer-events-auto bg-white border-l-4 border-primary-500 p-4 rounded-xl shadow-soft flex items-start gap-3 animate-[slideIn_0.3s_ease-out_forwards]" role="alert">
                    <div class="text-primary-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-slate-800">Berhasil!</h4>
                        <p class="text-sm text-slate-600 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="pointer-events-auto bg-white border-l-4 border-red-500 p-4 rounded-xl shadow-soft flex items-start gap-3 animate-[slideIn_0.3s_ease-out_forwards]" role="alert">
                    <div class="text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-slate-800">Terjadi Kesalahan</h4>
                        <p class="text-sm text-slate-600 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        <style>
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        </style>
        <script>
            // Auto hide flash messages after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('flash-message-container');
                if (container && container.children.length > 0) {
                    setTimeout(() => {
                        Array.from(container.children).forEach(child => {
                            child.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            child.style.opacity = '0';
                            child.style.transform = 'translateX(100%)';
                            setTimeout(() => child.remove(), 500); // Remove from DOM after fade out
                        });
                    }, 5000);
                }
            });
        </script>

        <!-- Main Content -->
        <main class="flex-grow w-full max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="animate-[fadeIn_0.4s_ease-out_forwards]">
                @yield('content')
            </div>
            <style>
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            </style>
        </main>
        
        <!-- Footer -->
        <footer class="mt-auto py-6 border-t border-slate-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500 font-medium">
                    &copy; {{ date('Y') }} E-Apotek. All rights reserved.
                </p>
                <div class="flex space-x-4 text-sm text-slate-500 font-medium">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-primary-500 shadow-glow"></span>
                        Sistem Aktif
                    </span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Alpine.js for interactive elements -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @stack('scripts')
</body>
</html>
