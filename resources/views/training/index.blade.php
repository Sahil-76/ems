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
                            <div class="card-title">Employees on Training
                            </div>
                            <div class="table-responsive">
                                <table id="data-table" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee</th>
                                            <th>Department</th>
                                            <th>Trainer</th>
                                            <th>Result</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php 
                                            $user = null;
                                        @endphp
                                        @foreach ($trainingEmployees as $trainingEmployee)
                                            @php
                                                $user  =    $trainingEmployee->user;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><a href="{{ route('employeeDetail', ['employee' => $user->employee->id]) }}" target="_blank"> {{ $user->name ?? ''}} </a></td>
                                                <td><a href="{{ route('employeeView', ['department_id' => $user->employee->department_id]) }}" target="_blank"> {{ $user->employee->department->name ?? ''}} </a></td>
                                                <td>{{$trainingEmployee->trainer->name ?? null}}</td>
                                                <td>{{$trainingEmployee->result}}</td>
                                                <td>
                                                    <a href="{{ route('training.edit', $trainingEmployee->id) }}" class="btn btn-warning p-3">Detail</a>
                                                </td>
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
                    targets: [1, 2, 3, 4]
                },
                {
                    orderable: false,
                    targets: '_all'
                }
            ]
        });

    </script>
@endsection
