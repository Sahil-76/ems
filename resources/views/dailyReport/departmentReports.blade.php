@extends('layouts.master')
@section('content')
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
        }

        td {
            vertical-align: top;
        }

    </style>

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Daily Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row  mb-3">
        <div class="col-12">
            <div class="card">
                {{ Form::open(['method' => 'GET']) }}
                <div class="card-body">
                    <p class="card-title">Filter</p>
                    <div class="form-group row">
                        {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('department_id', $departments, request()->department_id, ['class' => 'form-control selectJS department', 'placeholder' => 'Select your department']) }}
                        </div>

                        {{ Form::label('report_date', 'Select Date', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::date('date', empty(request()->date) ? $today : request()->date, ['class' => 'form-control date', 'placeholder' => 'choose from date']) }}
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            {{ Form::submit('Filter', ['class' => 'btn btn-primary']) }}
                            <a href="{{ request()->url() }}" class="btn btn-success">Clear Filter</a>
                            {{ Form::close() }}
                        </div>
                        <div class="col-md-4 text-sm-right mt-1">
                            <a href="{{route('dailyReport.exportDailyReport',request()->query())}}" class="btn btn-danger">Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title float-left">Daily Reports</p>
                    <strong
                        class="float-right">{{ empty(request()->report_date) ? getFormatedDate($today) : getFormatedDate(request()->report_date) }}</strong>
                    <br><br>
                    <div class="table-responsive">
                        <table class="table table-striped">

                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Leave Session</th>
                                    <th>Task 1</th>
                                    <th>Task 2</th>
                                    <th>Task 3</th>
                                    <th>Task 4</th>
                                    <th>Task 5</th>
                                    <th>Task 6</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($users->isEmpty())
                                <tr>
                                    <td colspan="8">
                                        <marquee behavior="alternate" direction="right"> No data available</marquee>
                                    </td>
                                </tr>
                                @else
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->employee->department->name }}</td>
                                        @if($user->leaves->count() > 1)
                                            <td>Full Day</td>
                                        @else
                                            <td>{{ optional($user->leaves)->first()->leave_session ?? 'Present'}}</td>
                                        @endif
                                        <td style="white-space: normal;">{{ optional($user->workReports)->first()->task1 ?? '' }}</td>
                                        <td style="white-space: normal;">{{ optional($user->workReports)->first()->task2 ?? '' }}</td>
                                        <td style="white-space: normal;">{{ optional($user->workReports)->first()->task3 ?? '' }}</td>
                                        <td style="white-space: normal;">{{ optional($user->workReports)->first()->task4 ?? '' }}</td>
                                        <td style="white-space: normal;">{{ optional($user->workReports)->first()->task5 ?? '' }}</td>
                                        <td style="white-space: normal;">{{ optional($user->workReports)->first()->task6 ?? '' }}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6 mt-3 float-md-left">
                           <strong>Total Results: </strong> {{$users->total()}}
                        </div>
                        <div class="col-sm-6 mt-3">
                            <div class="float-md-right">
                                {{$users->appends(request()->query())->links()}}
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('footerScripts')
    <script>
        $('.department').bind('click change',function(){
            $('#excel_department').val($(this).val());
        });
        $('.date').bind('click change',function(){
            $('#excel_date').val($(this).val());
        });
    </script>

@endsection
