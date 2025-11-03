<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'table_rental_id' => ['nullable', 'integer'],
            'stall_rental_id' => ['nullable', 'integer'],
            'volantes_id' => ['nullable', 'integer'],
            'receipt' => [
               'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'amount' => ['required','numeric','min:0'],
            'reference_number' => ['required', 'string'],
        ];
    }
}
