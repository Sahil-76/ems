<?php

namespace App\Models;

use App\User;
use App\Models\Ticket;
use App\Models\Company;
use App\Models\AssetSubType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Asset extends Model
{
    protected $table    = 'assets';
    protected $guarded  = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('is_exported', function (Builder $builder) {
            $builder->whereNull('assets.is_exported')->orWhere('assets.is_exported','0');
        });
    }

    public function assetSubType()
    {
        return $this->belongsTo(AssetSubType::class,'sub_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'assigned_to');
    }

    public function assetLogs()
    {
        return $this->hasMany('App\Models\AssetLogs','asset_id','id');
    }

    public function assetDetail()
    {
        return $this->hasOne(AssetDetails::class,'asset_id','id');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
}
