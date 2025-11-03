<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Business;
use App\Models\RequirementChecklist;
use App\Http\Requests\BusinessPermitRequest;

use App\Http\Controllers\PermitController;
use App\Http\Controllers\RequirementController;

use App\Http\Resources\BusinessResource;

use Carbon\Carbon;

class BusinessController extends Controller
{
    public function index(Request $request): Response
    {   
        $user =  Auth::user();

        $data = $user->role_id === 3 ? [
            'filters' => $request->all('search'),
            'business' => Business::query()
                ->filter($request->only('search'))
                ->myApplication($user->id)
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($data) => [
                    'id' => $data->id,
                    'name' => $data->name,
                    'trade_or_franchise_name' => $data->trade_or_franchise_name,
                    'business_address' => $data->business_address,
                    'business_phone' => $data->business_phone,
                    'business_email' => $data->business_email,
                    'business_telephone' => $data->business_telephone,
                    'area_of_sqr_meter' => $data->area_of_sqr_meter,
                    'permit_id' => $data->id,
                    'permitDetails' => $data->permits,
                    'user_id' => $data->id,
                    'userDetails' => $data->user
                ]),
        ]
        : [
            'filters' => $request->all('search'),
            'business' => Business::query()
                ->filter($request->only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($data) => [
                    'id' => $data->id,
                    'name' => $data->name,
                    'trade_or_franchise_name' => $data->trade_or_franchise_name,
                    'business_address' => $data->business_address,
                    'business_phone' => $data->business_phone,
                    'business_email' => $data->business_email,
                    'business_telephone' => $data->business_telephone,
                    'area_of_sqr_meter' => $data->area_of_sqr_meter,
                    'permits' => $data->permits,
                    'user' => $data->user
                ]),
        ];
        return Inertia::render($user->role_id === 3 ? 'Users/Applications/Business'
        : 'Admin/Applications/Business', $data);
    }

    public function countMonthlyApplicants(){
        $rawCounts = Business::selectRaw('EXTRACT(MONTH FROM created_at) AS month, COUNT(*) AS total')
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
        return Business::query()->totalTodayApplicants();
    }

    public function indexApi(Request $request)
    {
        $filters = $request->only(['search']);

        $businesses = Business::with([
                'user.role',
                'user.gender',
                'permitDetails.requirements.requirementDetails',
                'permitDetails.formDetails',
                'permitDetails.payments'
            ])
            ->filter($filters)
            ->paginate(10)
            ->withQueryString(); 

        return response()->json([
            'data' => BusinessResource::collection($businesses),
            'links' => [
                'first' => $businesses->url(1),
                'last' => $businesses->url($businesses->lastPage()),
                'prev' => $businesses->previousPageUrl(),
                'next' => $businesses->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $businesses->currentPage(),
                'from' => $businesses->firstItem(),
                'last_page' => $businesses->lastPage(),
                'path' => $businesses->path(),
                'per_page' => $businesses->perPage(),
                'to' => $businesses->lastItem(),
                'total' => $businesses->total(),
            ],
            'filters' => $filters
        ]);

        // return Inertia::render('Business/Index', [
        //     'businesses' => BusinessResource::collection($businesses),
        //     'filters' => $filters,
        // ]);
    }

    public function createAPI(BusinessPermitRequest $request)
    {
        $user = Auth::user();

        $permitController = new PermitController();
        $requirementController = new RequirementController();
        $formDetailsController = new FormDetailsController();
        $checklist = new RequirementChecklist();
        
        $payload = $request->validated();
        
        $formDetails = $formDetailsController->addBusinessPermit($payload);
   

        $permitDetails = $permitController->addBusinessPermit([
            'form_detail_id' => $formDetails->id,
            'type' => $payload->type,
        ]);
        
        $businessPermit = Business::create(
         collect($payload)->merge([
            'permit_id' => $permitDetails->id,
            'user_id' => 2,
        ])->toArray());
 
        $requirements = $requirementController->addRequirements(array_map(function ($item) use ($permitDetails) {
            $item['permit_id'] = $permitDetails->id;
            return $item;
        }, $payload['requirements']));



        return response()->json([
            'user' => $user,
            'requirements' => $checklist->requirementsList(),
            'data' => [
                $formDetails,
                $permitDetails,
                $businessPermit,
                $requirements,
            ]
        ]);
    }
}
