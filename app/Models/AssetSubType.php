<?php

namespace App\Models;

use App\Models\AssetType;
use Illuminate\Database\Eloquent\Model;

class AssetSubType extends Model
{
    protected $table    = 'asset_sub_types';
    protected $guarded  = ['id'];

    public function assetType()
    {
        return $this->belongsTo(AssetType::class,'asset_type_id','id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class,'sub_type_id','id');
    }

}
