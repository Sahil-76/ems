@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asset Type</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Asset Type
                                @can('create', new App\Models\AssetType())
                                    <a href="{{ route('asset-type.create') }}"
                                        class="btn btn-sm btn-primary float-lg-right m-2"><i class="fa fa-plus"></i> Create</a>
                                @endcan
                            </p>
                            <div class=" table-responsive">
                                <table id="example1" class="table" style="width: 100%">

                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            @canany(['update', 'delete'], new App\Models\AssetType())
                                                <th>Action</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assetTypes as $assetType)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $assetType->name }}</td>
                                                @canany(['update', 'delete'], new App\Models\AssetType())
                                                    <td>
                                                        @can('update', new App\Models\AssetType())
                                                            <a href="{{ route('asset-type.edit', $assetType->id) }}"><i
                                                                    class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @can('delete', new App\Models\AssetType())
                                                            <a href="javascript:void(0);"
                                                                onclick="deleteItem('{{ route('asset-type.destroy', $assetType->id) }}')"><i
                                                                    class="fa fa-trash text-danger"></i></a>
                                                        @endcan
                                                    </td>
                                                @endcanany
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footerScripts')
    <script>
        $('#example1').dataTable({
            ordering: false,
            "scrollX": true,
        });
    </script>
@endsection
