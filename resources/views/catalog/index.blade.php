@extends('layouts.catalog')

@section('title', 'Product Catalog')

@section('catalog_filters')
<form method="GET" action="{{ route('catalog.index') }}" class="flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[200px]">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all">
    </div>
    <select name="category" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all cursor-pointer">
        <option value="">All Categories</option>
        @if(isset($categories))
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        @endif
    </select>
    <select name="sort_by" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all cursor-pointer">
        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Newest</option>
        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
    </select>
    <select name="sort_order" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:border-transparent transition-all cursor-pointer">
        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
    </select>
    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'sort_by', 'sort_order']))
        <a href="{{ route('catalog.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-emerald-custom transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            Clear
        </a>
    @endif
</form>
@endsection

@section('content')
<div class="min-h-screen">
    <!-- Elegant Page Header -->
    <div class="relative bg-white dark:bg-black border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-black dark:text-white mb-4">
                    Product <span class="text-emerald-custom">Catalog</span>
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Discover our curated collection of premium merchandise products
                </p>
            </div>
        </div>
        
        <!-- Decorative accent line -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-emerald-custom to-transparent opacity-30"></div>
    </div>

    <!-- Products Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($products->count() > 0)
            <!-- Results Info -->
            <div class="flex items-center justify-between mb-8">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-black dark:text-white">{{ $products->total() }}</span> products found
                    @if(request()->hasAny(['search', 'category']))
                        <span class="mx-2">•</span>
                        <span>Filtered results</span>
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-500">
                    Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }}
                </div>
            </div>

            <!-- Modern Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                @foreach($products as $product)
                    <div class="group relative bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 dark:border-gray-800 hover:border-emerald-custom/50">
                        <!-- Product Image Container -->
                        <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 overflow-hidden">
                            <a href="{{ route('catalog.show', $product->slug) }}" class="block w-full h-full">
                                @if($product->image)
                                    <img 
                                        src="{{ asset('storage/' . $product->image) }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="w-32 h-32 bg-emerald-custom/10 rounded-full group-hover:bg-emerald-custom/20 transition-colors flex items-center justify-center">
                                            <svg class="w-16 h-16 text-emerald-custom/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Stock Badge -->
                                @if($product->stock <= 0)
                                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center">
                                        <span class="px-4 py-2 bg-red-500 text-white font-semibold rounded-full text-sm">Out of Stock</span>
                                    </div>
                                @elseif($product->stock <= 10)
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 bg-orange-500 text-white text-xs font-semibold rounded-full">Low Stock</span>
                                    </div>
                                @endif

                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-emerald-custom/0 group-hover:bg-emerald-custom/5 transition-colors duration-300"></div>
                            </a>
                        </div>

                        <!-- Product Info -->
                        <div class="p-5">
                            <!-- Category Badge -->
                            @if($product->category)
                                <div class="mb-3">
                                    <span class="inline-block px-3 py-1 text-xs font-medium bg-emerald-custom/10 text-emerald-custom rounded-full">
                                        {{ $product->category }}
                                    </span>
                                </div>
                            @endif

                            <!-- Product Name -->
                            <a href="{{ route('catalog.show', $product->slug) }}">
                                <h3 class="font-bold text-lg text-black dark:text-white mb-2 group-hover:text-emerald-custom transition-colors line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                            </a>

                            <!-- Description -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 min-h-[2.5rem]">
                                {{ Str::limit($product->description, 80) }}
                            </p>

                            <!-- Price and Action -->
                            <div class="flex items-end justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                                <div>
                                    <div class="text-2xl font-bold text-emerald-custom mb-1">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                    @if($product->stock > 0)
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            {{ $product->stock }} in stock
                                        </p>
                                    @endif
                                </div>
                                <a 
                                    href="{{ route('catalog.show', $product->slug) }}" 
                                    class="px-4 py-2 bg-black dark:bg-emerald-custom text-white text-sm font-semibold rounded-lg hover:bg-gray-900 dark:hover:bg-[#0ea572] transition-all duration-300 transform hover:scale-105 shadow-sm hover:shadow-md"
                                >
                                    View
                                </a>
                            </div>
                        </div>

                        <!-- Decorative corner accent -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-custom/5 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                @endforeach
            </div>

            <!-- Elegant Pagination -->
            <div class="mt-12 flex justify-center">
                <div class="bg-white dark:bg-gray-900 rounded-xl p-2 shadow-lg border border-gray-200 dark:border-gray-800">
                    {{ $products->links() }}
                </div>
            </div>
        @else
            <!-- Enhanced Empty State -->
            <div class="text-center py-20">
                <div class="max-w-md mx-auto">
                    <div class="relative mb-8">
                        <div class="w-32 h-32 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 rounded-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-emerald-custom/20 rounded-full"></div>
                    </div>
                    <h3 class="text-2xl font-bold text-black dark:text-white mb-3">No products found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                            We couldn't find any products matching your filters. Try adjusting your search criteria.
                        @else
                            No products are available at the moment. Please check back later.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-black dark:bg-emerald-custom text-white font-semibold rounded-lg hover:bg-gray-900 dark:hover:bg-[#0ea572] transition-colors shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All Filters
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Custom pagination styling */
    .pagination {
        display: flex;
        gap: 0.5rem;
    }
    
    .pagination a,
    .pagination span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .pagination a {
        color: #6b7280;
        background: white;
        border: 1px solid #e5e7eb;
    }
    
    .pagination a:hover {
        color: #10B981;
        background: #f0fdf4;
        border-color: #10B981;
    }
    
    .pagination span {
        color: white;
        background: #10B981;
        border: 1px solid #10B981;
    }
    
    .dark .pagination a {
        background: #111827;
        border-color: #374151;
        color: #9ca3af;
    }
    
    .dark .pagination a:hover {
        color: #10B981;
        background: #064e3b;
        border-color: #10B981;
    }
    
    .dark .pagination span {
        background: #10B981;
        border-color: #10B981;
    }
</style>
@endsection
