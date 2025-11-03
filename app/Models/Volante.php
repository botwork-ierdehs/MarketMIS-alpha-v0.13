<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Volante extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'stall_id',
        'permit_id',
        'business_name',
        'started_date',
        'end_date',
        'status',
        'acknowledgeContract',
        'attachment_signature',
        'bulb',
        'fees_additional',
        'total_payment',   
    ];

    public function stalls(): BelongsTo
    {
        return $this->belongsTo(Stall::class, 'stall_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function permits(): BelongsTo
    {
        return $this->belongsTo(Permits::class, 'permit_id');
    }    

    public function payments(): HasOne
    {
        return $this->hasOne(Payment::class, 'volantes_id');
    }    

    public function scopeFilter($query, array $filters)
    {
        \Log::info('Filtering by stallType', ['stallType' => $query]);
        $query
        ->when($filters['search'] ?? null, function ($query, $search) {
             $query->whereHas('users', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['category'] ?? null, function ($query, $category) {
            $query->whereHas('stalls.stallsCategories', function ($q) use ($category) {
                $q->where('id',  $category);
            });
        });
    }

    public function scopeTotalTodayApplicants($query){
        return $query->whereDate('created_at', Carbon::today())->count();
    }

    // Volante model
    public function scopeMyApplication($query, $userID)
    {
        return $query->where('user_id', $userID); // Just load relationships
    }

    public function scopeForApproval($query, $id)
    {
        return $query->whereHas('permits', function ($q) use ($id) {
                $q->where('assign_to', $id);
            });
    }

    public function scopeMyApplicationUnderMyDep($query, $depID){
        return $query->whereHas('permits', function ($q) use ($depID) {
                $q->where('department_id',  $depID)->where('assign_to',  2);
        });
    }

    public function scopeExpiringSoon($query)
    {
        $now = Carbon::today();
        $fiveDays = Carbon::today()->addDays(5);

        return $query->whereHas('permit', function ($q) use ($now, $fiveDays) {
            $q->whereNotNull('issued_date')
                ->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$now, $fiveDays]);
        })->get();
    }
}
