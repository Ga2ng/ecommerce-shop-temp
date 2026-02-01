@extends('layouts.admin')

@section('title', 'Products Management')
@section('page-title', 'Products Management')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h3 class="text-lg font-semibold text-black dark:text-white">All Products</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your product catalog</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-emerald-custom text-white font-semibold rounded-lg hover:bg-[#0ea572] transition-colors">
        + Add New Product
    </a>
</div>

<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mr-3">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 object-cover rounded-lg">
                                    @else
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-black dark:text-white">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 40) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full">
                                {{ $product->category ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-custom">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-black dark:text-white">{{ $product->stock }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-emerald-custom hover:text-[#0ea572]">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No products found. <a href="{{ route('admin.products.create') }}" class="text-emerald-custom hover:underline">Create one</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

