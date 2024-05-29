@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item ">Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                {{ Form::model($badge, ['route' => $submitRoute, 'method' => $method, 'files' => true]) }}
                    <div class="card-body">
                        <h4 class="card-title">Badge Form</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Badge Name','required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('Image', 'Upload Image', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        @if(!empty($badge->getImage()))
                                        <img class="mr-3" src="{{ $badge->getImage() }}" id="stored-image" width="70" height="70">
                                        @endif
                                        @if (!empty($badge->image))
                                        <input type="file" name="image" value="{{$badge->image}}" accept="image/jpeg,image,jpg,image/png,.gif" id="gallery-photo-add">
                                        @else
                                            <input type="file" name="image" value="{{$badge->image}}" accept="image/jpeg,image,jpg,image/png,.gif" id="gallery-photo-add" required>
                                        @endif
                                        <div id="gallery"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
<script>
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
