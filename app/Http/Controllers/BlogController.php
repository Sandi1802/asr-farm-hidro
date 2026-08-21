<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Setting;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('blog.index', compact('posts', 'settings'));
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('blog.show', compact('post', 'settings'));
    }
}
