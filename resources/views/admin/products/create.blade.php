@extends('layouts.admin')

@section('title', 'Create Product')
@section('page-title', 'Create New Product')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
        @csrf

        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price & Stock -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price *</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock *</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <input type="text" name="category" id="category" value="{{ old('category') }}" list="categories" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                <datalist id="categories">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
                @error('category')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Image</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum file size: 10MB</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-emerald-custom border-gray-300 rounded focus:ring-emerald-custom">
                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Product is active</label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-custom text-white font-semibold rounded-lg hover:bg-[#0ea572] transition-colors">
                    Create Product
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

