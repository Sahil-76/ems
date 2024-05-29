<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $table    = 'asset_categories';
    protected $guarded  = ['id'];

    public function AssetType()
    {
        return $this->hasMany('App\Models\AssetType','asset_category_id');
    }
}
