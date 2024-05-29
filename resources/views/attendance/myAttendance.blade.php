@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Attendance</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body ">
                    <div class="card-title">
                        <h4 class="float-left"> Attendance </h4>
                        <div class="float-right">
                            {{ Form::open(['method' => 'GET', 'id' => 'date-form']) }}
                                <button type="button" class="btn btn-sm btn-primary" id="date-btn" value="Select Date">
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
                            {{ Form::close() }}
                        </div>
                    </div>
                    <div class="table-responsive ">
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Punch In</th>
                                    <th>Punch Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($myAttendances as $myAttendance)
                                    <tr @if ($myAttendance->in < 0) class="bg-danger" @endif>
                                        <td>{{ getFormatedDate($myAttendance->punch_date) }}</td>
                                        <td>{{ !empty($myAttendance->punch_in) ? Carbon\Carbon::createFromFormat('H:i:s', $myAttendance->punch_in)->format('g:i:s A') : '--:--' }}
                                        </td>
                                        <td>{{ !empty($myAttendance->punch_out) ? Carbon\Carbon::createFromFormat('H:i:s', $myAttendance->punch_out)->format('g:i:s A') : '--:--' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $fromDate = request()->dateFrom ?? '';
        $toDate = request()->dateTo ?? '';
    @endphp
@endsection
@section('footerScripts')
    <script>
        if ('{{ $fromDate }}' != '') {
            var startDate = moment('{{ $fromDate }}');
            var endDate = moment('{{ $toDate }}');
        } else {
            var startDate = moment().add(1, 'days');
            var endDate = moment().add(1, 'days');
        }
        $('#date-btn').daterangepicker({
                showOn: "button",
                showButtonPanel: true,
                buttonImageOnly: true,
                buttonText: "",
                showButtonPanel: true,
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Select Date': [moment().add(1, 'days'), moment().add(1, 'days')],
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
                startDate: startDate,
                endDate: endDate,
            },
            function(start, end) {
                if (start.format('YYYY-M-DD') == moment().add(1, 'days').format('YYYY-M-DD') && end.format(
                    'YYYY-M-DD') == moment().add(1, 'days').format('YYYY-M-DD')) {
                    clearDateFilters('date-btn', 'date');
                    $('#date-form').closest('form').submit();
                }
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
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i> &nbsp;Filter Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }

        $('#example1').dataTable({
            order: [
                [0, 'desc']
            ],
            searching: false,
            columnsDefs: [{
                "name": "Date",
                sorting: false
            }, {
                "name": "Punch In",
                sorting: false
            }, {
                "name": "Punch Out",
                sorting: false
            }, ],
        });
    </script>
@endsection
