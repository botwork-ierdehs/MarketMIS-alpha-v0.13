<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ValidateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        Log::info('Login attempt', [
            'route_name' => $request
        ]);
        // Get user ID from route parameter (e.g., /users/{user})
        $userId = $request->route('user');
        if (!$userId || !User::find($userId)) {
            return redirect()->back()->withErrors(['user' => 'User not found.']);
        }
        return $next($request);
    }
}
