@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-8 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                    <div class="card-title">Leave Balance Form
                        <div class="col-md-4 float-right">
                            <form action="" method="GET">
                                <input type="hidden" name="month" value="{{ $leaveBalance->month }}">
                                <select style='width:100%;' name="user_id" data-placeholder="select an option"
                                    placeholder="select an option" class='selectJS' onchange="this.form.submit();">
                                    @foreach ($userDepartments as $department => $users)
                                        <optgroup label="{{ $department }}">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    @if ($user->id == request()->user_id || $user->id == $leaveBalance->user_id) selected @endif>
                                                    {{ $user->name . ' (' . ($user->employee->biometric_id ?? '') . ')' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    {{ Form::model($leaveBalance, ['route' => $submitRoute, 'method' => $method]) }}
                    <div class=" mt-5">

                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('name', $leaveBalance->user->name ?? '', [
                                        'class' => 'form-control',
                                        'placeholder' => 'Select Type',
                                        'readonly' => 'readonly',
                                        'data-placeholder' => 'Enter Balance',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('month', 'Month', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('month', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Select Type',
                                        'readonly' => 'readonly',
                                        'data-placeholder' => 'Enter Balance',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('balance', 'Balance', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('balance', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Select Type',
                                        'data-placeholder' => 'Enter Balance',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('absent', 'Absent', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {{ Form::text('absent', null, ['class' => 'form-control', 'placeholder' => 'Absent']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">1-20 {{$now->format("M")}} Leaves</label>
                                <div class="col-sm-9">
                                    <input type="text" value="{{$beforeCutOffLeaves}}"  class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">21-{{$now->endOfMonth()->format("d")}} {{$now->format("M")}} Leaves</label>
                                <div class="col-sm-9">
                                    <input type="text" value="{{$afterCutOffLeaves}}" class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('after_cut_off', 'Taken Leaves After Cutoff', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('after_cut_off', $leaveBalance->leaves_after_cut_off, [
                                        'class' => 'form-control',
                                        'disabled' => 'disabled',
                                    ]) !!}
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('sundays', 'Total Sundays', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('sundays', $leaveBalance->total_sundays, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('deduction', 'Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {{ Form::text('deduction', null, ['class' => 'form-control', 'placeholder' => 'Deduction']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('pre_approval_deduction', 'Pre Approval Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                <div class="col-sm-9">
                                    {{ Form::text('pre_approval_deduction', null, ['class' => 'form-control', 'placeholder' => 'Pre Approval Deduction']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label(
                                    'prev_month_deduction','Prev Month Cutoff Deduction',
                                    ['class' => 'col-sm-3 col-form-label'],
                                ) !!}
                                <div class="col-sm-9">
                                    {{ Form::text('prev_month_deduction', null, ['class' => 'form-control', 'placeholder' => 'Prev Month Deduction']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label(
                                    'next_month_deduction','Next Month Cutoff Deduction',
                                    ['class' => 'col-sm-3 col-form-label'],
                                ) !!}
                                <div class="col-sm-9">
                                    {{ Form::text('next_month_deduction', null, ['class' => 'form-control', 'placeholder' => 'Next Month Deduction']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 pull-right">
            <div class="card">
                <h3 class="card-title m-3">Comments</h3>
                <div class="card-body" style="height:18rem;overflow:auto;">

                    @foreach ($leaveBalance->leaveBalanceComplaints as $comment)
                        @php
                            $position = 'justify-content-start';
                            $position2 = 'pull-left';
                            if (auth()->user()->id == $comment->user_id) {
                                $position = 'justify-content-end';
                                $position2 = 'float-lg-right';
                            }
                        @endphp
                        {{-- <span class="pull-right">{{$comment->user->name}}</span> --}}
                        <span @if(auth()->user()->hasRole('HR') && ($comment->user->id == auth()->user()->name)) class="float-lg-right" @else class="{{ $position2 }}" @endif> {{ $comment->user->name ?? '' }}</span>
                        <br>
                        <div class="d-flex flex-row  mb-4 {{ $position }}">
                            <div class="p-3 ms-3" style="border-radius: 15px; background-color: rgba(57, 192, 237,.2);">

                                <p class="small mb-0">{{ $comment->description }}</p>
                                <span style="font-size: 10px" class="pull-right">({{ getDateTime($comment->created_at) }}
                                    )</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <div class="form-outline">
                        <form action="{{ route('leaveBalanceComplaintRaise') }}" method="post">
                            @csrf
                            <div class="input-group">
                                <input type="hidden" name="leave_balance_id" value="{{ $leaveBalance->id }}">
                                <div class="col-9">
                                    <input type="text" required name="description" placeholder="Type Message ..."
                                        class="form-control" spellcheck="false" data-ms-editor="true">
                                </div>
                                <div class="col-1 ">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
