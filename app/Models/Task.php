<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table        = 'tasks';
    protected $guarded      = 'id';

    public function department()
    {
        return $this->belongsTo('App\Models\Department','department_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_tasks','task_id' ,'user_id');
    }
}
