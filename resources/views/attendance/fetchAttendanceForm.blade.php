@extends('layouts.master')
@section("headerLinks")

<style>

</style>

@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Fetch Attendance Form</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-2">
                                <h4 class="card-title">Fetch Attendance Form</h4>
                            </div>
                            <div class="row">
                                <form class="col-md-6">
                                    <div>
                                        <input type="date" name="fetch_date" required id="date"
                                            class="form-control">
                                    </div>
                                </form>
                                <div class="col-md-6 text-right">
                                    <div id="fluid-meter-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footerScripts')
    <script src="{{ url('js/js-fluid-meter.js') }}"></script>
    <script>
        sessionStorage.setItem("progress", 0);
        var fm2 = new FluidMeter();
        fm2.init({
            targetContainer: document.getElementById("fluid-meter-2"),
            fillPercentage: 0,
            options: {
                fontFamily: "Oxygen",
                drawPercentageSign: true,
                drawBubbles: true,
                size: 300,
                borderWidth: 10,
                backgroundColor: "#000000",
                foregroundColor: "#F3797E",

                foregroundFluidLayer: {
                    fillStyle: "#4747A1",
                    angularSpeed: 90,
                    maxAmplitude: 11,
                    frequency: 25,
                    horizontalSpeed: -200
                },
                backgroundFluidLayer: {
                    fillStyle: "#7DA0FA",
                    angularSpeed: 100,
                    maxAmplitude: 13,
                    frequency: 23,
                    horizontalSpeed: 230
                }
            }
        });
        let url = "{{ url('attendance/store') }}";
        $('#date').on('change', function() {
            if (!confirm("Are you sure ?")) {
                $('#date').val("");
                return false;
            }
            $("#date").prop('disabled',true);
            $.ajax({
                url: url,
                type: 'get',
                data: {
                    'fetch_date': $(this).val()
                },
                success: function(response) {
                    clearInterval(fetchProgress);
                },
                error:function(error)
                {
                    alert("Something went wrong");
                    clearInterval(fetchProgress);
                    $(this).prop('disabled',false);
                },
                complete:function()
                {
                    $("#date").prop('disabled',false);
                    fm2.setPercentage(100);
                    toastr.success("Attendance Fetched");
                    setTimeout(() => {
                        reset();
                    }, 4000);
                }
            })
           var fetchProgress    = setInterval(function() {
                $.getJSON('/get/progress', function(data) {
                        if(sessionStorage.getItem('progress')<=Number(data[0]))
                        {
                            fm2.setPercentage(Number(data[0]));

                            sessionStorage.setItem('progress', Number(data[0]));
                        }
                    });
            }, 100);
        });

        function reset()
        {
            fm2.setPercentage(Number(0.1));
        }
    </script>
@endsection
