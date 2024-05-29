@extends('layouts.master')
@section('content')
             
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{route('draftList')}}">Draft Profile List</a>
                </li>
                <li class="breadcrumb-item active">Draft Profile</li>
            </ol>
        </nav>
    </div>
</div>
    <div class="row">
        @foreach($drafts as $draft)
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body gallery">
                    <p class="card-title">Employee : {{$employee->name}}@if(!empty($employee->registration_id)) ({{$employee->registration_id}}) @endif</p>
                  
                    {{Form::hidden('id', null)}}

                    <div class="col-md-12">
                        <div class="form-group row">
                            {{Form::label('submitted', "Submitted: ",['class'=>'col-sm-3 col-form-label'])}}
                            <div class="col-sm-9">
                                <input type="text" class="form-control"  value="{{$draft->updated_at->format('j F, Y')}} ({{$draft->created_at->diffForHumans()}})" disabled/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            {{Form::label('old_value', "Current Value: ",['class'=>'col-sm-3 col-form-label'])}}
                            <div class="col-sm-9">
                                @php $field = $draft->field_name; @endphp
                                @if (!empty($draft->is_file) && !empty($employee->documents->$field))
                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->$field])}}" type="button" class="btn btn-danger btn-icon-text">
                                    <i class="ti-file btn-icon-prepend"></i>                                                    
                                    Preview
                                </a>
                                @elseif(!empty($draft->is_file) && $draft->field_name=='profile_pic')
                                <a target="_blank" href="{{$employee->user->user_image}}" type="button" class="employee-image">                                                    
                                    <img src="{{$employee->user->user_image}}" height="80px" width="80px" alt="">
                                </a>
                                @elseif(in_array($draft->field_name,['account_no', 'bank_name', 'account_holder', 'ifsc_code']))
                                <input type="text" class="form-control"  value="{{$employee->bankdetail->$field ?? 'N/A'}}" disabled/>
                                @elseif(in_array($draft->field_name,['person_name', 'person_relation', 'person_contact', 'person_address']))
                                <input type="text" class="form-control"  value="{{$employee->employeeEmergencyContact->$field ?? 'N/A'}}" disabled/>
                                @elseif($field == 'qualification_id')
                                <input type="text" class="form-control"  value="{{getQualificationName($employee->$field) ?? 'N/A'}}" disabled/>
                                @elseif($field == 'birth_date' || $field == 'join_date')
                                <input type="text" class="form-control"  value="{{getFormatedDate($employee->$field)}}" disabled/>
                                @else
                                <input type="text" class="form-control"  value="{{$employee->$field ?? 'N/A'}}" disabled/>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            {{Form::label($draft->field_name, "Draft ".ucwords(str_replace('_',' ',$draft->field_name)).": ",['class'=>'col-sm-3 col-form-label'])}}
                            <div class="col-sm-9">
                                @if (!empty($draft->is_file) && $draft->field_name!='profile_pic')
                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $draft->field_value])}}" type="button" class="btn btn-danger btn-icon-text">
                                    <i class="ti-file btn-icon-prepend"></i>                                                    
                                    Preview
                                </a>
                                @elseif(!empty($draft->is_file) && $draft->field_name=='profile_pic')
                                <a target="_blank" href="{{url('employee/picture/'.$draft->field_value)}}" type="button" class="employee-image">
                                    <img src="{{url('employee/picture/'.$draft->field_value)}}" height="80px" width="80px" alt="">
                                </a>
                                @elseif($field == 'qualification_id')
                                <input type="text" class="form-control"  value="{{getQualificationName($draft->field_value)}}" disabled/>
                                @elseif($field == 'birth_date' || $field == 'join_date')
                                <input type="text" class="form-control"  value="{{getFormatedDate($draft->field_value)}}" disabled/>
                                @else
                                <input type="text" class="form-control"  value="{{$draft->field_value}}" disabled/>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            {{Form::label('comment', "Remarks: ",['class'=>'col-sm-3 col-form-label'])}}
                            <div class="col-sm-9">
                                <textarea id="{{$draft->id}}" class="form-control" placeholder="Optional" name="comment" value=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="action {{$draft->id}} btn btn-primary btn-rounded btn-fw mr-5" onclick="action({{$employee->id}}, {{$draft->id}}, true,{{$employee->user->id}})">Approve</button>
                        <button type="submit" class="action {{$draft->id}} btn btn-danger btn-rounded btn-fw" onclick="action({{$employee->id}}, {{$draft->id}}, false,{{$employee->user->id}})">Reject</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
    
@endsection
@section('footerScripts')
<script>
    function action(employee_id, draft_id, is_approved,user_id)
    {   
        $('.'+draft_id).attr('disabled',true);
        var target = event.target;
        var comments=$('#'+draft_id).val();
        var url = "{{route('draftAction')}}";
        $.ajax({
            url: url,
            type: 'post',
            headers: { 'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content') },
            data: {employee: employee_id, draft: draft_id, is_approved: is_approved,user_id:user_id,comment:comments},
            success: function(response){
                if(response.status == 'approved')
                {
                    toastr.success('Draft: Approved');
                    location.reload();
                }
                else if(response.status == 'rejected')
                {
                    toastr.info('Draft: Rejected');
                    location.reload();
                }
                $(target).closest('.col-md-4').remove();
            },

        })
    }
</script>
@endsection
