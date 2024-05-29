@extends('layouts.master')
@section('content')
    @php
        $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assets Assignment List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card">
                {{ Form::open(['method' => 'GET']) }}
                    <div class="card-body">
                        <p class="card-title">Filter</p>
                        <div class="form-group row">
                            @can('modify', new App\Models\Asset())
                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('department_id', $departments, request()->department_id, ['onchange' => 'getEmployees(this.value)','class' => 'form-control selectJS','placeholder' => 'Select Department']) }}
                                </div>
                            @endcan

                            {{ Form::label('barcode', 'Enter Barcode', ['class' => 'col-sm-2 col-form-label']) }}
                            <div class="col-sm-4">
                                {{ Form::text('barcode', request()->barcode, ['class' => 'form-control col-sm-11', 'placeholder' => 'Enter Barcode']) }}
                            </div>

                            {{ Form::label('user_id', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    <select style='width:100%;' name="user_id" data-placeholder="select an option"
                                        placeholder="select an option" class='form-control selectJS' id="employees">
                                        <option value="" disabled selected>Select your option</option>
                                        @foreach ($employeeDepartments as $department => $employee)
                                            <optgroup label="{{ $department }}">
                                                @foreach ($employee as $user)
                                                    <option value="{{$user->user_id}}" @if($user->user_id == request()->user_id) selected @endif> {{ $user->name.' ('.$user->biometric_id.')'}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                            {{ Form::label('sub_type', 'Select Subtype', ['class' => 'col-sm-2 col-form-label']) }}
                            <div class="col-sm-4">
                                {{ Form::select('sub_type', $assetSubTypes, request()->sub_type, ['class' => 'form-control selectJS','placeholder' => 'Select Sub type']) }}
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-check form-check-primary">
                                        <label class="form-check-label">Unassigned
                                            <input type="checkbox" name="unassigned" @if(request()->unassigned) checked @endif class="form-check-input">
                                            <i class="input-helper"></i></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="row">
                @php
                    $page = 0;
                    if (!empty(request()->page) && request()->page != 1) {
                        $page = request()->page - 1;
                        $page = $page * 25;
                    }
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Employee Asset Assignment
                            </div>
                            <div class="table-responsive">
                                <table id="example1" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Barcode</th>
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $employee)
                                            <tr>
                                                <td>{{ $loop->iteration + $page }}</td>
                                                <td>{{ $employee->name ?? null }}</td>
                                                <td>{{ $employee->department->name ?? null }}</td>
                                                <td>
                                                    @if (!empty($employee->biometric_id))
                                                        {!! $generator->getBarcode($employee->biometric_id, $generator::TYPE_CODE_128) !!}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($employee->biometric_id))
                                                    <a href="{{ route('assignEquipments', ['id' => $employee->biometric_id]) }}"
                                                        target="_blank"><i class="fa fa-edit"></i></a>
                                                    @endif
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
    <script src="{{ url('js/scanner.js') }}"></script>
    <script>
        var url = "{{ route('assignEquipments') }}";

        $(document).scannerDetection({
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
            preventDefault: true,
            endChar: [13],
            onComplete: function(barcode, qty) {
                validScan = true;
                window.open(url + "?id=" + barcode, '_blank');
            },
            onError: function(string, qty) {
                res = string.split("-");
                var inward_id = res[0];
                var per_id = res[2];
            }
        });

        $('#example1').dataTable();

        function getEmployees(department_id) {
                if (department_id) {
                    $.ajax({
                        url: "{{ route('getUsers') }}/" + department_id,
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {
                            var options = `<option value=''></option>`;
                            $.each(response, function(key,user) {
                                options += "<option value='" + user.user_id + "'>" +user.name +"("+user.biometric_id+")"
                                    "</option>";
                            });

                            $('#employees').html(options);
                            $("select").select2({
                                placeholder: "Select an option",
                                allowClear: true,
                            });
                        }
                    })
                }
            }
    </script>
@endsection
