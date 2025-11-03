<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
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
        ];
    }
}
