@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Shift Type Form</div>
                    {{Form::model($shift, ['route' => $submitRoute, 'method' => $method])}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('name', 'Name', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-sm-9">
                                                {{ Form::text('name', null , ['class'=>'form-control','placeholder'=>'Name','required'=>'true']) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('start_time', 'Start Time', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-sm-9">
                                                {!! Form::time('start_time', null, ['class'=>'form-control','placeholder'=>'Start Time','required'=>'true']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('mid_time', 'Mid Time', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-sm-9">
                                                {{ Form::time('mid_time', null , ['class'=>'form-control','placeholder'=>'Mid Time','required'=>'true']) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            {!! Form::label('end_time', 'End Time', ["class" => "col-sm-3 col-form-label"]) !!}
                                            <div class="col-sm-9">
                                                {{ Form::time('end_time', null , ['class'=>'form-control','placeholder'=>'End Time','required'=>'true']) }}
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