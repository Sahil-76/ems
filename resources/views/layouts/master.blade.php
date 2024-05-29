<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Management System</title>
    <link rel="icon"       href="{{ url('img/favicon.ico') }}" sizes="16x16">
    <link rel="stylesheet" href="{{ url('skydash/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ url('css/tail.select.css') }}">
    <link rel="stylesheet" href="{{ url('css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('skydash/js/select.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link type="text/css"  rel="stylesheet" href="{{ url('js/jsgrid/jsgrid.min.css') }}" />
    <link type="text/css"  rel="stylesheet" href="{{ url('js/jsgrid/jsgrid-theme.min.css') }}" />
    <link rel="stylesheet" href="{{ url('js/toastr/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

    <style>

        .navbar.navbar-info .navbar-menu-wrapper {
            background: #4B49AC;
            border-bottom: 1px solid white !important;
        }
        a:hover {
            text-decoration: none;
        }
        .hidden {
            display: none;
        }
        .notification-bell {
            position: absolute;
            left: 57%;
            width: 27px;
            height: 23px;
            border-radius: 100%;
            background: #4B49AC;
            top: -3px;
            border: 1px solid #ffffff;
            color: blanchedalmond;
            font-size: small;
        }
        .carousel-item {
            height: 50px;
        }

        .settings-panel {
            right: -380px;
            width: 380px !important;
        }

    </style>
    @yield('headerLinks')

</head>

