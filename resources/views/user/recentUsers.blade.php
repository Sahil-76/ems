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
                    <p class="card-title float-left">Employee List</p>
                    <div>
                        <div class="float-right">
                            {{ Form::open(['method' => 'GET', 'id' => 'form']) }}
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
                            {{ Form::hidden('dateFrom', request()->dateFrom ?? '', ['id' => 'dateFrom']) }}
                            {{ Form::hidden('dateTo', request()->dateTo ?? '', ['id' => 'dateTo']) }}
                            {{ Form::close() }}
                        </div>
                    </div>
                    <div class="table table-responsive mt-5">
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
                                        <td>{{ $employee->department->name ?? '' }}</td>
                                        <td>{{ $employee->office_email ?? '' }}</td>
                                        <td><a href="{{ route('employeeDetail', ['employee' => $employee->id]) }}"
                                                class="btn btn-warning btn-lg p-3">Details</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('footerScripts')
    <script>
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
                $('#form').closest('form').submit();
            }
        );

        $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('date-btn', 'date');
            $('#form').closest('form').submit();
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }
    </script>

@endsection
