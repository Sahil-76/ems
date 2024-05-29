@extends('layouts.master')
@section('headerLinks')
<style>
.table td, .table th{
    padding: 5px;
}
</style>
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leave History</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 mb-3">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Filter</p>
                            {{Form::open(['method'=>'GET'])}}
                            <div class="form-group row">

                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{Form::select('department_id',$department, request()->department_id,
                                      ['onchange' => 'getEmployees(this.value)','class' => 'form-control selectJS','placeholder'=>'Select your Department'])}}
                                </div>

                                {{ Form::label('user_id', 'Select Name', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    <select style='width:100%;' name="user_id" data-placeholder="select an option"
                                        placeholder="select an option" class='form-control selectJS' id="employees">
                                        <option value="" disabled selected>Select an option</option>
                                        @foreach ($employeeDepartments as $department => $employees)
                                            <optgroup label="{{ $department }}">
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        @if ($employee->id == request()->user_id) selected @endif>
                                                        {{$employee->name.' ('.$employee->employee->biometric_id.')'}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>


                                {{ Form::label('leave_type_id', 'Select Leave Type ', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                     {{Form::select('leave_type_id', $leave_types, request()->leave_type_id,
                                     ['class' => 'form-control selectJS','placeholder'=>'Select your Leave Type'])}}
                                </div>

                                {{ Form::label('leave_session', 'Select Leave Session', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{Form::select('leave_session', $leave_session, request()->leave_session,
                                     ['class' => 'form-control selectJS','placeholder'=>'Select your Leave Type'])}}
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                    <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                                    {{ Form::close() }}
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                            {{Form::submit('Filter',['class'=>'btn btn-primary','style'=>'float:right;'])}}
                            </div>
                            {{Form::close()}}
                            <div class="col-md-6">

                             <a href="{{ route('exportLeave',request()->query()) }}" class="btn m-2 float-right btn-primary">Export</a>
                            <a href="{{request()->url()}}" class="btn btn-success">Clear Filter</a> --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-12">
            <!-- Default box -->

            <div class="card">

                <div class="card-body">
                    <p class="card-title float-left">Leave Approve List</p>
                    <div class="col-md-8 float-right text-right">
                        <b>Total Results: </b>{{ $leaves->total() }}
                    </div>
                        <br><br>
                        <div class="table-responsive">
                        <table id="" class="table  table-hover">

                            <thead>
                                <tr>
                                    <th style="white-space: normal">Type</th>
                                    <th  style="white-space: normal">Session</th>
                                    <th  style="white-space: normal">Department</th>
                                    <th  style="white-space: normal">Name</th>
                                    <th  style="white-space: normal">From Date</th>
                                    <th  style="white-space: normal">To Date</th>
                                    <th  style="white-space: normal">Duration</th>
                                    <th  style="white-space: normal">Applied At</th>
                                    <th  style="white-space: normal" class="text-center">Reason</th>
                                    <th  style="white-space: normal">Remarks</th>
                                    <th  style="white-space: normal">Status</th>
                                    <th  style="white-space: normal">Attachment</th>
                                    <th  style="white-space: normal">Update</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($leaves as $leave)

                                    <tr>
                                        <td  style="white-space: normal">{{$leave->leaveType->name}}</td>
                                        <td  style="white-space: normal">{{$leave->leave_session}}</td>
                                        <td  style="white-space: normal">{{optional($leave->user->employee)->department->name ?? ""}}</td>
                                        <td  style="white-space: normal">{{optional($leave->user)->name}}</td>
                                        <td  style="white-space: normal">{{getFormatedDate($leave->from_date)}}</td>
                                        <td  style="white-space: normal">{{getFormatedDate($leave->to_date)}}</td>
                                        <td  style="white-space: normal">{{$leave->duration}} {{Str::plural('Day', $leave->duration)}}</td>
                                        <td  style="white-space: normal">{{getFormatedDate($leave->created_at)}}</td>
                                        <td  style="white-space: normal"><textarea name="" id="" cols="30" rows="3" disabled>{{$leave->reason}}</textarea></td>
                                        <td  style="white-space: normal">{{$leave->remarks ?? 'N/A'}}</td>
                                        <td  style="white-space: normal">{{ucfirst($leave->status)}}</td>
                                        <td  style="white-space: normal">
                                        @if($leave->attachment)
                                        <a target="_blank" href="{{route('viewFile', ['file' => $leave->attachment])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                        @else
                                        N/A
                                        @endif
                                        </td>

                                        <td  style="white-space: normal">
                                            {{ Form::open(['route'=>'updateLeave']) }}
                                                {{ Form::hidden('id', $leave->id) }}
                                                    <button type="submit" onclick="return confirm('Are you sure?');"
                                                    class="btn btn-success btn-xl p-2"
                                                    >Pre Approve</button>
                                            {{ Form::close() }}
                                        </td>
                                    </tr>

                                @empty
                                <tr>
                                    <td colspan="6"><h4><marquee behavior="alternate" direction="right">No data available</marquee></h4></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-4 float-right">
                <div class="col-md-12 float-right">
                    {{ $leaves->appends(request()->query())->links() }}
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
            searching:false,
            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

            columnsDefs: [
                {
                    "name": "Nature",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Type",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Department"
                },
                {
                    "name": "Name"
                },
                {
                    "name": "From Date",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "To Date",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Duration",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Timing",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Reason",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Remarks",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Status",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Attachment",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Action",
                    sorting: false,
                    searching: false
                },

            ],

        });
    </script>

@endsection
