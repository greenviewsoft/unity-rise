@extends('layouts.user.app')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">
    <!-- <link rel="stylesheet" type="text/css" href="fonts/bootstrap-icons.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
@endsection
@section('content')
    @include('extra.snakbarcss')

    <div class="page-content footer-clear">
        <div class="page_top_title deposit_page">
            <div class="arrow"><a href="{{ url('user/amazon') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
            <h3 class="text-center">Order-grab rules</h3>
            <div class="telegram_boat"></div>
        </div>
        <div class="order-grav-ruls">
            <div class="row">
                <div class="col-md-12">
                    <div class="grave-box-area">
                        <div class="title-area">
                            <strong>Get the Order</strong>
                            <p>Click "Grab now" button to get the order</p>
                        </div>
                        <div class="grab-box-bg">
                            <div class="grab_shadow">
                                <div id="countdown" class="box-cunter-nmbr countdown">
                                    <span class="digit">0</span>
                                    <span class="digit">0</span>
                                    <span class="digit">0</span>
                                </div>
                            </div>
                        </div>
                        <p class="botton-text-grab">Order grabbing...the result will be shown below.</p>
                    </div>
                    <button id="grab_loader" class="btn btn-danger grab-btn">Grab Now</button>




                    <div class="grab-page">
                        <h5>Result today</h5>
                        <div class="card custom_ballet">
                            <div class="content">
                                <a href="{{ url('user/history') }}" class="d-flex py-1">
                                    <div class="align-self-center">
                                        <div class="logo">
                                            <img src="{{ asset('public/assets/user/images/') }}/logo.png" alt="logo">
                                        </div>
                                    </div>
                                    <div class="align-self-center ps-1">
                                        <h5 class="pt-1 mb-n1">Hello</h5>
                                        <p class="mb-0 font-11 opacity-50">{{ Auth::user()->username }}</p>
                                    </div>
                                    <div class="align-self-center ms-auto text-end">
                                        <i class="refress bi bi-arrow-clockwise"></i>
                                    </div>
                                </a>
                                <a href="{{ url('user/history') }}" class="d-flex py-1">
                                    <div class="align-self-center ps-1">
                                        <h5 class="pt-1 mb-n1">My total assets</h5>
                                        <p class="pt-2 mb-0 font-20 opacity-50">{{ Auth::user()->balance }}</p>
                                    </div>
                                </a>
                                <div class="divider my-2 opacity-50"></div>
                                <a href="{{ url('user/history') }}" class="d-flex py-1">
                                    <div class="align-self-center ps-1">
                                        <h5 class="pt-1 mb-n1">Today's Profits</h5>
                                    </div>
                                    <div class="align-self-center ms-auto text-end">
                                        <h4 class="pt-1 mb-n1 color-blue-dark">$ {{ $todaygrabs }}</h4>
                                    </div>
                                </a>
                                <div class="divider my-2 opacity-50"></div>
                                <a href="{{ url('user/history') }}" class="d-flex py-1">
                                    <div class="align-self-center ps-1">
                                        <h5 class="pt-1 mb-n1">Promotion Bonus</h5>
                                    </div>
                                    <div class="align-self-center ms-auto text-end">
                                        <h4 class="pt-1 mb-n1 color-green-dark">0.00</h4>
                                    </div>
                                </a>
                                <div class="divider my-2 opacity-50"></div>
                                <a href="{{ url('user/history') }}" class="d-flex py-1">
                                    <div class="align-self-center ps-1">
                                        <h5 class="pt-1 mb-n1">Accumulated Profits</h5>
                                    </div>
                                    <div class="align-self-center ms-auto text-end">
                                        <h4 class="pt-1 mb-n1 color-green-dark">0.00</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>





                    <button class="bottom_grab btn btn-danger grab-btn">Order-grab rules</button>
                </div>
            </div>
        </div>
    </div>


    @include('extra.snakbarjs')
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            var digits = $(".digit");
            var grab_loader = $("#grab_loader");

            grab_loader.click(function() {
                // Disable the button to prevent multiple clicks
                grab_loader.attr("disabled", true);

                // Initialize the countdown numbers
                // digits.text("9 8 7 6 5 4 3 2 1 0");

                // Animate the countdown numbers
                digits.addClass("scrolling");

                // Start the countdown
                var count = 100;
                var countdownInterval = setInterval(function() {
                    count--;

                    // Update the countdown numbers
                    digits.text(count);

                    // If the countdown reaches 0, stop the interval and enable the button
                    if (count === 0) {
                        clearInterval(countdownInterval);
                        grab_loader.attr("disabled", false);
                    }
                }, 10);
            });
        });
    </script>
    <script>
        function grabcall() {
            setTimeout(function() {
                var myModalgrab = new bootstrap.Modal(document.getElementById(
                    'myModalgrab'));
                myModalgrab.show();
            }, 3000); // Delay in milliseconds (2 seconds in this example)
        }
    </script>


    <script>
        $("#grab_loader").click(function() {
            $("#snackbar").text('Waiting for the response of the product search sestym..');
            myFunction();

            $.ajax({
                type: 'GET',
                url: "{{ url('user/order/grab') }}",
                success: function(res) {
                    console.log(res.image);
                    if (res.error) {

                        $("#snackbar2").text(res.error);
                        myFunction2();
                    }
                    if (res.grab) {
                        $("#modalImage").attr("src", res.image);
                        $("#description").text(res.product.description);
                        $("#price").text(res.product.price);
                        $("#title").text(res.product.title);
                        $("#grab").text(res.grab.amount);
                        $("#time").text(res.time);
                        $("#total").text(res.total);

                        grabcall();
                    }
                    if (res.location) {
                        window.location.href = res.location;
                    }
                }
            })

        });

        $('#submit').click(function() {
            window.location.href = "{{ url('user/grab/submit') }}";
        });
    </script>

    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
