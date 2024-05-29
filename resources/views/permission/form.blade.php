@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item "><a href="{{ route('permission.index') }}">Permission</a></li>
                    {{-- <li class="breadcrumb-item active">{{ $user->name }}</li> --}}
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Permission Create</h4>
                    {!! Form::model($permissions, ['route' => $submitRoute, 'method' => $method]) !!} 
                    <div class="row">
                        <div class="col-md-4">
                          <div class="form-group row">
                            {{ Form::label('module_id', 'Select Module:', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                              {{ Form::select('module_id', $modules, $permissions->modules->name ?? '', array_merge(['id' => 'module', 'class' => 'form-control select2', 'tabindex' => '-1', 'required' => 'required'])) }}
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group row">
                            {{ Form::label('access', 'Access', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                              {{ Form::text('access', null, ['class' => 'form-control', 'required' ,'placeholder' => 'Enter Access']) }}
                              @error('access')
                                <span class="text-danger">{{ $message }}</span>
                              @enderror
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group row">
                            {{ Form::label('description', 'Description', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                              {{ Form::text('description', null, ['class' => 'form-control', 'required' ,'placeholder' => 'Enter Description']) }}
                              @error('description')
                                <span class="text-danger">{{ $message }}</span>
                              @enderror
                            </div>
                          </div>
                        </div>
                      </div>                   
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
@endsection
