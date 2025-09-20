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
        <div class="page_top_title deposit_page">
            <div class="arrow">
                <a href="{{ url('user/dashboard') }}">
                    <i class="bi bi-arrow-left-circle-fill"></i>
                </a>
            </div>
            <h3 class="text-center">{{ __('lang.deposit') }}</h3>

            @include('layouts.user.partial.support')
            
        </div>

        <div class="deposit_page_grave">

            <form action="{{ url('user/deposite-address') }}" method="get">
                @csrf
                <div class="box_method" data-bs-toggle="offcanvas" data-bs-target="#deposit-method">
                    <label>{{ __('lang.deposit_method') }}</label>
                    <input class="form-control custom" disabled type="text" name="" placeholder=""
                        value="{{ __('lang.vr_currency') }}">
    
                </div>
    
                <div class="box_method" data-bs-toggle="offcanvas" data-bs-target="#deposit-currency">
                    <label>{{ __('lang.currency') }}</label>
                    <select class="form-control custom" name="currency">
                        <option value="USDT-BEP20">USDT-BEP20</option>
                    </select>
                </div>


                <div class="box_method" data-bs-toggle="offcanvas" data-bs-target="#deposit-currency">
                    <label>{{ __('lang.amount') }}</label>
                    <input type="number" class="form-control" name="amount" placeholder="Amount">
                </div>
    
                <div class="next_btn_deposit">
                    <button type="submit" class="mx-3 btn btn-full shadow-bg shadow-bg-s">{{ __('lang.next_step') }}</button>
                </div>
            </form>

        </div>

    </div>




@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
