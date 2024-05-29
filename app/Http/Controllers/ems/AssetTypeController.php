<?php

namespace App\Http\Controllers\ems;

use App\Models\AssetType;
use App\Models\AssetCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetTypeRequest;

class AssetTypeController extends Controller
{

    public function index()
    {
        $this->authorize('view', new AssetType());
        $data['assetTypes']     = AssetType::with('AssetCategory')->get();
        return view('assets.type.index',$data);
    }

    public function create()
    {
        $assetType              =   new AssetType();
        $this->authorize('create', $assetType);
        $data['assetType']      =   $assetType;
        $data['submitRoute']    =   route('asset-type.store');
        $data['method']         =   'post';
        $data['categories']     =   AssetCategory::pluck('name','id')->toArray();
        return view('assets.type.form',$data);
    }

    public function store(AssetTypeRequest $request)
    {
        $assetType                           =      new assetType();
        $this->authorize('create', $assetType);
        $assetType->name                     =      $request->name;
        $assetType->asset_category_id        =      $request->asset_category_id;
        $assetType->save();
        return redirect(route('asset-type.index'))->with('success','Asset Type Added Successfully');
    }

    public function edit($id)
    {
        $this->authorize('update', new AssetType());
        $data['assetType']       =   AssetType::find($id);
        $data['submitRoute']     =    route('asset-type.update',$id);
        $data['method']          =   'put';
        $data['categories']      =    AssetCategory::pluck('name','id')->toArray();
        return view('assets.type.form',$data);
    }

    public function update(AssetTypeRequest $request, $id)
    {
        $this->authorize('update', new AssetType());
        $assetType                          = AssetType::find($id);
        $assetType ->name                   = $request->name;
        $assetType->asset_category_id       = $request->asset_category_id;
        $assetType ->update();
        return redirect(route('asset-type.index'))->with('success','Asset Type Updated Successfully');
    }

    public function destroy($id)
    {
        $this->authorize('delete', new AssetType());
        $assetType=AssetType::find($id);
        $assetType->delete();
    }
}
