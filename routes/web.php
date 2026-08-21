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
Route::get('/products', [ProductController::class, 'index']);
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{id}', [BlogController::class, 'show']);

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
