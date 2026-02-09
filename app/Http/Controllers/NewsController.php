<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Daftar berita (published).
     */
    public function index(Request $request): View
    {
        $news = News::published()
            ->orderBy('published_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        return view('news.index', compact('news'));
    }

    /**
     * Detail single berita by slug.
     */
    public function show(News $news): View
    {
        if (!$news->is_published) {
            abort(404);
        }

        $recentNews = News::published()
            ->where('id', '!=', $news->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('news.show', [
            'news' => $news,
            'recentNews' => $recentNews,
        ]);
    }
}
