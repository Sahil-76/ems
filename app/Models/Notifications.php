<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $guarded  = 'id';
    protected $table    = 'notification';
    protected $appends  = ['time'];

    function getTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
