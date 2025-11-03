<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
         Log::info('Login attempt', [
            'route_name' => $user
        ]);
        return Inertia::render('Admin/Department/Index', [
            'departments' => Department::query()
            ->filterByDepartmentID($user->department_id, $user->role_id)
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($dep) => [
                'id' => $dep->id,
                'name' => $dep->name,
                'admins' => $dep->admins ? [
                    'id' => $dep->admins->id,
                    'code' => $dep->admins->name,
                    'users' => $dep->admins->users ? $dep->admins->users->map(fn ($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]) : [],
                ] : null,
            ]),
        ]);
    }

    public function apiIndex()
    {
         $user = Auth::user();
         Log::info('Login attempt', [
            'route_name' => $user
        ]);
        return response()->json([
            'departments' => Department::query()->filterByDepartmentID($user ? $user->department_id : null,
                $user ? $user->role_id : 1)->get(),
        ]);
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);
        return Inertia::render('Admin/Department/Show', [
            'department' => $department,
        ]);
    }
}
