@extends('layouts.master')
@section('content')

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Exit Employees</li>
            </ol>
        </nav>
    </div>

    <div class="col-12 mb-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{ Form::open(['method' => 'GET']) }}
                    <div class="card-body">
                        <p class="card-title">Filter</p>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                {{ Form::select('department_id', $departments, request()->department_id, ['onchange' => 'getEmployees(this.value)', 'class' =>
                                'form-control selectJS', 'placeholder' => 'Select your Department',
                                'data-placeholder'=>'Select Department']) }}
                            </div>
                            <div class="col-sm-3">
                                {{Form::select('name',$employees, request()->name, ['class' => 'form-control selectJS',
                                'id' => 'employees', 'placeholder' => 'Select Employee','data-placeholder'=>'Select Employee']) }}
                            </div>
                            <div class="col-sm-3">
                                {{ Form::text('biometric_id', null, ['class'=>'form-control', 'placeholder' => 'Enter EY Code']) }}
                            </div>
                            <div class="col">
                                <button type="button" class="btn bg-primary  text-white" name="daterange"
                                    id="date-btn" value="Select Date">
                                    @if(request()->has('dateFrom') && request()->has('dateTo'))
                                        <span>
                                            {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y')}} - {{
                                            Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y')}}
                                        </span>
                                    @else
                                        <span>
                                            <i class="fa fa-calendar"></i> &nbsp;Filter Date&nbsp;
                                        </span>
                                    @endif
                                        <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                            {{Form::hidden('dateFrom',request()->dateFrom ?? null, array('id'=>'dateFrom'))}}
                            {{Form::hidden('dateTo', request()->dateTo ?? null, array('id'=>'dateTo'))}}
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
        <div class="row">   </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col">
                        <h4 class="">Exit Employees List</h4>
                    </div>
                    @can('exitExport', new App\Models\Employee())
                    <div class="mr-3">
                            <a href="{{route('exportExit', http_build_query(request()->query()))}}" target="_blank"
                                class="btn btn-warning btn-rounded float-right">Export</a>
                            </div>
                        @endcan
                        @can('hrUpdateEmployee', new App\Models\Employee())
                            <div class="mr-3">
                                <a href="{{url('records/old_exit_employees_list.pdf')}}" target="_blank"
                                    class="btn btn-success btn-rounded float-right">View old records</a>
                            </div>
                            <div class="mr-4">
                                <a href="{{route('exitForm')}}" class="btn btn-primary btn-rounded float-right">Add new
                                    record</a>
                            </div>
                        @endcan
                </div>
                <div class="table-responsive  col-12">
                    <table id="data-table" class="table table-hover text-center gallery">

                        <thead>
                            <tr>
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Exit Date</th>
                                <th>EY Code</th>
                                <th>Details</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Experience: <span></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{Form::open(['route' =>'uploadExperience','files' => 'true'])}}
            <div class="modal-body">

                {{Form::hidden('employee_id', null,['id'=>'employee_id'])}}
                {{Form::file('experience_file')}}

            </div>
            @can('hrUpdateEmployee', new App\Models\Employee())
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
            @endcan
            {{Form::close()}}
        </div>
    </div>
</div>
@endsection

@section('footerScripts')
<script>
    $('#date-btn').daterangepicker(
    {
        opens: 'left',
        locale: { cancelLabel: 'Clear' },
        ranges   : {
            'Today'       : [moment(), moment()],
            'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 5 Days' : [moment().subtract(4, 'days'),moment()],
            'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
            'Last 14 Days': [moment().subtract(13,'days'),moment()],
            'Last 30 Days': [moment().subtract(29, 'days'),moment()],
            'This Month'  : [moment().startOf('month'), moment().endOf('month')],
            'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        }
        // startDate: moment().subtract(29, 'days'),
        //endDate  : moment()
    },
    function (start, end) {
        $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
        $('#dateFrom').val(start.format('YYYY-M-DD'));
        $('#dateTo').val(end.format('YYYY-M-DD'));
    }
);

$('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
    clearDateFilters('date-btn','date');
});

function clearDateFilters(id, inputId){
    $('#'+id+' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
    $('#'+inputId+'From').val('');
    $('#'+inputId+'To').val('');
}

        $(document).ready(function() {

            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                scrollY: '85vh',
                scrollCollapse: true,
                order: [[3, 'desc']],
                pageLength: 10,
                ajax: "{!! route('exitList', http_build_query(request()->query())) !!}",
                columns: [{
                    data: 'image_source',
                    orderable: false,
                    
                }, {
                    data: "name"
                }, {
                    data: "department_name",
                }, {
                    data: "exit_date"
                }, {
                    data: "biometric_id",
                }, {
                    data: "detail",
                    orderable: false,
                }, {
                    data: "status",
                    orderable: false,
                }
            ]
            });

        });

    function uploadExperience(employee_id, employee_name){
        $('#employee_id').val(employee_id);
        $('#exampleModalLabel span').text(employee_name);
    }

</script>
<script>
    function getEmployees(department_id) {
            if (department_id) {
                $.ajax({
                    url: "{{ route('getEmployees') }}/" + department_id,
                    type: 'get',
                    data:{
                        type: 'exit'
                    },
                    dataType: 'json',
                    success: function(response) {
                        var options = `<option value=''></option>`;
                        $.each(response, function(key, value) {
                            options += "<option value='" + key + "'>" + value + "</option>";
                        });

                        $('#employees').html(options);
                        $("select").select2({
                            placeholder: "Select an option"
                        });
                    }
                })
            }
        }
</script>
@endsection