<?php

namespace App\Providers;

use App\User;
use App\Models\Role;
use App\Models\Leave;
use App\Models\Module;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Permission;
use App\Observers\RoleObserver;
use App\Observers\UserObserver;
use App\Observers\LeaveObserver;
use App\Observers\ModuleObserver;
use App\Observers\DocumentObserver;
use App\Observers\EmployeeObserver;
use App\Models\EmployeeProfileDraft;
use App\Observers\DepartmentObserver;
use App\Observers\PermissionObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\EmployeeProfileDraftObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Relation::morphMap([

            'employee'             => 'App\Models\Employee',
            'department'           => 'App\Models\Department',
            'attendance'           => 'App\Models\Attendance',
            'user'                 => 'App\User',
            'role'                 => 'App\Models\Role',
            'permission'           => 'App\Models\Permission',
            'module'               => 'App\Models\Module',
            'electricity'          => 'App\Models\Electricity',
            'document'             => 'App\Models\Document',
            'leave'                => 'App\Models\Leave',

        ]);

        Department::observe(DepartmentObserver::class);
        Employee::observe(EmployeeObserver::class);
        Role::observe(RoleObserver::class);
        Permission::observe(PermissionObserver::class);
        Module::observe(ModuleObserver::class);
        Document::observe(DocumentObserver::class);
        User::observe(UserObserver::class);
        Leave::observe(LeaveObserver::class);
        EmployeeProfileDraft::observe(EmployeeProfileDraftObserver::class);

    }
}
