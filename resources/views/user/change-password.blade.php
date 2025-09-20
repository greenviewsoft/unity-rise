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
            <h3 class="text-center">{{ __('lang.cng_login_password') }}</h3>
            <div class="telegram_boat"></div>
        </div>

        <div class="content loginpassword">

            <div class="chage_password_sc">
                <form class="personal_info" method="post" action="" id="myform">
                  @csrf
                    <div class="info_details">
                        <label for="c1a" class="form-label-always-active">{{ __('lang.org_login_password') }} :</label>
                        <input type="password" name="old_password" class="form-control" id="c1a"
                            placeholder="{{ __('lang.org_login_password') }}" />
                    </div>

                    <div class="info_details">
                        <label for="c1a" class="form-label-always-active">{{ __('lang.mew_login_password') }} :</label>
                        <input type="password" name="password" class="form-control" id="c1a"
                            placeholder="{{ __('lang.mew_login_password') }}" />
                    </div>

                    <div class="info_details">
                        <label for="c1a" class="form-label-always-active">{{ __('lang.conf_login_password') }} :</label>
                        <input type="password" name="password_confirmation" class="form-control" id="c1a"
                            placeholder="{{ __('lang.conf_login_password') }}" />
                    </div>

                    <button type="button" id="updatePassword"
                        class="btn-block btn btn-full gradient-highlight shadow-bg shadow-bg-s mt-4">{{ __('lang.confrim') }}</button>
                </form>
            </div>

        </div>

    </div>
@endsection



@section('js')
    <script>
        $("#updatePassword").click(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'GET',
                url: "{{ url('user/password-update') }}",
                data: $('#myform').serialize(),
                success: function(res) {
                    // alert(res)
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
        })
    </script>
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
