<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\TablesRequest;
use App\Http\Requests\PaymentsRequest;

use App\Models\TableRental;
use App\Models\Stall;
use App\Models\RequirementChecklist;
use App\Models\StallsCategories;
use App\Models\FeeMasterlist;
use App\Models\User;
use App\Models\Payment;
use App\Models\Permits;
use App\Models\ApprovalPermit;


use App\Http\Controllers\PermitController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StallsListController;
use App\Http\Controllers\ApprovalPermitController;
use App\Services\TwilioService;

use Carbon\Carbon;
use Illuminate\Support\Str;

class TablesController extends Controller
{
    public function index(Request $request): Response
    {
        $user =  Auth::user();
        $data = [
            'filters' => $request->all('search', 'category', 'status'),
            'tableRentals' => $this->getIndexData($request),
            'stallsCategories' => StallsCategories::query()->where('is_table_rental', true)->where('is_transient', false)->get(),
        ];
        return Inertia::render($user->role_id === 3 ? 'Users/Applications/TableRental'
            : 'Admin/Applications/TableRental', $data);
    }

    public function create(): Response
    {
        // Get necessary data for creating a table rental
        $createPayload = $this->getCreatePayload();
        return Inertia::render('Users/Applications/TableRental/Form', $createPayload);
    }

    public function store(TablesRequest $request)
    {
        $payload = $request->validated();
        $total_payments = 0;
        // Step 1 → calculate payment
        if (isset($payload['step']) && $payload['step'] == 1) {
            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($feeMasterlist, $fees_additional) = $this->prepareFees($request, $stall);

            $total_payments = $this->calculateTableRentalPaymentFromFees(
                (int) $request->bulb,
                $stall,
                $feeMasterlist,
                $fees_additional
            );
            return Inertia::render('Users/Applications/TableRental/Form', 
            array_merge($this->getCreatePayload(), [
                    'total_payments' => $total_payments,
                ]))->withViewData(['step' => 1]);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 2) {
            return Inertia::render('Users/Applications/TableRental/Form', array_merge($this->getCreatePayload(), [
                'requirementsPayload' => $payload['requirements'],
                ]))
                ->withViewData(['step' => 2]);
        }

        // Step 3 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 3) {
            return Inertia::render('Users/Applications/TableRental/Form')
                ->withViewData(['step' => 3]);
        }


        // Step 4 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 4) {
            $payload['fees_additional'] = $payload['fees'];
            $this->addData($payload, $request);

