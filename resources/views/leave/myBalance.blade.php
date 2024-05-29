@extends('layouts.master')
@section('headerLinks')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
<style>
    .tooltip-inner {
        background: rgba(208, 217, 242);
        color: black;
        border-radius: 5px;
        box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.5);
    }
    .bs-tooltip-top .arrow::before, .bs-tooltip-auto[x-placement^="top"] .arrow::before {
    top: 0;
    border-width: 0.4rem 0.4rem 0;
    border-top-color: rgba(208, 217, 242);
    }
    .fc-day-top{
        cursor: pointer;
    }
    .fc-content{
        font-size:10px;
        text-align: center;
    }
    .fc-event-container{
        height:20px;
        width:100px;
    }
    .myBalance-Table > tr:first-child{
        display: block;
        float: left;
    }
    .myBalance-Table > tr{
        display: block;
    }
    .myBalance-Table > tr > td {
        display: block;
    }
    .myBalance-Table > tr:nth-child(2)  {
        text-align: center;
    }
    .modal-content {
    width: inherit;
    border:2px solid #4B49AC;
    border-radius: 5px;
    }
    .modal .modal-dialog {
    width: 100%;
    max-width: 800px;
    position: relative;
    margin: 20px auto auto;
}

.modal .modal-dialog .modal-content .modal-body {
    padding: 5px;
}
#loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    background: rgba(0, 0, 0, 0.01) no-repeat center center;
    z-index: 99999;
}
.dot-opacity-loader {
    position: absolute;
    top:0;
    bottom: 0;
    left: 4%;
    right: 0;

    margin: auto;
}
</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-title">Leave Dashboard <span class="float-right" id="calendar-name"></span></div>
                    <div id="calendar"></div>
                </div>
            </div>
            </div>
        <div class="col-md-5" id="balance-chart"></div>
    </div>

            <div class="modal fade" id="openPopup" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body" id="modalBody">

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="complaint-popup" style="display: none;">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Raise Balance Query</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('leaveBalanceComplaintRaise')}}" method="post">
                            @csrf
                            <input type="hidden" id="leave_balance_id" name="leave_balance_id">
                      <textarea required placeholder="Type here" name="description" class="form-control" cols="15" rows="4"></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-success btn-sm">Submit</button>
                    </div>
                </form>
                  </div>
                </div>
              </div>

