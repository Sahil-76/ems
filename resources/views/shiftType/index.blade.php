@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Shift Types</li>
                </ol>
            </nav>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Shift Types
                                <div class="float-right">
                                    <a href="{{route('shift-type.create')}}" class="btn btn-sm btn-primary"> <i class="fa fa-plus"></i> Create </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="example1" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Start Time </th>
                                            <th>Mid Time </th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($shiftTypes as $type)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $type->name ?? ''}}</td>
                                                <td>{{ Carbon\Carbon::createFromFormat('H:i:s',$type->start_time ?? '')->format('g:i:s A') }}</td>
                                                <td>{{ Carbon\Carbon::createFromFormat('H:i:s',$type->mid_time ?? '')->format('g:i:s A') }}</td>
                                                <td>{{ Carbon\Carbon::createFromFormat('H:i:s',$type->end_time ?? '')->format('g:i:s A') }}</td>
                                                <td>
                                                    <a href="{{ route('shift-type.edit', ['shift_type' => $type->id]) }}"><i class="fa fa-edit"></i></a>
                                                    <a href="javascript:void(0);" onclick="deleteItem('{{ route('shift-type.destroy', $type->id) }}')">
                                                        <i class="fa fa-trash text-danger"></i></a>
                                                </td>
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