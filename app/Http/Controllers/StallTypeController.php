<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\StallsCategories;
use App\Models\FeeMasterlist;

use App\Http\Requests\StallTypeRequest;

class StallTypeController extends Controller
{
    public function index(Request $request): Response
    {   
        return Inertia::render('Admin/StallTypeCategory/Index', [
            'filters' => $request->all('search'),
            'stallsCategories' => StallsCategories::query()
                ->filter($request->only('search', 'category'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($stall) => [
                    'id' => $stall->id,
                    'name' => $stall->name,
                    'description' => $stall->description,
                    'fees' => $stall->feeMasterlists,
                    'is_transient' => $stall->is_transient,
                    'fee_masterlist_ids' => $stall->fee_masterlist_ids ? $stall->fee_masterlist_ids : [],
                    'fee' => $stall->fee_masterlist_ids ? FeeMasterlist::whereIn('id', json_decode($stall->fee_masterlist_ids, true))->get() : [],
                ]),
        ]);
    }

    public function create(): Response
    {   
        return Inertia::render('Admin/StallTypeCategory/Form', [
            'feeMasterlists' => FeeMasterlist::all(),
        ]);
    }

    public function store(StallTypeRequest $request): RedirectResponse
    {   
        StallsCategories::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'fee_masterlist_ids' => $request['fee_masterlist_ids'],
        ]);
        return redirect()->route('admin.stall.type')->with('success', 'Stall Type created successfully.');
    }

    public function edit(StallsCategories $stallsCategories): Response
    {
        return Inertia::render('Admin/StallTypeCategory/Form', [
            'feeMasterlists' => FeeMasterlist::all(),
            'stallsCategories' => [
                'id' => $stallsCategories->id,
                'name' => $stallsCategories->name,
                'description' => $stallsCategories->description,
                'fee_masterlist_ids' => $stallsCategories->fee_masterlist_ids,
                'is_transient' => $stallsCategories->is_transient,
            ]
        ]);
    }

    public function update(StallTypeRequest $request, StallsCategories $stallsCategories): RedirectResponse
    {
        $stallsCategories->update(
          [
            'name' => $request->name,
            'description' => $request->description,
            'fee_masterlist_ids' => $request->fee_masterlist_ids,
          ] 
        );
        return redirect()->route('admin.stall.type')->with('success', 'Stall Type updated successfully.');
    }
}
