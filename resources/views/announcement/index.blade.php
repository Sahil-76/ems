@extends('layouts.master')
@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Announcement List</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Announcement
                                <div class="float-right">
                                    @can('create',new App\Models\Announcement())
                                        <a href="{{ route('announcement.create') }}" class="btn btn-sm btn-primary"> <i
                                                class="fa fa-plus"></i> Create </a>
                                    @endcan
                                </div>
                            </div>
                            @php
                                $page = 0;
                                if (!empty(request()->page) && request()->page != 1) {
                                    $page = request()->page - 1;
                                    $page = $page * 25;
                                }
                            @endphp
                            <div class="">
                                <table id="example1" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Is Publish</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            @canany(['update','delete'],new App\Models\Announcement())
                                                <th>Edit</th>
                                            @endcanany
                                        </tr>
                                    </thead>
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
            processing: true,
            serverSide: true,
            ajax: {
            url: "{!! route('getAnnouncement') !!}",
        },
            columns: [
                {data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false},
                {
                    data: 'title',
                },
                {
                    data: 'start_dt',
                },
                {
                    data: 'end_dt',
                },
                {
                    data: 'is_publish',
                },
                {
                    data: 'start_time',
                },
                {
                    data: 'end_time',
                },
                @canany(['update','delete'],new App\Models\Announcement())
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                },
                @endcanany
        ],
        "columnDefs": [ {
            "targets": [0,1,2,3,4,5,6,7],
            "createdCell":function(td, cellData, rowData, row, col) {
            
                $(td).css('white-space', 'normal');
                $(td).css('font-size', 'small');
            },
            }
        ]
        
        });

    </script>
@endsection
