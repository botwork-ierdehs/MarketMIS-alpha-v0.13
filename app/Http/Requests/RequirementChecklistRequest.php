<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequirementChecklistRequest extends FormRequest
{
      /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreate = $this->isMethod('post'); 
        $req = $this->route('requirementsList');
        return [
            'name' => ['required','string','max:255',$req
                ? Rule::unique('requirement_checklists')->ignore($req) // ignore if editing
                : Rule::unique('requirement_checklists')],
            'description' => [$isCreate ? 'required' : 'nullable', 'max:255'],
            'isRequired' => [$isCreate ? 'required' : 'nullable', 'boolean'],
            'isStallType' => [$isCreate ? 'required' : 'nullable', 'integer'],
            'isStall' => [$isCreate ? 'required' : 'nullable', 'boolean'],
            'isVolante' => [$isCreate ? 'required' : 'nullable', 'boolean'],
            'isVolanteType' => [$isCreate ? 'required' : 'nullable', 'integer']
        ];
    }

}
