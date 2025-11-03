<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeMasterlist;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class FeeMasterlistController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/FeeMasterlist/Index', [
            'fees' => FeeMasterlist::query()->paginate(10)
                ->withQueryString()
                ->through(fn ($fee) => [
                    'id' => $fee->id,
                    'type' => $fee->type,
                    'amount' => $fee->amount,
                    'description' => $fee->description,
                    'is_daily' => $fee->is_daily,
                    'is_monthly' => $fee->is_monthly,
                    'is_per_kilo' => $fee->is_per_kilo,
                    'is_styro' => $fee->is_styro,
                ]),
        ]);
    }

    public function edit(FeeMasterlist $feeMasterlist): Response
    {
        return Inertia::render('Admin/FeeMasterlist/Form', [
            'feeMasterlist' => [
                'id' => $feeMasterlist->id,
                'type' => $feeMasterlist->type,
                'amount' => $feeMasterlist->amount,
                'is_daily' => $feeMasterlist->is_daily,
                'is_monthly' => $feeMasterlist->is_monthly,
                'is_per_kilo' => $feeMasterlist->is_per_kilo,
                'is_styro' => $feeMasterlist->is_styro,
            ],
        ]);
    }

    public function update(Request $request, FeeMasterlist $feeMasterlist): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'is_daily' => 'boolean',
            'is_monthly' => 'boolean',
            'is_per_kilo' => 'boolean',
            'is_styro' => 'boolean',
        ]);

        $feeMasterlist->update($request->all());

        return redirect()->route('admin.fee.masterlist')->with('success', 'Fee Masterlist updated successfully.');
    }
}
