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
            <h3 class="text-center">VIP</h3>
            <div class="telegram_boat"></div>
        </div>

        <div class="content vip_page_sc">
            <div class="title">{{ __('lang.current_level') }}(VIP {{$level}})</div>

            @if (isset($vipf))
                <div class="vip_box card">
                    <div class="balance_vip">
                        <div class="vip_bg">
                            <p>VIP {{$level}}</p>
                        </div>
                        <div class="balance_vip_details">
                            <strong>{{ __('lang.min_balance') }}</strong>
                            <span>{{ $vipf->requred_from }}</span>
                        </div>
                    </div>
                    <div class="rate_vip">
                        <strong>{{ __('lang.com_rate') }}</strong>
                        <span><b>{{ $vipf->income_from }} - {{ $vipf->income_to }}%</b></span>
                    </div>
                    <div class="ammount_vip">
                        <strong>{{ __('lang.open_marks') }}</strong>
                        <span>{{ __('lang.required_amount') }}：0</span>
                    </div>
                </div>

                <div class="vip_reach_label">
                    @php
                        $vipup = App\Models\Vip::where('id', '>', $vipf->id)
                        ->first();
                    @endphp
                    <p>{{ __('lang.to_reach_next') }}</p>
                    <div class="card balance_upgrade">
                        <strong>{{ __('lang.balance_upgrade') }}</strong>
                        @if (isset($vipup))
                            <span>{{ Auth::user()->balance }}/{{ $vipup->requred_from }}</span>
                        @endif

                    </div>
                </div>
            @endif


            <div class="button_vip_bottom">
                <a href="{{ url('user/deposit') }}" class="btn btn-success">{{ __('lang.deposit') }}</a>
                <a href="{{ url('user/waves') }}" class="btn btn-danger">{{ __('lang.summary') }} <i class="bi bi-arrow-right"></i></a>
            </div>

        </div>

    </div>
@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
