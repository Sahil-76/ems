@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item ">Manual Attendance Form</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="col-12 mb-3">

        <div class="row">
            <div class="col-12">
                <div class="card">

                    {{ Form::model($attendance, ['route' => $submitRoute, 'method' => 'Post']) }}
                    <div class="card-body">
                        <p class="card-title">Manual Attendance Form</p>

                        <div class="form-group row">
                            @can('hrEmployeeList', new App\Models\Employee())
                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('department_id', $departments, null, ['onchange' => 'getEmployees(this.value)', 'class' => 'form-control selectJS', 'placeholder' => 'Select your department']) }}
                                </div>
                            @endcan

                            {!! Form::label('punch_date', 'Punch Date', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm-4">
                                {{ Form::date('punch_date', null, ['onchange' => 'getAttendance(this.value)', 'id' => 'date', 'class' => 'form-control', 'placeholder' => 'Punch Date']) }}
                            </div>

                            {{ Form::label('user_id', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                            <div class="col-sm-4">
                                <select style='width:100%;' name='user_id' class="form-control selectJS"
                                    data-placeholder="Select an option" placeholder="select an option" id="employees"
                                    onchange=emptyDate()>
                                    <option value="" disabled selected>Select your option</option>
                                    @foreach ($employeeDepartments as $department => $employees)
                                        <optgroup label="{{ $department }}">
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->user_id }}">
                                                    {{ $employee->name . ' (' . $employee->biometric_id . ')' }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            {!! Form::label('punch_in', 'Punch In ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm-4">
                                {{ Form::time('punch_in', null, ['id' => 'punchIn', 'class' => 'form-control', 'placeholder' => 'Punch In']) }}
                            </div>
                            
                            {{ Form::label('shift_type_id', 'Select Shift', ['class' => 'col-sm-2 col-form-label']) }}
                            <div class="col-md-4">
                                <select name="shift_type_id" required class="form-control selectJS">
                                    @foreach ($shifts as $shiftType)
                                        <option value="{{ $shiftType->id }}">
                                            {{ $shiftType->name . ' (' . $shiftType->start_time . ' - ' . $shiftType->end_time . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- {{ Form::select('shift_type_id', $shifts, $employee->user->shift_type_id ?? '', ['class' => 'form-control selectJS','placeholder' =>'Select Shift Type' , 'data-placeholder'=>'Select Shift Type']) }} --}}
                            </div>

                            {!! Form::label('punch_out', 'Punch Out ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm-4">
                                {{ Form::time('punch_out', null, ['id' => 'punchOut', 'class' => 'form-control', 'placeholder' => 'Punch Out']) }}
                            </div>
                            
                            {{ Form::label('status', 'Select Status', ['class' => 'col-sm-2 col-form-label']) }}
                            <div class="col-sm-4">
                                {{ Form::select('status', $status, null, ['class' => 'form-control selectJS', 'placeholder' => 'Select Status']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::submit('Submit', ['class' => 'btn m-2 btn-primary']) }}
                                {{ Form::close() }}
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

        function getAttendance(date) {
            if (date) {
                var id = $('#employees').val();
                $.ajax({
                    url: "{{ route('getAttendance') }}/",
                    type: 'get',
                    dataType: 'json',
                    data: {
                        'user_id': id,
                        'date': date
                    },
                    success: function(response) {
                        if (response != null) {
                            $('#punchIn').val(response.punch_in);
                            $('#punchOut').val(response.punch_out);
                        }
                    }
                })
            }

        }

        function emptyDate() {
            $('#date').val('');
            $('#punchIn').val('');
            $('#punchOut').val('');
        }
    </script>
@endsection
