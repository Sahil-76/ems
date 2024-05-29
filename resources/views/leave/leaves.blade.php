@extends('layouts.master')
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
                            <p class="card-title float-left">Leave List</p>
                            <div>
                                <div class="float-right">
                                    {{ Form::open(['method' => 'GET', 'id' => 'date-form']) }}
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
                                    {{ Form::close() }}
                                </div>
                            </div>
                            <br><br><br>
                            <div class="table-responsive">
                                <table id="example1" class="table"  style="width:100%"> 

                                    <thead>
                                        <tr>
                                            <th>Leave Session</th>
                                            <th>Leave Type</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Duration</th>
                                            {{-- <th>Sundays</th> --}}
                                            <th>Leave Reason</th>
                                            <th>Attachment</th>
                                            <th>Remarks</th>
                                            <th>Approval Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaves as $leave)
                                            <tr>
                                                <td>{{ $leave->leave_session }}</td>
                                                {{-- <td>{{$leave->employee->name}}</td> --}}
                                                <td>{{ $leave->leaveType->name ?? '' }}</td>
                                                <td>{{ getFormatedDate($leave->from_date) }}</td>
                                                <td>{{ getFormatedDate($leave->to_date) }}</td>
                                                <td>{{ $leave->duration }} {{ Str::plural('Day', $leave->duration) }}</td>
                                                {{-- <td>{{$leave->sundays}}</td> --}}
                                                <td><textarea name="" id="" cols="30" rows="3"
                                                        disabled>{{ $leave->reason }}</textarea></td>
                                                <td>
                                                    @if ($leave->attachment)
                                                        <a target="_blank"
                                                            href="{{ route('viewFile', ['file' => $leave->attachment]) }}">
                                                            <i class="fa fa-eye text-primary"></i>
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!is_null($leave->is_approved))
                                                        @if (!$leave->remarks)
                                                            OK
                                                        @else
                                                            {{ $leave->remarks }}
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ ucfirst($leave->status) }}</td>
                                                @if ($leave->status == 'Approved' || $leave->status == 'Pre Approved')
                                                    <td><button class="btn btn-danger btn-sm p-2"
                                                            @if ($leave->leaveCancellation()) disabled @else onclick="cancelLeave({{ $leave->id }})" @endif>Cancel</button></td>
                                                @elseif($leave->status == 'Pending')
                                                    <td><button class="btn btn-danger btn-sm p-2"
                                                            onclick="cancelLeave({{ $leave->id }})">Cancel</button></td>
                                                @else
                                                    <td>{{ $leave->status }}</td>
                                                @endif
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

        $('#example1').dataTable({
            ordering: false,
            "scrollX": true,

        });

        function cancelLeave(leaveId) {
            var status = confirm("Are you sure ?");
            if (!status) {
                return false;
            }
            $(event.target).attr('disabled', true);
            var route = "{{ route('leaveCancel') }}";
            $.ajax({
                url: route,
                type: 'get',
                data: {
                    leave_id: leaveId
                },
                success: function(response) {
                    toastr.success('Leave Cancelled');
                    location.reload();
                }
            });
        }
    </script>

@endsection
