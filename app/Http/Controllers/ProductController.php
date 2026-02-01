<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View
    {
        $query = Product::active();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['name', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get unique categories for filter dropdown
        $categories = Product::active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        // Paginate results
        $products = $query->paginate(12)->withQueryString();

        return view('catalog.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'min_price', 'max_price', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        // Only show active products
        if (!$product->is_active) {
            abort(404);
        }

        // Get related products (same category, excluding current product)
        $relatedProducts = Product::active()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('catalog.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}

