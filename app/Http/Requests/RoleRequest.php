<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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

    public function messages()
    {
        $permissions    = request()->get('permission');
        $messages       = [];

        if (!empty($permissions))
        {
            for ($i = 0; $i < count($permissions); $i++) 
            {
                $messages['permission.' . $i . '.exists'] = 'The assigned permission ' . ($i + 1) . ' is invalid.';
            }
        }

        return $messages;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id     = !empty (request()->input('id')) ? intval (request()->input('id')) : NULL;

        $rules = [
            'name'          => ['required', 'string', 'max:50', 'unique:role,name,' . $id . ',id'],
            'description'   => 'nullable|string|max:500',
            'permission.*'  => 'nullable|exists:permission,id',
        ];

        return $rules;
    }
}
