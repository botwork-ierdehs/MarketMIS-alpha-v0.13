<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormDetails extends Model
{
    protected $casts = [
        'business_ammendment' => 'array',
        'is_enjoying_tax_incentives' => 'boolean',
        'isRented' => 'boolean'
    ];

    protected $fillable = [
        'business_mode', 'business_type', 'business_ammendment',
        'is_enjoying_tax_incentives', 'is_enjoying_tax_incentives_no_reason',
        'isRented', 'lessorDetails', 'line_of_business',
        'no_of_units', 'capitalization', 'gross'
    ];
}
