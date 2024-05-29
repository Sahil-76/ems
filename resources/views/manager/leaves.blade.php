@extends('layouts.master')
@section('headerLinks')
    <style>
        .error {
            border: 1px solid red;
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
                            <p class="card-title float-left">Leave Request List</p>
                            <div>
                                <div class="float-right">
                                    {{Form::open(['method'=>'GET'])}}
                                        <button type="button" class="btn btn-sm btn-light bg-white" name="daterange"
                                            id="date-btn" value="Select Date">
                                            @if (request()->has('dateFrom') && request()->has('dateTo'))
                                                <span>
                                                    {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y') }}
                                                    - {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y') }}
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
                                        {{Form::close()}}
                                </div>
                            </div>
                            <br><br><br>
                            <div class="table-responsive">
                                <table id="example1" class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Leave Session</th>
                                            <th>Leave Type</th>
                                            <th>Employee</th>
                                            <th>Applied At</th>
                                            {{-- @if (count($departmentIds) > 1) --}}
                                                <th>Department</th>
                                            {{-- @endif --}}
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Reason</th>
                                            <th>Duration</th>
                                            {{-- <th>Timings</th> --}}
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($leaves->isEmpty())
                                        <tr>
                                            <td colspan="8">
                                                <marquee behavior="alternate" direction="right"> No data available</marquee>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach ($leaves as $leave)
                                            @php
                                            $class  =   'table-danger';
                                            if($leave->status=='Pre Approved')
                                            {
                                                $class = 'table-success';
                                            }
                                            elseif($leave->status=='Pending' || $leave->status=='Forwarded' || $leave->status=='Auto Forwarded')
                                            {
                                                $class='';
                                            }
                                            @endphp
                                            <tr class="border-top {{$class}}">
                                                <td>{{ $leave->leave_session }}</td>
                                                <td>{{ $leave->leaveType->name ?? '' }}</td>
                                                <td>{{ $leave->user->name }}</td>
                                                <td>{{getFormatedDate($leave->created_at)}}</td>
                                                {{-- @if (count($departmentIds) > 1) --}}
                                                    <td>{{ $leave->user->employee->department->name ?? '' }}</td>
                                                {{-- @endif --}}
                                                <td>{{ getFormatedDate($leave->from_date) }}</td>
                                                <td>{{ getFormatedDate($leave->to_date) }}</td>
                                             
                                                <td><textarea  cols="15" rows="3" disabled>{{ $leave->reason }}</textarea></td>
                                          
                                                <td>{{ $leave->duration }} {{ Str::plural('Day', $leave->duration) }}</td>
                                         
                                                <td>{{ $leave->status }}</td>

                                            </tr>
                                            <tr>
                                                @if (is_null($leave->is_approved) && $leave->forwarded != 1)
                                                    <td colspan='7'>
                                                        {{ Form::open(['class' => 'd-flex']) }}
                                                        {{ Form::hidden('id', $leave->id) }}
                                                    
                                                        @if ($leave->attachment)
                                                            <div class="col-2">
                                                                <label for="" class="font-weight-bold">Attachment</label>
                                                                <br>
                                                                <a target="_blank"
                                                                    href="{{ route('viewFile', ['file' => $leave->attachment]) }}">
                                                                    <i class="fa fa-eye text-primary"></i>
                                                                </a>
                                                            </div>
                                                        @endif

                                                        <div class="col-4 remarks">
                                                            {{ Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) }}
                                                            {{ Form::textarea('remarks', $leave->remarks ?? '', ['rows' => '1', 'cols' => '20', 'class' => 'form-control remarks-field']) }}
                                                        </div>



                                                        <div class="col-3 action">
                                                            <br>
                                                            @if ($leave->forwardedLeave() && $leave->forwarded != 1)
                                                                <button type="submit" value="Approved"
                                                                    class="btn btn-primary btn-rounded m-3 leave-action">Approve</button>

                                                                <button type="submit" value="reject"
                                                                    class="btn btn-danger btn-rounded m-3 leave-action">Reject</button>
                                                            @endif
                                                            @if($leave->forwarded != 1)
                                                            <button type="submit" value="forward"
                                                                class="btn btn-primary btn-rounded m-3 leave-action" >Forward
                                                                To HR</button>
                                                            @endif
                                                        </div>

                                                        {{ Form::close() }}
                                                    </td>
                                               
                                                @endif
                                            </tr>
                                        @endforeach
                                        @endif
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
        $('body').addClass('sidebar-icon-only');
        $('#date-btn').daterangepicker({
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today'       : [moment(), moment()],
                    'Tomorrow'    : [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Next 5 Days' : [moment(), moment().add(4, 'days')],
                    'Next 14 Days': [moment(), moment().add(13, 'days')],
                    'Next 30 Days': [moment(), moment().add(29, 'days')],
                    'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                    'Next Month'  : [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
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
            clearDateFilters('date-btn','date');
        });

        function clearDateFilters(id, inputId){
            $('#'+id+' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#'+inputId+'From').val('');
            $('#'+inputId+'To').val('');
        }

        // $('.leave-alter').on('click', function() {

        //     var action = $(this).val();
        //     var button = this;
        //     var buttonValue = $(button).html();

        //     $('form').one('submit', function() {

        //         event.preventDefault();
        //         $(button).html('Please wait').attr('disabled', true).append(
        //             '<i class="mdi mdi-rotate-right mdi-spin ml-1" aria-hidden="true"></i>');
        //         var form = this;
        //         var formData = $(this).serialize() + "&action=" + action;
        //         $(this).find('.action button').attr('disabled', true);
        //         var link = "{{ route('leaveAlter') }}";
        //         $.ajax({
        //             url: link,
        //             type: 'post',
        //             headers: {
        //                 'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
        //             },
        //             data: formData,
        //             success: function(response) {
        //                 toastr.info(response);
        //                 location.reload();
        //             },
        //             error: function(response) {
        //                 $(button).html(buttonValue);
        //                 $(button).attr('disabled', false).find('i').remove();
        //                 var errors = response.responseJSON.errors;
        //                 $.each(errors, function(key, val) {
        //                     $(form).find('input[name="' + key + '"]').addClass('error')
        //                         .siblings('span').html(val[0]);

        //                 });
        //             }
        //         });
        //     });
        // });
        $('.leave-action').on('click', function() {
            var action = $(this).val();
            if (action == 'forward' || action == 'reject') {
                var remarks = $(this).closest('.action').siblings('.remarks').find('.remarks-field').val();
                if (remarks == '') {
                    alert('Remarks Required');
                    return false;
                }
            }
            $(this).html('Please wait').append(
                '<i class="mdi mdi-rotate-right mdi-spin ml-1" aria-hidden="true"></i>');
            $('form').on('submit', function() {
                event.preventDefault();
                var formData = $(this).serialize() + "&action=" + action;
                $(this).find('.action button').attr('disabled', true);
                var link = "{{ route('leaveAction') }}";
                $.ajax({
                    url: link,
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                    },
                    data: formData,
                    success: function(response) {
                        toastr.info('Leave '+action);
                        location.reload();
                    },
                    error: function(error) {
                        alert('something went wrong');
                    }
                });
            });
        });
    </script>

@endsection
