<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\SubAdminType;

class SubAdminTypeController extends Controller
{
    public function index(Request $request): Response
    {   
        return Inertia::render('Admin/SubAdminType/Index', [
            'filters' => $request->all('search'),
            'subAdminTypes' => SubAdminType::query()
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
            'subAdminTypes' => SubAdminType::query()
                ->filter($request->only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($dep) => [
                    'id' => $dep->id,
                    'code' => $dep->name,
                    'dep' => $dep->departments,
                ]),
        ]);
    }
}
