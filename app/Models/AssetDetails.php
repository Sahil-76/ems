<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;

class AssetDetails extends Model
{
    protected $table    = 'asset_details';
    protected $guarded  = ['id'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
