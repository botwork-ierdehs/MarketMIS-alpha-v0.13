<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\RequirementChecklist;

use App\Http\Requests\RequirementChecklistRequest;

class RequirementListController extends Controller
{
    public function index(Request $request): Response
    {   
        return Inertia::render('Admin/Requirements/Index', [
            'filters' => $request->all('search'),
            'requirementsList' => RequirementChecklist::query()
                ->filter($request->only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($dep) => [
                    'id' => $dep->id,
                    'name' => $dep->name,
                    'description' => $dep->description,
                    'isRequired' => $dep->isRequired === true ? 'Yes' : 'No',
                    'isStallType' => $dep->isStallType,
                    'isStall' => $dep->isStall === true ? 'Yes' : 'No',
                    'isVolante' => $dep->isVolante === true ? 'Yes' : 'No',
                    'isVolanteType' => $dep->isVolanteType,
                ]),
        ]);
    }

    public function apiIndex(Request $request)
    {
        return response()->json([
            'filters' => $request->all('search'),
            'requirementsList' => RequirementChecklist::query()
                ->filter($request->only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($dep) => [
                    'id' => $dep->id,
                    'name' => $dep->name,
                    'description' => $dep->description,
                    'isRequired' => $dep->isRequired,
                    'isStallType' => $dep->isStallType,
                    'isStall' => $dep->isStall,
                    'isVolante' => $dep->isVolante,
                    'isVolanteType' => $dep->isVolanteType,
                ]),
        ]);
    }

    public function create(): Response
    {   
        return Inertia::render('Admin/Requirements/Form');
    }

    public function store(RequirementChecklistRequest $request): RedirectResponse
    {   
        RequirementChecklist::create([
            'name' => $request['name'],
        ]);
        return redirect()->route('admin.requirements.list')->with('success', 'Requirement Type created successfully.');
    }

    public function edit(RequirementChecklist $requirementsList): Response
    {
        return Inertia::render('Admin/Requirements/Form', [
            'requirementsList' => [
                'id' => $requirementsList->id,
                'name' => $requirementsList->name,
                'description' => $requirementsList->description,
                'isRequired' => $requirementsList->isRequired,
                'isStallType' => $requirementsList->isStallType,
                'isStall' => $requirementsList->isStall,
                'isVolante' => $requirementsList->isVolante,
                'isVolanteType' => $requirementsList->isVolanteType,
            ]
        ]);
    }

    public function update(RequirementChecklistRequest $request, RequirementChecklist $requirementsList): RedirectResponse
    {
        $requirementsList->update(
          [
            'name' => $request->name,
          ] 
        );
        return redirect()->route('admin.requirements.list')->with('success', 'Requirement Type updated successfully.');
    }
}
