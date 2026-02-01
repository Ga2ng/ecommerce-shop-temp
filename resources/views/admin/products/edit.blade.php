@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-2xl">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price & Stock -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">Rp</span>
                            <input type="text" name="price_display" id="price_display" value="{{ old('price', $product->price) ? 'Rp ' . number_format(old('price', $product->price), 0, ',', '.') : 'Rp ' . number_format($product->price, 0, ',', '.') }}" placeholder="Rp 0" class="w-full pl-12 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent" autocomplete="off">
                            <input type="hidden" name="price" id="price" value="{{ old('price', $product->price) }}">
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: Rp 100.000</p>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock *</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <input type="text" name="category" id="category" value="{{ old('category', $product->category) }}" list="categories" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                    <datalist id="categories">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image -->
                @if($product->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Image</label>
                        <div class="flex justify-center">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-800">
                        </div>
                    </div>
                @endif

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Image {{ $product->image ? '(Leave empty to keep current)' : '' }}</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum file size: 10MB</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 text-emerald-custom border-gray-300 rounded focus:ring-emerald-custom">
                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Product is active</label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-emerald-custom text-white font-semibold rounded-lg hover:bg-[#0ea572] transition-colors">
                        Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceDisplay = document.getElementById('price_display');
    const priceInput = document.getElementById('price');

    // Format Rupiah saat input
    priceDisplay.addEventListener('input', function(e) {
        let value = e.target.value;
        // Hapus semua karakter selain angka
        value = value.replace(/[^\d]/g, '');
        
        if (value === '') {
            priceDisplay.value = '';
            priceInput.value = '';
            return;
        }

        // Format dengan titik sebagai pemisah ribuan
        const formatted = 'Rp ' + parseInt(value).toLocaleString('id-ID');
        priceDisplay.value = formatted;
        priceInput.value = value;
    });

    // Handle paste
    priceDisplay.addEventListener('paste', function(e) {
        e.preventDefault();
        let paste = (e.clipboardData || window.clipboardData).getData('text');
        paste = paste.replace(/[^\d]/g, '');
        if (paste) {
            priceDisplay.value = 'Rp ' + parseInt(paste).toLocaleString('id-ID');
            priceInput.value = paste;
        }
    });

    // Format saat form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!priceInput.value || priceInput.value === '0') {
            e.preventDefault();
            alert('Price is required');
            return false;
        }
    });
});
</script>
@endsection
