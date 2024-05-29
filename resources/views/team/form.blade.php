@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Team Form</div>
                    {{Form::model($team, ['route' => $submitRoute, 'method' => $method])}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">

                                    @can('hrEmployeeList', new App\Models\Employee())
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                {!! Form::label('department_id', 'Select Department', ["class" => "col-sm-3 col-form-label"]) !!}
                                                <div class="col-7">
                                                    {!! Form::select('department_id', $departments ,null, ['class'=>'form-control selectJS','onchange'=>'getEmployees(this.value)','placeholder'=>'Select Department']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endcan

                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('name', 'Team Name', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-7">
                                                {{ Form::text('name', null , ['class'=>'form-control ','placeholder'=>'Enter Team Name','required'=>'true', "style"=>"width: 343px;"]) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('reporting_person', 'Reporting Person', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-7">
                                                {{ Form::text('reporting_person', null , ['class'=>'form-control ','placeholder'=>'Enter Reporting Person', "style"=>"width: 343px;"]) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('users', 'Select Employee', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-7">
                                                {!! Form::select('users[]', $users ,$selectedUsers ?? null, ['class'=>'form-control tail-select', 'id'=>'employees', 'placeholder'=>'Select Employee', 'multiple'=>'multiple']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
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
                url: "{{route('getUsers')}}/" + department_id,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    var options = `<option value=''></option>`;
                    $.each(response, function (id, data) {
                        options += "<option value='" + data.user_id + "'>" + data.name+'('+data.office_email+')' + "</option>";

                    });

                    $('#employees').html(options);
                    tail.select('#employees').reload();
                }
            })
        }
    }
</script>
@endsection
