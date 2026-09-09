<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect user to Google/Facebook.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver($provider);

        return $driver->stateless()->redirect();
    }

    /**
     * Handle Google/Facebook callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $socialUser = $driver->stateless()->user();

            $providerId = $socialUser->getId();
            $email = $socialUser->getEmail();

            /*
            |--------------------------------------------------------------------------
            | Find existing user
            |--------------------------------------------------------------------------
            */
            $user = User::where(function ($query) use ($provider, $providerId, $email) {
                $query->where(function ($q) use ($provider, $providerId) {
                    $q->where('provider', $provider)
                      ->where('provider_id', $providerId);
                });

                if ($email) {
                    $query->orWhere('email', $email);
                }
            })->first();

            /*
            |--------------------------------------------------------------------------
            | Create or Update User
            |--------------------------------------------------------------------------
            */
            if (! $user) {
                $user = User::create([
                    'name' => $socialUser->getName()
                        ?? $socialUser->getNickname()
                        ?? 'User',
                    'email' => $email,
                    'avatar' => $socialUser->getAvatar(),
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'password' => null,
                    'role' => User::ROLE_USER,
                ]);
            } else {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'avatar' => $user->avatar ?: $socialUser->getAvatar(),
                ]);
            }



            /*
            |--------------------------------------------------------------------------
            | Create Sanctum Token & Redirect
            |--------------------------------------------------------------------------
            */
            $token = $user->createToken('auth_token')->plainTextToken;
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            $userData = urlencode(json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->role,
            ]));

            return redirect()->away("{$frontendUrl}/auth/callback?token={$token}&user={$userData}");

        } catch (Exception $e) {
            report($e);

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            return redirect()->away("{$frontendUrl}/login?error=social_auth_failed");
        }
    }

    /**
     * Validate supported providers.
     */
    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, ['google', 'facebook'], true)) {
            abort(404);
        }
    }
}

