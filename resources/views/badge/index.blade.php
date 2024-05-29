@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Badge</li>
                </ol>
            </nav>
        </div>
        
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Badge List
                                @can('create',new App\Models\Badge())
                                    <a href="{{route('badge.create')}}" class="btn btn-sm btn-primary float-lg-right m-2">Add </a>
                                @endcan
                            </p>
                            <div class=" table-responsive">
                                <table id="example1" class="table" style="width: 100%">

                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            @canany(['update','delete'],new App\Models\Badge())
                                                <th>Action</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badges as $badge)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{ucfirst($badge->name)}}</td>
                                                <td>
                                                    @if (!empty($badge->image))
                                                        <a target="_blank"
                                                            href="{{ route('downloadImage', ['reference' => $badge->image]) }}">
                                                            <i class="fa fa-eye text-primary"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                @canany(['update','delete'],new App\Models\Badge())
                                                    <td>
                                                        @can('update',new App\Models\Badge())
                                                            <a href="{{route('badge.edit',$badge->id)}}"><i class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @can('delete',new App\Models\Badge())
                                                            <a href="javascript:void(0);" class="text-red" onclick="deleteItem('{{ route('badge.destroy', $badge->id) }}')">
                                                                <i class="fa fa-trash text-danger"></i></a>
                                                        @endcan
                                                    </td>
                                                @endcanany
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
        });
    </script>
@endsection
