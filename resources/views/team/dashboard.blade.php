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
            font-size: 15px;
            font-weight: 800;
        }
        .table tbody td{
            font-size: 13px !important;
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
    <h3><strong>Team Dashboard</strong></h3>
    <div class="row">
        @foreach($data as $department => $teams)
        <div class="col-sm-6 col-xxl-6">
            <div class="card mt-3">
                <div class="card-body table-responsive">
                    <div class="card-title">{{$department}}
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Team</th>
                                <th>Reporting person</th>
                                <th class="text-center">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalCount = 0;
                            @endphp
                            @foreach ($teams as $team)
                                @php
                                    $totalCount = $totalCount + $team->users_count;
                                @endphp
                                <tr>
                                    <td>{{$team->name}}</td>
                                    <td>{{$team->reporting_person}}</td>
                                    <td class="text-center" onclick='showUsers("{{$team->id}}")'>
                                        <span class="badge badge-dark" style="cursor:pointer">
                                            {{$team->users_count}}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="color: blue">Total</th>
                                <td></td>
                                <td class="text-center">
                                    <span class="badge badge-primary" > {{ $totalCount }} </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div> 
    <div class="modal fade" id="usersModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
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

  function showUsers(id) {
            $.ajax({
                url: "{!! route('team.users') !!}",
                type: 'GET',
                data: {"id": id},
                success: function (response) {
                    $('#usersModal .modal-content').html(response.view);
                    $('#usersModal').modal('show');
                }
            });
        }
</script>
@endsection
