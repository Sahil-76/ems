@extends('layouts.master')
@section('headerLinks')
    <style>
        .card .card-title{
            margin-bottom: 0.2rem;
            color:#4b49ac;
        }
        .table.dataTable thead .sorting_asc {
            background-image: none !important;
        }
        .table{
            overflow: hidden !important;
        }
        .table tr{
            background: transparent !important;
        }
        .table thead th{
            font-size: 13px;
            font-weight: 800;
        }
        .table tbody td{
            font-size: 11px !important;
        }
        .table tfoot th{
            font-size: 14px;
        }
        .table td, .table th{
            padding: 5px;
        }

        table.dataTable > thead .sorting:before, table.dataTable > thead .sorting:after, 
        table.dataTable > thead .sorting_asc:before, table.dataTable > thead .sorting_asc:after, 
        table.dataTable > thead .sorting_desc:before, table.dataTable > thead .sorting_desc:after, table.dataTable > 
        thead .sorting_asc_disabled:before, table.dataTable > thead .sorting_asc_disabled:after, table.dataTable > 
        thead .sorting_desc_disabled:before, table.dataTable > thead .sorting_desc_disabled:after{
            font-size: 7px !important;
        }
        table.dataTable > thead > tr > th:not(.sorting_disabled), table.dataTable > thead > tr > td:not(.sorting_disabled){
            padding-right: 20px;
            width: 20% !important;
        }
        .dataTables_wrapper .dataTable thead .sorting:before, .dataTables_wrapper .dataTable thead .sorting_asc:before, .dataTables_wrapper .dataTable thead .sorting_desc:before, .dataTables_wrapper .dataTable thead .sorting_asc_disabled:before, .dataTables_wrapper .dataTable thead .sorting_desc_disabled:before{
            bottom: -2px;
        }
        .dataTables_wrapper .dataTable thead .sorting:after, .dataTables_wrapper .dataTable thead .sorting_asc:after, .dataTables_wrapper .dataTable thead .sorting_desc:after, .dataTables_wrapper .dataTable thead .sorting_asc_disabled:after, .dataTables_wrapper .dataTable thead .sorting_desc_disabled:after{
            top: -1px;
        }
        .shift-type  table.dataTable td,.shift-type table.dataTable th{    
            padding: 7px 22px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin transparent">
            <div class="row">

                <div class="col tretch-card transparent">
                    <div class="card card-tale">
                        <a style="color: white;" href="{{ route('employeeView',['user_type'=>['Employee','Office Junior']]) }} "  target="_blank">
                            <div class="card-body">
                                <p class="mb-4"> Total Employee</p>
                                <p class="fa-3x mb-2">{{ $employeeTotal }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col stretch-card transparent">
                    <div class="card card-light-danger">
                        <a style="color: white;"  href="{{ route('employeeView',['user_type'=>['Employee']]) }}" target="_blank">
                            <div class="card-body">
                                <p class="mb-4">Employees</p>
                                <p class="fa-3x mb-2">{{ $employeeCount }}</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col stretch-card transparent">
                    <div class="card card-dark-blue">
                        <a style="color: white;" href="{{ route('employeeView',['user_type'=>['Office Junior']]) }}" target="_blank">
                            <div class="card-body">
                                <p class="mb-4">Office Juniors</p>
                                <p class="fa-3x mb-2">{{ $officeJuniorCount }}</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col stretch-card transparent">
                    <div class="card card-tale">
                        <a style="color: white;" href="{{ route('departmentView') }}" target="_blank">
                            <div class="card-body">
                                <p class="mb-4">Departments</p>
                                <p class="fa-3x mb-2">{{ $departmentCount }}</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">By Department
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Department</th>
                                <th>Manager</th>
                                <th>Line Manager</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php  $employeesCount   = 0;   @endphp
                            @foreach ($departments as $department)
                                <tr>
                                    @php
                                        $employeesCount= $department->employees_count+$employeesCount;
                                    @endphp
                                    <td>{{$department->name}}</td>
                                    <td>{{ $department->deptManager->name ??'N/A'}}</td>
                                    <td>{{ $department->lineManager->name ??'N/A'}}</td>
                                    <td><a href="{{ route('employeeView', ['department_id'=>$department->id]) }}" target="_blank">{{ $department->employees_count}}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th  colspan="3">Total</th>
                                <th> <a  href="{{ route('employeeView',['user_type'=>['Employee']]) }}" target="_blank">{{ $employeesCount }}</a></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        {{-- {{$taskAssignments->appends(request()->input())->links()}} --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-xxl-5">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Unassigned Assets
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Department</th>
                                <th>Manager</th>
                                <th>Laptop</th>
                                <th>Mouse</th>
                                <th>Charger</th>
                                <th>Headphone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $laptopCount = 0;
                                $mouseCount = 0;
                                $chargerCount = 0;
                                $headphoneCount =0;
                            @endphp
                            @foreach ($departmentUnassignedAssets as $departmentUnassignedAssets)
                                <tr>
                                     @php
                                          $laptopCount = $laptopCount+$departmentUnassignedAssets->unassignedLaptops;
                                          $chargerCount = $chargerCount+$departmentUnassignedAssets->unassignedCharger;
                                          $headphoneCount = $headphoneCount+$departmentUnassignedAssets->unassignedHeadphn;
                                          $mouseCount=$mouseCount+$departmentUnassignedAssets->unassignedMouse;
                                     @endphp
                                    <td>{{ $departmentUnassignedAssets->name }}</td>
                                    <td>{{ $departmentUnassignedAssets->deptManager->name ?? 'N/A' }}</td>
                                    <td><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Laptop'] ,'unassigned'=>'on','department_id'=>$departmentUnassignedAssets->id]) }}" target="_blank">{{ $departmentUnassignedAssets->unassignedLaptops}}</a></td>
                                    <td><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Mouse'] ,'unassigned'=>'on','department_id'=>$departmentUnassignedAssets->id]) }}" target="_blank">{{ $departmentUnassignedAssets->unassignedMouse   }}</a></td>
                                    <td><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Charger'] ,'unassigned'=>'on','department_id'=>$departmentUnassignedAssets->id]) }}" target="_blank">{{ $departmentUnassignedAssets->unassignedCharger  }} </a></td>
                                    <td><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Headphone'] ,'unassigned'=>'on','department_id'=>$departmentUnassignedAssets->id]) }}" target="_blank">{{ $departmentUnassignedAssets->unassignedHeadphn}} </a></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Laptop'] ,'unassigned'=>'on']) }}" target="_blank">{{ $laptopCount }}</a></th>
                                <th><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Mouse'] ,'unassigned'=>'on']) }}" target="_blank">{{ $mouseCount }}</a></th>
                                <th><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Charger'] ,'unassigned'=>'on']) }}" target="_blank">{{ $chargerCount }}</th>
                                <th><a href="{{ route('assignmentList',['sub_type'=>$subTypes['Headphone'] ,'unassigned'=>'on']) }}" target="_blank">{{ $headphoneCount }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        {{-- {{$taskAssignments->appends(request()->input())->links()}} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>  
    <div class="row mt-2">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">By Shift Type
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Department</th>
                                <th>Manager Name</th>
                                <th>Morning</th>
                                <th>Evening</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $morningCount = 0;
                                $eveningCount = 0;
                                $headCount    = 0;
                            @endphp
                            @foreach ($ShiftTypeDepartments as $dept)
                                <tr>
                                    @php
                                        $morningCount=  $dept['Morning Shift']+$morningCount;
                                        $eveningCount= $dept['Evening Shift']+$eveningCount;
                                        $headCount= $dept['HeadCount']+$headCount;
                                    @endphp
                                    <td>{{ $dept['Name'] }}</td>
                                    <td>{{ $dept['Manager'] }} </td>
                                    <td><a href="{{ route('employeeView',['shift_type'=>'Morning','department_id'=>$dept['id']])}}" target="_blank"> {{ $dept['Morning Shift']}}</a></td>  {{-- link --}}
                                    <td><a href="{{ route('employeeView',['shift_type'=>'Evening','department_id'=>$dept['id']])}}" target="_blank">{{  $dept['Evening Shift']  }}</a></td>
                                    <td><a href="{{ route('employeeView',['department_id'=>$dept['id']])}}" target="_blank">{{ $dept['HeadCount'] }}</a> </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th  colspan="2">Total</th>
                                <th><a href="{{ route('employeeView',['shift_type'=>'Morning']) }}"  target="_blank">{{ $morningCount }}</a></th>
                                <th><a href="{{ route('employeeView',['shift_type'=>'Evening']) }}"  target="_blank">{{ $eveningCount }}</a></th>
                                <th><a  href="{{ route('employeeView') }}" target="_blank">{{ $headCount }}</a></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        {{-- {{$taskAssignments->appends(request()->input())->links()}} --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">By Gender
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Department</th>
                                <th>Manager</th>
                                <th>Male</th>
                                <th>Female</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $maleCount = 0;
                                $femaleCount = 0;
                                $totalCount = 0;
                            @endphp
                            @foreach ($byGenderTypes as $byGenderType)
                                <tr>
                                    @php
                                        $maleCount = $maleCount+$byGenderType->maleCount;
                                        $femaleCount = $femaleCount+$byGenderType->femaleCount;
                                        $totalCount = $totalCount+$byGenderType->employees_count;
                                    @endphp
                                    <td>{{ $byGenderType->name }}</td>
                                    <td>{{ $byGenderType->deptManager->name ?? 'N/A' }}</td>
                                    <td><a href="{{ route('employeeView',['department_id'=>$byGenderType->id,'gender'=>'male'])}}" target="_blank">{{ $byGenderType->maleCount}}</a></td>
                                    <td><a href="{{ route('employeeView',['department_id'=>$byGenderType->id,'gender'=>'female'])}}" target="_blank">{{ $byGenderType->femaleCount}}</a></td>
                                    <td><a href="{{ route('employeeView',['department_id'=>$byGenderType->id])}}" target="_blank">{{ $byGenderType->employees_count}}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th><a href="{{ route('employeeView',['gender'=>'male'])}}" target="_blank">{{ $maleCount }}</a></th>
                                <th><a href="{{ route('employeeView',['gender'=>'female'])}}" target="_blank">{{ $femaleCount }}</a></th>
                                <th><a href="{{ route('employeeView')}}" target="_blank">{{ $totalCount }}</a></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        {{-- {{$taskAssignments->appends(request()->input())->links()}} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
<script>

  $('.listtable').dataTable({
    searching:false,
    paging:false,
    info:false
  });
</script>
@endsection
