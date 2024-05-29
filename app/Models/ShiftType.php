<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ShiftType extends Model
{
    protected $table    = 'shift_types';
    protected $guarded  = ['id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }
}
