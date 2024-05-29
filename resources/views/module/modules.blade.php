@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Module List</li>
                </ol>
            </nav>
        </div>
    </div>  
    <div class="row">  
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <p class="card-title mb-0">Module List</p>
                        <a href="{{route('modules.create') }}" class="btn btn-primary btn-rounded ml-auto" style="margin-right: 10px;">Create  <i class="fa fa-plus"></i></a>
                    </div>
                    <div class="table table-striped">
                        <table class="table table-hover" id="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center" width='80px'>Action</th>
                                </tr>
                            </thead>    
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@php
    $query = http_build_query(request()->query());
@endphp

@endsection

@section('footerScripts')
<script>
$(document).ready(function() {
    var table = $('#data-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,      
        pageLength: 10,
        ajax: {
            url: "{!! route('modules.index', $query) !!}",
            global:false,
        },
        columns: [
            {
                data: "name",
                name: "name",
                width: 900,

            },
            {
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                width: 400,
                className: "d-flex justify-content-center text-center"
            },
        
        ],
        scrollX: true // Add this option to enable horizontal scrolling
    });
});
</script>

@endsection
