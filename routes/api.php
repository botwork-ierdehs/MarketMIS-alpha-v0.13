<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Department;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Rules\Password;
use Illuminate\Validation\Rules;
use App\Http\Controllers\StallRentalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\StallsListController;
use App\Http\Controllers\StallTypeController;
use App\Http\Controllers\RequirementListController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\SubAdminTypeController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\VolanteRentalController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\PaymentController;

use App\Http\Middleware\ValidateEmailAndAge;
use App\Http\Middleware\ValidateUser;
use App\Http\Middleware\BusinessPermitMiddleware;
use App\Http\Middleware\ValidateVolanteDates;

use App\Notifications\CustomVerifyEmail;
use App\Http\Controllers\BusinessController;

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        return response()->json(['message' => 'Logged in', 'user' => Auth::user()]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
});

Route::middleware('auth:sanctum')->get('/users', function () {
    return response()->json([
        'message' => 'This is a test dashboard route',
        'user' => Auth::user()
    ]);
});

Route::post('/register', function (Request $request) {
   
    $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'integer'],
            'gender_id' => ['required', 'integer'],
            'department_id' => ['integer'],
            'mobile' => ['required', 'string', 'max:12'],
            'address' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', new Password],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

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

    Auth::login($user);
        $user->notify(new CustomVerifyEmail);

    return response()->json([
        'message' => 'Registration successful',
        'user' => $user,
    ]);
});

// Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
//     ->middleware(['signed'])
//     ->name('verification.verify');


// Route::post('admin/users/email/verification-send', [UserController::class, 'emailSend'])->name('admin.users.email');
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed'])
    ->name('verification.verify');
// Route::get('users', [UserController::class, 'apiIndex']);
Route::get('users/user-type', [UserTypeController::class, 'apiIndex']);
Route::get('users/sub-admin-type', [SubAdminTypeController::class, 'apiIndex']);
// Route::put('admin/users/email/verification3/{user}', [UserController::class, 'emailSend'])->name('admin.users.email');

Route::get('/users/create', [UserController::class, 'apiCreate']);
Route::put('/users/approve/{user}', [UserController::class, 'approverOrRejectedAPI']);

Route::get('users/{user}/edit', [UserController::class, 'editApi'])
    ->name('users.edit');
Route::put('users/{user}/update', [UserController::class, 'updateApi'])
->name('users.update');

// department
Route::get('/departments', [DepartmentController::class, 'apiIndex']);

//stalls
Route::get('system-setting/stalls', [StallsListController::class, 'apiIndex'])->name('stall;s');
Route::get('system-setting/stalls/{id}', [StallsListController::class, 'getSingleRecord'])->name('stalls.single');
//volante-type
//stall-type
Route::get('system-setting/stall-type', [StallTypeController::class, 'apiIndex']);
Route::get('system-setting/requirements', [RequirementListController::class, 'apiIndex']);
Route::get('admin/applications/business-permit', [BusinessController::class, 'indexApi']);
Route::middleware(BusinessPermitMiddleware::class)->group(function () {
    Route::post('admin/applications/business-permit', [BusinessController::class, 'createAPI']);
});
Route::get('department/applications/stall-rental-permits', [StallRentalController::class, 'indexApi'])->name('department.applications.index');
Route::get('department/applications/stall-rental-permits/create', [StallRentalController::class, 'createAPI'])->name('department.applications.create');
Route::post('department/applications/stall-rental-permits', [StallRentalController::class, 'storeAPI'])
    ->name('department.applications.store');
Route::get('department/applications/stall-rental-permits/{stallRental}/edit', [StallRentalController::class, 'editAPI'])->name('department.applications.edit');
Route::put('department/applications/stall-rental-permits/{stallRental}/update', [StallRentalController::class, 'updateAPI'])->name('department.applications.update');
Route::get('department/applications/stall-rental-permits/{stallRental}', [StallRentalController::class, 'showApi'])->name('department.applications.show');

Route::get('admin/applications/volantes', [VolanteRentalController::class, 'indexApi'])->name('admin.applications.volantes.index');
Route::get('admin/applications/volantes/create', [VolanteRentalController::class, 'createApi'])->name('admin.applications.volantes.create');
Route::post('admin/applications/volantes', [VolanteRentalController::class, 'storeApi'])
    ->name('admin.applications.volantes.store');
Route::get('admin/applications/volantes/{volanteRental}/edit', [VolanteRentalController::class, 'editApi'])->name('admin.applications.volantes.edit');
Route::put('admin/applications/volantes/{volanteRental}/update', [VolanteRentalController::class, 'updateApi'])->name('admin.applications.volantes.update');
Route::put('admin/applications/volantes/{volanteRental}/approve', [VolanteRentalController::class, 'approveApi'])->name('admin.applications.volantes.approve');
Route::put('admin/applications/volantes/{volanteRental}/reject', [VolanteRentalController::class, 'rejectApi'])->name('admin.applications.volantes.reject');

Route::get('admin/applications/table-rental', [TablesController::class, 'indexApi'])->name('admin.applications.table.index');
Route::get('admin/applications/table-rental/create', [TablesController::class, 'createApi'])->name('admin.applications.table.create');
Route::post('admin/applications/table-rental', [TablesController::class, 'storeApi'])
    ->name('admin.applications.tables.store');
Route::get('admin/applications/table-rental/{tableRental}/edit', [TablesController::class, 'editApi'])->name('admin.applications.table.edit');
Route::put('admin/applications/table-rental/{tableRental}/update', [TablesController::class, 'updateApi'])->name('admin.applications.table.update');
Route::put('admin/applications/table-rental/{tableRental}/approve', [TablesController::class, 'approveApi'])->name('admin.applications.table.approve');
Route::get('admin/applications/table-rental/{tableRental}', [TablesController::class, 'showApi'])->name('admin.applications.table.show');

Route::post('admin/table-rental/payment', [PaymentController::class, 'rentalPayment']);
