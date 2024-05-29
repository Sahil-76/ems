<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered listtable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Biometric Id</th>
                            @foreach ($dateArray as $date)
                                <th class="text-center">{{ Carbon\Carbon::parse($date)->format('D ') }} <br>
                                    {{ Carbon\Carbon::parse($date)->format('d-M') }}</th>
                            @endforeach
                            <th>Half Days</th>
                            <th>Full Days</th>
                            <th>Working Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userArray as $email => $user)
                            <tr>
                                @php
                                    $fullDays = 0;
                                    $halfDays = 0;
                                    $userId = $user['id'];
                                @endphp
                                <td>
                                    {{ $user['name'] }}
                                </td>
                                <td>{{ $user['biometric_id'] }}</td>
                                @foreach ($user as $key => $attendance)
                                    @if ($key == 'biometric_id' || $key == 'name' || $key == 'id')
                                        @continue
                                    @endif
                                    @if ($attendance['session'] == 'Full day')
                                        @php $fullDays+=1; @endphp
                                        <td class="bg-yellow" style="background: yellow;">Full Day</td>
                                    @elseif (!empty($attendance['punch_in']) && empty($attendance['session']))
                                        <td>{{ Carbon\Carbon::parse($attendance['punch_in'])->format('h:iA') }}
                                            <br><br><span class="text-danger">
                                                {{ !empty($attendance['punch_out']) ? Carbon\Carbon::parse($attendance['punch_out'])->format('h:iA') : '' }}
                                            </span>
                                        </td>
                                    @elseif((!empty($attendance['punch_in']) && $attendance['session'] == 'Second half') ||
                                        $attendance['session'] == 'First half')
                                        @php $halfDays+=1; @endphp
                                        <td style="background:#644f4f33">
                                            @if (!empty($attendance['punch_in']))
                                                {{ Carbon\Carbon::parse($attendance['punch_in'])->format('h:iA') }}
                                            @else
                                                {{ $attendance['session'] }}
                                            @endif
                                            <br><br><span class="text-danger">
                                                @if (!empty($attendance['punch_out']))
                                                    {{ !empty($attendance['punch_out']) ? Carbon\Carbon::parse($attendance['punch_out'])->format('h:iA') : '' }}
                                                @endif
                                            </span>
                                        </td>
                                    @else
                                        <td>--:--</td>
                                    @endif
                                @endforeach
                                <td><a target="_blank"
                                        href="{{ route('hrLeaveHistory', ['user_id' => $userId, 'leave_session' => 'Half day', 'dateFrom' => $startDate, 'dateTo' => $endDate, 'status' => '1']) }}">{{ $halfDays }}</a>
                                </td>
                                <td> <a target="_blank"
                                        href="{{ route('hrLeaveHistory', ['user_id' => $userId, 'leave_session' => 'Full day', 'dateFrom' => $startDate, 'dateTo' => $endDate, 'status' => '1']) }}">{{ $fullDays }}</a>
                                </td>
                                <td>{{ $workingDays }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
