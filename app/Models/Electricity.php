<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Electricity extends Model
{
    use HasFactory;
    protected $table    =   "electricity";
    protected $guarded  =   ['id'];
    protected $appends  =   ['total_units'];


    public function getTotalUnitsAttribute()
    {
        if(empty($this->end_unit)){return null;}
        return $this->end_unit  -   $this->start_unit;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return  $this->morphMany('App\Models\ActivityLog','module');
    }

}
