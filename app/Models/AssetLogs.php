<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetLogs extends Model
{
    protected $table    = 'asset_logs';
    protected $guarded  = ['id'];

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset','asset_id','id');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User','user_id','id')->withoutGlobalScopes();
    }

    public function assignedTo()
    {
        return $this->belongsTo('App\User','assigned_to','id')->withoutGlobalScopes();
    }
}
