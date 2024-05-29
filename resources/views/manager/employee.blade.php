@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Employee List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Employee List : Department</p>
  
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table id="example1" class="table table-hover">
    
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Email</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    
                                            @foreach ($employees as $employee)
                                                <tr>
                                                    <td>{{ $employee->name }}</td>
                                                    <td>{{ $employee->department->name ?? null }}</td>
                                                    <td>{{ $employee->office_email}}</td>
                                                    <td><a href="{{route('employeeDetail',['employee'=>$employee->id])}}" class="btn btn-warning btn-lg p-3">Details</a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
  
            </div>
        </div>
    </div>
</div>
@endsection


@section('footerScripts')
<script>
    $('#example1').dataTable({
        ordering: false,
        fixedColumns: true,

        columnsDefs: [
            {
                "name": "Name"
            },
            
            {
                "name": "Department"
            },
            {
                "name": "Email"
            },
            
            {
                "name": "Details",
                sorting: false,
                searching: false
            },

        ],
        initComplete: function() {
            var data = this;
            this.api().columns([0, 1, 2]).every(function() {
                var column = this;
                var columnName = $(column.header()).text();
                var select = $('<select class="selectJS form-control" data-placeholder="' +
                        columnName + '"><option value=""></option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        if (val == "all") {
                            val = "";
                        }
                        column
                            .search(val ? '^' + val + '$' : '', true, true)
                            .draw();
                    });
                select.append('<option value="all">All</option>')
                column.data().unique().each(function(d, j) {
                    select.append('<option value="' + d + '">' + d +
                        '</option>')
                });
            });
        }
    });
</script>

@endsection
