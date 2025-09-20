@extends('layouts.user.app')

@section('css')
<link rel="stylesheet" href="{{ asset('public/assets/user/styles/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/user/styles/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/user/styles/custom.css?var=1.12') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="manifest" href="_manifest.json">
<meta id="theme-check" name="theme-color" content="#6a3be4">
<link rel="shortcut icon" href="{{ asset('public/assets/user/images/logo.png') }}" type="image/x-icon">

<style>
/* ===== Root Variables ===== */
:root {
    --primary-purple: #6366f1;
    --secondary-purple: #8b5cf6;
    --bg-dark: #0f0f23;
    --bg-card: #1a1a2e;
    --text-primary: #ffffff;
    --text-secondary: #e2e8f0;
    --text-muted: #94a3b8;
    --gradient-primary: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa);
}

/* ===== Body ===== */
body {
    font-family: 'Inter', sans-serif;
    background: var(--bg-dark);
    background-image:
        radial-gradient(circle at 20% 80%, rgba(99,102,241,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(139,92,246,0.1) 0%, transparent 50%);
    color: var(--text-primary);
    min-height: 100vh;
    overflow-x: hidden;
}

/* ===== Page Content ===== */
.page-content {
    background: transparent;
    overflow-x: hidden;
    padding: 0 12px;
}

/* ===== Cards ===== */
.card-style {
    background: rgba(26,26,46,0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
}

.custom_ballet {
    background: rgba(26,26,46,0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 15px;
}

.card-style:hover, .custom_ballet:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 60px rgba(99,102,241,0.4);
}

/* ===== Custom Ballet Card ===== */
.custom_ballet .content {
    position: relative;
    z-index: 2;
    color: #fff;
}

/* ===== Slider Banner ===== */
.slider_banner {
    border-radius: 20px;
    overflow: hidden;
    margin: 20px 0;
    box-shadow: 0 12px 40px rgba(99,102,241,0.4);
}

.slider_banner img,
.slider_banner video {
    width: 100%;
    border-radius: 20px;
    object-fit: cover;
}

/* ===== User Balance ===== */
.balance_total h5 {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
}

.balance_total p {
    font-size: 28px;
    font-weight: 700;
    background: linear-gradient(135deg, #fff, var(--secondary-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ===== Total Balance Area ===== */
.total_balance_area a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    color: inherit;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.total_balance_area a:hover {
    background: rgba(99,102,241,0.1);
}

/* ===== Responsive Mobile Fix ===== */
@media (max-width: 768px) {
    .slider_banner {
        margin: 16px 0;
    }
    .balance_total p {
        font-size: 24px;
    }
    .total_balance_area h4 {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .slider_banner {
        margin: 12px 0;
    }
    .balance_total p {
        font-size: 20px;
    }
    .total_balance_area h4 {
        font-size: 14px;
    }
}


.menu_button .menu-label {
    font-size: 14px;
    font-weight: 500;
    color: #ffffff; /* <--- Ensures visibility on dark desktop bg */
    transition: color 0.3s ease;
}




   
}


</style>
@endsection
@section('content')
<div class="page-content footer-clear">
   @include('layouts.user.partial.header')
   <div class="slider_banner">
      @foreach ($events as $event)
      @if ($event->type == 'video')
      <div class="slingle_slider_area">
         <div class="video-file">
            <div class="bg-video-wrap">
               <video src="{{ asset($event->image) }}" loop=""
                  muted="" autoplay="">
               </video>
            </div>
         </div>
      </div>Deposit
      @endif
      @if ($event->type == 'image')
      <div class="slingle_slider_area">
         <div class="image_area">
            <img src="{{ asset($event->image) }}">
         </div>
      </div>
      @endif
      @endforeach
   </div>






   <div class="card card-style custom_ballet">
      <div class="content">
         <a href="{{ url('user/history') }}" class="d-flex py-1">
            <div class="align-self-center">
               <div class="logo">
                  <img src="{{ asset('public/assets/user/images/') }}/logo.png" alt="logo">
               </div>
            </div>
            <div class="top_battet align-self-center ps-1"> 
    <h5 class="pt-1 mb-n1">{{ __('lang.hello') }}</h5>
    <p class="mb-0 font-11">{{ Auth::user()->username }}</p>
    <div class="rank-display mt-1">
        <div class="rank-badge rank-{{ strtolower($userRankName) }}">
            <i class="bi bi-trophy-fill rank-icon"></i>
            <span class="rank-text">{{ $userRankName }}</span>
        </div>
    </div>
</div>

            <div class="align-self-center ms-auto text-end">
               <i class="refress bi bi-arrow-clockwise"></i>
            </div>
         </a>
         <a href="{{ url('user/history') }}" class="d-flex py-1">
            <div class="balance_total align-self-center ps-1">
               <h5 class="pt-1 mb-n1">{{ __('lang.total_assets') }}</h5>
               <p class="pt-2 mb-0 font-20">$ {{ Auth::user()->balance }}</p>
            </div>
         </a>
         <div class="divider my-2 opacity-50"></div>
         <div class="total_balance_area">
            <!-- Today Profits from Investment -->
            <a href="{{ url('user/history') }}" class="py-1">
               <div class="align-self-center ps-1">
                  <h5 class="pt-1 mb-n1">{{ __('lang.today_profit') }}</h5>
                
               </div>
               <div class="align-self-center ms-auto">
                  <h4 class="pt-1 mb-n1 color-blue-dark">${{ number_format($todaygrabs, 2) }}</h4>
               </div>
            </a>
            <div class="divider my-2 opacity-50"></div>
            
            <!-- Promotion Bonus from Refer Commission -->
            <a href="{{ url('user/history') }}" class="py-1">
               <div class="align-self-center ps-1">
                  <h5 class="pt-1 mb-n1">{{ __('lang.promo_bonus') }}</h5>
                 
               </div>
               <div class="align-self-center ms-auto">
                  <h4 class="pt-1 mb-n1 color-green-dark">${{ number_format($refercom, 2) }}</h4>
               </div>
            </a>
            <div class="divider my-2 opacity-50"></div>
            
            <!-- Accumulated Profits from Leader Rewards -->
            <a href="{{ url('user/history') }}" class="py-1">
               <div class="align-self-center ps-1">
                  <h5 class="pt-1 mb-n1">{{ __('lang.acc_profit') }}</h5>
                 
               </div>
               <div class="align-self-center ms-auto">
                  <h4 class="pt-1 mb-n1 color-purple-dark">${{ number_format($allrefercom + $allgrabs, 2) }}</h4>
               </div>
            </a>
         </div>
      </div>
   </div>

</br>

<div class="content menu_button">
    <div class="button_ruls d-flex flex-wrap text-center justify-content-center">

        <div class="menu-item mb-4">
            <a href="{{ route('user.deposit') }}" class="menu-link">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/piggy-bank.png') }}" alt="deposit">
                </div>
            </a>
            <h6 class="menu-label">{{ __('lang.deposit') }}</h6>
        </div>

        <div class="menu-item mb-4">
            <a href="{{ route('user.withdraw') }}" class="menu-link">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/atm.png') }}" alt="withdraw">
                </div>
            </a>
            <h6 class="menu-label">{{ __('lang.withdraw') }}</h6>
        </div>

        <div class="menu-item mb-4">
            <a href="{{ route('user.invite') }}" class="menu-link">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/friends.png') }}" alt="invite">
                </div>
            </a>
            <h6 class="menu-label">{{ __('lang.invite_friends') }}</h6>
        </div>

        <div class="menu-item mb-4">
            <a href="{{ route('user.team') }}" class="menu-link">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/analysis.png') }}" alt="team">
                </div>
            </a>
            <h6 class="menu-label">Our Team</h6>
        </div>

    </div>
</div>


   </br>
   <div class="task_loby content my-0 mt-n2 px-1">
      <div class="d-flex">
         <div class="align-self-center">
            <h3 class="font-16 mb-2">Investment Plans</h3>
         </div>
         {{-- <div class="align-self-center ms-auto">
            <a href="#" class="font-12 pt-1">{{ __('lang.view_all') }}</a>
         </div> --}}
      </div>
      
      <!-- Dynamic Investment Plans -->
      @if($investmentPlans && $investmentPlans->count() > 0)
         @foreach($investmentPlans as $index => $plan)
         <div class="featured-investment-card mt-3 p-3 rounded" style="background: linear-gradient(135deg, #6a3be4 0%, #8b5cf6 100%); color: white;">
            <div class="d-flex align-items-center mb-2">
               @if($index == 0)
               <span class="badge bg-warning text-dark me-2">FEATURED</span>
               @endif
               <h5 class="mb-0 text-white">{{ $plan->name }}</h5>
            </div>
            <div class="row">
               <div class="col-12">
                  <div class="mb-3">
                     <h4 class="text-white mb-1">${{ number_format($plan->min_amount) }} - ${{ number_format($plan->max_amount) }}</h4>
                     <div class="d-flex align-items-center mb-2">
                        <span class="me-3">💰 {{ $plan->daily_profit }}% Daily Profit for {{ $plan->duration_days }} Days</span>
                     </div>
                     <div class="d-flex align-items-center mb-2">
                        <span class="me-3">📈 Total Profit: {{ $plan->total_profit }}%</span>
                     </div>
                     @if($plan->description)
                     <div class="d-flex align-items-center mb-2">
                        <span class="me-3">ℹ️ {{ $plan->description }}</span>
                     </div>
                     @endif
                     <div class="d-flex align-items-center mb-3">
                        <span class="me-3">📅 Status: 
                           <span class="badge {{ $plan->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                              {{ $plan->status == 1 ? 'Active' : 'Inactive' }}
                           </span>
                        </span>
                     </div>
                  </div>
                  <div class="text-center">
                      <a href="{{ url('user/investment') }}" class="btn btn-light btn-sm px-4 py-2 fw-bold">Invest Now</a>
                   </div>
               </div>
            </div>
         </div>
         @endforeach
      @else
         <div class="featured-investment-card mt-3 p-3 rounded" style="background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%); color: white;">
            <div class="text-center py-4">
               <h5 class="text-white mb-3">No Investment Plans Available</h5>
               <p class="text-white-50">Please check back later for available investment opportunities.</p>
            </div>
         </div>
      @endif
   </div>


   <div class="card card-style loby_box_bg shadow-bg shadow-bg-s mb-4">
      <div class="content">
         <a href="{{ url('/user/investment') }}" class="text-center">
            <div class="align-self-center">
               <h1 class="mb-0 font-40"><img src="{{ asset('public/assets/user/images/') }}/bdfc20f.png" alt="wealth management"></h1>
            </div>
            <div class="loby-title align-self-center">
               <h5 class="">
                  {{ __('lang.wealth_management') }}
               </h5>
               <p>{{ __('lang.stable_income') }}</p>
            </div>
            <div class="align-self-center ms-auto">
               <i class="bi bi-arrow-right-short color-white d-block pt-1 font-20 opacity-50"></i>
            </div>
         </a>
      </div>
   </div>
   {{-- <div class="card card-style amazon_part gradient-red shadow-bg shadow-bg-s">
      <div class="content">
         <a href="{{ url('user/amazon') }}" class="d-flex">
            <div class="align-self-center">
               <h1 class="mb-0 font-40"><img src="{{ asset('public/assets/user/images/') }}/Amazon_icon.svg.png" alt="Amazon sales"></h1>
            </div>
            <div class="loby-title align-self-center">
               <h5 class="">
                  {{ __('lang.sales_commission') }}
               </h5>
            </div>
            <div class="align-self-center ms-auto">
               <i class="bi bi-arrow-right-short color-white d-block pt-1 font-20 opacity-50"></i>
            </div>
         </a>
      </div>
   </div> --}}
   <div class="content profit-list-users">
      <div class="title">
         <h2>{{ __('lang.profit_withdraw') }}</h2>
         <div class="slider">
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>26******pj</b> earn <b>$481.83</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>pb******n2</b> earn <b>$3649.68</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>ge******9r</b> earn <b>$4401.44</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>bh******wr</b> earn <b>$1125.68</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>43******0q</b> earn <b>$1261.63</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>ws******0f</b> earn <b>$45.00</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>kc******7f</b> earn <b>$101.24</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>7b******lj</b> earn <b>$646.7</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>jd******4t</b> earn <b>$80.44</b></span>
            </div>
            <div class="user_profit_area card card-style">
               <div class="user-icon">
                  <i class="bi bi-person-circle"></i>
               </div>
               <span><b>qn******gq</b> earn <b>$100.00</b></span>
            </div>
         </div>
      </div>
   </div>
   <div class="gap-tool"></div>
</div>



@endsection
@section('js')
<script>
$(document).ready(function() {
    // Other dashboard functionality can be added here
});
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.js"></script>
<script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
<script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
<script type="text/javascript">
   $(window).on('load', function() {
   
       $('#myModal').modal('show');
   
   });
</script>
<script>
   $(document).ready(function() {
   
       $(".slider").slick({
   
           centerMode: true,
   
           centerPadding: '0px',
   
           slidesToShow: 4,
   
           autoplay: true,
   
           vertical: true,
   
           verticalSwiping: true,
   
           arrows: false,
   
           swipeToSlide: true,
   
           focusOnSelect: true,
   
       });
   
   });
   
   
   
   $(document).ready(function() {
   
       $(".slider_banner").slick({
   
           centerMode: true,
   
           centerPadding: '0px',
   
           slidesToShow: 1,
   
           autoplay: true,
   
           verticalSwiping: true,
   
           arrows: false,
   
           swipeToSlide: true,
   
           focusOnSelect: true,
   
       });
   
   });
</script>
@endsection