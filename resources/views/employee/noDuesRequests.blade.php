@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employee</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">

                {{ Form::open(['method' => 'GET']) }}
                <div class="card-body">
                    <p class="card-title">Filter</p>
                    <div class="form-group row">

                        {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-3">
                            {{ Form::select('department_id', $department_id, request()->department_id, [
                                'onchange' => 'getEmployees(this.value)',
                                'class' => 'form-control selectJS',
                                'placeholder' => 'Select your department',
                            ]) }}
                        </div>

                        {{ Form::label('user_id', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-3">
                            <select style='width:100%;' name="user_id" data-placeholder="select an option" id="employees"
                                placeholder="select an option" class='form-control selectJS'>
                                <option value="" disabled selected>Select your option</option>
                                @foreach ($employeeDepartments as $department => $employee)
                                    <optgroup label="{{ $department }}">
                                        @foreach ($employee as $user)
                                            <option value="{{ $user->user_id }}"
                                                @if ($user->user_id == request()->user_id) selected @endif>
                                                {{ $user->office_email }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="btn bg-primary  text-white" name="daterange" id="date-btn"
                            value="Select Date">
                            @if (request()->has('dateFrom') && request()->has('dateTo'))
                                <span>
                                    {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y') }} -
                                    {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y') }}
                                </span>
                            @else
                                <span>
                                    <i class="fa fa-calendar"></i> &nbsp;Filter Date&nbsp;
                                </span>
                            @endif
                            <i class="fa fa-caret-down"></i>
                        </button>
                        {{ Form::hidden('dateFrom', request()->dateFrom ?? null, ['id' => 'dateFrom']) }}
                        {{ Form::hidden('dateTo', request()->dateTo ?? null, ['id' => 'dateTo']) }}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                            <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <h4 class="">Employee No Dues Requests</h4>
                        </div>
                    </div>
                    <div class="table-responsive col-12">
                        <table class="table table-borderless" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Picture</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Exit Date</th>
                                    <th>Reason</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody class="gallery">
                                @foreach ($employees as $employee)
                                    <tr class="border-top">
                                        <td>
                                            <a target="_blank" href="{{ $employee->user->user_image }}"
                                                class="employee-image"><img src="{{ $employee->user->user_image }}"
                                                    width="42" height="42"></a>
                                        </td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->office_email }}</td>
                                        <td>{{ $employee->department->name ?? null }}</td>
                                        <td>{{ getFormatedDate($employee->exit_date) }}</td>
                                        <td style="white-space: normal;">{{ $employee->reason }}</td>
                                        <td><a href="{{ route('employeeDetail', ['employee' => $employee->id]) }}"
                                                class="p-2 text-primary fas fa-address-card"
                                                style="font-size:20px;border-radius:5px;"></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            {{ Form::open(['route' => ['noDuesSubmit', $employee->employee_id], 'class' => 'd-flex']) }}
                                            <div class="col-md-3">
                                                <p>Manager</p>
                                                @if (empty($employee->dept_no_due) &&
                                                        Auth::user()->can('managerNoDuesApprover', new App\Models\Employee()))
                                                    {{ Form::select('dept_no_due', $actions, $employee->dept_no_due ?? null, ['class' => 'selectJS form-control']) }}
                                                @else
                                                    {{ Form::select('dept_no_due', $actions, $employee->dept_no_due ?? null, ['class' => 'selectJS form-control', 'disabled' => true]) }}
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <p>IT Department</p>
                                                @if (empty($employee->it_no_due) &&
                                                        Auth::user()->can('itNoDuesApprover', new App\Models\Employee()) &&
                                                        !empty($employee->dept_no_due))
                                                    {{ Form::select('it_no_due', $actions, $employee->it_no_due ?? null, ['class' => 'selectJS form-control']) }}
                                                @else
                                                    {{ Form::select('it_no_due', $actions, $employee->it_no_due ?? null, ['class' => 'selectJS form-control', 'disabled' => true]) }}
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <p class="label">HR Department</p>
                                                @if (empty($employee->hr_no_due) &&
                                                        Auth::user()->can('hrNoDuesApprover', new App\Models\Employee()) &&
                                                        !empty($employee->it_no_due) &&
                                                        !empty($employee->dept_no_due))
                                                    {{ Form::select('hr_no_due', $actions, $employee->hr_no_due ?? null, ['class' => 'selectJS form-control']) }}
                                                @elseif($employee->user->user_type == 'Office Junior')
                                                    {{ Form::select('hr_no_due', $actions, $employee->hr_no_due ?? null, ['class' => 'selectJS form-control']) }}
                                                @else
                                                    {{ Form::select('hr_no_due', $actions, $employee->hr_no_due ?? null, ['class' => 'selectJS form-control', 'disabled' => true]) }}
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <br>
                                                <button type="submit"
                                                    class="btn btn-primary btn-rounded m-3">Submit</button>
                                            </div>
                                            {{ Form::close() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="float-right">
                        {{ $employees->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        $('body').addClass('sidebar-icon-only');

        $('#date-btn').daterangepicker({
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 5 Days': [moment().subtract(4, 'days'), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                }
            },
            function(start, end) {
                $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
                $('#dateFrom').val(start.format('YYYY-M-DD'));
                $('#dateTo').val(end.format('YYYY-M-DD'));
            }
        );

        $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('date-btn', 'date');
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }
    </script>
@endsection
