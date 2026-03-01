<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('posts', [App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
    Route::get('posts/create', [App\Http\Controllers\PostController::class, 'create'])->name('posts.create');
    Route::get('posts/analytics', [App\Http\Controllers\PostController::class, 'analytics'])->name('posts.analytics');
    Route::get('posts/{id}', [App\Http\Controllers\PostController::class, 'show'])->name('posts.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
