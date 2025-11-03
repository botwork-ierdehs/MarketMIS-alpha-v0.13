<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Notifications\CustomResetPassword;
use App\Models\ApprovalPermit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthday',
        'address',
        'role_id',
        'gender_id',
        'department_id',
        'user_type_id',
        'department_position_id',
        'mobile',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function departments(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function departmentPositions(): BelongsTo
    {
        return $this->belongsTo(DepartmentPosition::class, 'department_position_id');
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function subAdminType(): BelongsTo
    {
        return $this->belongsTo(SubAdminType::class);
    }

    public function volantes()
    {
        return $this->hasMany(Volante::class, 'user_id');
    }

    public function stallRentals()
    {
        return $this->hasMany(StallRental::class, 'user_id');
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function scopeWhereRole($query, $role)
    {
        switch ($role) {
            case 'admin': return $query->where('role_id', 1);
            case 'vendor': return $query->where('role_id', 3)->where('user_type_id', '=', null);
            case 'sub-admin': return $query->where('role_id', 2);
            case 'inspector': return $query->where('role_id', 3)->where('user_type_id', 3);
        }
    }

    public function scopeNotMe($query, $id)
    {
        return $query->where('id', '!=', $id);
    }

    public function scopeAdmin($query, $id)
    {
        if($id === 1){
            return $query->where('id','!=', $id);
        }
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        })->when($filters['gender'] ?? null, function ($query, $gender) {
            $query->where('gender_id',$gender);
        })->when($filters['user_type_id'] ?? null, function ($query, $userType) {
            $query->where('user_type_id',$userType);
        })->when($filters['sub_admin_type_id'] ?? null, function ($query, $subAdminType) {
            $query->where('sub_admin_type_id',$subAdminType);
        })->when($filters['department_id'] ?? null, function ($query, $department_id) {
            $query->where('department_id',$department_id);
        });
    }

    public function sendPasswordResetNotification($token)
    {
        // This will automatically use getEmailForPasswordReset() inside the notification
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Get the email address where password reset links are sent.
     * You can customize this method if needed.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
        
        // Example customizations:
        // return $this->recovery_email ?? $this->email; // Use recovery email if available
        // return strtolower(trim($this->email)); // Ensure email is lowercase and trimmed
    }

    // Count Methods
    public function getTotalApplicationsCount()
    {
        return $this->volantes()->count() + $this->stallRentals()->count();
    }

    public function getVolantePermitsCount()
    {
        return $this->volantes()->count();
    }

    public function getStallRentalsCount()
    {
        return $this->stallRentals()->count();
    }

    public function getPendingPermitsCount()
    {
        $pendingVolante = $this->volantes()->where('status', 0)->count();
        \Log::info('Calculating pending permits for user', ['user_id' => $pendingVolante]);
        $pendingStalls = $this->stallRentals()->where('status', 0)->count();
                \Log::info('Calculating pending permits for user', ['user_id' => $pendingStalls]);

        return $pendingVolante + $pendingStalls;
    }

    // Accessors
    public function getTotalApplicationsAttribute()
    {
        return $this->getTotalApplicationsCount();
    }

    public function getPendingPermitsAttribute()
    {
        return $this->getPendingPermitsCount();
    }

    public function totalStallForDepApproval($departmentId)
    {
        return \App\Models\StallRental::whereHas('permits', function ($q) use ($departmentId) {
        $q->where('permits.department_id', $departmentId)
          ->where('permits.assign_to', 2);
        })->count();
    }

    public function totalVolanteForDepApproval()
    {
        return \App\Models\Volante::whereHas('permits', function ($q) {
            $q->where('department_id', $this->department_id)
            ->where('assign_to', 2);
        })->count();
    }

    public function totalTableForDepApproval()
    {
        return \App\Models\TableRental::whereHas('permits', function ($q) {
            $q->where('department_id', $this->department_id)
            ->where('assign_to', 2);
        })->count();
    }

    public function totalApprovedApplications()
    {
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        return \App\Models\ApprovalPermit::select(
                DB::raw("SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as approved"),
                DB::raw("SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as rejected")
            )
            ->where('department_id', $this->department_id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->first();
    }


}
