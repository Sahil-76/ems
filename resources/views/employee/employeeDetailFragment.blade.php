<div class="table-responsive  col-12">
    <table id="example1" class="table table-hover">

        <thead>
            <tr>
                <th>Picture</th>
                <th>Name</th>
                <th>Department</th>
                <th>Joining Date</th>
                <th>Details</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a target="_blank" href="{{ $employee->user->user_image }}" class="employee-image"><img
                    src="{{ $employee->user->user_image }}" width="42" height="42"></a></td>
                <td>{{ $employee->name }}</td>
                <td>{{$employee->department->name ?? 'N/A'}}</td>
                <td>{{ getFormatedDate($employee->join_date) }}</td>
                
                <td><a target="_blank" href="{{ route('employeeDetail', ['employee' => $employee->id]) }}"
                    class="p-2 text-primary fas fa-address-card"
                    style="font-size:20px;border-radius:5px;"></a></td>
                <td><a onclick="showExitForm()"
                    class="btn btn-danger btn-rounded">Initiate No Dues</a></td>
            </tr>
        </tbody>
    </table>
</div>