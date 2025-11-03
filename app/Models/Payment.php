<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'table_rental_id',
        'stall_rental_id',
        'business_id',
        'volantes_id',
        'receipt',
        'date',
        'amount',
        'reference_number',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function volantes(): HasOne
    {
        return $this->hasOne(Volante::class);
    }

    public function business(): HasOne
    {
        return $this->hasOne(Business::class);
    }
    
    public function stallRentals(): HasOne
    {
        return $this->hasOne(StallRental::class);
    }

    public function scopeWithStallRental($query)
    {
        return $query->whereNotNull('stall_rental_id');
    }

    public function scopeWithTableRental($query)
    {
        return $query->whereNotNull('table_rental_id');
    }


    public function scopeWithVolanteRental($query)
    {
        return $query->whereNotNull('volantes_id');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
             ->whereYear('created_at', now()->year);
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);
    }

    public function scopeInCurrentQuarter($query)
    {
        $now = Carbon::now();
        $currentMonth   = $now->month;
        $currentQuarter = ceil($currentMonth / 3);

        // Quarter start & end
        $startOfQuarter = Carbon::createFromDate($now->year, ($currentQuarter - 1) * 3 + 1, 1)->startOfMonth();
        $endOfQuarter   = (clone $startOfQuarter)->addMonths(2)->endOfMonth();

        // Due date = 20th of the last month of the quarter
        $dueDate = (clone $endOfQuarter)->day(20)->startOfDay();

        return $query->whereBetween('created_at', [$startOfQuarter, $endOfQuarter])
             ->whereDate('created_at', '>=', $dueDate);
    }

}
