@extends('layouts.admin')

@section('title', 'News Management')
@section('page-title', 'News Management')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h3 class="text-lg font-semibold text-black dark:text-white">All News</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your news articles</p>
    </div>
    <a href="{{ route('admin.news.create') }}" class="px-4 py-2 bg-emerald-custom text-white font-semibold rounded-lg hover:bg-[#0ea572] transition-colors">
        + Add New News
    </a>
</div>

<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($news as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($item->image)
                                    <div class="flex-shrink-0 h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mr-3">
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="h-12 w-12 object-cover">
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-black dark:text-white">{{ Str::limit($item->title, 50) }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit(strip_tags($item->content), 60) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $item->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $item->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300' }}">
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.news.edit', $item) }}" class="text-emerald-custom hover:text-[#0ea572]">Edit</a>
                                <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this news?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No news found. <a href="{{ route('admin.news.create') }}" class="text-emerald-custom hover:underline">Create one</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($news->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
            {{ $news->links() }}
        </div>
    @endif
</div>
@endsection

