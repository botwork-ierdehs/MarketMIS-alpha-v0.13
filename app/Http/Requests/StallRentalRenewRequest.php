<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StallRentalRenewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

        protected function prepareForValidation()
    {
        // Ensure stall_rental_id is always available
        $routeParam = $this->route('stallRental');
        $routeId = is_object($routeParam) ? $routeParam->id : $routeParam;

        $this->merge([
            'stall_rental_id' => $this->input('stall_rental_id') ?? $routeId,
            'bulb' => $this->input('bulb', 0),
            'type' => $this->input('type', 1),
        ]);
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');
        $stallRentalId = $this->input('stall_rental_id'); // ✅ guaranteed to exist if editing or renewing
        $step = $this->input('step');

        $businessNameRules = [
            $isCreate ? 'required' : 'nullable',
            'string',
            'max:255',
        ];

        $rules = [
            'step' => [$isCreate ? 'required' : 'nullable', 'integer', 'in:1,2,3,4'],
            'stall_id' => [$isCreate || !$stallRentalId ? 'required' : 'nullable', 'integer', 'exists:stalls,id'],
            'business_name' => $businessNameRules,
            'type' => 'nullable',
            'fees' => ['nullable', 'array'],
            'bulb' => ['nullable', 'integer', 'min:0'],
            'total_payment' => ['nullable', 'numeric'],
            'stall_rental_id' => ['nullable', 'integer', 'exists:stall_rentals,id'],
        ];

        if (in_array($step, [2, 4])) {
            $rules += [
                'requirements' => ['nullable', 'array'],
                'requirements.*.requirement_checklist_id' => ['integer', 'exists:requirement_checklists,id'],
                'requirements.*.attachment' => [
                    $isCreate ? 'required' : 'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,pdf',
                    'max:2048'
                ],
            ];
        }

        if (in_array($step, [3, 4])) {
            $rules += [
                'attachment_signature' => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
                'acknowledgeContract' => 'boolean|nullable',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'stall_id.required' => 'Please select a stall.',
            'stall_id.exists' => 'The selected stall is invalid.',
        ];
    }
}
