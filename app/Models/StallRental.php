<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class StallRental extends Model
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

   
    public function requirements(): BelongsTo
    {
        return $this->belongsTo(Requirement::class, 'requirement_id');
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
        return $this->hasOne(Payment::class, 'stall_rental_id');
    }    

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }   

    public function scopeFilter($query, array $filters)
    {
        \Log::info('Filtering by stallType', ['stallType' => $query]);
        $query
        ->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('business_name', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['category'] ?? null, function ($query, $category) {
            $query->whereHas('stalls.stallsCategories', function ($q) use ($category) {
                $q->where('id',  $category);
            });
        })->when($filters['type'] ?? null, function ($query, $status) {
            $query->whereHas('permits', function ($q) use ($status) {
                $q->where('status',  $status);
            });
        });
    }

    public function scopeWhereStall($query, $stall)
    {
        return $query->where('stall_id', $stall);
    }

    public function scopeTotalTodayApplicants($query){
        return $query->whereDate('created_at', Carbon::today())->count();
    }

    public function scopeMyApplication($query, $userID){
        return $query->where('user_id', $userID);
    }

    public function scopeForApproval($query, $id)
    {
        return $query->whereHas('permits', function ($q) use ($id) {
                $q->where('assign_to',  $id);
            });
    }

    public function scopeMyApplicationUnderMyDep($query, $depID){
        return $query->whereHas('permits', function ($q) use ($depID) {
            $q->where('department_id', $depID)->where('assign_to',  2);
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
