@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {{ Form::open(['route' => 'switchlogin', 'method' => 'POST']) }}
                        <div class="card-body">
                            <p class="card-title">User List</p>
                            <div class="col-sm-12">
                                <select style='width:100%;' name="id" data-placeholder="select an option"
                                    placeholder="select an option" class='form-control selectJS' id="employees">
                                    <option value="" disabled selected>Select an option</option>

                                    @foreach ($employeeDepartments as $department => $employees)
                                        <optgroup label="{{ $department }}">
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">
                                                    {{ $employee->name . ' (' . optional($employee->employee)->biometric_id . ')' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach

                                </select>
                            </div>

                            <button class="btn btn-primary mt-4">Submit</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
