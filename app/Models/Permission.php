<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    protected $table    = 'permission';
    protected $guarded  = ['id'];
    protected $appends  = ['module_name'];

    public function module() 
    {
        return $this->belongsTo(Module::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    public function getModuleNameAttribute()
    {
        return $this->module->name;
    }

    public function activity()
    {  
       return  $this->morphMany('App\Models\ActivityLog','module');
    }
}
