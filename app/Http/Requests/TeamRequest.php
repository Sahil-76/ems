<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
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
        if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR')){
            $rules['department_id'] =  'required';  
        }
        else{
            $rules['name']          =  'required';
        }
        return  $rules;
    }
}
