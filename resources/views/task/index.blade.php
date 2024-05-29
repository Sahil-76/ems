@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-4 mt-2">
            @can('create',new App\Models\Task())
                <a href="{{route('task.create')}}" class="btn btn-sm btn-primary"> <i class="fa fa-plus"></i> Create </a>
            @endcan
        </div>
        <div class="col-8">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Task</li>
                </ol>
            </nav>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Basic Tasks
                            </div>
                            <div class="table-responsive">
                                <table id="basic-table" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Task</th>
                                            @can('hrEmployeeList',new App\Models\Employee())
                                                <th>Action</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($basicTasks as $task)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $task->name }}</td>
                                                @can('hrEmployeeList',new App\Models\Employee())
                                                    <td>
                                                        <a href="{{route('task.edit',['task'=>$task->id])}}"><i class="fa fa-edit"></i></a>
                                                        <a href="javascript:void(0);" onclick="deleteItem('{{ route('task.destroy', $task->id) }}')">
                                                            <i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                @endcan
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Departmental Tasks</div>
                            <div class="table-responsive">
                                <table id="dept-table" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Task</th>
                                            <th>Department</th>
                                            @canany(['update','delete'],new App\Models\Task())
                                                <th>Action</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($departmentalTasks as $task)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $task->name }}</td>
                                                <td>{{ $task->department->name ?? ''}}</td>
                                                @canany(['update','delete'],new App\Models\Task())
                                                    <td>
                                                        @can('update',new App\Models\Task())
                                                            <a href="{{route('task.edit',['task'=>$task->id])}}"><i class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @can('delete',new App\Models\Task())
                                                            <a href="javascript:void(0);" onclick="deleteItem('{{ route('task.destroy', $task->id) }}')">
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

        $('#basic-table').DataTable({
            pageLength:5,
            columnDefs: [{
                    orderable: true,
                    targets: [1]
                },
                {
                    orderable: false,
                    targets: '_all'
                }
            ]
        });
        $('#dept-table').DataTable({
            pageLength:5,
            columnDefs: [{
                    orderable: true,
                    targets: [1, 2]
                },
                {
                    orderable: false,
                    targets: '_all'
                }
            ]
        });

    </script>
@endsection
