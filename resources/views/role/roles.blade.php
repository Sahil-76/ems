@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">Role List</p>

                    <div id="jsGrid"></div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script type="text/javascript">
        editRolePermissionUrl = "{{ route('role.edit', ['role' => ':id']) }}";

        var myFields = [

            {
                title: "Name",
                name: "name",
                type: "text",
                width: 200,
                validate: "required"

            },
            {
                title: "Description",
                name: "description",
                type: "text",
                width: 400,
                filtering: false,

            },
            {
                title: "Permission",
                width: 200,
                align: "center",
                @can('assignPermission', new App\Models\Permission())
                    itemTemplate: function(value, item) {

                        var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);


                        var $customEditButton = $("<a>")
                            .attr('href', editRolePermissionUrl.replace(':id', item.id))
                            .attr({
                                class: 'btn btn-primary btn-rounded btn-fw'
                            }).html("Assign")
                            .click(function(e) {
                                e.stopPropagation();
                            })
                        return $result.add($customEditButton);
                    }
                @endcan
            },
            {
                type: "control",
                width: 100,
                @cannot('update', new App\Models\Role())
                    editButton: false,
                @endcan
                @cannot('delete', new App\Models\Role())
                    deleteButton: false,
                @endcan
                /* itemTemplate: function(value, item) {

                              var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);

                              var $iconPencil = $("<i>").attr({class: "fa fa-edit align-top pl-2"});
                              var $customEditButton = $("<a>")
                                .attr('href', editRolePermissionUrl.replace(':id', item.id))
                                .attr({title: 'Assign Permissions'})
                                .click(function(e) {
                                    e.stopPropagation();
                                })
                                .append($iconPencil);

                              return $result.add($customEditButton);
                          } */
            },

        ];

        var deleteRole = '{{ route('deleteRole') }}';
        var insertRole = '{{ route('insertRole') }}';
        var updateRole = '{{ route('updateRole') }}';
        var roleURL = '{{ route('roleList') }}';

        $("#jsGrid").jsGrid({
            fields: myFields,
            width: "100%",
            paging: true,
            autoload: true,
            @can('insert', App\Models\Role::class)
                inserting: true,
            @endcan
            editing: true,
            filtering: true,
            paging: true,
            pageSize: 10,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete Role?",
            controller: {
                loadData: function(filter) {
                    return $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: roleURL,
                        data: filter
                    });
                },
                insertItem: function(item) {
                    console.log(item);
                    return $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: insertRole,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        data: item
                    });
                },
                updateItem: function(item) {
                    //   updateRole = updateRole.replace(':id', item['id']);
                    return $.ajax({
                        type: "POST",
                        url: updateRole,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: item
                    });
                },
                deleteItem: function(item) {
                    //   deleteRole = deleteRole.replace(':id', item['id']);
                    return $.ajax({
                        type: "POST",
                        url: deleteRole,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: item
                    });
                }
            },

            onItemInserted: function(args) {

                toastr.success('Role Added Successfully')
            },
            onItemUpdated: function(args) {

                toastr.success('Role Updated Successfully')

            },
            onItemDeleted: function(args) {

                toastr.success('Role Deleted Successfully')

            },
            onError: function(args) {

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
