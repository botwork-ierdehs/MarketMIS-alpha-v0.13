<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubAdminType extends Model
{
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        });
    }

    public function departments(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    } 

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sub_admin_type_id');
    } 
}
