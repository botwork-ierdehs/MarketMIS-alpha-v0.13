<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermitRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'form_detail_id' => 'required|integer',
            'type' => 'required|integer',
            'issued_date' => 'required|date',
            'expiry_date' => 'required|date',
        ];
    }            
}
