<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\SubAdminType;
use App\Models\UserType;
use App\Models\Gender;
use App\Models\Department;
use App\Models\StallRental;
use App\Models\Volante;
use App\Models\Business;
use App\Models\Stall;
use App\Models\TableRental;
use App\Actions\Fortify\UpdateUserProfileInformation;

 
use App\Http\Requests\UserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

use App\Notifications\CustomVerifyEmail;

class UserController extends Controller
{
    protected static ?string $password;

    public function index(Request $request): Response
    {
        $user =  Auth::user();

        if($user->role_id === 3) {
            return Inertia::render('Users/Dashboard', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->getNameAttribute(),
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'address' => $user->address,
                    'birthDay' => Carbon::parse($user->birthDay),
                    'status' => $user->status,
                    'email_verified_at' => $user->email_verified_at,
                    'gender_id' => $user->gender_id,
                    'roles' => $user->role,
                    'genders' => $user->gender,
                    'subAdminType'=>$user->subAdminType,
                    'userType'=>$user->userType,
                    'departments'=>$user->departments
                ],
                'myTopApply' => $this->getMyTopApply(),
                'myApplicationCount' => $this->myApplicationCount(),
            ]);
        }
        else {
            if ($user->role_id === 2) {
            return Inertia::render('Department/Dashboard', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->getNameAttribute(),
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'address' => $user->address,
                    'birthDay' => Carbon::parse($user->birthDay),
                    'status' => $user->status,
                    'gender_id' => $user->gender_id,
                    'roles' => $user->role,
                    'genders' => $user->gender,
                    'subAdminType' => $user->subAdminType ? [
                        'id' => $user->subAdminType->id,
                        'name' => $user->subAdminType->name,
                        'departments' => $user->subAdminType->departments
                    ] : null,
                    'userType' => $user->userType,
                ],
                'myTopApply' => $this->getDepartmentApplicationForApprove(),
                'stalls' => Stall::all(),
                'counts' => $this->departmentApplicationCount()
            ]);
            } else {

            $data = Inertia::render('Admin/Users/Index', [
                'filters' => Request::all('search', 'role', 'gender', 'user_type_id', 'subAdminType', 'department_id'),
                'userType' => UserType::all(),
                'subAdminType' => SubAdminType::all(),
                'roles' => Role::all(),
                'genders' => Gender::all(),
                'users' => User::query()
                ->notMe($user ? $user->id : 1)
                ->filter(Request::only('search', 'role', 'gender', 'user_type_id', 'subAdminType', 'department_id'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->getNameAttribute(),
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'address' => $user->address,
                    'birthDay' => Carbon::parse($user->birthday),
                    'status' => $user->email_verified_at,
                    'gender_id' => $user->gender_id,
                    'roles' => $user->role,
                    'genders' => $user->gender,
                    'subAdminType'=>$user->subAdminType,
                    'userType'=>$user->userType,
                    'joinDate'=>$user->created_at,
                ]),
            ]);
                     Log::info('Accessing user index');
            // if(!Request::has('role')) {
            //     abort(400, 'Missing required query parameter: role');
            // }
            return $data;
            }
        }
  
    }

    public function apiIndex(Request $request)
    {
        $user =  Auth::user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->getNameAttribute(),
                'email' => $user->email,
                'mobile' => $user->mobile,
                'address' => $user->address,
                'birthDay' => Carbon::parse($user->birthDay),
                'status' => $user->email_verified_at !== null,
                'gender_id' => $user->gender_id,
                'roles' => $user->role,
                'genders' => $user->gender,
                'subAdminType'=>$user->subAdminType,
                'userType'=>$user->userType
            ],
            'myTopApply' => $this->getMyTopApply()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'userType' => UserType::all(),
            'subAdminType' => SubAdminType::all(),
            'genders' => Gender::all(),
            'departments' => Department::all(),
        ]);
    }

    public function apiCreate()
    {
        return response()->json([
            'userType' => UserType::all(),
            'subAdminType' => SubAdminType::all(),
            'roles' => Role::all(),
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'address' => $user->address,
                'birthDay' => Carbon::parse($user->birthDay),
                'status' => $user->email_verified_at !== null,
                'gender_id' => $user->gender_id,
                'role' => $user->role->name,
                'role_id' => $user->role_id,
                'sub_admin_type_id'=>$user->sub_admin_type_id,
                'user_type_id'=>$user->user_type_id,
                'department_id'=>$user->department_id,
            ]
        ]);
    }

    public function register(UserRequest $request): RedirectResponse
    {
        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'middle_name' => $request['middle_name'],
            'role_id' => $request['role_id'],
            'gender_id' => $request['gender_id'],
            'department_id' => $request['department_id'],
            'address' => $request['address'],
            'birthday' => $request['birthday'],
            'mobile' => $request['mobile'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

          Log::info('Login attempt', [
            'user_type_id' => $user 
        ]);
        $user->notify(new CustomVerifyEmail);
        return redirect()->back()->with('success', 'User registered successfully.');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create([
            'user_type_id' => $request['user_type_id'],
            'role_id' => $request['role_id'],
            'gender_id' => $request['gender_id'],
            'department_id' => $request['department_id'],
            'department_position' => 3,
            'sub_admin_type_id' => $request['sub_admin_type_id'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'middle_name' => $request['middle_name'] ?? '',
            'address' => $request['address'],
            'birthday' => $request['birthDay'],
            'email' => $request['email'],
            'mobile' => $request['mobile'],
            'password' => static::$password ??= Hash::make('MarketISAdmin'),
        ]);
        $roleName = $this->handleRole($request['role_id'],$request['user_type_id']);
        $user->notify(new CustomVerifyEmail);
        return redirect()->route('admin.users.all', ['role' => $roleName])->with('success', 'User created successfully.');
    }

    public function apiStore(UserRequest $request)
    {
         $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'middle_name' => $request['middle_name'],
            'role_id' => $request['role_id'],
            'gender_id' => $request['gender_id'],
            'department_id' => $request['department_id'],
            'address' => $request['address'],
            'birthday' => $request['birthday'],
            'mobile' => $request['mobile'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

          Log::info('Login attempt', [
            'user_type_id' => $user 
        ]);
        $user->notify(new CustomVerifyEmail);
        return response()->json([$user]);
    }


    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'userType' => UserType::all(),
            'subAdminType' => SubAdminType::all(),
            'roles' => Role::all(),
            'genders' => Gender::all(),
            'departments' => Department::all(),
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'address' => $user->address,
                'birthDay' => Carbon::parse($user->birthday),
                'status' => $user->email_verified_at !== null,
                'gender_id' => $user->gender_id,
                'role' => $user->role->name,
                'role_id' => $user->role_id,
                'sub_admin_type_id'=>$user->sub_admin_type_id,
                'user_type_id'=>$user->user_type_id,
                'department_id'=>$user->department_id,
            ]
        ]);
    }

    public function editApi(User $user): RedirectResponse
    {
         Log::info('Login attempt', [
            'user_type_id' => $user
        ]);
        return response()->json([
            'userType' => UserType::all(),
            'subAdminType' => SubAdminType::all(),
            'roles' => Role::all(),
            'genders' => Gender::all(),
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'address' => $user->address,
                'birthDay' => Carbon::parse($user->birthday),
                'status' => $user->email_verified_at !== null,
                'gender_id' => $user->gender_id,
                'sub_admin_type_id'=>$user->sub_admin_type_id,
                'user_type_id'=>$user->user_type_id,
                'department_id'=>$user->department_id,
            ]
        ]);
    }

    private function handleRole($role, $userType)
    {
        switch ($role) {
            case 3:
                if($userType === 3){
                    return 'inspector';
                }
               return 'user';
            case 'inspector':
                return 'Handling inspector logic';
            case 2:
                return 'sub-admin';
            
        }
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $roleName = $this->handleRole($user->role_id,$user->user_type_id);
        $user->update(
          [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $user->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'birthDay' => Carbon::parse($user->birthDay),
            'status' => $request->email_verified_at !== null,
            'gender_id' => $request->gender_id,
            'sub_admin_type_id'=>$request->sub_admin_type_id,
            'user_type_id'=>$request->user_type_id,
            'department_id'=>$request->department_id,
          ] 
        );
        return redirect()->route('admin.users.all', ['role' => $roleName])->with('success', 'User updated successfully.');
    }

    public function approverOrRejected(User $user): RedirectResponse
    {
        $roleName = $this->handleRole($user->role_id,$user->user_type_id);

        $status = $user->status;

        $statusValue = false;

        if(!$status) {
            $statusValue = true;
        }
        $user->update(
          [
            'status' => $statusValue,
          ] 
        );
        return redirect()->route('admin.users.all', ['role' => $roleName])->with('success', 'User status updated successfully.');
    }

    public function approverOrRejectedAPI(User $user)
    {
        $roleName = $this->handleRole($user->role_id,$user->user_type_id);

        $status = $user->status;

        $statusValue = false;

        if(!$status) {
            $statusValue = true;
        }
        $user->update(
          [
            'status' => $statusValue,
          ] 
        );
        return response()->json([
            'message' => 'User profile updated successfully.',
        ]);
    }

    public function updateApi(UserRequest $request, User $user)
    {
        // Reuse Fortify's UpdateUserProfileInformation action
       $user->update(
          [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $user->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'birthDay' => Carbon::parse($user->birthDay),
            'status' => $request->email_verified_at !== null,
            'gender_id' => $request->gender_id,
            'sub_admin_type_id'=>$request->sub_admin_type_id,
            'user_type_id'=>$request->user_type_id,
            'department_id'=>$request->department_id,
          ] 
        );

        return response()->json([
            'message' => 'User profile updated successfully.',
        ]);
    }

    public function getDepartmentApplicationForApprove(){
        $user = Auth::user();
        $department_id = $user->department_id;
        $stall =  StallRental::myApplicationUnderMyDep($department_id)->latest()->first();;
        $volante = Volante::myApplicationUnderMyDep($department_id)->latest()->first();;
        $tables = TableRental::myApplicationUnderMyDep($department_id)->latest()->first();;
        return collect([
            $stall ? [
                'type' => 'Stall Leasing',
                'created_at' => $stall ? $stall->created_at : null,
                'status' => $stall ? $stall->permits->status : null,
                'stall' => $stall ? $stall->stalls->name : null,
                'data' => $stall ? $stall : null, 
                'vendor' => $stall ? $stall->users : null,
            ] : null,
            $volante ? [
                'type' => 'Market Volante',
                'created_at' => $volante->created_at,
                'status' => $volante->permits->status,
                'stall' => $volante->stalls->name,
                'data' => $volante ? $volante : null,
                'vendor' => $stall ? $stall->users : null,
            ] : null,
            ],
            $tables ? [
                'type' => 'Table Spaces',
                'created_at' => $tables->created_at,
                'status' => $tables->permits->status,
                'stall' => $tables->stalls->name,
                'data' => $tables ? $tables : null,
                'vendor' => $stall ? $stall->users : null,
            ] : null
            )->filter() // remove nulls
            ->sortByDesc('created_at') // sort latest first
            ->values();
    }

     private function departmentApplicationCount(){
        $user = Auth::user();
        Log::info('Fetching application counts for user', ['user_id' => $user->id]);

        $dashboardData = [
            'totalStall' => $user->totalStallForDepApproval($user->department_id),
            'totalVolante' => $user->totalVolanteForDepApproval(),
            'totalTable' => $user->totalTableForDepApproval(),
            'total' => $user->totalApprovedApplications(),
        ];
        return $dashboardData;
    }

    public function getMyTopApply(){
        $user = Auth::user();
        $userId = $user->id;
        $stall = StallRental::where('user_id', $userId)->latest()->first();
        $volante = Volante::where('user_id', $userId)->latest()->first();
        $permit = Business::where('user_id', $userId)->latest()->first();

        return collect([
            $stall ? [
                'type' => 'Stall Rental',
                'created_at' => $stall->created_at,
                'status' => $stall->permits->status,
                'stall' => $stall->stalls->name,
                'data' => $stall,
            ] : null,
            $volante ? [
                'type' => 'Volante',
                'created_at' => $volante->created_at,
                'status' => $volante->permits->status,
                // 'stall' => $stall->stalls->name,
                'data' => $volante,
            ] : null,
            $permit ? [
                'type' => 'Business Permit',
                'created_at' => $permit->created_at,
                'status' => $permit->status,
                'data' => $permit,
            ] : null,
            ])->filter() // remove nulls
            ->sortByDesc('created_at') // sort latest first
            ->values();
    }

    private function myApplicationCount(){
        $user = Auth::user();
        Log::info('Fetching application counts for user', ['user_id' => $user->id]);

        $dashboardData = [
            'total_applications' => $user->getTotalApplicationsCount(),
            'volante_permits' => $user->getVolantePermitsCount(),
            'stall_rentals' => $user->getStallRentalsCount(),
            'pending_permits' => $user->getPendingPermitsCount(),
        ];
        return $dashboardData;
    }
}
