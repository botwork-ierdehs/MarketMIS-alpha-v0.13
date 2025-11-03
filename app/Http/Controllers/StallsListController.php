<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

use App\Models\Stall;
use App\Models\StallsCategories;
use App\Models\FeeMasterlist;

use App\Http\Requests\StallListRequest;

class StallsListController extends Controller
{
    public function index(Request $request): Response
    {   
        return Inertia::render('Admin/Stalls/Index', [
            'filters' => $request->all('search', 'category'),
            'stallsCategories' => StallsCategories::all(),
            'stalls' => Stall::query()
                ->filter($request->only('search', 'category'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($stall) => [
                    'id' => $stall->id,
                    'name' => $stall->name,
                    'stall_category_id' => $stall->stall_category_id,
                    'size' => $stall->size,
                    'coordinates' => $stall->coordinates,
                    'location_description' => $stall->location_description,
                    'is_occupied' => $stall->is_occupied,
                    'fee_masterlist_id' => $stall->stallsCategories->fee_masterlist_id ?? null,
                    'fee' => FeeMasterlist::find($stall->stallsCategories->fee_masterlist_id ),
                    'status' => $stall->status,
                    'categories' => $stall->stallsCategories ?
                    [
                        'id' => $stall->stallsCategories->id,
                        'name' => $stall->stallsCategories->name,
                        'description' => $stall->stallsCategories->description,
                        'is_transient' => $stall->stallsCategories->is_transient,   
                    ] : null,
                ]),
        ]);
    }

    public function apiIndex(Request $request)
    {
        return response()->json([
            'filters' => $request->all('search', 'category'),
            'stallsCategories' => StallsCategories::all(),
            'stalls' => Stall::query()
                ->filter($request->only('search', 'category'))
                ->where('status', 2) // Only get vacant stalls
                ->get()
                ->transform(fn ($stall) => [
                   'id' => $stall->id,
                    'name' => $stall->name,
                    'stall_category_id' => $stall->stall_category_id,
                    'size' => $stall->size,
                    'coordinates' => $stall->coordinates,
                    'location_description' => $stall->location_description,
                    'is_occupied' => $stall->is_occupied,
                    'status' => $stall->status,
                    'categories' => $stall->stallsCategories ?
                    [
                        'id' => $stall->stallsCategories->id,
                        'name' => $stall->stallsCategories->name,
                        'description' => $stall->stallsCategories->description,
                        'is_transient' => $stall->stallsCategories->is_transient,   
                        'is_table' => $stall->stallsCategories->is_table,
                        'fee_masterlist_id' => $stall->stallsCategories->fee_masterlist_id ?? null,
                        'fee' => FeeMasterlist::find($stall->stallsCategories->fee_masterlist_id ),   
                    ] : null,
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Stalls/Form', [
            'stallsCategories' => StallsCategories::all(),
            'feeMasterlist' => FeeMasterlist::whereNotIn('id', [1, 2, 3])->get(),
        ]);
    }

    public function store(StallListRequest $request): RedirectResponse
    {
        $user =  Auth::user();
        Log::info('Login attempt', [
            '$user' => $user,
            '$request' => $request->validated()
        ]);
        Stall::create($request->validated() + ['created_by' => $user->id]);
        
        return redirect()->route('admin.stalls')->with('success', 'Stall created successfully.');
    }

    public function edit(Stall $stall): Response
    {
        Log::info('Login attempt', [
            '$id' => $stall
        ]);
        return Inertia::render('Admin/Stalls/Form', [
            'stallsCategories' => StallsCategories::all(),
            'feeMasterlist' => FeeMasterlist::whereNotIn('id', [1, 2, 3])->get(),
            'stall' => [
                'id' => $stall->id,
                'name' => $stall->name,
                'stall_category_id' => $stall->stall_category_id,
                'size' => $stall->size,
                'coordinates' => $stall->coordinates,
                'location_description' => $stall->location_description,
                'is_table' => $stall->is_table,
                'fee_masterlist_id' => $stall->stallsCategories->fee_masterlist_id ?? null,
                'fee' => FeeMasterlist::find($stall->stallsCategories->fee_masterlist_id ),
                'status' => $stall->status,
                'categories' => $stall->stallsCategories
            ]
        ]);
    }

    public function update(StallListRequest $request, Stall $stall): RedirectResponse
    {
        $stall->update(
          [
            'name' => $request->name,
            'stall_category_id' => $request->stall_category_id,
            'size' => $request->size,
            'area_of_sqr_meter' => $request->area_of_sqr_meter
          ] 
        );
        return redirect()->route('admin.stalls')->with('success', 'Stall updated successfully.');
    }

    public function getSingleRecord(Request $request)   
    {   
        return response()->json([
            'stalls' => Stall::query()
                ->singleRecord($request->route('id'))
                ->get()
                ->transform(fn ($stall) => [
                    'id' => $stall->id,
                    'name' => $stall->name,
                    'stall_category_id' => $stall->stall_category_id,
                    'size' => $stall->size,
                    'categories' => $stall->stallsCategories,
                    'status' => $stall->status,
                ]),
        ]);
    }

    public function statusUpdate(Request $request, Stall $stall)
    {
        return $stall->update(['status' => $request->status]);
    }
}
