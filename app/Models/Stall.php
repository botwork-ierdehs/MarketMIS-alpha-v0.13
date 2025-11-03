<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Stall extends Model
{
    protected $fillable = [
        'name',
        'stall_category_id',
        'size',
        'coordinates',
        'location_description',
        'status',
    ];
    public function stallsCategories(): BelongsTo
    {
        return $this->belongsTo(StallsCategories::class, 'stall_category_id');
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
    
    public function scopeSingleRecord($query, $id) {
         Log::info('Login attempt', [
            '$id' => $id
        ]);
        return $query->where('id', $id);
    }
}
