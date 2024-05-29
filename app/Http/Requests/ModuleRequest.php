<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id     = !empty (request()->input('id')) ? intval (request()->input('id')) : NULL;

        $rules  = [
            'name'          => ['required', 'string', 'max:50', 'unique:module,name,' . $id . ',id'],
            'description'   => ['nullable', 'string', 'max:500'],
        ];

        return $rules;
    }
}
