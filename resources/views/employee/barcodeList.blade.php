@extends('layouts.master')
@section('headerLinks')
    <style type="text/css">
        .content {
            position: absolute;
            top: 5%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 200px;
            text-align: center;
            background-color: #e8eae6;
            box-sizing: border-box;
            padding: 10px;
            z-index: 100;
            display: none;
            /*to hide popup initially*/
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 15px;
            background-color: black;
            color: white;
            border-radius: 50%;
            padding: 4px;
        }

    </style>
@endsection
@section('content')
    @php

    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();

    @endphp
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Barcode List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 mb-3">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {{ Form::open(['method' => 'GET']) }}
                        <div class="card-body">
                            <p class="card-title">Filter</p>
                            <div class="form-group row">
                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('department_id', $department_id, request()->department_id, ['onchange' => 'getEmployees(this.value)', 'class' => 'form-control selectJS', 'placeholder' => 'Select your department']) }}
                                </div>

                                {{ Form::label('name', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('name', $names, request()->name, ['id' => 'employees', 'class' => 'form-control selectJS', 'placeholder' => 'Select Employee']) }}
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary">
                                            <label class="form-check-label">ID Card Uploaded
                                                <input type="checkbox" name="id_card" @if(request()->id_card) checked @endif class="form-check-input">
                                                <i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary">
                                            <label class="form-check-label">ID Card Not Uploaded
                                                <input type="checkbox" name="not_id_card" @if(request()->not_id_card) checked @endif class="form-check-input">
                                                <i class="input-helper"></i></label>
                                        </div>
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
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <!-- Default box -->
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <b>Barcode List</b>
                            <div class="col-md-8 float-right text-right">
                                <b>Total Results: </b>{{ $employees->total() }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive ">
                            <table id="" class="table table-hover gallery">
                                <thead>
                                    <tr>
                                        <th>Aadhaar Name</th>
                                        <th>Department</th>
                                        <th>Picture</th>
                                        <th>Contract Date</th>
                                        <th>Emp Code</th>
                                        <th>Bar Code</th>
                                        <th>Upload Id Card</th>
                                        {{-- <th>Bar Code Download</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->aadhaar_name }}<span class="ml-1"
                                                    onclick="copyToClipboard('{{ $employee->aadhaar_name }}')"><i
                                                        class="fa fa-copy text-primary"></span></td>
                                            <td>{{ $employee->department_trial }}<span class="ml-1"
                                                    onclick="copyToClipboard('{{ $employee->department_trial }}')"><i
                                                        class="fa fa-copy text-primary"></span></td>
                                            <td>
                                                <a target="_blank" href="{{ $employee->user->user_image }}" class="employee-image"><img
                                                        src="{{ $employee->user->user_image }}" width="42" height="42">
                                                </a>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($employee->contract_date)->format('d-m-Y') }}<span class="ml-1"
                                                    onclick="copyToClipboard('{{ \Carbon\Carbon::parse($employee->contract_date)->format('d-m-Y') }}')"><i
                                                        class="fa fa-copy text-primary"></span></td>
                                            <td>{{ $employee->department->short_name . '-' . $employee->biometric_id }} <span class="ml-1"
                                                    onclick="copyToClipboard('{{ $employee->department->short_name . '-' . $employee->biometric_id }}')"><i
                                                        class="fa fa-copy text-primary"></span></td>
                                            <td>
                                                @if (!empty($employee->biometric_id))
                                                    @php
                                                        $html = $generator->getBarcode($employee->biometric_id, $generator::TYPE_CODE_128) . "<p style='margin-left:74px;'>$employee->biometric_id</p>";
                                                    @endphp
                                                    {!! $generator->getBarcode($employee->biometric_id, $generator::TYPE_CODE_128) !!}
                                                    <p style="margin-left:74px;">{{ $employee->biometric_id }}<a class="ml-1"
                                                            href="{{ route('barCodeImage', ['html' => $html, 'employee_id' => $employee->id]) }}">
                                                            <i class="fa fa-download"></i> </a></p>
                                                @endif
                                                {{-- <span> </span> --}}
                                            </td>

                                            <td> 
                                                {{-- <button onclick="togglePopup({{ $employee->id }})"
                                                    class="btn btn-primary">Upload ID Card</button> --}}
                                                <button onclick='action("{{ $employee->id }}")'
                                                    class="btn btn-primary btn-lg p-3" data-toggle="modal"
                                                    data-target="#openPopup">Upload ID Card</button>
                                            </td>
                                            <td>
                                                @if (!empty($employee->id_card))
                                                    <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->id_card])}}">
                                                        <i class="fa fa-eye text-primary"></i>
                                                    </a>
                                                @endif
                                                </div>
                                            </td>
                                            {{-- <td> <a href="{{route('barCodeImage',['html'=>$html,'employee_id'=>$employee->id]) }}"> <i class="fa fa-download"></i> </a></td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-4 float-right">
                        <div class="col-md-12">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    </div>

               
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="openPopup" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalLabel">Ticket Action: <span></span></h5> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{ form::open(['route' => 'uploadIdCard', 'files' => 'true', 'method' => 'post']) }}
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId" value="">
                   
                    <div class="form-group row">
                        {{ Form::label('Id Card', 'ID Card', ['class' => 'col-sm-3 col-form-label']) }}
                        <input type="file" name="id_card" id="upload">
                     
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection


@section('footerScripts')
    <script>
        function getEmployees(department_id) {
            if (department_id) {
                $.ajax({
                    url: "{{ route('getEmployees') }}/" + department_id,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        var options = `<option value=''></option>`;
                        var option = `<option value=''></option>`;
                        $.each(response, function(name, office_email) {
                            option += "<option value='" + name + "'>" + name + "</option>";
                            options += "<option value='" + office_email + "'>" + office_email +
                                "</option>";
                        });

                        $('#employees').html(options);
                        $('#emails').html(option);
                        $("select").select2({
                            placeholder: "Select an option"
                        });
                    }
                })
            }
        }
        $('#example1').dataTable({
            ordering: false,
            fixedColumns: true,
            'searching': false,

            columnsDefs: [{
                    "name": "Picture",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Name"
                },
            ],

        });
        // $(document).ready(function(){
        //     $(".content").hide();
        // });
    

        function action(id) {
            $('#userId').val(id);
        }
    </script>
@endsection
