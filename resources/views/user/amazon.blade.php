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
    <div class="page-content footer-clear amazon">

        <div class="page_top_title deposit_page">

            <div class="arrow"><a href="{{ url('user/dashboard') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>

            <h3 class="text-center">E-commerce sales commission</h3>

            <div class="telegram_boat"></div>

        </div>



        <div class="mt-4 amazon_ballet">



            @foreach ($vips as $key => $vip)
                @php
                    $numberToCheck = Auth::user()->balance;
                    $lowerBound = $vip->requred_from;
                    $upperBound = $vip->requred_to;

                    if ($numberToCheck >= $lowerBound && $numberToCheck <= $upperBound) {
                        $atvie = true;
                    } else {
                        $atvie = false;
                    }
                @endphp
                <a href="{{ $atvie == true ? url('user/order-grav-ruls') : '#' }}"
                    class="{{ $atvie == true ? 'active' : '' }} default-link card card-style">

                    <div class="card-center px-4">

                        <div class="d-flex">

                            <div class="align-self-center">

                                <img class="amazon_logo" src="{{ App\Models\Sitesetting::find(1)->app_url.'/'.$vip->image }}" alt="amazon">

                            </div>

                            <div class="amazon-details align-self-center">

                                <strong class="mb-n1">Required amount : {{ $vip->requred_from }} ~
                                    {{ $vip->requred_to }}</strong>

                                <p class="mb-0 opacity-70">Income : {{ $vip->income_from }} ~ {{ $vip->income_to }}</p>

                            </div>

                            <div class="vip_image_lavel">
                                <span>V{{ $key }}</span>
                            </div>


                        </div>

                    </div>

                </a>
            @endforeach



        </div>



    </div>
@endsection







@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>

    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
