<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Roles that a given role also satisfies (role hierarchy).
     *
     * @var array<string, array<string>>
     */
    protected array $hierarchy = [
        User::ROLE_SUPER_ADMIN => [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_USER],
        User::ROLE_ADMIN => [User::ROLE_ADMIN, User::ROLE_USER],
        User::ROLE_USER => [User::ROLE_USER],
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'এই কাজের অনুমতি আপনার নেই।'], 403);
        }

        $effectiveRoles = $this->hierarchy[$user->role] ?? [$user->role];

        if (empty(array_intersect($effectiveRoles, $roles))) {
            return response()->json(['message' => 'এই কাজের অনুমতি আপনার নেই।'], 403);
        }

        return $next($request);
    }
}
