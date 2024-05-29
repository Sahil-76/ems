@extends('layouts.master')
@section('content')

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employeeView') }}">Employee</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">

            <div class="card">

                {{ Form::model($employee, ['route' => $submitRoute, 'files' => 'true']) }}
                <div class="card-body">
                    <h4 class="card-title">Employee Form</h4>
                    {{Form::hidden('id',null)}}

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('department_id', $list['departments'], null, ['class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('name', 'Employee Name', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                 {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Employee Name']) }}

                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('phone', 'Contact Number', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::tel('phone', null, ['class' => 'form-control', 'onkeyup' => 'if(isNaN(this.value)){this.value=""}', 'minlength' => '10', 'maxlength' => '10', 'placeholder' => 'Contact Number']) }}

                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('office_email', 'TKA Email', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::email('office_email', $employee->office_email, ['class' => 'form-control', 'placeholder' => 'Enter TKA Email']) }}

                                    @error('office_email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('personal_email', 'Personal Email', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::email('personal_email', null, ['class' => 'form-control', 'placeholder' => 'Personal Email']) }}
                                    @error('personal_email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('employee_dob', 'D-O-B', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('birth_date', $employee->birth_date, ['class' => 'form-control', 'placeholder' => 'choose birth date']) }}

                                    @error('birth_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('join_date', 'Joining Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('join_date', $employee->join_date, ['class' => 'form-control', 'placeholder' => 'choose joining date']) }}

                                    @error('join_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('onboard_status', 'Onboard Status', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('onboard_status', ['Training' => 'Training', 'Onboard' => 'Onboard'], null, ['class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}

                                    @error('onboard_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('registration_id', 'Registration Id', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('registration_id', null, ['class' => 'form-control', 'placeholder' => 'Registration Id']) }}

                                    @error('registration_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('biometric_id', 'Biometric Id', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('biometric_id', null, ['class' => 'form-control', 'placeholder' => 'Biometric Id']) }}

                                    @error('biometric_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('qualification_id', 'Select Qualification', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('qualification_id', $list['qualification'], null, ['class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}

                                    @error('qualification')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('designation', 'Designation', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {!! Form::select('designation_id', $designations, null, ['class'=>'form-control selectJS', 'placeholder' => 'Select an option']) !!}

                                    @error('designation')
                                      <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('off_day', 'Select Off Day', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('off_day', $days, $employee->user->off_day ?? null, ['class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}

                                    @error('off Day')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('user_type', 'Select Type', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('user_type', $userTypes, $employee->user->user_type ?? null, ['class' => 'form-control selectJS', 'placeholder' => 'Choose one']) }}

                                    @error('user_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('pf_no', 'PF Number', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('pf_no', null, ['class' => 'form-control', 'placeholder' => 'PF Number']) }}

                                    @error('pf_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('profile_pic', 'Profile Picture', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <input type="file" name="profile_pic" accept="image/jpeg,image,jpg,image/png"
                                        class="form-control">
                                    @error('profile_pic')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if (!empty($employee->user->profile_pic))
                                    <img src="{{ $employee->user->user_image }}" width="70" height="70">
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">Is Active
                                        <input type="checkbox" name="is_active" @if($employee->is_active)
                                         checked @endif class="form-check-input">
                                    </label>
                                    @error('is_active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('aadhaar_number','Adhaar Number', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('aadhaar_number',$employee->documents->aadhaar_number ?? null,['class'=>'form-control', 'placeholder'=>'XXXX XXXX XXXX'])}}

                                    @error('aadhaar_number')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('aadhaar_file','Adhaar Upload', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <div class="col-9 float-left">
                                        <input type="file" name="aadhaar_file" class="form-control" />
                                    </div>
                                    @if (!empty($employee->documents->aadhaar_file))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->aadhaar_file])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @error('aadhaar_file')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('pan_number','Pan Number', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('pan_number',$employee->documents->pan_number ?? null,['class'=>'form-control', 'placeholder'=>'XXXX XXXX XXXX'])}}

                                    @error('pan_number')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('pan_file','Pan Upload', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <div class="col-9 float-left">
                                        <input type="file" name="pan_file" class="form-control" accept="pdf" />
                                    </div>
                                    @if (!empty($employee->documents->pan_file))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->pan_file])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @error('pan_file')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('contract_date', 'Contract Sign Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('contract_date', $employee->contract_date, ['class' => 'form-control', 'placeholder' => 'Choose Contract Date']) }}

                                    @error('contract_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('second_contract_date', 'Second Contract Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('second_contract_date', $employee->second_contract_date, ['class' => 'form-control', 'placeholder' => 'Second Contract Date']) }}

                                    @error('second_contract_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('cv','CV Upload', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <div class="col-9 float-left">
                                        <input type="file" name="cv" class="form-control" accept="pdf" />
                                    </div>
                                    @if (!empty($employee->documents->cv))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cv])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @error('cv')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('shift_type_id', 'Shift Type', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <select name="shift_type_id" class="form-control selectJS" placeholder="Select Shift Type" data-placeholder="Select Shift Type">
                                        @foreach ($shifts as $shiftType)
                                        <option value="{{ $shiftType->id}}" @if(!empty($employee->user) && $employee->user->shift_type_id == $shiftType->id) selected @endif>
                                            {{$shiftType->name.' ('.$shiftType->start_time.' - '.$shiftType->end_time.')'}}</option>
                                        @endforeach
                                    </select>
                                    {{-- {{ Form::select('shift_type_id', $shifts, $employee->user->shift_type_id ?? '', ['class' => 'form-control selectJS','placeholder' =>'Select Shift Type' , 'data-placeholder'=>'Select Shift Type']) }} --}}

                                </div>
                                @error('shift_type_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('gender', 'Gender',['class' => 'col-sm-3 col-form-label']) }}
                                <div class="mt-3 ml-5">
                                    {!! Form::radio('gender', 'Male', !empty($employee->gender) && $employee->gender == 'Male' ? 1 : null, ['class' => 'mr-1']) !!}
                                    <label class="mr-5">Male</label>
                                    {!! Form::radio('gender', 'Female', !empty($employee->gender) && $employee->gender == 'Female' ? 1 : null, ['class' => 'mr-1']) !!}
                                    <label class="mr-5">Female</label>
                                </div>
                                @error('gender')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('asset_policy','Asset Policy', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                <div class="col-9 float-left">
                                    <input type="file" name="asset_policy" accept="pdf" />
                                </div>
                                @if (!empty($employee->documents->asset_policy))
                                <div class="col-3 float-right text-right">
                                    <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->asset_policy])}}">
                                        <i class="fa fa-eye text-primary"></i>
                                    </a>
                                </div>
                                @endif
                                @error('asset_policy')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <div class="form-check form-check-primary">
                                <label class="form-check-label">Is Power User
                                    <input type="checkbox" name="is_power_user" @if($employee->is_power_user)
                                     checked @endif class="form-check-input">
                                </label>
                                @error('is_power_user')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('bank_details_form','Bank Details',['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::checkbox('bank_details_form',null,false,['class'=>'from-control mt-3 show-bank-details'])}}
                                </div>
                            </div>
                        </div>

         


                        <div class="col-md-12 mb-1 bank-form" style="border-top:1px solid #ced4da;display:none">
                            <span class="card-title">Bank Details:</span>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('account_holder', 'Account Holder', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::text('account_holder', $employee->bankdetail->account_holder ?? '', ['class' => 'form-control', 'placeholder' => 'Account Holder']) }}

                                            @error('account_holder')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group row">

                                        {{ Form::label('bank_name', 'Bank Name', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::text('bank_name', $employee->bankdetail->bank_name ?? '', ['class' => 'form-control', 'placeholder' => 'Bank Name']) }}

                                            @error('bank_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">

                                        {{ Form::label('account_no', 'Account Number', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::number('account_no', $employee->bankdetail->account_no ?? '', ['class' => 'form-control', 'placeholder' => 'Account Number']) }}

                                            @error('account_no')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">

                                        {{ Form::label('ifsc_code', 'IFSC Code', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::text('ifsc_code', $employee->bankdetail->ifsc_code ?? '', ['class' => 'form-control', 'placeholder' => 'IFSC Code']) }}

                                            @error('ifsc_code')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{Form::label('cheque','Cancel Cheque/ Passbook', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            <div class="col-9 float-left">
                                                <input type="file" name="cheque" accept="pdf" />
                                            </div>
                                            @if (!empty($employee->documents->cheque))
                                            <div class="col-3 float-right text-right">
                                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cheque])}}">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a>
                                            </div>
                                            @endif
                                            @error('cheque')
                                                <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>


                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>


                        {{Form::close()}}
                    </div>
                </div>

            </div>



        </div>
    </div>
    <div class="row ">
      @can('hrImportEmployee', new App\Models\Employee())
        <div class="col-12 grid-margin">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Bulk Import Employee Sheet</h4>
              <form action="{{ route('importEmployee') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="file" name="file" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <button class="btn btn-warning float-right">Upload</button>
                        </div>
                    </div>
                </div>

              </form>
            </div>
          </div>
        </div>
        @endcan
    </div>
    <!-- /.row -->

@endsection
@section('footerScripts')
@if(!empty($employee->bankdetail->bank_name))
<script>
    $('.show-bank-details').prop('checked', true);
    $('.bank-form').show();

</script>
@endif
    <script>
        $('.show-bank-details').click(function () {
        if ($(this).prop('checked')) {
            $('.bank-form').fadeIn('slow');
        } else {
            $('.bank-form').fadeOut('slow');
        }
    });
        $('#add_document').click(function() {
            var count = $('#documents').find('div').length;
            var html =
                '<div class="row form-group">\
                        <span class="col-sm-1 text-center">#</span>\
                        <input type="text" placeholder="Enter Document Name"  maxlength="100" minlength="2" style="border:1px solid grey" class="form-control-sm mr-3 col-md-4" name="documents[' +
                (count + 1) + '][category]" required>\
                        <select class="form-control-sm mr-3 col-md-2" name="documents[' + (count + 1) + '][document_type]" >\
                        <option value="" disabled>Document Type</option>\
                        <option value="office">Office</option>\
                        <option value="personal">Personal</option>\
                        </select>\
                        <input class="col-sm-3" accept="application/pdf" placeholder="Enter Detail" name="documents[' + (
                    count + 1) + '][document]" type="file" required>\
                        <button  style="margin-top:-10px" type="button" class="removeDocument btn"><i class="fa fa-trash text-danger"></i></button>\
                    </div>';
            $('#documents').append(html);
            $('#documents').show();
        });
        $("#documents").on('click', '.removeDocument.btn', function() {
            $(this).closest('div.row.form-group').remove();
            var count = $('#documents').find('div.row.form-group').length;
            if (count == 0)
                $('#documents').hide();
            else
                $('#documents').show();
        });


        $('.show-bank-details').click(function() {
            if ($(this).prop('checked')) {
                $('.bank-form').fadeIn('slow');
            } else {
                $('.bank-form').fadeOut('slow');
            }
        });


        function deleteDocument(path) {
            //module = module.toLowerCase();
            if (event) {
                event.preventDefault();
            }
            var sure = confirm('are you sure');
            if (!sure) {
                return false;
            }

            $.ajax({
                url: path,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                },
                dataType: 'html',
                success: function(response) {
                    location.reload();
                },
                error: function(response) {
                    if (response.status == '404') {
                        alert("Item not found");
                    } else
                        alert(response.statusText);
                }
            });
            return true;
        }
        @error('account_holder')

            $('.bank-form').show();

        @enderror

        @error('bank_name')

            $('.bank-form').show();

        @enderror

        @if (!empty($employee->bankdetail->bank_name))

            $('.show-bank-details').prop('checked', true);
            $('.bank-form').show();

        @endif

    </script>
@endsection
