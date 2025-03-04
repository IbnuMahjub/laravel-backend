<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $request->file('avatar')->store('avatar-images');
            }
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
