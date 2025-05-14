<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes group
use App\Http\Controllers\AdminDashboardController;

use App\Http\Controllers\PostController;

use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('posts', PostController::class);
    Route::get('/dashboard/export-posts', [AdminDashboardController::class, 'exportPosts'])->name('admin.dashboard.exportPosts');
});

require __DIR__.'/auth.php';
