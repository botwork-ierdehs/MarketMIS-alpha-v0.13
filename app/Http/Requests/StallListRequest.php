<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StallListRequest extends FormRequest
{

    public function rules(): array
    {
        $stall = $this->route('stall');
        return [
            'name' => ['required','string','max:255',$stall
                ? Rule::unique('stalls')->ignore($stall) // ignore if editing
                : Rule::unique('stalls')],
            'size' => 'max:255',
            'coordinates' => 'max:255',
            'stall_category_id' => 'required|integer',
            'location_description' => 'max:255',
        ];
    }
}
