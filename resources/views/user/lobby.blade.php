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
       <h3 class="text-center">{{ __('lang.task_lobby') }}</h3>
       <div class="telegram_boat"><i class="bi bi-headset"></i></div>
    </div>

    <div class="content lobby-product-details">
       <div class="product-box">
          <div class="recharge-symbol recharge-symbol-two">
             <div class="recharge-symbol-item">
                <a href="#">
                   <img src="{{ asset('public/assets/user/images/') }}/1692322896.jpeg" alt="">
                   <div class="bottom-bg">
                      <h6>Lobby Title 1</h6>
                      <p>Daily interest rate:5%</p>
                      <small>Cycle:10 Day</small>
                   </div>
                </a>
             </div>
             <div class="recharge-symbol-item">
                <a href="#">
                   <img src="{{ asset('public/assets/user/images/') }}/1692322855.jpeg" alt="">
                   <div class="bottom-bg">
                      <h6>Lobby Title 2</h6>
                      <p>Daily interest rate:7%</p>
                      <small>Cycle:15 Day</small>
                   </div>
                </a>
             </div>
             <div class="recharge-symbol-item">
                <a href="#">
                   <img src="{{ asset('public/assets/user/images/') }}/1692322957.jpeg" alt="">
                   <div class="bottom-bg">
                      <h6>Lobby Title 3</h6>
                      <p>Daily interest rate:9%</p>
                      <small>Cycle:20 Day</small>
                   </div>
                </a>
             </div>
             <div class="recharge-symbol-item">
                <a href="#">
                   <img src="{{ asset('public/assets/user/images/') }}/1692323011.jpeg" alt="">
                   <div class="bottom-bg">
                      <h6>Lobby Title 4</h6>
                      <p>Daily interest rate:12%</p>
                      <small>Cycle:30 Day</small>
                   </div>
                </a>
             </div>
             <div class="recharge-symbol-item">
                <a href="#">
                   <img src="{{ asset('public/assets/user/images/') }}/1692323072.jpeg" alt="">
                   <div class="bottom-bg">
                      <h6>Lobby Title 5</h6>
                      <p>Daily interest rate:20%</p>
                      <small>Cycle:40 Day</small>
                   </div>
                </a>
             </div>
             <div class="recharge-symbol-item">
                <a href="#">
                   <img src="{{ asset('public/assets/user/images/') }}/1692323122.jpeg" alt="">
                   <div class="bottom-bg">
                      <h6>Lobby Title 6</h6>
                      <p>Daily interest rate:21%</p>
                      <small>Cycle:50 Day</small>
                   </div>
                </a>
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
