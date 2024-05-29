@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pending Employee Profile</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary"></div>
                <div class="card-body">
                    <div class="card-title">Filter</div>
                    {{ Form::open(['method' => 'GET']) }}
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">Pending Profile Pic
                                        <input type="checkbox" @if(!empty(request()->pending_profile_pic)) checked @endif onchange="this.form.submit()" name="pending_profile_pic" class="form-check-input">
                                        <i class="input-helper"></i><i class="input-helper"></i></label>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Pending Employee Profile</p>
                            <div class="">
                                <table id="example1" style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Pending Fields</th>
                                            <th>View Detail</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendingEmployeeProfiles as $pendingEmployeeProfile)
                                        {{-- @if(array_key_exists($pendingEmployeeProfile->id,$personsAddedTheProfilePic))
                                        @continue
                                        @endif --}}
                                            <tr>
                                                <td>{{ $pendingEmployeeProfile->name }}</td>
                                                <td>{{ $pendingEmployeeProfile->office_email }}</td>
                                                <td>{{ $pendingEmployeeProfile->department->name ?? null }}</td>
                                                <td>{{$pendingEmployeeProfile->pending_fields}}</td>
                                                <td><a href="{{route('employeeDetail',['employee'=>$pendingEmployeeProfile->id])}}" class="p-2 text-primary fas fa-address-card"
                                                    style="font-size:20px;border-radius:5px;"></a></td>
                                                @if ($pendingEmployeeProfile->profileReminder->isEmpty())
                                                    <td><button id="{{ $pendingEmployeeProfile->id }}"
                                                            class="btn btn-primary btn-lg p-3">Send Reminder </button></td>
                                                @else
                                                    <td><button id="{{ $pendingEmployeeProfile->id }}"
                                                            class="btn btn-danger btn-lg p-3">Send Again </button></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                $('#example1').dataTable({
                    ordering: false,
                    "scrollX": true,

                    columns: [{
                            "name": "Name"
                        },
                        {
                            "name": "Email"
                        },
                        {
                            "name": "Department"
                        },
                        {
                            "name": "Pending Fields"
                        },
                        {
                            "name": "Action",
                            searching: false,
                            sorting: false,
                        },
                        {
                            "name": "View Detail",
                            searching: false,
                            sorting: false,
                        },

                    ],
                });

                $('tbody').on('click', 'button', function() {
                    var button = this;
                    $(button).attr('disabled', true).html('Sending').append(
                        '<i class="mdi mdi-rotate-right mdi-spin ml-1" aria-hidden="true"></i>');
                    var employeeId = $(button).attr('id');
                    var link = "{{ route('sendReminder', '') }}" + '/' + employeeId;
                    $.ajax({
                        url: link,
                        method: 'GET',
                        success: function() {
                            $(button).attr('disabled', false).html('Send Again').removeClass('btn-primary').addClass('btn-danger').find('i').remove();
                            toastr.success('Reminder Send');
                        },
                    });
                });
            </script>

        @endsection
