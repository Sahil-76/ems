@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb ">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                {{ Form::model($user, ['route' => $submitRoute]) }}
                <div class="card-body">
                    <h4 class="card-title">Change Password</h4>
                    <div class="row">

                        <div class="col-md-8">
                            <div class="form-group row">
                                {{ Form::label('current_password', 'Current Password', ['class' => 'col-sm-3 col-form-label']) }}

                                <div class="col-sm-8">
                                    {{ Form::password('current_password', ['class' => 'form-control', 'required', 'minlength' => '6', 'maxlength' => '15']) }}
                                    @error('current_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group row">
                                {{ Form::label('password', 'New Password', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-8">
                                    {{ Form::password('password', ['class' => 'form-control', 'required', 'minlength' => '6', 'maxlength' => '15']) }}
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group row">
                                {{ Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-8">
                                    {{ Form::password('password_confirmation', ['class' => 'form-control', 'required', 'minlength' => '6', 'maxlength' => '15']) }}
                                    @error('password_confirmation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Change</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection
