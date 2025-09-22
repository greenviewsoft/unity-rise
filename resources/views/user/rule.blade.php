@extends('layouts.user.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">
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
       <h3 class="text-center">Invest Our Planet</h3>
       <div class="telegram_boat"></div>
    </div>

    <div class="about-sc">
       <div class="about-to-banner">
          <img src="{{ asset('public/assets/user/images/') }}/rule.png" alt="Invest Our Planet">
       </div>

       <div class="content about-description">
          <h4 class="title">Investment Details</h4>
          <p>Invest in <strong>{{ config('app.name') }}</strong> and earn daily <strong>0.80%</strong> for <strong>50 days</strong>. Your principal will be returned after completing the package. This plan is suitable for all members aiming for steady returns.</p>

          <h4 class="title">Referral Commission</h4>
          <p>Referral commissions are applicable for <strong>12 Ranks</strong> up to <strong>40 levels</strong>. Commission rates vary based on the user rank and level.</p>

          <h4 class="title">Level & Rank Benefits</h4>
          <p>Users can increase their rank by completing different metrics and unlocking higher benefits. Higher ranks allow more commissions and increased earning potential.</p>

          <h4 class="title">Withdrawal Policy</h4>
          <p>Withdrawals are processed within 24 hours with no fees. Members must meet daily order volume requirements according to their level to be eligible for withdrawals.</p>

          <h4 class="title">Disclaimer</h4>
          <p>• {{ config('app.name') }} reserves the right to warn or freeze accounts in case of fraud or illegal activities.<br>
             • All users must comply with platform rules to ensure safe and fair operation.
          </p>
       </div>

       <div class="card card-style more-link mt-3">
          <h5>See more instructions</h5>
          <ul>
             <li><a href="{{ url('user/about') }}">About us</a></li>
             <li><a href="{{ url('user/promotion') }}">Promotion description</a></li>
             <li><a href="{{ url('user/dashboard') }}">Back to homepage</a></li>
          </ul>
       </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
