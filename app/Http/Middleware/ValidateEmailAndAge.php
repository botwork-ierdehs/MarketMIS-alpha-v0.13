<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

use App\Models\User;


class ValidateEmailAndAge
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::id();
         Log::info('Login attempt', [
            'route_name' => $user
        ]);
        // Check if email already exists
        $email = $request->input('email');
        if ($email && User::where('email', $email)
            ->where('id', '!=', $user)->exists()) {
            return redirect()->back()->withErrors(['email' => 'Email already exists.']);

        }

        // Check if user is at least 18 years old
        $birthday = $request->input('birthday');
        if ($birthday) {
            $age = Carbon::parse($birthday)->age;
            if ($age < 18) {
                return redirect()->back()->withErrors(['birthday' => 'You must be at least 18 years old.']);
            }
        }

        return $next($request);
    }
}