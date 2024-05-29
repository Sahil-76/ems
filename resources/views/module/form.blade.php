@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item "><a href="{{ route('modules.index') }}">Modules</a></li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ isset($module) ? 'Module Edit' : 'Module Create' }}</h4>
                    {!! Form::model($module ?? null, ['route' => $data['submitRoute'], 'method' => $data['method']]) !!}
                        
                        <div class="col-md-4">
                          <div class="form-group row">
                            {{ Form::label('name', 'Module Name', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                              {{ Form::text('name', null, ['class' => 'form-control', 'required' ,'placeholder' => 'Enter Module']) }}
                              @error('name')
                                <span class="text-danger">{{ $message }}</span>
                              @enderror
                            </div>
                          </div>                        
                      </div>
                    
                    <button type="submit" class="btn btn-primary mr-2">{{ isset($module) ? 'Update' : 'Submit' }}</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
@endsection
