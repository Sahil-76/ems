@extends('layouts.master')
@section('headerLinks')
    <style>
        table.dataTable thead .sorting_asc {
            background-image: none !important;
        }
    </style>
@endsection
@section('content')
    @php
        $lateTiming     =   lateTiming();
    @endphp
    <div class="container-scroller">
        <div id="carouselExampleIndicators" class="carousel slide m-2" data-bs-ride="carousel">
            <div class="carousel-inner" style="border-radius: 5px;">
                <div class="carousel-itemactive">
                    @if ($lateTiming &&
                        !auth()->user()->hasRole('admin'))
                        <marquee behavior="alternate" scrollAmount="12" style="font-size:28px">
                            {{ ucwords($lateTiming) }}</marquee>
                    @endif
                    @if (departmentAttendance() &&
                        Route::currentRouteName() != 'lateAttendanceDashboard' &&
                        auth()->user()->hasRole('manager'))
                        <marquee behavior="alternate" scrollAmount="12" style="font-size:28px"
                            class="text-danger">
                            <a href="{{ route('lateAttendanceDashboard') }}" style="color:red">
                                {{ departmentAttendance() }}
                            </a>
                        </marquee>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin transparent">
            <div class="row">
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <a style="color: white;" href="{{ route('employeeView') }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="mdi mdi-account"></i> Total Employee</p>
                                    <p class="fa-3x mb-2">{{ $hr['employeeCount'] }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <a style="color: white;" href="{{ route('exitList') }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="mdi mdi-account-multiple"></i> Exit Employee</p>
                                    <p class="fa-3x mb-2">{{ $hr['in_active'] }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan

                @if (auth()->user()->hasRole('employee') ||
                    auth()->user()->hasRole('admin'))
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <a style="color: white;" href="{{ route('myAttendance') }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="fa fa-user-circle"></i> Punch In Today
                                        ({{ Carbon\Carbon::now()->format('l') }})</p>
                                    @if (!empty($todayAttendance->punch_in))
                                        <p class="fa-2x mb-2">
                                            {{ Carbon\Carbon::createFromFormat('H:i:s', $todayAttendance->punch_in)->format('g:i:s A') }}
                                        </p>
                                    @else
                                        <p class="fa-2x mb-2">N/A</p>
                                    @endif

                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <a style="color: white;" href="{{ route('myBalance') }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="fa fa-balance-scale"></i> Leave Balance
                                        ({{ Carbon\Carbon::now()->format('F') }})</p>
                                    <p class="fa-3x mb-2">{{ $myBalance->balance ?? 0 }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif

                @can('leaveDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent" id="today-leave-list">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-walk"></i> Employees On Leave</p>
                                <p class="fa-3x mb-2">{{ count($leaveDashboard['leaves']) }}</p>
                            </div>
                            </a>
                        </div>
                    </div>
                @endcan
                {{-- @can('managerDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-alert-octagon"></i> Pending Entity Request</p>
                                <p class="fa-3x mb-2">{{ $manager['entityRequestCount'] }}</p>

                            </div>
                        </div>
                    </div>
                @endcan --}}

                {{-- @can('itDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-blue">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-television-guide"></i> Entity</p>
                                <p class="fa-3x mb-2">{{ $it['entityCount'] }}</p>

                            </div>
                        </div>
                    </div>
                @endcan --}}
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-danger">
                            <a style="color: white;" href="{{ route('hr.departmentList') }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="mdi mdi-briefcase"></i> Departments</p>
                                    <p class="fa-3x mb-2">{{ $hr['department'] }}</p>

                                </div>
                            </a>
                        </div>
                    </div>
                @endcan


                {{-- @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <a style="color: white;" href="{{route('hrRaiseTicket')}}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-ticket"></i>HR Opened Tickets</p>
                                <p class="fa-3x mb-2">{{$it['ticketCount']}}</p>

                            </div>
                        </div>
                    </div>
                @endcan --}}
                {{-- @if (auth()->user()->can('ItTicketDashboard', new App\Models\Ticket()) &&
    auth()->user()->hasRole('IT'))
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <a style="color: white;" href="{{route('ticketDashboard')}}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-ticket"></i> IT Opened Tickets</p>
                                <p class="fa-3x mb-2">{{$it['ticketCount']}}</p>

                            </div>
                        </div>
                    </div>
                @endif --}}
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <a style="color: white;" href="{{ route('pendingProfile') }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="mdi mdi-alert-octagon"></i> Pending Employee's Profile</p>
                                    <p class="fa-3x mb-2">{{ $hr['profilesPendingCount'] }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <a style="color: white;" href="{{ route('recent-joined') }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="fa fa-user-circle"></i> Recently Joined</p>
                                    <p class="fa-3x mb-2">{{ $recentJoining }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan


                @if (auth()->user()->hasRole('employee'))
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-blue">
                            <a style="color: white;"
                                href="{{ route('leaveList', ['dateFrom' => $start, 'dateTo' => $end]) }}">
                                <div class="card-body">
                                    <p class="mb-4"><i class="mdi mdi-walk"></i> My Leaves
                                        ({{ Carbon\Carbon::now()->format('F') }})</p>
                                    <p class="fa-3x mb-2">{{ $myLeaveDashboard['totalLeaves'] }} </p>
                                </div>
                            </a>
                        </div>

                    </div>

                    {{-- <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-dark-blue">
                    <a style="color: white;" href="{{route('myTickets')}}">
                      <div class="card-body">
                        <p class="mb-4"><i class="mdi mdi-ticket"></i> My Opened Tickets </p>
                        <p class="fa-3x mb-2">{{$employees['ticketCount']}} </p>
                      </div>
                    </a>
                  </div>
                </div> --}}
                @endif

                {{-- @can('powerUser', new App\User())
                  <div class="col-md-3 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                      <a style="color: white;" href="{{route('departmentTickets')}}">
                        <div class="card-body">
                          <p class="mb-4"><i class="mdi mdi-ticket"></i> Department Tickets </p>
                          <p class="fa-3x mb-2">{{$departmentTicketCount}}  </p>
                        </div>
                      </a>
                    </div>
                  </div>
                @endcan --}}
            </div>


        </div>
    </div>


    @can('leaveDashboard', new App\User())
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class=" col-6">
                                <h4 class="">Employees on leave</h4>
                            </div>
                            @can('hrDashboard', new App\User())
                                <div class=" col-6 float-right">
                                    {{ Form::open(['method' => 'GET', 'id' => 'date-form', 'class' => 'float-lg-right']) }}

                                    <button type="button" class="btn btn-sm btn-primary" id="date-btn" value="Select Date">
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

                                    {{ Form::close() }}
                                </div>
                            @endcan
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="display expandable-table" id="leave-list" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Biometric Id</th>
                                                <th>Department</th>
                                                <th>Shift Type</th>
                                                <th>Leave Session</th>
                                                <th>Timing</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTable">
                                            @foreach ($leaveDashboard['leaves'] as $leave)
                                                @php
                                                    $color = 'table-success';
                                                    if ($leave->leave_status == 'Pre Approved') {
                                                        $color = 'table-success';
                                                    } elseif ($leave->leave_status == 'Approved') {
                                                        $color = 'table-danger';
                                                    } elseif ($leave->leave_status == 'Absent') {
                                                        $color = 'table-active';
                                                    }
                                                    $leave_session = '';
                                                    switch ($leave->leave_session) {
                                                        case 'Second half':
                                                            $leave_session = $leave->mid_time . '-' . $leave->end_time;
                                                            break;
                                                        case 'First half':
                                                            $leave_session = $leave->start_time . '-' . $leave->mid_time;
                                                            break;
                                                        case 'Full day':
                                                            $leave_session = $leave->start_time . '-' . $leave->end_time;
                                                            break;

                                                        default:
                                                            # code...
                                                            break;
                                                    }
                                                @endphp
                                                <tr class="{{ $color }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $leave->name }}</td>
                                                    <td>{{ $leave->biometric_id }}</td>
                                                    <td>{{ $leave->department_name }}</td>
                                                    <td>{{ $leave->shift_types_name }}</td>
                                                    <td>{{ $leave->leave_status != 'Absent' ? $leave->leave_session : 'Absent' }}
                                                    </td>
                                                    <td>{{ getFormatedTime($leave_session) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    @if (!empty($employeeBirthday) && empty($employeeBirthday->birthday_reminder))
        <div class="swal-overlay swal-overlay--show-modal" id="birthday" tabindex="-1">
            <div class="swal-modal" role="dialog" aria-modal="true">
                <div>
                    {{-- <span class="swal-icon--success__line swal-icon--success__line--long"></span>
          <span class="swal-icon--success__line swal-icon--success__line--tip"></span>
          <div class="swal-icon--success__ring"></div>
          <div class="swal-icon--success__hide-corners"></div> --}}
                    <img style="max-height:200px;" src="{{ url('img/birthday.gif') }}" alt="">
                </div>
                <div class="swal-title" style="">Happy Birthday!</div>
                <div class="swal-text" style="">{{ $employeeBirthday->name }}</div>
                <div class="swal-footer">
                    <div class="swal-button-container">
                        <button class="swal-button swal-button--confirm"
                            onclick="setReadOn('{{ $employeeBirthday->id }}')">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- popup for late and on time --}}
    
    @if (!empty($todayAttendance) &&
        $todayAttendance->seen_at == 0 &&
        !request()->session()->has('orig_user'))
        @if (empty($lateTiming))
            <div class="swal-overlay swal-overlay--show-modal" id="time">
                <div class="swal-modal" role="dialog" aria-modal="true" style="width:510px;">
                    <button type="button" class="close mr-2" onMouseOver="this.style.color='red'"
                        onMouseOut="this.style.color='#000'" data-dismiss="modal" aria-label="Close"
                        onclick="updateSeen('{{ $todayAttendance->id }}')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div>
                        <img style="object-fit: contain; padding: 5px;" src="{{ url('img/on_time.gif') }}"
                            alt="">
                    </div>
                    <div class="swal-title" style="">You are on Time</div>
                </div>
            </div>
        @elseif(!auth()->user()->hasRole('admin'))
            <div class="swal-overlay swal-overlay--show-modal" id="time">
                <div class="swal-modal" role="dialog" aria-modal="true" style="width:510px;">
                    <button type="button" class="close mr-2" onMouseOver="this.style.color='red'"
                        onMouseOut="this.style.color='#000'" data-dismiss="swal-modal" aria-label="Close"
                        onclick="updateSeen('{{ $todayAttendance->id }}')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div>
                        <img style="object-fit: contain; padding: 5px;" src="{{ url('img/late.gif') }}" alt="">
                    </div>
                    <div class="swal-title" style="">{{ auth()->user()->name }}</div>
                </div>
            </div>
        @endif
    @endif


    @php
        $fromDate = request()->dateFrom ?? '';
        $toDate = request()->dateTo ?? '';
    @endphp
@endsection

@section('footerScripts')
    {{-- <script src="{{url('skydash/js/chart.js')}}"></script> --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function setReadOn(id) {
            $('#birthday').hide();
            $.ajax({
                url: "{{ route('setBirthdayReadOn', '') }}" + '/' + id,
                method: 'GET'
            });
        }
        var table = $('#leave-list').DataTable({
            "stripeClasses": []
        });
        $('.fill').change(function() {
            let filterString = $(this).data('filter');
            if (filterString == 'none') {
                $('#leave-list #myTable tr').show();
                return false;
            }
            $('#leave-list #myTable tr').show();
            $('#leave-list #myTable').find(`tr[class!="${filterString}"]`).hide();
        });


        $("#today-leave-list").click(function() {
            animateTable();
        });
        @if (request()->today_table)
            animateTable();
        @endif

        function animateTable() {
            $('html, body').animate({
                scrollTop: $('#leave-list').offset().top
            }, 'slow');
        }

        function updateSeen(id) {
            $('#time').hide();
            $.ajax({
                url: "update/seen/" + id,
                method: 'GET'
            });
        }

        if ('{{ $fromDate }}' != '') {
            var startDate = moment('{{ $fromDate }}');
            var endDate = moment('{{ $toDate }}');
        } else {
            var startDate = moment();
            var endDate = moment();
            $('#date-btn span').html(startDate.format('D/ M/ YY') + ' - ' + endDate.format('D/ M/ YY'));
        }
        $('#date-btn').daterangepicker({
                showOn: "button",
                showButtonPanel: true,
                buttonImageOnly: true,
                buttonText: "",
                showButtonPanel: true,
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 5 Days': [moment().subtract(4, 'days'), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                },
                startDate: startDate,
                endDate: endDate,
            },
            function(start, end) {
                if (start.format('YYYY-M-DD') == moment().format('YYYY-M-DD') && end.format('YYYY-M-DD') == moment()
                    .format('YYYY-M-DD')) {
                    clearDateFilters('date-btn', 'date');
                    $('#date-form').closest('form').submit();

                }
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
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i> &nbsp;Filter Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }
    </script>
@endsection
