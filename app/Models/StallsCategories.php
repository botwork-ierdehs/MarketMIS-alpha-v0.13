<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StallsCategories extends Model
{
    protected $fillable = [
        'name',
        'description',
        'monthly_fee',
        'daily_fee',
        'fee_masterlist_id',
    ];

    protected $casts = [
        'monthly_fee' => 'double',
        'daily_fee' => 'double',
        'fee_masterlist_ids' => 'array',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        });
    }
    public function feeMasterlists(): HasMany
    {
        return $this->hasMany(FeeMasterlist::class, 'id');
    }

}
