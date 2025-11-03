<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeMasterlist extends Model
{    
    protected $table = 'fee_masterlist';

    protected $fillable = [
        'name',
        'description',
        'amount',
        'fee_type',
    ];
}