@endsection
@section('footerScripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>

<script>
          $(window).on('load', function() {
            $.ajax({
                url: "{{ route('getBalance', http_build_query(request()->query())) }}",
                type: 'GET',
                success: function(response) {
                    var employeeLeaves = response.employeeLeaves;
                    var route = response.route;
                    makeCalendar(employeeLeaves,route);
                    $('#balance-chart').find('#balance-form').remove();
                    $('#balance-chart').append(response.balanceChart);
                    $('#calendar-name').html(response.user.name);
                    $('#modalBody').append(response.leaveForm);
                }
            });
        });

        function makeCalendar(employeeLeaves,route) {
                var year = new Date(new Date().getFullYear(), 0, 1);
                const startOfMonth = moment().clone().startOf('month').format('YYYY-MM-DD');
                let splitDate      = startOfMonth.split("-");
                if(splitDate[1]=="12")
                {
                    var endDateMoment   =   moment(moment().add("1","y").clone().startOf('year').format('YYYY-MM-DD'));
                    endDateMoment.add(2, 'months');
                }
                else
                {
                    var endDateMoment = moment(startOfMonth);
                        endDateMoment.add(2, 'months');
                }

                var calendar = $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'month,agendaWeek'
                },
                validRange: {
                start: year,//start date here
                end: endDateMoment //end date here
                },
                contentHeight:"auto",
                events: employeeLeaves,
                eventClick: function(event) {
                    var url = event.url;
                    var start = event.start.format('Y-MM-DD');
                        window.open([url+'?dateFrom='+start+"&dateTo="+start], "_blank");
                        return false;
                },
                eventRender: function(event, element) {
                    event.allDay = false;
                    element.find(".fc-title").css("color", "black");
                    $(element).tooltip({
                    title: "<b>Leave Type : </b>"+ event.type +'<br />'+ "<b>Status : </b>" + event.status,
                    html: true,
                    container: "body",
                });
                },
                longPressDelay: 0,
                selectable: true,
                selectHelper: true,
                select: function (start, end, allDay) {
                    var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
                    var end = $.fullCalendar.formatDate(end, "Y-MM-DD");
                    var date=new Date(end);
                    date.setDate(date.getDate() - 1);
                    var endDate = JSON.stringify(date);
                    endDate = endDate.slice(1,11);
                    var today = moment().format("Y-MM-DD");
                    var lastDate = moment().endOf('month').format("Y-MM-DD");
                    if(start >= today)
                    {
                        $('#from_date').val(start);
                        $('#to_date').val(endDate);
                        $('#openPopup').modal('show');

                        checkDate();

                        let from_date = $('#from_date').val().split("-");
                        let to_date_max = '';
                        if (from_date[2] < 21) {
                            to_date_max = from_date[0] + '-' + from_date[1] + '-' + '20';
                        }
                        maxDate();
                        if(endDate <= to_date_max)
                        {
                        $('#to_date').val(endDate);
                        }
                        else if(start > to_date_max && endDate <= lastDate)
                        {
                        $('#to_date').val(endDate);
                        }
                        else if(start <= to_date_max && endDate > to_date_max)
                        {
                            toastr.error('Select From Date and To Date Either after or before cutoff date');
                        }
                        toDateValidation();
                    }
                    else{
                        var format_date = moment().format("D-MMMM");
                        displayMessage('Select Date '+format_date+' Or greater than '+format_date);
                    }
                },
            });
            }
    function displayMessage(message) {
        toastr.success(message, 'Message');
    }

    var tglCurrent = $('#calendar').fullCalendar('getDate');
            $('body').on('click', 'button.fc-prev-button', function() {
                var tglCurrent = $('#calendar').fullCalendar('getDate');
                var date = formatDate(tglCurrent._d);
                getLeaveBalance(date);

            });

            $('body').on('click', 'button.fc-next-button', function() {
                var tglCurrent = $('#calendar').fullCalendar('getDate');
                var date = formatDate(tglCurrent._d);
                    getLeaveBalance(date);
            });
            function formatDate(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2);
                    return [date.getFullYear(), mnth].join("-");
            }

            function getLeaveBalance(date) {
                $.ajax({
                    url: "{{ route('getBalance', http_build_query(request()->query())) }}",
                    type: 'GET',
                    data: {
                        month: date
                    },
                    success: function(response) {
                        $('#balance-chart').find('#balance-form').remove();
                        $('#balance-chart').append(response.balanceChart);
                        $('#calendar-name').html(response.user.name);
                    }
                });
            }

</script>
<script>
    var lastDay = function(y, m) {
        return new Date(y, m + 1, 0).getDate();
    }
    $('#from_date,#to_date').change(function() {
        if ($('#to_date').val() != '' && $('#from_date').val() != '') {
            let from_date = $('#from_date').val().split("-");
            let to_date = $('#to_date').val().split("-");
            if (from_date[0] == to_date[0] && from_date[1] == to_date[1]) {
                $('#submit').prop('disabled', false);
            } else {
                $('#submit').prop('disabled', true);
                alert('Please select date of same month');
            }


        }


    });
    // code for minimum validation of date apply format
    $('#from_date').change(function() {
        if ($('#from_date').val() != '') {
            let from_date = $('#from_date').val().split("-");
            let to_date_max = '';
            if (from_date[2] < 21) {
                to_date_max = from_date[0] + '-' + from_date[1] + '-' + '20';
            } else {
                let last_day = lastDay(from_date[0], from_date[1] - 1);
                to_date_max = from_date[0] + '-' + from_date[1] + '-' + last_day;
            }
            $('#to_date').val('').prop('max', to_date_max);

        }
    });
    $('input:checkbox').change(function() {
        if ($('#halfDayType').is(':checked')) {
            $('#leave-type').show().find('input:radio').prop('checked', false).prop('required', true);
        } else {

            $('#leave-type').hide().find('input:radio').prop('checked', false).removeAttr('required');
        }
        if ($('#short-leave-type').is(':checked')) {
            $('#short-leave-timing').show();
            $('.selectJS').select2({
                placeholder: "Select an option",
                allowClear: true
            });
            $('#leave-type').hide().find('input:radio').prop('checked', false);
        } else {

            $('#short-leave-timing').hide();
        }

    });

    function raiseComplaint(balance_id)
    {
        $('#complaint-popup').modal('show');
        $('#complaint-popup').find("#leave_balance_id").val(balance_id);
    }
</script>
@endsection


