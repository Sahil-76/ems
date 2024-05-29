<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveRequest extends FormRequest
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
        $rules  = [
            'leave_nature'  =>  'required | sometimes',
            'leave_type'    =>  'required | sometimes',
            'reason'        =>  'required | sometimes',    
            'from_date'     =>  'required',
            'to_date'       =>  'required|after_or_equal:from_date', 
        ];

        if(request()->leave_type=='Half day')
        {
            $rules  +=  ['halfDayType'=>'required'];
        }

        if(request()->leave_type=='Short leave')
        {
            $rules  +=  ['timing'=>'required'];
        }

        if(request()->has('attachment'))
        {
            $rules  +=  ['attachment'=>'mimetypes:application/pdf'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'attachment'    =>  'File must be type of pdf',
        ];
    }
}
