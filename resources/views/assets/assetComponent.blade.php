@php

$generator = new Picqer\Barcode\BarcodeGeneratorHTML();

@endphp
<div class="col-md-6" id="{{ $assetAssignment->barcode }}">
    <div class="card card-outline mb-2">
        <div class="card-header bg-primary text-white">
            {{ $assetAssignment->assetSubType->name ?? '' }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive mailbox-messages">
                <table class="table ">
                    <tbody>
                        <tr>
                            <td>Category</td>
                            <td>{{ $assetAssignment->assetSubType->assetType->assetCategory->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>{{ $assetAssignment->assetSubType->assetType->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Barcode</td>
                            <td>{!! $generator->getBarcode("$assetAssignment->barcode", $generator::TYPE_CODE_128) !!}</td>
                        </tr>
                        <tr>
                            <td>Barcode No.</td>
                            <td>{{ $assetAssignment->barcode }}</td>
                        </tr>
                        <tr>
                            <td>Assigned To</td>
                            <td>{{ $assetAssignment->user->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>{{ $assetAssignment->status ?? '' }}</td>
                        </tr>

                        <tr>
                            <td>Assign Date</td>
                            <td>{{ isset($assetAssignment->created_at) ? $assetAssignment->created_at->format('d-m-Y') : '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.card -->
</div>
