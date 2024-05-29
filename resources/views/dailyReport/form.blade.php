@extends('layouts.master')
@section('headerLinks')
    <style>
        .toggle-sound {
            position: fixed;
            top: calc(35% - 25.5px);
            left: calc(90% - 25.5px);
            background-color: #EC407A;
            width: 54px;
            height: 53px;
            line-height: 55px;
            text-align: center;
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
            z-index: 99;
            animation: pulse 1.25s infinite cubic-bezier(0.66, 0, 0, 1);
            box-shadow: 0 0 0 0 #F06292;
        }

        .toggle-sound.sound-mute {
            box-shadow: none;
        }

        @-webkit-keyframes pulse {
            to {
                box-shadow: 0 0 0 45px rgba(232, 76, 61, 0);
            }
        }

        @-moz-keyframes pulse {
            to {
                box-shadow: 0 0 0 45px rgba(232, 76, 61, 0);
            }
        }

        @-ms-keyframes pulse {
            to {
                box-shadow: 0 0 0 45px rgba(232, 76, 61, 0);
            }
        }

        @keyframes pulse {
            to {
                box-shadow: 0 0 0 45px rgba(232, 76, 61, 0);
            }
        }

        .sound {
            width: 97%;
            height: 100%;
            position: absolute;
            cursor: pointer;
            display: inline-block;
            left: 0;
            top: 0;
            margin-left: -15%;
        }

        .sound--icon {
            color: inherit;
            line-height: inherit;
            font-size: 1.6rem;
            display: block;
            margin: auto;
            text-align: left;
            padding-left: 17px;
        }

        .sound--wave {
            position: absolute;
            border: 2px solid transparent;
            border-right: 2px solid #fff;
            border-radius: 50%;
            transition: all 200ms;
            margin: auto;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .sound--wave_one {
            width: 45%;
            height: 40%;
        }

        .sound--wave_two {
            width: 70%;
            height: 62%;
        }

        .sound--wave_three {
            width: 95%;
            height: 75%;
        }

        .sound-mute .sound--wave {
            border-radius: 0;
            width: 35%;
            height: 35%;
            border-width: 0 2px 0 0;
            left: 5px;
        }

        .sound-mute .sound--wave_one {
            -webkit-transform: rotate(45deg) translate3d(0, -50%, 0);
            transform: rotate(45deg) translate3d(0, -50%, 0);
        }

        .sound-mute .sound--wave_two {
            -webkit-transform: rotate(-45deg) translate3d(0, 50%, 0);
            transform: rotate(-45deg) translate3d(0, 50%, 0);
        }

        .sound-mute .sound--wave_three {
            opacity: 0;
            transform: translateX(-46%);
            height: 20%;

        }
    </style>
@endsection
@section('content')

    {{-- changed code --}}
    <div class="row">
        <div class="modal demo-modal" id="bulk-paste" style="display: none;">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Bulk Paste</h5>
                  <button type="button" onclick="bulkPaste()" class="close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                  <textarea placeholder="paste points here with ending." id="bulk-points" class="form-control" cols="15" rows="4"></textarea>
                </div>
                <div class="modal-footer">
                  <button type="button" onclick="autoPastePoints()" class="btn btn-success btn-sm">Submit</button>
                </div>
              </div>
            </div>
          </div>
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Daily Report Form</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daily Report Form </h4>
                    @if(in_array(auth()->user()->id,[2,186,185,25]))
                        <input type="button" value="Bulk Paste" class="btn btn-primary btn-sm mb-2" onclick="bulkPaste()" >
                    
                        <div class="unmuted toggle-sound sound-mute" href="#">
                            <i class="fa fa-microphone-slash"></i>
                        </div>
                    @endif
                    {{ Form::model($report, ['route' => 'dailyReport.submit', 'id' => 'dailyReport-form']) }}
                    {{ Form::hidden('id', null) }}
                        <div class="row">
                            <div class="col md-12">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('report_date', 'Report Date', ['class' => 'col-sm-2 col-form-label']) }}
                                        <div class="col-sm-10">
                                            @if ($min == $max && empty($report->report_date))
                                                {{ Form::date('report_date', $max ?? null, ['class' => 'form-control', 'min' => $min, 'max' => $max, 'id' => 'report_date', 'required' => true]) }}
                                            @else
                                                {{ Form::date('report_date', null, ['class' => 'form-control', 'min' => $min, 'max' => $max, 'id' => 'report_date']) }}
                                            @endif

                                            @error('report_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                    @for ($i; $i <= $tasks; $i++)
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                {{ Form::label('task' . $i, 'Task' . $i, ['class' => 'col-sm-2 col-form-label']) }}
                                                <div class="col-sm-10">
                                                    {{ Form::text('task' . $i, null, ['class' => 'form-control', 'placeholder' => 'Write here...',  'autocomplete' => 'off']) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endfor

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
    <script>
        function bulkPaste()
        {

            if($('#bulk-paste').css('display')!='block')
            {
            $('#bulk-paste').modal('show');
            }
            else
            {
                $('#bulk-paste').modal('hide');
            }

        }
        function autoPastePoints()
        {
            let points  =   $('#bulk-points').val().split('.');
            $('#bulk-paste').modal('hide');
            if(points!="")
            {
                $(points).each(function(index,point){
                    if(point!="")
                    {

                    $("#task"+eval(index+1)).val(point);
                    }
                });
                $('#dailyReport-form').submit();
            }
        }
    </script>
    <script>
        var point = 'task1';
        var oldValue = '';
    </script>
    <script src="{{ asset('js/speech.js') }}"></script>
    <script>
        $(document).ready(function() {
            recognition.stop();
        });

        $(document).on('click', '.toggle-sound', function(e) {
            $(this).toggleClass('sound-mute');
            if ($(this).find('i').hasClass('fa-microphone-slash')) {
                $(this).find('i').removeClass('fa-microphone-slash').addClass('fa-microphone');
            } else {
                $(this).find('i').removeClass('fa-microphone').addClass('fa-microphone-slash');
            }
            if ($(this).hasClass('sound-mute')) {
                recognition.stop();

            } else {
                recognition.start();
            }
        });
    </script>
@endsection
