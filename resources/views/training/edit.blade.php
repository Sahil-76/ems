@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-4 mt-2 ml-4">
            <h4><strong>{{ $trainingEmployee->user->name }}</strong></h4>
        </div>
        <div class="col-7 ml-5 ">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('training.index') }}">Training</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Training Progress</li>
                </ol>
            </nav>
        </div>
    </div>

    {{ Form::open(['route' => $submitRoute, 'method' => $method]) }}

    <div class="col-md-12">
        <div class="card card-primary card-outline p-3">
            <div class="card-body">
                <div class="form-group row">
                    {{ Form::label('trainer_id', 'Select Trainer', ['class' => 'col-sm-2 col-form-label'])}}
                    <div class="col-sm-3">
                        {{ Form::select('trainer_id', $trainers, $trainingEmployee->trainer_id ?? null, ['class'=>'form-control selectJS', 'placeholder' => 'Select Trainer'])}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 mt-3">
        <div class="card card-primary card-outline p-3">
            <h3 class="card-title m-2">Basic Tasks:</h3>
            <div class="card-body">
                <div class='form-group row'>
                    @foreach($basicTasks as $task)
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type='checkbox' class='mr-2' name='tasks[]' value="{{ $task->id }}" @if(in_array($task->id,$userTasks)) checked @endif >{{ $task->name }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if($departmentalTasks->isNotEmpty())
    <div class="col-md-12 mt-3">
        <div class="card card-primary card-outline p-3">
            <h3 class="card-title m-2">Departmental Tasks:</h3>
            <div class="card-body">
                <div class='form-group row'>
                    @foreach($departmentalTasks as $task)
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type='checkbox' class='mr-2' name='tasks[]' value="{{ $task->id }}" @if(in_array($task->id,$userTasks)) checked @endif >{{ $task->name }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="m-3">
        <div class="row">
            <div class="col-md-6">    
                @if(auth()->user()->employee->id == $manager_id || auth()->user()->hasRole('Admin'))
                <button type="submit" class="btn btn-primary">Save</button>
                @endif
            </div>
            @can('hrEmployeeList', new App\Models\Employee())
                <div class="col-md-6 text-right">
                    <a class="btn btn-success" onclick="checkResult('pass',{{$trainingEmployee->id}})">Pass</a>
                    <a class="btn btn-danger" onclick="checkResult('fail',{{$trainingEmployee->id}})">Fail</a>
                </div>
            @endcan
        </div>
    </div>
    
    {{ Form::close() }}
@endsection

@section('footerScripts')
    <script>
        function checkResult(val,id)
        {
            $.ajax({
                url: "{!! route('updateOnboardStatus') !!}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "id": id,
                    "result":val
                },
                success: function (response) {
                    window.location.href = "{{ route('training.index') }}";
                    toastr.success('Status Updated');
                }
            });
        }
    </script>
@endsection
