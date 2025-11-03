<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StallRentalEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreate = $this->isMethod('post');
       
    
        $rules = [
            'stall_id' => [ 'nullable', 'integer', 'exists:stalls,id'],
            'business_name' => ['nullable',
            'string',
            'max:255'],
            'type' => 'nullable',
            'fees' => ['nullable', 'array', 'min:1'],
            'bulb' => ['nullable', 'integer', 'min:0'],
            'total_payment' => ['nullable', 'numeric'],
            'requirements' => ['nullable', 'array'],
            'requirements.*.requirement_checklist_id' => [
                'integer',
                'exists:requirement_checklists,id'
            ],
            'requirements.*.attachment' => [
                 'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'attachment_signature' => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'acknowledgeContract' => 'boolean|nullable'
        ];
        return $rules;


    }

    public function messages()
    {
        return [
            'fees.min' => 'You must add at least one fee item.',
        ];
    }
}
