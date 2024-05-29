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

   
  
    <div class="row mb-3">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Shift Type
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->user->shiftType->name ?? null }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Gender
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->gender }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8 col-xxl-5">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Assets
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Employee Name</th>
                                <th>Equipments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assetAssignments as $employeeName=>$assetAssignment)
                                <tr>
                                    <td>{{ $employeeName }}</td>
                                    <td><p ><span style="color:green;">{{ $assetAssignment['assigned'] }}</span><span style="color:red;">{{ $assetAssignment['unAssigned'] }}</span> </p></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
