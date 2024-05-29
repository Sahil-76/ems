@extends('layouts.master')
@section('headerLinks')
    <style>
        table.dataTable thead .sorting_asc {
            background-image: none !important;
        }

        .table.dataTable thead .sorting_asc {
            background-image: none !important;
        }

        .table {
            overflow: hidden !important;
        }

        .table tr {
            background: transparent !important;
        }

        .table thead th {
            font-size: 13px;
            font-weight: 800;
        }

        .table tbody td {
            font-size: 11px !important;
        }

        .table tfoot th {
            font-size: 14px;
        }

        .table td,
        .table th {
            padding: 5px;
        }

        table.dataTable>thead .sorting:before,
        table.dataTable>thead .sorting:after,
        table.dataTable>thead .sorting_asc:before,
        table.dataTable>thead .sorting_asc:after,
        table.dataTable>thead .sorting_desc:before,
        table.dataTable>thead .sorting_desc:after,
        table.dataTable>thead .sorting_asc_disabled:before,
        table.dataTable>thead .sorting_asc_disabled:after,
        table.dataTable>thead .sorting_desc_disabled:before,
        table.dataTable>thead .sorting_desc_disabled:after {
            font-size: 7px !important;
        }

        table.dataTable>thead>tr>th:not(.sorting_disabled),
        table.dataTable>thead>tr>td:not(.sorting_disabled) {
            padding-right: 20px;
            width: 20% !important;
        }

        .dataTables_wrapper .dataTable thead .sorting:before,
        .dataTables_wrapper .dataTable thead .sorting_asc:before,
        .dataTables_wrapper .dataTable thead .sorting_desc:before,
        .dataTables_wrapper .dataTable thead .sorting_asc_disabled:before,
        .dataTables_wrapper .dataTable thead .sorting_desc_disabled:before {
            bottom: -2px;
        }

        .dataTables_wrapper .dataTable thead .sorting:after,
        .dataTables_wrapper .dataTable thead .sorting_asc:after,
        .dataTables_wrapper .dataTable thead .sorting_desc:after,
        .dataTables_wrapper .dataTable thead .sorting_asc_disabled:after,
        .dataTables_wrapper .dataTable thead .sorting_desc_disabled:after {
            top: -1px;
        }

        .shift-type table.dataTable td,
        .shift-type table.dataTable th {
            padding: 7px 22px;
        }
    </style>
