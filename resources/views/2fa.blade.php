@extends('layouts.app')

@section('content')
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper pt-0">
            <div class="content-wrapper d-flex align-items-center auth ">
                <div class="row w-100">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-transparent text-left p-5 text-center">
                            <h4 class="text-center">2FA Verification</h4>
                            {{-- <img src="../../../../images/faces/face13.jpg" class="lock-profile-img" alt="img"> --}}
                            <form class="pt-5" method="POST" action="{{ route('2fa.post') }}">
                                @csrf
                                @if ($message = Session::get('success'))
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($message = Session::get('error'))
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                                <div class="form-group">
                                    <label for="examplePassword1">
                                        <p class="text-center">We sent code to email :
                                            {{ substr(auth()->user()->email, 0, 5) . '******' . substr(auth()->user()->email, -3) }}
                                            with subject: EMS - 2FA Code</p>
                                    </label>
                                    <input type="number"
                                        class="form-control text-center @error('code') is-invalid @enderror" id="code"
                                        name="code" value="{{ old('code') }}" required autocomplete="code" autofocus
                                        placeholder="OTP">
                                </div>
                                <div class="mt-5">
                                    <button type="submit" class="btn btn-block btn-success btn-lg font-weight-medium"
                                        href="../../index.html">Submit</button>
                                </div>
                                <div class="mt-3 text-center" id="verifiBtn">
                                    <span>Resend Code in <span id="counter"></span></span>
                                    <a href="#" class="auth-link text-white"></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <script>
        function countdown() {
            var seconds = 31;

            function tick() {
                var counter = document.getElementById("counter");
                seconds--;
                counter.innerHTML =
                    "0:" + (seconds < 10 ? "0" : "") + String(seconds);
                if (seconds > 0) {
                    setTimeout(tick, 1000);
                } else {
                    document.getElementById("verifiBtn").innerHTML = `
                    <a class="btn btn-link" href="{{ route('2fa.resend') }}">Resend Code?</a>
                `;
                }
            }
            tick();
        }
        countdown();
    </script>
@endsection
