@extends('layouts.master')
@section('headerLinks')
<style>
    .table td{
        padding:5px;
    }

    .table th{
        padding-right: 5px;
        padding-left: 5px;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manual Leave List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12 mb-3">
        <div class="row">
            <div class="card">
                {{ Form::open(['method' => 'GET']) }}
                <div class="card-body">
                    <p class="card-title">Filter</p>
                    <div class="form-group row">
                        {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('department_id', $departments, request()->department_id, ['onchange' =>
                            'getEmployees(this.value)','class' => 'form-control selectJS','placeholder' => 'Select
                            Department']) }}
                        </div>

                        {{ Form::label('employee', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            <select style='width:100%;' name='user_id' class="form-control selectJS"
                                data-placeholder="Select an option" placeholder="select an option" id="employees">
                                <option value="" disabled selected>Select your option</option>
                                @foreach ($employeeDepartments as $department=> $employees)
                                <optgroup label="{{$department}}">
                                    @foreach($employees as $employee)
                                    <option value="{{$employee->id}}" @if($employee->id == request()->user_id) selected
                                        @endif>{{$employee->name.' ('.$employee->employee->biometric_id.')'}}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{ Form::label('leave_type_id', 'Select Leave Type', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('leave_type_id', $leaveTypes, request()->leave_type_id, ['class' =>
                            'form-control selectJS','placeholder' => 'Select Leave Type']) }}
                        </div>

                        {{ Form::label('leave_session', 'Select Session', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('leave_session', $sessions, request()->leave_session, ['class' =>
                            'form-control selectJS','placeholder' => 'Select Session']) }}
                        </div>
                        <div class="col-sm-4 mt-2">
                            <button type="button" class="btn btn-sm btn-primary" name="daterange" id="date-btn"
                                value="Select Date">
                                @if (request()->has('dateFrom') && request()->has('dateTo'))
                                <span>
                                    {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y') }} -
                                    {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y') }}
                                </span>
                                @else
                                <span>
                                    <i class="fa fa-calendar"></i> &nbsp;Filter Date&nbsp;
                                </span>
                                @endif
                                <i class="fa fa-caret-down"></i>
                            </button>
                            {{ Form::hidden('dateFrom', request()->dateFrom ?? null, ['id' => 'dateFrom']) }}
                            {{ Form::hidden('dateTo', request()->dateTo ?? null, ['id' => 'dateTo']) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                            <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                            {{ Form::close() }}
                        </div>
                        @can('hrEmployeeList', new App\Models\Employee())
                        <div class="col-sm-6">
                            <a href="{{ route('manual-leave.create') }}" class="btn m-2 float-md-right btn-success">
                                Add new Record <i class=""></i>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="card">
                <div class="card-body table-responsive">
                    <p class="card-title float-left">Manual Leave List</p>
                    <div class="table-responsive">
                        <table id="example1" class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Duration</th>
                                    <th>Timing</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Attachment</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
$query = http_build_query(request()->query());
@endphp

@endsection

@section('footerScripts')
<script>
    $('body').addClass('sidebar-icon-only');
    var filterColumns = [0, 1];
        $('#date-btn').daterangepicker({
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 5 Days': [moment().subtract(4, 'days'), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                },
                startDate: moment().subtract(5, 'days'),
                endDate  : moment()
            },
            function(start, end) {
                $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
                $('#dateFrom').val(start.format('YYYY-M-DD'));
                $('#dateTo').val(end.format('YYYY-M-DD'));
            }
        );

        $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('date-btn', 'date');
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }

        $(window).on('load', function(){
        $('#example1').dataTable({
            order:['4','asc'],
            processing: true,
            serverSide: true,
            ajax: {
            url: "{!! route('getManualLeave', $query) !!}",
        },
            columns: [
                {
                    data: 'leave_session',
                },
                {
                    data: 'leaveType',
                },
                {
                    data: 'userName',
                },
                {
                    data: 'departmentName',
                },
                {
                    data: 'from_date',
                },
                {
                    data: 'to_date',
                },
                {
                    data: 'duration',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'timing',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'reason',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'status',
                },
                {
                    data: 'attachment',
                    searchable: false,
                    orderable: false,
                },
        ]
        });
    });

        function getEmployees(department_id) {
                if (department_id) {
                    $.ajax({
                        url: "{{ route('getUsers') }}/" + department_id,
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {
                            var options = `<option value=''></option>`;
                            $.each(response, function(key,user) {
                                options += "<option value='" + user.user_id + "'>" +user.name +"("+user.biometric_id+")"
                                    "</option>";
                            });

                            $('#employees').html(options);
                            $("select").select2({
                                placeholder: "Select an option",
                                allowClear: true,
                            });
                        }
                    })
                }
            }

</script>
@endsection