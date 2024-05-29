@extends('layouts.master')
@section('content-header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Attendance Dashboard</h1>
        </div>
    </div>
@endsection
@section('headerLinks')
    <style>
        ::-webkit-scrollbar {
            width: 5px;
            height: 3px;
            border-radius: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #ced4da;
        }

        ::-webkit-scrollbar-thumb {
            background: #4b49ac;
        }

        .attendence .table {
            margin-top: 0 !important;
        }

        .attendence .table-responsive {
            height: 500px;
            width: auto;
            overflow: scroll;
        }

        .attendence .table thead tr {
            position: sticky;
            top: -1px;
            box-shadow: 0 0px 3px 1px #ced4da inset;
            z-index: 1;
        }

        .attendence .table tr th {
            box-shadow: 0 0px 3px 1px #ced4da inset;
            vertical-align: middle !important;
        }

        .attendence .table tr th:nth-child(1),
        tr td:nth-child(1) {
            position: sticky;
            left: -1px;
            box-shadow: 0 0px 3px 1px #ced4da inset;
            background-color: white;
        }

        .attendence .table td,
        th {
            font-size: 12px !important;
            padding: 10px 30px 10px 10px !important;
            background-color: white;
        }

        .attendence .dataTables_wrapper .dataTable thead .sorting:before,
        .attendence .dataTables_wrapper .dataTable thead .sorting_asc:before,
        .attendence .dataTables_wrapper .dataTable thead .sorting_desc:before,
        .attendence .dataTables_wrapper .dataTable thead .sorting_asc_disabled:before,
        .attendence .dataTables_wrapper .dataTable thead .sorting_desc_disabled:before {
            bottom: 3px;
        }

        .daterangepicker .calendar-table th,
        .daterangepicker .calendar-table td {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            min-width: 32px;
            width: 32px;
            height: 24px;
            line-height: 24px;
            font-size: 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            white-space: nowrap;
            cursor: pointer;
            position: unset;
            left: 0;
            box-shadow: none;
            padding: 0 !important;
        }

        .attendence .dataTables_wrapper .dataTable thead .sorting:after,
        .attendence .dataTables_wrapper .dataTable thead .sorting_asc:after,
        .attendence .dataTables_wrapper .dataTable thead .sorting_desc:after,
        .attendence .dataTables_wrapper .dataTable thead .sorting_asc_disabled:after,
        .attendence .dataTables_wrapper .dataTable thead .sorting_desc_disabled:after {
            top: 6px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin transparent">
            <div class="row">
                <div class="col-sm-3 tretch-card transparent">
                    <div class="card card-dark-blue">
                        @if (auth()->user()->hasRole('admin') ||
                            auth()->user()->hasRole('HR'))
                            <a style="color: white;" target="_blank" href="{{ route('employeeView') }}">
                            @elseif(auth()->user()->hasRole('powerUser'))
                                <a style="color: white;" href="#">
                                @else
                                    <a style="color: white;" target="_blank" href="{{ route('employeeManagerDashboard') }}">
                        @endif
                        <div class="card-body">
                            <p class="mb-4"><i class="mdi mdi-account"></i> Total Users</p>
                            <p class="fa-2x mb-2">{{ $totalUsers }}</p>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-3 mt-1 tretch-card transparent">
                    <div class="card card-tale">
                        <a style="color: white;"
                            href="{{ route('attendanceDashboard', [
                                'attendanceDateFrom' => request()->attendanceDateFrom,
                                'attendanceDateTo' => request()->attendanceDateTo,
                                'today_punched_in' => '1',
                            ]) }}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account"></i> Today Punched In</p>
                                <p class="fa-2x mb-2">{{ $todayPunchIn }}</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-3 mt-1 tretch-card transparent">
                    <div class="card card-light-danger">
                        <a style="color: white;"
                            href="{{ route('attendanceDashboard', [
                                'attendanceDateFrom' => request()->attendanceDateFrom,
                                'attendanceDateTo' => request()->attendanceDateTo,
                                'today_punched_not_in' => '1',
                            ]) }}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account"></i> Today Not Punched In</p>
                                <p class="fa-2x mb-2">{{ $todayNotPunchIn }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-sm-3 mt-1 tretch-card transparent">
                    <div class="card card-light-danger">
                        <a style="color: white;"
                            href="{{ route('attendanceDashboard', [
                                'attendanceDateFrom' => request()->attendanceDateFrom,
                                'attendanceDateTo' => request()->attendanceDateTo,
                                'yesterday_not_punch_out' => '1',
                            ]) }}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account"></i> Yesterday Not Punched Out</p>
                                <p class="fa-2x mb-2">{{ $yesterdayNotPunchOut }}</p>
                            </div>
                        </a>
                    </div>
                </div>


                <div class="col-sm-3 mt-1 tretch-card transparent">
                    <div class="card card-light-danger">
                        <a style="color: white;"
                            href="{{ route('attendanceDashboard', [
                                'attendanceDateFrom' => request()->attendanceDateFrom,
                                'attendanceDateTo' => request()->attendanceDateTo,
                                'on_half_day' => '1',
                            ]) }}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account"></i> Leave Today (Half Day)</p>
                                <p class="fa-2x mb-2">{{ $totalOnHalfDayLeaveToday }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-sm-3 mt-1 tretch-card transparent">
                    <div class="card card-light-blue">
                        <a style="color: white;"
                            href="{{ route('attendanceDashboard', [
                                'attendanceDateFrom' => request()->attendanceDateFrom,
                                'attendanceDateTo' => request()->attendanceDateTo,
                                'on_full_day' => '1',
                            ]) }}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account"></i> Leave Today (Full Day)</p>
                                <p class="fa-2x mb-2">{{ $totalOnFullDayLeaveToday }}</p>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary" id="accordion">
                    <p class="text-white">Filter <a href="javaScript:void(0)"><i id="fas"
                                class="float-right fas fa-plus text-white"></i></a></p>
                </div>
                <div class="card-body" id="title">
                    {{ Form::open(['method' => 'GET']) }}
                        <div class="row">
                            @can('hrEmployeeList', new App\Models\Employee())
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="responsibleUser">Select Department</label>
                                        {{ Form::select('department_id', $departments, request()->department_id ?? null, ['onchange' => 'getEmployees(this.value)', 'class' => 'form-control selectJS', 'data-placeholder' => 'Select Department', 'placeholder' => 'Select Department']) }}
                                    </div>
                                </div>
                            @endcan
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="responsibleUser">Select User</label>
                                    <select style='width:100%;' name="user_id" data-placeholder="select an option"
                                        placeholder="select an option" class='form-control selectJS' id="employees">
                                        <option value="" disabled selected>Select an option</option>
                                        @foreach ($employeeDepartments as $department => $employees)
                                            <optgroup label="{{ $department }}">
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        @if ($employee->id == request()->user_id) selected @endif>
                                                            {{ $employee->name . ' (' . $employee->employee->biometric_id . ')' }}
                                                    </option>   
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <label for="responsibleUser">Select Shift Type</label>
                                <select class="form-control selectJS " name="shift_id" value="option_select">
                                    <option value="" readonly> Select </option>
                                    @foreach ($shiftTypes as $shiftType)
                                        <option value="{{ $shiftType->id }}" @if (request()->shift_id == $shiftType->id) selected @endif>
                                            {{ $shiftType->name . ' (' . $shiftType->start_time . '-' . $shiftType->end_time . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @can('hrEmployeeList', new App\Models\Employee())
                                <div class="col-sm-4">
                                    <label for="responsibleUser">Select Employee Type</label>
                                    {{ Form::select('user_type', $userTypes, request()->user_type ?? null, ['class' => 'form-control selectJS', 'data-placeholder' => 'Select Employee Type', 'placeholder' => 'Select Department']) }}
                                </div>
                            @endcan
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="responsibleUser">Is Late Today</label>
                                    {{ Form::checkbox('is_late_today', '1', request()->is_late_today ?? null) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input id="dateFrom" name="attendanceDateFrom"
                                        value="{{ request()->attendanceDateFrom }}" type="hidden">
                                    <input id="dateTo" name="attendanceDateTo" value="{{ request()->attendanceDateTo }}"
                                        type="hidden">
                                    <button type="button" id="date-btn" class="btn btn-block   btn-primary"
                                        style="width:185px;margin-top: 31px;">
                                        @if (request()->has('attendanceDateFrom') && request()->has('attendanceDateTo'))
                                            @php
                                                $startDate = request()->attendanceDateFrom;
                                                $endDate = request()->attendanceDateTo;
                                            @endphp
                                            <span>
                                                {{ Carbon\Carbon::parse(request()->get('attendanceDateFrom'))->format('d/m/Y') }}
                                                -
                                                {{ Carbon\Carbon::parse(request()->get('attendanceDateTo'))->format('d/m/Y') }}
                                            </span>
                                        @else
                                            @php
                                                $startDate = now()->format('Y-m-d');
                                                $endDate = $startDate;
                                            @endphp
                                            <span>
                                                <i class="fa fa-calendar"></i> &nbsp;Select Date&nbsp;
                                            </span>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ request()->url() }}" class="btn btn-success">Clear</a>
                            </div>
                            @can('hrEmployeeList', new App\Models\Employee())
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('attendanceExport', request()->query()) }}" class="btn btn-danger">Export
                                        <i class="fa fa-download mr-1"></i></a>
                                </div>
                            @endcan
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row attendance mt-4" id="attendence-table">
        @include('attendance.attendanceTable')
    </div>
@endsection
@section('footerScripts')
    @php
        $query = http_build_query(request()->query());
    @endphp
    <script>
        $('body').addClass('sidebar-icon-only');
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
            },
            function(start, end) {
                $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
                $('#dateFrom').val(start.format('YYYY-M-DD'));
                $('#dateTo').val(end.format('YYYY-M-DD'));
                $('#date-form').closest('form').submit();
            }
        );

        $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('date-btn', 'date');
            $('#date-form').closest('form').submit();
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }

        $('.listtable').dataTable({});

        $(window).on('load', function() {
            $('#loader').show();
            var query = '{!! $query !!}';
            if (query != '') {
                $('#title').show();
                $("#fas").removeClass("fas fa-plus").addClass("fas fa-minus");
            } else {
                $('#title').hide();
                $("#fas").addClass("fa-plus");
            }
            $('#accordion').click(function() {
                $('#title').toggle();
                $("#fas").toggleClass('fa-plus fa-minus');
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
                        $.each(response, function(key, user) {
                            options += "<option value='" + user.user_id + "'>" + user.name + "(" + user
                                .biometric_id + ")"
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
