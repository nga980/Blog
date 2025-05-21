<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

Route::get('/dashboard', function () {
    $user = Auth::user();
    $perPage = 10; // default perPage for DataTables page length
    $isAdmin = $user ? $user->isAdmin() : false;
    if ($isAdmin) {
        $userCount = User::count();
        $postCount = Post::count();

        $latestPost = Post::orderBy('created_at', 'desc')->first();
        $latestPostDate = $latestPost ? $latestPost->created_at : null;

        $userGrowth = User::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $postGrowth = Post::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('admin.dashboard', compact(
            'userCount', 'postCount',
            'latestPost', 'latestPostDate',
            'userGrowth', 'postGrowth', 'perPage', 'isAdmin'
        ));
    }
    return redirect()->route('posts.index');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Allow normal users to create posts
    Route::get('/posts/create', [App\Http\Controllers\PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'])->name('posts.store');

    // Add posts index route for normal users
    Route::get('/posts', [App\Http\Controllers\PostController::class, 'userIndex'])->name('posts.index');
});

use App\Http\Controllers\AdminDashboardController;

use App\Http\Controllers\PostController;

use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/export-posts', [AdminDashboardController::class, 'exportPosts'])->name('admin.dashboard.exportPosts');

    // Add admin posts resource routes
    Route::resource('posts', PostController::class, ['as' => 'admin']);
});

// Move posts resource routes outside admin middleware group to allow both normal users and admins access
Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class);
});

require __DIR__.'/auth.php';

use App\Http\Controllers\CategoryController;

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::resource('categories', CategoryController::class, ['as' => 'admin']);
});

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class);
});
