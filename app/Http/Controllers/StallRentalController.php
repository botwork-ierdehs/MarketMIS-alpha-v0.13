<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Http\Requests\StallRentalRequest;
use App\Http\Requests\StallRentalRenewRequest;
use App\Http\Requests\StallRentalEditRequest;

use App\Models\StallRental;
use App\Models\Stall;
use App\Models\RequirementChecklist;
use App\Models\StallsCategories;
use App\Models\FeeMasterlist;
use App\Models\User;
use App\Models\ApprovalPermit;

use App\Http\Controllers\PermitController;
use App\Http\Controllers\RequirementController;

use Carbon\Carbon;
use App\Services\TwilioService;

class StallRentalController extends Controller
{
    public function index(Request $request): Response
    {   
        $user = Auth::user();

        $data = [
            'filters' => $request->all('search', 'category', 'status'),
            'stallRentals' => $this->getIndexData($request),
            'stallsCategories' => StallsCategories::query()->where('is_table_rental', false)
                ->where('is_transient', false)->get(),
        ];
        return Inertia::render($user->role_id === 3 ? 'Users/Applications/StallRental'
        : 'Admin/Applications/StallRental', $data);
    }

    public function show(Request $request, StallRental $stallRental): RedirectResponse | Response
    {
        $user = Auth::user();

        // Only allow access if user is role 2 and permit is assigned to their department
        if ($user->role_id === 1 || $user->role_id === 2 && $stallRental->permits && $stallRental->permits->department_id == $user->department_id) {
            return Inertia::render('Department/StallRental/Show', [
                'stallRental' => $this->getSingleData($stallRental),
            ]);
        }
        return Inertia::render('Users/Applications/StallRental/Details', [
                'stallRental' => $this->getSingleData($stallRental),
        ]);
    }

    public function edit(Request $request, StallRental $stallRental): Response|RedirectResponse
    {
        if (
            Str::contains($request->fullUrl(), 'renew') && !$stallRental->expiringSoon ||
            Str::contains($request->fullUrl(), 'reupload') &&  $stallRental->permits->status !== 3 ||  $stallRental->permits->status === 3) {
                Log::warning('Attempted renewal for non-expiring stall rental', [
                    'stall_rental_id' => $stallRental->id,
                ]);
                return back()->with('error', 'Stall rental application is not yet expiring!');
        }

        $user = Auth::user();
        if ($user && $user->role_id !== 3 && $stallRental->status === 1) {
            return redirect()->route('department.applications.stalls');
        }

        return Inertia::render('Users/Applications/StallRental/Form', array_merge(
            ['stallRental' => $this->getSingleData($stallRental)],
            $this->getCreatePayload()
        ));
    }

    public function store(StallRentalRequest $request)
    {
        $payload = $request->validated();
        $total_payments = 0;
                // Step 1 → calculate payment
        if (isset($payload['step']) && $payload['step'] == 1) {

            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($feeMasterlist, $fees_additional) = $this->prepareFees($request, $stall);

            $total_payments = $this->calculateStallRentalPaymentFromFees(
                (int) $request->bulb,
                $stall,
                $feeMasterlist,
                $fees_additional
            );
            return Inertia::render('Users/Applications/StallRental/Form', 
                array_merge($this->getCreatePayload(), [
                    'total_payments' => $total_payments,
                ]))->withViewData(['step' => 1]);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 2) {
            return Inertia::render('Users/Applications/StallRental/Form',
             array_merge($this->getCreatePayload(), [
                'requirementsPayload' => $payload['requirements'],
                ]))
                ->withViewData(['step' => 2]);
        }

        // Step 3 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 3) {
            return Inertia::render('Users/Applications/StallRental/Form')
                ->withViewData(['step' => 3]);
        }

        // Step 4 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 4) {
            $payload['fees_additional'] = $payload['fees'];
            $this->addData($payload, $request);

