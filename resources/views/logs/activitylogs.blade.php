@extends('layouts.master')
@section('content')

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Activity Logs</p>

                <div id="jsGrid"></div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('footerScripts')
<script type = "text/javascript" >
$(window).on('load', function() {
 $('#jsGrid').find('select').addClass('selectJS');
 $('.selectJS').select2({
      placeholder: "Select an option",
      allowClear: false
    });
});

    var users = '{!! $users !!}';
    var modules = '{!! $modules !!}';
    users = JSON.parse(users);
    modules = JSON.parse(modules);

var myFields = [

    {
      
            title: "Name",
            name: "user_id",
            type: "select",
            items: users,
            editing: false,
            valueType: "number|string",
            width: 40,

    },
    {
      
      title: "Module",
      name: "module_type",
      type: "select",
      items: modules,
      editing: false,
      valueType: "number|string",
      width: 40,

    },
    
    {
        title: "action",
        name: "action",
        type: "text",
        width: 50,
        filtering:false,
    },

    {
        
        title: "Date",
        name: 'date',
        type: "text",
        width: 40,
        filtering: false,

    },
];


var activityURL = '{{route("activityLogList")}}';

$("#jsGrid").jsGrid({
    fields: myFields,
    width: "100%",
    autoload: true,
    pageSize: 10,
    filtering: true,
    paging: true,
    pageSize: 10,
    pagerFormat: "Pages:  {pages}     {pageIndex} of {pageCount}",
    pageLoading: true,
    controller: {
        loadData: function (filter) {
            return $.ajax({
                type: "GET",
                dataType: "json",
                url: activityURL,
                data: filter,

            });
        }

    },

    onError: function (args) {

        errors = args.args[0].responseJSON.errors;
        error = '';
        $.each(errors, function (key, value) {

            error += value + "\n";

        });
        toastr.warning(error)

    },

});

</script>
@endsection