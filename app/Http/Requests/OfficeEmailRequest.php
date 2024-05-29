<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfficeEmailRequest extends FormRequest
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
        if(!is_null(request()->input('user.email')))
        { 
            $id = request()->user['id'];
            return [
                'user.email'    => ['required', 'string', 'email', 'max:50','unique:users,email,'.$id.',id','regex: /\S+@\S+\.\S+/'],
            ];  
        }

        return [
            'email'    => ['required', 'string', 'email', 'max:50','unique:users,email','regex: /\S+@\S+\.\S+/'],
        ];
    }
}
