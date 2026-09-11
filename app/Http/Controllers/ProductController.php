<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Setting;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::latest();
        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }
        $products = $query->paginate(8);
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('products.index', compact('products', 'settings'));
    }
}
