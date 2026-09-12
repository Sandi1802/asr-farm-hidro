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
        $post = Post::with('comments')->findOrFail($id);
        
        // Increment views
        $post->increment('views');

        $settings = Setting::pluck('value', 'key')->toArray();
        return view('blog.show', compact('post', 'settings'));
    }

    public function like($id)
    {
        $post = Post::findOrFail($id);
        $post->increment('likes');
        return response()->json(['success' => true, 'likes' => $post->likes]);
    }

    public function share($id)
    {
        $post = Post::findOrFail($id);
        $post->increment('shares');
        return response()->json(['success' => true, 'shares' => $post->shares]);
    }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $post = Post::findOrFail($id);
        $post->comments()->create([
            'name' => $request->name,
            'content' => $request->content
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }
}
