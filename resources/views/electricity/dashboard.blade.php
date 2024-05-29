@extends('layouts.master')

@section('content')
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
                              {{ Form::select('location', $locations, $location, ['class' => 'form-control selectJS', 'data-placeholder' => 'Select Location', 'placeholder' => 'Select Location']) }}
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
                  </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
    <div class="row mt-3">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="chart1"></canvas>
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
        const labels = {!! json_encode($labels) !!};
        const data = {
            labels: labels,
            datasets: [{
                label: 'Electricity Consumption',
                data: {!! json_encode($values) !!},
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 1,

            }]
        };
        const config = {
            type: 'line',
            data: data,
            options: {
    responsive: true,
    hover: {
      mode: 'index',
      intersec: false
    },
    scales: {
      x: {
        title: {
          display: true,
          text: 'Month'
        }
      },
      y: {
        title: {
          display: true,
          text: 'Value'
        },
        min: 0,
        max: 100,
        ticks: {
          // forces step size to be 50 units
          stepSize: 50
        }
      }
    }
  },
        };
        var myChart = new Chart(
            document.getElementById('chart1'),
            config
        );
    </script>
@endsection
