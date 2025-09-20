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
            <h3 class="text-center">About usdt-grab.vip</h3>
            <div class="telegram_boat"></div>
        </div>

        <div class="about-sc">
       <div class="about-to-banner">
          <img src="{{ asset('public/assets/user/images/') }}/about.png" alt="about">
       </div>
       <div class="content about-description">
          <h4 class="title">About Us</h4>
          <p>Since {{ config('app.name') }} members come from various countries and use different currencies, {{ config('app.name') }} employs cryptocurrency transactions to streamline the process. Please ensure to verify {{ config('app.name') }}'s USDT address diligently before recharging (the platform recharge address is subject to change, and users must visit the platform for the latest recharge address before proceeding). For any inquiries, kindly reach out to customer service</p>
    </div>

    </div>
@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
