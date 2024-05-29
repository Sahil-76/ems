<?php

namespace App\Http\Controllers\ems;

use App\Models\AssetCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCategoryRequest;

class AssetCategoryController extends Controller
{

    public function index()
    {
        $this->authorize('view', new AssetCategory());

        $data['assetCategorys']      =   AssetCategory::all();

        return view('assets.category.index',$data);
    }

    public function create()
    {
        $assetCategory              = new AssetCategory();
        $this->authorize('create', $assetCategory);
        $data['assetCategory']      =   $assetCategory;
        $data['submitRoute']        =   ['asset-category.store'];
        $data['method']             =   'POST';
        return view('assets.category.form',$data);
    }

    public function store(AssetCategoryRequest $request)
    {
        $assetCategory          = new AssetCategory();
        $this->authorize('create', $assetCategory);
        $assetCategory->name    = $request->name;
        $assetCategory->save();
        return redirect()->route('asset-category.index')->with('success','category created');
    }

    public function edit($id)
    {
        $this->authorize('update', new AssetCategory());
        $data['assetCategory']      =   AssetCategory::findOrFail($id);
        $data['submitRoute']        =   ['asset-category.update',['asset_category' => $id]];
        $data['method']             =   'PUT';
        return view('assets.category.form',$data);
    }

    public function update(AssetCategoryRequest $request, $id)
    {
        $this->authorize('update', new AssetCategory());
        $assetCategory          = AssetCategory::findOrFail($id);
        $assetCategory->name    = $request->name;
        $assetCategory->Update();
        return redirect()->route('asset-category.index')->with('success','category updated');
    }

    public function destroy($id)
    {
        $this->authorize('delete', new AssetCategory());
        AssetCategory::findOrFail($id)->delete();
    }
}
