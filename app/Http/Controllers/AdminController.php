<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(\Illuminate\Http\Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('admin');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    public function dashboard()
    {
        $productCount = Product::count();
        $postCount = Post::count();
        return view('admin.dashboard', compact('productCount', 'postCount'));
    }

    public function products()
    {
        $products = Product::latest()->get();
        return view('admin.products', compact('products'));
    }

    public function storeProduct(\Illuminate\Http\Request $request)
    {
        $imageUrl = $request->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('uploads', 'public');
            $imageUrl = '/storage/' . $path;
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imageUrl,
            'category' => $request->category,
            'tags' => $request->tags,
            'sale' => $request->has('sale')
        ]);
        return back();
    }

    public function destroyProduct($id)
    {
        Product::findOrFail($id)->delete();
        return back();
    }

    public function blog()
    {
        $posts = Post::latest()->get();
        return view('admin.blog', compact('posts'));
    }

    public function storePost(\Illuminate\Http\Request $request)
    {
        $imageUrl = $request->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('uploads', 'public');
            $imageUrl = '/storage/' . $path;
        }

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imageUrl
        ]);
        return back();
    }

    public function destroyPost($id)
    {
        Post::findOrFail($id)->delete();
        return back();
    }
    
    // --- Edit/Update Products ---
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products_edit', compact('product'));
    }
    public function updateProduct(\Illuminate\Http\Request $request, $id)
    {
        $imageUrl = $request->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('uploads', 'public');
            $imageUrl = '/storage/' . $path;
        }

        Product::findOrFail($id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imageUrl,
            'category' => $request->category,
            'tags' => $request->tags,
            'sale' => $request->has('sale')
        ]);
        return redirect('/admin/products')->with('success', 'Product updated!');
    }
    
    // --- Edit/Update Blog ---
    public function editPost($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.blog_edit', compact('post'));
    }
    public function updatePost(\Illuminate\Http\Request $request, $id)
    {
        $imageUrl = $request->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('uploads', 'public');
            $imageUrl = '/storage/' . $path;
        }

        Post::findOrFail($id)->update([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imageUrl
        ]);
        return redirect('/admin/blog')->with('success', 'Post updated!');
    }

    // --- Settings ---
    public function settings()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('admin.settings', compact('settings'));
    }
    public function updateSettings(\Illuminate\Http\Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'Settings updated successfully!');
    }
    
    // --- Testimonials ---
    public function testimonials()
    {
        $testimonials = \App\Models\Testimonial::latest()->get();
        return view('admin.testimonials', compact('testimonials'));
    }
    public function storeTestimonial(\Illuminate\Http\Request $request)
    {
        \App\Models\Testimonial::create([
            'name' => $request->name,
            'content' => $request->content,
            'image' => $request->image,
            'rating' => $request->rating ?? 5
        ]);
        return back()->with('success', 'Testimonial added!');
    }
    public function destroyTestimonial($id)
    {
        \App\Models\Testimonial::findOrFail($id)->delete();
        return back()->with('success', 'Testimonial deleted!');
    }
}
