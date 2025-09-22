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
            color: #ffffff;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(138, 43, 226, 0.3);
        }
        
        .individual-investments-container {
            margin-bottom: 30px;
        }
        
        .individual-investment-wrapper {
            background: linear-gradient(135deg, rgba(138, 43, 226, 0.1) 0%, rgba(106, 27, 154, 0.1) 100%);
            border: 2px solid rgba(138, 43, 226, 0.3);
            border-radius: 20px;
            padding: 0;
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(138, 43, 226, 0.2);
        }
        
        .individual-investment-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(138, 43, 226, 0.3);
            border-color: rgba(138, 43, 226, 0.5);
        }
        
        .investment-header {
            background: linear-gradient(135deg, #8a2be2 0%, #6a1b9a 100%);
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .investment-number {
            display: flex;
            align-items: center;
        }
        
        .investment-index {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .investment-title {
            flex: 1;
            margin-left: 10px;
        }
        
        .investment-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .individual-investment-card {
            background: rgba(40, 40, 80, 0.6);
            backdrop-filter: blur(5px);
            padding: 25px;
            border-radius: 0 0 18px 18px;
        }
        
        .investment-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .detail-item {
            background: rgba(50, 50, 100, 0.4);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(138, 43, 226, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }
        
        .detail-item:hover {
            background: rgba(60, 60, 120, 0.5);
            border-color: rgba(138, 43, 226, 0.4);
        }
        
        .detail-icon {
            background: linear-gradient(135deg, #8a2be2, #6a1b9a);
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.2rem;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .progress-section {
            background: rgba(50, 50, 100, 0.3);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .progress-label {
            color: #b39ddb;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .progress-percentage {
            color: #4ade80;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .progress-details {
            text-align: center;
            margin-top: 10px;
        }
        
        .active-investment-card, .investment-card {
            background: rgba(40, 40, 80, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(138, 43, 226, 0.3);
            transition: all 0.3s ease;
        }
        
        .active-investment-card:hover, .investment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(138, 43, 226, 0.2);
            border-color: rgba(138, 43, 226, 0.5);
        }
        
        .investment-history-section {
            background: rgba(40, 40, 80, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(138, 43, 226, 0.3);
        }
        
        .investment-detail-card {
            background: rgba(40, 40, 80, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(138, 43, 226, 0.3);
            transition: all 0.3s ease;
        }
        
        .investment-detail-card:hover {
            border-color: rgba(138, 43, 226, 0.5);
            box-shadow: 0 15px 35px rgba(138, 43, 226, 0.2);
            transform: translateY(-2px);
        }
        
        .field-group {
            background: rgba(50, 50, 100, 0.4);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .field-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .field-item {
            background: rgba(50, 50, 100, 0.4);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .field-label {
            font-size: 0.85rem;
            color: #b39ddb;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .field-value {
            font-size: 1rem;
            color: #ffffff;
            font-weight: 600;
        }
        
        .field-value.success {
            color: #4ade80;
        }
        
        .field-value.warning {
            color: #f59e0b;
        }
        
        .progress-modern {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-bar, .progress-bar-modern {
            background: linear-gradient(90deg, #8a2be2, #6a1b9a);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        
        .progress-bar-modern::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .profit-history-table, .table-modern {
            background: rgba(50, 50, 100, 0.4);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .table-modern {
            margin-bottom: 0;
        }
        
        .table-modern th {
            background: rgba(138, 43, 226, 0.3);
            color: #ffffff;
            font-weight: 600;
            padding: 15px;
            border: none;
        }
        
        .table-modern td {
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 15px;
            border: none;
            border-bottom: 1px solid rgba(138, 43, 226, 0.1);
        }
        
        .table-modern tbody tr:hover td {
            background: rgba(138, 43, 226, 0.2);
        }
        
        .badge, .badge-modern {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-active, .bg-success {
            background: linear-gradient(135deg, #4ade80, #22c55e) !important;
            color: white;
        }
        
        .badge-completed, .bg-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563) !important;
            color: white;
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
        
        .section-title {
            color: #ffffff;
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
        
        .no-investments {
            text-align: center;
            padding: 60px 20px;
            background: rgba(40, 40, 80, 0.6);
            border-radius: 15px;
            border: 1px solid rgba(138, 43, 226, 0.3);
        }
        
        .no-investments i {
            color: rgba(138, 43, 226, 0.5);
            margin-bottom: 20px;
        }
        
        .text-success {
            color: #4ade80 !important;
        }
        
        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        
        .alert-info {
            background: rgba(103, 58, 183, 0.2);
            border: 1px solid rgba(103, 58, 183, 0.3);
            color: #e8e9f3;
            border-radius: 12px;
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
            
            .field-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .active-investment-card, .investment-card {
                padding: 20px;
            }
            
            .stat-item {
                padding: 20px;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
            
            .active-investments-section,
            .investment-history-section {
                padding: 20px;
            }
            
            .investment-detail-card {
                padding: 20px;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
            
            .table-modern {
                font-size: 0.75rem;
            }
            
            .table-modern th,
            .table-modern td {
                padding: 8px 5px;
            }
            
            .investment-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .investment-number {
                justify-content: center;
            }
            
            .investment-details-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .detail-item {
                padding: 15px;
            }
            
            .detail-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
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
            
            .active-investment-card {
                padding: 15px;
            }
            
            .investment-detail-card {
                padding: 15px;
            }
            
            .individual-investment-wrapper {
                margin-bottom: 20px;
            }
            
            .investment-header {
                padding: 15px 20px;
            }
            
            .investment-index {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
            
            .individual-investment-card {
                padding: 20px;
            }
            
            .progress-section {
                padding: 15px;
            }
        }
    </style>
@endsection

@section('content')
<div class="page-content">
    <div class="page-title page-title-small">
        <h2><a href="{{ url('user/dashboard') }}"><i class="fa fa-arrow-left"></i></a>Investment History</h2>
        <a href="#" data-menu="menu-main" class="bg-fade-gray1-dark shadow-xl preload-img" data-src="{{ asset('public/assets/user/images/') }}/avatars/5s.png"></a>
    </div>
    <div class="card header-card shape-rounded" data-card-height="150">
        <div class="card-overlay bg-highlight opacity-95"></div>
        <div class="card-overlay dark-mode-tint"></div>
        <div class="card-bg preload-img" data-src="{{ asset('public/assets/user/images/') }}/pictures/20s.jpg"></div>
    </div>

    <div class="content mt-0">
        <!-- Comprehensive Investment Statistics -->
        <div class="stats-overview">
            <h5 class="section-title text-center mb-4">
                <i class="fas fa-chart-pie"></i>
                Investment Overview
            </h5>
            <div class="row g-3">
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="stat-item">
                        <div class="stat-value">{{ $statistics['total_investments'] }}</div>
                        <div class="stat-label">Total Investments</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="stat-item">
                        <div class="stat-value">{{ $statistics['active_investments'] }}</div>
                        <div class="stat-label">Active</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="stat-item">
                        <div class="stat-value">{{ $statistics['completed_investments'] }}</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="stat-item">
                        <div class="stat-value">${{ number_format($statistics['total_invested'], 0) }}</div>
                        <div class="stat-label">Total Invested</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="stat-item">
                        <div class="stat-value">${{ number_format($statistics['total_active_amount'], 0) }}</div>
                        <div class="stat-label">Active Amount</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="stat-item">
                        <div class="stat-value">${{ number_format($statistics['total_profit_earned'], 0) }}</div>
                        <div class="stat-label">Total Profit</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Individual Active Investments Section -->
        @if($statistics['active_investments_list']->count() > 0)
        <div class="individual-investments-container">
            <h5 class="section-title mb-4">
                <i class="fas fa-wallet"></i>
                Your Active Investments ({{ $statistics['active_investments_list']->count() }})
            </h5>
            
            @foreach($statistics['active_investments_list'] as $index => $activeInvestment)
            <div class="individual-investment-wrapper mb-4">
                <div class="investment-header">
                    <div class="investment-number">
                        <span class="investment-index">{{ $index + 1 }}</span>
                    </div>
                    <div class="investment-title">
                        <h6 class="mb-0 text-white fw-bold">Investment #{{ $activeInvestment->id }}</h6>
                        <span class="badge badge-modern badge-active">{{ ucfirst($activeInvestment->plan_type) }}</span>
                    </div>
                    <div class="investment-status">
                        <i class="fas fa-circle text-success"></i>
                        <span class="text-success fw-bold">ACTIVE</span>
                    </div>
                </div>
                
                <div class="individual-investment-card">
                    <div class="investment-details-grid">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="detail-content">
                                <div class="field-label">Investment Amount</div>
                                <div class="field-value success">${{ number_format($activeInvestment->amount, 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="detail-content">
                                <div class="field-label">Daily Profit</div>
                                <div class="field-value success">${{ number_format($activeInvestment->daily_profit, 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="detail-content">
                                <div class="field-label">Active Days</div>
                                <div class="field-value">{{ $activeInvestment->active_days }} days</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="detail-content">
                                <div class="field-label">Remaining Days</div>
                                <div class="field-value warning">{{ $activeInvestment->remaining_days }} days</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress-section">
                        <div class="progress-info">
                            <span class="progress-label">Investment Progress</span>
                            <span class="progress-percentage">{{ number_format($activeInvestment->progress_percentage, 1) }}%</span>
                        </div>
                        <div class="progress-modern">
                            <div class="progress-bar-modern" style="width: {{ $activeInvestment->progress_percentage }}%"></div>
                        </div>
                        <div class="progress-details">
                            <small class="text-white-50">
                                {{ $activeInvestment->profit_days_completed }} of {{ $activeInvestment->plan_duration }} days completed
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Investment Message -->
        @if($statistics['active_investments_list']->count() == 0)
        <div class="no-investments">
            <i class="fas fa-chart-line fa-3x mb-3"></i>
            <h5 class="text-white mb-3">No Active Investments</h5>
            <p class="text-white-50 mb-4">You don't have any active investments at the moment. Start investing to see your portfolio here.</p>
            <a href="{{ route('user.investment.index') }}" class="btn btn-modern">
                <i class="fas fa-plus me-2"></i>
                Start Investing
            </a>
        </div>
        @endif

    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection