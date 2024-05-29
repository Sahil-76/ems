@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manual Leave Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                        <h4 class="card-title">Apply Leave</h4>
                        <div class="row">
                            {{ Form::model($leave, ['route' => $submitRoute, 'method' => $method, 'files' => 'true']) }}
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('Select User', 'Select User', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        <select style='width:100%;' name='user_id' class="form-control selectJS"
                                        data-placeholder="Select an option" placeholder="select an option">
                                        <option value="" disabled selected>Select your option</option>
                                        @foreach ($employeeDepartments as $department=> $employees)
                                        <optgroup label="{{$department}}">
                                            @foreach($employees as $employee)
                                            <option value="{{$employee->user_id}}">{{$employee->name.' ('.$employee->biometric_id.')'}}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {{ Form::label('leave_type', 'Select  Leave Type', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::select('leave_type',$leaveTypes, null, ['class' => 'form-control selectJS', 'placeholder'=>'Select an option']) }}
                                    </div>
                                </div>
                                <div class="form-group leave_nature">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" value="1" name="" id="halfDayType">
                                        <label for="halfDayType">
                                            Half Day
                                        </label>
                                    </div>
                                </div>
                                <div id="halfday-category" style="display: none;">
                                    <div class="form-group row ml-xl-3">

                                        <div class="form-check col-sm-5">
                                            <input class="form-check-input" type="radio" name="leave_session" value="First half">
                                            <span class="" for="">First Half</span>
                                        </div>
                                        <div class="form-check col-sm-5">
                                            <input class="form-check-input" type="radio" name="leave_session" value="Second half">
                                            <span class="" for="">Second Half</span>
                                        </div>

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
                                                {{ Form::date('from_date', null, ['class' => 'form-control date','min'=>$today,'max'=>$max,'id'=>'from_date', 'placeholder' => 'choose from date']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            {{ Form::label('to_date', 'To Date', ['class' => 'col-sm-3 col-form-label']) }}
                                            <div class="col-sm-9">
                                                {{ Form::date('to_date', null, ['class' => 'form-control date','id'=>'to_date','min'=>$today,'max'=>$max, 'placeholder' => 'choose to date']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            {{ Form::label('reason', 'Enter Reason', ['class' => 'col-sm-3 col-form-label']) }}
                                            <div class="col-sm-9">
                                                {{ Form::textarea('reason', null, ['class' => 'form-control','rows'=>'4','cols'=>'4', 'placeholder' => 'Enter Reason']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            {{Form::label('attachment','Attach document', ['class' => 'col-sm-3 col-form-label']) }}
                                            <div class="col-sm-9 float-left">
                                                <input type="file" name="attachment" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('leave_status', 'Select  Leave Status', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('status',$status, null, ['class' => 'form-control selectJS', 'placeholder'=>'Select an option']) }}
                                </div>
                            </div>
                        </div>
                                <div class="form-group row">
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="declaration" required>
                                  I hereby declare that I have not any pending task related company & can contact me anytime if they required *
                                <i class="input-helper"></i></label>
                            </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" id="submit" class="btn btn-primary">Apply</button>
                            </div>

                            {{Form::close()}}
                        </div>
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

$('#from_date,#to_date').change(function(){
            if($('#to_date').val()!='' && $('#from_date').val()!='')
            {
                let from_date   =   $('#from_date').val().split("-");
                let to_date     =   $('#to_date').val().split("-");
                if(from_date[0]==to_date[0] && from_date[1] == to_date[1])
                {
                    $('#submit').prop('disabled',false);
                }
                else
                {
                    $('#submit').prop('disabled',true);
                    alert('Please select date of same month');
                }

            }
        });

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

        $('#halfday-category').show().find('input:radio').prop('checked', false).prop('required', true);
    } else {

        $('#halfday-category').hide().find('input:radio').prop('checked', false).removeAttr('required');

    }
});
</script>
@endsection
