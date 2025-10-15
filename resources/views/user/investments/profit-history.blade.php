@extends('layouts.user.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #e8e9f3;
        }
        
        .page-content {
            background: transparent;
        }
        
        .main-container {
            background: rgba(30, 30, 60, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(138, 43, 226, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 20px rgba(138, 43, 226, 0.1);
            margin: 20px auto;
            max-width: 1200px;
            padding: 30px;
        }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-item {
            background: linear-gradient(135deg, #8a2be2 0%, #6a1b9a 50%, #4a148c 100%);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border: 1px solid rgba(138, 43, 226, 0.3);
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(138, 43, 226, 0.2);
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(138, 43, 226, 0.4);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }
        
        .stat-value.success {
            color: #4ade80;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }
        
        .section-title {
            color: #8a2be2;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            font-size: 1.5rem;
        }
        
        .section-title i {
            color: #8a2be2;
            margin-right: 10px;
        }
        
        
        .filter-section {
            background: rgba(40, 40, 80, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(138, 43, 226, 0.3);
        }
        
        .form-control {
            background: rgba(50, 50, 100, 0.4);
            border: 1px solid rgba(138, 43, 226, 0.3);
            color: #ffffff;
            border-radius: 10px;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            background: rgba(50, 50, 100, 0.6);
            border-color: #8a2be2;
            color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(138, 43, 226, 0.25);
        }
        
        .form-label {
            color: #b39ddb;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .btn-modern {
            background: linear-gradient(135deg, #8a2be2, #6a1b9a);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 5px 15px rgba(138, 43, 226, 0.3);
        }
        
        .btn-modern:hover {
            background: linear-gradient(135deg, #9c27b0, #7b1fa2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(138, 43, 226, 0.4);
            color: white;
        }
        
        .btn-export {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
        }
        
        .btn-export:hover {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 222, 128, 0.4);
            color: white;
        }
        
        .profit-table {
            background: rgba(40, 40, 80, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(138, 43, 226, 0.3);
        }
        
        .profit-table th {
            background: rgba(138, 43, 226, 0.3);
            color: #ffffff;
            font-weight: 600;
            padding: 15px;
            border: none;
        }
        
        .profit-table td {
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 15px;
            border: none;
            border-bottom: 1px solid rgba(138, 43, 226, 0.1);
        }
        
        .profit-table tbody tr:hover td {
            background: rgba(138, 43, 226, 0.2);
        }
        
        .badge-plan {
            background: linear-gradient(135deg, #8a2be2, #6a1b9a);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .profit-amount {
            color: #4ade80;
            font-weight: 700;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            background: rgba(40, 40, 80, 0.6);
            border-radius: 15px;
            border: 1px solid rgba(138, 43, 226, 0.3);
        }
        
        .no-data i {
            color: rgba(138, 43, 226, 0.5);
            margin-bottom: 20px;
        }
        
        .plan-breakdown {
            background: rgba(40, 40, 80, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(138, 43, 226, 0.3);
        }
        
        .plan-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(50, 50, 100, 0.4);
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .plan-item:hover {
            background: rgba(60, 60, 120, 0.5);
            border-color: rgba(138, 43, 226, 0.4);
        }
        
        .plan-name {
            font-weight: 600;
            color: #ffffff;
        }
        
        .plan-profit {
            color: #4ade80;
            font-weight: 700;
        }
        
        .plan-count {
            color: #b39ddb;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
            }
            
            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .stat-item {
                padding: 20px;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
            
            
            .filter-section {
                padding: 20px;
            }
            
            .profit-table {
                font-size: 0.8rem;
            }
            
            .profit-table th,
            .profit-table td {
                padding: 8px 5px;
            }
        }
        
        @media (max-width: 480px) {
            .stats-overview {
                grid-template-columns: 1fr;
            }
            
            .stat-item {
                padding: 15px;
            }
            
            .main-container {
                padding: 15px;
                margin: 5px;
            }
            
            .stat-value {
                font-size: 1.25rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="page-content">
    <div class="page-title page-title-small">
        <h2><a href="{{ url('user/dashboard') }}"><i class="fa fa-arrow-left"></i></a>Daily Profit History</h2>
        <a href="#" data-menu="menu-main" class="bg-fade-gray1-dark shadow-xl preload-img" data-src="{{ asset('public/assets/user/images/') }}/avatars/5s.png"></a>
    </div>
    <div class="card header-card shape-rounded" data-card-height="150">
        <div class="card-overlay bg-highlight opacity-95"></div>
        <div class="card-overlay dark-mode-tint"></div>
        <div class="card-bg preload-img" data-src="{{ asset('public/assets/user/images/') }}/pictures/20s.jpg"></div>
    </div>

    <div class="main-container">
        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-item">
                <div class="stat-value success">${{ number_format($statistics['total_profits'], 2) }}</div>
                <div class="stat-label">Total Profits</div>
            </div>
            <div class="stat-item">
                <div class="stat-value success">${{ number_format($statistics['average_daily_profit'], 2) }}</div>
                <div class="stat-label">Average Daily</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['total_days'] }}</div>
                <div class="stat-label">Profit Days</div>
            </div>
            <div class="stat-item">
                <div class="stat-value success">${{ number_format($statistics['max_daily_profit'], 2) }}</div>
                <div class="stat-label">Best Day</div>
            </div>
        </div>

        <!-- Date Filter Section -->
        <div class="filter-section">
            <h5 class="section-title">
                <i class="bi bi-funnel"></i>
                Filter by Date Range
            </h5>
            <form method="GET" action="{{ route('user.investment.profit-history') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $statistics['start_date'] }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $statistics['end_date'] }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern me-2">
                        <i class="bi bi-search"></i>
                        Filter
                    </button>
                    <a href="{{ route('user.investment.profit-history.export', ['start_date' => $statistics['start_date'], 'end_date' => $statistics['end_date']]) }}" class="btn btn-export">
                        <i class="bi bi-download"></i>
                        Export
                    </a>
                </div>
            </form>
        </div>


        <!-- Profit by Plan Breakdown -->
        @if($statistics['profit_by_plan']->count() > 0)
        <div class="plan-breakdown">
            <h5 class="section-title">
                <i class="bi bi-pie-chart"></i>
                Profit by Investment Plan
            </h5>
            @foreach($statistics['profit_by_plan'] as $plan)
            <div class="plan-item">
                <div>
                    <div class="plan-name">{{ $plan->plan_name }}</div>
                    <div class="plan-count">{{ $plan->profit_count }} profit entries</div>
                </div>
                <div class="plan-profit">${{ number_format($plan->total_profit, 2) }}</div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Daily Profit History Table -->
        <div class="profit-table">
            <h5 class="section-title" style="padding: 20px 20px 0 20px;">
                <i class="bi bi-list-ul"></i>
                Daily Profit Records
            </h5>
            
            @if($profitHistory->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Investment ID</th>
                            <th>Plan</th>
                            <th>Day #</th>
                            <th>Profit Amount</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitHistory as $profit)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($profit->profit_date)->format('M d, Y') }}</td>
                            <td>#{{ $profit->investment_id }}</td>
                            <td>
                                <span class="badge-plan">
                                    {{ $profit->investment->plan ? $profit->investment->plan->name : $profit->investment->plan_type }}
                                </span>
                            </td>
                            <td>{{ $profit->day_number }}</td>
                            <td class="profit-amount">${{ number_format($profit->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($profit->created_at)->format('H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $profitHistory->appends(request()->query())->links() }}
            </div>
            @else
            <div class="no-data">
                <i class="fas fa-chart-line fa-3x mb-3"></i>
                <h5 class="text-white mb-3">No Profit Records Found</h5>
                <p class="text-white-50 mb-4">No profit records found for the selected date range. Try adjusting your filters or check back later.</p>
                <a href="{{ route('user.investment.index') }}" class="btn btn-modern">
                    <i class="fas fa-plus me-2"></i>
                    Start Investing
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
    
@endsection
