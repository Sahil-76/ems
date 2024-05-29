@extends('layouts.master')

@section('content')

    <style>
        .text-danger {
            display: none;
        }
    </style>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Role : {{ $role->name }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('role.index') }}">Roles</a></li>
                            <li class="breadcrumb-item active">Assign Permission</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @can('assignPermission', new App\Models\Permission())
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">

                        <div class="card-header">
                            <h3 class="card-title">Assign Permissions</h3>
                        </div>

                        {{ Form::model($role, ['route' => $submitRoute]) }}
                        @method('PUT')
                          {{ Form::hidden('id', null) }}
                          {{ Form::hidden('name', null) }}

                          <div class="card-body">

                              @if (!empty($permissions))
                                  <div class="box-body">
                                      <div class="form-group row">
                                          <button class="btn btn-info" type="button" id="clearAllSelected">Clear</button>
                                      </div>

                                      <div class="form-group row">
                                          @foreach ($permissions as $module_name => $permissions)
                                              <div class="container col-xs-12 ">
                                                  <div class="header col-xs-12 box box-body">
                                                      <h4 style="margin: 0px 0px 10px 10px;">
                                                          <u>{{ strtoupper($module_name) }}</u>:</h4>
                                                      <div class="col-xs-12 container row">
                                                          @foreach ($permissions as $access)
                                                              <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                                  <div class="checkbox no-margin" style="width: max-content">
                                                                      <label title="{{ $access->description }}"
                                                                          class="margin-r-5">
                                                                          {{ Form::checkbox(
                                                                              'permission[]',
                                                                              $access->id,
                                                                              $role->hasPermission($access->module->name, $access->access),
                                                                          
                                                                              ['class' => 'check-box'],
                                                                          ) }}
                                                                          &nbsp;&nbsp;&nbsp;{{ $access->access }}
                                                                      </label>

                                                                  </div>
                                                              </div>
                                                          @endforeach
                                                      </div>
                                                  </div>
                                              </div><br><br><br><br>
                                          @endforeach
                                      </div>
                                  </div>
                              @endif

                          </div>

                          <div class="card-footer">
                              <button type="submit" class="btn btn-primary">Submit</button>
                          </div>
                        {{ Form::close() }}

                    </div>
                </div>
            </section>
        @endcan


    </div>

@endsection

@section('footerScripts')
    <script>
        $(".check-box").click(function() {
            if ($(this).is(':checked')) {
                $(this).attr('checked', 'checked');
            } else {
                $(this).removeAttr('checked');
                // $(this).removeProp('checked');
            }
        })
        $('#clearAllSelected').click(function() {
            $.each($('input[type=checkbox]'), function(index, selector) {
                if ($(selector).is(':checked')) {
                    $(selector).trigger('click');
                }
            })
        })
    </script>
@endsection
