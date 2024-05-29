<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'designation';
    protected $guarded = ['id'];

    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
    }
}
