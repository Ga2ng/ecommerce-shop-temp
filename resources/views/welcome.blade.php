<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'E-comm') }}</title>

        @include('layouts.partials.theme-init')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50 shadow-sm transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-emerald-custom rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">E</span>
                            </div>
                            <span class="text-2xl font-bold text-black dark:text-white">E-comm</span>
                        </a>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-dark-mode-toggle />
            @if (Route::has('login'))
                    @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-emerald-custom dark:hover:text-emerald-custom transition-colors">
                            Dashboard
                        </a>
                    @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-emerald-custom dark:hover:text-emerald-custom transition-colors">
                            Log in
                        </a>
                        @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-sm font-medium px-4 py-2 bg-black dark:bg-emerald-custom text-white rounded-md hover:bg-gray-900 dark:hover:bg-[#0ea572] transition-colors">
                                Register
                            </a>
                        @endif
                    @endauth
                        @endif
                    </div>
                </div>
            </div>
                </nav>

        <!-- Hero Section with Background Image and Slogan -->
        <section class="relative bg-gradient-to-br from-gray-900 to-black dark:from-black dark:to-gray-900 text-white py-24 lg:py-32 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%2310B981\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Kualitas Terbaik untuk
                    <span class="text-emerald-custom">UMKM Anda</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Temukan berbagai produk merchandise berkualitas tinggi dengan harga terjangkau untuk mendukung bisnis Anda
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('catalog.index') }}" class="px-8 py-3 bg-emerald-custom text-white font-semibold rounded-md hover:bg-[#0ea572] transition-colors">
                        Lihat Katalog
                    </a>
                    <a href="#news" class="px-8 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-md hover:bg-white hover:text-black transition-colors">
                        Berita Terbaru
                    </a>
                </div>
            </div>
        </section>

        <!-- Catalog Products Section -->
        <section id="catalog" class="py-16 lg:py-24 bg-white dark:bg-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black dark:text-white mb-4">
                        Katalog Produk
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Pilih dari berbagai produk merchandise berkualitas tinggi untuk kebutuhan bisnis Anda
                    </p>
                </div>

                <!-- Product Grid (data dari model Product) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse($products as $product)
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:shadow-lg transition-shadow group">
                            <a href="{{ route('catalog.show', $product->slug) }}" class="block aspect-square bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="w-24 h-24 bg-emerald-custom/20 rounded-full group-hover:opacity-30 transition-opacity"></div>
                                    </div>
                                @endif
                            </a>
                            <div class="p-4">
                                <h3 class="font-semibold text-black dark:text-white mb-2 line-clamp-2">
                                    <a href="{{ route('catalog.show', $product->slug) }}" class="hover:text-emerald-custom transition-colors">{{ $product->name }}</a>
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-emerald-custom">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <a href="{{ route('catalog.show', $product->slug) }}" class="px-4 py-2 bg-black dark:bg-emerald-custom text-white text-sm rounded-md hover:bg-gray-900 dark:hover:bg-[#0ea572] transition-colors">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                            <p>Belum ada produk. Silakan cek katalog nanti.</p>
                        </div>
                    @endforelse
                </div>

                <div class="text-center mt-12">
                    <a href="{{ route('catalog.index') }}" class="inline-block px-8 py-3 bg-black dark:bg-emerald-custom text-white font-semibold rounded-md hover:bg-gray-900 dark:hover:bg-[#0ea572] transition-colors">
                        Lihat Semua Produk
                    </a>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section id="news" class="py-16 lg:py-24 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black dark:text-white mb-4">
                        Berita & Update
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Dapatkan informasi terbaru tentang produk, promo, dan tips bisnis untuk UMKM
                    </p>
                </div>

                <!-- News Grid (data dari model News) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($news as $item)
                        <article class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <a href="{{ route('news.show', $item->slug) }}" class="block h-48 overflow-hidden bg-gray-100 dark:bg-gray-800">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-custom/20 to-emerald-custom/10">
                                        <span class="text-emerald-custom/40 text-3xl font-bold">News</span>
                                    </div>
                                @endif
                            </a>
                            <div class="p-6">
                                <time class="text-sm text-emerald-custom mb-2 block" datetime="{{ $item->published_at?->toIso8601String() }}">
                                    {{ $item->published_at?->translatedFormat('d F Y') }}
                                </time>
                                <h3 class="text-xl font-semibold text-black dark:text-white mb-3 line-clamp-2">
                                    <a href="{{ route('news.show', $item->slug) }}" class="hover:text-emerald-custom transition-colors">{{ $item->title }}</a>
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ $item->excerpt }}</p>
                                <a href="{{ route('news.show', $item->slug) }}" class="text-emerald-custom font-medium hover:underline inline-flex items-center">
                                    Baca Selengkapnya
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                            <p>Belum ada berita. Silakan cek kembali nanti.</p>
                        </div>
                    @endforelse
                </div>

                @if($news->isNotEmpty())
                    <div class="text-center mt-10">
                        <a href="{{ route('news.index') }}" class="inline-block px-6 py-2.5 border-2 border-emerald-custom text-emerald-custom font-semibold rounded-lg hover:bg-emerald-custom hover:text-white transition-colors">
                            Semua Berita
                        </a>
                    </div>
                @endif
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-black dark:bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-xl font-bold mb-4">{{ config('app.name', 'E-comm') }}</h3>
                        <p class="text-gray-400">
                            Platform ecommerce untuk UMKM dengan produk merchandise berkualitas tinggi.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Tautan Cepat</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#catalog" class="hover:text-emerald-custom transition-colors">Katalog</a></li>
                            <li><a href="{{ route('news.index') }}" class="hover:text-emerald-custom transition-colors">Berita</a></li>
                            <li><a href="#" class="hover:text-emerald-custom transition-colors">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-emerald-custom transition-colors">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Kontak</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li>Email: info@e-comm.com</li>
                            <li>Telp: +62 123 456 789</li>
                            <li>Alamat: Jakarta, Indonesia</li>
                    </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'E-comm') }}. All rights reserved.</p>
                </div>
        </div>
        </footer>
    </body>
</html>
