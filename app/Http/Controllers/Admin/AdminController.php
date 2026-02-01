<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_news' => News::count(),
            'published_news' => News::where('is_published', true)->count(),
            'low_stock_products' => Product::where('stock', '<=', 10)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
        ];

        $recent_products = Product::latest()->take(5)->get();
        $recent_news = News::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_products', 'recent_news'));
    }
}
