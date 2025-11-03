<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StallTypeRequest extends FormRequest
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $stallType = $this->route('stallsCategories');
        return [
            'name' => ['required','string','max:255',$stallType
                ? Rule::unique('stalls_categories')->ignore($stallType) // ignore if editing
                : Rule::unique('stalls_categories')],
            'description' => 'required | max:255',
            'fee_masterlist_ids' => 'required'
        ];
    }
}
