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
            <div class="arrow"><a href="{{ url('user/dashboard') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
            <h3 class="text-center">{{ __('lang.tr_history') }}</h3>

            @include('layouts.user.partial.support')
        </div>

        <div class="content transaction-histroy-sc">
            <div class="deposit-withdrawal-btn">
                <a href="{{ url('user/record?type=deposit') }}"
                    class="btn gradient-green shadow-bg-m text-start theme-btn">{{ __('lang.deposit') }} <i
                        class="bi bi-arrow-up-right"></i></a>
                <a href="{{ url('user/record?type=withdraw') }}"
                    class="btn gradient-green shadow-bg-m text-start theme-btn">{{ __('lang.withdraw') }} <i
                        class="bi bi-arrow-up-right"></i></a>
            </div>


            @if ($type == 'deposit')
                @foreach ($deposites as $deposit)
                    <div class="transaction status-accroding-button">
                        <div class="card account-details-balance-box">
                            <div class="account-text-date">
                                <span>{{ __('lang.deposit_amount') }}</span>
                                <strong>{{ $deposit->created_at }}</strong>
                            </div>
                            <div class="account-balance-info">
                                <span class="text-success">+{{ $deposit->amount }}</span>
                                <span>{{ __('lang.you_receive') }}: ${{ $deposit->amount }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif


            @if ($type == 'withdraw')
                @foreach ($withdraws as $withdraw)
                    <div class="transaction status-accroding-button">
                        <div class="card account-details-balance-box">
                            <div class="account-text-date">
                                <span>{{ __('lang.deposit_amount') }}</span>
                                <strong>{{ $withdraw->created_at }}</strong>
                            </div>
                            <div class="account-balance-info">
                                <span class="text-danger">-{{ $withdraw->exact_amount}}</span>
                                <span>{{ __('lang.you_receive') }}: ${{ $withdraw->amount }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif




        </div>

    </div>
@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
