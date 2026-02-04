<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show($userId)
    {
        $user = \App\Models\User::with(['profile', 'news' => function($q) {
            $q->where('status', 'published')->latest()->take(10);
        }])->findOrFail($userId);

        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_linkedin' => 'nullable|url',
        ]);

        if ($request->name) {
            $user->update(['name' => $request->name]);
        }

        $profileData = $request->except(['name', 'avatar']);

        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')
                ->store('avatars', 'public');
        }

        $user->profile()->update($profileData);

        return response()->json($user->load('profile'));
    }
}