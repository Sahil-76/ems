<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\AssetSubType;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table    = 'companies';
    protected $fillable = ['name'];

    public function assetSubTypes()
    {
        return $this->belongsToMany(AssetSubType::class, 'asset_company', 'company_id', 'asset_sub_type_id');
    }

    public function asset()
    {
        return $this->belongsToMany(Asset::class, 'company_id', 'id');
    }
}
