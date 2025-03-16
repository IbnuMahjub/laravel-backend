<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'message' => 'Login Gagal.',
                ], 401);
            }

            // $role = $user->role_user ? $user->role_user->role_name : null;
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'message' => 'Login Berhasil',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    "google_id" => $user->google_id ? $user->google_id : "",
                    'avatar' => !empty($user->avatar) && Storage::exists($user->avatar) ? Storage::url($user->avatar) : '',  // Menambahkan pengecekan avatar
                    'accses_role' => [
                        'id_role_user' => $user->id_role_user,
                        'role_name' => $user->role_user ? $user->role_user->role_name : ""
                    ]
                ],
                'token' => $token,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Register Berhasil',
                'data' =>  $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }




    public function googleLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(uniqid()),
                'google_id' => $request->google_id,
                'avatar' => $request->avatar
            ]);
        }

        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'message' => 'Login Google Berhasil',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
