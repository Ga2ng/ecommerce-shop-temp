@extends('layouts.catalog')

@section('title', $news->title)

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Breadcrumb -->
    <div class="border-b border-gray-200 dark:border-gray-800 py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-500 dark:text-gray-400 hover:text-emerald-custom transition-colors">Home</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('news.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-emerald-custom transition-colors">Berita</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-[200px] sm:max-w-md" title="{{ $news->title }}">{{ $news->title }}</span>
            </nav>
        </div>
    </div>

    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
        <!-- Header -->
        <header class="mb-10">
            <time class="text-sm font-medium text-emerald-custom" datetime="{{ $news->published_at?->toIso8601String() }}">
                {{ $news->published_at?->translatedFormat('l, d F Y') }}
            </time>
            <h1 class="mt-3 text-3xl sm:text-4xl lg:text-5xl font-bold text-black dark:text-white leading-tight tracking-tight">
                {{ $news->title }}
            </h1>
        </header>

        <!-- Featured Image -->
        @if($news->image)
            <div class="rounded-2xl overflow-hidden mb-10 border border-gray-200 dark:border-gray-800 shadow-lg">
                <img
                    src="{{ asset('storage/' . $news->image) }}"
                    alt="{{ $news->title }}"
                    class="w-full h-auto object-cover"
                >
            </div>
        @endif

        <!-- Content (HTML dari admin atau plain text) -->
        <div class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:mt-8 [&_h2]:mb-4 [&_h2]:text-black dark:[&_h2]:text-white [&_h3]:text-xl [&_h3]:font-bold [&_h3]:mt-6 [&_h3]:mb-3 [&_p]:mb-4 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-4 [&_li]:mb-1 [&_a]:text-emerald-custom [&_a]:underline [&_a:hover]:no-underline [&_strong]:font-bold [&_strong]:text-black dark:[&_strong]:text-white">
            {!! $news->content !!}
        </div>
    </article>

    <!-- Recent News -->
    @if($recentNews->isNotEmpty())
        <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 py-14">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold text-black dark:text-white mb-6">Berita terkait</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach($recentNews as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="group block p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-custom/50 hover:shadow-md transition-all">
                            @if($item->image)
                                <div class="aspect-video rounded-lg overflow-hidden mb-3 bg-gray-100 dark:bg-gray-800">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <time class="text-xs text-emerald-custom font-medium">{{ $item->published_at?->translatedFormat('d M Y') }}</time>
                            <h3 class="mt-1 font-semibold text-black dark:text-white line-clamp-2 group-hover:text-emerald-custom transition-colors">{{ $item->title }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Back link -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
        <a href="{{ route('news.index') }}" class="inline-flex items-center text-emerald-custom font-medium hover:underline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke daftar berita
        </a>
    </div>
</div>
@endsection
