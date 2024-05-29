@extends('layouts.master')
@section('content')
    @php
        $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    @endphp
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
                <div class="widget-user-image gallery">
                    <br><a target="_blank" href="{{ $employee->user->user_image }}" class="employee-image">
                        <img class="img-circle elevation-2" height="120px" width="120px" style="border-radius: 100%"
                            src="{{ $employee->user->getImagePath() }}" alt="User Avatar"></a>
                </div>
                <div class="row pt-3 pb-4">
                    <div class="col-sm-12">
                        <div class="description-block">
                            <div class="description-header text-uppercase"><span class="">{{ $employee->name }}</span>
                                @can('hrUpdateEmployee', $employee)
                                    &nbsp;
                                    <a href="{{ route('editEmployee', ['employee' => $employee->id]) }} " data-toggle="tooltip"
                                        data-placement="top" title="Edit Profile"><i class="fa fa-user-edit"></i> </a>
                                @endcan
                                @if (!empty($employee) && $employee->is_power_user)
                                    <i title="Power User" class="fa fa-bolt ml-3 text-warning" style="font-size:18px;"></i>
                                @endif
                            </div>
                            <span class="description-text">{{ $employee->department->name ?? null }}</span>
                        </div>
                        @if (!empty($employee->biometric_id))
                            <span class="float-lg-right">{!! $generator->getBarcode("$employee->biometric_id", $generator::TYPE_CODE_128) !!}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                @if (Auth::user()->can('viewFullProfile', new App\Models\Employee()) || Auth::user()->id == $employee->user_id)
                    <div class="col-md-6">
                        <div class="card card-outline mb-2">
                            <div class="card-header bg-primary text-white">
                                Personal Information
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <td>D-O-B</td>
                                                <td>{{ getFormatedDate($employee->birth_date) }}</td>
                                            </tr>
                                            <tr>
                                                <td> Personal Email</td>
                                                <td>
                                                    @if ($employee->personal_email != null)
                                                        {{ $employee->personal_email }}
                                                    @else
                                                        <code class="text-danger">N/A</code>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Contact</td>
                                                <td>
                                                    @if ($employee->phone != null)
                                                        {{ $employee->phone }}
                                                    @else
                                                        <code class="text-danger">N/A</code>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Qualification</td>
                                                <td>{{ optional($employee->qualification)->name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-6">
                    <div class="card card-outline mb-2">
                        <div class="card-header bg-primary text-white">
                            Office Details
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive mailbox-messages">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <td>Join Date</td>
                                            <td>{{ getFormatedDate($employee->join_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Employee ID</td>
                                            <td>{{ $employee->registration_id }}</td>
                                        </tr>
                                        <tr>
                                            <td>Office Email</td>
                                            <td>
                                                @if ($employee->user_id == null)
                                                    <code class="text-danger">N/A</code>@else{{ $employee->user->email }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if (Auth::user()->can('viewFullProfile', new App\Models\Employee()) || Auth::user()->id == $employee->user_id)
                                        <tr>
                                            <td>Contract Date</td>
                                            <td colspan="3">
                                                @if (empty($employee->contract_date))
                                                    <code class="text-danger">N/A</code>
                                                @else
                                                    {{ \Carbon\Carbon::parse($employee->contract_date)->format('d/m/Y') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if (!empty($employee->second_contract_date))
                                            <tr>
                                                <td>Second Contract Date</td>
                                                <td colspan="3">
                                                        {{ \Carbon\Carbon::parse($employee->second_contract_date)->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                        @endif
                                        @endif

                                        <tr>
                                            <td>Biometric ID</td>
                                            <td>
                                                @if ($employee->biometric_id == null)
                                                    <code class="text-danger">N/A</code>@else{{ $employee->biometric_id }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Shift Time</td>
                                            <td>
                                                @if (!empty($employee->user->shiftType))
                                                    {{ Carbon\Carbon::createFromFormat('H:i:s', $employee->user->shiftType->start_time)->format('g:i A') }}
                                                    -
                                                    {{ Carbon\Carbon::createFromFormat('H:i:s', $employee->user->shiftType->end_time)->format('g:i A') }}
                                                @else
                                                    <code class="text-danger">N/A</code>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!empty($employee->department))
                    <div class="col-md-6">
                        <div class="card card-outline mb-2">
                            <div class="card-header bg-primary text-white">
                                Department Details
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <td>Manager</td>
                                                <td>{{ $employee->department->deptManager->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Name</td>
                                                <td>{{ $employee->department->name }}</td>
                                            </tr>
                                            @if (!empty($employee->department->deptTeamLeader))
                                                <tr>
                                                    <td>Team Leader</td>
                                                    <td>{{ $employee->department->deptTeamLeader->name .
                                                        ' (' .
                                                        $employee->department->deptTeamLeader->department->name .
                                                        ')' }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td>Designation</td>
                                                <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (Auth::user()->can('viewFullProfile', new App\Models\Employee()) || Auth::user()->id == $employee->user_id)
                    @if (!empty($documents))
                        <div class="col-md-6">
                            <div class="card card-outline mb-2">
                                <div class="card-header bg-primary text-white">
                                    <i class="fa fa-file"></i> Documents
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive mailbox-messages">
                                        <table class="table ">
                                            <tbody>
                                                <tr>
                                                    <td>Aadhaar Number</td>
                                                    <td>{{ $employee->documents->aadhaar_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Aadhaar File</td>
                                                    <td>
                                                        @if (!empty($employee->documents->aadhaar_file))
                                                            <a target="_blank"
                                                                href="{{ route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->aadhaar_file]) }}">
                                                                <i class="fa fa-eye text-primary"></i>
                                                            </a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'aadhaar_file']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pan Number</td>
                                                    <td>{{ $employee->documents->pan_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Pan File</td>
                                                    <td>
                                                        @if (!empty($employee->documents->pan_file))
                                                            <a target="_blank"
                                                                href="{{ route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->pan_file]) }}">
                                                                <i class="fa fa-eye text-primary"></i></a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'pan_file']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>CV</td>
                                                    <td>
                                                        @if (!empty($employee->documents->cv))
                                                            <a target="_blank"
                                                                href="{{ route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cv]) }}">
                                                                <i class="fa fa-eye text-primary"></i></a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'cv']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Passport</td>
                                                    <td>
                                                        @if (!empty($employee->documents->passport))
                                                            <a target="_blank"
                                                                href="{{ route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->passport]) }}">
                                                                <i class="fa fa-eye text-primary"></i></a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'passport']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Image</td>
                                                    <td>
                                                        @if (!empty($employee->user->user_image))
                                                            <a target="_blank"
                                                                href="{{ $employee->user->user_image . '?download=true' }}">
                                                                <i class="fa fa-download text-primary"></i></a>
                                                            </span>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Video</td>
                                                    <td>
                                                        @if (!empty($employee->documents->video_file))
                                                            <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->video_file])}}">
                                                                <i class="fa fa-eye text-primary"></i>
                                                            </a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'video_file']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (!empty($employee->bankdetail))
                        <div class="col-md-6">
                            <div class="card card-outline mb-2">
                                <div class="card-header bg-primary text-white">
                                    <i class="fa fa-bank"></i> Bank Details
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive mailbox-messages">
                                        <table class="table ">
                                            <tbody>
                                                <tr>
                                                    <td>Account Holder</td>
                                                    <td>
                                                        @if ($employee->bankdetail->account_holder != null)
                                                            {{ $employee->bankdetail->account_holder }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bank Name</td>
                                                    <td>
                                                        @if ($employee->bankdetail->bank_name != null)
                                                            {{ $employee->bankdetail->bank_name }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Account Number</td>
                                                    <td>
                                                        @if ($employee->bankdetail->account_no != null)
                                                            {{ $employee->bankdetail->account_no }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Ifsc Code</td>
                                                    <td>
                                                        @if ($employee->bankdetail->ifsc_code != null)
                                                            {{ $employee->bankdetail->ifsc_code }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Cheque</td>
                                                    <td>
                                                        @if (!empty($employee->documents->cheque))
                                                            <a target="_blank"
                                                                href="{{ route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cheque]) }}">
                                                                <i class="fa fa-eye text-primary"></i></a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'cheque']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (!empty($employee->employeeEmergencyContact))
                        <div class="col-md-6">
                            <div class="card card-outline mb-2">
                                <div class="card-header bg-primary text-white">
                                    <i class="fa fa-bank"></i> Emergency Contact Details
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive mailbox-messages">
                                        <table class="table ">
                                            <tbody>
                                                <tr>
                                                    <td>Name of the person</td>
                                                    <td>
                                                        @if ($employee->employeeEmergencyContact->person_name != null)
                                                            {{ $employee->employeeEmergencyContact->person_name }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Relation with employee</td>
                                                    <td>
                                                        @if ($employee->employeeEmergencyContact->person_relation != null)
                                                            {{ $employee->employeeEmergencyContact->person_relation }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Contact number</td>
                                                    <td>
                                                        @if ($employee->employeeEmergencyContact->person_contact != null)
                                                            {{ $employee->employeeEmergencyContact->person_contact }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Address</td>
                                                    <td>
                                                        @if ($employee->employeeEmergencyContact->person_address != null)
                                                            {{ $employee->employeeEmergencyContact->person_address }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($assets->isNotEmpty())
                        <div class="col-md-6">
                            <div class="card card-outline mb-2">
                                <div class="card-header bg-primary text-white">
                                    <i class="fa fa-bank"></i> Assets
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive mailbox-messages">
                                        <table class="table ">
                                            <tbody>
                                                @foreach ($assets as $asset)
                                                    <tr>
                                                        <td> {{ $asset->assetSubType->name ?? '' }}</td>
                                                        <td>
                                                            {{ $asset->barcode }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td> Policy</td>
                                                    <td>
                                                        @if (!empty($employee->documents->asset_policy))
                                                            <a target="_blank"
                                                                href="{{ route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->asset_policy]) }}">
                                                                <i class="fa fa-eye text-primary"></i>
                                                            </a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'asset_policy']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (!empty($employee->employeeExitDetail))
                        <div class="col-md-6">
                            <div class="card card-outline mb-2">
                                <div class="card-header bg-primary text-white">
                                    <i class="fa fa-bank"></i> Exit Details
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive mailbox-messages">
                                        <table class="table ">
                                            <tbody>
                                                <tr>
                                                    <td>Exit Date</td>
                                                    <td>
                                                        @if ($employee->employeeExitDetail->exit_date != null)
                                                            {{ getFormatedDate($employee->employeeExitDetail->exit_date) }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Reason</td>
                                                    <td>
                                                        @if ($employee->employeeExitDetail->reason != null)
                                                            {{ $employee->employeeExitDetail->reason }}
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Status</td>
                                                    <td>
                                                        {{ $employee->employeeExitDetail->status() }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Experience Letter</td>
                                                    <td>
                                                        @if ($employee->employeeExitDetail->experience_file != null)
                                                            <a target="_blank"
                                                                href="{{ route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->employeeExitDetail->experience_file]) }}">
                                                                <i class="fa fa-eye text-primary"></i>
                                                            </a>
                                                            @can('hrUpdateEmployee', $employee)
                                                                <span style="margin: 6px;"> <a
                                                                        href="{{ route('deleteEmployeeDocument', ['employee_id' => $employee->id, 'reference' => 'experience_file']) }}"><i
                                                                            class="fa fa-trash  text-danger"></i></a></span>
                                                            @endcan
                                                        @else
                                                            <code class="text-danger">N/A</code>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
@endsection
