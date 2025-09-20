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
       <h3 class="text-center">Account Details</h3>
       <div class="telegram_boat"><i class="bi bi-headset"></i></div>
    </div>

   <div class="account-details-sc">
      <div class="card accordion border-0 accordion-s" id="accordion-group-6">
          <div class="accordion-item">
             <button class="flex_button_middle accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion6-1" aria-expanded="false">
             <span class="font-600 font-13">Today</span>
             </button>
             <div id="accordion6-1" class="accordion-collapse collapse" data-bs-parent="#accordion-group-6" style="">
                <div class="pb-3">
                   <ul class="report event-list">
                      <div class="form-check form-check-custom">
                         <input checked="" class="form-check-input" type="checkbox" name="type" value="" id="c2a">
                         <label class="form-check-label" for="c2a">Today</label>
                         <i class="is-checked color-highlight font-13 bi bi-check-circle-fill"></i>
                         <i class="is-unchecked color-highlight font-13 bi bi-circle"></i>
                      </div>

                      <div class="form-check form-check-custom">
                         <input class="form-check-input" type="checkbox" name="type" value="" id="c2b">
                         <label class="form-check-label" for="c2b">Yesterday</label>
                         <i class="is-checked color-highlight font-13 bi bi-check-circle-fill"></i>
                         <i class="is-unchecked color-highlight font-13 bi bi-circle"></i>
                      </div>

                      <div class="form-check form-check-custom">
                         <input class="form-check-input" type="checkbox" name="type" value="" id="c2c">
                         <label class="form-check-label" for="c2c">Within a week</label>
                         <i class="is-checked color-highlight font-13 bi bi-check-circle-fill"></i>
                         <i class="is-unchecked color-highlight font-13 bi bi-circle"></i>
                      </div>

                      <div class="form-check form-check-custom">
                         <input class="form-check-input" type="checkbox" name="type" value="" id="c2d">
                         <label class="form-check-label" for="c2d">Customized </label>
                         <i class="is-checked color-highlight font-13 bi bi-check-circle-fill"></i>
                         <i class="is-unchecked color-highlight font-13 bi bi-circle"></i>
                      </div>

                   </ul>
                </div>
             </div>
          </div>
       </div>
       <div class="content account-details-balance-sc">
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>General withdrawal fee</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-danger">-3.28</span>
                <span>Balance: $100.99</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>Commission fee</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-danger">+0.20</span>
                <span>Balance: $118.87</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>Order refund</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-success">+101.96</span>
                <span>Balance: $118.87</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>General withdrawal fee</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-danger">-3.28</span>
                <span>Balance: $100.99</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>Commission fee</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-danger">+0.20</span>
                <span>Balance: $118.87</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>Order refund</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-success">+101.96</span>
                <span>Balance: $118.87</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>General withdrawal fee</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-danger">-3.28</span>
                <span>Balance: $100.99</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>Commission fee</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-danger">+0.20</span>
                <span>Balance: $118.87</span>
             </div>
          </div>
          <div class="card account-details-balance-box">
             <div class="account-text-date">
                <span>Order refund</span>
                <strong>2023-08-22 13:39:48</strong>
             </div>
             <div class="account-balance-info">
                <span class="text-success">+101.96</span>
                <span>Balance: $118.87</span>
             </div>
          </div>
       </div>
       <div class="fixed-total-ammount">
          <div class="item">
             <p>Total Recharge Amount : </p>
             <span>0</span>
          </div>
          <div class="item">
             <p>Total Withdrawal Amount : </p>
             <span>0</span>
          </div>
       </div>
   </div>
    
 </div>
@endsection



@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
