<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class CheckUserRole
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        \Log::info('CheckUserRole middleware triggered.', [
            'roles' => $roles,
            'user_id' => $user ? $user : null,
            'roles_checked' => in_array($user->role_id, $roles),
            'user_role_id' => $user ? $user->role_id : null,
            'request_url' => $request->fullUrl(),
        ]);
        // Check if logged in and has allowed role
        if (!$user || !in_array($user->role_id, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
