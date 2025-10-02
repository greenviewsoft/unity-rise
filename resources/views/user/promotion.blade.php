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
       <h3 class="text-center">Promotion description</h3>
       <div class="telegram_boat"></div>
    </div>

    <div class="about-sc">
       <div class="about-to-banner">


<img src="{{ asset('public/'.$sitesetting->promotion_image) }}" alt="promotion_image ">



          
        </div>
       <div class="content about-description">
          @if($sitesetting && $sitesetting->promotion_content)
             {!! $sitesetting->promotion_content !!}
          @else
             <h4 class="title">{{ config('app.name') }} Agency Cooperation Program</h4>
             <p>{{ config('app.name') }} allows members to earn rewards by inviting friends and family to join. Provide a unique invitation code for others, allowing them to receive additional rewards from the invitees' first deposit.</p>

             <h4 class="title">Invitation qualifications</h4>
             <p>• The inviter must be a member of the  {{ config('app.name') }} platform
                • Inviters must fully comply with all terms and conditions in order to participate in the  {{ config('app.name') }} agency cooperation program
             </p>

             <h4 class="title">Basic information</h4>
             <p>When relatives and friends use your invitation code to register for a  {{ config('app.name') }}  account, it means that you have participated in the {{ config('app.name') }}  agency cooperation program, and that you fully and unconditionally agree to these terms and conditions, as well as the decision and interpretation of  {{ config('app.name') }} . These decisions and interpretations are final and binding on all matters relating to the plan.
             </p>

          <h4 class="title">Invite</h4>
          <p>• The invitation code can be used to scan the code when relatives and friends register. Relatives and friends scan the inviter's invitation code to become the main agent of the invitee.
          </p>

          <h4 class="title"> Reward method</h4>
          <p>  • The reward depends on the first deposit amount of the invited relatives and friends, and the relative position of the invitees in the agency program determines the rewards you can get.

          </p>
          @endif

     

       </div>
       <div class="card card-style more-link">
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
