<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VolanteTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $volanteType = $this->route('volanteCategory');
        return [
            'name' => ['required','string','max:255',$volanteType
                ? Rule::unique('volante_categories')->ignore($volanteType) // ignore if editing
                : Rule::unique('volante_categories')],
        ];
    }

}
