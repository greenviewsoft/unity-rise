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
       <div class="arrow"><a href="{{ url('user/userinfo') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
       <h3 class="text-center">Wallet usage tutorial</h3>
       <div class="telegram_boat"><i class="bi bi-headset"></i></div>
    </div>

    <div class="content wallet_guide_sc pt-4">
       <div class="text-center text-info mb-4">
          <h4>Start to use ‘wallet’ service</h4>
          <p>‘wallet’ service provide a fast and safe way to manage your virtual wallet. To ensure the security of your fund, please complete the following settings before starting.</p>
       </div>

       <div class="card add_phn_nmbr mb-4 p-4">
          <div class="title"><h4>Add a phone number</h4></div>
          <div class="divider my-2 opacity-50"></div>
          <div class="details-wallet">
             <div class="text-part">
                <span class="nmbr"><i class="bi bi-check-circle-fill"></i><strong>+880-17******44</strong></span>
                <p class="">This is for verifying your identity, it can not be changed after adding.</p>
             </div>
             <div class="icon-part">
                <img src="images/a63765b.svg" alt="a63765b.svg">
             </div>
          </div>
       </div>

       <div class="card add_phn_nmbr p-4">
          <div class="title"><h4>Set a withdraw password</h4></div>
          <div class="divider my-2 opacity-50"></div>
          <div class="details-wallet">
             <div class="text-part">
                <p class="">The last level of fund security</p>
                <p class="is-btn">Go to settings</p>
             </div>
             <div class="icon-part">
                <img src="images/6675194.svg" alt="6675194.svg">
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
