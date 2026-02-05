<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PublicNewsController;
use App\Http\Controllers\Api\PublicCategoryController;

use App\Http\Controllers\Api\NewsController as UserNewsController;

use App\Http\Controllers\NewsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================
// PUBLIC ROUTES
// ==========================

Route::get('/test', fn () => response()->json(['message' => 'API working']));

Route::post('/contact', [ContactController::class, 'store']);
Route::post('/newsletter', [NewsletterController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/news', [PublicNewsController::class, 'index']);
Route::get('/news/{slug}', [PublicNewsController::class, 'show']);
Route::get('/categories', [PublicCategoryController::class, 'index']);

Route::get('/news-details/{slug}', [InteractionController::class, 'showNewsWithDetails']);
Route::get('/news/{id}/comments', [InteractionController::class, 'getComments']);


// ==========================
// AUTHENTICATED ROUTES
// ==========================

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/dashboard-stats', [AuthController::class, 'dashboardStats']);
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);

    Route::get('/bookmarks', [AuthController::class, 'getBookmarks']);
    Route::post('/bookmark/toggle', [AuthController::class, 'toggleBookmark']);

    Route::post('/news/{id}/like', [InteractionController::class, 'toggleLike']);
    Route::post('/news/{id}/comment', [InteractionController::class, 'storeComment']);
    Route::post('/author/{id}/subscribe', [InteractionController::class, 'toggleSubscribe']);

    Route::post('/news/store', [UserNewsController::class, 'store']);

    Route::put('/profile', [ProfileController::class, 'update']);

    // ==========================
    // CREATOR
    // ==========================

    Route::middleware('creator')->group(function () {

        Route::get('/creator/news', [NewsController::class, 'myNews']);
        Route::post('/creator/news', [NewsController::class, 'store']);
        Route::put('/creator/news/{id}', [NewsController::class, 'update']);
        Route::delete('/creator/news/{id}', [NewsController::class, 'destroy']);

    });

    // ==========================
    // ADMIN
    // ==========================

    Route::middleware('admin')->group(function () {

        Route::apiResource('users', UserController::class);
        Route::apiResource('categories', CategoryController::class);

        Route::get('/admin/comments', [InteractionController::class, 'getAllComments']);
        Route::delete('/admin/comments/{id}', [InteractionController::class, 'deleteComment']);

        Route::get('/admin/news', [NewsController::class, 'adminIndex']);
        Route::put('/admin/news/{id}/approve', [NewsController::class, 'approve']);

    });

});
