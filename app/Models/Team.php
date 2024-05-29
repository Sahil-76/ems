<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $table    =   'teams';
    protected $guarded  =   'id';

    public function department()
    {
        return $this->belongsTo('App\Models\Department','department_id');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

}
