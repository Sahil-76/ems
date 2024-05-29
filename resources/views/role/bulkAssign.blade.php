@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Roles</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <strong>Form</strong>
                </div>
                {{ Form::open(['route' => 'bulkStore', 'method' => 'POST']) }}
                <div class="card-body">
                    <h3 class="card-title mb-4">Users:</h3>
                    <div class="form-group">
                        <select style='width:100%;' name='user[]' class="form-control tail-select"
                            data-placeholder="Select an option" placeholder="select an option" multiple="multiple"
                            id="userEmail" onchange="getRoles()">
                            <option value="" disabled none>Select your option</option>
                            @foreach ($employeeDepartments as $department => $employees)
                                <optgroup label="{{ $department }}">

                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            @if ($employee->id == request()->user_id) selected @endif>
                                            {{ $employee->name . ' (' . $employee->employee->biometric_id . ')' }}</option>
                                    @endforeach
                                    
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <h3 class="card-title mb-4">Roles:</h3>
                    <div class='form-group'>
                        <div class="row">
                            @foreach ($roles as $id=>$name)
                                <div class="col-3">
                                    {{ Form::checkbox('role[]', $id, null, ['class' => 'form-group']) }}
                                    {{ Form::label('role', ucFirst($name), ['class' => 'form-group']) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class='ml-3'>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection


@section('footerScripts')
    <script></script>
@endsection