<body class="sidebar-light">
    @php
        $commonCount    =   commonCount();
        // $lateTiming     =   lateTiming();
        // $itCount=itTicketCount() ;
    @endphp

    @if (url('') == 'http://ems.tka-in.com' || url('') == 'https://ems.tka-in.com')
        @php
            $hidden = 'hidden';
        @endphp
    @else
        @php
            $hidden = '';
        @endphp

    @endif

    <div class="container-scroller">
        <nav id="header" class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="{{ route('dashboard') }}">EMS</a>
                <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">EMS</a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu" id="iconMenu"></span>
                </button>

                <ul class="navbar-nav navbar-nav-right">
                    @php
                        $annoucements = getAnnouncements();
                    @endphp
                    @if ($annoucements->isNotEmpty())
                        <li class="nav-item nav-profile"><span class="btn btn-sm btn-warning mt-1"
                                id="annoucement">Announcements</span></li>
                    @endif
                    <li class="nav-item dropdown">
                        @if (!Session::has('orig_user'))
                            @if (in_array(strtolower(auth()->user()->email), App\User::$developers))
                                <li class="nav-item">
                                    <a href="{{ route('switchUser') }}" class="nav-link">Switch User</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('swithLogout') }}">
                                    <span class="">Return Back
                                    </span>
                                </a>
                            </li>
                        @endif
                    </li>

                    <li class="nav-item dropdown mt-1">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown">
                            <i class="fas fa-moon mx-0 theme-change"></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown mt-1">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown">
                            <i class="fas fa-running mx-0"></i>
                        </a>
                        <span class="badge badge-danger mb-3" id="leave-count">0</span>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            id="leave-notification" style="min-width: 290px;" aria-labelledby="notificationDropdown">
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown">
                            <i class="icon-bell mx-0"></i>
                            <span class="notification-bell" style="background: #FF4747;border:#FF4747" id="count"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" id="notification"
                            style="min-width: 290px;" aria-labelledby="notificationDropdown">
                        </div>
                    </li>

                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            @if (!empty(auth()->user()->employee))
                                <img src="{{ auth()->user()->getImagePath() }}" alt="{{ auth()->user()->name }}" />
                            @else
                                <img src="404" alt="">
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ route('changePassword') }}">
                                <i class="ti-settings text-primary"></i>
                                Change Password
                            </a>

                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="ti-power-off text-primary"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>

                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            @include('layouts.annoucement')

            @include('layouts.sidebar')

            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>

                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            EMS developed in
                            July,2021.
                        </span>
                    </div>
                </footer>
            </div>
            <div class="modal hide fade" id="modal-default" aria-labelledby="ModalLabel" aria-hidden="true"
                role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">

                            <h5 class="modal-title" id="chart-heading"></h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div id="modal-data"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ url('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('skydash/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ url('skydash/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ url('skydash/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ url('skydash/js/dataTables.select.min.js') }}"></script>
    <script src="{{ url('skydash/js/off-canvas.js') }}"></script>
    <script src="{{ url('skydash/js/hoverable-collapse.js') }}"></script>
    <script src="{{ url('skydash/js/template.js') }}"></script>
    <script src="{{ url('skydash/js/settings.js') }}"></script>
    <script src="{{ url('skydash/js/todolist.js') }}"></script>
    <script src="{{ url('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ url('js/tail.select.js') }}"></script>
    <script src="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ url('skydash/js/dashboard.js') }}"></script>
    <script src="{{ url('skydash/js/Chart.roundedBarCharts.js') }}"></script>
    <script src="{{ url('skydash/vendors/typeahead.js/typeahead.bundle.min.js') }}"></script>
    <script src="{{ url('skydash/vendors/select2/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/jsgrid/jsgrid.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/common.js') }}"></script>
    <script src="{{ url('js/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.magnific-popup/1.0.0/jquery.magnific-popup.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>

        $(document).ready(function() {
            var announcements   =   "{{$annoucements->isNotEmpty()}}";
            var isHr            =   "{{!auth()->user()->hasRole('HR')}}";
            if (sessionStorage.getItem('theme') != null) {
                if (sessionStorage.getItem('theme') == 'dark') {
                    setDarkTheme();
                } else {
                    setLightTheme();
                }
            }

            if(sessionStorage.getItem('announcement_seen'))
            {
                $('#theme-settings').removeClass("open");
            }
            else if(announcements && isHr)
            {
                $('#theme-settings').addClass("open");
            }

            $(".gallery").magnificPopup({
                type: "image",
                delegate: ".employee-image",
                mainClass: 'mfp-main',
                
            });
        });

        $('.theme-change').on('click', function() {
            if ($('.theme-change').hasClass('fa-moon')) {
                setDarkTheme();
                sessionStorage.setItem("theme", "dark");
            } else {
                setLightTheme();
                sessionStorage.setItem("theme", "light");
            }
        });

        $('#settings-close').on('click',function(){
            sessionStorage.setItem("announcement_seen",1);
        })

        function setDarkTheme() {
            $('.theme-change').removeClass('fa-moon').addClass('fa-sun').css('color', 'yellow');
            $('body').addClass('sidebar-dark').removeClass('sidebar-light');
            $('.navbar-brand').css('color', 'white');
            $('nav').addClass('navbar-info');
        }

        function setLightTheme() {
            $('.theme-change').removeClass('fa-sun').addClass('fa-moon').css('color', '');
            $('body').addClass('sidebar-light').removeClass('sidebar-dark');
            $('.navbar-brand').css('color', '#4747A1');
            $('nav').removeClass('navbar-info');
        }

        $(function() {
            @if ($message = Session::get('success'))
                toastr.success('{{ $message }}');
            @endif
            @if ($message = Session::get('failure'))
                toastr.warning('{{ $message }}');
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.warning('{{ $error }}');
                @endforeach
            @endif
        });
        tail.select(".tail-select", {
            search: true,
            multiShowCount: true,
            multiSelectAll: true,
            multiPinSelected: false,
            width: '350px',
            placeholder: 'select an option'
        });
        var clearNotification = "{{ route('clearNotification') }}";
        // var notification = setInterval(notifications, 60000);
        // notifications();

        window.onload  = function notifications() {
            $.ajax({
                url: "{{ route('getNotification') }}",
                type: 'get',
                dataType: 'json',
                success: function(response) {

                    var html = "";
                    var leaveNotifications = "";
                    var leaveCount = 0;
                    var notificationCount = 0;
                    if (response.count == 0) {
                        html += `<li class="list text-center">
                    <h6>No Notification</h6>
                    </li>`
                        leaveNotifications = html;
                    }

                    if (response.count >= 1) {
                        html += `<div class="overflow-auto" style="max-height:250px">`;
                        leaveNotifications += `<div class="overflow-auto" style="max-height:250px">`;
                        $.each(response.notifications, function(index, notification) {
                            if (notification.type != 'leave') {
                                notificationCount = notificationCount + parseInt(1);
                                html += `<div onclick="setReadAt('` + notification.id + `','` +
                                    notification.link + `')" class="dropdown-item preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div>

                              </div>
                            </div>
                              <div class="preview-item-content">
                              <h6 class="preview-subject font-weight-normal">${notification.message}</h6>
                              <p class="font-weight-light small-text mb-0 text-muted">
                              <small class="pull-right"><i class="mdi mdi-clock"></i> ${notification.time}</small>
                              </p>
                              </div>

                          </div>`
                            } else {
                                leaveCount = leaveCount + parseInt(1);
                                leaveNotifications += `<div onclick="setReadAt('` + notification.id +
                                    `','` + notification.link + `')" class="dropdown-item preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div>

                              </div>
                            </div>
                              <div class="preview-item-content">
                              <h6 class="preview-subject font-weight-normal">${notification.message}</h6>
                              <p class="font-weight-light small-text mb-0 text-muted">
                              <small class="pull-right"><i class="mdi mdi-clock"></i> ${notification.time}</small>
                              </p>
                              </div>

                          </div>`
                            }
                        });
                        if (notificationCount != 0) {
                            if (notificationCount > 1) {
                                html =
                                    `<center>You have ${notificationCount} new notifications</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications()">X</i>` +
                                    html;
                            } else {
                                html =
                                    `<center>You have ${notificationCount} new notification</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications()">X</i>` +
                                    html;
                            }
                            html += `</div>`;
                        } else {
                            html += `<li class="list text-center">
                          <h6>No Notification</h6>
                          </li>`;
                        }
                        if (leaveCount != 0) {
                            if (leaveCount > 1) {
                                leaveNotifications =
                                    `<center>You have ${leaveCount} new notifications</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications('leave')">X</i>` +
                                    leaveNotifications;
                            } else {
                                leaveNotifications =
                                    `<center>You have ${leaveCount} new notification</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications('leave')">X</i>` +
                                    leaveNotifications;
                            }
                            leaveNotifications += `</div>`;
                        } else {
                            leaveNotifications += `<li class="list text-center">
                          <h6>No Notification</h6>
                          </li>`
                        }
                    }
                    $('#notification').html(html);
                    $('#leave-notification').html(leaveNotifications);
                    $('#count').html(notificationCount);
                    $('#leave-count').html(leaveCount);
                },
                error: function() {
                    clearInterval(notification);
                },
            });
        }

        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);

            toastr.success('Link Copied');
        }

        function clearNotifications(type = null) {
            console.log(type);
            $.ajax({
                url: clearNotification,
                type: 'get',
                data: {
                    type: type
                },
                success: function(response) {
                    location.reload();
                },
                error: function(error) {
                    console.log(error);
                },
            });
        }

        function setReadAt(notification_id, link) {
            $.ajax({
                url: "{{ route('notificationReadStatus', '') }}" + "/" + notification_id,
                type: 'get',
                success: function(response) {
                    window.location.href = link;
                },

            })
        }

        function deleteItem(path, method = 'DELETE') {
            var sure = confirm('Are you sure?');
            if (!sure) {
                return false;
            }
            $.ajax({
                url: path,
                type: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response && response.success) {
                        toastr.success(response.success);
                    } else {
                        toastr.success('Successfully Done.');
                    }
                    location.reload();
                },
                error: function(response) {
                    if (response.status == '404') {
                        alert("Item not found");
                    } else {
                        alert(response.statusText);
                    }
                }
            });
            return true;
        }

        $(document).ready(function()
        {
            $('#annoucement').on('click', function() {
                $('#settings-trigger').trigger('click');
            });
            $('.selectJS').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });
            if (sessionStorage.getItem("sidebar")) {
                if (sessionStorage.getItem("sidebar") == 'dark') {
                    $('#sidebar-light-theme').removeClass('selected');
                    $('.navbar-brand').css('color', 'white');
                    $('body').addClass('sidebar-dark');
                } else {
                    $('#sidebar-dark-theme').removeClass('selected');
                    $('body').addClass('sidebar-light');
                    $('.navbar-brand').css('color', '#4747A1');
                }
                $('#sidebar-' + sessionStorage.getItem("sidebar") + '-theme').addClass('selected');
            }
            if (sessionStorage.getItem("header")) {
                $('#header').addClass('navbar-' + sessionStorage.getItem("header"));
            }

            setInterval(function checkSession() {
                $.getJSON('/check-session', function(data) {
                    if (data.guest) {
                        location.reload(true);
                    }
                });
            }, 60000);
        });

        function themeColor(color, section) {
            sessionStorage.setItem(section, color);
        }

        $("#jsGrid").jsGrid({
            pageButtonCount: 4,
            pagerFormat: "Pages: {first} {prev} {pages} {next} {last}    {pageIndex} of {pageCount}",
            pagePrevText: "Prev",
            pageNextText: "Next",
            pageFirstText: "First",
            pageLastText: "Last",
            pageNavigatorNextText: "...",
            pageNavigatorPrevText: "..."
        });

        $("form[method='GET']").submit(function() {
            $("input,select").each(function(index, input) {
                if ($(input).val() == "") {
                    $(input).attr("name", '');
                }
            });
        });

    </script>
    @yield('footerScripts')

</body>
</html>
