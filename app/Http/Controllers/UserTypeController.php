<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\UserType;

class UserTypeController extends Controller
{
    public function index(Request $request): Response
    {   
        return Inertia::render('Admin/UserType/Index', [
            'filters' => $request->all('search'),
            'userTypes' => UserType::query()
                ->filter($request->only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($dep) => [
                    'id' => $dep->id,
                    'name' => $dep->name,
                ]),
        ]);
    }

    public function apiIndex(Request $request)
    {
        return response()->json([
            'filters' => $request->all('search'),
            'userTypes' => UserType::query()
                ->filter($request->only('search'))
                ->get(),
        ]);
    }
}
