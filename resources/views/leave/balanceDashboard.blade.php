@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Balance Dashboard</li>
                </ol>
            </nav>`
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
                                    {{ Form::select('department_id', $departments, request()->department_id, ['onchange' => 'getEmployees(this.value)','class' => 'form-control selectJS', 'placeholder' => 'Select your department']) }}
                                </div>

                                {{ Form::label('user_id', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                                {{-- <div class="col-sm-4">
                                    {{ Form::select('user_id', $users, request()->user_id, ['class' => 'form-control selectJS', 'placeholder' => 'Select Employee']) }}
                                </div> --}}
                                <div class="col-sm-4">
                                    <select style='width:100%;' name="user_id" data-placeholder="select an option"
                                        placeholder="select an option" class='form-control selectJS' id="employees">
                                        <option value="" disabled selected>Select an option</option>
                                        @foreach ($employeeDepartments as $department => $employees)
                                            <optgroup label="{{ $department }}">
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        @if ($employee->id == request()->user_id) selected @endif>
                                                        {{$employee->name.' ('.$employee->employee->biometric_id.')'}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary">
                                            <label class="form-check-label">Pending Queries
                                                <input type="checkbox" name="has_complaint" @if(request()->has_complaint) checked @endif class="form-check-input">
                                                <i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary">
                                            <label class="form-check-label">All Queries
                                                <input type="checkbox" name="previous_query" @if(request()->previous_query) checked @endif class="form-check-input">
                                                <i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary">
                                            <div class="row">
                                            <label class="form-check-label col-sm-4">Month</label>
                                            <input type="month" name="month" value="{{ request()->month }}" style="border-color: #CED4DA;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                    <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                                    {{ Form::close() }}
                                    @can('hrUpdateEmployee', auth()->user())
                                    <a href="{{ route('leaveBalanceExport', http_build_query(request()->query())) }}"
                                        class="btn m-2 btn-primary float-lg-right">Export</a>
                                    @endcan
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <!-- Default box -->

            <div class="card">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <b>Leave Balance</b>
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
                                        <th>Balance</th>
                                        {{-- <th>Prev. Month Bal</th> --}}
                                        <th>Absent</th>
                                        {{-- <th>Prev Month Deduction</th> --}}
                                        <th>{{ $date->addMonth()->format('F') . ' Deduction' }}</th>
                                        <th>Taken Leaves</th>
                                        <th>Deduction</th>
                                        <th>Pre Approval Deduction</th>
                                        <th>Final Deduction</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    @foreach ($leaveBalances as $leaveBalance)
                                        <tr>
                                            <td>{{ $leaveBalance->user->name." (".$leaveBalance->user->employee->biometric_id.") " ?? '' }}</td>
                                            <td>{{ $leaveBalance->user->employee->department->name ?? '' }}</td>
                                            <td>{{ $leaveBalance->balance }}</td>
                                            <td>{{ empty($leaveBalance->absent) ? 0 : $leaveBalance->absent / 2 }}</td>
                                            {{-- <td>{{ empty($leaveBalance->prev_month_deduction) ? 0 : $leaveBalance->prev_month_deduction }}</td> --}}
                                            <td>{{ empty($leaveBalance->next_month_deduction) ? 0 : $leaveBalance->next_month_deduction }}
                                            </td>
                                            <td><a href="{{ route('hrLeaveHistory', ['dateFrom' => $start, 'dateTo' => $end, 'user_id' => $leaveBalance->user_id]) }}"
                                                    target="_blank">{{ empty($leaveBalance->taken_leaves) ? 0 : $leaveBalance->taken_leaves }}</a>
                                            </td>
                                            <td>{{ ($leaveBalance->deduction ?? 0) + ($leaveBalance->pre_approval_deduction ?? 0) }}</td>
                                            <td>{{$leaveBalance->pre_approval_deduction ?? 0}}</td>
                                            <td>{{ $leaveBalance->final_deduction }}</td>
                                            <td><a href="{{ route('leaveBalanceEdit', $leaveBalance) }}"><i
                                                        class="fa fa-edit"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="row mt-4 float-right">
                        <div class="col-md-12">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

    </div>
@endsection


@section('footerScripts')
    <script>
        $('#example1').dataTable();

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
