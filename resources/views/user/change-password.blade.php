@extends('layouts.user.app')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">
<link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link
    href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap"
    rel="stylesheet">

<style>
    /* ✅ Only change input field bg */
    .password-wrapper {
        position: relative;
        margin-bottom: 15px;
    }

    .password-wrapper .form-control {
        background-color: #f8f9fa !important; /* soft gray bg */
        color: #000 !important;
        border: 1px solid #ccc;
        border-radius: 8px;
        height: 45px;
        padding-right: 40px; /* space for eye icon */
    }

    .password-wrapper .form-control::placeholder {
        color: #999;
    }

    /* Focus style */
    .password-wrapper .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
    }

    /* Eye icon positioning */
    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #777;
    }

    .toggle-password:hover {
        color: #000;
    }

    .form-label-always-active {
        font-weight: 500;
        color: #333;
    }
</style>
@endsection

@section('content')
<div class="page-content footer-clear">
    <div class="page_top_title deposit_page">
        <div class="arrow"><a href="{{ url('user/userinfo') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>
        <h3 class="text-center">{{ __('lang.cng_login_password') }}</h3>
        <div class="telegram_boat"></div>
    </div>

    <div class="content loginpassword">
        <div class="chage_password_sc">
            <form class="personal_info" method="post" action="" id="myform">
                @csrf

                <div class="info_details password-wrapper">
                    <label class="form-label-always-active">{{ __('lang.org_login_password') }} :</label>
                    <input type="password" name="old_password" class="form-control password-field"
                        placeholder="{{ __('lang.org_login_password') }}">
                    <i class="bi bi-eye-slash toggle-password"></i>
                </div>

                <div class="info_details password-wrapper">
                    <label class="form-label-always-active">{{ __('lang.mew_login_password') }} :</label>
                    <input type="password" name="password" class="form-control password-field"
                        placeholder="{{ __('lang.mew_login_password') }}">
                    <i class="bi bi-eye-slash toggle-password"></i>
                </div>

                <div class="info_details password-wrapper">
                    <label class="form-label-always-active">{{ __('lang.conf_login_password') }} :</label>
                    <input type="password" name="password_confirmation" class="form-control password-field"
                        placeholder="{{ __('lang.conf_login_password') }}">
                    <i class="bi bi-eye-slash toggle-password"></i>
                </div>

                <button type="button" id="updatePassword"
                    class="btn btn-primary btn-block mt-4 w-100">{{ __('lang.confrim') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // ✅ Show/Hide password
    $(document).on('click', '.toggle-password', function() {
        let input = $(this).siblings('.password-field');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            $(this).removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            input.attr('type', 'password');
            $(this).removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    // ✅ Update password AJAX
    $("#updatePassword").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: "{{ url('user/password-update') }}",
            data: $('#myform').serialize(),
            success: function(res) {
                if (res.error) {
                    $("#snackbar2").text(res.error);
                    myFunction2();
                }
                if (res.success) {
                    $("#snackbar").text(res.success);
                    myFunction();
                    $('#transaction_id').val('');
                    window.location.href = "{{ url('user/mine') }}";
                }
                if (res.location) {
                    window.location.href = res.location;
                }
            }
        })
    });
</script>

<script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
<script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
