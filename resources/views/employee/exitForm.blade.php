@extends('layouts.master')
@section('content')   

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exitList') }}">Exit Employees</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Form</li>
                </ol>
            </nav>
        </div>
    
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Employee Exit Form</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('department_id', $departments, null, ['onchange'=>'getEmployees(this.value)','class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('employee_id', 'Select Employee', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('employee_id',$employees, null, ['id' => 'employees','onchange'=>'getEmployeeDetail(this.value)','class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}
                                    @error('employee_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="employee-detail" class="gallery">

                    </div>
                </div>
            </div>
            <div class="card mt-4" id="exit-form" style="display: none">
                <div class="card-header bg-primary text-white">
                    Exit Details
                </div>
                {{Form::open(['route' => 'noDuesInitiate', 'method' => 'POST'])}}
                {{Form::hidden('employee_id', null, ['id' => 'employee_id'])}}
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('reason', 'Reason', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('reason', '', ['class' => 'form-control', 'placeholder' => 'Reason','required']) }}
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('exit_date', 'Exit Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('exit_date', '', ['class' => 'form-control', 'placeholder' => 'Reason','required']) }}

                                    @error('exit_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Initiate</button>
                        </div>
                    </div>
                </div>
                {{Form::close()}}
        
            </div>
        
        </div>
    </div>
    
@endsection

@section('footerScripts')
<script>

    $(document).ready(function() {
        $(document).on('submit', 'form', function() {
            $('button').attr('disabled', 'disabled');
        });
    });

    function getEmployees(department_id) {
        if (department_id) {
            $.ajax({
                url: "{{route('getEmployees')}}/" + department_id,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    var options = `<option value='' selected disabled hidden>Select Employee</option>`;
                    $.each(response, function (id, name) {
                        options += "<option value='" + id + " '>" + name + "</option>";
                    });
                 
                    $('#employees').html(options);
                    $("#employees").select2({
                        placeholder: "Select Employee"
                    });
                }
            })
        }
    }

    function getEmployeeDetail(employee_id){
        if (employee_id) {
            $.ajax({
                url: "{{route('getEmployeeDetail')}}/" + employee_id,
                type: 'get',
                dataType: 'html',
                success: function (response) {
                   $('#employee-detail').html(response);
                   $('#employee_id').val(employee_id);
                }
            })
        }
    }

    function showExitForm(){
        $('#exit-form').show();
    }
</script>

@endsection