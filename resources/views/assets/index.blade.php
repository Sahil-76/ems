@extends('layouts.master')
@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assets List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card">

                {{ Form::open(['method' => 'GET']) }}
                <div class="card-body">
                    <p class="card-title">Filter</p>
                    <div class="form-group row">

                        {{ Form::label('type', 'Select Type', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('type', $types, request()->type, ['class' => 'form-control selectJS','placeholder' => 'Select Type']) }}
                        </div>

                        {{ Form::label('sub_type', 'Select Sub Type', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('sub_type', $sub_types, request()->sub_type, ['class' => 'form-control selectJS','placeholder' => 'Select Sub Type']) }}
                        </div>

                        {{ Form::label('status', 'Select Status', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('status', $statuses, request()->status, ['class' => 'form-control selectJS','placeholder' => 'Select Status']) }}
                        </div>

                        {{ Form::label('Bar Code', 'Bar Code', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::text('bar_code', request()->bar_code, ['class' => 'form-control', 'placeholder' => 'Enter Bar Code']) }}
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                            <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                        </div>
                    </div>

                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Assets List
                                <div class="float-right">
                                    @can('create',new App\Models\Asset())
                                        <a href="{{ route('asset.create') }}" class="btn btn-sm btn-primary"> <i
                                                class="fa fa-plus"></i> Create </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="example1" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Sub Type</th>
                                            <th>Company</th>
                                            <th>Barcode</th>
                                            <th class="text-center">Img</th>
                                            <th>Status</th>
                                            <th>Description</th>
                                            @can('update',new App\Models\Asset())
                                                <th>Edit</th>
                                            @endcan
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $query  = http_build_query(request()->query());
    @endphp
@endsection

@section('footerScripts')
    <script>
        $('#example1').dataTable({
            fixedColumns: true,
            processing: true,
            serverSide: true,
            ajax: {
            url: "{!! route('assetList', $query) !!}",
        },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'assetType_name',
                        name: 'assetType_name'
                    },
                    {
                        data: 'subType_name',
                        name: 'subType_name'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'barcode',
                    },
                    {
                        data: 'img',
                        orderable: false,
                        searchable: false
                    }, 
                    {
                        data: 'status',
                    }, 
                    {
                        data: 'description',
                    }, 
                    @can('update',new App\Models\Asset())
                    {
                        data: 'edit',
                        orderable: false,
                        searchable: false
                    }, 
                    @endcan
                    {
                        data: 'detail',
                        orderable: false,
                        searchable: false
                    }, 
                ]
        });
    </script>
@endsection
