<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetTypeRequest extends FormRequest
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
        return [
            'name'              => 'required',
            'asset_category_id' => 'required',
        ];
    }
    
    public function messages()
    {
        return [
            'name.required'              => 'name is required',
            'asset_category_id.required' => 'category is required'
        ];
    }
}
