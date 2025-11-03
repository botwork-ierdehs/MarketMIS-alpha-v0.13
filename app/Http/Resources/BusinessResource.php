<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class BusinessResource extends JsonResource
{
    private function getPermitType(int $type): string
    {
        return $type === 1 ? 'New' : 'Renew';
    }

    private function getBusinessModeOfPayment(int $mode): string
    {
        Log::info('Login attempt', [
            '$mode' =>$mode 
        ]);
        return match ($mode) {
            1 => 'Monthly',
            2 => 'Quarterly',
            3 => 'Annually',
            default => 'Unknown',
        };
    }

    private function getBusinessType(int $type): string
    {
        return match ($type) {
            1 => 'Single Proprietorship',
            2 => 'Partnership',
            3 => 'Corporation',
            default => 'Unknown',
        };
    }

    public function toArray(Request $request): array
    {
        $user = optional($this->user);
        $gender = optional($user->gender);
        $role = optional($user->role);
        $permitDetails = optional($this->permitDetails);
        $form = optional($permitDetails->formDetails);

        return [
            'id' => $this->id,
            'user_id' => (int) ($this->user_id ?? 0),
            'permit_id' => (int) ($this->permit_id ?? 0),
            'name' => $this->name ?? '',
            'trade_name' => $this->trade_or_franchise_name ?? '',
            'business_address' => $this->business_address ?? '',
            'business_phone' => $this->business_phone ?? '',
            'business_email' => $this->business_email ?? '',
            'business_telephone' => $this->business_telephone ?? '',
            'area' => (float) ($this->area_of_sqr_meter ?? 0),
            'created_at' => optional($this->created_at)->toDateString() ?? '',

            'user' => [
                'id' => (int) ($user->id ?? 0),
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'email' => $user->email ?? '',
                'mobile' => $user->mobile ?? '',
                'gender' => $gender->name ?? '',
                'role' => $role->name ?? '',
            ],

            'permit_details' => [
                'issued_date' => $permitDetails->issued_date ?? '',
                'expiry_date' => $permitDetails->expiry_date ?? '',
                'type' => $this->getPermitType((int) ($form->business_type ?? 0)),
                'status' => (int) ($permitDetails->status ?? 0),
                'remarks' => $permitDetails->remarks ?? '',

                'form_details' => [
                    'business_mode' => $this->getBusinessModeOfPayment((int) ($form->business_mode ?? 0)),
                    'business_type' => $this->getBusinessType((int) ($form->business_type ?? 0)),
                    'business_ammendment' => $form->business_ammendment ?? '',
                    'tax_incentives' => (bool) ($form->is_enjoying_tax_incentives ?? false),
                    'tax_incentive_reason' => $form->is_enjoying_tax_incentives_no_reason ?? '',
                    'is_rented' => (bool) ($form->isRented ?? false),
                    'lessor_details' => $form->lessorDetails ?? '',
                    'line_of_business' => $form->line_of_business ?? '',
                    'no_of_units' => (int) ($form->no_of_units ?? 0),
                    'capitalization' => (float) ($form->capitalization ?? 0),
                    'gross' => (float) ($form->gross ?? 0),
                ],

                'requirements' => collect($permitDetails->requirements ?? [])->map(function ($req) {
                    $reqDetails = optional($req->requirementDetails);
                    return [
                        'id' => (int) ($req->id ?? 0),
                        'attachment' => $req->attachment ?? '',
                        'requirement_name' => $reqDetails->name ?? '',
                        'is_business' => (bool) ($reqDetails->isBusiness ?? false),
                        'is_stall' => (bool) ($reqDetails->isStall ?? false),
                        'is_volante' => (bool) ($reqDetails->isVolante ?? false),
                    ];
                })->values(),
            ],
        ];
    }
}
