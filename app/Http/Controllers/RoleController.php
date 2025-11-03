<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): Response
    {   
        $user = Auth::user();
        return Inertia::render('Admin/Roles/Index', [
            'filters' => $request->all('search'),
            'roles' => Role::query()
                ->filter($request->only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($data) => [
                    'id' => $data->id,
                    'name' => $data->name,
                ]),
        ]);
    }
}
