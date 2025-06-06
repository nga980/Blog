<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return view('welcome');
});

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
    return redirect()->route('user.home');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/user/home', [UserHomeController::class, 'index'])->name('user.home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Only allow normal users to view posts index
    Route::get('/posts', [PostController::class, 'userIndex'])->name('posts.index');

    // Add post detail route for normal users
    Route::get('/posts/{post}', [PostController::class, 'userShow'])->name('posts.show');

    // Add categories index route for normal users
    Route::get('/categories', [CategoryController::class, 'userIndex'])->name('categories.index');

    // Add category detail route for normal users
    Route::get('/categories/{category}', [CategoryController::class, 'userShow'])->name('categories.show');
});

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/export-posts', [AdminDashboardController::class, 'exportPosts'])->name('admin.dashboard.exportPosts');

    // Admin posts resource routes
    Route::resource('posts', PostController::class, ['as' => 'admin']);

    // Admin categories resource routes
    Route::resource('categories', CategoryController::class, ['as' => 'admin']);
});

require __DIR__.'/auth.php';
