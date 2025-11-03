<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Validation\ValidationException;
use Illuminate\Cache\RateLimiter;

use App\Http\Controllers\StallRentalController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\VolanteRentalController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\UserController;

class AccountsController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_id, [1, 2, 3])) {
            abort(403, 'Unauthorized action.');
        }

        $stallRentalController = new StallRentalController();
        $tablesController = new TablesController();
        $businessController = new BusinessController();
        $volanteRentalController = new VolanteRentalController();
        $permitController = new PermitController();

        return Inertia::render('Admin/Dashboard', [
            'occupiedStallRentalPerCategory' => $stallRentalController->countAllPerCategoryAndStall(),
            'businessPermitMonthlyCounts' => $businessController->countMonthlyApplicants(),
            'stallPermitMonthlyCounts' => $stallRentalController->countMonthlyApplicants(),
            'volantePermitMonthlyCounts' => $volanteRentalController->countMonthlyApplicants(),
            'tablePermitMonthlyCounts' => $tablesController->countMonthlyApplicants(),
            'todayTableCount' => $tablesController->countTodayApplicants(),
            'todayStallCount' => $stallRentalController->countTodayApplicants(),
            'todayVolanteCount' => $volanteRentalController->countTodayApplicants(),
            'permitStatusCounts' => $permitController->countPerStatus(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = auth()->user();
        // Set session department
        if ($user->role_id === 1) {
            $redirectUrl = '/admin/dashboard';
        } elseif ($user->role_id === 3) {
            $redirectUrl = '/my-rentals/stall-leasing';
        } elseif ($user->role_id === 2) {
            $redirectUrl = '/department/dashboard';
        } else {
            abort(404, 'Not Found');
        }

        return redirect()->intended($redirectUrl);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        return redirect('/login');
    }
}