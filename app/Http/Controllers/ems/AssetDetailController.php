<?php

namespace App\Http\Controllers\ems;

use App\Models\AssetDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetDetailsRequest;

class AssetDetailController extends Controller
{

    public function create(Request $request)
    {
        $this->authorize('create', new AssetDetails());
        $data['assetDetail']    =   AssetDetails::firstOrNew(['asset_id' => $request->asset]);
        $data['submitRoute']    =   ['asset-detail.store'];
        $data['method']         =   'POST';

        return view('assets.detail.form', $data);
    }

    public function store(AssetDetailsRequest $request)
    {
        $this->authorize('create', new AssetDetails());
        AssetDetails::updateOrCreate([
                'asset_id' => $request->asset_id],[
                'company' => $request->company,
                'ram' => $request->ram,
                'rom' => $request->rom,
        ]);
        return redirect()->route('asset.show',['asset' => $request->asset_id])->with('success', 'Created Successfully.');
    }

    public function edit($id)
    {
        $this->authorize('update', new AssetDetails());
        $data['assetDetail']    =   AssetDetails::findOrFail($id);
        $data['submitRoute']    =   ['asset-detail.update',['asset_detail' => $id]];
        $data['method']         =   'PUT';
        return view('assets.detail.form', $data);
    }

    public function update(AssetDetailsRequest $request, $id)
    {
        $this->authorize('update', new AssetDetails());
        $assetDetail            =   AssetDetails::findOrFail($id);
        $assetDetail->asset_id  =   $request->asset_id;
        $assetDetail->company   =   $request->company;
        $assetDetail->ram       =   $request->ram;
        $assetDetail->rom       =   $request->rom;
        $assetDetail->update();
        return redirect()->route('asset.show',['asset' => $request->asset_id])->with('success', 'Updated Successfully.');
    }
    
}
