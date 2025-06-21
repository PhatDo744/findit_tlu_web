<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get current user profile
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('role');
        
        return response()->json([
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'photo_url' => $user->photo_url,
            'created_at' => $user->created_at,
            'role' => [
                'id' => $user->role->id,
                'name' => $user->role->name
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20'
        ]);
        
        $user->update($validated);
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'photo_url' => $user->photo_url
            ]
        ]);
    }

    /**
     * Update user avatar
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'avatar' => 'required|image|max:2048' // 2MB max
        ]);
        
        // Delete old avatar if exists
        if ($user->photo_url) {
            \Storage::disk('public')->delete($user->photo_url);
        }
        
        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['photo_url' => $path]);
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'photo_url' => $user->photo_url
            ]
        ]);
    }
}
