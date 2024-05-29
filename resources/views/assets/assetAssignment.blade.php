@extends('layouts.master')
@section('headerLinks')
    <style>
        .modal {
            top: 10%;
            left: 40%;
        }
        .modal-sm {
            max-width: 400px;
            max-height: 500px;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employee Profle</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin">
            <div class="col-12 m-auto text-center bg-white">
                <div class="widget-user-image">
                    <br><img class="img-circle elevation-2" height="120px" width="120px" style="border-radius: 100%"
                        src="{{ $employee->user->getImagePath() }}" alt="User Avatar">
                </div>
                <div class="row pt-3 pb-4">
                    <div class="col-sm-12">
                        <div class="description-block">
                            <div class="description-header text-uppercase"><span
                                    class="">{{ $employee->name ?? '' }}</span></div>
                            @if(!auth()->user()->hasRole('BLR IT'))
                            <span class="description-text">{{ $employee->department->name ?? '' }}</span>
                            @endif
                        </div>
                        <!-- /.description-block -->
                        @can('modify', new App\Models\Asset())
                            <button class="m-1 btn btn-sm btn-danger float-lg-right" onclick="changeAction()">Unassign</button>
                        @endcan
                        @can('modify', new App\Models\Asset())
                            <button class="m-1 btn btn-sm btn-primary float-lg-right" onclick="showLogs()">Logs</button>
                        @endcan
                    </div>
                    <!-- /.col -->

                </div>
                <!-- /.row -->
            </div>
            <div class="row my-xl-2" id="employee-assets">
                @if ($employee->user->assetAssignments->isNotEmpty())
                    @foreach ($employee->user->assetAssignments as $assetAssignment)
                        @include('assets.assetComponent')
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="modal modal-sm" id="logs" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog-scrollable" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="font-weight-500">Assignment Logs</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="modalBody">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Updates</h4>
                            <ul class="bullet-line-list">
                                @foreach ($employee->user->assetAssignments as $assignment)
                                    @foreach ($assignment->assetLogs as $log)
                                        <li>
                                            <h6>{{ ucfirst($log->action) ?? '' }}</h6>
                                            @if (!empty($log->reason))
                                                <p><strong>Description: </strong>{{ $log->reason }}</p>
                                            @endif
                                            <p><strong>Action By: </strong>{{ $log->user->name ?? '' }}</p>
                                            @if (!empty($log->assignedTo))
                                                <p><strong>Assigned To: </strong>{{ $log->assignedTo->name ?? '' }}</p>
                                            @endif
                                            <p><strong>Asset: </strong>{{ $log->asset->assetSubType->name ?? '' }} (<a
                                                    href="{{ route('asset.show', $log->asset_id) }}">{{ $log->asset->barcode }}</a>)
                                            </p>
                                            <p class="text-muted mb-4">
                                                <i class="ti-time"></i>
                                                {{ getFormatedDateTime($log->created_at) }}
                                            </p>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footerScripts')
    <script src="{{ url('js/scanner.js') }}"></script>
    <script>
        var biometric_id = "{{ request()->id }}";
        var action = "assign";

        function changeAction() {

            if (confirm('Are you sure ?')) {
                if ($(event.target).html() == "Unassign") {
                    action = "unassign";
                    $(event.target).removeClass('btn-danger').addClass('btn-primary').html('Assign');
                } else {
                    $(event.target).removeClass('btn-primary').addClass('btn-danger').html('Unassign');
                    action = "assign";
                }
            }
        }
        $(document).scannerDetection({
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
            preventDefault: true,

            endChar: [13],
            onComplete: function(assetBarCode, qty) {
                validScan = true;
                // $('#barcode-field').val(barcode);
                barcodeAdded(assetBarCode);
            },
            onError: function(string, qty) {
                res = string.split("-");
                var inward_id = res[0];
                var per_id = res[2];
            }
        });

        function barcodeAdded(assetBarCode) {
            $.ajax({
                url: "{{ route('assetAssign') }}",
                data: {
                    'biometric_id': biometric_id,
                    'assetBarCode': assetBarCode,
                    'action': action,
                },
                type: 'GET',
                success: function(response) {
                    if (response.message == 'false') {
                        alert("Asset not found");
                        return false;
                    }
                    if (action == 'assign') {

                        if (response.message == 'Asset assigned') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                        $('#employee-assets').append(response.view);
                    } else {
                        $('#employee-assets').find(`#${assetBarCode}`).remove();
                        toastr.success(response.message);
                    }
                },
                error: function(error) {
                    // console.log(error);
                }
            });
        }

        function showLogs() {
            $('#logs').modal('show');
        }
    </script>
@endsection
