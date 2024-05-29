@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Departments</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <!-- Default box -->

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h3>Departments List</h3>
                        <input id="search" type="text" placeholder="Search.." class="col-md-2 form-control float-right">
                    </div>
                    <div class="table-sm table-responsive  col-12">
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Department</th>
                                    <th>Total Employees</th>
                                    <th>Manager</th>
                                    <th>Team Leader</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="myTable">
                                @foreach ($departments as $department)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $department->name }}</td>
                                        <td><a href="{{ route('employeeView',['department_id'=>$department->id]) }}" target="_blank">{{ $department->employees_count }}</a></td>
                                        <td id="manager{{$loop->iteration}}" data-manager="{{$department->manager_id}}" class="manager-select">{{$department->deptManager->name ?? null}}</td>
                                        <td id="teamleader{{$loop->iteration}}" data-teamleader="{{$department->team_leader_id}}" class="teamleader-select">{{$department->deptTeamLeader->name ?? null}}</td>
                                        <td id="button{{$loop->iteration}}"><button class="btn btn-primary" onclick="showEmployees('{{$loop->iteration}}','{{$department->id}}', '{{$department->manager_id}}',{{ $department->team_leader_id }})">Edit</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="col-12 mt-3">
                            <div class="float-right">
                                {{ $departments->links() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <select style='width:100%;display:none;' data-placeholder="select an option"
        placeholder="select an option" class='selectData form-control'>
        @foreach ($employeeDepartments as $department=> $employees)
        <optgroup label="{{$department}}">
            @foreach($employees as $employee)
            <option value="{{$employee->id}}">{{$employee->name}}</option>
            @endforeach
        </optgroup>
        @endforeach
        </select>
@endsection
@section('footerScripts')
    <script>
        $(document).ready(function(){
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        function showEmployees(index, departmentId, selectedManager,selectedTeamLeader)
        {
            console.log(selectedManager,selectedTeamLeader);
            let html  =   $('.selectData:first').show();
            let html2 =   $(html).clone();
            var button = `<button type="button" onClick="updateManager('${departmentId}', '${index}')" class="btn btn-danger">Update</button>`;

            $(`#manager${index}`).html(html);

            $(`#teamleader${index}`).html(html2);
                $(`#manager${index} select`).val(selectedManager).select2({
                    allowClear: true,
                }).trigger('change');
                $(`#teamleader${index} select`).val(selectedTeamLeader).select2({
                    allowClear: true,
                }).trigger('change');
            $(`#button${index}`).html(button);
        }

        function updateManager(departmentId, index)
        {
            var old_manager_id = $(event.target).parent('td').siblings('.manager-select').data('manager');
            var  old_team_leader_id = $(event.target).parent('td').siblings('.teamleader-select').data('teamleader');

            // console.log(old_manager_id);

            $(event.target).html('Please wait').append(
                        '<i class="mdi mdi-rotate-right mdi-spin ml-1" aria-hidden="true"></i>').attr('disabled',true);
            var manager = $(event.target).closest('td').siblings('td.manager-select').find('select').val();
            var teamleader = $(event.target).closest('td').siblings('td.teamleader-select').find('select').val();
            $.ajax({
                url : "{{route('hr.managerUpdate')}}",
                type: 'post',
                data: {"departmentId": departmentId, "manager_id": manager,'teamleader':teamleader,'old_manager':old_manager_id,'old_team_leader':old_team_leader_id},
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                success: function(response)
                {
                    toastr.success('Updated');
                    location.reload();
                }
            });
        }
    </script>
@endsection
