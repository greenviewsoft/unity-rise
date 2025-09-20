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

            <h3 class="text-center">{{ __('lang.team_report') }}</h3>

            <div class="telegram_boat"></div>

        </div>



        <div class="content team-report-area">

            <div class="team_details_area">
                <div class="team_deposit_content">
                            <form action="">
                                <div class="row">

                                    <div class="col-6">
                                        <input type="date" class="form-control" name="from" placeholder="to">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" class="form-control" name="to" placeholder="to">
                                    </div>
                                    <div class="col-12">
                                        <div class="search_bar">
                                            <nav class="">
                                                <div class="">
                                                    <form>
                                                        <input class="form-control me-2" type="search"
                                                            placeholder="Search" aria-label="Search" name="key">
                                                        <button class="btn btn-outline-success" type="submit"><i
                                                                class="bi bi-search"></i></button>
                                                    </form>
                                                </div>
                                            </nav>
                                        </div>
                                    </div>


                                </div>
                            </form>
                            <div class="team_report_area_data">
                                <div class="">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                               
                                                <th scope="col">Email</th>
                                                <th scope="col">Account</th>
                                                <th scope="col">Deposit</th>
                                                <th scope="col">Profit</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($refersusers as $refersuser)
                                                <tr>
                                                    
                                                    <td style="color: white;">{{ $refersuser->email }}</td>
                                                    <td style="color: white;">{{ $refersuser->username }}</td>
                                                    <td style="color: white;">{{ number_format($refersuser->total_deposit, 2) }} USDT</td>
                                                    <td style="color: white;">{{ number_format($refersuser->total_profit, 2) }} USDT</td>
                                                  
                                                    <td>
                                        @if ($refersuser->total_deposit > 100)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">INACTIVE</span>
                                        @endif
                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

        </div>



    </div>
@endsection







@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>

    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>

    <script>
        $('.select-dropdown__button').on('click', function() {
            $('.select-dropdown__list').toggleClass('active');
        });

        $('.select-dropdown__list-item').on('click', function() {
            var itemValue = $(this).data('value');
            console.log(itemValue);
            $('.select-dropdown__button span').text($(this).text()).parent().attr('data-value', itemValue);
            $('.select-dropdown__list').toggleClass('active');
        });
    </script>
@endsection
