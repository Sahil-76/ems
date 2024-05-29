@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Employee</li>
            </ol>
        </nav>
    </div>

    <div class="col-12 grid-margin">
        <div class="card">
            {{Form::model($employee,array('route'=>$submitRoute,"files"=>"true"))}}
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <h4 class="card-title">Employee Profile Update</h4>
                    </div>
                    <div class="col-9 text-right">
                       <span class="text-danger"> * All Files must be type of pdf except Profile Picture </span>

                    </div>
                </div>

                {{Form::hidden('id',null)}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('name','Employee Name', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::text('name',null,['class'=>'form-control','id'=>'name','placeholder'=>'Employee Name'])}}
                                @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('phone','Contact Number', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::text('phone',null,['class'=>'form-control','onkeyup'=>'if(isNaN(this.value)){this.value=""}','disabled'=>draft_check($employee->id,'phone'),'minlength'=>'10','maxlength'=>'10','placeholder'=>'Contact Number', 'required' => 'required'])}}
                                @if(draft_check($employee->id,'phone'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('phone')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('personal_email','Personal Email Id', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::email('personal_email',null,['class'=>'form-control','disabled'=>draft_check($employee->id,'personal_email'),'placeholder'=>'Personal Email', 'required' => 'required'])}}
                                @if(draft_check($employee->id,'personal_email'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('personal_email')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('birth_date','D-O-B', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::date('birth_date',$employee->birth_date,['class'=>'form-control','disabled'=>draft_check($employee->id,'birth_date'),'placeholder'=>'choose birth date', 'required' => 'required'])}}
                                @if(draft_check($employee->id,'birth_date'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('birth_date')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('qualification_id','Select Qualification', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::select('qualification_id',$list['qualification'],null,['class'=>'selectJS col-12','disabled'=>draft_check($employee->id,'qualification_id'), 'placeholder'=>'Choose one', 'required' => 'required'])}}
                                @if(draft_check($employee->id,'qualification_id'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('qualification')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('profile_pic','Profile Picture', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                <img class="mr-3" src="{{ $employee->user->getImagePath() }}" width="70" height="70">
                                @if(!empty($employee->user->profile_pic))
                                    {{Form::File('profile_pic',['accept'=>'image/jpeg,image,jpg,image/png','disabled'=>draft_profile_check($employee->id,'profile_pic')])}}
                                @else
                                    {{Form::File('profile_pic',['accept'=>'image/jpeg,image,jpg,image/png','disabled'=>draft_check($employee->id,'profile_pic'), 'required' => 'required'])}}
                                @endif
                                @if(draft_check($employee->id,'profile_pic'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('profile_pic')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('aadhaar_number','Adhaar Number', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::text('aadhaar_number',$employee->documents->aadhaar_number ?? null,['class'=>'form-control','disabled'=>draft_check($employee->id,'aadhaar_number'), 'placeholder'=>'XXXX XXXX XXXX', 'required' => 'required'])}}
                                @if(draft_check($employee->id,'aadhaar_number'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
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
                                    @if(!empty($employee->documents->aadhaar_file))
                                        {{Form::file('aadhaar_file',['disabled'=>draft_check($employee->id,'aadhaar_file'),"accept"=>"application/pdf"])}}
                                    @else
                                        {{Form::file('aadhaar_file',['disabled'=>draft_check($employee->id,'aadhaar_file'), 'required' => 'required',"accept"=>"application/pdf"])}}
                                    @endif
                                </div>
                                @if (!empty($employee->documents->aadhaar_file))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->aadhaar_file])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                @endif
                                @if(draft_check($employee->id,'aadhaar_file'))
                                    <p style="color:red;"> Sent for Approval</p>
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
                                {{ Form::text('pan_number',$employee->documents->pan_number ?? null,['class'=>'form-control','disabled'=>draft_check($employee->id,'pan_number'), 'placeholder'=>'XXXX XXXX XXXX'])}}
                                @if(draft_check($employee->id,'pan_number'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
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
                                    {{Form::file('pan_file',['accept'=>'pdf','disabled'=>draft_check($employee->id,'pan_file'),"accept"=>"application/pdf"])}}
                                </div>
                                @if (!empty($employee->documents->pan_file))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->pan_file])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                @endif
                                @if(draft_check($employee->id,'pan_file'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('pan_file')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('join_date', 'Joining Date', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::date('join_date', $employee->join_date, ['class' => 'form-control', 'disabled'=>draft_check($employee->id,'join_date'), 'placeholder' => 'choose joining date']) }}
                                @if(draft_check($employee->id,'join_date'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('join_date')
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
                                    @if(!empty($employee->documents->cv))
                                        {{Form::file('cv',['accept'=>'pdf','disabled'=>draft_check($employee->id,'cv'),"accept"=>"application/pdf"])}}
                                    @else
                                        {{Form::file('cv',['accept'=>'pdf','disabled'=>draft_check($employee->id,'cv'), 'required' => 'required',"accept"=>"application/pdf"])}}
                                    @endif
                                </div>
                                @if (!empty($employee->documents->cv))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cv])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                @endif
                                @if(draft_check($employee->id,'cv'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('cv')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('contract_date', 'Contract Sign Date', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::date('contract_date', $employee->contract_date, ['class' => 'form-control', 'placeholder' => 'Choose Contract Date','readonly'=>'readonly']) }}
                                @error('contract_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('passport','Passport', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                <div class="col-9 float-left">
                                    @if(!empty($employee->documents->passport))
                                        {{Form::file('passport',['accept'=>'pdf','disabled'=>draft_check($employee->id,'passport'),"accept"=>"application/pdf"])}}
                                    @else
                                        {{Form::file('passport',['accept'=>'pdf','disabled'=>draft_check($employee->id,'passport'),"accept"=>"application/pdf"])}}
                                    @endif
                                </div>
                                @if (!empty($employee->documents->passport))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->passport])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                @endif
                                @if(draft_check($employee->id,'passport'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('passport')
                                    <span class="text-danger">{{$message}}</span>
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
                            {{ Form::label('biometric_id', 'Biometric Id', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::text('biometric_id', null, ['class' => 'form-control', 'placeholder' => 'Biometric Id','readonly'=>'readonly']) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('shift_type_id', 'Shift Type', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::text('shift_type_id',$employee->Shift, ['class' => 'form-control selectJS','placeholder' =>'Select Shift Type' , 'data-placeholder'=>'Select Shift Type','readonly'=>'readonly']) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('video_file', 'Video File', ['class' => 'col-sm-3 col-form-label'])}}
                            <div class="col-sm-9">
                                <div class="col-9 float-left">
                                    @if(!empty($employee->documents->video_file))
                                        {{Form::file('video_file', ['accept' => 'video/*', 'disabled' => draft_check($employee->id, 'video_file')])}}
                                    @else
                                        {{Form::file('video_file', ['accept' => 'video/*', 'disabled' => draft_check($employee->id, 'video_file')])}}
                                    @endif
                                </div>
                                @if (!empty($employee->documents->video_file))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->video_file])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                @endif
                                @if(draft_check($employee->id, 'video_file'))
                                    <p style="color:red;">Sent for Approval</p>
                                @endif
                                @error('video_file')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('gender', 'Gender',['class' => 'col-sm-3 col-form-label']) }}
                            <div class="mt-3 ml-4">
                                {!! Form::radio('gender', 'Male', !empty($employee->gender) && $employee->gender == 'Male' ? 1 : null, ['class' => 'mr-1']) !!}
                                <label class="mr-5">Male</label>
                                {!! Form::radio('gender', 'Female', !empty($employee->gender) && $employee->gender == 'Female' ? 1 : null, ['class' => 'mr-1']) !!}
                                <label class="mr-5">Female</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{Form::label('asset_policy','Asset Policy', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                <div class="col-9 float-left">
                                    @if(!empty($employee->documents->asset_policy))
                                        {{Form::file('asset_policy',['disabled'=>draft_check($employee->id,'asset_policy'),"accept"=>"application/pdf"])}}
                                    @else
                                        {{Form::file('asset_policy',['disabled'=>draft_check($employee->id,'asset_policy'),"accept"=>"application/pdf"])}}
                                    @endif
                                </div>
                                @if (!empty($employee->documents->asset_policy))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->asset_policy])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                @endif
                                @if(draft_check($employee->id,'asset_policy'))
                                    <p style="color:red;"> Sent for Approval</p>
                                @endif
                                @error('asset_policy')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-1 bank-form" style="border-top:1px solid #ced4da">
                        <span class="card-title">Bank Details:</span>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('account_holder','Account Holder', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::text('account_holder',$employee->bankdetail->account_holder ?? null,['class'=>'form-control','disabled'=>draft_check($employee->id,'account_holder'),'placeholder'=>'Account Holder'])}}
                                        @if(draft_check($employee->id,'account_holder'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('account_holder')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('bank_name','Bank Name', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::text('bank_name',$employee->bankdetail->bank_name ?? '',['class'=>'form-control','disabled'=>draft_check($employee->id,'bank_name'),'placeholder'=>'Bank Name'])}}
                                        @if(draft_check($employee->id,'bank_name'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('bank_name')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('account_no','Account Number', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::number('account_no',$employee->bankdetail->account_no ?? null,['class'=>'form-control','disabled'=>draft_check($employee->id,'account_no'),'placeholder'=>'Account Number'])}}
                                        @if(draft_check($employee->id,'account_no'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('account_no')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('ifsc_code','IFSC Code', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::text('ifsc_code',$employee->bankdetail->ifsc_code ?? '',['class'=>'form-control','disabled'=>draft_check($employee->id,'ifsc_code'),'placeholder'=>'IFSC Code'])}}
                                        @if(draft_check($employee->id,'ifsc_code'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('ifsc_code')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('cheque','Cancel Cheque/ Passbook', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        <div class="col-9 float-left">
                                            @if(!empty($employee->documents->cheque))
                                                {{Form::file('cheque',['disabled'=>draft_check($employee->id,'cheque'),"accept"=>"application/pdf"])}}
                                            @else
                                                {{Form::file('cheque',['disabled'=>draft_check($employee->id,'cheque'),"accept"=>"application/pdf"])}}
                                            @endif
                                        </div>
                                        @if (!empty($employee->documents->cheque))
                                            <div class="col-3 float-right text-right">
                                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cheque])}}">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a>
                                            </div>
                                        @endif
                                        @if(draft_check($employee->id,'cheque'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('cheque')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-1 bank-form" style="border-top:1px solid #ced4da">
                        <span class="card-title">Emergency Contact Details:</span>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('person_name','Name of the person', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::text('person_name',$employee->employeeEmergencyContact->person_name ?? null,['class'=>'form-control','disabled'=>draft_check($employee->id,'person_name'),'placeholder'=>'Contact Person Name', 'required' => 'required'])}}
                                        @if(draft_check($employee->id,'person_name'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('person_name')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('person_relation','Relation with employee', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::select('person_relation', $person_relations, $employee->employeeEmergencyContact->person_relation ?? '',['class'=>'form-control selectJS','disabled'=>draft_check($employee->id,'person_relation'), 'required' => 'required'])}}
                                        @if(draft_check($employee->id,'person_relation'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('person_relation')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('person_contact','Contact number', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::number('person_contact',$employee->employeeEmergencyContact->person_contact ?? null,['class'=>'form-control','disabled'=>draft_check($employee->id,'person_contact'),'placeholder'=>'Contact Number', 'required' => 'required'])}}
                                        @if(draft_check($employee->id,'person_contact'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('person_contact')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('person_address','Address', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::text('person_address',$employee->employeeEmergencyContact->person_address ?? '',['class'=>'form-control','disabled'=>draft_check($employee->id,'person_address'),'placeholder'=>'Address', 'required' => 'required'])}}
                                        @if(draft_check($employee->id,'person_address'))
                                            <p style="color:red;"> Sent for Approval</p>
                                        @endif
                                        @error('person_address')
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
@endsection

@section('footerScripts')
<script src="{{ url('js/scanner.js') }}"></script>
<script>
    var url = "{{ route('assignEquipments') }}";

    $(document).scannerDetection({
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
        preventDefault: true
        , endChar: [13]
        , onComplete: function(barcode, qty) {
            validScan = true;
            window.open(url+"?id="+barcode,'_blank');
        }
        , onError: function(string, qty) {
            res = string.split("-");
            var inward_id = res[0];
            var per_id = res[2];
        }
    });

</script>
@endsection
