@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Asset Form</div>
                    {{ Form::model($asset, ['route' => $submitRoute, 'method' => $method]) }}

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('category_id', 'Category', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('category_id', $categories, null, ['class' => 'form-control selectJS', 'id' => 'category-id', 'onchange' => 'getTypes(this.value)', 'placeholder' => 'Select Category', 'required', 'data-placeholder' => 'Select Category']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('type_id', 'Type', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('type_id', [], null, ['class' => 'form-control selectJS', 'id' => 'type-id', 'onchange' => 'getSubTypes(this.value)', 'placeholder' => 'Select Type', 'required', 'data-placeholder' => 'Select Type']) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('sub_type_id', 'Sub Type', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('sub_type_id', [], null, ['class' => 'form-control selectJS', 'id' => 'sub-type-id', 'placeholder' => 'Select Type', 'onchange' => 'getCompanyTypes(this.value)', 'required', 'data-placeholder' => 'Select Type']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('company_id', 'Company', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('company_id', [], null, ['class' => 'form-control selectJS', 'id' => 'company-id', 'placeholder' => 'Select Type', 'required', 'data-placeholder' => 'Select Type']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('barcode', 'Barcode', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{ Form::text('barcode', null, ['id' => 'barcode-field','class' => 'form-control','placeholder' => 'barcode','readonly' => 'readonly']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('status', 'Status', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('status', $status, null, ['class' => 'form-control selectJS', 'placeholder' => 'Select Type', 'data-placeholder' => 'Select Type']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('Description', 'Description', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::textarea('description',null,['class'=>'form-control','rows'=>'3'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" id="submitButton" class="btn btn-primary me-2"
                                        onclick="barcodeAdded()">Submit</button>
                                </div>

                            </div>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footerScripts')
    <script src="{{ url('js/scanner.js') }}"></script>
    <script>
        var regExp = /[a-zA-Z]/g;
        $(document).scannerDetection({
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
            preventDefault: true,

            endChar: [13],
            onComplete: function(barcode, qty) {
                validScan = true;
                if (regExp.test(barcode) || /^[a-zA-Z0-9- ]*$/.test(barcode) == false) {
                    alert("Invalid barcode");
                    $('#submitButton').prop('disabled', true);
                    return false;
                } else {
                    $('#submitButton').prop('disabled', false);
                    $('#barcode-field').val(barcode);
                    barcodeAdded();
                }

            },
            onError: function(string, qty) {

                res = string.split("-");
                var inward_id = res[0];
                var per_id = res[2];
            }
        });
    </script>

    <script>
        function barcodeAdded() {
            event.preventDefault();
            if ($('#category-id').val() == "") {
                alert('please select category');
                return false;
            }
            if ($('#type-id').val() == "") {
                alert('please select type');
                return false;
            }
            if ($('#sub-type-id').val() == "") {
                alert('please select subtype');
                return false;
            }
            if ($('#barcode-field').val() == "") {
                alert('please add barcode');
                return false;
            }

            let data = $('form').serialize();
            $.ajax({
                url: "{{ route('asset.store') }}",
                data: data,
                type: 'POST',
                success: function(response) {
                    console.log('fvfdgdf');
                    $('#barcode-field').val('');
                    if (response == 'asset added') {
                        toastr.success(response);
                    } else {
                        alert(response);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }


        function getTypes(type_id) {
            if (type_id) {
                $.ajax({
                    url: "{{ route('getTypes') }}/?id=" + type_id,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        var options = `<option value=''></option>`;
                        $.each(response, function(id, name) {
                            options += "<option value='" + id + "'>" + name + "</option>";
                        });

                        $('#type-id').html(options);
                        $("select").select2({
                            placeholder: "Select an option"
                        });
                    }
                })
            }
        }

        function getSubTypes(sub_type_id) {
            console.log(sub_type_id);
            if (sub_type_id) {
                $.ajax({
                    url: "{{ route('getSubTypes') }}/?id=" + sub_type_id,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        var options = `<option value=''></option>`;
                        $.each(response, function(id, name) {
                            options += "<option value='" + id + "'>" + name + "</option>";
                        });

                        $('#sub-type-id').html(options);
                        $("select").select2({
                            placeholder: "Select an option"
                        });
                    }
                })
            }
        }

        function getCompanyTypes(company_id) {
            console.log(company_id);
            if (company_id) {
                $.ajax({
                    url: "{{ route('getCompanyTypes') }}/?id=" + company_id,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        var options = `<option value=''></option>`;
                        $.each(response, function(id, name) {
                            options += "<option value='" + id + "'>" + name + "</option>";
                        });

                        $('#company-id').html(options);
                        $("select").select2({
                            placeholder: "Select an option"
                        });
                    }
                })
            }
        }
    </script>
@endsection
