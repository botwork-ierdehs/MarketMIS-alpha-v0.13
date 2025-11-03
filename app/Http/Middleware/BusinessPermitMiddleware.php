<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Business;

class BusinessPermitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $businessName = $request->input('name'); // Adjust key as needed

        if (!$businessName) {
            // return redirect()->back()->withErrors(['error' => 'Business name is required']);
            return response()->json(['error' => 'Business name is required'], 400);
        }

        $query = Business::where('name', $businessName);

        // Exclude the current business if updating
        if ($request->route('business')) {
            $query->where('id', '!=', $request->route('business')->id);
        }

        if ($query->exists()) {
            // return redirect()->back()->withErrors(['error' => 'Business name already exists']);
            return response()->json(['error' => 'Business name already exists'], 409);
        }

        return $next($request);
    }
}
