<?php

namespace App\Http\Controllers\ems;

use App\Models\AssetType;
use App\Models\Equipment;
use App\Models\AssetSubType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssetSubTypeController extends Controller
{
    public function index()
    {
        $this->authorize('view', new AssetSubType());
        $data['subTypes']    =      AssetSubType::with('assetType')->get();
        return view('assets.subType.index',$data);
    }

    public function create()
    {
        $subType                      = new AssetSubType();
        $this->authorize('create', $subType);
        $data['subType']              = $subType;
        $data['method']               = 'POST';
        $data['submitRoute']          = ['asset-subtype.store'];
        $data['types']                = AssetType::pluck('name', 'id')->toArray();
        return view('assets.subType.form',$data);
    }

    public function store(Request $request)
    {
        $subType                    = new AssetSubType();
        $this->authorize('create', $subType);
        $subType->name              = $request->name;
        $subType->is_assignable     = empty($request['is_assignable']) ? 0 : 1;
        $subType->asset_type_id     = $request->asset_type_id;
        $subType->save();
        return redirect()->route('asset-subtype.index')->with('success','subType created');
    }

    public function edit($id)
    {
        $this->authorize('update', new AssetSubType());
        $data['subType']              = AssetSubType::findOrFail($id);
        $data['method']               = 'PUT';
        $data['submitRoute']          = ['asset-subtype.update',['asset_subtype' => $id]];
        $data['types']                = AssetType::pluck('name', 'id')->toArray();
        return view('assets.subType.form',$data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', new AssetSubType());
        $subType                    = AssetSubType::findOrFail($id);
        $subType->name              = $request->name;
        $subType->is_assignable     = empty($request['is_assignable']) ? 0 : 1;
        $subType->asset_type_id     = $request->asset_type_id;
        $subType->update();
        return redirect()->route('asset-subtype.index')->with('success','subType updated');
    }

    public function destroy($id)
    {
        $this->authorize('delete', new AssetSubType());
        AssetSubType::findOrFail($id)->delete();
    }
}
