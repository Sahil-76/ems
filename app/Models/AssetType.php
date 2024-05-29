<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    protected $table    = 'asset_types';
    protected $guarded  = ['id'];

    public function assetCategory()
    {
        return $this->belongsTo('App\Models\AssetCategory');
    }
}
