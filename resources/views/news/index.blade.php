@extends('layouts.catalog')

@section('title', 'Berita & Update')

@section('content')
<div class="min-h-screen">
    <!-- Page Header -->
    <div class="relative bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-black dark:text-white mb-4">
                Berita <span class="text-emerald-custom"> & Update</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Informasi terbaru tentang produk, promo, dan tips bisnis untuk UMKM
            </p>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-emerald-custom/30 to-transparent"></div>
    </div>

    <!-- News List -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($news->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($news as $item)
                    <article class="group bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300">
                        <a href="{{ route('news.show', $item->slug) }}" class="block aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-gray-800">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-custom/10 to-emerald-custom/5">
                                    <svg class="w-16 h-16 text-emerald-custom/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6m4-4h-1m-4 0H9m-2 0h2M7 8V5a2 2 0 012-2h2a2 2 0 012 2v3"></path>
                                    </svg>
                                </div>
                            @endif
                        </a>
                        <div class="p-6">
                            <time class="text-sm text-emerald-custom font-medium" datetime="{{ $item->published_at?->toIso8601String() }}">
                                {{ $item->published_at?->translatedFormat('d F Y') }}
                            </time>
                            <h2 class="mt-2 text-xl font-bold text-black dark:text-white line-clamp-2 group-hover:text-emerald-custom transition-colors">
                                <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
                            </h2>
                            <p class="mt-3 text-gray-600 dark:text-gray-400 text-sm line-clamp-3">{{ $item->excerpt }}</p>
                            <a href="{{ route('news.show', $item->slug) }}" class="mt-4 inline-flex items-center text-sm font-semibold text-emerald-custom hover:underline">
                                Baca selengkapnya
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $news->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6m4-4h-1m-4 0H9m-2 0h2M7 8V5a2 2 0 012-2h2a2 2 0 012 2v3"></path>
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Belum ada berita saat ini.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block text-emerald-custom font-medium hover:underline">Kembali ke beranda</a>
            </div>
        @endif
    </div>
</div>
@endsection
