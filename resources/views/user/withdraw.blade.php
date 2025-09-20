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
            <h3 class="text-center">{{ __('lang.withdraw') }}</h3>
            <div class="telegram_boat"></div>
        </div>

        <form action="" id="myform" autocomplete="off">
            @csrf
            <div class="withdraw-page-sc">
                <div class="content withdraw-page-top">
                    <h5>{{ __('lang.withdraw_to') }}</h5>
                    @if (Auth::user()->crypto_address == null)
                    <a href="{{ url('user/address-setup') }}">{{ __('lang.select_wallet') }}</a>
                    @endif

                </div>
                <div class="content wallet-verification-sc">
                    <div class="card verified-box">
                        <div class="logo-content">
                            <img src="{{ asset('public/assets/user/images/') }}/logo.png" alt="bep20_usdt">
                            <h4>BEP20 USDT</h4>
                        </div>
                        <div class="address-wallet">
                            @php
                                $first = substr(Auth::user()->crypto_address, 0, 4);
                                $last = substr(Auth::user()->crypto_address, -3);
                            @endphp
                            <span>{{ $first }} **** **** {{ $last }}</span>
                        </div>
                    </div>
                </div>
                <div class="content enter-ammount-balance">
                    <p>{{ __('lang.enter_cash_withdraw') }}</p>
                    <span class="text-danger">{{ __('lang.balance') }}：{{ $authbal }} </span>
                </div>
                <div class="content input-ammount-box">
                    <input type="text" id="quantity" name="quantity" class="form-control" placeholder="Please Enter">
                </div>


                <div class="content cash-withdrawal">
                    <p>{{ __('lang.gen_withdraw_fees') }} : <span class="text-end">{{ $settingtrx->withdraw_vat }}</span></p>
                    <p>{{ __('lang.add_withdraw_fees') }} : <span class="text-end" id="push_vat">0</span></p>
                </div>
                <div class="card card-style notice-withdrawl">
                    <h5><i class="bi bi-volume-up-fill"></i>{{ __('lang.withdraw_notice') }}</h5>
                    <p>{{ __('lang.reminder') }}</p>
                </div>

                <div class="content button-cash">
                    <button type="button" id="Withdrawal"
                        class="btn btn-block btn-full gradient-highlight shadow-bg shadow-bg-xs">{{ __('lang.cash_withdraw') }}</button>
                </div>
            </div>
        </form>


    </div>
@endsection



@section('js')
    <script>
        function isDoubleClicked(element) {
            //if already clicked return TRUE to indicate this click is not allowed
            if (element.data("isclicked")) return true;

            //mark as clicked for 1 second
            element.data("isclicked", true);
            setTimeout(function() {
                element.removeData("isclicked");
            }, 1000);

            //return FALSE to indicate this click was allowed
            return false;
        }

        $(document).ready(function() {
            // Attach an event listener to the input box for the 'change' event
            $('#quantity').on('keyup', function() {
                // Get the entered value
                var enteredValue = $(this).val();
                var percentage = "{{ $settingtrx->withdraw_vat }}";

                // Check if the entered value is a valid number
                if (!isNaN(enteredValue)) {
                    // Calculate 100% of 3% of the entered value
                    var result = ((parseFloat(enteredValue) / 100) * percentage).toFixed(2);

                    // Update the input box with the calculated result
                    $('#push_vat').text(result);
                } else {
                    alert('Please enter a valid number.');
                }
            });
        });


        $('#Withdrawal').on("click", function() {
            if (isDoubleClicked($(this))) return;


            $.ajax({
                type: 'post',
                url: "{{ url('user/withdraw/validate') }}",
                data: $("#myform").serialize(),
                success: function(res) {
                    console.log(res);
                    if (res.error) {
                        $("#snackbar2").text(res.error);
                        myFunction2();
                    }
                    if (res.success) {
                        $("#snackbar").text(res.success);
                        myFunction();
                        $('#quantity').val('');
                    }
                    if (res.location) {
                        location.reload();
                    }
                }
            })
        });
    </script>
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
