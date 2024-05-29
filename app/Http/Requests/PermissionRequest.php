<?php

namespace App\Http\Requests;

use App\Models\Module;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;  
    }

    // public function rules()
    // {
    //     $module_id  = request()->has('module_id') ? request()->input('module_id') : NULL;
    //     $id         = request()->has('id') ? request()->input('id') : NULL;
        
    //     return [
    //         'module_id'     => ['required', 'exists:module,id'],
    //         'access'        => ['required', 'string', 'max:50', 'unique:permission,access,' . $id . ',id,module_id,' . $module_id],
    //         'description'   => ['nullable', 'string', 'max:500'],
    //     ];
    // }
    public function rules()
    {
        $module_id = request()->input('module_id');
        $id = $this->route('permission') ? $this->route('permission')->id : NULL; // assumes route model binding
        
        return [
            'module_id' => ['required', 'exists:module,id'],
            'access' => [
                'required',
                'string',
                'max:50',
                Rule::unique('permission')->where(function ($query) use ($module_id, $id) {
                    $query = $query->where('access', request()->input('access'));
            
                    if ($id !== null) { // Only add module ID check if we are updating an existing Permission
                        $query = $query->where('module_id', $module_id)
                            ->where('id', '<>', $id);
                    }
            
                    return $query;
                })->ignore($id, 'id'),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            'module_id.required'    => 'The module field is required.',
            'module_id.exists'      => 'Invalid module.',
        ];
    }
}
