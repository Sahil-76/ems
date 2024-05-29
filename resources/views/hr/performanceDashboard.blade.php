@extends('layouts.master')
@section('headerLinks')
    <style>
        .card .card-title {
            margin-bottom: 0.2rem;
            color: #4b49ac;
        }

        .table.dataTable thead .sorting_asc {
            background-image: none !important;
        }

        .table {
            overflow: hidden !important;
        }

        .table tr {
            background: transparent !important;
        }

        .table thead th {
            font-size: 13px;
            font-weight: 800;
        }

        .table tbody td {
            font-size: 11px !important;
        }

        .table tfoot th {
            font-size: 14px;
        }

        .table td,
        .table th {
            padding: 5px;
        }

        table.dataTable>thead .sorting:before,
        table.dataTable>thead .sorting:after,
        table.dataTable>thead .sorting_asc:before,
        table.dataTable>thead .sorting_asc:after,
        table.dataTable>thead .sorting_desc:before,
        table.dataTable>thead .sorting_desc:after,
        table.dataTable>thead .sorting_asc_disabled:before,
        table.dataTable>thead .sorting_asc_disabled:after,
        table.dataTable>thead .sorting_desc_disabled:before,
        table.dataTable>thead .sorting_desc_disabled:after {
            font-size: 7px !important;
        }

        table.dataTable>thead>tr>th:not(.sorting_disabled),
        table.dataTable>thead>tr>td:not(.sorting_disabled) {
            padding-right: 20px;
            width: 20% !important;
        }

        .dataTables_wrapper .dataTable thead .sorting:before,
        .dataTables_wrapper .dataTable thead .sorting_asc:before,
        .dataTables_wrapper .dataTable thead .sorting_desc:before,
        .dataTables_wrapper .dataTable thead .sorting_asc_disabled:before,
        .dataTables_wrapper .dataTable thead .sorting_desc_disabled:before {
            bottom: -2px;
        }

        .dataTables_wrapper .dataTable thead .sorting:after,
        .dataTables_wrapper .dataTable thead .sorting_asc:after,
        .dataTables_wrapper .dataTable thead .sorting_desc:after,
        .dataTables_wrapper .dataTable thead .sorting_asc_disabled:after,
        .dataTables_wrapper .dataTable thead .sorting_desc_disabled:after {
            top: -1px;
        }

        .shift-type table.dataTable td,
        .shift-type table.dataTable th {
            padding: 7px 22px;
        }

    </style>
@endsection 
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Performance Dashboard</li>
                </ol>
            </nav>  
        </div>
    </div>
    <form action="" id="date-form" method="get">
        <div class="col-md-3">
            <div class="form-group">
                <input id="dateFrom" name="attendanceDateFrom" type="hidden">
                <input id="dateTo" name="attendanceDateTo" type="hidden">
                <button type="button" id="date-btn" class="btn btn-sm btn-block btn-primary" style="width: 197px">
                    @if (!empty(request()->get('attendanceDateFrom')) && !empty(request()->get('attendanceDateTo')))
                    <span>
                        {{ Carbon\Carbon::parse(request()->get('attendanceDateFrom'))->format('d/m/Y') }} -
                        {{ Carbon\Carbon::parse(request()->get('attendanceDateTo'))->format('d/m/Y') }}
                    </span>
                @else
                    <span>
                       <i class="fa fa-calendar" value=""></i> &nbsp;Select Date&nbsp;
                    </span>
                @endif
                <i class="fa fa-caret-down"></i>
                </button>
            </div>
        </div>
    </form>
    <div class="row mb-4">
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Early Comers
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Total Duration</th>
                                <th>Department</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topEarlyComers as $topEarlyComer)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$topEarlyComer->early_minutes))) @endphp
                                <tr>
                                    <td><a target="_blank"
                                            href="{{ route('employeeDetail', ['employee' => $topEarlyComer->employee->id]) }}">{{ $topEarlyComer->name }}</a>
                                    </td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
                                    <td>{{ $topEarlyComer->employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $topEarlyComer->shiftType->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Late Comers
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Total Duration</th>
                                <th>Department</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topLateComers as $topLateComer)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$topLateComer->late_minutes))) @endphp
                                <tr>
                                    <td><a target="_blank"
                                            href="{{ route('employeeDetail', ['employee' => $topLateComer->employee->id]) }}">{{ $topLateComer->name }}</a>
                                    </td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
                                    <td>{{ $topLateComer->employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $topEarlyComer->shiftType->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row  mb-4">
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Late Going
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Total Duration</th>
                                <th>Department</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topLateGoings as $topLateGoing)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$topLateGoing->late_going_minutes))) @endphp
                                <tr>
                                    <td><a target="_blank"
                                            href="{{ route('employeeDetail', ['employee' => $topLateGoing->employee->id]) }}">{{ $topLateGoing->name }}</a>
                                    </td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
                                    <td>{{ $topLateGoing->employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $topEarlyComer->shiftType->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Early Going
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Total Duration</th>
                                <th>Department</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topEarlyGoings as $topEarlyGoing)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$topEarlyGoing->early_going_minutes))) @endphp
                                <tr>
                                    <td><a target="_blank"
                                            href="{{ route('employeeDetail', ['employee' => $topEarlyGoing->employee->id]) }}">{{ $topEarlyGoing->name }}</a>
                                    </td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
                                    <td>{{ $topEarlyGoing->employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $topEarlyComer->shiftType->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Late Department Comers
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topLateDepartmentComers as $department=>$time)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$time))) @endphp
                                <tr>
                                    <td>{{ $department }}</td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Late Department Goings
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topLateDepartmentGoings as $department=>$topLateDepartmentGoings)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$topLateDepartmentGoings))) @endphp
                                <tr>
                                    <td>{{ $department }}</td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Early Department Comers
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topEarlyDepartmentComers as $department=>$topEarlyDepartmentComer)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$topEarlyDepartmentComer))) @endphp
                                <tr>
                                    <td>{{ $department }}</td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xxl-4">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Top Early Department Goings
                    </div>
                    <table class="table table-striped listtable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topEarlyDepartmentGoings as $department=>$topEarlyDepartmentGoing)
                                @php $duration  =   explode(':',date('H:i', mktime(0,$topEarlyDepartmentGoing))) @endphp
                                <tr>
                                    <td>{{ $department }}</td>
                                    <td>{{ $duration[0] == 0 ? '0 hour' : "$duration[0] " . Str::plural('hour', $duration[0]) }}
                                        {{ $duration[1] == 0 ? '0 minute' : "$duration[1] " . Str::plural('minute', $duration[0]) }}
                                    </td>
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
        $('.listtable').DataTable({
            searching: false,
            paging: false,
            info: false,
            order: [
                [1, "desc"]
            ]
        });
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
                startDate: moment().subtract(7, 'days'),
                endDate: moment()
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
    </script>
@endsection
