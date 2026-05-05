<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display the storefront home page with products.
     */
    public function index()
    {
        // Get products that have stock, ordered by newest
        $products = Product::where('stock', '>', 0)->latest()->paginate(12);
        
        return view('home', compact('products'));
    }

    /**
     * Display the product details page.
     */
    public function show(Product $product)
    {
        return view('store.show', compact('product'));
    }
}
