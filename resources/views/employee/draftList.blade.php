@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Draft List</li>
            </ol>
        </nav>
    </div>


            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary"></div>
                    <div class="card-body">
                        <div class="card-title">Filter</div>
                        {{ Form::open(['method' => 'GET']) }}
                        <div class="row">


                            <div class="col-3">
                                <div class="form-group">
                                    <div class="form-check form-check-primary">
                                        {{-- <label class="form-check-label">Draft  Profile Pic
                                            <input type="checkbox" @if(!empty(request()->draft_profile)) checked @endif onchange="this.form.submit()" name="draft_profile" class="form-check-input">
                                            <i class="input-helper"></i><i class="input-helper"></i></label> --}}
                                            <select name="draft_field" class="form-control selectJS" placeholder="Select Field"   onchange="this.form.submit()">
                                                <option value="" readonly>Select</option>
            
                                                @foreach($draftFields as $index=>$draftField)
            
                                                <option value="{{ $draftField }}"
                                                    {{ (request()->draft_field== $draftField )  ?' selected':'' }}>
                                                    {{  $draftField }}</option>
                                            @endforeach
                                            </select>
                                    </div>
                                </div>
                            </div>
                     

                        </div>
                        
                    
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Draft List</p>
  
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table">
                                    <table id="example1" class="table table-hover">
    
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Department</th>
                                                @can('hrEmployeeList',new App\Models\Employee())
                                                <th class="text-center">Action</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
    
                                            {{-- @foreach ($pendingProfiles as $pendingProfile)
                                                <tr>
                                                    <td>{{ $pendingProfile->employee->name ?? ''}}</td>
                                                    <td>{{ $pendingProfile->employee->department->name ?? null }}</td>
                                                    <td class="text-center"><a href="{{route('draftView',['employee'=>$pendingProfile->employee_id])}}" class="btn btn-primary btn-lg p-3">Action</a></td>
                                                </tr>
                                            @endforeach --}}
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
@php
    $query  =  http_build_query(request()->query());
@endphp
@endsection


@section('footerScripts')
<script>
    $('#example1').dataTable({
        processing: true,
        serverSide: true,
        ajax:'{{ route('draftList',$query)}}',
        columns: [
            {
                data: "employee_name"
            },
            
            {
                data: "department_name"
            },
            @can('hrEmployeeList',new App\Models\Employee()) 
            {
                data: "action",
                sorting: false,
                searching: false
            }
            @endcan,

        ],
        "columnDefs": [ {
            "targets": [2],
            "createdCell":function(td, cellData, rowData, row, col) {            
                $(td).addClass('text-center');
            },
            }
        ]
    });
</script>

@endsection
