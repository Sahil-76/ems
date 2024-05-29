<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    protected $table    = 'role';
    protected $guarded  = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('display_order', function (Builder $builder) {
            $builder->orderBy('display_order');
        });
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'role_user', 'role_id', 'user_id'); 
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function hasPermission($moduleName, $access = null)
    {
        if(empty(Module::$cache))
        {
            Module::$cache = Module::all()->map(function($item,$key){
                $item->name = strtolower($item->name);
                return $item;
            });
        }
        
        $module         = Module::$cache->where('name', strtolower($moduleName))->first();
        $module_id      = empty($module)? null : $module->id;
        $permissions    = $this->permissions->map(function ($item, $key) {
                                    $item->access = strtolower($item->access);
                                    return $item;
                                });

        if(empty($module_id) || $permissions->isEmpty())
        {
            return FALSE;
        }

        $result     = $permissions->where('module_id', $module_id);

        if(!empty($access))
        {
            $result = $result->where('access', strtolower($access));
        }
        
        if($result->isEmpty())
        {
            return False;
        }
        return true;
    }

    public function activity()
    {  
        return  $this->morphMany('App\Models\ActivityLog','module');
    }
}
