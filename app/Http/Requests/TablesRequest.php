<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class TablesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
        $tableRentalId = $this->route('tableRental')?->id;
        $isRenewal = str_contains($this->fullUrl(), 'renew');

        \Log::info('tableRentalRequest validation', [
            'tableRentalId' =>  $tableRentalId,
            'route' => $this->fullUrl(),
            'isRenewal' => $isRenewal,
        ]);

        $step = $this->input('step');

        $businessNameRules = [
            $isCreate ? 'required' : 'nullable',
            'string',
            'max:255',
        ];

        // ✅ Only apply unique rule if NOT renewal
        if (!$isRenewal) {
            $businessNameRules[] = Rule::unique('stall_rentals')->ignore($tableRentalId);
        }

        $rules = [
            'step' => [$isCreate ? 'required' : 'nullable', 'integer', 'in:1,2,3,4'],
            'stall_id' => [$isCreate || !$tableRentalId ? 'required' : 'nullable', 'integer', 'exists:stalls,id'],
            'business_name' => $businessNameRules,
            'type' => 'nullable',
            'fees' => ['nullable', 'array', 'min:1'],
            'bulb' => ['nullable', 'integer', 'min:0'],
            'total_payment' => ['nullable', 'numeric'],
        ];

        // Step 2 & 4 → allow requirements
        if (in_array($step, [2, 4])) {
            $rules += [
                'requirements' => ['nullable', 'array'],
                'requirements.*.requirement_checklist_id' => [
                    'integer',
                    'exists:requirement_checklists,id'
                ],
                'requirements.*.attachment' => [
                    $isCreate ? 'required' : 'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,pdf',
                    'max:2048'
                ],
            ];
        }

        // Step 3 & 4 → allow signature
        if (in_array($step, [3, 4])) {
            $rules += [
                'attachment_signature' => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
                'acknowledgeContract' => 'boolean|nullable'
            ];
        }

        return $rules;
    }

    /**
     * Prepare inputs before validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            // If bulb is not provided, default to 0
            'bulb' => $this->input('bulb', 0),
            'type' => $this->input('type', 1),
            // Always default requirements to array so backend won’t break
        ]);
    }

    public function messages()
    {
        return [
            'stall_id.required' => 'Please select a table.',
            'stall_id.exists' => 'The selected table is invalid.',
            'fees.min' => 'You must add at least one fee item.',
        ];
    }
}
