@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Designation List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Designation List</p>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>
  @endsection
  
@section('footerScripts')
<script type="text/javascript">
 
  var myFields= [
          
            {
                title:"Name",
                name: "name",
                type:"text",
                width:400,
                validate:"required"
            },
            {
                type:"control",
                width:100
            },
     
        ];

   var insertDesignation    = `{{ route("designation.store") }}`;
   var updateDesignation    = `{{ route("designation.update",['designation'=>':id']) }}`;
   var deleteDesignation    = `{{ route("designation.destroy",['designation'=>':id']) }}`;
   var DesignationURL       = '{{route("designation.index")}}';

    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        paging: true,
        autoload: true,
        inserting: true,
        editing: true,
        filtering:true,
        paging:true,
        pageSize:10,
        pageLoading:true,
        deleteConfirm: "Do you really want to delete Designation?",
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:DesignationURL,
                    data: filter
                });
            },
            insertItem: function(item) {
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url: insertDesignation,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                   
                    data:item
                });
            },
            updateItem: function(item) {
              updateDesignation = updateDesignation.replace(':id',item['id']);
                return $.ajax({
                    type: "PUT",
                    url:updateDesignation,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            },
            deleteItem: function(item) {
                deleteDesignation = deleteDesignation.replace(':id',item['id']);
                return $.ajax({
                    type: "DELETE",
                    url:deleteDesignation,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            }
        },
        
        onItemInserted: function(args) {
          
          toastr.success('Designation Added Successfully')
        },
        onItemUpdated: function(args) {

          toastr.success('Designation Updated Successfully')
            
        },
        onItemDeleted: function(args) {

        toastr.success('Designation Deleted Successfully')

        },
        onError: function (args) {

          errors = args.args[0].responseJSON.errors;
          error = '';
          $.each(errors, function(key, value) {
              
              error += value + "\n";

          });
          toastr.warning(error);
        },
  
    });

</script>
@endsection
