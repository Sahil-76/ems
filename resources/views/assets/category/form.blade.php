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

                {{ Form::model($assetCategory, ['route' => $submitRoute, 'method' => $method]) }}
                    <div class="card-body">
                        <h4 class="card-title">Asset Category</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Asset Category Name']) }}
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
