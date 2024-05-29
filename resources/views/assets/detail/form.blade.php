@extends('layouts.master')
@section('content')

<div class="row">
  <div class="col-12">
    <nav aria-label="breadcrumb" class="float-right">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
          <li class="breadcrumb-item ">Asset Detail Form</li>
        </ol>
    </nav>
  </div>
  <div class="col-12 grid-margin">

    <div class="card">
        {{Form::model($assetDetail,['route'=>$submitRoute,'method'=>$method, 'files' => true])}}
        <div class="card-body">
            <h4 class="card-title">Asset Detail Form</h4>
            {{Form::hidden('asset_id',null)}}

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group row">
                        {{Form::label('company','Company', ['class' => 'col-sm-3 col-form-label','files' => 'true']) }}
                        <div class="col-sm-9">
                            {{Form::text('company',null,['class'=>'form-control','placeholder'=>'Enter Company'])}}
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-6">
                    <div class="form-group row">
                        {{ Form::label('bill', 'Bill', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            <input type="file" name="bill" accept="image/jpeg,image,jpg,image/png"
                                class="form-control">
                        </div>
                    </div>
                </div> --}}

                <div class="col-md-6">
                    <div class="form-group row">
                        {{Form::label('ram','RAM', ['class' => 'col-sm-3 col-form-label','files' => 'true']) }}
                        <div class="col-sm-9">
                            {{Form::text('ram',null,['class'=>'form-control','placeholder'=>'Enter Company'])}}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group row">
                        {{Form::label('rom','ROM', ['class' => 'col-sm-3 col-form-label','files' => 'true']) }}
                        <div class="col-sm-9">
                            {{Form::text('rom',null,['class'=>'form-control','placeholder'=>'Enter Company'])}}
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>

                {{Form::close()}}
            </div>
        </div>

    </div>



</div>
</div>
@endsection
@section('footerScripts')

@endsection
