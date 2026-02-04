<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:creator,user',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Create profile
        Profile::create([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user->load('profile'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user->load('profile'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(
            $request->user()->load('profile')
        );
    }

    // 8. TOGGLE BOOKMARK (Save ya Unsave karo)
    public function toggleBookmark(Request $request)
    {
        $user = $request->user();
        
        // Validate karo ki news ID aayi hai ya nahi
        $request->validate(['news_id' => 'required|exists:news,id']);

        // Check karo ki pehle se saved hai kya?
        $exists = DB::table('bookmarks')
            ->where('user_id', $user->id)
            ->where('news_id', $request->news_id)
            ->first();

        if ($exists) {
            // Agar saved hai, toh delete karo (Unsave)
            DB::table('bookmarks')->where('id', $exists->id)->delete();
            return response()->json(['message' => 'Removed from Saved Stories', 'status' => 'removed']);
        } else {
            // Agar nahi hai, toh save karo
            DB::table('bookmarks')->insert([
                'user_id' => $user->id,
                'news_id' => $request->news_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'Added to Saved Stories', 'status' => 'added']);
        }
    }
}
