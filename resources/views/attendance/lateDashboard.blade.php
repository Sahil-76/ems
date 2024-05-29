@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Late Attendance</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 mb-3">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {{ Form::open(['method' => 'GET']) }}
                            <div class="card-body">
                                <p class="card-title">Filter</p>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <button type="button" class="btn btn-sm btn-light bg-white" name="daterange"
                                            id="date-btn" value="Select Date">
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
                                    </div>
                                    {{ Form::hidden('dateFrom', request()->dateFrom ?? null, ['id' => 'dateFrom']) }}
                                    {{ Form::hidden('dateTo', request()->dateTo ?? null, ['id' => 'dateTo']) }}
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                        <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                                    </div>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div class="row"></div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body ">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="col-md-8 float-right text-right">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 table-responsive ">
                            <table id="example1" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Manager</th>
                                        @if (request()->dateFrom == request()->dateTo)
                                            <th>Date</th>
                                            <th>Shift Type</th>
                                            <th>Punch In</th>
                                        @endif
                                        @if (request()->dateFrom != request()->dateTo)
                                            <th>No. of Days Late</th>
                                        @endif
                                        <th>Total Minutes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userAttendances as $name => $userAttendance)
                                        <tr>
                                            <td>{{ $userAttendance[0]->user->employee->name ?? '' }}</td>
                                            <td>{{ $userAttendance[0]->user->employee->department->name ?? '' }}</td>
                                            <td>{{ $userAttendance[0]->user->employee->department->manager ?? '' }}</td>
                                            @if (request()->dateFrom == request()->dateTo)
                                                <td>{{ getFormatedDate($userAttendance[0]->punch_date) }}</td>
                                                <td>{{ $userAttendance[0]->user->shiftType->name . ' (' . $userAttendance[0]->user->shiftType->start_time . ')' }}
                                                </td>
                                                <td>{{ !empty($userAttendance[0]->punch_in) ? Carbon\Carbon::createFromFormat('H:i:s', $userAttendance[0]->punch_in)->format('g:i:s A') : '--:--' }}
                                                </td>
                                            @endif
                                            @if (request()->dateFrom != request()->dateTo)
                                                <td>{{ $userAttendance->count() }}</td>
                                            @endif
                                            <td>{{ abs($userAttendance->sum('in')) }}</td>
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
@endsection

@section('footerScripts')
    <script>
        $('#example1').dataTable();
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
                }
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
    </script>
@endsection
