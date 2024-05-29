@extends('layouts.master')
@section('content')

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Qualification List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Qualification List</p>
  
                <div id="jsGrid"></div>
  
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
            width:400
           
        },
        @canany(['update','delete'],new  App\Models\Qualification())
        {
            type:"control",
            width:100
        
       }
       @endcanany
 
    ];

var deleteQualification =   '{{ route("deleteQualification") }}';
var insertURL           =   '{{ route("insertQualification") }}';
var updateQualification =   '{{ route("updateQualification") }}';
var qualificationURL    =   '{{route("qualificationList")}}';

$("#jsGrid").jsGrid({
    fields:myFields,
    width: "100%",
    paging: true,
    autoload: true,
    inserting: true,
    @can('update',new  App\Models\Qualification())
    editing: true,
    @endcan
    filtering:true,
    paging:true,
    pageSize:10,
    pageLoading:true,
    deleteConfirm: "Do you really want to delete Qualification?",
    controller: {
        loadData: function(filter) {
            return $.ajax({
                type: "GET",
                dataType:"json",
                url:qualificationURL,
                data: filter
            });
        },
        insertItem: function(item) {
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
                url:updateQualification,
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: item
            });
        },
        deleteItem: function(item) {
            return $.ajax({
                type: "DELETE",
                url:deleteQualification,
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: item
            });
        }
    },
    onItemInserted: function(args) {
     
      toastr.success('Qualification Added Successfully')
    },
    onItemUpdated: function(args) {

      toastr.success('Qualification Updated Successfully')
        
    },
    onItemDeleted: function(args) {

     toastr.success('Qualification Deleted Successfully')

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
