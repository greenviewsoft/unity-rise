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
       <div class="arrow"><a href="{{ url('user/news') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
       <h3 class="text-center">{{ $announcement->livetext }}</h3>
       <div class="telegram_boat"></div>
    </div>

   <div class="news-event">
      <div class="row">
         <div class="col-12 mb-n2 text-start">

            <div class="" style="margin-left: 5px; margin-right:5xp;">
                {!! $announcement->announcement !!}
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
