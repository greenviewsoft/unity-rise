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
            <div class="arrow"><a href="{{ url('user/withdraw') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
            <h3 class="text-center">{{ __('lang.withdraw_address') }}</h3>
            <div class="telegram_boat"></div>
        </div>


        <form action="{{ url('user/bep20address-update') }}" method="post">
            @csrf
            <div class="withdraw-page-sc">
                <div class="content withdraw-page-top">
                    <h5>{{ __('lang.withdraw_account') }}</h5>
                </div>

                @if(session('success'))
                    <div class="content">
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="content">
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="content">
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif


                <div class="content enter-ammount-balance">
                    <p>{{ __('lang.usdt_address') }}</p>
                </div>
                <div class="content input-ammount-box">
                    <input type="text" name="crypto_address" class="form-control" placeholder="{{ __('lang.enter_address') }}"
                        value="{{ old('crypto_address', Auth::user()->crypto_address) }}" required>
                </div>


                <div class="content enter-ammount-balance">
                    <p>Withdraw Pin</p>
                </div>
                <div class="content input-ammount-box">
                    <input type="password" name="crypto_password" class="form-control" placeholder="{{ __('lang.enter_password') }}" required>
                </div>

                <div class="content button-cash">
                    <button class="btn btn-block btn-full gradient-highlight shadow-bg shadow-bg-xs">{{ __('lang.submit') }}</button>
                </div>
            </div>
        </form>


    </div>
@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
