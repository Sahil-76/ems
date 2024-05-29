@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item ">Electricity Form</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="col-12 mb-3">

        <div class="row">
            <div class="col-sm-6">
                <div class="card">

                    {{ Form::model($electricity, ['url' => $submitRoute, 'method' => $method]) }}
                    <div class="card-body">
                        <p class="card-title">Electricity Form</p>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    {{ Form::label('date', 'Date', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::date('date', null, ['class' => 'form-control' ,isset($electricity->id) ? 'disabled' : 'required' ]) }}
                                    </div>

                                    {!! Form::label('location', 'Location', ['class' => 'col-sm-3 col-form-label']) !!}

                                    <div class="col-sm-9">
                                        @if(!empty($electricity->id))
                                            {!! Form::text('location', $electricity->location ?? '', ['class' => 'form-control ', 'disabled']) !!}
                                        @else
                                            {!! Form::select('location', $locations, null, ['class' => 'form-control selectJS', 'required','placeholder'=>'Select Location']) !!} 
                                        @endif 
                                    </div>
                                    
                                    {{ Form::label('start_unit', 'Start Unit', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::number('start_unit', null, ['class' => 'form-control', 'required', 'placeholder' => 'Enter here...']) }}
                                    </div>

                                    {{ Form::label('end_unit', 'End Unit', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::number('end_unit', null, ['class' => 'form-control', 'required', 'placeholder' => 'Enter here...']) }}
                                    </div>

                                    {{ Form::label('total_units', 'Units Consumed', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::number('total_units', null, ['class' => 'form-control', 'disabled', 'placeholder' => 'Not Declared Yet']) }}
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ Form::submit('Submit', ['class' => 'btn m-2 btn-primary']) }}
                                {{ Form::close() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @if($electricity->activity->isNotEmpty())
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Updates</h4>
                            <ul class="bullet-line-list">
                                @foreach ($electricity->activity as $log)
                                        <li>
                                            <h6>{{ ucfirst($log->action) ?? '' }}</h6>
                                            <p><strong>Action By: </strong>{{ $log->user->name ?? '' }}</p>
                                            <p class="text-muted mb-4">
                                                <i class="ti-time"></i>
                                                {{ getFormatedDateTime($log->created_at) }}
                                            </p>
                                        </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
