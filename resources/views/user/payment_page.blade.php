<!DOCTYPE HTML>

<html lang="en">



<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />

    <title>usdt-grab.vip</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/user/images/') }}/logo.png">
    <link rel="shortcut icon" href="{{ asset('public/assets/user/images/') }}/logo.png" type="image/x-icon">

    <meta id="theme-check" name="theme-color" content="#FFFFFF">

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">

    <!-- <link rel="stylesheet" type="text/css" href="fonts/bootstrap-icons.css"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    @include('extra.snakbarcss')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>



</head>



<body class="theme-light">

    <div id="preloader">

        <div class="spinner-border color-highlight" role="status"></div>

    </div>

    <div id="page" class="deposit_bg">

        <div class="page-content footer-clear">

        <div class="page_top_title deposit_page">

            <div class="arrow">

                <a href="{{ url('user/deposit') }}">

                    <i class="bi bi-arrow-left-circle-fill"></i>

                </a>

            </div>

            <h3 class="text-center">{{ __('lang.deposit') }}</h3>

            <div class="telegram_boat"><i class="bi bi-headset"></i></div>

        </div>



        <div class="deposit_page_grave">



            <div id="countdown" class="countdown-deposit">
                <span style="color: red">Please submit otherwise payment not added</span>
                <br>
                <span id="minutes"></span>:<span id="seconds"></span>

            </div>





            <form action="{{ url('user/deposite-information') }}" method="get">

                @csrf

                <div class="box_method">

                    <!--<label>{{ __('lang.deposit_method') }}</label>-->

                    <div class="deposit qr-code-sc">
                        @php

                            $link = $addresstrx->address_base58;

                            $url = 'https://barcode.tec-it.com/barcode.ashx?data=' . $link . '&code=QRCode&eclevel=L';

                        @endphp

                        <img src="{{ $url }}" alt="bar">
                    </div>

                </div>
                <p class="address">{{ __('lang.address') }}</p>
                <div class="deposit copyboard">
                    <input type="text" readonly class="copy_text invite-link form-control" value="{{ $addresstrx->address_base58 }}">
                    <button type="button" id="mycopy" class="deposit_btn" onclick="copyText()">{{ __('lang.copy_link') }}</button>
                </div>

                <div class="foot">
                    <p>{{ __('lang.trx_warning') }}</p>
                    <p class="text-danger">{{ __('lang.click_submit') }}</p>
                </div>

                <div class="next_btn_deposit">

                    <button type="submit" class="mx-3 btn btn-full gradient-blue shadow-bg shadow-bg-s">{{ __('lang.submit') }}</button>

                </div>

            </form>



        </div>



    </div>

    </div>

    <script>

        // Set the countdown target time (in seconds)

        var countdownTime = "{{ $timeLeftInSeconds }}"; // 5 minutes



        // Function to update the countdown

        function updateCountdown() {

            var minutes = Math.floor(countdownTime / 60);

            var seconds = countdownTime % 60;



            // Update the HTML elements with the new values

            $("#minutes").text(minutes < 10 ? "0" + minutes : minutes);

            $("#seconds").text(seconds < 10 ? "0" + seconds : seconds);



            // Decrement the countdown time

            countdownTime--;



            // Check if the countdown has reached zero

            if (countdownTime < 0) {

                // Countdown has expired, you can perform actions here

                $("#countdown").text("Deposite Account expired");

                window.location.href = "{{ url('user/sessionex') }}";

                clearInterval(interval); // Stop updating the countdown

            }

        }



        // Call the updateCountdown function initially

        updateCountdown();



        // Update countdown every second

        var interval = setInterval(updateCountdown, 1000);

    </script>

    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/my.js"></script>


</body>

