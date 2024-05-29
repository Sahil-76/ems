@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Department</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Update Department</h4>
                    {{ Form::open(['route' => 'assignDepartment', 'class' => 'form-sample']) }}
                    <div class="form-group col-md-6">
                        {{ Form::label('Employee', 'Select Employee', ['class' => 'form-label']) }}
                        {{ Form::select('employee', $employees, null, ['class' => 'form-control selectJS employee', 'placeholder' => 'Choose one']) }}

                        @error('employee')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('Department', 'Select Department', ['class' => 'form-label']) }}
                        {{ Form::select('department', $departments, null, ['class' => 'form-control selectJS department', 'placeholder' => 'Choose one']) }}

                        @error('department')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        $(document).ready(function() {
            $('.employee').change(function() {
                var employee_id = $(this).val();
                $.ajax({
                    url: "{{ route('changeDepartment') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: employee_id
                    },
                    success: function(response) {
                        $(".department").select2("val", response);
                    }
                });
            });
        });
    </script>
@endsection
