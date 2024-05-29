           <div class="card" id="balance-form">
                <div class="card-body">
                    <div class="card-title ml-4 mb-5">My Balance


                        @if(Carbon\Carbon::createFromFormat('Y-m-d',$myBalance->month)->format('M') == Carbon\Carbon::now()->startofMonth()->format('M') )
                        <button class="btn btn-primary btn-sm float-lg-right" onclick="raiseComplaint({{$myBalance->id}})" type="button">Have a Query?</button>
                        @endif
                    </div>
                        <div class="col-md-12 p-0">
                            <div class="table-responsive">
                                <table id="example1" class="table mt-2">
                                    <tbody class="myBalance-Table">
                                        <tr>
                                            {{-- <td>Name</td> --}}
                                            <td>Month</td>
                                            <td>Balance</td>

                                            <td>Absent</td>
                                            <td>Taken Leaves this Month</td>
                                            <td>Taken Leaves After Cutoff</td>
                                            <td>Deduction</td>
                                            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d',$myBalance->month)->subMonth()->format('F') .' Cutoff Deduction' }}</td>
                                            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d',$myBalance->month)->addMonth()->format('F') .' Cutoff Deduction' }}</td>
                                            <td>Final Deduction</td>

                                        </tr>
                                        <tr>
                                                {{-- <td>{{ $myBalance->user->name ?? '' }}</td> --}}
                                                <td>{{ getFormatedDate($myBalance->month) }}</td>
                                                <td>{{ $myBalance->balance ?? '0' }}</td>
                                                <td>{{  $myBalance->absent ?? '0' }}</td>
                                                <td>{{ $myBalance->taken_leaves  ?? '0'}}</td>
                                                <td>{{ $myBalance->leaves_after_cut_off ?? '0' }}</td>
                                                <td>{{ ($myBalance->deduction ?? '0') + ($myBalance->pre_approval_deduction ?? '0') }}</td>
                                                <td>{{ $myBalance->prev_month_deduction ?? '0'}}</td>
                                                <td>{{ $myBalance->next_month_deduction ?? '0' }}</td>
                                                <td>{{ $myBalance->final_deduction ?? '0' }}</td>

                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>


