<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/api/webhook', function () {
    return response(request('hub_challenge'));
});

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'connections' => [
            ['id' => 1, 'provider' => 'facebook', 'name' => 'Marketing Page', 'status' => 'active'],
            ['id' => 3, 'provider' => 'instagram', 'name' => 'Brand Account', 'status' => 'active'],
            ['id' => 2, 'provider' => 'twitter', 'name' => 'Tech Updates', 'status' => 'active'],
        ],
        'upcoming' => [
            ['id' => 101, 'title' => 'Monday Motivation', 'platforms' => ['instagram'], 'date' => 'Tomorrow, 09:00 AM'],
            ['id' => 102, 'title' => 'Product Feature Teaser', 'platforms' => ['twitter', 'facebook'], 'date' => 'Wed, Mar 4, 02:00 PM'],
        ],
        'latestPosts' => [
            ['id' => 1, 'title' => 'Summer Sale Kickoff', 'platforms' => ['instagram', 'facebook'], 'date' => '2h ago', 'status' => 'published'],
            ['id' => 2, 'title' => 'New Feature Reveal', 'platforms' => ['twitter', 'linkedin'], 'date' => '5h ago', 'status' => 'published'],
            ['id' => 3, 'title' => 'Community Spotlight', 'platforms' => ['facebook'], 'date' => '1d ago', 'status' => 'published'],
        ],
        'stats' => [
            'posts_this_month' => 10,
            'monthly_target' => 30,
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('posts/analytics', [PostController::class, 'analytics'])->name('posts.analytics');
    Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
