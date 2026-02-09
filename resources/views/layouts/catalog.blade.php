<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Product Catalog') - {{ config('app.name', 'E-comm') }}</title>

        @include('layouts.partials.theme-init')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
        <!-- Minimalist Navigation Bar -->
        <nav class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50 shadow-sm transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <a href="/" class="flex items-center space-x-2 group">
                        <div class="w-8 h-8 bg-emerald-custom rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-white font-bold text-sm">E</span>
                        </div>
                        <span class="text-xl font-bold text-black dark:text-white">{{ config('app.name', 'E-comm') }}</span>
                    </a>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center space-x-6">
                        <a href="/" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-custom transition-colors">Home</a>
                        <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-black dark:text-white border-b-2 border-emerald-custom">Catalog</a>
                        <a href="/#news" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-custom transition-colors">News</a>
                    </div>

                    <!-- User Menu & Mobile Toggle -->
                    <div class="flex items-center gap-3">
                        <x-dark-mode-toggle />
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="hidden sm:inline-block text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-emerald-custom transition-colors">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="hidden sm:inline-block text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-emerald-custom transition-colors">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="hidden sm:inline-block text-sm font-medium px-4 py-2 bg-black dark:bg-emerald-custom text-white rounded-lg hover:bg-gray-900 dark:hover:bg-[#0ea572] transition-colors">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        @endif
                        <button id="mobile-menu-toggle" class="lg:hidden text-gray-700 dark:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Filter Bar (Sticky below nav) -->
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-16 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div id="mobile-menu" class="hidden lg:block py-4">
                    <form method="GET" action="{{ route('catalog.index') }}" class="flex flex-wrap items-center gap-3">
                        <!-- Search Input -->
                        <div class="relative flex-1 min-w-[200px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Search products..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all"
                            >
                        </div>

                        <!-- Category Filter -->
                        <select 
                            name="category" 
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all cursor-pointer"
                        >
                            <option value="">All Categories</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        <!-- Sort By -->
                        <select 
                            name="sort_by" 
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all cursor-pointer"
                        >
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Newest</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                        </select>

                        <!-- Sort Order -->
                        <select 
                            name="sort_order" 
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all cursor-pointer"
                        >
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
                        </select>

                        <!-- Clear Filters Button -->
                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'sort_by', 'sort_order']))
                            <a href="{{ route('catalog.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-emerald-custom transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Minimalist Footer -->
        <footer class="bg-black dark:bg-gray-900 text-white mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-8 h-8 bg-emerald-custom rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">E</span>
                            </div>
                            <span class="text-xl font-bold">{{ config('app.name', 'E-comm') }}</span>
                        </div>
                        <p class="text-gray-400 text-sm">
                            Quality merchandise for your business needs.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider">Quick Links</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="{{ route('catalog.index') }}" class="hover:text-emerald-custom transition-colors">Catalog</a></li>
                            <li><a href="/#news" class="hover:text-emerald-custom transition-colors">News</a></li>
                            <li><a href="#" class="hover:text-emerald-custom transition-colors">About</a></li>
                            <li><a href="#" class="hover:text-emerald-custom transition-colors">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider">Contact</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li>info@e-comm.com</li>
                            <li>+62 123 456 789</li>
                            <li>Jakarta, Indonesia</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name', 'E-comm') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Mobile Menu Toggle Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof $ !== 'undefined') {
                    $('#mobile-menu-toggle').on('click', function() {
                        $('#mobile-menu').toggleClass('hidden');
                    });
                } else {
                    document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
                        const menu = document.getElementById('mobile-menu');
                        menu.classList.toggle('hidden');
                    });
                }
            });
        </script>
        
        @stack('scripts')
    </body>
</html>
