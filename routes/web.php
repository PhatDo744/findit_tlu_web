<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;

Route::get('/', function () {
    // Tạm thời chuyển hướng đến trang login, sau này có thể là trang chủ public
    return redirect()->route('login');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('logout', [LoginController::class, 'logout'])->name('logout.get');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management Routes
    Route::resource('users', UserController::class)->except(['show', 'edit', 'create']);
    Route::post('users/{user}/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

    // Category Management Routes
    Route::resource('categories', CategoryController::class)->except(['show', 'create']);

    // Item Management Routes
    Route::resource('items', ItemController::class)->except(['create', 'store']);
    Route::post('items/{item}/update-status', [ItemController::class, 'updateStatus'])->name('items.updateStatus');

    // Các routes khác của admin sẽ được thêm vào đây
});

// Route::get('/', function () {
//     return view('welcome');
// });