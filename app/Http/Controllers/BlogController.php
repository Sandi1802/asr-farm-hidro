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
        // Check if this session has already liked this post
        if (session()->has('liked_post_' . $id)) {
            $post = Post::findOrFail($id);
            return response()->json(['success' => false, 'likes' => $post->likes, 'message' => 'Already liked']);
        }

        $post = Post::findOrFail($id);
        $post->increment('likes');
        
        // Save to session so they can't like again
        session()->put('liked_post_' . $id, true);

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
