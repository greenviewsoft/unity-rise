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
            <div class="arrow"><a href="{{ url('user/deposit') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
            <h3 class="text-center">Deposit Details</h3>
            
            @include('layouts.user.partial.support')

        </div>

        <div class="deposit_page_details">

            <div class="success_point">
                <img src="{{ asset('public/assets/user/images/') }}/check.png" alt="check.png">
                <p>{{ __('lang.pro_deposit') }}</p>
            </div>
            <div class="custom_task_lobby card card-style">
                <div class="content">

                    <div class="d-flex py-1">

                        <div class="align-self-center ps-1">
                            <h5 class="pt-1 mb-n1">{{ __('lang.dep_method') }}</h5>
                        </div>
                        <div class="align-self-center ms-auto text-end">
                            <h4 class="dp_right_item ">{{ $order->method }}</h4>
                        </div>
                    </div>
                    <div class="divider my-2 payment_details"></div>
                    <div class="d-flex py-1">

                        <div class="align-self-center ps-1">
                            <h5 class="pt-1 mb-n1">{{ __('lang.order_no') }}</h5>
                        </div>
                        <div class="align-self-center ms-auto text-end">
                            <h4 class="dp_right_item ">{{ $order->order_number }}</h4>
                        </div>
                    </div>
                    <div class="divider my-2 payment_details"></div>
                    <div class="d-flex py-1">

                        <div class="align-self-center ps-1">
                            <h5 class="pt-1 mb-n1">{{ __('lang.dep_amount') }}</h5>
                        </div>
                        <div class="align-self-center ms-auto text-end">
                            <h4 class="dp_right_item ">{{ $order->amount }}</h4>
                        </div>
                    </div>
                    <div class="divider my-2 payment_details"></div>
                    <div class="d-flex py-1">

                        <div class="align-self-center ps-1">
                            <h5 class="pt-1 mb-n1">{{ __('lang.currency') }}</h5>
                        </div>
                        <div class="align-self-center ms-auto text-end">
                            <h4 class="dp_right_item ">{{ $order->currency }}</h4>
                        </div>
                    </div>

                    @if ($order->currency == 'TRX')
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.conv_rate') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">{{ $order->conversion_rate }}</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.after_exchange') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">
                                    {{ $order->amount * $settingtrx->conversion }} USDT</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.actually_receive') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">
                                    {{ $order->amount * $settingtrx->conversion }} USDT</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                    @endif


                    @if ($order->currency == 'USDT-TRC20')
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.conv_rate') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">{{ $order->conversion_rate }}</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.after_exchange') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">{{ $order->amount }} USDT</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.actually_receive') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">{{ $order->amount }} USDT</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                    @endif

                    @if ($order->currency == 'USDT-BEP20')
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.conv_rate') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">{{ $order->conversion_rate }}</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.after_exchange') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">{{ $order->amount }} USDT</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">{{ __('lang.actually_receive') }}</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item ">{{ $order->amount }} USDT</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                        <div class="d-flex py-1">

                            <div class="align-self-center ps-1">
                                <h5 class="pt-1 mb-n1">Payment Status</h5>
                            </div>
                            <div class="align-self-center ms-auto text-end">
                                <h4 class="dp_right_item text-success">Completed (Wallet Balance)</h4>
                            </div>
                        </div>
                        <div class="divider my-2 payment_details"></div>
                    @endif

                </div>
            </div>
            <div class="btn_deposit">
                <a href="{{ url('user/dashboard') }}" class="mx-3 btn btn-full gradient-blue shadow-bg shadow-bg-s">{{ __('lang.back_home') }}</a>
                <a href="https://t.me/tsmc_globalchat" class="mx-3 btn btn-full gradient-blue shadow-bg shadow-bg-s">{{ __('lang.connect_support') }}</a>
            </div>


        </div>

    </div>
@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
