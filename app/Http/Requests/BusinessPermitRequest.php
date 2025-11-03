<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessPermitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow all authorized users; update if needed
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string:unique:business',
            'trade_or_franchise_name' => 'nullable|string',
            'business_address' => 'required|string',
            'business_phone' => 'nullable|string',
            'business_email' => 'nullable|email',
            'business_telephone' => 'nullable|string',
            'area_of_sqr_meter' => 'nullable|string',

            #form details
            'business_mode' => 'required|integer',
            'business_type' => 'required|integer',
            'business_ammendment.from' => 'nullable|integer',
            'business_ammendment.to' => 'nullable|integer',
            'is_enjoying_tax_incentives' => 'required|boolean',
            'is_enjoying_tax_incentives_no_reason' => 'nullable|string',
            'isRented' => 'required|boolean',
            'lessorDetails' => 'nullable|string',
            'line_of_business' => 'required|string',
            'no_of_units' => 'nullable|string',
            'capitalization' => 'nullable|numeric|decimal:0,2',
            'gross' => 'nullable|numeric|decimal:0,2',
            'requirements' => 'nullable|array',
            'requirements.*.requirement_checklist_id' => 'required|integer|exists:requirement_checklists,id',
            'requirements.*.attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',           
        ];
    }
}
