<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequirementRequest extends FormRequest
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
        $isCreate = $this->isMethod('post'); // true for create, false for update

        return [
            'requirements' => [
            $isCreate ? 'required' : 'nullable',
            'array'
            ],

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
}
