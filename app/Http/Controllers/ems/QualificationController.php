<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Qualification;
use App\Http\Requests\QualificationRequest;

class QualificationController extends Controller
{
    public function view()
    {
        $this->authorize('view',new Qualification());
        return view('qualification.qualification');
    }

    public function list(Request $request)
    {
        $this->authorize('view',new Qualification);
        $pageIndex      =  $request->pageIndex;
        $pageSize       =  $request->pageSize;
        $qualifications =  Qualification::query();
        if(!empty($request->get('name')))
        {
            $qualifications = $qualifications->where('name','like', '%' .$request->get('name') . '%');
        }
        $data['itemsCount'] = $qualifications->count();
        $data['data']       = $qualifications->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
        return json_encode($data);
    }

    public function insert(QualificationRequest $request)
    {
        $this->authorize('insert',new Qualification);
        $qualification          = new Qualification();
        $qualification->name    = $request->name;
        $qualification->save();
        return $qualification->toArray();
    }

    public function update(QualificationRequest $request)
    {
        $qualification  = Qualification::find($request->id);
        $this->authorize('update',$qualification);
        $qualification->name    = $request->name;
        $qualification->save();
        return $qualification->toArray();
    }

    public function delete(Request $request)
    {
        $qualification  = Qualification::find($request->id);
        $this->authorize('delete',$qualification);
        $qualification->delete();
        return json_encode("done");
    }
}
