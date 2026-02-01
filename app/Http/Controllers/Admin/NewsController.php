<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display a listing of news.
     */
    public function index(): View
    {
        $news = News::latest()->paginate(15);
        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new news.
     */
    public function create(): View
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created news.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:10240',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        $validated['is_published'] = $request->has('is_published');
        
        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        News::create($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'News created successfully.');
    }

    /**
     * Show the form for editing the specified news.
     */
    public function edit(News $news): View
    {
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified news.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:10240',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            // Store new image
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        
        if ($validated['is_published'] && !$news->is_published) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'News updated successfully.');
    }

    /**
     * Remove the specified news.
     */
    public function destroy(News $news): RedirectResponse
    {
        // Delete image if exists
        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }
        
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News deleted successfully.');
    }
}
