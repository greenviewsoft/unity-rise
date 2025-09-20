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
    <div class="page-content footer-clear">
        <div class="page_top_title">
            <h3 class="text-center">{{ __('lang.task_lobby') }}</h3>
        </div>

        <div class="custom_task_lobby card card-style custom_ballet">
            <div class="content">

                <a href="#" class="d-flex py-1">
                    <div class="align-self-center">
                        <div class="logo">
                            <img src="{{ asset('public/assets/user/images/') }}/logo.png" alt="logo">
                        </div>
                    </div>
                    <div class="loby_title align-self-center ps-1">
                        <h5 class="pt-1 mb-n1">{{ __('lang.hello') }}</h5>
                        <p class="mb-0 font-11 opacity-50">{{ Auth::user()->username }}</p>
                    </div>
                    <div class="align-self-center ms-auto text-end">
                        <i class="refress bi bi-arrow-clockwise"></i>
                    </div>
                </a>

                <a href="#" class="d-flex py-1">
                    <div class="loby_title balnce align-self-center ps-1">
                        <h5 class="pt-1 mb-n1">{{ __('lang.total_asset') }}</h5>
                        <p class="mb-0 font-16 pt-2">$ {{ Auth::user()->balance }}</p>
                    </div>
                </a>

                <div class="divider my-2 waves"></div>
                <a href="#" class="loby_table d-flex py-1">
                    <div class="align-self-center ps-1">
                        <h5 class="pt-1 mb-n1">{{ __('lang.today_profit') }}</h5>
                    </div>
                    <div class="align-self-center ms-auto text-end">
                        <h4 class="pt-1 mb-n1 color-green-dark">{{ $todaygrabs }}</h4>
                    </div>
                </a>
            </div>
        </div>

        <div class="amazon_ballet nbr">
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

                                <strong class="mb-n1">{{ __('lang.required_amount') }} : {{ $vip->requred_from }} ~
                                    {{ $vip->requred_to }}</strong>

                                <p class="mb-0 opacity-70">{{ __('lang.income') }} : {{ $vip->income_from }} ~ {{ $vip->income_to }}</p>

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
<link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.js"></script>



<script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>

<script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>


<script type="text/javascript">
    $(window).on('load', function() {

        $('#myModal').modal('show');

    });
</script>

<script>
    $(document).ready(function() {

        $(".slider").slick({

            centerMode: true,

            centerPadding: '0px',

            slidesToShow: 4,

            autoplay: true,

            vertical: true,

            verticalSwiping: true,

            arrows: false,

            swipeToSlide: true,

            focusOnSelect: true,

        });

    });



    $(document).ready(function() {

        $(".slider_banner").slick({

            centerMode: true,

            centerPadding: '0px',

            slidesToShow: 1,

            autoplay: true,

            verticalSwiping: true,

            arrows: false,

            swipeToSlide: true,

            focusOnSelect: true,

        });

    });
</script>
@endsection
