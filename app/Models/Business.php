<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Carbon\Carbon;
class Business extends Model
{

     protected $fillable = [
        'user_id',
        'permit_id',
        'name',
        'trade_or_franchise_name',
        'business_address',
        'business_phone',
        'business_email',
        'business_telephone',
        'area_of_sqr_meter',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function permitDetails(): BelongsTo
    {
        return $this->belongsTo(Permits::class, 'permit_id');
    } 
    
    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('trade_or_franchise_name', 'like', "%{$search}%")
                ->orWhere('business_address', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        // You can add more filter conditions here
    }


    public function scopeTotalTodayApplicants($query){
        return $query->whereDate('created_at', Carbon::today())->count();
    }

    public function scopeMyApplication($query, $userID){
        return $query->where('user_id', $userID);
    }
}
