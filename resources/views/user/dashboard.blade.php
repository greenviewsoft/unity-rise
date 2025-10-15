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
}

/* ===== Ranking Progress Styles ===== */
.ranking-progress-section {
    background: linear-gradient(135deg, rgba(26,26,46,0.95), rgba(30,30,60,0.95));
    border-radius: 16px;
    padding: 20px;
    border: 1px solid rgba(99,102,241,0.3);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(99,102,241,0.15);
}

.rank-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

.rank-title {
    color: var(--text-primary);
    font-weight: 700;
    font-size: 16px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rank-title::before {
    content: "🎯";
    font-size: 18px;
}

.bonus-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.bonus-amount {
    color: #10b981;
    font-weight: 700;
    font-size: 18px;
    line-height: 1;
    text-shadow: 0 2px 4px rgba(16,185,129,0.3);
}

.bonus-label {
    color: var(--text-muted);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(16,185,129,0.1);
    padding: 2px 6px;
    border-radius: 4px;
    margin-top: 2px;
}

.progress-item {
    margin-bottom: 18px;
    padding: 12px;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.08);
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.progress-label {
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.progress-label::before {
    content: "";
    width: 8px;
    height: 8px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 50%;
    display: inline-block;
}

.progress-value {
    color: var(--text-primary);
    font-size: 14px;
    font-weight: 700;
}

.progress-bar-container {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.progress-bar {
    flex: 1;
    height: 10px;
    background: rgba(255,255,255,0.1);
    border-radius: 6px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899);
    border-radius: 6px;
    transition: width 0.5s ease;
    box-shadow: 0 2px 8px rgba(99,102,241,0.4);
    position: relative;
}

.progress-fill::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-percent {
    color: var(--text-primary);
    font-size: 12px;
    font-weight: 700;
    min-width: 45px;
    text-align: right;
    background: rgba(99,102,241,0.1);
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid rgba(99,102,241,0.2);
}

.remaining-amount {
    color: #f59e0b;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
    background: rgba(245,158,11,0.1);
    padding: 2px 6px;
    border-radius: 4px;
}

.unlock-status {
    padding: 12px 16px;
    border-radius: 12px;
    text-align: center;
    margin-top: 8px;
}

.unlock-ready {
    background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.25));
    border: 1px solid rgba(16,185,129,0.4);
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 4px 16px rgba(16,185,129,0.2);
    animation: pulse-green 2s infinite;
}

.unlock-pending {
    background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.25));
    border: 1px solid rgba(245,158,11,0.4);
    color: #f59e0b;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 4px 16px rgba(245,158,11,0.2);
}

@keyframes pulse-green {
    0%, 100% { box-shadow: 0 4px 16px rgba(16,185,129,0.2); }
    50% { box-shadow: 0 6px 20px rgba(16,185,129,0.4); }
}

.max-rank-achieved {
    background: linear-gradient(135deg, rgba(26,26,46,0.95), rgba(30,30,60,0.95));
    border-radius: 16px;
    padding: 20px;
    border: 1px solid rgba(251,191,36,0.4);
    text-align: center;
    box-shadow: 0 8px 32px rgba(251,191,36,0.15);
    margin-top: 16px;
}

.max-rank-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #fbbf24;
    font-size: 16px;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(251,191,36,0.3);
    animation: glow-gold 3s ease-in-out infinite;
}

@keyframes glow-gold {
    0%, 100% { text-shadow: 0 2px 4px rgba(251,191,36,0.3); }
    50% { text-shadow: 0 4px 8px rgba(251,191,36,0.6); }
}

.max-rank-badge i {
    font-size: 18px;
}

/* Simple Rank Section */
.simple-rank-section {
    text-align: center;
    margin: 20px 0;
}

.rank-unlock-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: #fff;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    cursor: pointer;
}

