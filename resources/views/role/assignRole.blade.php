@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Roles</li>
                </ol>
            </nav>
        </div>

        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Form</h3>
                </div>
                {!! Form::open(['method' => 'post', 'id' => 'form']) !!}
                    <div class="card-body">
                        {{ Form::label('user', 'Select User', ['class' => 'col-sm-2 col-form-label']) }}

                        {{ Form::select('user', $employees, null, ['class' => 'form-control selectJS','id'=>'userEmail', 'placeholder' => 'Choose one','onchange'=>"getRoles()"]) }}
                    </div>
                {{ Form::close() }}
            </div>
        </div>

        
        <div class="col-md-12" style="display: none" id="userRoles">
            <div class="card card-primary card-outline">
                <div class="card-header">
                </div>
                <h3 class="card-title m-2">Roles:</h3>
                {{ Form::open(['route' => 'role.store', 'method' => 'POST']) }}
                    {!! Form::hidden('user', null, ['id' => 'selectedUser']) !!}
                    <div class="card-body" id="roleChecks">

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        function getRoles() {
            var user = $('#userEmail').val();
            console.log(user);
            $.ajax({
                url: "{{ route('role.create') }}",
                type: 'GET',
                data: {
                    "id": user
                },
                success: function(response) {
                    var userRoles = Object.values(response.userRoles);

                    $('#userRoles').show();

                    var data = " <div class='form-group row'>";

                    $.each(response.roles, function(index, value) {

                        data += `<div class="card-body col-md-3">
                                    <div class="checkbox">`;
                        if ($.inArray(value, userRoles) != -1) {

                            data +=
                                `   <input type='checkbox' class='mr-2' name='roles[]' value='${index}' checked>${value}`;
                        } else {
                            data +=
                                `   <input type='checkbox' class='mr-2' name='roles[]' value='${index}'>${value}`;
                        }
                        data += `</div>
                                    </div>`;
                    });

                    data += "</div>";

                    $('#roleChecks').html(data);
                    $('#selectedUser').val(user); // for hidden field of user in assign form
                },
                error: function(response) {
                    alert(response);
                }
            });
        }
    </script>
@endsection
