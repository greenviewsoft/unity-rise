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
       <h3 class="text-center">Rule description</h3>
       <div class="telegram_boat"></div>
    </div>

    <div class="about-sc">
       <div class="about-to-banner">
          <img src="{{ asset('public/assets/user/images/') }}/rule.png" alt="about">
       </div>
       <div class="content about-description">
          <h4 class="title">About Deposit</h4>
          <p>Since {{ config('app.name') }} members come from various countries and use different currencies, {{ config('app.name') }} employs cryptocurrency transactions to streamline the process. Please ensure to verify {{ config('app.name') }}'s USDT address diligently before recharging (the platform recharge address is subject to change, and users must visit the platform for the latest recharge address before proceeding). For any inquiries, kindly reach out to customer service.</p>

          <h4 class="title">About order amount</h4>
          <p>Order amount paid by {{ config('app.name') }} members is determined by the member's account balance, access to orders, and markets conditions on the day. The maximum number of orders each account can send per day depends on your user level.</p>

          <h4 class="title">About order-grab commission</h4>
          <div class="border border-blue-dark rounded-s overflow-hidden mb-4">
          <table class="text-center table color-theme border-blue-dark ">
             <thead class="">
                <tr class="color-white">
                   <th scope="col">
                      <h5 class="color-white font-15 mb-0">Level</h5>
                   </th>
                   <th scope="col">
                      <h5 class="color-white font-15 mb-0">Income </h5>
                   </th>
                   <th scope="col">
                      <h5 class="color-white font-15 mb-0">Daily amount</h5>
                   </th>
                </tr>
             </thead>
             <tbody>
                <tr>
                   <td><strong>VIP 1</strong></td>
                   <td>earns </td>
                   <td>2 USDT</td>
                </tr>

                <tr>
                   <td><strong>VIP 2</strong></td>
                   <td>earns </td>
                   <td>7.2 USDT</td>
                </tr>

                <tr>
                   <td><strong>VIP 3</strong></td>
                   <td>earns </td>
                   <td>13 USDT </td>
                </tr>

                <tr>
                   <td><strong>VIP 4</strong></td>
                   <td>earns </td>
                   <td>27 USDT</td>
                </tr>

                <tr>
                   <td><strong>VIP 5</strong></td>
                   <td>earns </td>
                   <td>65 USDT</td>
                </tr>

                <tr>
                   <td><strong>VIP 6</strong></td>
                   <td>earns </td>
                   <td>136 USDT </td>
                </tr>

                <tr>
                   <td><strong>VIP 7</strong></td>
                   <td>earns </td>
                   <td>340 USDT </td>
                </tr>
                
                <tr>
                   <td><strong>VIP 8</strong></td>
                   <td>earns </td>
                   <td>550 USDT </td>
                </tr>
                
                <tr>
                   <td><strong>VIP 9</strong></td>
                   <td>earns </td>
                   <td>1368 USDT</td>
                </tr>

                <!--<tr>-->
                <!--   <td><strong>VIP 8</strong></td>-->
                <!--   <td>0.13%</td>-->
                <!--   <td>14</td>-->
                <!--</tr>-->

                <!--<tr>-->
                <!--   <td><strong>VIP 9</strong></td>-->
                <!--   <td>0.13%</td>-->
                <!--   <td>14</td>-->
                <!--</tr>-->

                <!--<tr>-->
                <!--   <td><strong>VIP 10</strong></td>-->
                <!--   <td>0.13%</td>-->
                <!--   <td>14</td>-->
                <!--</tr>-->

                <!--<tr>-->
                <!--   <td><strong>VIP 11</strong></td>-->
                <!--   <td>0.13%</td>-->
                <!--   <td>14</td>-->
                <!--</tr>-->

                <!--<tr>-->
                <!--   <td><strong>VIP 2</strong></td>-->
                <!--   <td>0.13%</td>-->
                <!--   <td>14</td>-->
                <!--</tr>-->
                
             </tbody>
          </table>
       </div>

       <h4 class="title">About withdrawal</h4>
       <p>The withdrawal time of {{ config('app.name') }} is usually within 24 hours, and there is no fee charges upon withdrawal.
        <br><br>

        ※At the request of relevant departments, in order to prevent members from being suspected of money laundering and other illegal activities, {{ config('app.name') }} members need to complete the order volume corresponding to the user level every day before they can withdraw.

       </p>

       <h4 class="title">About level requirements</h4>
       <p> You can increase your user level by completing various metrics. Please refer to the current level progress, benefits and upgrade conditions for each level.

       </p>

       <h4 class="title">Disclaimer</h4>
       <p> • In the event of fraud or other related behaviors, including but not limited to the above examples, {{ config('app.name') }} reserves the right to warn or freeze your account.

       <br>

       • In the event of fraud or other related behaviors, including but not limited to the above examples, {{ config('app.name') }} reserves the right to warn or freeze your account.

       <br>

       </p>

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
