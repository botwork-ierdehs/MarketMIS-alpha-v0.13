<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permits extends Model
{
    protected $fillable = [
        'form_detail_id', 'type',
        'status', 'issued_date', 'expiry_date',
        'permit_number'
    ];

    public function formDetails(): BelongsTo 
    {
        return $this->belongsTo(FormDetails::class,  'form_detail_id');
    }

    public function departments(): BelongsTo 
    {
        return $this->belongsTo(Department::class,  'department_id');
    }

    public function payments(): BelongsTo 
    {
        return $this->belongsTo(Payments::class);
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class,  'permit_id');
    }

    public function approvals()
    {
        return $this->hasMany(ApprovalPermit::class,  'permit_id');
    }

    public function routeNotificationForTwilio()
    {
        // assuming `mobile` field holds the user's phone number
        return $this->mobile;
    }

}
