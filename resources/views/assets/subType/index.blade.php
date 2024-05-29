@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asset Sub-Type</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Asset Sub-Type
                                @can('create', new App\Models\AssetSubType())
                                    <a href="{{ route('asset-subtype.create') }}"
                                        class="btn btn-sm btn-primary float-lg-right m-2">Add </a>
                                @endcan
                            </p>
                            <div class=" table-responsive">
                                <table id="example1" class="table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            @canany(['update', 'delete'], new App\Models\AssetSubType())
                                                <th>Action</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subTypes as $subType)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ ucfirst($subType->name) }}</td>
                                                <td>{{ $subType->assetType->name ?? '' }}</td>
                                                @canany(['update', 'delete'], new App\Models\AssetSubType())
                                                    <td>
                                                        @can('update', new App\Models\AssetSubType())
                                                            <a href="{{ route('asset-subtype.edit', $subType->id) }}"><i
                                                                    class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @can('delete', new App\Models\AssetSubType())
                                                            <a href="javascript:void(0);" class="text-red"
                                                                onclick="deleteItem('{{ route('asset-subtype.destroy', $subType->id) }}')"><i
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
