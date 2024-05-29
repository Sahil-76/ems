@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User List</li>
                </ol>
            </nav>
        </div>

        <div class="col-12">
            <div class="card">
                {{ Form::open(['method' => 'GET']) }}
                <div class="card-body">
                    <p class="card-title">Filter</p>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Status</label>
                                {{ Form::select('status', [1 => 'Active', 0 => 'Inactive'], null, ['class' => 'form-control selectJS', 'placeholder' => 'All']) }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            {{ Form::label('last_login', 'Last Login') }}<br>
                            <button type="button" class="btn btn-sm btn-primary" name="daterange" id="lastLoginDate-btn"
                                value="Select Date">
                                @if (!empty(request()->dateFrom) && !empty(request()->dateTo))
                                    <span>
                                        {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y') }}
                                        -
                                        {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span>
                                        <i class="fa fa-calendar"></i> &nbsp;Select Date&nbsp;
                                    </span>
                                @endif
                                <i class="fa fa-caret-down"></i>
                            </button>
                            {{ Form::hidden('dateFrom', request()->dateFrom ?? null, ['id' => 'dateFrom']) }}
                            {{ Form::hidden('dateTo', request()->dateTo ?? null, ['id' => 'dateTo']) }}

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                            <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                        </div>
                    </div>

                </div>
                {{ Form::close() }}
            </div>
        </div>


        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="card-title">User List</p>
                        </div>
                        <div class="col-6">
                            <div class="card-tools float-right">
                                <a href="{{ route('user.create') }}" class="btn btn-primary btn-rounded">Create <i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="example1" style="width: 100%" class="table ">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Role</th>
                                    <th>Last Login</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->is_active)
                                                Active
                                            @else
                                                Inactive
                                            @endif
                                        </td>
                                        <td>{{ implode(', ', $user->roles->pluck('name')->toArray()) }}</td>
                                        <td>{{ $user->last_login_at }}</td>
                                        @can('insert', auth()->user())
                                            <td><a class="mdi mdi-table-edit" style="font-size:20px;border-radius:5px;"
                                                    href="{{ route('user.edit', ['user' => $user->id]) }}"></a></td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footerScripts')
    <script>
        $('#example1').dataTable();

        $('#lastLoginDate-btn').daterangepicker({
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 5 Days': [moment().subtract(4, 'days'), moment()],
                    'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                },
                startDate: moment(),
                endDate: moment()
            },
            function(start, end) {
                $('#lastLoginDate-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
                $('#dateFrom').val(start.format('YYYY-M-DD'));
                $('#dateTo').val(end.format('YYYY-M-DD'));
            }
        );;
        $('#lastLoginDate-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('lastLoginDate-btn', 'date');
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }
    </script>
@endsection