.rank-unlock-btn:hover {
    background: linear-gradient(135deg, #2980b9, #3498db);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    color: #fff;
    text-decoration: none;
}



.max-rank-section {
    text-align: center;
    margin: 20px 0;
}

.max-rank-section .max-rank-badge {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: #fff;
    padding: 15px 25px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 600;
    box-shadow: 0 6px 20px rgba(243, 156, 18, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Rank Badge Styles */
.rank-display {
    margin-top: 8px;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    background: var(--gradient-primary);
    color: white;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.rank-icon {
    font-size: 12px;
}

.rank-text {
    line-height: 1;
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



/* ---------- CMC-mini ---------- */
.cmc-mini{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px}
.cmc-mini::-webkit-scrollbar{height:0}
.cmc-card{flex:0 0 140px;background:rgba(26,26,46,.8);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px;color:#fff;font-size:13px}
.cmc-card.up{border-color:#10b981}
.cmc-card.down{border-color:#ef4444}
.cmc-symbol{font-weight:600;display:flex;align-items:center;gap:4px}
.cmc-price{font-size:15px;font-weight:700;margin:4px 0}
.cmc-chg{font-size:12px}
.cmc-chg.up{color:#10b981}
.cmc-chg.down{color:#ef4444}
.cmc-card .d-flex{gap:10px}
.cmc-card img[src*="/sparklines/"]{border-radius:4px;background:rgba(255,255,255,.05)}
/* sparkline dark-mode + crisp */
.cmc-card img[src*="/sparklines/"]{
  background: rgba(255,255,255,.03);
  border-radius: 4px;
  image-rendering: -webkit-optimize-contrast; /* chrome */
  image-rendering: crisp-edges;              /* firefox */
  filter: brightness(1.15) contrast(1.2);    /* pop the line */
}

/* skeleton shimmer while loading */
#cmc-mini:empty{
  display:flex;gap:8px;
}
#cmc-mini:empty::after{
  content:"";
  flex:0 0 140px;height:70px;
  background:linear-gradient(90deg,rgba(99,102,241,.1) 40%,rgba(139,92,246,.2) 50%,rgba(99,102,241,.1) 60%);
  background-size:200% 100%;
  animation:shimmer 1.2s infinite;
  border-radius:12px;
}
@keyframes shimmer{to{background-position:-200% 0}}

/* ===== Customer Support Styles ===== */
.support-link {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.3s ease;
}

.support-link:hover {
    text-decoration: none;
    color: inherit;
    transform: translateY(-2px);
}

.support-card {
    background: rgba(26,26,46,0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.support-card:hover {
    background: rgba(26,26,46,0.9);
    border-color: rgba(99,102,241,0.4);
    box-shadow: 0 8px 32px rgba(99,102,241,0.1);
}

.support-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
}

.support-link:hover .support-icon {
    transform: scale(1.1);
}

.support-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-primary);
    margin: 0;
}

/* Profile Photo Styles */
.profile-photo-section {
    position: relative;
    display: inline-block;
}

.profile-photo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-purple);
    transition: all 0.3s ease;
}

.photo-upload-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    background: var(--primary-purple);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid var(--bg-dark);
}

.photo-upload-overlay:hover {
    background: var(--secondary-purple);
    transform: scale(1.1);
}

.photo-upload-overlay i {
    font-size: 10px;
    color: white;
}

#photoInput {
    display: none;
}


/* Toast Notification Styles */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(16, 185, 129, 0.95);
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 9999;
    animation: slideInRight 0.3s ease;
}

.toast-notification.error {
    background: rgba(239, 68, 68, 0.95);
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
/* Toast Notification Styles */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(16, 185, 129, 0.95);
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 9999;
    animation: slideInRight 0.3s ease;
}

.toast-notification.error {
    background: rgba(239, 68, 68, 0.95);
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
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
        
             

         <div class="align-self-center">
    <a href="{{ route('user.userinfo') }}" style="text-decoration: none;">
        <div class="profile-photo-section">
            @if(Auth::user()->photo && file_exists(('public/uploads/profile/' . Auth::user()->photo)))
                <img src="{{ asset('public/uploads/profile/' . Auth::user()->photo) }}" alt="Profile Photo" class="profile-photo">
            @else
                <img src="{{ asset('public/assets/user/images/logo.png') }}" alt="Default Avatar" class="profile-photo">
            @endif
            
            <!-- Click করলে userinfo page এ যাবে -->
            <div class="photo-upload-overlay">
                <i class="bi bi-pencil-fill"></i>
            </div>
        </div>
    </a>
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

<!-- Simple Ranking Section -->
@if($next_rank_requirement)
<div class="simple-rank-section mt-3">
    <a href="{{ route('user.rank.requirements') }}" class="rank-unlock-btn">
        <i class="bi bi-lock-fill"></i>
        <span>Complete to unlock</span>
    </a>
</div>
@else
<div class="max-rank-section mt-3">
    <div class="max-rank-badge">
        <i class="bi bi-crown-fill"></i>
        <span>Maximum Rank Achieved!</span>
    </div>
</div>


@endif



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

         <!-- Financial Stats Cards Grid -->
         <div class="row g-3 mt-2">
            <!-- Today Profits -->
            <div class="col-6">
               <a href="{{ route('user.investment.profit-history') }}" style="text-decoration: none;">
                  <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                     <div class="d-flex align-items-center mb-2">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px; margin-right: 12px;">
                           <i class="bi bi-graph-up-arrow" style="font-size: 24px; color: #fff;"></i>
                        </div>
                        <div class="flex-grow-1">
                           <p class="mb-0" style="font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">Today Profit</p>
                           <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 18px;">${{ number_format($todaygrabs, 2) }}</h4>
                        </div>
                     </div>
                  </div>
               </a>
            </div>

            <!-- Referral Income -->
            <div class="col-6">
               <a href="{{ url('/user/team') }}" style="text-decoration: none;">
                  <div class="card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                     <div class="d-flex align-items-center mb-2">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px; margin-right: 12px;">
                           <i class="bi bi-people-fill" style="font-size: 24px; color: #fff;"></i>
                        </div>
                        <div class="flex-grow-1">
                           <p class="mb-0" style="font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">Referral Income</p>
                           <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 18px;">${{ number_format($total_referral_income, 2) }}</h4>
                        </div>
                     </div>
                  </div>
               </a>
            </div>

            <!-- Team Income -->
            <div class="col-6">
               <a href="{{ url('/user/team') }}" style="text-decoration: none;">
                  <div class="card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border: none; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);">
                     <div class="d-flex align-items-center mb-2">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px; margin-right: 12px;">
                           <i class="bi bi-trophy-fill" style="font-size: 24px; color: #fff;"></i>
                        </div>
                        <div class="flex-grow-1">
                           <p class="mb-0" style="font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">Team Income</p>
                           <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 18px;">${{ number_format($allrankrewards, 2) }}</h4>
                        </div>
                     </div>
                  </div>
               </a>
            </div>

            <!-- Total Deposit -->
            <div class="col-6">
               <a href="{{ url('user/record?type=deposit') }}" style="text-decoration: none;">
                  <div class="card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);">
                     <div class="d-flex align-items-center mb-2">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px; margin-right: 12px;">
                           <i class="bi bi-wallet2" style="font-size: 24px; color: #fff;"></i>
                        </div>
                        <div class="flex-grow-1">
                           <p class="mb-0" style="font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">Total Deposit</p>
                           <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 18px;">${{ number_format($total_deposit, 2) }}</h4>
                        </div>
                     </div>
                  </div>
               </a>
            </div>

            <!-- Total Withdrawal -->
            <div class="col-6">
               <a href="{{ url('user/record?type=withdraw') }}" style="text-decoration: none;">
                  <div class="card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);">
                     <div class="d-flex align-items-center mb-2">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px; margin-right: 12px;">
                           <i class="bi bi-cash-stack" style="font-size: 24px; color: #fff;"></i>
                        </div>
                        <div class="flex-grow-1">
                           <p class="mb-0" style="font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">Total Withdrawal</p>
                           <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 18px;">${{ number_format($total_withdrawal, 2) }}</h4>
                        </div>
                     </div>
                  </div>
               </a>
            </div>
            
           <!-- Total Investment -->
<div class="col-6">
   <a href="{{ url('/user/investment/history') }}" style="text-decoration: none;">
      <div class="card" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border: none; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.35);">
         <div class="d-flex align-items-center mb-2">
            <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px; margin-right: 12px;">
               <i class="bi bi-piggy-bank-fill" style="font-size: 24px; color: #fff;"></i>
            </div>
            <div class="flex-grow-1">
               <p class="mb-0" style="font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">Total Investment</p>
               <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 18px;">
                  ${{ number_format($total_investment, 2) }}
               </h4>
            </div>
         </div>
      </div>
   </a>
</div>

            
         </div>
      </div>
   </div>

</br>


<!-- CoinMarketCap-mini -->
<div class="container mb-3">
  <h6 class="text-white-50 mb-2">Live Market Trade</h6>
  <div id="cmc-mini" class="cmc-mini"></div>
</div>

<!-- Team Statistics Section -->
<div class="card card-style bg-dark text-white shadow-lg mb-4">
   <div class="content">
      <h5 class="text-white text-center mb-3">Team Statistics</h5>
      
      <!-- Direct Members Section -->
      <div class="row g-2 mb-3">
         <div class="col-12">
            <h6 class="text-white-50 mb-2">Direct Members</h6>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Total Direct</h6>
               <span class="text-primary fw-bold">{{ $direct_member_count }} Members</span>
            </div>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Active</h6>
               <span class="text-success fw-bold">{{ $direct_active_members }} Members</span>
            </div>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Inactive</h6>
               <span class="text-warning fw-bold">{{ $direct_inactive_members }} Members</span>
            </div>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-info bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Direct B</h6>
               <span class="text-info fw-bold small">${{ number_format($direct_business_total, 2) }}</span>
            </div>
         </div>
      </div>
      
      <!-- Total Team Section -->
      <div class="row g-2 mb-3">
         <div class="col-12">
            <h6 class="text-white-50 mb-2">Total Team (All Downline Levels)</h6>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-purple bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Total Team</h6>
               <span class="text-light fw-bold">{{ $total_team_members }} Members</span>
            </div>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Active Team</h6>
               <span class="text-success fw-bold">{{ $total_active_team_members }} Members</span>
            </div>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Inactive Team</h6>
               <span class="text-warning fw-bold">{{ $total_inactive_team_members }} Members</span>
            </div>
         </div>
         <div class="col-6 col-md-3">
            <div class="text-center p-2 bg-info bg-opacity-10 rounded">
               <h6 class="text-white mb-1 small">Total TB</h6>
               <span class="text-info fw-bold small">${{ number_format($total_downline_business, 2) }}</span>
            </div>
         </div>
      </div>
      
     
   </div>
</div>

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
@php
$telegram = DB::table('social_links')
    ->where('name', 'Telegram Support')
    ->where('is_active', 1)
    ->first();

    $youtube = DB::table('social_links')
    ->where('name', 'Youtube Support')
    ->where('is_active', 1)
    ->first();


    $zoom = DB::table('social_links')
    ->where('name', 'Zoom Suppprt')
    ->where('is_active', 1)
    ->first();
    $telegramGroups = DB::table('social_links')
    ->where('name', 'Telegram Groups')
    ->where('is_active', 1)
    ->first();

    $buy = DB::table('social_links')
    ->where('name', 'Buy')
    ->where('is_active', 1)
    ->first();

    $sell = DB::table('social_links')
    ->where('name', 'Sell')
    ->where('is_active', 1)
    ->first();



@endphp

<div class="content menu_button">
    <div class="button_ruls d-flex flex-wrap text-center justify-content-center">



        
        @if($telegram)
        <div class="menu-item mb-4">
            <a href="{{ $telegram->url }}" class="menu-link" target="_blank">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/customer-service.png') }}" alt="telegram">
                </div>
           
            <h6 class="menu-label">{{ $telegram->name }}</h6> </a>
        </div>
        @endif
        @if($youtube)
        <div class="menu-item mb-4">
            <a href="{{ $youtube->url }}" class="menu-link" target="_blank">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/youtube.png') }}" alt="withdraw">
                </div>
           
            <h6 class="menu-label">Youtube</h6> </a>
        </div>
        @endif
        @if($zoom)
        <div class="menu-item mb-4">
            <a href="{{ $zoom->url }}" class="menu-link" target="_blank">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/zoom.png') }}" alt="invite">
                </div>
          
            <h6 class="menu-label">Zoom</h6> </a>
        </div>
        @endif
        @if($telegramGroups)
        <div class="menu-item mb-4">
            <a href="{{ $telegramGroups->url }}" class="menu-link" target="_blank">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/telegram.png') }}" alt="team">
                </div>
           
            <h6 class="menu-label">Telegram Groups</h6> </a>
     
        </div>
        @endif

    </div>
</div>



<div class="content menu_button">
    <div class="button_ruls d-flex flex-wrap text-center justify-content-center">

        <div class="menu-item mb-4">
            <a href="{{ URL('/user/rule') }}" class="menu-link">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/court.png') }}" alt="deposit">
                </div>
            </a>
            <h6 class="menu-label">Rules</h6>
        </div>

        <div class="menu-item mb-4">
            <a href="{{ URL('/user/about') }}" class="menu-link">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/aboutX.png') }}" alt="withdraw">
                </div>
            </a>
            <h6 class="menu-label">About</h6>
        </div>
@if($buy)
        <div class="menu-item mb-4">
            <a href="{{ $buy->url }}" class="menu-link" target="_blank">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/buy.png') }}" alt="invite">
                </div>
          
            <h6 class="menu-label">BUY</h6>
        </div>
        @endif
        @if($sell)
        <div class="menu-item mb-4">
            <a href="{{ $sell->url }}" class="menu-link" target="_blank">
                <div class="menu-icon-wrapper">
                    <img class="menu-icon" src="{{ asset('public/assets/user/images/selling.png') }}" alt="team">
                </div>
    
            <h6 class="menu-label">Sell</h6> </a>
        </div>
@endif
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
  
    <!-- Profit Withdrawal -->
   <div class="content profit-list-users">
      <div class="title">
         <h2>Profit Withdrawal</h2>
         <div class="slider">
            @forelse($profitwithdraws as $withdrawal)
               <div class="user_profit_area card card-style">
                  <div class="user-icon">
                     <i class="bi bi-person-circle"></i>
                  </div>
                  <span><b>{{ substr($withdrawal->username, 0, 2) }}******{{ substr($withdrawal->username, -2) }}</b> earn <b>${{ number_format($withdrawal->amount, 2) }}</b></span>
               </div>
            @empty
               <div class="user_profit_area card card-style">
                  <div class="user-icon">
                     <i class="bi bi-person-circle"></i>
                  </div>
                  <span><b>No profit withdrawals yet</b></span>
               </div>
            @endforelse
         </div>
      </div>
   </div>
   
   <!-- Customer Support Section -->
   {{-- <div class="card card-style">
      <div class="content">
         <h4 class="mb-3 text-white">
            <i class="fas fa-headset me-2"></i>Customer Support
         </h4>
         <p class="text-white mb-4">Need help? Contact our support team through any of these channels:</p>
         
         <div class="row">
            @php
               $socialLinks = \App\Models\SocialLink::getActiveLinks();
            @endphp
            
            @if($socialLinks->count() > 0)
               @foreach($socialLinks as $link)
                  <div class="col-6 col-md-4 col-lg-3 mb-3">
                     <a href="{{ $link->url }}" target="_blank" class="support-link">
                        <div class="support-card">
                           <div class="support-icon" style="color: {{ $link->color }};">
                              <i class="{{ $link->icon }}"></i>
                           </div>
                           <div class="support-name">{{ $link->name }}</div>
                        </div>
                     </a>
                  </div>
               @endforeach
            @else
               <div class="col-12">
                  <div class="text-center py-4">
                     <i class="fas fa-headset fa-3x text-muted mb-3"></i>
                     <p class="text-muted">Support channels will be available soon</p>
                  </div>
               </div>
            @endif
         </div>
      </div>
   </div>
    --}}
   <div class="gap-tool"></div>
</div>



@endsection

@section('js')

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


/* ---------- CMC-mini (icon + sparkline) ---------- */
(async()=>{
  const list = [                       // id, name, CMC coin-id
    {id:'bitcoin',       name:'BTC',  cmc:'1'},
    {id:'tether',        name:'USDT', cmc:'825'} ,
    {id:'ethereum',      name:'ETH',  cmc:'1027'},
    {id:'binancecoin',   name:'BNB',  cmc:'1839'},
    {id:'solana',        name:'SOL',  cmc:'5426'},
    
  ];
  const ids = list.map(i=>i.id).join(',');
  const url = `https://api.coingecko.com/api/v3/simple/price?ids=${ids}&vs_currencies=usd&include_24hr_change=true`;
  const res = await fetch(url);
  const data = await res.json();
  const box = document.getElementById('cmc-mini');
  box.innerHTML = '';          // clear old

  list.forEach(item=>{
    const p = data[item.id].usd;
    const c = data[item.id].usd_24h_change || 0;
    const up = c >= 0;
    const iconSrc = `https://s2.coinmarketcap.com/static/img/coins/64x64/${item.cmc}.png`;
    const sparkSrc = `https://s3.coinmarketcap.com/generated/sparklines/web/7d/2781/${item.cmc}.svg`;

    box.insertAdjacentHTML('beforeend', `
      <div class="cmc-card ${up?'up':'down'}">
        <div class="d-flex align-items-center justify-content-between">
          <!-- left: icon + name + price -->
          <div>
            <div class="cmc-symbol">
              <img src="${iconSrc}" width="20" height="20">
              <span>${item.name}</span>
            </div>
            <div class="cmc-price">$${p.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})}</div>
            <div class="cmc-chg ${up?'up':'down'}">
              ${up?'▲':'▼'} ${Math.abs(c).toFixed(2)}%
            </div>
          </div>
          <!-- right: 7d sparkline -->
          <img src="${sparkSrc}" width="60" height="30" alt="sparkline" style="opacity:.8">
        </div>
      </div>`);
  });
})();


// let reloadLock = false;
// setInterval(() => {
//   if (!reloadLock) {
//     reloadLock = true;
//     location.reload();
//   }
// }, 10000);
</script>


@endsection