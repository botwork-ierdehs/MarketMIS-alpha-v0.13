<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ValidateVolanteDates
{
    /**
     * Handle an incoming request.
     */
   public function handle(Request $request, Closure $next)
{
    $today = Carbon::today();
    $startedDate = Carbon::parse($request->input('started_date'));
    $expireDate = $request->input('end_date') 
        ? Carbon::parse($request->input('end_date')) 
        : null;

    // 1. Started date must be at least 10 days ahead
    if ($startedDate->lt($today->copy()->addDays(10))) {
        return redirect()->back()->withErrors([
            'started_date' => 'The start date must be at least 10 days from today.'
        ]);
    }

    // 2. Cannot be later than expire date (if provided)
    if ($expireDate && $startedDate->gt($expireDate)) {
        return redirect()->back()->withErrors([
            'started_date' => 'The start date cannot be later than the expire date.'
        ]);
    }

    // 3. Expire date cannot be Dec 31 of current year OR any date in January of next year
    if ($expireDate) {
        $dec31CurrentYear = Carbon::createFromDate($today->year, 12, 31)->endOfDay();
        $janStartNextYear = Carbon::createFromDate($today->year + 1, 1, 1)->startOfDay();
        $janEndNextYear   = Carbon::createFromDate($today->year + 1, 1, 31)->endOfDay();

        if (
            $expireDate->equalTo($dec31CurrentYear) ||
            $expireDate->between($janStartNextYear, $janEndNextYear)
        ) {
            return redirect()->back()->withErrors([
                'end_date' => 'The expire date cannot be December 31 of this year or any date in January of next year.'
            ]);
        }
    }

    return $next($request);
}

}
