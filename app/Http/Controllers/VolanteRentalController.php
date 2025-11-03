<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Volante;
use App\Models\User;
use App\Models\volanteRental;
use App\Models\StallType;
use App\Models\StallCategory;
use App\Models\FeeMasterlist;
use App\Models\RequirementChecklist;
use App\Models\Stall;
use App\Models\Permits;
use App\Models\ApprovalPermit;

use App\Http\Requests\VolanteRentalRequest;

use App\Http\Controllers\PermitController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ApprovalPermitController;
use App\Models\StallsCategories;

use App\Http\Controllers\StallsListController;
use App\Services\TwilioService;

use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class VolanteRentalController extends Controller
{

    public function index(Request $request): Response
    {   
        $user =  Auth::user();
        $data = [
            'filters' => $request->all('search', 'category'),
            'volanteRentals' => $this->getIndexData($request),
            'user' => $user,
            'stallsCategories' => StallsCategories::whereIn('id', [4, 5, 6])->get(),
        ];
        return Inertia::render($user->role_id === 3 ? 'Users/Applications/VolanteRental'
        : 'Admin/Applications/VolanteRental', $data);
    }

    public function edit(Request $request, Volante $volanteRental): Response|RedirectResponse
    {
        $user = Auth::user();
        if ($user->role_id !== 3 && $volanteRental->status === 1) {
            return redirect()->route('department.applications.volantes');
        }

        if (
            Str::contains($request->fullUrl(), 'renew') && !$volanteRental->expiringSoon ||
            Str::contains($request->fullUrl(), 'reupload') &&  $volanteRental->permits->status !== 3 ||  $volanteRental->permits->status === 3) {
                Log::warning('Attempted renewal for non-expiring stall rental', [
                    'stall_rental_id' => $volanteRental->id,
                ]);
                return back()->with('error', 'Stall rental application is not yet expiring!');
        }
        return Inertia::render('Users/Applications/Volante/Form', array_merge(
                ['volanteRental' => $this->getSingleData($volanteRental)],
                $this->getCreatePayload()
        ));
    }

    public function validate(VolanteRentalRequest $request)
    {
        $payload = $request->validated();
        $total_payments = 0;
        // Step 1 → calculate payment
        if (isset($payload['step']) && $payload['step'] == 1) {

            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($fees_additional) = $this->prepareFees($request, $stall);

            $total_payments = $this->calculateVolantePaymentFromFees(
                (int) $request->bulb,
                $stall,
                $fees_additional
            );
            return Inertia::render('Users/Applications/Volante/Form', 
                array_merge($this->getCreatePayload(), [
                    'total_payments' => $total_payments,
                ]))->withViewData(['step' => 1]);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 2) {
            return Inertia::render('Users/Applications/Volante/Form',
             array_merge($this->getCreatePayload(), [
                'requirementsPayload' => $payload['requirements'],
                ]))
                ->withViewData(['step' => 2]);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 3) {
            return Inertia::render('Users/Applications/Volante/Form')
                ->withViewData(['step' => 3]);
        }

       
        // Default fallback
        return back()->withErrors(['step' => 'Invalid step provided.']);
    }

    public function show(Request $request, Volante $volanteRental): RedirectResponse | Response
    {
        $user = Auth::user();

        // Only allow access if user is role 2 and permit is assigned to their department
        if ($user->role_id === 1 || $user->role_id === 2 && $volanteRental->permits && $volanteRental->permits->department_id == $user->department_id) {
            return Inertia::render('Department/Volante/Show', [
                'volanteRental' => $this->getSingleData($volanteRental),
            ]);
        }
        return Inertia::render('Users/Applications/Volante/Details', [
                'volanteRental' => $this->getSingleData($volanteRental),
        ]);
    }

     public function store(VolanteRentalRequest $request)
    {
        $payload = $request->validated();
        $total_payments = 0;
                // Step 1 → calculate payment
        if (isset($payload['step']) && $payload['step'] == 1) {

            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($fees_additional) = $this->prepareFees($request, $stall);
            $total_payments = $this->calculateVolantePaymentFromFees(
                (int) $request->bulb,
                $stall,
                $fees_additional
            );
            return Inertia::render('Users/Applications/Volante/Form', 
                array_merge($this->getCreatePayload(), [
                    'total_payments' => $total_payments,
                ]))->withViewData(['step' => 1]);
        }

        // Step 2 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 2) {
            return Inertia::render('Users/Applications/Volante/Form',
             array_merge($this->getCreatePayload(), [
                'requirementsPayload' => $payload['requirements'],
                ]))
                ->withViewData(['step' => 2]);
        }

        // Step 3 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 3) {
            return Inertia::render('Users/Applications/Volante/Form')
                ->withViewData(['step' => 3]);
        }

        // Step 4 → just return current props, no changes
        if (isset($payload['step']) && $payload['step'] == 4) {
            $payload['fees_additional'] = $payload['fees'];
            $this->addData($payload, $request);

            return back()->with('success', 'Market Volante application created successfully.');
        }
    
        // Default fallback
        return back()->withErrors(['step' => 'Invalid step provided.']);
        
    }

    public function create(): Response
    {
        return Inertia::render('Users/Applications/Volante/Form', $this->getCreatePayload());
    }

    public function update(VolanteRentalRequest $request, Volante $volanteRental) : RedirectResponse
    {
        $this->updateData($volanteRental, $request->all(), $request);
        return redirect()->route('applications.volantes.index')->with('success', 'Volante rental application updated successfully.');
    }

    public function approveVolante(Request $request, Volante $volanteRental): Response|RedirectResponse
    {

        $rentalDetails = $this->getSingleData($volanteRental);
        $approvalDetails = $this->approve($rentalDetails);
        return  back()->with('success',  'Market Volante approved successfully.');
    }

    public function rejectVolante(Request $request,  Volante $volanteRental): Response|RedirectResponse
    {
        Log::info('Rejecting VolanteRental', [
            'volanteRental' => $volanteRental
        ]);
 
        $this->disApprove($volanteRental, $request->all());
        return   back()->with('success', 'Volante rental rejected.');
    }
    
    public function indexApi(Request $request)
    {
        // $user = Auth::user();
        $user = [
            'id'=> 3,
            'name' => 'John Doe',
            'role_id' => 3
        ];
       
        Log::info('User: ', ['user' => $user]);
        $volanteRentals = $this->getIndexData($request);

        return response()->json([
            'volanteRentals' => $volanteRentals,
        ]);

    }

    public function editApi(Volante $volanteRental)
    {
      return response()->json(
            array_merge(
                ['volanteRental' => $this->getSingleData($volanteRental)],
                $this->getCreatePayload()
            )
        );

    }

    public function createApi()
    {
        return response()->json($this->getCreatePayload());

    }

    public function storeApi(VolanteRentalRequest $request)
     {  
        $payload = $request->validated();
        $total_payments = 0;
        // Step 1 → calculate payment
        if (isset($payload['step']) && $payload['step'] == 1) {
            $stall = Stall::with('stallsCategories')->findOrFail((int) $payload['stall_id']);
            list($fees_additional) = $this->prepareFees($request, $stall);
            $total_payments = $this->calculateVolantePaymentFromFees(
                (int) $request->bulb,
                $stall,
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
            list($fees_additional) = $this->prepareFees($request, $stall);

            $total_payments = $this->calculateVolantePaymentFromFees(
                (int) $request->bulb,
                $stall,
                $fees_additional
            );


            $payloadRequest = $payload;
            $payloadRequest['total_payment'] = $total_payments->total;
            $payloadRequest['fees_additional'] = json_encode($request->fees ?? []);

            // $payloadRequest = (object) $payloadRequest;

            Log::info('Final payload for stall rental creation', [
                'payload' => $payloadRequest,
            ]);

            $this->addData($payloadRequest);

            return response()->json([
                'step'    => 3,
                'message' => 'Market Volante application created successfully.',
                'data'    => $payloadRequest,
            ]);
        }


        return response()->json([
            'step' => null,
            'message' => 'Invalid step provided.',
        ], 400);
    }

    public function updateApi(VolanteRentalRequest $request, Volante $volanteRental)
    {
        Log::info('Update hit', [
            'volante_id' => $volanteRental->id,
            'request_all' => $request
        ]);
        return response()->json(
            array_merge(
                ['volanteRental' => $this->updateData($volanteRental, $request->all())],
            )
        );
    }

    public function approveApi(Volante $volanteRental)
    {
        $rentalDetails = $this->getSingleData($volanteRental);
        $approvalDetails = $this->approve($rentalDetails);
        return response()->json(['message' =>  $approvalDetails]);
    }

    public function rejectApi(Request $request, Volante $volanteRental)
    {
        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);
        $this->disApprove($volanteRental, $request->all());
        return response()->json(['message' => 'Volante rental rejected.', 'remarks' => $request->remarks]);
    }

    public function countMonthlyApplicants(){
        $rawCounts = Volante::selectRaw('EXTRACT(MONTH FROM created_at) AS month, COUNT(*) AS total')
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
        return Volante::query()->totalTodayApplicants();
    }

  private function approve($volanteRental) {
        $user = Auth::user();
        $permit = (object)$volanteRental['permits'];
        $vendor = (object)$volanteRental['vendor'];

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
            $volanteRentalModel = Volante::findOrFail($volanteRental['id']);
            
            Log::info('Approving TableRental', [
                'table_rental_id' => $volanteRentalModel->id,
                'status' => 1,
            ]);
            $this->updateData($volanteRentalModel, ['status' => 1]);

            $stallController = new StallsListController();
            // Update stall status to 'assigned' (3)
            $stallController->statusUpdate(
                new Request(['status' => 1]),
                Stall::findOrFail((int) $volanteRentalModel['stall_id'])
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
            'volanteRental' => $volanteRental,
            'permit' => $permit
        ];

    }


    private function disApprove($volanteRental, array $payload) {
        $user = Auth::user();
        $permit = (object)$volanteRental['permits'];
        $vendor = (object)$volanteRental['user'];
        $approvalPermitController = new ApprovalPermitController();

        $approvalPermitController->storeApproval(
            $volanteRental->permit_id, 
            $volanteRental->permits->department_id,
            $user->id,
            2
        );
        $volanteRentalModel = Volante::findOrFail($volanteRental['id']);
        $this->updateData($volanteRentalModel, ['status' => 3]);
        $permitController = new PermitController();
        $permitController->update([
                'status' => 2, //rejected
                'remarks' => $payload['remarks'],
                'assign_to' => 1, // 1 = user
                'is_initial' => false, // set to false when forwarded to user
            ], $volanteRentalModel->permit_id);
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

    private function prepareFees($request, $stall): array
    {

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

        return [ $fees];
    }

    private function calculateVolantePaymentFromFees(
        int $bulb,
        Stall $stall,
        array $fees,
        ?bool $isNotPaid = null, // make it optional
    ) {
        $total = 0;
        $breakdown = [];

        // Ensure fees is always an array
        if (is_string($fees)) {
            $fees = json_decode($fees, true);
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

    private function getIndexData(Request $request) {
        $user = Auth::user();

        $query = Volante::query()->filter($request->only('search', 'category'));

        if ($user && $user->role_id === 3) {
            $query->myApplication($user->id);
        }

        if ($user && $user->role_id === 2) {
            $query->myApplicationUnderMyDep($user->department_id);
        }

        return $query->paginate(10)
            ->through(function ($volanteRental) use ($user) {
                $stall = $volanteRental->stalls;
                $stallCategory = $stall ? $stall->stallsCategories : null;
                $permits = $volanteRental->permits;

                // penalty check
                $startDate = $volanteRental->started_date; 

                $isCurrentQuarterPaid = false;

                if ($startDate && !$startDate->isSameMonth(now())) {
                    $isCurrentQuarterPaid = $volanteRental->payments()
                        ->inCurrentQuarter()
                        ->where('volantes_id', $volanteRental->id)
                        ->exists();
                }
                        
                // fees
                list($fees_additional) = $this->prepareFees($volanteRental, $stall);

                $total_payments = $this->calculateVolantePaymentFromFees(
                    (int) $volanteRental->bulb,
                    $stall,
                    $fees_additional
                );

               $currentPayment = $volanteRental->payments()
                    ->withVolanteRental()
                    ->thisMonth()
                    ->get();

                return [
                    'vendor' => $volanteRental->user,
                    'id' => $volanteRental->id,
                    'name' => $volanteRental->business_name,
                    'status' => $volanteRental->status,
                    'start_date' => $volanteRental->started_date ? Carbon::parse($volanteRental->started_date) : null,
                    'end_date' => $volanteRental->started_date ? Carbon::parse($volanteRental->end_date) : null,
                    'acknowledgeContract' => $volanteRental->acknowledgeContract,
                    'stalls' => $stall ? [
                        'id' => $stall->id,
                        'name' => $stall->name,
                        'size' => $stall->size,
                        'status' => $stall->status,
                        'area_of_sqr_meter' => $stall->area_of_sqr_meter,
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
                        'assign_to' => $permits->assign_to,
                        'is_initial' => $permits->is_initial,
                        'created_at' => $permits->created_at ? Carbon::parse($permits->created_at) : null,
                        'issued_date' => $permits->issued_date ? Carbon::parse($permits->issued_date) : null,
                        'expiry_date' => $permits->expiry_date ? Carbon::parse($permits->expiry_date) : null
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
                    'paymentDetails' => $total_payments,
                    'quarterly_payment' => $total_payments->total,
                    'next_payment_due' => $this->nextStartDate($permits->issued_date),
                    'penalty' => $isCurrentQuarterPaid ? ($total_payments->total * 0.12) : 0,
                    'currentPayment' => $currentPayment,
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

    private function getSingleData(Volante $volanteRental) {
        $user = Auth::user();
    
        $stall = $volanteRental->stalls;
        if ($user->role_id === 2 && $stall && $volanteRental->permits && $volanteRental->permits->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }
        $stallCategory = $stall ? $stall->stallsCategories : null;
        $permits = $volanteRental->permits;
    
        // penalty check
        $startDate = $volanteRental->started_date; 

        $isCurrentQuarterPaid = false;

        if ($startDate && !$startDate->isSameMonth(now())) {
            $isCurrentQuarterPaid = $volanteRental->payments()
                ->inCurrentQuarter()
                ->where('volantes_id', $volanteRental->id)
                ->exists();
        }

        // fees
        list($fees_additional) = $this->prepareFees($volanteRental, $stall);

        $total_payments = $this->calculateVolantePaymentFromFees(
            (int) $volanteRental->bulb,
            $stall,
            $fees_additional,
            $isCurrentQuarterPaid
        );

        $currentPayment = $volanteRental->payments()
            ->withVolanteRental()
            ->thisMonth()
            ->get();
        return [
            'vendor' => $volanteRental->user,
            'id' => $volanteRental->id,
            'name' => $volanteRental->business_name,
            'location' => $volanteRental->location,
            'duration' => $volanteRental->duration,
            'quantity' => $volanteRental->quantity,
            'status' => $volanteRental->status,
            'bulb' => $volanteRental->bulb,
            'fees_additional' => json_decode($volanteRental->fees_additional, true) ?? [],
            'area_of_sqr_meter' => $volanteRental->area_of_sqr_meter,
            'start_date' => $volanteRental->started_date ? Carbon::parse($volanteRental->started_date) : null,
            'end_date' => $volanteRental->end_date ? Carbon::parse($volanteRental->end_date) : null,
            'stalls' => $stall ? [
                'id' => $stall->id,
                'name' => $stall->name,
                'size' => $stall->size,
                'status' => $stall->status,
                'area_of_sqr_meter' => $stall->area_of_sqr_meter,
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
                'permit_type' => $permits->type,
                'type' => $permits->type === 1 ? 'New' : 'Renewal',
                'permit_number' => $permits->permit_number,
                'department_id' => $permits->department_id,
                'status' => $permits->status,
                'remarks' => $permits->remarks,
                'assign_to' => $permits->assign_to,
                'is_initial' => $permits->is_initial,
                'issued_date' => $permits->issued_date ? Carbon::parse($permits->issued_date) : null,
                'expiry_date' => $permits->expiry_date ? Carbon::parse($permits->expiry_date) : null
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
            'paymentDetails' => $total_payments,
            'quarterly_payment' => $total_payments->total,
            'next_payment_due' => $this->nextStartDate($permits->issued_date),
            'penalty' => $isCurrentQuarterPaid ? ($total_payments->total * 0.12) : 0,
            'currentPayment' => $currentPayment,
            'current_payment' => count($currentPayment) > 0 ? 'Paid' : 'Not Paid',
            'expiringSoon' => $volanteRental->expiringSoon,
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
            'payments_history' => $volanteRental->payments()
                ->withVolanteRental()->get()
        ];  
    }

    private function nextStartDate($startDate = null)
    {
        if (empty($startDate)) {
            return null;
        }

        $date = \Carbon\Carbon::parse($startDate);

        // Get the start of the current month
        $monthStart = $date->copy()->startOfMonth();

        // Move to the start of the next month
        $nextMonthStart = $monthStart->copy()->addMonth();

        // Set the day to 20th
        $nextMonth20th = $nextMonthStart->copy()->day(20);

        return $nextMonth20th->toDateString();
    }

    private function addData(array $payload, VolanteRentalRequest $request)
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
        $volanteRental = Volante::create($payload);
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
            'volanteRental' => $volanteRental,
            'permitDetails' => $permitDetails,
            'requirements' => $requirements,
        ];
    }

    private function getCreatePayload(){
        return [
            'stallTypesAll' => Stall::with('stallsCategories')
                ->whereIn('stall_category_id', [4, 5, 6])
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
                            'fee' => FeeMasterlist::find($data->stallsCategories->fee_masterlist_id),
                        ] : null,
                        'coordinates' => $data->coordinates ? json_decode($data->coordinates, true) : null,
                        'size' => $data->size ? json_decode($data->size, true) : null,
                        'status' => $data->status,
                    ];
                }),
            'stallTypes' => Stall::with('stallsCategories')
                ->whereIn('stall_category_id', [4, 5, 6])->where('status', 2)
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

   private function updateData(?Volante $volanteRental, array $payload, ?VolanteRentalRequest $request = null)
    {
        $stallController = new StallsListController();
        $permitController = new PermitController();


        // If stall_id is changed → old stall to available (e.g. 3), new stall to reserved (2)
        if (isset($payload['stall_id']) && $payload['stall_id'] != $volanteRental->getOriginal('stall_id')) {
            // Update old stall (release it, example: status = 3 or whatever "available" is)
            if ($volanteRental->getOriginal('stall_id')) {
                $stallController->statusUpdate(
                    new Request(['status' => 2]),
                    Stall::findOrFail((int) $volanteRental->getOriginal('stall_id'))
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
                Stall::findOrFail((int) $volanteRental->stall_id)
            );
        }

        Log::info('Payload for updateData', [
            'payload' => $payload,
            'volanteRental'=>$volanteRental
        ]);
        unset($payload['step']);
        $permitPayload = $payload;


        if(isset($payload['status']) && $payload['status'] == 3) {
            $permitPayload['status'] = 3;
            unset($payload['status']);// keep table rental status as is (3 = rejected)
        }
        if(isset($payload['status']) && $payload['status'] == 1) {
            $permitPayload['status'] = 1;
             unset($payload['status']);// keep table rental status as is (3 = rejected)
        }
        if(isset($payload['type']) && $payload['type'] === 2) {
            $permitPayload['issued_date'] = null;
            $permitPayload['expiry_date'] = null;
            $permitPayload['assign_to'] = 2;
            $permitPayload['is_initial'] = true;
            $permitPayload['department_id'] = 2;
            $permitPayload['status'] = 2;
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


        $permitController->update($permitPayload, $volanteRental->permit_id);
        // Update requirements if present
   
        if (isset($payload['requirements']) && is_array($payload['requirements'])) {
            $requirementController = new RequirementController();

            $approvalRejection = ApprovalPermit::where('permit_id', $volanteRental->permit_id)
                ->where('status', 2) // rejected
                ->first();
            Log::info('Handling requirements update for StallRental', [
                'stall_rental_id' => $volanteRental->id,
                'permit_id' => $volanteRental->permit_id,
                'approval_rejection' => $approvalRejection ? $approvalRejection->toArray() : null,
            ]);
            if ($approvalRejection) {
                $permitController->update([
                'status' => 0, // back to pending
                'assign_to' => 2,
                'department_id' => $approvalRejection->department_id,
                'is_initial' => $volanteRental->permits->is_initial,
                ], $volanteRental->permit_id);
            } else {
                Log::warning("No rejection record found for permit_id {$volanteRental->permit_id}");
            }

            $requirementController->deleteRequirements($volanteRental->permit_id);

            foreach ($payload['requirements'] as $requirementData) {
                if (isset($requirementData['id'])) {
                $requirementController->updateRequirement($requirementData['id'], $requirementData);
                } else {
                $requirementData['permit_id'] = $volanteRental->permit_id;
                $requirementController->addRequirements([$requirementData]);
                }
            }
            ApprovalPermit::where('permit_id', $volanteRental->permit_id)
            ->where('status', 2) // rejected
            ->delete();
        }

        Log::info('Stall Rental updated successfully.', [
            'id' => $volanteRental->id,
            'updated_data' => $volanteRental->toArray()
        ]);
        
        return $volanteRental->update($payload);
    }


}
