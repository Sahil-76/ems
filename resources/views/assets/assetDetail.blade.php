@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asset Detail</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-5">
                <div class="card-body">
                    <div class="card-title mb-5">
                        <h4 class="float-left">Asset Detail</h4>
                        @canany(['update','create'],new App\Models\Asset())
                            @if ($asset->assetSubType->name ?? "" == 'Laptop')
                                @if (empty($asset->assetDetail))
                                    @can('create',new App\Models\Asset())
                                        <a href="{{ route('asset-detail.create', ['asset' => $asset->id]) }}" target="_blank"
                                            class="float-right"><i class="fa fa-edit"></i></a>
                                    @endcan
                                @else
                                    @can('update',new App\Models\Asset())
                                        <a href="{{ route('asset-detail.edit', ['asset_detail' => $asset->assetDetail->id]) }}"
                                            target="_blank" class="float-right"><i class="fa fa-edit"></i></a>
                                    @endcan
                                @endif
                            @endif
                        @endcanany
                    </div>
                    <div class="table-responsive">
                        <table id="example1" class="table">
                            <tbody>
                                <tr>
                                    <th>SubType</th>
                                    <td class="text-right">
                                        {{ ucfirst($asset->assetSubType->name ?? "") ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Barcode</th>
                                    <td class="text-right">{{ $asset->barcode ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td class="text-right">{{ $asset->status ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned To</th>
                                    <td class="text-right">{{ $asset->user->name ?? '' }}</td>
                                </tr>
                                @if (!empty($asset->assetDetail) && $asset->assetSubType->name == 'Laptop')
                                    <tr>
                                        <th>Company</th>
                                        <td class="text-right">
                                            {{ ucfirst($asset->company->name ?? '')  }}</td>
                                    </tr>
                                    <tr>
                                        <th>RAM</th>
                                        <td class="text-right">{{ $asset->assetDetail->ram ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>ROM</th>
                                        <td class="text-right">{{ $asset->assetDetail->rom ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Added Date</th>
                                        <td class="text-right">{{ Carbon\Carbon::parse($asset->created_at)->format('d-m-Y') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if ($asset->assetLogs->isNotEmpty())
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Updates</h4>
                        <ul class="bullet-line-list">

                            @foreach ($asset->assetLogs as $log)
                                <li>
                                    <h6>{{ ucfirst($log->action) ?? '' }}</h6>
                                    @if (!empty($log->reason))
                                        <p><strong>Description: </strong>{{ $log->reason }}</p>
                                    @endif
                                    <p><strong>Action By: </strong>{{ $log->user->name ?? ''}}</p>
                                    @if (!empty($log->assignedTo))
                                    <p><strong>Assigned To: </strong>{{ $log->assignedTo->name ?? '' }}</p>
                                    @endif
                                    <p class="text-muted mb-4">
                                        <i class="ti-time"></i>
                                        {{ getFormatedDateTime($log->created_at) }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('footerScripts')
    <script>

    </script>
@endsection
