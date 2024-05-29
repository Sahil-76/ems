@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <span class="font-weight-bold">Department List</span>
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Department List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">
                        <a href="{{ route('departments.create') }}" class="btn btn-facebook">Create New</a>
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Short Name</th>
                                    <th>Manager</th>
                                    <th>Team Leader</th>
                                    <th>Line Manager</th>
                                    <th>Total Employees</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($departments as $department)
                                    <tr>
                                        <td>{{ $department->name }}</td>
                                        <td>{{ $department->short_name }}</td>
                                        <td>{{ $department->manager_name }}</td>
                                        <td>{{ $department->team_leader_name }}</td>
                                        <td>{{ $department->line_manager_name }}</td>
                                        <td>{{ $department->employees_count }}</td>
                                        <td>
                                            <a class="btn btn-danger" href="{{ route('departments.edit', $department) }}">Edit</a>
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
@endsection

@section('footerScripts')
@endsection
