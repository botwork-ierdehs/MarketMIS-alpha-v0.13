<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalPermit extends Model
{
    public function approver(): BelongsTo 
    {
        return $this->belongsTo(User::class,  'approver_id');
    }

    public function department(): BelongsTo 
    {
        return $this->belongsTo(Department::class,  'department_id');
    }
}
