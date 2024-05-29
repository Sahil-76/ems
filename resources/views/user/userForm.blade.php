@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item "><a href="{{ route('user.index') }}">User</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">User Edit</h4>
                        {{ Form::model($user, ['route' => ['user.update', $user->id]]) }}
                        @method('PUT')
                        {{Form::hidden('id',null)}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) }}

                                    <div class="col-sm-9">
                                        {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('email', 'E-Mail Address', ['class' => 'col-sm-3 col-form-label']) }}

                                    <div class="col-sm-9">
                                        {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) }}
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('user_type', 'Select Type', ['class' => 'col-form-label ml-3']) }}
                                    <div class="col-sm-9 ml-auto">
                                        {{ Form::select('user_type', $userTypes, null, ['class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}

                                        @error('user_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Reset Password</label>

                                    <div class="col-sm-9">
                                        {{ Form::checkbox('resetPwd') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Active Only</label>
                                    <div class="col-sm-4">
                                        <div class="form-check">
                                            {{ Form::checkbox('is_active') }}
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Assign Roles</h4>
                        {{ Form::open(['route' => 'assignRole']) }}

                          <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group">
                                      {{ Form::hidden('user', $user->id) }}
                                      <div class="form-group row">
                                          @foreach ($roles as $role)
                                              <div class=" col-lg-3 col-md- col-sm-6 col-xs-12">
                                                  <div class="checkbox no-margin">
                                                      <label>
                                                          {{ Form::checkbox('role[]', $role->id, $user->hasRole($role->name)) }}
                                                          {{ $role->name }}
                                                      </label>
                                                  </div>
                                              </div>
                                          @endforeach
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
    </div>
@endsection

@section('footerScripts')
@endsection
