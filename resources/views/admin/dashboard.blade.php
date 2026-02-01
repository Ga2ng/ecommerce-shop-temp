@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Total Products -->
    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Products</p>
                <p class="text-3xl font-bold text-black dark:text-white">{{ $stats['total_products'] }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-custom/10 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-custom" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">{{ $stats['active_products'] }} active</p>
    </div>

    <!-- Total News -->
    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total News</p>
                <p class="text-3xl font-bold text-black dark:text-white">{{ $stats['total_news'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">{{ $stats['published_news'] }} published</p>
    </div>

    <!-- Low Stock -->
    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Low Stock</p>
                <p class="text-3xl font-bold text-orange-500">{{ $stats['low_stock_products'] }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">{{ $stats['out_of_stock'] }} out of stock</p>
    </div>
</div>

<!-- Recent Products & News -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Products -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-black dark:text-white">Recent Products</h3>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-emerald-custom hover:underline">View all</a>
            </div>
        </div>
        <div class="p-6">
            @if($recent_products->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_products as $product)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-black dark:text-white">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-emerald-custom hover:underline text-sm">Edit</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No products yet</p>
            @endif
        </div>
    </div>

    <!-- Recent News -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-black dark:text-white">Recent News</h3>
                <a href="{{ route('admin.news.index') }}" class="text-sm text-emerald-custom hover:underline">View all</a>
            </div>
        </div>
        <div class="p-6">
            @if($recent_news->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_news as $news)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-black dark:text-white">{{ Str::limit($news->title, 40) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $news->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $news->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ $news->is_published ? 'Published' : 'Draft' }}
                                </span>
                                <a href="{{ route('admin.news.edit', $news) }}" class="text-emerald-custom hover:underline text-sm">Edit</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No news yet</p>
            @endif
        </div>
    </div>
</div>
@endsection

