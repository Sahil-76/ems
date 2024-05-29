@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Department List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
                <p class="card-title">Department List</p>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Short Name</th>
                                <th>Manager</th>
                                <th>Team Leader</th>
                                <th>Line Manager</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                                <tr>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->short_name }}</td>
                                    <td>{{ $department->manager_name }}</td>
                                    <td>{{ $department->team_leader_name }}</td>
                                    <td>{{ $department->line_manager_name }}</td>
                                    <td>
                                        <button class="btn btn-danger">Edit</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


              {{-- <div id="jsGrid"></div> --}}

          </div>
      </div>
  </div>
</div>
  @endsection
  
@section('footerScripts')
<script>
      var myFields= [
          
            {
                title:"name",
                name: "name",
                type:"text",
                width:200
               
            },
            {
                title:"Short Name",
                name: "short_name",
                type:"text",
                width:200
               
            },   
            {
                title:"description",
                name: "description",
                filtering:false,
                type:"text",
                width:400
               
            },
            @canany(['update','delete'],new  App\Models\Department())
            {
                type:"control",
                width:100
            }
           @endcanany
     
        ];

   var deleteDepartment = '{{ route("deleteDepartment") }}';
   var insertURL = '{{ route("insertDepartment") }}';
   var updateDepartment = '{{ route("updateDepartment") }}';
   var departmentURL='{{route("departmentList")}}';

    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        paging: true,
        autoload: true,
        inserting: true,
        @can('update',new  App\Models\Department())
        editing: true,
        @endcan
        filtering:true,
        paging:true,
        pageSize:10,
        pageLoading:true,
        deleteConfirm: "Do you really want to delete Department?",
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:departmentURL,
                    data: filter
                });
            },
            insertItem: function(item) {
              console.log(item);
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url: insertURL,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                   
                    data:item
                });
            },
            updateItem: function(item) {
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url:updateDepartment,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            },
            deleteItem: function(item) {
                return $.ajax({
                    type: "DELETE",
                    url:deleteDepartment,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            }
        },
        onItemInserted: function(args) {
        
          toastr.success('Department Added Successfully')
        },
        onItemUpdated: function(args) {

          toastr.success('Department Updated Successfully')
            
        },
        onItemDeleted: function(args) {

         toastr.success('Department Deleted Successfully')
  
        },
        onError: function(args) {

          errors = args.args[0].responseJSON.errors;
          error = '';
          $.each(errors, function(key, value) {
              
              error += value + "\n";

          });
          toastr.warning(error)

        },
     
      
    });
</script>

@endsection
