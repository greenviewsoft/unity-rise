@extends('layouts.user.app')





@section('css')

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">

    <!-- <link rel="stylesheet" type="text/css" href="fonts/bootstrap-icons.css"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

@endsection



@section('content')

<section class="wrapper-container login-register">

  <div class="page-content footer-clear">
      <div class="page_top_title deposit_page">

        <div class="arrow">

            <a href="{{ url('user/dashboard') }}">

                <i class="bi bi-arrow-left-circle-fill"></i>

            </a>

        </div>

        <h3 class="text-center">Invite friend</h3>

        <div class="telegram_boat"><i class="bi bi-headset"></i></div>

    </div>

      <div class="share_bg_area">
      
      <div class="container share-sc">
        <div class="code-gen">
            <div class="content">
               <p>Invitation code:</p>
               <h4>{{ Auth::user()->invitation_code }}</h4>
            </div>
            <div class="invite-bar"><a href="#" class="">Invitation reward</a></div>
         </div>

         <div class="qr-code-sc">
            @php
                $link = Auth::user()->invitation_code;

                $url = 'https://barcode.tec-it.com/barcode.ashx?data=' . $link . '&code=QRCode&eclevel=L';

            @endphp

            <img src="{{ $url }}" alt="bar">
         </div>
         
         <div class="copyboard">
            <input type="text" class="copy_text invite-link form-control" value="{{ url('index.html#/register/' . Auth::user()->invitation_code) }}">
            <button id="mycopy" class="share-btn shadow-bg shadow-bg-s" onclick="copyText()"><i class="bi bi-clipboard-check"></i> Copy Link</button>
         </div>
         <div class="refer_box"><a href="{{ url('user/guide') }}" class=""><img src="{{ asset('public/assets/user/assets/') }}/Images//gift.png" alt=""></a></div>
      </div>
   </div>
      
 </div>

 </section>

@endsection


@section('js') 

    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/my.js"></script>

@endsection

