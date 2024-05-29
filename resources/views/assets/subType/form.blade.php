@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item ">Form</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin">
            <div class="card">

                {{ Form::model($subType, ['route' => $submitRoute, 'method' => $method]) }}
                    <div class="card-body">
                        <h4 class="card-title">Asset Sub-Type</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('type', 'Type', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::select('asset_type_id', $types, null, ['class' => 'form-control selectJS', 'placeholder' => 'Select any module', 'Required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Asset Sub-Type Name', 'Required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-check form-check-primary">
                                        <label class="form-check-label">Is Assignable
                                            <input type="checkbox" name="is_assignable"
                                                @if ($subType->is_assignable) checked @endif class="form-check-input">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

