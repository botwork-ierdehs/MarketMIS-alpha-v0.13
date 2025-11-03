<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inspection extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): HasOne
    {
        return $this->hasOne(Business::class);
    }

    public function stallsRental(): HasOne
    {
        return $this->hasOne(StallRental::class);
    }
}
