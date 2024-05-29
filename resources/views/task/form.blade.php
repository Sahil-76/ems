@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Task Form</div>
                    {{Form::model($task, ['route' => $submitRoute, 'method' => $method])}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">

                                    @can('hrEmployeeList', new App\Models\Employee())
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                {!! Form::label('department_id', 'Select Department', ["class" => "col-sm-3 col-form-label"]) !!}
                                                <div class="col-7">
                                                    {!! Form::select('department_id', $departments ,null, ['class'=>'form-control selectJS','placeholder'=>'Select Department']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endcan

                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('name', 'Task Name', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-7">
                                                {{ Form::text('name', null , ['class'=>'form-control ','placeholder'=>'Enter Task Name','required'=>'true', "style"=>"width: 343px;"]) }}
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
        
</script>
@endsection
