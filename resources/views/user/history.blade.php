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
            <h3 class="text-center">{{ __('lang.history') }}</h3>
        </div>
        <div class="custom_ballet">
            <div class="content histroy">

                <a href="page-activity.html" class="py-1">
                    <div class="align-self-center ps-1">
                        <h5 class="">{{ __('lang.my_asset') }}</h5>
                        <p class="balance_histroy">$ {{ Auth::user()->balance }}</p>
                        <p>The data is provided by Unity Rise investment Hub </p>
                        {{--{{ __('lang.sales_ranking') }}<br> {{ __('lang.official') }}--}}
                    </div>
                </a>
            </div>
        </div>

        <div class="custom_tabls_histroy_page content">
            <div class="tabs tabs-pill" id="tab-group-2">

                <div class="collapse show" id="tab-4" data-bs-parent="#tab-group-2">

                    <div id="content">
                        @foreach ($histories as $history)
                            @php
                                $product = App\Models\Product::find($history->product_id);
                                $grab = App\Models\Product::find($history->grab_id);
                            @endphp
                            @if (isset($product) && isset($grab))
                                <div class="product-histroy-grave-sc">
                                    <div class="card box-product">
                                        <div class="product-image-sc">
                                            <div class="top-name-product">
                                                <h4>{{ $product->title }}</h4>
                                                <div class="badge status btn btn-success btn-xs">Completed</div>
                                            </div>
                                            <div class="product-image-title">
                                                <div><img src="{{ App\Models\Sitesetting::find(1)->app_url.'/'.$product->image }}" alt="product1">
                                                </div>
                                                <div class="details">
                                                    <p>
                                                        {{ $product->description }}
                                                    </p>

                                                    <div class="product-price-info">
                                                        <span>${{ $product->price }}</span>
                                                        <span>x 7</span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-info-sc">
                                            <div class="item">
                                                <span>Order number</span>
                                                <strong style="text-align: right;">#{{ $grab->id }}</strong>
                                            </div>
                                            <div class="item">
                                                <span>Grab time</span>
                                                @php
                                                    $grabtime = \Carbon\Carbon::parse($grab->created_at)->format('Y-m-d');
                                                @endphp
                                                <strong>{{ $grabtime }}</strong>
                                            </div>
                                            <div class="item">
                                                <span>Commission fee</span>
                                                <strong>${{ $history->amount }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>





                    <div class="container">
                        <div class="row">
                            <!-- Your content goes here -->
                        </div>

                        <!-- Load More Button (Centered) -->
                        <div class="text-center">
                            <button id="load-more" class="histroy btn btn-primary mx-auto">{{ __('lang.load_more') }}</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endsection



    @section('js')
        <script>
            var page = 1;

            $('#load-more').on('click', function() {
                page++;

                $.ajax({
                    url: "{{ route('user.order.load') }}?page=" + page,
                    method: 'GET',
                    success: function(data) {
                        $('#content').append(data);
                        if(data.error){
                            $("#snackbar2").text(data.error);
                            myFunction2();
                        }
                    }
                });
            });
        </script>

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
