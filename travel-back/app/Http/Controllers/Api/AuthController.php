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
        
        $login = trim($request->input('login'));
        $password = $request->input('password');

        // Determine if login input is email or phone number
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $user = User::where('email', $login)->first();
        } else {
            // Strip non-numeric characters for phone search (e.g. "01712-345678" -> "01712345678")
            $cleanedPhone = preg_replace('/[^0-9]/', '', $login);
            
            // Handle optional country code (+88017... -> 017...)
            if (str_starts_with($cleanedPhone, '880')) {
                $cleanedPhone = substr($cleanedPhone, 2);
            }

            $user = User::where('phone', $cleanedPhone)->first();
        }

        // Check password
        if (!$user || !$user->password || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email/phone or password.',
            ], 401);
        }

        // Revoke old tokens & create new session token
        $user->tokens()->delete();
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