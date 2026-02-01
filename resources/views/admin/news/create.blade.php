@extends('layouts.admin')

@section('title', 'Create News')
@section('page-title', 'Create New News')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-2xl">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content *</label>
                    <textarea name="content" id="content" rows="10" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Featured Image</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-custom focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum file size: 10MB</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Published -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="w-4 h-4 text-emerald-custom border-gray-300 rounded focus:ring-emerald-custom">
                    <label for="is_published" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publish immediately</label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('admin.news.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-emerald-custom text-white font-semibold rounded-lg hover:bg-[#0ea572] transition-colors">
                        Create News
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
