@extends('layouts.master')
@section('content')

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item ">Form</li>
            </ol>
        </nav>
    </div>
    <div class="col-12 grid-margin">

        <div class="card">
            {{ Form::model($announcement,['route'=>$submitRoute,'method'=>$method, 'files' => true]) }}
                <div class="card-body">
                    <h4 class="card-title ">Announcement Form</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('title','Title', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::text('title',null,['class'=>'form-control','id'=>'title','placeholder'=>'Title'])}}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('user_id','Users', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::select('user_id[]',$users,$announcement->users->pluck('id')->toArray(),['class'=>'form-control tail-select','multiple'=>'multiple'])}}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('start_dt', 'Start Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('start_dt', null, ['class' => 'form-control', 'placeholder' => 'Choose Start Date']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('end_dt', 'End Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('end_dt', null, ['class' => 'form-control', 'placeholder' => 'Choose End Date']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('start_time', 'Start Time', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::time('start_time', null, ['class' => 'form-control', 'placeholder' => 'Choose Start Time']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('end_time', 'End Time', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::time('end_time', null, ['class' => 'form-control', 'placeholder' => 'Choose End Time']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('attachment', 'Upload Attachment', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    @if(!empty($announcement->getImage()))
                                    <img class="mr-3" src="{{ $announcement->getImage() }}" id="stored-image" width="70" height="70">
                                    @endif
                                    @if (!empty($announcement->attachment))
                                        <input type="file" name="attachment" value="{{$announcement->attachment}}" accept="image/jpeg,image,jpg,image/png" id="gallery-photo-add">
                                    @else
                                        <input type="file" name="attachment" value="{{$announcement->attachment}}" accept="image/jpeg,image,jpg,image/png" id="gallery-photo-add">
                                    @endif
                                    <div id="gallery"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('is_publish', 'Is Publish', ['class' => 'col-md-3 control-label']) }}
                                <div class="col-sm-9">
                                    @if(empty($announcement->id))
                                    {{ Form::checkbox('is_publish', null,true )}}
                                    @else
                                    {{ Form::checkbox('is_publish', null )}}
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                {{ Form::label('description', 'Description', ['class' => 'control-label ml-3']) }}
                                <div class="col-sm-12">
                                    {{ Form::textarea('description', null, ['class' => 'form-control summernote', 'placeholder' => 'Description']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            {{Form::close()}}
        </div>

    </div>
</div>
@endsection

@section('footerScripts')
<script>
  $(document).ready(function(){
        summernoteload('.summernote');

        tail.select('#employee-tail-select', {
            search: true,
            multiSelectAll: true,
            multiPinSelected: false,
            width:'100%'
        });
    });

    function summernoteload(elm) {
        $(elm).summernote({
            toolbar: [
                ['cleaner', ['cleaner']], // The Button
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['media', 'link', 'hr']],
                // ['view', ['fullscreen', 'codeview']],
                ['help', ['help']],
            ],
            cleaner: {
                action: 'button', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
                newline: '<br>', // Summernote's default is to use '<p><br></p>'
                notStyle: 'position:absolute;top:0;left:0;right:0', // Position of Notification
                icon: '<i class="note-icon">clean</i>',
                keepHtml: true, // Remove all Html formats
                keepOnlyTags: ['<p>', '<ul>', '<li>', '<a>', '<h3>', '<h4>', '<h5>', '<img>', '<ol>',
                    '<span>'
                ], // If keepHtml is true, remove all tags except these
                keepClasses: true, // Remove Classes
                badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript',
                    'html'
                ], // Remove full tags with contents
                badAttributes: ['style', 'start', 'color', 'bgcolor'], // Remove attributes from remaining tags
                limitChars: false, // 0/false|# 0/false disables option
                limitDisplay: 'both', // text|html|both
                limitStop: false // true/false
            },
            height: 200,

        });
    }

        $(function() {
    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {
        $('#gallery').empty();
        $('#stored-image').hide();
        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img>')).attr({'src': event.target.result,'height':'200px','width':'200px'}).appendTo(placeToInsertImagePreview);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('#gallery-photo-add').on('change', function() {
        imagesPreview(this, 'div#gallery');
    });
    });


</script>
@endsection
