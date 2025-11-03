<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requirement extends Model
{
    protected $fillable = [
        'permit_id',
        'requirement_checklist_id',
        'attachment'
    ];

    public function requirementsCheckList(): HasMany 
    {
        return $this->hasMany(RequirementCheckList::class);
    }

    public function permits(): BelongsTo 
    {
        return $this->belongsTo(Permits::class);
    }

    public function requirementDetails(): BelongsTo {
        return $this->belongsTo(RequirementChecklist::class, 'requirement_checklist_id');
    }
}
