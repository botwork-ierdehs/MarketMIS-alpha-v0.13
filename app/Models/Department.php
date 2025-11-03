<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Department extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeFilterByDepartmentID($query, $departmentId=null, $role_id=null)
    {
        if (empty($departmentId) && $role_id == 1) {
            return $query;
        }
        return $query->where('id', $departmentId);
    }

    public function admins(): HasOne
    {
        return $this->hasOne(SubAdminType::class, 'department_id');
    } 
}