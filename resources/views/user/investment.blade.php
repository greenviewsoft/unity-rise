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
        /* Dark Purple Theme for Investment Plans */
        .plan-option {
            background: linear-gradient(135deg, #1a1a2e 0%, #1f1b3a 100%);
            border: 2px solid rgba(138, 43, 226, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .plan-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(138, 43, 226, 0.3);
            border-color: #8a2be2;
        }

        .plan-option.selected {
            border: 3px solid #8a2be2 !important;
            box-shadow: 0 10px 30px rgba(138, 43, 226, 0.5);
            background: linear-gradient(135deg, #2b1a3f 0%, #3c1a6a 100%);
        }

        .plan-option h6 {
            color: #ffffff;
        }

        .plan-option small {
            color: #b3b3ff;
        }

        .plan-option .badge {
            background: linear-gradient(135deg, #6a3be4, #8b5cf6);
            color: #fff;
            font-weight: 600;
        }

        /* Form & Details Cards */
        .balance-info, .profit-calculation {
            background: #1f1a3a;
            border: 1px solid #6a3be4;
            color: #ffffff;
        }

        .balance-info .text-success {
            color: #4ade80 !important;
        }

        .balance-info .text-primary {
            color: #6a3be4 !important;
        }

        .profit-calculation h6 {
            color: #b3b3ff;
        }

        .profit-calculation .fw-bold {
            color: #ffffff;
        }

        .form-control {
            background: #2b1a3f;
            color: #ffffff;
            border: 1px solid #6a3be4;
        }

        .form-control:focus {
            border-color: #8a2be2;
            box-shadow: 0 0 5px rgba(138, 43, 226, 0.5);
            background: #3c1a6a;
            color: #ffffff;
        }

        /* Buttons */
        .btn-primary, .btn-lg {
            background: linear-gradient(135deg, #6a3be4 0%, #8b5cf6 100%);
            color: #fff;
            border: none;
        }

        .btn-primary:hover, .btn-lg:hover {
            background: linear-gradient(135deg, #7b49f0 0%, #9c5cff 100%);
        }

        /* Section Titles */
        .section-title {
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            font-size: 1.5rem;
        }

        .section-title i {
            color: #8a2be2;
            margin-right: 10px;
        }

        /* Alerts */
        .alert-success, .alert-danger {
            background: #2b1a3f;
            color: #ffffff;
            border: 1px solid #6a3be4;
        }

        .alert-success i, .alert-danger i {
            color: #8a2be2;
        }
    </style>
@endsection

@section('content')
<div class="page-content">
    <div class="page-title page-title-small">
        <h2><a href="{{ url('user/dashboard') }}"><i class="fa fa-arrow-left"></i></a>Investment</h2>
    </div>

    <div class="content mt-0">
        <!-- Investment Plans Selection -->
        @if($investmentPlans->count() > 0)
            <div class="card card-style">
                <div class="content mb-0">
                    <h5 class="section-title mb-3"><i class="fas fa-chart-line me-2"></i>Choose Investment Plan</h5>
                    <div class="row">
                        @foreach($investmentPlans as $plan)
                            <div class="col-12 mb-3">
                                <div class="plan-option p-3 rounded" data-plan-id="{{ $plan->id }}" 
                                     data-plan-name="{{ $plan->name }}" 
                                     data-min-amount="{{ $plan->min_amount }}" 
                                     data-max-amount="{{ $plan->max_amount }}" 
                                     data-daily-profit="{{ $plan->daily_profit_percentage }}" 
                                     data-duration="{{ $plan->duration_days }}">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h6 class="mb-1 fw-bold">{{ $plan->name }}</h6>
                                            <small>{{ $plan->description }}</small>
                                            <div class="mt-2">
                                                <span class="badge me-2">{{ $plan->daily_profit_percentage }}% Daily</span>
                                                <span class="badge me-2">{{ $plan->duration_days }} Days</span>
                                                <span class="badge">${{ number_format($plan->min_amount) }} - ${{ number_format($plan->max_amount) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="text-success fw-bold">{{ $plan->total_profit_percentage }}%</div>
                                            <small>Total Return</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="card card-style">
                <div class="content text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5>No Investment Plans Available</h5>
                </div>
            </div>
        @endif

        <!-- Investment Form -->
        <div class="card card-style mt-3">
            <div class="content">
                <h5 class="section-title mb-4"><i class="fas fa-dollar-sign me-2"></i>Investment Details</h5>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form id="investmentForm" action="{{ route('user.investment.create') }}" method="POST" style="{{ $investmentPlans->count() == 0 ? 'display: none;' : '' }}">
                    @csrf
                    <input type="hidden" name="plan_id" id="selectedPlanId" value="">
                    <input type="hidden" name="plan_type" id="selectedPlanType" value="">
                    
                    <!-- Balance & Selected Plan -->
                    <div class="mb-4 balance-info p-3 rounded">
                        <div class="row">
                            <div class="col-6">
                                <small>Current Balance</small>
                                <div class="fw-bold text-success h6">${{ number_format(Auth::user()->balance, 2) }}</div>
                            </div>
                            <div class="col-6">
                                <small>Selected Plan</small>
                                <div class="fw-bold text-primary h6" id="selectedPlanName">Select a plan above</div>
                            </div>
                        </div>
                    </div>

                  <!-- Investment Amount -->
<div class="mb-4">
    <label for="investment_amount" class="form-label">Investment Amount</label>
    <div class="input-group">
        <span class="input-group-text" style="background: #6a3be4; color: #00ff00; border: none;">$</span>
        <input type="number" class="form-control" id="investment_amount" name="amount" 
               min="0" max="0" step="1" required disabled
               placeholder="Select a plan first" autocomplete="off"
               value="{{ old('amount') }}"
               style="background: #3c1a6a; color: #ffffff; border: 1px solid #6a3be4;">
    </div>
    <div class="form-text mt-2">
        <small class="text-muted" id="amountRange">Select a plan to see investment range</small>
    </div>
    <div id="amountError" class="text-danger mt-2" style="display: none;"></div>
</div>


                    <!-- Profit Calculation -->
                    <div class="mb-4 profit-calculation p-3 rounded">
                        <h6 class="mb-3">Profit Calculation</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <small>Daily Profit</small>
                                <div class="fw-bold text-success" id="dailyProfit">$0.00</div>
                            </div>
                            <div class="col-4">
                                <small>Total Profit (<span id="durationDays">0</span> days)</small>
                                <div class="fw-bold text-info" id="totalProfit">$0.00</div>
                            </div>
                            <div class="col-4">
                                <small>Total Return</small>
                                <div class="fw-bold text-primary" id="totalReturn">$0.00</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lg fw-bold"><i class="fas fa-rocket me-2"></i>Confirm Investment</button>
                        <a href="{{ url('user/dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    let selectedPlan = null;
    
    $('.plan-option').on('click', function() {
        $('.plan-option').removeClass('selected');
        $(this).addClass('selected');
        
        selectedPlan = {
            id: $(this).data('plan-id'),
            name: $(this).data('plan-name'),
            minAmount: parseFloat($(this).data('min-amount')),
            maxAmount: parseFloat($(this).data('max-amount')),
            dailyProfit: parseFloat($(this).data('daily-profit')),
            duration: parseInt($(this).data('duration'))
        };
        
        $('#selectedPlanId').val(selectedPlan.id);
        $('#selectedPlanType').val(selectedPlan.name.toLowerCase().replace(/\s+/g, '_'));
        $('#selectedPlanName').text(selectedPlan.name);
        
        const amountInput = $('#investment_amount');
        amountInput.attr('min', selectedPlan.minAmount);
        amountInput.attr('max', selectedPlan.maxAmount);
        amountInput.attr('placeholder', `Enter amount (${selectedPlan.minAmount} - ${selectedPlan.maxAmount})`);
        amountInput.prop('disabled', false);
        $('#amountRange').text(`Minimum: $${selectedPlan.minAmount.toLocaleString()} | Maximum: $${selectedPlan.maxAmount.toLocaleString()}`);
        $('#durationDays').text(selectedPlan.duration);
        amountInput.val('').trigger('input');
    });

    $('#investment_amount').on('input', function() {
        if (!selectedPlan) return;

        const amount = parseFloat($(this).val()) || 0;
        const userBalance = {{ Auth::user()->balance ?? 0 }};
        const errorDiv = $('#amountError');
        const submitBtn = $('#investmentForm button[type="submit"]');

        if (amount < selectedPlan.minAmount) {
            errorDiv.text(`Minimum investment amount is $${selectedPlan.minAmount.toLocaleString()}`).show();
            submitBtn.prop('disabled', true);
        } else if (amount > selectedPlan.maxAmount) {
            errorDiv.text(`Maximum investment amount is $${selectedPlan.maxAmount.toLocaleString()}`).show();
            submitBtn.prop('disabled', true);
        } else if (amount > userBalance) {
            errorDiv.text('Insufficient balance for this investment').show();
            submitBtn.prop('disabled', true);
        } else {
            errorDiv.hide();
            submitBtn.prop('disabled', false);
        }

        if (amount >= selectedPlan.minAmount && amount <= selectedPlan.maxAmount) {
            const dailyProfit = amount * selectedPlan.dailyProfit / 100;
            const totalProfit = dailyProfit * selectedPlan.duration;
            const totalReturn = amount + totalProfit;

            $('#dailyProfit').text('$' + dailyProfit.toFixed(2));
            $('#totalProfit').text('$' + totalProfit.toFixed(2));
            $('#totalReturn').text('$' + totalReturn.toFixed(2));
        } else {
            $('#dailyProfit').text('$0.00');
            $('#totalProfit').text('$0.00');
            $('#totalReturn').text('$0.00');
        }
    });

    $('#investmentForm').on('submit', function(e) {
        if (!selectedPlan) {
            e.preventDefault();
            alert('Please select an investment plan first');
            return false;
        }

        const amount = parseFloat($('#investment_amount').val()) || 0;
        const userBalance = {{ Auth::user()->balance ?? 0 }};

        if (amount < selectedPlan.minAmount || amount > selectedPlan.maxAmount || amount > userBalance) {
            e.preventDefault();
            alert(`Please enter a valid investment amount between $${selectedPlan.minAmount.toLocaleString()} and $${selectedPlan.maxAmount.toLocaleString()}`);
            return false;
        }

        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...').prop('disabled', true);
    });
});
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
<script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection
