<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $posts = \App\Models\Post::latest()->take(3)->get();
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('home', compact('settings', 'posts'));
    }
}
