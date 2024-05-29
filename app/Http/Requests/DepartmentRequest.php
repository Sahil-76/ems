<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
        $id =   request()->department  ??  "0" ;
        
        return [
            'name'         => ['required','unique:departments,name,'.$id.',id','min:2','max:50','string'],
            'description'  => 'max:200'
        ];
    }
}
