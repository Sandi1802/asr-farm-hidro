<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Setting;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('products.index', compact('products', 'settings'));
    }
}
