@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asset Category</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Asset Category
                                @can('create', new App\Models\AssetCategory())
                                    <a href="{{ route('asset-category.create') }}"
                                        class="btn btn-sm btn-primary float-lg-right m-2">Add </a>
                                @endcan
                            </p>
                            <div class=" table-responsive">
                                <table id="example1" class="table" style="width: 100%">

                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            @canany(['update', 'delete'], new App\Models\AssetCategory())
                                                <th>Action</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assetCategorys as $assetCategory)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ ucfirst($assetCategory->name) }}</td>
                                                @canany(['update', 'delete'], new App\Models\AssetCategory())
                                                    <td>
                                                        @can('update', new App\Models\AssetCategory())
                                                            <a href="{{ route('asset-category.edit', $assetCategory->id) }}"><i
                                                                    class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @can('delete', new App\Models\AssetCategory())
                                                            <a href="javascript:void(0);" class="text-red"
                                                                onclick="deleteItem('{{ route('asset-category.destroy', $assetCategory->id) }}')"><i
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
