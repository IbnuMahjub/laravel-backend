<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function profile()
    {
        try {
            $profile = User::find(auth()->user()->id);
            return response()->json([
                'data' => $profile
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(Request $request)
    {
        try {
            $profile = User::find(auth()->user()->id);

            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                if ($profile->avatar) {
                    Storage::delete($profile->avatar);
                }

                $avatarName = 'avatar-' . auth()->user()->id . '.' . $request->file('avatar')->getClientOriginalExtension();

                $validated['avatar'] = $request->file('avatar')->storeAs('avatar-images', $avatarName);
            }

            $profile->update($validated);

            $profileUrl = $profile->avatar ? asset('storage/' . $profile->avatar) : null;

            return response()->json([
                'status' => 'success updated',
                'data' => [
                    'name' => $profile->name,
                    'username' => $profile->username,
                    'email' => $profile->email,
                    'avatar' => $profileUrl
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }
}
