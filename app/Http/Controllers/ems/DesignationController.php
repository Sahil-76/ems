<?php

namespace App\Http\Controllers\ems;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DesignationController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('view', new Department());
        if($request->ajax())
        {
            $pageIndex          = $request->pageIndex;
            $pageSize           = $request->pageSize;
            $designations       = Designation::query();

            if(!empty($request->get('name')))
            {
                $designations  = $designations->where('name','like', '%' .$request->get('name') . '%');
            }

            $data['itemsCount'] = $designations->count();
            $data['data']       = $designations->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();

            return json_encode($data);
        }
        return view('designation.designations');
    }

    public function store(Request $request)
    {
        $designation         = new Designation();
        $designation->name   = $request->name;
        $designation->save();
        return $designation;
    }

    public function update(Request $request, $id)
    {
        $designation            = Designation::findOrFail($id);
        $designation->name      = $request->name;
        $designation->save();
        return $designation;
    }

    public function destroy($id)
    {
        Designation::findOrFail($id)->delete();
        return json_encode('done');
    }
}
