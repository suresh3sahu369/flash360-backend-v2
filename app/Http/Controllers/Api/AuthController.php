<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // 1. REGISTER USER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // 2. LOGIN USER
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Incorrect credentials provided.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // 3. GET USER PROFILE
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    // 4. DASHBOARD STATS (UPDATED: Ab Recent Activity khali nahi jayegi!)
    public function dashboardStats(Request $request)
    {
        $user = $request->user();

        // 1. Total Saved Count
        $savedCount = DB::table('bookmarks')->where('user_id', $user->id)->count();

        // 2. Recent 3 Saved News nikalo (Ye Naya Code Hai 👇)
        $recentActivity = DB::table('bookmarks')
            ->join('news', 'bookmarks.news_id', '=', 'news.id')
            ->leftJoin('categories', 'news.category_id', '=', 'categories.id')
            ->where('bookmarks.user_id', $user->id)
            ->select(
                'news.title', 
                'news.slug', 
                'news.image', 
                'bookmarks.created_at',
                'categories.name as category_name'
            )
            ->orderBy('bookmarks.created_at', 'desc')
            ->limit(3) // Sirf top 3 dikhayenge
            ->get();

        return response()->json([
            'status' => 'Active',
            'saved_stories' => $savedCount,
            'comments' => 0,
            'recent_activity' => $recentActivity // 👈 Ab yahan asli data jayega
        ]);
    }

    // 5. UPDATE PROFILE
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user
        ]);
    }

    // 6. GET SAVED NEWS (Bookmarks List)
    public function getBookmarks(Request $request)
    {
        $user = $request->user();

        $savedNews = DB::table('bookmarks')
            ->join('news', 'bookmarks.news_id', '=', 'news.id')
            ->leftJoin('categories', 'news.category_id', '=', 'categories.id')
            ->where('bookmarks.user_id', $user->id)
            ->select(
                'news.id', 
                'news.title', 
                'news.slug', 
                'news.image', 
                'news.created_at', 
                'categories.name as category_name'
            )
            ->orderBy('bookmarks.created_at', 'desc')
            ->get();

        return response()->json($savedNews);
    }

    // 7. TOGGLE BOOKMARK (Save/Unsave)
    public function toggleBookmark(Request $request)
    {
        $user = $request->user();
        
        $request->validate(['news_id' => 'required|exists:news,id']);

        $exists = DB::table('bookmarks')
            ->where('user_id', $user->id)
            ->where('news_id', $request->news_id)
            ->first();

        if ($exists) {
            DB::table('bookmarks')->where('id', $exists->id)->delete();
            return response()->json(['message' => 'Removed', 'status' => 'removed']);
        } else {
            DB::table('bookmarks')->insert([
                'user_id' => $user->id,
                'news_id' => $request->news_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'Saved', 'status' => 'added']);
        }
    }

    // 8. LOGOUT USER
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}