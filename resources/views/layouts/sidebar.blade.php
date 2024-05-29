<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a style="cursor: default;" href="javascript:void(0)" class="nav-link">
                <i>
                    @if (!empty(auth()->user()->employee))
                        <img src="{{ auth()->user()->getImagePath() }}" alt="{{ auth()->user()->name }}"
                            width="40" height="40" style="border-radius: 100%;" />
                    @else
                        <img src="404" alt="" width="40" height="40" style="border-radius: 100%;">
                    @endif
                </i>
                <span class="menu-title" style="padding-left:10px;">{{ ucfirst(auth()->user()->name) }}
                    @if (!empty(auth()->user()->employee) && auth()->user()->employee->is_power_user)
                        <i title="Power User" class="fa fa-bolt ml-3 text-warning" style="font-size:18px;"></i>
                    @endif
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        {{-- Admin Panel --}}
        @if (Auth::user()->can('view', new App\User()) ||
            Auth::user()->can('view', new App\Models\Role()) ||
            Auth::user()->can('view', new App\Models\Permission()) ||
            Auth::user()->can('view', new App\Models\Module()))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#admin" aria-expanded="false" aria-controls="admin">
                    <i class="icon-head menu-icon"></i>
                    <span class="menu-title">Admin</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="admin">
                    <ul class="nav flex-column sub-menu">
                        @can('view', new App\User())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('user.index') }}">User</a></li>
                        @endcan
                        @can('view', new App\Models\Role())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('role.index') }}">Role</a></li>
                        @endcan
                        @can('view', new App\Models\Permission())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('permission.index') }}">Permission</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\Module())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('modules.index') }}">Module</a></li>
                        @endcan
                        @can('view', new App\Models\Role())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('assignRoles') }}">Assign Role</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('bulkAssignRole') }}">Bulk Assign
                                    Role</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endif

        {{-- HR Panel --}}
        @if (Auth::user()->can('hrEmployeeList', new App\Models\Employee()) ||
             Auth::user()->can('pendingProfile', new App\Models\Employee()) ||
             Auth::user()->can('hrUpdateEmployee', new App\Models\Employee()))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#hr" aria-expanded="false" aria-controls="hr">
                    <i class="icon-columns menu-icon"></i>
                    <span class="menu-title">HR</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="hr">
                    <ul class="nav flex-column sub-menu">
                        @can('hrEmployeeList', new App\Models\Employee())
                            <li class="nav-item"><a class="nav-link" href="{{ route('employeeView') }}">Employee List</a>
                            </li>
                            @can('hrUpdateEmployee', new App\Models\Employee())
                            <li class="nav-item"><a class="nav-link" href="{{ route('employeeDashboard') }}">Employee
                                    Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('performanceDashboard') }}">Performance
                                    Dashboard</a>
                            </li>

                            <li class="nav-item"><a class="nav-link" href="{{ route('exitList') }}">Exit Employee List</a>
                            </li>
                            @endif
                        @endcan
                        @can('pendingProfile', new App\Models\Employee())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pendingProfile') }}">Pending Profiles
                                    @if ($commonCount['pendingProfiles'] != 0)
                                        <span
                                            class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0"
                                            style="width:18px; height:18px; font-size:10px;">{{ $commonCount['pendingProfiles'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endcan
                        @can('hrEmployeeList', new App\Models\Employee())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('draftList') }}">Draft List
                                    @if ($commonCount['drafts'] != 0)
                                        <span
                                            class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0 ml-3"
                                            style="width:18px; height:18px; font-size:10px;">{{ $commonCount['drafts'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endif

        {{-- Leave Panel --}}
        @if (Auth::user()->can('hrEmployeeList', new App\Models\Employee()))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#leave-tab" aria-expanded="false"
                    aria-controls="leave-tab">
                    <i class="icon-clock menu-icon"></i>
                    <span class="menu-title">Leave</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="leave-tab">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('manual-leave.index') }}">Manual Leave
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('hrLeaveList') }}">Leave Requests
                                @if ($commonCount['managerLeaves'] != 0)
                                    <span
                                        class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0"
                                        style="width:18px; height:18px; font-size:10px;">{{ $commonCount['managerLeaves'] }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('leaveBalanceDashboard') }}">Balance Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forwardedLeaveList') }}">Forwarded Leaves
                                @if ($commonCount['forwardedLeaves'] != 0)
                                    <span
                                        class="badge badge-light text-dark d-flex justify-content-center align-items-center ml-1"
                                        style="width:18px; height:18px; font-size:10px;">{{ $commonCount['forwardedLeaves'] }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('hrLeaveHistory') }}">Leave History</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('hrLeaveStatusUpdateList') }}">Leave Update</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        {{-- Attendance Panel --}}
        @if (Auth::user()->can('create', new App\Models\Attendance()) ||
             Auth::user()->can('dashboard', new App\Models\Attendance()))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#attendance" aria-expanded="false"
                    aria-controls="attendance">
                    <i class="icon-align-justify menu-icon"></i>
                    <span class="menu-title">Attendance</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="attendance">
                    <ul class="nav flex-column sub-menu">
                        @can('dashboard', new App\Models\Attendance())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('attendanceDashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lateAttendanceDashboard') }}">Late Dashboard</a>
                            </li>
                        @endcan
                        @can('create', new App\Models\Attendance())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('manual-attendance.create') }}">Manual Attendance</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endif

        {{-- Asset Panel --}}
        @can('assetPermission', new App\User())
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#it" aria-expanded="false" aria-controls="it">
                    <i class="icon-bar-graph menu-icon"></i>
                    <span class="menu-title">Asset</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="it">
                    <ul class="nav flex-column sub-menu">
                        @can('dashboard', new App\Models\Asset())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('assetDashboard') }}">Asset Dashboard</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\Asset())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('asset.index') }}">Asset</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\AssetCategory())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('asset-category.index') }}">Asset Category</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\AssetType())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('asset-type.index') }}">Asset Type</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\AssetSubType())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('asset-subtype.index') }}">Asset Sub Type</a>
                            </li>
                        @endcan
                        @can('assignmentList', new App\Models\Asset())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('assignmentList') }}">Assignment List</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        {{-- Manager Panel --}}
        @if (Auth::user()->can('managerLeaveList', new App\Models\Leave()))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#manager" aria-expanded="false"
                    aria-controls="manager">
                    <i class="icon-grid-2 menu-icon"></i>
                    <span class="menu-title">Manager</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="manager">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('employeeManagerDashboard') }}">Employee Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('managerLeaveList') }}">Leave Requests
                                @if ($commonCount['departmentLeaves'] != 0)
                                    <span
                                        class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0"
                                        style="width:18px; height:18px; font-size:10px;">{{ $commonCount['departmentLeaves'] }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('managerLeaveHistory') }}">Leave History</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('managerEmployeeView') }}">Employee List</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        {{-- Team --}}
        {{-- @if(Auth::user()->can('hrUpdateEmployee', new App\Models\Employee()) || Auth::user()->can('managerDashboard', new App\User())) --}}
        @canany(['view', 'create', 'update'], new App\Models\Task())
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#team" aria-expanded="false"
                    aria-controls="team">
                    <i class="fa fa-users menu-icon"></i>
                    <span class="menu-title">Team</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="team">
                    <ul class="nav flex-column sub-menu">
                        @canany(['view', 'create', 'update'], new App\Models\Team())
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('team.index') }}">Team List</a>
                            </li>
                        @endcanany
                        @can('dashboard', new App\Models\Team())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('teamDashboard') }}">Team Dashboard</a>
                            </li>
                        @endcan
                        @canany(['view', 'create', 'update'], new App\Models\Task())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('task.index') }}">Task List</a>
                            </li>
                        @endcanany
                        @can('trainingView', new App\Models\Task())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('training.index') }}">On Training</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
        {{-- @endif --}}

        {{-- Electricity --}}

        @if(Auth::user()->can('view', new App\Models\Electricity()) || Auth::user()->can('dashboard', new App\Models\Electricity()))
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#electricity" aria-expanded="false"
                    aria-controls="electricity">
                    <i class="fa fa-bolt menu-icon"></i>
                    <span class="menu-title">Electricity</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="electricity">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link" href="{{ route('electricity.create') }}">Create</a>
                        </li>
                        @can('view', new App\Models\Electricity())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('electricity.index') }}">Units List</a>
                            </li>
                        @endcan
                        @can('dashboard', new App\Models\Electricity())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('electricity.dashboard') }}">Dashboard</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endif

        {{-- No Dues Requests Panel --}}
        @canany(['hrNoDuesApprover', 'itNoDuesApprover', 'managerNoDuesApprover'], new App\Models\Employee())
            <li class="nav-item">
                <a class="nav-link" href="{{ route('noDuesRequests') }}">
                    <i class="mdi mdi-clock-alert menu-icon fa-lg"></i>
                    <span class="menu-title">No Dues Requests</span>
                </a>
            </li>
        @endcanany

        {{-- Employees Panels --}}
        @if (auth()->user()->hasRole('employee') ||
            //  auth()->user()->hasRole('admin') ||
             auth()->user()->hasRole('powerUser'))
            {{-- My Profile Panel --}}
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#employee" aria-expanded="false"
                    aria-controls="manager">
                    <i class="mdi mdi-account-settings menu-icon"></i>
                    <span class="menu-title">My Profile</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="employee">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('employeeDetail', ['employee' => Auth::user()->employee->id]) }}">Profile</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('editProfile', ['employee' => Auth::user()->employee->id]) }}">Edit Profile</a>
                        </li>
                        <li class="nav-item "> <a class="nav-link" href="{{ route('leaveList') }}">Leave List</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('myBalance') }}">My Balance</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('myAttendance') }}">My Attendance</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        @if (auth()->user()->hasRole('employee') ||
        auth()->user()->hasRole('admin') ||
        auth()->user()->hasRole('powerUser') || auth()->user()->hasRole('Line Manager'))

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#daily-report" aria-expanded="false"
                aria-controls="daily-report">
                <i class="mdi mdi-book-open-page-variant  menu-icon"></i>
                <span class="menu-title">Daily Report</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse ml-sm-n4" id="daily-report">
                <ul class="nav flex-column sub-menu">
                    @if (auth()->user()->hasRole('employee'))
                    <li class="nav-item"> <a class="nav-link" href="{{ route('dailyReport.form') }}">Submit Report</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('dailyReport.myList') }}">My Reports</a>
                    </li>
                    @endif
                    @if (auth()->user()->can('hrUpdateEmployee', new App\User()) ||
                         auth()->user()->can('managerDashboard', new App\User()))
                        <li class="nav-item"> <a class="nav-link" href="{{ route('dailyReport.departmentReports') }}">
                            Department Reports</a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>

        @endif

        {{-- Settings Panel --}}
        @if (auth()->user()->can('checkPermission', new App\User()) ||
            auth()->user()->can('barcodeListView', new App\Models\Employee()))

            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#settings" aria-expanded="false"
                    aria-controls="settings">
                    <i class="icon-cog menu-icon"></i>
                    <span class="menu-title">Settings</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse ml-sm-n4" id="settings">
                    <ul class="nav flex-column sub-menu">
                        @can('hrEmployeeList', new App\Models\Employee())
                            <li class="nav-item"> <a class="nav-link" href="{{ url('attendance/fetch/form') }}">Fetch Attendance</a> </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('shift-type.index') }}">Shift Type</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('updateEmployeeDepartment') }}">Update Department</a></li>
                        @endcan
                        @can('view', new App\Models\Qualification())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('qualificationView') }}">Qualification</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\Department())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('departments.index') }}">Department</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('designation.index') }}">Designation</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\LeaveType())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('leave-type.index') }}">Leave Type</a>
                            </li>
                        @endcan
                        @can('view', new App\Models\Badge())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('badge.index') }}">Badge</a></li>
                        @endcan
                        @can('view', new App\Models\Announcement())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('announcement.index') }}">Announcement</a>
                            </li>
                        @endcan
                        @can('barcodeListView', new App\Models\Employee())
                            <li class="nav-item"> <a class="nav-link" href="{{ route('barcodeList') }}">Barcode List</a>
                            </li>
                        @endcan

                    </ul>
                </div>
            </li>
        @endif

        {{-- Activity Logs Panel --}}
        @can('view', new App\Models\ActivityLog())
            <li class="nav-item">
                <a class="nav-link" href="{{ route('activityLogView') }}">
                    <i class="mdi mdi-bullseye menu-icon fa-lg"></i>
                    <span class="menu-title">Activity Logs</span>
                </a>
            </li>
        @endcan

    </ul>
</nav>
