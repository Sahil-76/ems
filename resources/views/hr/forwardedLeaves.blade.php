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
        <div class="col-12 mb-3">

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        {{ Form::open(['method' => 'GET']) }}
                        <div class="card-body">
                            <p class="card-title">Filter</p>
                            <div class="form-group row">

                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('department_id', $department_id, request()->department_id, ['onchange' => 'getEmployees(this.value)', 'class' => 'form-control selectJS', 'placeholder' => 'Select your department']) }}
                                </div>

                                {{ Form::label('leave_session', 'Select Leave Session', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('leave_session', $leaveSessions, request()->leave_session, ['class' => 'form-control selectJS', 'placeholder' => 'Select Leave Session']) }}
                                </div>
                                {{ Form::label('leave_type_id', 'Select Leave Type', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('leave_type_id', $leaveTypes, request()->leave_type_id, ['class' => 'form-control selectJS', 'placeholder' => 'Select Leave Type']) }}
                                </div>
                                {{ Form::label('user_id', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{-- {{ Form::select('user_id', $users, request()->user_id, ['class' => 'form-control selectJS', 'placeholder' => 'Select Employee']) }} --}}
                                    <select name="user_id" style='width:100%;' data-placeholder="select an option"
                                        placeholder="select an option" class='selectJS form-control' id="employees">
                                        <option value="" disabled selected>Select an option</option>
                                        @foreach ($employeeDepartments as $department => $employees)
                                            <optgroup label="{{ $department }}">
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        @if (request()->user_id == $employee->id) selected @endif>
                                                        {{ $employee->name . ' (' . $employee->employee->biometric_id . ')' }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-8">
                                    <div class="float-left">
                                        {{ Form::open(['method' => 'GET']) }}
                                        <button type="button" class="btn btn-sm btn-light bg-white" name="daterange"
                                            id="date-btn" value="Select Date">
                                            @if (request()->has('dateFrom') && request()->has('dateTo'))
                                                <span>
                                                    {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y') }}
                                                    -
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
                                <div class="col-sm-2">
                                    <div class="form-check form-check-primary">
                                        <label class="form-check-label">Is Pending
                                            <input type="checkbox" @if (!empty(request()->is_pending)) checked @endif
                                                name="is_pending" class="form-check-input">
                                            <i class="input-helper"></i><i class="input-helper"></i></label>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row"> --}}
                            <div class="col-md-12">
                                <a href="{{ request()->url() }}" class="btn m-2  float-right btn-success">Clear
                                    Filter</a>
                                {{ Form::submit('Filter', ['class' => 'btn m-2 float-right btn-primary']) }}
                                {{ Form::close() }}
                                {{-- </div> --}}

                            </div>
                            {{ Form::close() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dot-opacity-loader" style="display:none;" id="loader">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="row">

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="card-title float-left">Forwarded Leave Requests <span id="total-count"></span></p>
                    <div style="margin-bottom:1.2rem;display:none;" id="bulk-leaves">
                        <button class="float-right btn btn-danger btn-sm btn-rounded bulk-leave-action ml-3"
                            value="Rejected">Reject</button>
                        <button class="float-right btn btn-primary btn-sm bulk-leave-action btn-rounded"
                            value="Approved">Approve</button>
                    </div>
                    <div class="table-responsive">
                        <table id="example1" class="table table-borderless" style="width: 100%">

                            <thead>
                                <tr>

                                    <th>
                                        @if ($leaves->where('is_approved', null)->isNotEmpty())
                                            <input type="checkbox" class="mr-3" id="all-leaves" name="leaves[]">
                                        @endif
                                        Leave Session
                                    </th>
                                    <th>Leave Type</th>
                                    <th>Department</th>
                                    <th>Employee</th>
                                    <th>Applied At</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Duration</th>
                                    {{-- <th>Timings</th> --}}
                                    <th>Forward Reason</th>
                                    <th>Forwarded By</th>
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
                                        <tr class="border-top">
                                            <td>
                                                @if ($leave->is_approved == null)
                                                    <input type="checkbox" class="mr-3"
                                                        data-leave-id="{{ $leave->id }}" onclick="leaveAction()"
                                                        name="leaves[]">
                                                @endif
                                                {{ $leave->leave_session ?? '' }}
                                            </td>
                                            <td>{{ $leave->leaveType->name ?? '' }}</td>
                                            <td>{{ optional($leave->user->employee)->department->name ?? 'N/A' }}</td>
                                            <td>{{ optional($leave->user->employee)->name ?? 'N/A' }}</td>
                                            <td>{{ getFormatedDate($leave->created_at) }}</td>
                                            <td>{{ getFormatedDate($leave->from_date) }}</td>
                                            <td>{{ getFormatedDate($leave->to_date) }}</td>
                                            <td>{{ $leave->duration }} {{ Str::plural('Day', $leave->duration) }}
                                            </td>
                                            {{-- <td>{{getFormatedTime($leave->timing)}}</td> --}}
                                            <td>{{ !empty($leave->remarks) ? $leave->remarks : 'N/A' }}</td>

                                            <td>{{ $leave->user->employee->department->deptManager->name ?? null }}
                                            </td>
                                            <td>{{ $leave->status }}</td>
                                        </tr>
                                        <tr>

                                            <td colspan='10'>
                                                {{ Form::open(['class' => 'd-flex']) }}
                                                {{ Form::hidden('id', $leave->id) }}
                                                <div class="col-3">
                                                    {{ Form::label('reason', 'Leave Reason', ['class' => 'font-weight-bold']) }}
                                                    {{ Form::textarea('reason', $leave->reason, ['rows' => '1', 'cols' => '20', 'class' => 'form-control', 'disabled' => true]) }}
                                                </div>
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
                                                <div class="col-3 remarks">
                                                    {{ Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) }}
                                                    {{ Form::textarea('remarks', null, ['rows' => '1', 'cols' => '20', 'class' => 'form-control remarks-field']) }}
                                                </div>


                                                @if (is_null($leave->is_approved))
                                                    <div class="col-2 action">
                                                        <br>
                                                        {{-- <button type="submit" value="Pre Approved" class="btn btn-primary btn-rounded m-3 leave-action">Pre Approved</button> --}}
                                                        <button type="submit" value="Approved"
                                                            class="btn btn-primary btn-rounded m-3 leave-action">Approve</button>
                                                        <button type="submit" value="reject"
                                                            class="btn btn-danger btn-rounded m-3 leave-action">Reject</button>

                                                    </div>
                                                @endif

                                                {{ Form::close() }}
                                            </td>

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
    <div class="col-sm-12">
        <div class="float-right">
            {{ $leaves->appends(request()->query())->links() }}
        </div>
    </div>
@endsection


@section('footerScripts')
    <script>
        var leaves = [];
        $('#date-btn').daterangepicker({
            opens: 'left',
            locale: {
                cancelLabel: 'Clear'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                'Next 5 Days': [moment(), moment().add(4, 'days')],
                'Next 14 Days': [moment(), moment().add(13, 'days')],
                'Next 30 Days': [moment(), moment().add(29, 'days')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
            },
            // startDate: moment().subtract(29, 'days'),
            //endDate  : moment()
        }, function(start, end) {
            $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
            $('#dateFrom').val(start.format('YYYY-M-DD'));
            $('#dateTo').val(end.format('YYYY-M-DD'));
        });
        $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('date-btn', 'date');
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }
        $('#all-leaves').click(function() {
            leaves = []
            $('#bulk-leaves').hide();
            $('table tbody tr td div.action').show();
            if ($(this).is(':checked')) {
                $.each($('#example1 tbody tr td').find('input:checkbox'), function(index, value) {
                    leaves.push($(value).data('leave-id'));
                });
                $('#bulk-leaves').show();
                $('table tbody tr td div.action').hide();
            }
            $('#example1 tbody tr td').find('input:checkbox').prop('checked', $(this).is(':checked'));
            if (leaves.length != 0) {
                $('#total-count').html(leaves.length);
            } else {
                $('#total-count').html('');
            }
        });

        function leaveAction() {
            if ($(event.target).is(':checked')) {
                $('table tbody tr td div.action').hide();
                $('#bulk-leaves').show();
                leaves.push($(event.target).data('leave-id'));
                if ($('#example1 tbody tr td').find('input:checkbox').length == leaves.length) {
                    $('#all-leaves').prop('checked', true);
                }
            } else {
                let index = leaves.indexOf($(event.target).data('leave-id'));
                if (index !== -1) {
                    leaves.splice(index, 1);
                }
                if (leaves.length == 0) {
                    $('#bulk-leaves').hide();
                }
                $('#all-leaves').prop('checked', false);
            }
            if (leaves.length != 0) {
                $('#total-count').html(leaves.length);
            } else {
                $('table tbody tr td div.action').show();
                $('#total-count').html('');
            }
        }

        $('.bulk-leave-action').on('click', function() {
            let action = $(this).val();
            $('body').css('filter', 'blur(1px)');
            $('#loader').show();
            $.ajax({
                url: "{{ route('bulkLeaveAction') }}",
                method: "post",
                data: {
                    action: action,
                    leaves: leaves
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('body').css('filter', '');
                    $('#loader').hide();
                    toastr.info('Leave ' + action);
                    window.location.href = "{{ route('forwardedLeaveList') }}";
                },
                error: function(errors) {
                    alert('something went wrong');
                }
            });
        });

        $('.leave-alter').on('click', function() {

            var action = $(this).val();
            var button = this;
            var buttonValue = $(button).html();

            $('form').one('submit', function() {

                event.preventDefault();
                $(button).html('Please wait').attr('disabled', true).append(
                    '<i class="mdi mdi-rotate-right mdi-spin ml-1" aria-hidden="true"></i>');
                var form = this;
                var formData = $(this).serialize() + "&action=" + action;
                $(this).find('.action button').attr('disabled', true);
                var link = "{{ route('leaveAlter') }}";
                $.ajax({
                    url: link,
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                    },
                    data: formData,
                    success: function(response) {
                        toastr.info(response);
                        location.reload();
                    },
                    error: function(response) {
                        $(button).html(buttonValue);
                        $(button).attr('disabled', false).find('i').remove();
                        var errors = response.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            $(form).find('input[name="' + key + '"]').addClass('error')
                                .siblings('span').html(val[0]);

                        });
                    }
                });
            });
        });
        $('.leave-action').on('click', function() {
            var action = $(this).val();
            if (action == 'reject') {
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
                        toastr.info('Leave ' + action);
                        location.reload();
                    },
                    error: function(error) {
                        alert('something went wrong');
                    }
                });
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
