<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // Store avatar
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request
                ->file('avatar')
                ->store('avatars', 'public');
        }

        // Never allow public registration to choose admin role
        $data['role'] = User::ROLE_USER;

        // Password will be hashed by User model cast
        $user = User::create($data);

        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User created successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        
        $login = trim($request->login);
        $password = $request->password;

        // Determine email or phone
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $login)->first();
        } else {
            $user = User::where('phone', $login)->first();
        }

        // User not found
        if (!$user) {
            return response()->json([
                'message' => 'ইমেইল/ফোন অথবা পাসওয়ার্ড সঠিক নয়।',
            ], 401);
        }

        // Password missing
        if (!$user->password) {
            return response()->json([
                'message' => 'এই অ্যাকাউন্টে পাসওয়ার্ড দিয়ে লগইন করা যাবে না।',
            ], 401);
        }

        // Check password
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'ইমেইল/ফোন অথবা পাসওয়ার্ড সঠিক নয়।',
            ], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}