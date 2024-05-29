@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Electricity</li>
            </ol>
        </nav>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary"></div>
            <div class="card-body">
                <div class="card-title">Filter</div>
                {{ Form::open(['method' => 'GET']) }}
                  <div class="row">
                      <div class="col-md-4">
                          <div class="form-group">
                              <label>Select Location</label>
                              {{ Form::select('location', $locations,request()->location, ['class' => 'form-control selectJS', 'data-placeholder' => 'Select Location', 'placeholder' => 'Select Location']) }}
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label>Select Action By</label>
                              {{ Form::select('user_id', $users, request()->user_id, ['class' => 'form-control selectJS', 'data-placeholder' => 'Select User', 'placeholder' => 'Select User']) }}
                          </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                            <input id="dateFrom" name="from_date"
                                value="{{ request()->from_date }}" type="hidden">
                            <input id="dateTo" name="to_date" value="{{ request()->to_date }}"
                                type="hidden">
                            <button type="button" id="date-btn" class="btn btn-block btn-primary"
                                style="width:185px;margin-top: 31px;">
                                @if (request()->has('from_date') && request()->has('to_date'))
                                    @php
                                        $startDate = request()->from_date;
                                        $endDate = request()->to_date;
                                    @endphp
                                    <span>
                                        {{ Carbon\Carbon::parse(request()->get('from_date'))->format('d/m/Y') }}
                                        -
                                        {{ Carbon\Carbon::parse(request()->get('to_date'))->format('d/m/Y') }}
                                    </span>
                                @else
                                    @php
                                        $startDate = now()->format('Y-m-d');
                                        $endDate = $startDate;
                                    @endphp
                                    <span>
                                        <i class="fa fa-calendar"></i> &nbsp;Select Date&nbsp;
                                    </span>
                                @endif
                            </button>
                        </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6 text-left">
                          <button type="submit" class="btn btn-primary me-2">Filter</button>
                          <a href="{{ request()->url() }}" class="btn btn-success">Clear</a>
                      </div>
                      @if (in_array(strtolower(auth()->user()->email), App\User::$developers))
                        <div class="col-md-6 text-right">
                            <a href="{{ route('electricity.export',request()->query()) }}" class="btn btn-success">Export</a>
                        </div>
                      @endif
                  </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
    
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Electricity
                            {{-- <div class="float-right">
                                @can('create',new App\Models\Electricity())
                                    <a href="{{route('electricity.create')}}" class="btn btn-sm btn-primary"> <i class="fa fa-plus"></i> Create </a>
                                @endcan
                            </div> --}}
                        </div>
                        <div class="table-responsive">
                            <table id="data-table" class="table" id="mytable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Start Unit</th>
                                        <th>End Unit</th>
                                        <th>Units Consumed</th>
                                        <th>Waste unit</th>
                                        <th>Action By</th>
                                        @can('update',new App\Models\Electricity())
                                            <th>Action</th>
                                        @endcan
                                    </tr>
                                </thead>
                                {{-- <tbody>
                                    @foreach ($electricities as $electricity)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ getFormatedDate($electricity->date)}}</td>
                                            <td>{{ $electricity->location}}</td>
                                            <td>{{ $electricity->start_unit}}</td>
                                            <td>{{ $electricity->end_unit}}</td>
                                            <td>{{ $electricity->total_units}}</td>
                                            <td>{{ $electricity->waste_units ?? 0}}</td>
                                            <td>{{ $electricity->user->name ?? ''}}</td>
                                            @can('update',new App\Models\Electricity())
                                                <td><a href="{{ route('electricity.edit', ['electricity' => $electricity->id]) }}"><i class="fa fa-edit"></i></a></td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody> --}}
                            </table>
                            @php
                            $query = http_build_query(request()->query());
                            @endphp
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footerScripts')
    <script>
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
                },
            },
            function(start, end) {
                $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
                $('#dateFrom').val(start.format('YYYY-M-DD'));
                $('#dateTo').val(end.format('YYYY-M-DD'));
                $('#date-form').closest('form').submit();
            }
        );

        $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('date-btn', 'date');
            $('#date-form').closest('form').submit();
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }
        $('#data-table').DataTable({
            "order": [
                [0, "desc"]
            ] 
        });

    </script>
    <script>
        $(document).ready(function() {
            //$('.js-example-basic-single').select2();
    
            var dataTable = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('electricity.index',$query) }}",
                    type: "GET",
                    
                },
                columns: [
                    { data: 'date', name: 'date' },
                    { data: 'location', name: 'location' },
                    { data: 'start_unit', name: 'start_unit' },
                    { data: 'end_unit', name: 'end_unit'},
                    { data: 'total_units', name: 'total_units'},
                    { data: 'waste_units', name: 'waste_units'},
                    {
                    data: 'id',
                    name: 'action',
                    // render: function(data, type, row) {
                    //   return '<a href="/roles/' + data + '/edit" class="btn btn-warning btn-sm">Edit</a>'
                    //          '<a href="/roles/' + data + '/destroy" class="btn btn-danger btn-sm">Delete</a>';
                    // //<a href="/roles/${data}" class="btn btn-info btn-sm">Show</a>
                    // }
                    render: function(data, type, row) {
                    var editButton = '<a href="/categories/' + data + '/edit" class="btn btn-warning btn-sm">Edit</a>';
                    var deleteButton = '<a href="/categories/' + data + '/delete" class="btn btn-danger btn-sm">Delete</a>';
                    return editButton + ' ' + deleteButton;
                    }

                }
                    //{ data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
    
            $('#ems').on('change', function () {
                dataTable.ajax.reload();
            });
        });
    </script>
    
@endsection