            return back()->with('success', 'Stall rental application created successfully.');
        }
    
        // Default fallback
        return back()->withErrors(['step' => 'Invalid step provided.']);
        
    }

    public function renewal(StallRentalRequest $request, StallRental $stallRental)
    {
        $payload = $request->all();
        $step = $payload['step'] ?? null;

        // 🔍 Log incoming payload for debugging
        Log::info('StallRentalController@renewal payload', $payload);

        if (is_null($step)) {
            return back()->withErrors(['step' => 'Step is required.']);
        }

        switch ((int) $step) {
            case 1:
                // 🧮 Calculate payment
                $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
                [$feeMasterlist, $fees_additional] = $this->prepareFees($request, $stall);

                $total_payments = $this->calculateStallRentalPaymentFromFees(
                    (int) $payload['bulb'] ?? 0,
                    $stall,
                    $feeMasterlist,
                    $fees_additional
                );

                return Inertia::render('Users/Applications/StallRental/Form', array_merge(
                    [
                        'stallRental' => $this->getSingleData($stallRental),
                    ],
                    $this->getCreatePayload(),
                    [
                        'total_payments' => $total_payments,
                    ]
                ))->withViewData(['step' => 1]);

            case 2:
                // 📎 Handle requirements
                return Inertia::render('Users/Applications/StallRental/Form', array_merge(
                    $this->getCreatePayload(),
                    [
                        'requirementsPayload' => $payload['requirements'] ?? [],
                    ]
                ))->withViewData(['step' => 2]);

            case 3:
                // ✍️ Contract signature step
                return Inertia::render('Users/Applications/StallRental/Form')
                    ->withViewData(['step' => 3]);

            case 4:
                // ✅ Final update and save
                $payload['fees_additional'] = $payload['fees'] ?? [];

                // Use only validated payload for update (avoid $request->all())
                $this->updateData($stallRental, $payload);

                return back()->with('success', 'Stall rental application renewed successfully.');

            default:
                return back()->withErrors(['step' => 'Invalid step provided.']);
        }
    }

    public function create(): Response
    {
        return Inertia::render('Users/Applications/StallRental/Form', $this->getCreatePayload());
    }

    public function update(StallRentalEditRequest $request, StallRental $stallRental): Response|RedirectResponse
    {
        $payload = collect($request)
            ->except(['_token', '_method', 'step'])
            ->filter(fn($value) => !is_null($value) && $value !== '')
            ->toArray();
        $this->updateData($stallRental, $request->all(), $request);
        return redirect()->route('applications.stalls.index')->with('success', 'Stall rental application updated successfully.');
    }

    public function approveRental(Request $request, StallRental $stallRental): Response|RedirectResponse
    {
        $rentalDetails = $this->getSingleData($stallRental);
        $approvalDetails = $this->approve($rentalDetails);
        Log::info('Stall rental approved', [
            'stall_rental_id' => $stallRental->id,
            'approval_details' => $approvalDetails,
        ]);
        
        return back()->with('success',  'Stall rental approved successfully.');
    }

    public function rejectRental(Request $request, StallRental $stallRental): Response|RedirectResponse
    {
        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);
        $this->disApprove($stallRental, $request->all());
        return back()->with('success', 'Stall rental rejected.');
    }

    public function countAllPerCategoryAndStall() {
        return StallRental::with('stalls.stallsCategories')
            ->where('status', 1)
            ->get()
            ->groupBy(fn ($rental) => $rental->stall?->stallsCategories?->name)
            ->map(fn ($group) => [
                'name' => $group->first()->stall->stallsCategories->name ?? 'Unknown',
                'total' => $group->count()
                ])->values();
    }

    public function countMonthlyApplicants(){
        $rawCounts = StallRental::selectRaw('EXTRACT(MONTH FROM created_at) AS month, COUNT(*) AS total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->pluck('total', 'month');

        return collect(range(1, 12))->map(function ($month) use ($rawCounts) {
            return [
                'month' => Carbon::create()->month($month)->format('M'), // e.g. 'Jan', 'Feb'
                'total' => $rawCounts->get($month, 0)
            ];
        });
    }

    public function countTodayApplicants(){
        return StallRental::query()->totalTodayApplicants();
    }

    public function indexApi(Request $request)
    {
        // $user = Auth::user();
        $stallRentals = $this->getIndexData($request);

        return response()->json([
            'filters' => $request->all('search', 'stallType'),
            'stallRentals' => $stallRentals,
            'stallsCategories' => StallsCategories::query()->where('is_table_rental', false)
                ->where('is_transient', false)->get(),
        ]);

    }

    public function showApi(Request $request, StallRental $stallRental)
    {
        return response()->json([
            'stallRental' => $this->getSingleData($stallRental),
        ]);
    }

    public function storeApi(StallRentalRequest $request)
    {  
        $payload = $request->validated();
        $total_payments = 0;
        // Step 1 → calculate payment
        if (isset($payload['step']) && $payload['step'] == 1) {

            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($feeMasterlist, $fees_additional) = $this->prepareFees($request, $stall);

            $total_payments = $this->calculateStallRentalPaymentFromFees(
                (int) $request->bulb,
                $stall,
                $feeMasterlist,
                $fees_additional
            );
          
                 return response()->json(
             array_merge($this->getCreatePayload(), [
                    'requirements' => $payload['requirements'],
                     'total_payments' => $total_payments,
                    'step' => 1
                ]), 200);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 2) {
            return response()->json(
             array_merge($this->getCreatePayload(), [
                    'requirements' => $payload['requirements'],
                    'step' => 2
                ]), 200);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 3) {
            return response()->json(['step' => 3], 200);
        }

        // Step 3 → final save
        if (isset($payload['step']) && $payload['step'] == 4) {
            
            $this->addData($payload, $request);

            return response()->json('success', 'Stall rental application created successfully.', 200);

        }
        Log::info('StallRentalController@store step', [
            'step' => $payload['step'] ?? null,
        ]);
        // Default fallback
        return response()->json(['step' => 'Invalid step provided.'], 400);
    }

    public function createApi()
    {
        return response()->json($this->getCreatePayload());

    }

    public function editApi(StallRental $stallRental)
    {
      return response()->json(
            array_merge(
                ['stallRental' => $this->getSingleData($stallRental)],
                $this->getCreatePayload()
            )
        );

    }

    public function updateApi(StallRentalRequest $request, StallRental $stallRental)
    {
        return response()->json(
            array_merge(
                ['stallRental' => $this->updateData($stallRental, $request->all())],
            )
        );
    }

    public function approveApi(Request $request, StallRental $stallRental)
    {
        $rentalDetails = $this->getSingleData($stallRental);
        $approvalDetails = $this->approve($rentalDetails);
        return response()->json([
            'message' => 'Stall rental approved successfully.',
            'data' => $approvalDetails,
        ]);
    }

    public function rejectApi(Request $request, StallRental $stallRental)
    {
        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);
        $this->disApprove($stallRental, $request->all());
        return response()->json([
            'message' => 'Stall rental rejected.',
        ]);
    }


    public function approveOrReject(Request $request, StallRental $stallRental)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $statusValue = $request->status === 'approved' ? 1 : 2; // 1 = approved, 2 = rejected (adjust as needed)

        $stallRental->status = $statusValue;
        $stallRental->save();

        return response()->json([
            'message' => 'Stall rental status updated successfully.',
            'status' => $stallRental->status,
        ]);
    }

    private function prepareFees($request, $stall): array
    {
        // Base fee from stall category
        $feeMasterlist = FeeMasterlist::where(
            'id',
            json_decode($stall->stallsCategories->fee_masterlist_id, true)
        )->first();

        // Merge request fees with bulb fee if needed
        $feesIds = $request->fees_additional ? json_decode($request->fees_additional , true) : $request->fees ?? [];
        // dd($feesIds);
        if (!is_array($feesIds)) {
            $feesIds = json_decode($feesIds, true) ?? [];
        }

        if ((int) $request->bulb > 0) {
            $feesIds = array_merge($feesIds, [3]); // id 3 is Bulbs
        }

        // Fetch all fees from DB
        $fees = FeeMasterlist::whereIn('id', $feesIds)->get()->map(function ($f) {
            return [
                'id'     => $f->id,
                'type'   => $f->type,
                'amount' => $f->amount,
            ];
        })->toArray();

        return [$feeMasterlist, $fees];
    }

    private function calculateStallRentalPaymentFromFees(
        int $bulb,
        Stall $stall,
        FeeMasterlist $feeMasterlist,
        array $fees,
        ?bool $isNotPaid = null, // make it optional
    ) {
        $total = 0;
        $breakdown = [];

        // Ensure fees is always an array
        if (is_string($fees)) {
            $fees = json_decode($fees, true);
        }

        // Compute occupancy fee (per sqm if stall)
        if ($stall->stall_category_id === 7 && $stall->size) {
            $size = json_decode($stall->size, true);
            $area = $size['length'] * $size['width']; 
            $fee = $area * $feeMasterlist->amount;
            $breakdown[] = (object) ['type' => 'Occupancy Permit Fee', 'amount' => $fee, 'size' => $size ?
             "{$size['length']}m x {$size['width']}m = {$area} sqm" : null];
            $total += $fee;
        }

        // Fixed table fees
        if (in_array($stall->stall_category_id, [1, 2, 3, 6])) {
            $fee = $feeMasterlist->amount;
            $breakdown[] = (object) ['type' => $feeMasterlist->type, 'amount' => $fee];
            $total += $fee;
        }

        // Add fixed fees except bulbs
        foreach ($fees as $fee) {
            $type = $fee['type'];
            $amount = $fee['amount'];

            if ($type !== 'Bulbs') {
                $breakdown[] = (object) ['type' => $type, 'amount' => $amount];
                $total += $amount;
            }
        }

        // Add bulb charges
        
        foreach ($fees as $fee) {
            $type = $fee['type'];
            $amount = $fee['amount'];

            if ($type === 'Bulbs' && $bulb > 0) {
                $bulbFee = $bulb * $amount;
                $breakdown[] = (object) ['type' => "Bulbs ({$bulb} pcs)", 'amount' => $bulbFee];
                $total += $bulbFee;
            }
        }

         // Apply surcharge if unpaid
        if ($isNotPaid) {
            $surcharge = $total * 0.12;
            $breakdown[] = (object) ['type' => '12% Surcharge (Unpaid)', 'amount' => $surcharge];
            $total += $surcharge;
        }
        
        return (object) [
            'stall' => $stall,
            'breakdown' => $breakdown,
            'total' => $total,
        ];
    }

    private function quarterStartDate($startDate = null)
    {
        if (empty($startDate)) {
            return null;
        }

        $date = Carbon::parse($startDate);

        // start of quarter for given date
        $quarterStart = $date->firstOfQuarter();

        // next quarter start
        $nextQuarterStart = $quarterStart->copy()->addQuarter();

        // set to 20th day
        $nextQuarter20th = $nextQuarterStart->day(20);

        return $nextQuarter20th->toDateString();
    }


    private function getIndexData(Request $request) {
        $user = Auth::user();

        $query = StallRental::query()->filter($request->all('search', 'category', 'type'));

        if ($user && $user->role_id === 3) {
            $query->myApplication($user->id);
        }

        if ($user && $user->role_id === 2) {
            $query->myApplicationUnderMyDep($user->department_id);
            Log::info('Fetching stall rentals for department', [
                'department_id' => $user->department_id,
                'user_id' => $user ? $user->id : null,
            ]);
        }

        return $query->paginate(10)
            ->through(function ($data) use ($user) {
                $stall = $data->stalls;
                $stallCategory = $stall ? $stall->stallsCategories : null;
                $permits = $data->permits;

                 // penalty check
                $startDate = $data->started_date; 

                $isCurrentQuarterPaid = false;

                if ($startDate && !$startDate->isSameMonth(now())) {
                    $isCurrentQuarterPaid = $data->payments()
                        ->inCurrentQuarter()
                        ->where('stall_rental_id', $data->id)
                        ->exists();
                }

                // fees
                list($feeMasterlist, $fees_additional) = $this->prepareFees($data, $stall);
                $paymentDetails = $this->calculateStallRentalPaymentFromFees(
                (int) $data->bulb,
                    $stall,
                    $feeMasterlist,
                    $fees_additional,
                    $isCurrentQuarterPaid
                );

                $currentPayment = $data->payments()
                        ->withStallRental()
                        ->thisMonth()
                        ->get();
                return [
                    'vendor' => $data->user,
                    'id' => $data->id,
                    'name' => $data->business_name,
                    'status' => $data->status,
                    'start_date' => $data->started_date ? Carbon::parse($data->started_date) : null,
                    'end_date' => $data->started_date ? Carbon::parse($data->end_date) : null,
                    'acknowledgeContract' => $data->acknowledgeContract,
                    'stalls' => $stall ? [
                        'id' => $stall->id,
                        'name' => $stall->name,
                        'size' => $stall->size,
                        'status' => $stall->status,
                        'location' => $stall->location,
                        'coordinates' => json_decode($stall->coordinates, true),
                        'stall_category_id' => $stall->stall_category_id,
                        'stallsCategories' => $stallCategory ? [
                            'id' => $stallCategory->id,
                            'name' => $stallCategory->name,
                            'description' => $stallCategory->description,
                            'is_transient' => $stallCategory->is_transient,
                            'fee_masterlist_ids' => $stallCategory->fee_masterlist_ids,
                            'fee' => FeeMasterlist::where(
                                'id',
                               $stallCategory->fee_masterlist_id
                            )->get(),
                        ] : null,
                    ] : null,
                    'permits' => $permits ? [
                        'id' => $permits->id,
                        'permit_number' => $permits->permit_number,
                        'type' => $permits->type === 1 ? 'New' : 'Renewal',
                        'status' => $permits->status,
                        'created_at' => $permits->created_at ? Carbon::parse($permits->created_at) : null,
                        'issued_date' => $permits->issued_date ? Carbon::parse($permits->issued_date) : null,
                        'expiry_date' => $permits->expiry_date ? Carbon::parse($permits->expiry_date) : null,
                    ] : null,
                    'requirements' => $permits ? $permits->requirements->map(function ($requirement) {
                        return [
                            'id' => $requirement->id,
                            'attachment' => $requirement->attachment,
                            'name' => $requirement->requirementDetails->name,
                            'checklist_id' => $requirement->requirement_checklist_id,
                            'url' => asset('uploads' . $requirement->path),
                        ];
                    }) : collect(),
                    'quarterly_payment' => $paymentDetails->total,
                    'next_payment_due' => $this->quarterStartDate($permits->issued_date),
                    'penalty' => $isCurrentQuarterPaid ? ($paymentDetails->total * 0.12) : 0,
                    'currentPayment' => $currentPayment,
                    'current_payment' => count($currentPayment) > 0 ? 'Paid' : 'Not Paid',
                    'paymentDetails' => $paymentDetails,
                    'expiringSoon' => $data->expiringSoon,
                    'approvals' => $permits->approvals ? $permits->approvals->map(function ($approval) {
                        return [
                            'id' => $approval->id,
                            'approver' => $approval->approver ? [
                                'id' => $approval->approver->id,
                                'name' => $approval->approver->first_name . ' ' . $approval->approver->last_name,
                                'email' => $approval->approver->email,
                                'position' => $approval->approver->departmentPositions ? $approval->approver->departmentPositions->name : null
                            ] : null,
                            'department' => $approval->department->name,
                            'status' => $approval->status,
                            'created_at' => $approval->created_at ? Carbon::parse($approval->created_at) : null,
                        ];
                    }) : collect(),
                    'payments_history' => $data->payments()
                        ->withStallRental()->get()
                ];
            })
            ->withQueryString();
    }

    private function getCreatePayload(){
        return [
            'stallTypesAll' => Stall::where('stall_category_id', 7)
                ->get()
                ->map(function ($data) {
                    return [
                        'id' => $data->id,
                        'name' => $data->name,
                        'categories' => $data->stallsCategories ? [
                            'id' => $data->stallsCategories->id,
                            'name' => $data->stallsCategories->name,
                            'description' => $data->stallsCategories->description,
                            'is_transient' => $data->stallsCategories->is_transient,
                            'fee_masterlist_ids' => $data->stallsCategories->fee_masterlist_id,
                            'fee' => FeeMasterlist::where(
                                'id', // column name
                                $data->stallsCategories->fee_masterlist_id // array of IDs
                            )->get(),
                        ] : null,
                        'coordinates' => $data->coordinates ? json_decode($data->coordinates, true) : null,
                        'size' => $data->size ? json_decode($data->size, true) : null,
                        'status' => $data->status,
                    ];
                }),
            'stallTypes' => Stall::where('stall_category_id', 7)->where('status', 2)
                ->get()
                ->map(function ($data) {
                    return [
                        'id' => $data->id,
                        'name' => $data->name,
                        'categories' => $data->stallsCategories ? [
                            'id' => $data->stallsCategories->id,
                            'name' => $data->stallsCategories->name,
                            'description' => $data->stallsCategories->description,
                            'is_transient' => $data->stallsCategories->is_transient,
                            'fee_masterlist_ids' => $data->stallsCategories->fee_masterlist_id,
                            'fee' => FeeMasterlist::where(
                                'id', // column name
                                $data->stallsCategories->fee_masterlist_id // array of IDs
                            )->get(),
                        ] : null,
                        'coordinates' => $data->coordinates ? json_decode($data->coordinates, true) : null,
                        'size' => $data->size ? json_decode($data->size, true) : null,
                        'status' => $data->status,
                    ];
                }),
            'requirements' => RequirementChecklist::query()->requirementsList(),
            'fees' => FeeMasterlist::whereIn('id', [1, 2, 3])->get(),
        ];
    }

    private function getSingleData(StallRental $stallRental) {
        $user = Auth::user(); //Auth::user();
        $stall = $stallRental->stalls;

        if ($user->role_id === 2 && $stall && $stallRental->permits && $stallRental->permits->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        $stallCategory = $stall ? $stall->stallsCategories : null;
        $permits = $stallRental->permits;

        // penalty check
        $startDate = $stallRental->started_date; 

        $isCurrentQuarterPaid = false;

        if ($startDate && !$startDate->isSameMonth(now())) {
            $isCurrentQuarterPaid = $stallRental->payments()
                ->inCurrentQuarter()
                ->where('stall_rental_id', $stallRental->id)
                ->exists();
        }

        // fees
        list($feeMasterlist, $fees_additional) = $this->prepareFees($stallRental, $stall);
        $paymentDetails = $this->calculateStallRentalPaymentFromFees(
            (int) $stallRental->bulb,
            $stall,
            $feeMasterlist,
            $fees_additional,
            $isCurrentQuarterPaid
        );

        $currentPayment = $stallRental->payments()
            ->withStallRental()
            ->thisMonth()
            ->get();
        return [
            'vendor' => $stallRental->user,
            'id' => $stallRental->id,
            'name' => $stallRental->business_name,
            'status' => $stallRental->status,
            'fees_additional' => json_decode($stallRental->fees_additional, true) ?? [],
            'bulb' => $stallRental->bulb,
            'coordinates' => $stallRental->coordinates,
            'start_date' => $stallRental->started_date ? Carbon::parse($stallRental->started_date) : null,
            'end_date' => $stallRental->started_date ? Carbon::parse($stallRental->end_date) : null,
            'stalls' => $stall ? [
                'id' => $stall->id,
                'name' => $stall->name,
                'size' => $stall->size,
                'status' => $stall->status,
                'coordinates' => $stall->coordinates,
                'stall_category_id' => $stall->stall_category_id,
                'stallsCategories' => $stallCategory ? [
                    'id' => $stallCategory->id,
                    'name' => $stallCategory->name,
                    'description' => $stallCategory->description,
                    'is_transient' => $stallCategory->is_transient,
                    'fee_masterlist_ids' => $stallCategory->fee_masterlist_id,
                    'fee' => FeeMasterlist::where(            
                        'id',
                       $stallCategory->fee_masterlist_id
                    )->get(),
                ] : null,
            ] : null,
            'permits' => $permits ? [
                'id' => $permits->id,
                'type' => $permits->type === 1 ? 'New' : 'Renewal',
                'permit_number' => $permits->permit_number,
                'department_id' => $permits->department_id,
                'status' => $permits->status,
                'remarks' => $permits->remarks,
                'attachment_signature' => $permits->attachment_signature, 
                'assign_to' => $permits->assign_to,
                'is_initial' => $permits->is_initial,
                'issued_date' => $permits->issued_date ? Carbon::parse($permits->issued_date) : null,
                'expiry_date' => $permits->expiry_date ? Carbon::parse($permits->expiry_date) : null,
            ] : null,
            'requirements' => $permits ? $permits->requirements->map(function ($requirement) {
                return [
                    'id' => $requirement->id,
                    'attachment' => $requirement->attachment, 
                    'name' => $requirement->requirementDetails->name,  
                    'checklist_id' => $requirement->requirement_checklist_id,
                    'url' => asset('uploads' . $requirement->path),
                ];
            }) : collect(),
            'paymentDetails' => $paymentDetails,
            'quarterly_payment' => $paymentDetails->total,
            'next_payment_due' => $this->quarterStartDate($permits->issued_date),
            'penalty' => $isCurrentQuarterPaid ? ($paymentDetails->total * 0.12) : 0,
            'current_payment' => count($currentPayment) > 0 ? 'Paid' : 'Not Paid',
            'approvals' => $permits->approvals ? $permits->approvals->map(function ($approval) {
                 return [
                    'id' => $approval->id,
                    'approver' => $approval->approver ? [
                        'id' => $approval->approver->id,
                        'name' => $approval->approver->first_name . ' ' . $approval->approver->last_name,
                        'email' => $approval->approver->email,
                        'position' => $approval->approver->departmentPositions ? $approval->approver->departmentPositions->name : null
                    ] : null,
                    'department' => $approval->department->name,
                    'status' => $approval->status,
                    'created_at' => $approval->created_at ? Carbon::parse($approval->created_at) : null,
                ];
            }) : collect(),
            'payments_history' => $stallRental->payments()
                ->withStallRental()->get()
        ];  
    }

    private function addData(array $payload, StallRentalRequest $request)
    {
        $user = Auth::user();
        
        $requirementController = new RequirementController();
        $paymentController = new PaymentController();
        $stallController = new StallsListController();
        $permitController = new PermitController();

        // Payments Calculation
        $stall = Stall::query()->where('id',(int) ($payload['stall_id']))
            ->get()->map(function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'size' => $data->size,
                    'status' => $data->status,
                    'coordinates' => $data->coordinates,
                    'stall_category_id' => $data->stall_category_id,
                    'stallsCategories' => $data->stallsCategories,
                ];
            })->first();
        
        $permitDetails = $permitController->addBusinessPermit($payload);
            
        
        $file = $request->file('attachment_signature');
        $uuidName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $fileName = 'attachment_signature-'.$payload['business_name'].'-'.$uuidName;
        $file->move(public_path('uploads'), $fileName);

        $path = 'uploads/' . $fileName;

        $payload['attachment_signature'] = $path;
        $payload['user_id'] = $user->id;
        $payload['permit_id'] = $permitDetails->id;
        $payload['bulb'] = $payload['bulb'] ? $payload['bulb'] : 0;
        $payload['fees_additional'] = !empty($payload['fees']) 
            ? json_encode($payload['fees']) 
            : null;
        $payload['attachment_signature'] = 'attachment_signature-'.$payload['business_name'].'-'.$uuidName;
        // $payload['acknowledgeContract'] = $payload['acknowledgeContract'] ? $payload['acknowledgeContract'] : true;

        $stallRental = StallRental::create($payload);
        $requirements = $payload['requirements'] ?? [];
        Log::info('Adding requirements to StallRental', [
            'reques' => $payload,
            'stall_rental_id' => $stallRental->id,
            'permit_id' => $permitDetails->id,
            'requirements_count' => count($requirements),
            ]);
        if (!empty($requirements)) {
      
            $requirements = $requirementController->addRequirements(
            array_map(function ($item) use ($permitDetails) {
                // Make sure required keys exist
                return [
                'permit_id' => $permitDetails->id,
                'requirement_checklist_id' => $item['requirement_checklist_id'] ?? null,
                'attachment' => $item['attachment'] ?? null,
                ];
            }, $requirements)
            );
            Log::info('Requirements added successfully', [
            'stall_rental_id' => $stallRental->id,
            'permit_id' => $permitDetails->id,
            ]);
        }
        
        $amountFee = $payload['total_payment'] ?? 0;

        // Update stall to Reserved
        $stallController->statusUpdate(
            new Request(['status' => 3]),
            Stall::findOrFail((int) $payload['stall_id'])
        );

        return [
            'stallRental' => $stallRental,
            'permitDetails' => $permitDetails,
            'requirements' => $requirements,
        ];
    }

    private function updateData(?StallRental $stallRental, array $payload, ?StallRentalEditRequest $request = null)
    {
        $stallController = new StallsListController();
        $permitController = new PermitController();

        // If stall_id is changed → old stall to available (e.g. 3), new stall to reserved (2)
        if (isset($payload['stall_id']) && $payload['stall_id'] != $stallRental->getOriginal('stall_id')) {
            // Update old stall (release it, example: status = 3 or whatever "available" is)
            if ($stallRental->getOriginal('stall_id')) {
                $stallController->statusUpdate(
                    new Request(['status' => 2]),
                    Stall::findOrFail((int) $stallRental->getOriginal('stall_id'))
                );
            }

            // Update new stall (set to reserved = 2)
            $stallController->statusUpdate(
                new Request(['status' => 3]),
                Stall::findOrFail((int) $payload['stall_id'])
            );
        } else {
            // If stall_id didn’t change → still update the current stall to status = 2
            $stallController->statusUpdate(
                new Request(['status' => 2]),
                Stall::findOrFail((int) $stallRental->stall_id)
            );
        }

        Log::info('Payload for updateData', [
            'payload' => $payload,
            'stallRental'=>$stallRental
        ]);
        unset($payload['step']);
        $permitPayload = $payload;

        if(isset($payload['status']) && $payload['status'] == 2) {
            $permitPayload['status'] = 2;
            unset($payload['status']);// keep table rental status as is (3 = rejected)
        }

        if(isset($payload['status']) && $payload['status'] == 1) {
            $permitPayload['status'] = 1;
             unset($payload['status']);// keep table rental status as is (3 = rejected)
        }
        
        if (isset($payload['type']) && (int)$payload['type'] === 2) {
            $permitPayload['issued_date'] = null;
            $permitPayload['expiry_date'] = null;
            $permitPayload['assign_to'] = 2;
            $permitPayload['is_initial'] = true;
            $permitPayload['department_id'] = 2;
            $permitPayload['status'] = 0; // 2 = pending or renewal, adjust as needed
            $payload['status'] = 0; // set table rental status to pending (0), adjust if needed
        }

        if(isset($payload['fees'])) {
            $payload['fees_additional'] = !empty($payload['fees']) 
            ? json_encode($payload['fees']) 
            : null;
        }
        if(!empty($payload['attachment_signature'])){
            $file = $request->file('attachment_signature');
            $uuidName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $fileName = 'attachment_signature-'.$payload['business_name'].'-'.$uuidName;
            $file->move(public_path('uploads'), $fileName);

            $path = 'uploads/' . $fileName;
     
            $payload['attachment_signature'] = $path;

        }
        else{
            unset($payload['attachment_signature']);
        }

        if (!isset($payload['bulb'])) {
            $payload['bulb'] = 0;
        }

        if (empty($payload['total_payment'])) {
            unset($payload['total_payment']);
        }

        unset($permitPayload['_method']);
        unset($permitPayload['total_payment']);
        unset($permitPayload['business_name']);
        unset($permitPayload['requirements']);
        unset($permitPayload['fees']);
        unset($permitPayload['fees_additional']);
        unset($permitPayload['stall_rental_id']);
        unset($permitPayload['stall_id']);
        unset($permitPayload['acknowledgeContract']);
        unset($permitPayload['attachment_signature']);
        unset($permitPayload['bulb']);


        $permitController->update($permitPayload, $stallRental->permit_id);
        // Update requirements if present
   
        if (isset($payload['requirements']) && is_array($payload['requirements'])) {
            $requirementController = new RequirementController();

            $approvalRejection = ApprovalPermit::where('permit_id', $stallRental->permit_id)
                ->where('status', 2) // rejected
                ->first();
            Log::info('Handling requirements update for StallRental', [
                'stall_rental_id' => $stallRental->id,
                'permit_id' => $stallRental->permit_id,
                'approval_rejection' => $approvalRejection ? $approvalRejection->toArray() : null,
            ]);
            if ($approvalRejection) {
                $permitController->update([
                'status' => 0, // back to pending
                'assign_to' => 2,
                'department_id' => $approvalRejection->department_id,
                'is_initial' => $stallRental->permits->is_initial,
                ], $stallRental->permit_id);
            } else {
                Log::warning("No rejection record found for permit_id {$stallRental->permit_id}");
            }

            $requirementController->deleteRequirements($stallRental->permit_id);

            foreach ($payload['requirements'] as $requirementData) {
                if (isset($requirementData['id'])) {
                $requirementController->updateRequirement($requirementData['id'], $requirementData);
                } else {
                $requirementData['permit_id'] = $stallRental->permit_id;
                $requirementController->addRequirements([$requirementData]);
                }
            }
            ApprovalPermit::where('permit_id', $stallRental->permit_id)
            ->where('status', 2) // rejected
            ->delete();
        }

        Log::info('Stall Rental updated successfully.', [
            'id' => $stallRental->id,
            'updated_data' => $stallRental->toArray()
        ]);
        
        return $stallRental->update($payload);
    }

    private function disApprove($stallRental, array $payload) {
        $user = Auth::user();
        $vendor = (object)$stallRental['user'];
        $permit = (object)$stallRental['permits'];

        $approvalPermitController = new ApprovalPermitController();

        $approvalPermitController->storeApproval(
            $stallRental->permit_id, 
            $stallRental->permits->department_id,
            $user->id,
            2
        );
        $this->updateData($stallRental, ['status' => 3]);
        $permitController = new PermitController();
        $permitController->update([
                'status' => 2, //rejected
                'remarks' => $payload['remarks'],
                'assign_to' => 1, // 1 = user
                'is_initial' => false, // set to false when forwarded to user
            ], $stallRental->permit_id);

        // Send SMS notification to user about rejection         
        if ($vendor) {
            $twilioService = new TwilioService();
            
            $msg  = "Your application for Stall Rental has been rejected due to: {$payload['remarks']}.\n\n";
            $msg .= "Permit Details:\n";
            $msg .= "Permit No.: {$permit->permit_number}\n";
            $msg .= "Issued Date: {$permit->issued_date}\n";
            $msg .= "Expiry Date: {$permit->expiry_date}\n\n";
            $msg .= "From: Market MIS";

            $twilioService->sendSms($vendor->mobile, $msg);
        }
    }

    private function approve($stallRental) {
        $user = Auth::user();
        $vendor = (object)$stallRental['vendor'];
        Log::info('Approving StallRental', [
            'stall_rental_id' => $stallRental['id'],
            'vendor' => $vendor ? $vendor : null,
            'user' => $user ? $user->id : null,
        ]);
        $permit = (object)$stallRental['permits'];

        $currentDeptId = $permit->department_id; // Approver's department
        $approvalPermitController = new ApprovalPermitController();
        $approvalPermitController->storeApproval(
            $permit->id, 
            $currentDeptId,
            $user->id
        );
        $permitController = new PermitController();

        // STEP 1: Initial → SMPO
        if ($permit->is_initial) {
             $permitController->update([
                'department_id' => 3, // Office of the Mayor
                'assign_to' => 2, // assign to approver
                'is_initial' => false
            ], $permit->id);
           
            return response()->json(['message' => 'Office of Public Market Initial approval done. Your application was sent to Office of the Mayor.']);
        }

        // STEP 2: Office of the Mayor >   Market 
        if (!$permit->is_initial && $currentDeptId === 3 && $permit->status == 0) {
            $permitController->update([
                'department_id' => 2, // Office of the Public Market
            ], $permit->id);
           
            return response()->json(['message' => 'Office of Public Market Initial approval done. Sent to Office of the Mayor.']);
        }

        // STEP 3: Office of the Mayor >   Market → Final Approval
        if (!$permit->is_initial && $currentDeptId === 2 && $permit->status == 0) {
            $requiredDepartments = [
                2, // Public Market
                3, // OFFICE OF THE MUNICIPAL MAYOR
            ];

            $approvedDepartments = ApprovalPermit::where('permit_id', $permit->id)
                ->whereIn('department_id', $requiredDepartments)
                ->where('status', 1)
                ->distinct()
                ->pluck('department_id')
                ->toArray();

            if (count($approvedDepartments) < count($requiredDepartments)) {
                return response()->json(['message' => 'Cannot finalize: Missing required department approvals.'], 400);
            }

            // Finalize approval
            $permitController->update([
                'status'        => 1, // Approved
                'issued_date'   => Carbon::now()->format('Y-m-d'),
                'expiry_date'   => Carbon::now()->copy()->addYears(3)->toDateString(),
                'assign_to'     => 1,
                'permit_number' => $permitController->generatePermitNumber(10),
                'department_id' => 0
            ], $permit->id);
            Log::info('Approving TableRental', [
                'table_rental_id' => $stallRental,
                'status' => 1,
            ]);
            $stallRentalModel = StallRental::findOrFail($stallRental['id']);
            $this->updateData($stallRentalModel, ['status' => 1]);

            $stallController = new StallsListController();
            // Update stall status to 'assigned' (3)
            $stallController->statusUpdate(
                new Request(['status' => 1]),
                Stall::findOrFail((int) $stallRentalModel['stall_id'])
            );

            // Send SMS for approval notification to user about approval
          
            if ($vendor) {
                $twilioService = new TwilioService();
                $msg = "Your application for Stall Rental has been approved. \n";
                $msg .= "Permit No.: {$permit->permit_number}\n";
                $msg .= "Issued Date: {$permit->issued_date}\n";
                $msg .= "Expiry Date: {$permit->expiry_date}\n";
                $twilioService->sendSms($vendor->mobile, $msg);
            }

        }

      
        return [
            'message' => 'Volante rental approved successfully.',
            'stallRental' => $stallRental,
            'permit' => $permit
        ];

    }
    
}
