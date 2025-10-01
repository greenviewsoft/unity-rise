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

            <!-- Rank Progress Section -->
            <div class="rank-progress-section mb-4">
                <div class="card bg-dark text-white shadow-lg">
                    <div class="card-body p-3">
                        <h5 class="card-title text-white text-center mb-3">Rank Progress</h5>
                         <div class="row g-2">
                             <div class="col-6 col-md-3">
                                 <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                     <h6 class="text-white mb-1 small">Current Rank</h6>
                                     <span class="badge bg-primary px-2 py-1">{{ $current_rank_requirement['name'] ?? 'Rank ' . $user_rank }}</span>
                                 </div>
                             </div>
                             <div class="col-6 col-md-3">
                                 <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                     <h6 class="text-white mb-1 small">Total Business</h6>
                                     <span class="text-success fw-bold small">{{ number_format($current_rank_requirement['business_volume'] ?? 0, 2) }} USDT</span>
                                 </div>
                             </div>
                             <div class="col-6 col-md-3">
                                 <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                                     <h6 class="text-white mb-1 small">Completed</h6>
                                     <span class="text-info fw-bold small">{{ number_format($business_completed, 2) }} USDT</span>
                                 </div>
                             </div>
                             <div class="col-6 col-md-3">
                                 <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                     <h6 class="text-white mb-1 small">Remaining</h6>
                                     <span class="text-warning fw-bold small">{{ number_format($business_remaining, 2) }} USDT</span>
                                 </div>
                             </div>
                         </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-white-50">Progress to Next Rank ({{ $next_rank_requirement['name'] ?? 'Next Level' }})</small>
                                <small class="text-white fw-bold">
                                    @if($business_remaining > 0)
                                        {{ number_format($business_remaining, 2) }} USDT remaining
                                    @else
                                        Rank achieved! Next: {{ number_format($next_rank_requirement['business_volume'], 2) }} USDT
                                    @endif
                                </small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-gradient bg-success" role="progressbar" 
                                     style="width: {{ $next_rank_requirement['business_volume'] > 0 ? min(100, ($business_completed / $next_rank_requirement['business_volume']) * 100) : 0 }}%"
                                     aria-valuenow="{{ $business_completed }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ $next_rank_requirement['business_volume'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Statistics Section -->
            <div class="team-statistics-section mb-4">
                <div class="card bg-dark text-white shadow-lg">
                    <div class="card-body p-3">
                        <h5 class="card-title text-white text-center mb-3">Team Statistics</h5>
                        
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
                                    <h6 class="text-white mb-1 small">Direct Business</h6>
                                    <span class="text-info fw-bold small">{{ number_format($direct_business_total, 2) }} USDT</span>
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
                                    <h6 class="text-white mb-1 small">Total Downline Business</h6>
                                    <span class="text-info fw-bold small">{{ number_format($total_downline_business, 2) }} USDT</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Referral Income Section -->
                        <div class="row g-2">
                            <div class="col-12">
                                <h6 class="text-white-50 mb-2">Referral Income</h6>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-center p-3 bg-gradient bg-success bg-opacity-20 rounded">
                                    <h6 class="text-white mb-1">Total Referral Income</h6>
                                    <span class="text-white fw-bold h5">{{ number_format($total_referral_income, 2) }} USDT</span>
                                    <small class="d-block text-white-50 mt-1">All commission types combined</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="team_details_area">
                <div class="team_deposit_content">
                  
                            <div class="team_report_area_data">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-dark">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col" class="text-center">Level</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Account</th>
                                                <th scope="col" class="text-end">Invest</th>
                                                
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($refersusers as $refersuser)
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge bg-info px-2 py-1">L{{ $refersuser->level ?? 1 }}</span>
                                                    </td>
                                                    <td class="text-white small">{{ $refersuser->email }}</td>
                                                    <td class="text-white small">{{ $refersuser->username }}</td>
                                                    <td class="text-end text-white small">{{ number_format($refersuser->total_deposit, 2) }} <span class="text-muted">USDT</span></td>
                                                   
                                                   
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