@endsection
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
                                  <label for="responsibleUser">Select Type</label>
                                  {{ Form::select('type_id', $types, request()->type_id ?? null, ['class' => 'form-control selectJS', 'data-placeholder' => 'Select Asset', 'placeholder' => 'Select Asset']) }}
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label for="responsibleUser">Select Status</label>
                                  {{ Form::select('status', $statuses, request()->status ?? null, ['class' => 'form-control selectJS', 'data-placeholder' => 'Select Status', 'placeholder' => 'Select Status']) }}
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
        <div class="col-sm-7">
            <div class="card">
                <div class="card-body">
                    <canvas id="chart1"></canvas>
                </div>
            </div>
        </div>

        <div class="col-sm-5">
            <div class="card" style="min-height: 490px;">
                <div class="card-body">
                    <canvas id="chart2"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-5">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Unassigned Assets</div>
                    <div class="table-responsive">
                        <table class="table table-striped listtable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Department</th>
                                    <th>Manager</th>
                                    <th>Laptop</th>
                                    <th>Mouse</th>
                                    <th>Charger</th>
                                    <th>Headset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $laptopCount = 0;
                                    $mouseCount = 0;
                                    $chargerCount = 0;
                                    $headphoneCount = 0;
                                @endphp
                                @foreach ($departmentUnassignedAssets as $departmentUnassignedAssets)
                                    <tr>
                                        @php
                                            $laptopCount = $laptopCount + $departmentUnassignedAssets->unassignedLaptops;
                                            $chargerCount = $chargerCount + $departmentUnassignedAssets->unassignedCharger;
                                            $headphoneCount = $headphoneCount + $departmentUnassignedAssets->unassignedHeadphn;
                                            $mouseCount = $mouseCount + $departmentUnassignedAssets->unassignedMouse;
                                        @endphp
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $departmentUnassignedAssets->name }}</td>
                                        <td>{{ $departmentUnassignedAssets->deptManager->name ?? 'N/A' }}</td>
                                        <td><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Laptop'], 'unassigned' => 'on', 'department_id' => $departmentUnassignedAssets->id]) }}"
                                                target="_blank">{{ $departmentUnassignedAssets->unassignedLaptops }}</a>
                                        </td>
                                        <td><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Mouse'], 'unassigned' => 'on', 'department_id' => $departmentUnassignedAssets->id]) }}"
                                                target="_blank">{{ $departmentUnassignedAssets->unassignedMouse }}</a></td>
                                        <td><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Charger'], 'unassigned' => 'on', 'department_id' => $departmentUnassignedAssets->id]) }}"
                                                target="_blank">{{ $departmentUnassignedAssets->unassignedCharger }} </a>
                                        </td>
                                        <td><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Headphone'], 'unassigned' => 'on', 'department_id' => $departmentUnassignedAssets->id]) }}"
                                                target="_blank">{{ $departmentUnassignedAssets->unassignedHeadphn }} </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Laptop'], 'unassigned' => 'on']) }}"
                                            target="_blank">{{ $laptopCount }}</a></th>
                                    <th><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Mouse'], 'unassigned' => 'on']) }}"
                                            target="_blank">{{ $mouseCount }}</a></th>
                                    <th><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Charger'], 'unassigned' => 'on']) }}"
                                            target="_blank">{{ $chargerCount }}</th>
                                    <th><a href="{{ route('assignmentList', ['sub_type' => $subTypes['Headphone'], 'unassigned' => 'on']) }}"
                                            target="_blank">{{ $headphoneCount }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-7">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="card-title">Asset Count
                        <span class="float-right">Total Employees:-{{ $totalEmployees }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped listtable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Asset Type</th>
                                    <th>Asset Sub Type</th>
                                    <th>Damaged</th>
                                    <th>Maintenance</th>
                                    <th>Working</th>
                                    <th>Assigned</th>
                                    <th>Unassigned</th>
                                    <th>Total</th>
                                    <th>Required</th>
                                </tr>
                            </thead>
                            <tbody id="myTable">
                                @foreach ($subTypesCount as $subType)
                                    <tr>
                                        @php
                                            $requiredCount = $employeeCount - $subType['workingCount'];
                                        @endphp
                                        @if ($subType['subTypeName'] == 'Charger')
                                            @php $requiredCount = $requiredCount - $havingUPS; @endphp
                                        @endif
                                        @if (in_array($subType['subTypeName'], ['UPS', 'LED', 'Display', 'Printers']))
                                            @php $requiredCount =0; @endphp
                                        @endif
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $subType['assetType'] }}</td>
                                        <td>{{ $subType['subTypeName'] }}</td>
                                        <td><a href="{{ route('asset.index', ['sub_type' => $subType['id'], 'status' => 'Damaged']) }}"
                                                target="_blank">{{ $subType['damagedCount'] }}</a></td>
                                        <td><a href="{{ route('asset.index', ['sub_type' => $subType['id'], 'status' => 'Maintenance']) }}"
                                                target="_blank">{{ $subType['maintenanceCount'] }}</a></td>
                                        <td><a href="{{ route('asset.index', ['sub_type' => $subType['id'], 'status' => 'Working']) }}"
                                                target="_blank">{{ $subType['workingCount'] }}</a></td>
                                        <td><a href="{{ route('asset.index', ['sub_type' => $subType['id'], 'status' => 'Assigned']) }}"
                                                target="_blank">{{ $subType['assignedCount'] }}</a></td>
                                        <td><a href="{{ route('asset.index', ['sub_type' => $subType['id'], 'status' => 'Unassigned']) }}"
                                                target="_blank">{{ $subType['unassignedCount'] }}</a></td>
                                        <td><a href="{{ route('asset.index', ['sub_type' => $subType['id']]) }}"
                                                target="_blank">{{ $subType['totalCount'] }}</a></td>
                                        @if ($requiredCount < 0)
                                            <td>0</td>
                                        @else
                                            <td>{{ $requiredCount }}</a></td>
                                        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        //Bar Chart

        var barChart = '{!! json_encode($barChart) !!}';

        var typeName = [];
        var maintenanceCount = [];
        var damagedCount = [];
        var assignedCount = [];
        var workingCount = [];
        var unassignedCount = [];

        $.each(JSON.parse(barChart), function(index, data) {


            typeName.push(data.subTypeName);
            maintenanceCount.push(data.maintenanceCount);
            damagedCount.push(data.damagedCount);
            assignedCount.push(data.assignedCount);
            workingCount.push(data.workingCount);
            unassignedCount.push(data.unassignedCount);

        });


        const labels = typeName;
        const data = {
            labels: labels,
            datasets: [{
                    label: 'Damaged',
                    backgroundColor: 'rgb(255, 71, 71)',
                    borderColor: 'rgb(255, 71, 71)',
                    data: damagedCount,
                },
                {
                    label: 'Working',
                    backgroundColor: 'rgb(75, 13, 172)',
                    borderColor: 'rgb(75, 13, 172)',
                    data: workingCount,
                },
                {
                    label: 'Assigned',
                    backgroundColor: 'rgb(255, 193, 0)',
                    borderColor: 'rgb(255, 193, 0)',
                    data: assignedCount,
                },
                {
                    label: 'Unassigned',
                    backgroundColor: 'rgb(119, 136, 153)',
                    borderColor: 'rgb(119, 136, 153)',
                    data: unassignedCount,
                },
                {
                    label: 'Maintenance',
                    backgroundColor: 'rgb(116, 166, 126)',
                    borderColor: 'rgb(116, 166, 126)',
                    data: maintenanceCount,
                },

            ],
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {

                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        grid: {
                            display: false,
                        },
                        ticks: {
                            autoSkip: false,
                        }
                    }
                },
                interaction: {
                    intersect: true,
                    mode: 'index'
                },

                plugins: {

                    title: {
                        display: true,
                        text: 'Asset Type'
                    },
                },
                events: ['mousemove', 'click'],


            }
        };

        var myChart = new Chart(
            document.getElementById('chart1'),
            config
        );
        //Piechart
        const assetData = {
            labels: {!! json_encode($pieChartLabels) !!},
            datasets: [{
                // label: 'My First Dataset',
                data: {!! json_encode($pieChartValues) !!},
                backgroundColor: [
                    'rgb(255, 71, 71)',
                    'rgb(75, 13, 172)',
                    'rgb(255, 193, 0)',
                    'rgb(119, 136, 153)',
                    'rgb(116, 166, 126)'
                ],
                hoverOffset: 4
            }]
        };

        const configure = {
            type: 'pie',
            data: assetData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                layouts: {
                    margin: 20
                },
                plugins: {

                    title: {
                        display: true,
                        text: 'Asset Status'
                    },
                },
            }
        };

        var myChart = new Chart(
            document.getElementById('chart2'),
            configure
        );
    </script>
@endsection
