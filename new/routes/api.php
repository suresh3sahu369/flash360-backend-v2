<?php

use Illuminate\Support\Facades\Route;

// 👇 NAYA Auth Controller
use App\Http\Controllers\Api\AuthController;

// 👇 NAYA Interaction Controller (Like/Comment/Author ke liye)
use App\Http\Controllers\Api\InteractionController;

use App\Http\Controllers\Api\ContactController;

// 👇👇👇 [IMPORTANT FIX] Frontend News Controller (Alias use kiya taaki conflict na ho)
use App\Http\Controllers\Api\NewsController as UserNewsController;

// 👇 PURANE Controllers (Admin wale)
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\PublicNewsController;
use App\Http\Controllers\Api\PublicCategoryController;
use App\Http\Controllers\Api\NewsletterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================
// 1. PUBLIC ROUTES
// ==========================

Route::post('/contact', [ContactController::class, 'store']);
Route::post('/newsletter', [NewsletterController::class, 'store']);
Route::get('/test', fn () => response()->json(['message' => 'API working']));

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public News Read
Route::get('/news', [PublicNewsController::class, 'index']); 
Route::get('/news/{slug}', [PublicNewsController::class, 'show']); 
Route::get('/categories', [PublicCategoryController::class, 'index']);

// Social Features (Read Only)
Route::get('/news-details/{slug}', [InteractionController::class, 'showNewsWithDetails']);
Route::get('/news/{id}/comments', [InteractionController::class, 'getComments']);


// ==========================
// 2. PROTECTED ROUTES (Login Required)
// ==========================
Route::middleware('auth:sanctum')->group(function () {

    // --- Authentication Actions ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']); 

    // --- DASHBOARD ROUTES ---
    Route::get('/dashboard-stats', [AuthController::class, 'dashboardStats']);
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);
    
    // --- SAVED STORIES ---
    Route::get('/bookmarks', [AuthController::class, 'getBookmarks']);
    Route::post('/bookmark/toggle', [AuthController::class, 'toggleBookmark']);

    // --- SOCIAL INTERACTIONS ---
    Route::post('/news/{id}/like', [InteractionController::class, 'toggleLike']);
    Route::post('/news/{id}/comment', [InteractionController::class, 'storeComment']);
    Route::post('/author/{id}/subscribe', [InteractionController::class, 'toggleSubscribe']);

    // 👇👇👇 [YEH RAHA WO MISSING ROUTE JO 405 ERROR DE RAHA THA] 👇👇👇
    // Humne 'UserNewsController' use kiya hai jo abhi banaya tha
    Route::post('/news/store', [UserNewsController::class, 'store']);


    // --- Legacy Profile Update ---
    Route::put('/profile', [ProfileController::class, 'update']);

    // ==========================
    // CREATOR ROUTES
    // ==========================
    Route::middleware('creator')->group(function () {
        Route::get('/creator/news', [NewsController::class, 'myNews']);
        Route::post('/creator/news', [NewsController::class, 'store']);
        Route::put('/creator/news/{id}', [NewsController::class, 'update']);
        Route::delete('/creator/news/{id}', [NewsController::class, 'destroy']);
    });

    // ==========================
    // ADMIN ROUTES
    // ==========================
    Route::middleware('admin')->group(function () {
        // User Management
        Route::apiResource('users', UserController::class);

        // Category Management
        Route::apiResource('categories', CategoryController::class);

        // Comment Management
        Route::get('/admin/comments', [InteractionController::class, 'getAllComments']);
        Route::delete('/admin/comments/{id}', [InteractionController::class, 'deleteComment']);

        // News Management
        Route::get('/admin/news', [NewsController::class, 'adminIndex']);
        Route::put('/admin/news/{id}/approve', [NewsController::class, 'approve']);
    });

});