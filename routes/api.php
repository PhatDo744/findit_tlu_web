<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\CategoryController;
use App\Http\Controllers\Api\User\PostController;
use App\Http\Controllers\Api\User\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Public routes
Route::get('categories', [CategoryController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::prefix('user')->group(function () {
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::post('avatar', [ProfileController::class, 'updateAvatar']);
        Route::get('items', [PostController::class, 'myPosts']);
    });
    
    // Items/Posts
    Route::get('items', [PostController::class, 'index']);
    Route::get('items/{id}', [PostController::class, 'show']);
    Route::post('items', [PostController::class, 'store']);
    Route::put('items/{id}', [PostController::class, 'update']);
    Route::delete('items/{id}', [PostController::class, 'destroy']);
    Route::put('items/{id}/complete', [PostController::class, 'markAsCompleted']);
    
    // Item images
    Route::post('items/{id}/images', [PostController::class, 'uploadImage']);
    Route::delete('item-images/{id}', [PostController::class, 'deleteImage']);
    
    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // New route for getting items by category
    Route::get('items/category/{categoryId}', [PostController::class, 'itemsByCategory']);

    // New route for getting items by type
    Route::get('items/type/{itemType}', [PostController::class, 'itemsByType']);

    // New route for getting items by category and type
    Route::get('items/category/{categoryId}/type/{itemType}', [PostController::class, 'itemsByCategoryAndType']);

    // New route for searching items
    Route::get('items/search/{keyword}', [PostController::class, 'searchItems']);
});
