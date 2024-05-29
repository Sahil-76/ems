<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualLeaveRequest extends FormRequest
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
        $rules = [
            'user_id'       =>      'required',
            'leave_type'    =>      'required',
            'from_date'     =>      'required',
            'to_date'       =>      'required',
            'reason'        =>      'required',
            'status'        =>      'required',
        ];

        if(request()->has('attachment'))
        {
            $rules  +=  ['attachment'=>'mimetypes:application/pdf'];
        }

        return $rules;
    }

    public function attributes()
    {
        return[
            'user_id'       => 'User',
            'from_date'     => 'From Date',
            'to_date'       => 'To Date',
        ];
    }

    public function messages()
    {
        return [
            'attachment'    =>  'File must be type of pdf',
        ];
    }
}
