<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table    = 'departments';
    protected $guarded  = ['id'];
    protected $appends  = ['manager'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('hidden', function (Builder $builder) {
            $builder->where('is_hidden', null);
        });
    }

    function scopeAllowLineManager($query) {

        if (auth()->user()->hasRole('Line Manager')) {
            $query->orWhere('line_manager_id', auth()->user()->id);
        }else{
            $query->where('manager_id', auth()->user()->employee->id)->orWhere('team_leader_id', auth()->user()->employee->id);
        }
    }

    public function employees()
    {
        return $this->hasMany('App\Models\Employee', 'department_id');
    }

    public function deptManager()
    {
        return $this->belongsTo('App\Models\Employee','manager_id');
    }

    public function deptTeamLeader()
    {
        return $this->belongsTo('App\Models\Employee','team_leader_id');
    }

    public function lineManager() 
    {
        return $this->belongsTo(User::class, 'line_manager_id');
    }

    public function teams()
    {
        return $this->hasMany('App\Models\Team');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function activity()
    {
        return  $this->morphOne('App\Models\ActivityLog','module');
    }

    public function getManagerAttribute()
    {
        return $this->managerDetails()->name ?? '';
    }

    public function getTeamLeaderAttribute()
    {
        return $this->teamLeaderDetails()->name ?? '';
    }

    // public function managerDetails()
    // {
    //     $manager    =   $this->employees()->whereHas('user.roles', function($query){
    //                             $query->where('name', 'Manager');
    //                         })->first();

    //     return $manager;
    // }

    public function managerDetails()
    {
        return  $this->deptManager; 
    }
}
