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
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .wheel {
            width: 250px;
            transition: transform 3s ease-out;
            cursor: pointer;
            background: #fff;
            border-radius: 50%;
        }

        #spinButton {
            margin-top: 20px;
            font-size: 16px;
            background: red;
            color: #fff;
            border: none;
            padding: 7px 26px;
        }

        #spinDisable {
            margin-top: 20px;
            font-size: 16px;
            background: gray;
            color: #fff;
            border: none;
            padding: 7px 26px;
            opacity: 0.5;
            /* You can adjust the opacity value as needed */
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <div class="page-content footer-clear">
        <div class="page_top_title deposit_page">
            <div class="arrow"><a href="{{ url('user/dashboard') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
            <h3 class="text-center">Events</h3>
            <div class="telegram_boat"></div>
        </div>

        <div class="content event-page-sec">

            <div title="top-area">

                <div class="event-item-sc">
                    <br>
                    <br>

                    <div class="container">
                        <p class="text-center">Spin functionality has been removed.</p>
                    </div>




                </div>
            </div>

        </div>

    </div>
@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