            return back()->with('success', 'Table spaces rental application created successfully.');
        }
    

        return back()->withErrors(['step' => 'Invalid step provided.']);

    }

    public function show(Request $request, TableRental $tableRental): RedirectResponse | Response
    {
        $user = Auth::user();

        // Only allow access if user is role 2 and permit is assigned to their department
        if ($user->role_id === 1 || $user->role_id === 2 && $tableRental->permits && $tableRental->permits->department_id == $user->department_id) {
            return Inertia::render('Department/TableRental/Show', [
                'tableRental' => $this->getSingleData($tableRental),
            ]);
        }
        return Inertia::render('Users/Applications/TableRental/Details', [
                'tableRental' => $this->getSingleData($tableRental),
        ]);
    }

    public function edit(TableRental $tableRental, TablesRequest $request): Response|RedirectResponse
    {
        $user = Auth::user();
        if ($user->role_id !== 3 && $tableRental->status === 1) {
            return redirect()->route('department.applications.table');
        }

        if (
            Str::contains($request->fullUrl(), 'renew') && !$tableRental->expiringSoon ||
            Str::contains($request->fullUrl(), 'reupload') &&  $tableRental->permits->status !== 3 ||  $tableRental->permits->status === 3) {
                Log::warning('Attempted renewal for non-expiring stall rental', [
                    'stall_rental_id' => $tableRental->id,
                ]);
                return back()->with('error', 'Stall rental application is not yet expiring!');
        }
        return Inertia::render('Users/Applications/TableRental/Form', array_merge(
                ['tableRental' => $this->getSingleData($tableRental)],
                $this->getCreatePayload()
        ));
    }

    public function update(TablesRequest $request, TableRental $tableRental): Response|RedirectResponse
    {
        $this->updateData($tableRental, $request->all(), $request);
        return redirect()->route('applications.table.index')->with('success', 'Table rental application updated successfully.');
    }

    public function approveTable(Request $request, TableRental $tableRental) :  Response|RedirectResponse
    {
        $rentalDetails = $this->getSingleData($tableRental);
        $approvalDetails = $this->approve($rentalDetails);
        return redirect()->back()->with('success', 'Table rental approved successfully.');
    }

    public function rejectTable(Request $request, TableRental $tableRental) :  Response|RedirectResponse
    {
        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);
        $this->disApprove($tableRental, $request->all());
        return redirect()->route('department.applications.table')->with('success', 'Table rental rejected.');
    }

    // api
    public function indexApi(Request $request)
    {
        $tableRentals = $this->getIndexData($request);

        return response()->json([
            'filters' => $request->all('search', 'stallType'),
            'tableRentals' => $tableRentals,
            'stallsCategories' => StallsCategories::query()->where('is_table_rental', true)->get(),
        ]);

    }

    public function storeApi(TablesRequest $request)
    {  
        $payload = $request->validated();
        $total_payments = 0;
        // Step 1 → calculate payment
        if (isset($payload['step']) && $payload['step'] == 1) {
            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($feeMasterlist, $fees_additional) = $this->prepareFees($request, $stall);
            
            $total_payments = $this->calculatetableRentalPaymentFromFees(
                (int) $request->bulb,
                $stall,
                $feeMasterlist,
                $fees_additional
            );
            return response()->json([
                'step' => 1,
                'data' => $this->getCreatePayload(),
                'stall' => $stall,
                'total_payments' => $total_payments->total,
            ]);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 2) {
            return response()->json([
                'step' => 2,
            ]);
        }

        // Step 3 → final save
        if (isset($payload['step']) && $payload['step'] == 3) {
            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($feeMasterlist, $fees_additional) = $this->prepareFees($request, $stall);

            $total_payments = $this->calculatetableRentalPaymentFromFees(
                (int) $request->bulb,
                $stall,
                $feeMasterlist,
                $fees_additional
            );


            $payloadRequest = $payload;
            $payloadRequest['total_payment'] = $total_payments->total;
            $payloadRequest['fees_additional'] = json_encode($request->fees ?? []);

            $this->addData($payloadRequest);

            return response()->json([
                'step'    => 3,
                'message' => 'Stall rental application created successfully.',
                'data'    => $payloadRequest,
            ]);
        }


        return response()->json([
            'step' => null,
            'message' => 'Invalid step provided.',
        ], 400);
    }
    
    public function showApi(TableRental $tableRental)
    {
        $tableRentalDetails = $this->getSingleData($tableRental);

        return response()->json([
            'tableRental' => $tableRentalDetails,
        ]);
    }

    public function createApi()
    {
        return response()->json($this->getCreatePayload());

    }

    public function editApi(TableRental $tableRental)
    {
        return response()->json(array_merge(
            ['tableRental' => $this->getSingleData($tableRental)],
            $this->getCreatePayload()
        ));
    }

    public function updateApi(TablesRequest $request, TableRental $tableRental)
    {
        $this->updateData($tableRental, $request->all(), $request);

        return response()->json([
            'message' => 'Table rental application updated successfully.',
            'tableRental' => $this->getSingleData($tableRental),
        ]);
    }

    public function countTodayApplicants(){
        return TableRental::query()->totalTodayApplicants();
    }

    // private functions
    private function addData(array $payload, TablesRequest $request)
    {
        $user = Auth::user(); //Auth::user();
        
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


        $tableRental = TableRental::create($payload);
        $requirements = $requirementController->addRequirements(array_map(function ($item) use ($permitDetails) {
            $item['permit_id'] = $permitDetails->id;
            return $item;
        }, $payload['requirements']));

        
        $amountFee = $payload['total_payment'] ?? 0;

        // Update stall to Reserved
        $stallController->statusUpdate(
            new Request(['status' => 3]),
            Stall::findOrFail((int) $payload['stall_id'])
        );

        return [
            'tableRental' => $tableRental,
            'permitDetails' => $permitDetails,
            'requirements' => $requirements,
        ];
    }

    private function getCreatePayload(){
        return [
            'stallTypesAll' => Stall::whereIn('stall_category_id', [1, 2,3])
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
                        'coordinates' => $data->stall->coordinates ?? null,
                        'size' => $data->stall->size ?? null,
                        'status' => $data->status,
                    ];
                }),
            'stallTypes' => Stall::whereIn('stall_category_id', [1, 2,3])
                ->where('status', 2)
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
                        'coordinates' => $data->stall->coordinates ?? null,
                        'size' => $data->stall->size ?? null,
                        'status' => $data->status,
                    ];
                }),
                'requirements' => RequirementChecklist::query()->requirementsList(),
                'fees' => FeeMasterlist::whereIn('id', [1, 2, 3])->get(),

        ];
    }

    private function getIndexData(Request $request) {
        $user = Auth::user();
        
        $query = TableRental::query()->filter($request->all('search', 'category'));

        if ($user && $user->role_id === 3) {
            $query->myApplication($user->id);
        }

        if ($user && $user->role_id === 2) {
            $query->myApplicationUnderMyDep($user->department_id);
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
                        ->where('table_rental_id', $data->id)
                        ->exists();
                }

                // fees
                list($feeMasterlist, $fees_additional) = $this->prepareFees($data, $stall);
                $paymentDetails = $this->calculateTableRentalPaymentFromFees(
                (int) $data->bulb,
                    $stall,
                    $feeMasterlist,
                    $fees_additional,
                    $isCurrentQuarterPaid
                );

                $currentPayment = $data->payments()
                    ->withTableRental()
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
                        'coordinates' => $stall->coordinates,
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
                    'paymentDetails' => $paymentDetails,
                    'quarterly_payment' => $paymentDetails->total,
                    'next_payment_due' => $this->quarterStartDate(),
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
                ];
            })
            ->withQueryString();

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

    private function getSingleData(TableRental $tableRental) {
        $user = Auth::user(); //Auth::user();
        $stall = $tableRental->stalls;

        if ($user->role_id === 2 && $stall && $tableRental->permits && $tableRental->permits->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        $stallCategory = $stall ? $stall->stallsCategories : null;
        $permits = $tableRental->permits;

        // penalty check
        $startDate = $tableRental->started_date; 

        $isCurrentQuarterPaid = false;

        if ($startDate && !$startDate->isSameMonth(now())) {
            $isCurrentQuarterPaid = $tableRental->payments()
                ->inCurrentQuarter()
                ->where('stall_rental_id', $tableRental->id)
                ->exists();
        }

        // fees
        list($feeMasterlist, $fees_additional) = $this->prepareFees($tableRental, $stall);
        $paymentDetails = $this->calculateTableRentalPaymentFromFees(
            (int) $tableRental->bulb,
            $stall,
            $feeMasterlist,
            $fees_additional,
            $isCurrentQuarterPaid
        );

        $currentPayment = $tableRental->payments()
            ->withTableRental()
            ->thisMonth()
            ->get();
        return [
            'vendor' => $tableRental->user,
            'id' => $tableRental->id,
            'name' => $tableRental->business_name,
            'business_name' => $tableRental->business_name,
            'status' => $tableRental->status,
            'fees_additional' => json_decode($tableRental->fees_additional, true) ?? [],
            'bulb' => $tableRental->bulb,
            'coordinates' => $tableRental->coordinates,
            'start_date' => $tableRental->started_date ? Carbon::parse($tableRental->started_date) : null,
            'end_date' => $tableRental->started_date ? Carbon::parse($tableRental->end_date) : null,
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
            'payments_history' => $tableRental->payments()
                ->withTableRental()->get()
        ];  
    }
   
    private function quarterStartDate() {
        $today = Carbon::now();

        // start of current quarter
        $quarterStart = $today->firstOfQuarter();

        // move to next quarter
        $nextQuarterStart = $quarterStart->copy()->addQuarter();

        // set to 20th day
        $nextQuarter20th = $nextQuarterStart->day(20);

        return $nextQuarter20th->toDateString();
    }

    private function calculateTableRentalPaymentFromFees(
        int $bulb,
        Stall $stall,
        FeeMasterlist $feeMasterlist,
        array|string $fees,
        ?bool $isNotPaid = null, // make it optional
    ) {
        $total = 0;
        $breakdown = [];

        // Ensure fees is always an array
        if (is_string($fees)) {
            $fees = json_decode($fees, true);
        }

        Log::info('Calculating table rental payment from fees', [
            'bulb' => $bulb,
            'stall_id' => $stall?->id,
            'stall_category_id' => $stall?->stall_category_id,
            'fee_masterlist_id' => $feeMasterlist?->id,
            'fees' => $fees,
            'isNotPaid' => $isNotPaid,
        ]);

        // Fixed table fees
        if (in_array($stall->stall_category_id, [1, 2, 3, 5, 6])) {
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

    public function approveOrReject(Request $request, TableRental $tableRental)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $statusValue = $request->status === 'approved' ? 1 : 2; // 1 = approved, 2 = rejected (adjust as needed)

        $tableRental->status = $statusValue;
        $tableRental->save();

        return response()->json([
            'message' => 'Table rental status updated successfully.',
            'status' => $tableRental->status,
        ]);
    }

    private function disApprove($tableRental, array $payload) {
        $user = Auth::user();
        $permit = (object)$tableRental['permits'];
        $vendor = (object)$tableRental['user'];
        $approvalPermitController = new ApprovalPermitController();

        $approvalPermitController->storeApproval(
            $tableRental->permit_id, 
            $tableRental->permits->department_id,
            $user->id,
            2
        );
        $this->updateData($tableRental, ['status' => 3]);
        $permitController = new PermitController();
        $permitController->update([
                'status' => 2, //rejected
                'remarks' => $payload['remarks'],
                'assign_to' => 1, // 1 = user
                'is_initial' => false, // set to false when forwarded to user
            ], $tableRental->permit_id);
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

    private function approve($tableRental) {
        $user = Auth::user();
        $permit = (object)$tableRental['permits'];
        $vendor = (object)$tableRental['vendor'];

        $currentDeptId = $permit->department_id; // Approver's department
        $approvalPermitController = new ApprovalPermitController();
        $approvalPermitController->storeApproval(
            $permit->id, 
            $currentDeptId,
            $user->id,
            1 // approved (1), rejected (2
        );
        $permitController = new PermitController();

        // STEP 1: Initial → SMPO
        if ($permit->is_initial) {
             $permitController->update([
                'department_id' => 3, // Office of the Mayor
                'assign_to' => 2, // assign to approver
                'is_initial' => false
            ], $permit->id);
           
            return response()->json(['message' => 'Office of Public Market Initial approval done. Sent to Office of the Mayor.']);
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
            $tableRentalModel = TableRental::findOrFail($tableRental['id']);
            
            Log::info('Approving TableRental', [
                'table_rental_id' => $tableRentalModel->id,
                'status' => 1,
            ]);
            $this->updateData($tableRentalModel, ['status' => 1]);

            $stallController = new StallsListController();
            // Update stall status to 'assigned' (3)
            $stallController->statusUpdate(
                new Request(['status' => 1]),
                Stall::findOrFail((int) $tableRentalModel['stall_id'])
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
            'tableRental' => $tableRental,
            'permit' => $permit
        ];

    }

    public function renewal(TablesRequest $request, TableRental $tableRental)
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

                $total_payments = $this->calculateTableRentalPaymentFromFees(
                    (int) $payload['bulb'] ?? 0,
                    $stall,
                    $feeMasterlist,
                    $fees_additional
                );

                return Inertia::render('Users/Applications/TableRental/Form', array_merge(
                    [
                        'tableRental' => $this->getSingleData($tableRental),
                    ],
                    $this->getCreatePayload(),
                    [
                        'total_payments' => $total_payments,
                    ]
                ))->withViewData(['step' => 1]);

            case 2:
                // 📎 Handle requirements
                return Inertia::render('Users/Applications/TableRental/Form', array_merge(
                    $this->getCreatePayload(),
                    [
                        'requirementsPayload' => $payload['requirements'] ?? [],
                    ]
                ))->withViewData(['step' => 2]);

            case 3:
                // ✍️ Contract signature step
                return Inertia::render('Users/Applications/TableRental/Form')
                    ->withViewData(['step' => 3]);

            case 4:
                // ✅ Final update and save
                $payload['fees_additional'] = $payload['fees'] ?? [];

                // Use only validated payload for update (avoid $request->all())
                $this->updateData($tableRental, $payload);

                return back()->with('success', 'Table rental application renewed successfully.');

            default:
                return back()->withErrors(['step' => 'Invalid step provided.']);
        }
    }

    private function updateData(?TableRental $tableRental, array $payload, ?TablesRequest $request = null)
    {
        $stallController = new StallsListController();
        $permitController = new PermitController();
        $requirementController = new RequirementController();

        // If stall_id is changed → old stall to available (e.g. 3), new stall to reserved (2)
        if (isset($payload['stall_id']) && $payload['stall_id'] != $tableRental->getOriginal('stall_id')) {
            // Update old stall (release it, example: status = 3 or whatever "available" is)
            if ($tableRental->getOriginal('stall_id')) {
                $stallController->statusUpdate(
                    new Request(['status' => 2]),
                    Stall::findOrFail((int) $tableRental->getOriginal('stall_id'))
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
                Stall::findOrFail((int) $tableRental->stall_id)
            );
        }

        Log::info('Payload for updateData', [
            'payload' => $payload,
            'tableRental'=>$tableRental
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


        $permitController->update($permitPayload, $tableRental->permit_id);
        // Update requirements if present
   
        if (isset($payload['requirements']) && is_array($payload['requirements'])) {

            $approvalRejection = ApprovalPermit::where('permit_id', $tableRental->permit_id)
                ->where('status', 2) // rejected
                ->first();
            Log::info('Handling requirements update for StallRental', [
                'stall_rental_id' => $tableRental->id,
                'permit_id' => $tableRental->permit_id,
                'approval_rejection' => $approvalRejection ? $approvalRejection->toArray() : null,
            ]);
            if ($approvalRejection) {
                $permitController->update([
                'remarks' => null, // back to pending
                'status' => 0, // back to pending
                'assign_to' => 2,
                'department_id' => $approvalRejection->department_id,
                'is_initial' => $tableRental->permits->is_initial,
                ], $tableRental->permit_id);
            } else {
                Log::warning("No rejection record found for permit_id {$tableRental->permit_id}");
            }

            $requirementController->deleteRequirements($tableRental->permit_id);

            foreach ($payload['requirements'] as $requirementData) {
                if (isset($requirementData['id'])) {
                $requirementController->updateRequirement($requirementData['id'], $requirementData);
                } else {
                $requirementData['permit_id'] = $tableRental->permit_id;
                $requirementController->addRequirements([$requirementData]);
                }
            }
            ApprovalPermit::where('permit_id', $tableRental->permit_id)
            ->where('status', 2) // rejected
            ->delete();
        }

        Log::info('Stall Rental updated successfully.', [
            'id' => $tableRental->id,
            'updated_data' => $tableRental->toArray()
        ]);
        
        return $tableRental->update($payload);
    }

    public function countMonthlyApplicants(){
        $rawCounts = TableRental::selectRaw('EXTRACT(MONTH FROM created_at) AS month, COUNT(*) AS total')
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
}
