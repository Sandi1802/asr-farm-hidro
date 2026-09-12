<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [PageController::class, 'about']);
Route::get('/testimonials', [PageController::class, 'testimonials']);
Route::get('/contact', [PageController::class, 'contact']);
Route::post('/contact', [PageController::class, 'submitContact']);
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{id}', [BlogController::class, 'show']);
Route::post('/blog/{id}/like', [BlogController::class, 'like']);
Route::post('/blog/{id}/share', [BlogController::class, 'share']);
Route::post('/blog/{id}/comment', [BlogController::class, 'comment']);
Route::get('/blog/{id}/comment', function($id) { return redirect('/blog/'.$id); });

Route::get('/admin/login', [AdminController::class, 'login'])->name('login');
Route::post('/admin/login', [AdminController::class, 'authenticate']);
Route::get('/admin/logout', [AdminController::class, 'logout']);

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings']);
    Route::post('/settings', [AdminController::class, 'updateSettings']);
    
    // Testimonials
    Route::get('/testimonials', [AdminController::class, 'testimonials']);
    Route::post('/testimonials', [AdminController::class, 'storeTestimonial']);
    Route::post('/testimonials/{id}/delete', [AdminController::class, 'destroyTestimonial']);

    // Clients
    Route::get('/clients', [AdminController::class, 'clients']);
    Route::post('/clients', [AdminController::class, 'storeClient']);
    Route::get('/clients/{id}/edit', [AdminController::class, 'editClient']);
    Route::post('/clients/{id}/edit', [AdminController::class, 'updateClient']);
    Route::post('/clients/{id}/delete', [AdminController::class, 'destroyClient']);

    // Messages
    Route::get('/messages', [AdminController::class, 'messages']);
    Route::post('/messages/{id}/read', [AdminController::class, 'markMessageRead']);
    Route::post('/messages/{id}/delete', [AdminController::class, 'destroyMessage']);
    
    // Products
    Route::get('/products', [AdminController::class, 'products']);
    Route::post('/products', [AdminController::class, 'storeProduct']);
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct']);
    Route::post('/products/{id}/update', [AdminController::class, 'updateProduct']);
    Route::post('/products/{id}/delete', [AdminController::class, 'destroyProduct']);
    
    // Blog
    Route::get('/blog', [AdminController::class, 'blog']);
    Route::post('/blog', [AdminController::class, 'storePost']);
    Route::get('/blog/{id}/edit', [AdminController::class, 'editPost']);
    Route::post('/blog/{id}/update', [AdminController::class, 'updatePost']);
    Route::post('/blog/{id}/delete', [AdminController::class, 'destroyPost']);
});
