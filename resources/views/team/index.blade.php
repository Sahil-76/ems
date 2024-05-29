@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Teams</li>
                </ol>
            </nav>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Teams
                                <div class="float-right">
                                    @can('create',new App\Models\Team())
                                        <a href="{{route('team.create')}}" class="btn btn-sm btn-primary"> <i class="fa fa-plus"></i> Create </a>
                                    @endcan
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="data-table" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Team</th>
                                            <th>Department</th>
                                            <th>Reporting Person</th>
                                            @canany(['update','delete'],new App\Models\Team())
                                                <th>Action</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($teams as $team)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $team->name ?? ''}}</td>
                                                <td>{{ $team->department->name ?? ''}}</td>
                                                <td>{{ $team->reporting_person ?? ''}}</td>
                                                @canany(['update','delete'],new App\Models\Team())
                                                    <td>
                                                        @can('update',new App\Models\Team())
                                                            <a href="{{ route('team.edit', ['team' => $team->id]) }}"><i class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @can('delete',new App\Models\Team())
                                                            <a href="javascript:void(0);" onclick="deleteItem('{{ route('team.destroy', $team->id) }}')">
                                                                <i class="fa fa-trash text-danger"></i></a>
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

        $('#data-table').DataTable({
            columnDefs: [{
                    orderable: true,
                    targets: [1, 2, 3]
                },
                {
                    orderable: false,
                    targets: '_all'
                }
            ]
        });

    </script>
@endsection
