<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementChecklist extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        });
    }
    
    private function businessPermitNew() {
        return $this->where('isBusiness', true)
            ->whereIn('isBusinessType', [1, 3])
            ->get();
    }

    private function businessPermitRenew() {
        return $this->where('isBusiness', true)
            ->whereIn('isBusinessType', [2, 3])
            ->get();
    }

    private function stallPermitNew() {
        return $this->where('isStall', true)
            ->whereIn('isStallType', [1, 3])
            ->get();
    }

    private function stallPermitRenew() {
        return $this->where('isStall', true)
            ->whereIn('isStallType', [2, 3])
            ->get();
    }

    private function volantePermitNew() {
        return $this->where('isVolante', true)
            ->whereIn('isVolanteType', [1, 3])
            ->get();
    }

    private function volantePermitRenew() {
        return $this->where('isVolante', true)
            ->whereIn('isVolanteType', [2, 3])
            ->get();
    }

    private function tablePermitNew() {
        return $this->where('isTable', true)
            ->whereIn('isTableType', [1, 3])
            ->get();
    }

    private function tablePermitRenew() {
        return $this->where('isTable', true)
            ->whereIn('isTableType', [2, 3])
            ->get();
    }

    public function scopeRequirementsList() {
       return [
            'stall_new' => $this->stallPermitNew(),
            'stall_renew' => $this->stallPermitRenew(),
            'volante_new' => $this->volantePermitNew(),
            'volante_renew' => $this->volantePermitRenew(),
            'table_new' => $this->tablePermitNew(),
            'table_renew' => $this->tablePermitRenew(),
        ];
    }
}
