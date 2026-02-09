<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Product;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Menampilkan halaman utama dengan produk dan berita dari database.
     */
    public function index(): View
    {
        $products = Product::active()
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $news = News::published()
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return view('welcome', [
            'products' => $products,
            'news' => $news,
        ]);
    }
}
