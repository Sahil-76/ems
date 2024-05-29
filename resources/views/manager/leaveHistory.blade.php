@extends('layouts.master')
@section('headerLinks')
    <style>
        .table td {
            padding: 5px;
        }

        .table th {
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
                    <li class="breadcrumb-item active" aria-current="page">Leave List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title float-left">Leave History List</p>
                            <div>
                                <div class="float-right">
                                    {{ Form::open(['method' => 'GET']) }}
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
                                    {{ Form::hidden('dateFrom', request()->dateFrom ?? null, ['id' => 'dateFrom']) }}
                                    {{ Form::hidden('dateTo', request()->dateTo ?? null, ['id' => 'dateTo']) }}

                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    {{ Form::close() }}
                                </div>
                            </div>
                            <br><br><br>
                            <div class="">
                                <table id="example1" class="table">

                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Session</th>
                                            <th>Name</th>
                                            @if ($departmentCount)
                                                <th>Department</th>
                                            @endif
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Duration</th>
                                            <th>Timing</th>
                                            <th>Reason</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Attachment</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
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
        @if ($departmentCount)

            filterColumns = [0, 1, 3];
        @endif
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
                // startDate: moment().subtract(29, 'days'),
                //endDate  : moment()
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


        $(window).on('load', function() {

            $('#example1').dataTable({
                    @if($departmentCount)
                        order:  ['4','desc'],   
                    @else
                        order:  ['3','desc'],
                    @endif
                serverSide: true,
                processing: true,
                ajax: "{!! route('managerLeaveHistory', $query) !!}",
                columns: [
                    {
                        data: "leaveType",
                    },
                    {
                        data: "leave_session",
                    },
                    {
                        data: 'user_name',
                    },
                    @if ($departmentCount)
                        {
                            data: "department_name"
                        },
                    @endif {
                        data: "from_date",
                    },
                    {
                        data: "to_date",
                    },
                    {
                        data: "duration",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "timing",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "reason",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "remarks",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "status",
                    },
                    {
                        data: "attachment",
                        orderable: false,
                        searchable: false
                    },

                ],
                "columnDefs": [{
                    @if($departmentCount)
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,11],
                    @else
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                    @endif
                    "createdCell": function(td, cellData, rowData, row, col) {

                        $(td).css('white-space', 'normal');
                        $(td).css('font-size', 'small');
                    },
                }]
            });
        });
    </script>
@endsection
