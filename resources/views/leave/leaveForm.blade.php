@extends('layouts.master')
@section('content')
    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leave Form</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin">

            <div class="card">
                {{ Form::model($model, ['route' => $submitRoute, 'files' => 'true', 'onsubmit' => 'myButton.disabled = true; return true;']) }}
                <div class="card-body">
                    <div class="card-title">Apply leave <span class="float-lg-right">Default Leave Session is Full
                            Day</span></div>
         
                    <div class="row">


                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('leave_type', 'Select leave type', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('leave_type', $leaveTypes, null, ['class' => 'form-control selectJS', 'placeholder' => 'Select an option', 'required' => 'required']) }}

                                    @error('leave_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <div class="form-check ml-3 form-check-primary">
                                    <label class="form-check-label">Half Day
                                        <input type="checkbox" name="leave_session" id="halfDayType" value="Half day"
                                            class="form-check-input">
                                    </label>
                                    @error('leave_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                          
                            <div class="form-group row" id="leave-type" style="display: none;">
                              
                                <div class="form-check ml-3">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="halfDayType" value="First half">
                                        First half
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="halfDayType" value="Second half">
                                        Second Half
                                        <i class="input-helper"></i></label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('from_date', 'From Date', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::date('from_date', !empty(request()->from_date) ? request()->from_date : null, ['class' => 'form-control date', 'min' => $today, 'max' => $max, 'id' => 'from_date', 'placeholder' => 'choose from date', 'required' => 'required']) }}

                                            @error('from_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('to_date', 'To Date', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::date('to_date', !empty(request()->to_date) ? request()->to_date : null, ['class' => 'form-control date', 'id' => 'to_date', 'min' => $today, 'max' => $max, 'placeholder' => 'choose to date', 'required' => 'required']) }}

                                            @error('to_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('reason', 'Enter Reason', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::textarea('reason', null, ['class' => 'form-control', 'rows' => '4', 'cols' => '4', 'placeholder' => 'Enter Reason', 'required' => 'required']) }}

                                            @error('reason')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('attachment', 'Attach document', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9 float-left">
                                            <input type="file" name="attachment" class="form-control" />
                                        </div>
                                        @error('attachment')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" required>
                                    I hereby declare that I have not any pending task related company & can contact me
                                    anytime if they required *
                                    <i class="input-helper"></i></label>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" id="submit" name="myButton" class="btn btn-primary">Apply</button>
                        </div>

                        {{ Form::close() }}
                    </div>
                </div>

            </div>



        </div>


    </div>
@endsection
@section('footerScripts')
    <script>
        var lastDay = function(y, m) {
            return new Date(y, m + 1, 0).getDate();
        }
        $('#from_date,#to_date').change(function() {
            if ($('#to_date').val() != '' && $('#from_date').val() != '') {
                let from_date = $('#from_date').val().split("-");
                let to_date = $('#to_date').val().split("-");
                if (from_date[0] == to_date[0] && from_date[1] == to_date[1]) {
                    $('#submit').prop('disabled', false);
                } else {
                    $('#submit').prop('disabled', true);
                    alert('Please select date of same month');
                }


            }


        });
        // code for minimum validation of date apply format
        $('#from_date').change(function() {
            if ($('#from_date').val() != '') {
                let from_date = $('#from_date').val().split("-");
                let to_date_max = '';
                if (from_date[2] < 21) {
                    to_date_max = from_date[0] + '-' + from_date[1] + '-' + '20';
                } else {
                    let last_day = lastDay(from_date[0], from_date[1] - 1);
                    to_date_max = from_date[0] + '-' + from_date[1] + '-' + last_day;
                }
                $('#to_date').val('').prop('max', to_date_max);

            }
        });
        $('input:checkbox').change(function() {
            if ($('#halfDayType').is(':checked')) {
                $('#leave-type').show().find('input:radio').prop('checked', false).prop('required', true);
            } else {

                $('#leave-type').hide().find('input:radio').prop('checked', false).removeAttr('required');
            }
            if ($('#short-leave-type').is(':checked')) {
                $('#short-leave-timing').show();
                $('.selectJS').select2({
                    placeholder: "Select an option",
                    allowClear: true
                });
                $('#leave-type').hide().find('input:radio').prop('checked', false);
            } else {

                $('#short-leave-timing').hide();
            }

        });
    </script>
@endsection
