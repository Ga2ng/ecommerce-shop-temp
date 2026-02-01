@extends('layouts.catalog')

@section('title', $product->name)

@section('content')
<div class="min-h-screen bg-white dark:bg-black">
    <!-- Elegant Breadcrumb -->
    <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="/" class="text-gray-500 dark:text-gray-500 hover:text-emerald-custom transition-colors">Home</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('catalog.index') }}" class="text-gray-500 dark:text-gray-500 hover:text-emerald-custom transition-colors">Catalog</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-black dark:text-white font-medium">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Product Detail Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
            <!-- Product Image -->
            <div class="relative">
                <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl overflow-hidden shadow-xl border border-gray-200 dark:border-gray-800">
                    @if($product->image)
                        <img 
                            src="{{ asset('storage/' . $product->image) }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <div class="w-48 h-48 bg-emerald-custom/10 rounded-full flex items-center justify-center">
                                <svg class="w-24 h-24 text-emerald-custom/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Decorative accent -->
                <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-emerald-custom/5 rounded-full blur-3xl -z-10"></div>
            </div>

            <!-- Product Info -->
            <div class="lg:pl-8">
                <!-- Category Badge -->
                @if($product->category)
                    <div class="mb-4">
                        <span class="inline-block px-4 py-2 text-sm font-semibold bg-emerald-custom/10 text-emerald-custom rounded-full">
                            {{ $product->category }}
                        </span>
                    </div>
                @endif

                <!-- Product Name -->
                <h1 class="text-4xl md:text-5xl font-bold text-black dark:text-white mb-6 leading-tight">
                    {{ $product->name }}
                </h1>

                <!-- Price -->
                <div class="mb-8">
                    <div class="flex items-baseline gap-3">
                        <span class="text-4xl font-bold text-emerald-custom">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="mb-8">
                    @if($product->stock > 0)
                        <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-green-800 dark:text-green-300">In Stock</p>
                                <p class="text-sm text-green-600 dark:text-green-400">{{ $product->stock }} units available</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-red-800 dark:text-red-300">Out of Stock</p>
                                <p class="text-sm text-red-600 dark:text-red-400">This product is currently unavailable</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                @if($product->description)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-black dark:text-white mb-4">Description</h2>
                        <div class="prose prose-gray dark:prose-invert max-w-none">
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ $product->description }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    @if($product->stock > 0)
                        <button class="flex-1 px-8 py-4 bg-emerald-custom text-white font-bold rounded-xl hover:bg-[#0ea572] transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    @else
                        <button disabled class="flex-1 px-8 py-4 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-bold rounded-xl cursor-not-allowed">
                            Out of Stock
                        </button>
                    @endif
                    <button class="px-8 py-4 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:border-emerald-custom hover:text-emerald-custom transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        Wishlist
                    </button>
                </div>

                <!-- Product Details Card -->
                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-black dark:text-white mb-6">Product Details</h3>
                    <dl class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-800">
                            <dt class="text-gray-600 dark:text-gray-400 font-medium">SKU</dt>
                            <dd class="text-black dark:text-white font-semibold">#{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                        @if($product->category)
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-800">
                                <dt class="text-gray-600 dark:text-gray-400 font-medium">Category</dt>
                                <dd class="text-black dark:text-white font-semibold">{{ $product->category }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between items-center py-3">
                            <dt class="text-gray-600 dark:text-gray-400 font-medium">Stock</dt>
                            <dd class="text-black dark:text-white font-semibold">{{ $product->stock }} units</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        @if($relatedProducts->count() > 0)
            <div class="mt-20 pt-16 border-t border-gray-200 dark:border-gray-800">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-black dark:text-white mb-3">Related Products</h2>
                    <p class="text-gray-600 dark:text-gray-400">You might also like these products</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="group bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-800 hover:border-emerald-custom/50">
                            <a href="{{ route('catalog.show', $relatedProduct->slug) }}" class="block aspect-square bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 relative overflow-hidden">
                                @if($relatedProduct->image)
                                    <img 
                                        src="{{ asset('storage/' . $relatedProduct->image) }}" 
                                        alt="{{ $relatedProduct->name }}" 
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="w-24 h-24 bg-emerald-custom/10 rounded-full"></div>
                                    </div>
                                @endif
                            </a>
                            <div class="p-4">
                                <a href="{{ route('catalog.show', $relatedProduct->slug) }}">
                                    <h3 class="font-bold text-black dark:text-white mb-2 hover:text-emerald-custom transition-colors line-clamp-2">
                                        {{ $relatedProduct->name }}
                                    </h3>
                                </a>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-xl font-bold text-emerald-custom">
                                        Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                                    </span>
                                    <a href="{{ route('catalog.show', $relatedProduct->slug) }}" class="px-4 py-2 bg-black dark:bg-emerald-custom text-white text-sm font-semibold rounded-lg hover:bg-gray-900 dark:hover:bg-[#0ea572] transition-colors">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
