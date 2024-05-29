@extends('layouts.master')
@section('content')
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Permission List</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    {{ Form::open(['method' => 'GET']) }}
                    <div class="card-body">
                        <p class="card-title">Filter</p>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                {{ Form::label('module', 'Select Module') }}
                                {{ Form::select('module_id', $modules, request()->module_id ?? null, array_merge(['id' => 'module', 'class' => 'form-control selectJS', 'placeholder' => 'All'])) }}
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
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="card-title">Permission List</p>
                            </div>
                            <div class="col-6">
                                <div class="card-tools float-right">
                                    <a href="{{ route('permission.create') }}" class="btn btn-primary btn-rounded ml-auto"
                                        style="margin-right: 10px;">Create <i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                       
                        <div class="table-responsive">
                            <table class="table table-hover" style="width: 100%" id="data-table">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th class="text-center">Access</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @php
        $query = http_build_query(request()->query());
    @endphp
@endsection

@section('footerScripts')
    <script>
        $(document).ready(function() {
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: "{!! route('permission.index', $query) !!}",
                    global: false,
                },
                columns: [{
                        data: "module_name",
                        name: "module.name",
                        className: "text-center"
                    },
                    {
                        data: "access",
                        name: "permission.access",
                        className: "text-center"
                    },
                    {
                        data: "description",
                        name: "permission.description",
                        className: "text-center"
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },

                ]
            });

        })
    </script>
@endsection
