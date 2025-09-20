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
<div class="page-content">
    <div class="page-title page-title-small">
        <h2><a href="{{ url('user/dashboard') }}"><i class="fa fa-arrow-left"></i></a>Investment</h2>
        <a href="#" data-menu="menu-main" class="bg-fade-gray1-dark shadow-xl preload-img" data-src="{{ asset('public/assets/user/images/') }}/avatars/5s.png"></a>
    </div>
    <div class="card header-card shape-rounded" data-card-height="150">
        <div class="card-overlay bg-highlight opacity-95"></div>
        <div class="card-overlay dark-mode-tint"></div>
        <div class="card-bg preload-img" data-src="{{ asset('public/assets/user/images/') }}/pictures/20s.jpg"></div>
    </div>

    <div class="content mt-0">
        <!-- Investment Plans Selection -->
        @if($investmentPlans->count() > 0)
            <div class="card card-style">
                <div class="content mb-0">
                    <h5 class="mb-3"><i class="fas fa-chart-line me-2" style="color: #6a3be4;"></i>Choose Investment Plan</h5>
                    <div class="row">
                        @foreach($investmentPlans as $plan)
                            <div class="col-12 mb-3">
                                <div class="plan-option p-3 rounded border" data-plan-id="{{ $plan->id }}" 
                                     data-plan-name="{{ $plan->name }}" 
                                     data-min-amount="{{ $plan->min_amount }}" 
                                     data-max-amount="{{ $plan->max_amount }}" 
                                     data-daily-profit="{{ $plan->daily_profit_percentage }}" 
                                     data-duration="{{ $plan->duration_days }}" 
                                     style="cursor: pointer; transition: all 0.3s;">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h6 class="mb-1 fw-bold">{{ $plan->name }}</h6>
                                            <small class="text-muted">{{ $plan->description }}</small>
                                            <div class="mt-2">
                                                <span class="badge bg-success me-2">{{ $plan->daily_profit_percentage }}% Daily</span>
                                                <span class="badge bg-info me-2">{{ $plan->duration_days }} Days</span>
                                                <span class="badge bg-primary">${{ number_format($plan->min_amount) }} - ${{ number_format($plan->max_amount) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="text-success fw-bold">{{ $plan->total_profit_percentage }}%</div>
                                            <small class="text-muted">Total Return</small>
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
                    <p class="text-muted">Please check back later for available investment plans.</p>
                </div>
            </div>
        @endif

        <!-- Investment Form -->
        <div class="card card-style">
            <div class="content">
                <h5 class="mb-4"><i class="fas fa-dollar-sign me-2" style="color: #6a3be4;"></i>Investment Details</h5>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form id="investmentForm" action="{{ route('user.investment.create') }}" method="POST" style="{{ $investmentPlans->count() == 0 ? 'display: none;' : '' }}">
                    @csrf
                    <input type="hidden" name="plan_id" id="selectedPlanId" value="">
                    <input type="hidden" name="plan_type" id="selectedPlanType" value="">
                    
                    <!-- Current Balance Display -->
                    <div class="mb-4">
                        <div class="balance-info p-3 rounded" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Current Balance</small>
                                    <div class="fw-bold text-success h6">${{ number_format(Auth::user()->balance, 2) }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Selected Plan</small>
                                    <div class="fw-bold text-primary h6" id="selectedPlanName">Select a plan above</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Amount Input -->
                    <div class="mb-4">
                        <label for="investment_amount" class="form-label">
                            <i class="fas fa-dollar-sign me-2" style="color: #6a3be4;"></i>
                            Investment Amount
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #6a3be4; color: white; border: none;">$</span>
                            <input type="number" class="form-control" id="investment_amount" name="amount" 
                                   min="0" max="0" step="1" required disabled
                                   placeholder="Select a plan first" autocomplete="off"
                                   value="{{ old('amount') }}">
                        </div>
                        <div class="form-text mt-2">
                            <small class="text-muted" id="amountRange">💡 Select a plan to see investment range</small>
                        </div>
                        <div id="amountError" class="text-danger mt-2" style="display: none;"></div>
                        @error('amount')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Profit Calculation Display -->
                    <div class="mb-4">
                        <div class="profit-calculation p-3 rounded" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                            <h6 class="mb-3">💰 Profit Calculation</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted">Daily Profit</small>
                                    <div class="fw-bold text-success" id="dailyProfit">$0.00</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Total Profit (<span id="durationDays">0</span> days)</small>
                                    <div class="fw-bold text-info" id="totalProfit">$0.00</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Total Return</small>
                                    <div class="fw-bold text-primary" id="totalReturn">$0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lg fw-bold" 
                                style="background: linear-gradient(135deg, #6a3be4 0%, #8b5cf6 100%); color: white; border: none; border-radius: 10px;">
                            <i class="fas fa-rocket me-2"></i>
                            Confirm Investment
                        </button>
                        <a href="{{ url('user/dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
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
    
    // Plan selection functionality
    $('.plan-option').on('click', function() {
        // Remove previous selection
        $('.plan-option').removeClass('border-primary').addClass('border');
        
        // Add selection to clicked plan
        $(this).removeClass('border').addClass('border-primary');
        
        // Get plan data
        selectedPlan = {
            id: $(this).data('plan-id'),
            name: $(this).data('plan-name'),
            minAmount: parseFloat($(this).data('min-amount')),
            maxAmount: parseFloat($(this).data('max-amount')),
            dailyProfit: parseFloat($(this).data('daily-profit')),
            duration: parseInt($(this).data('duration'))
        };
        
        // Update form fields
        $('#selectedPlanId').val(selectedPlan.id);
        $('#selectedPlanType').val(selectedPlan.name.toLowerCase().replace(/\s+/g, '_'));
        $('#selectedPlanName').text(selectedPlan.name);
        
        // Update amount input
        const amountInput = $('#investment_amount');
        amountInput.attr('min', selectedPlan.minAmount);
        amountInput.attr('max', selectedPlan.maxAmount);
        amountInput.attr('placeholder', `Enter amount (${selectedPlan.minAmount} - ${selectedPlan.maxAmount})`);
        amountInput.prop('disabled', false);
        
        // Update amount range text
        $('#amountRange').text(`💡 Minimum: $${selectedPlan.minAmount.toLocaleString()} | Maximum: $${selectedPlan.maxAmount.toLocaleString()}`);
        
        // Update duration display
        $('#durationDays').text(selectedPlan.duration);
        
        // Clear previous amount and recalculate
        amountInput.val('').trigger('input');
        
        console.log('Plan selected:', selectedPlan);
    });
    
    // Investment amount validation and calculation
    $('#investment_amount').on('input', function() {
        if (!selectedPlan) {
            return;
        }
        
        const amount = parseFloat($(this).val()) || 0;
        const userBalance = {{ Auth::user()->balance ?? 0 }};
        const errorDiv = $('#amountError');
        const submitBtn = $('#investmentForm button[type="submit"]');
        
        console.log('Input changed - Amount:', amount, 'Balance:', userBalance, 'Plan:', selectedPlan);
        
        // Validation
        if (amount < selectedPlan.minAmount) {
            errorDiv.text(`⚠️ Minimum investment amount is $${selectedPlan.minAmount.toLocaleString()}`).show();
            submitBtn.prop('disabled', true);
        } else if (amount > selectedPlan.maxAmount) {
            errorDiv.text(`⚠️ Maximum investment amount is $${selectedPlan.maxAmount.toLocaleString()}`).show();
            submitBtn.prop('disabled', true);
        } else if (amount > userBalance) {
            errorDiv.text('⚠️ Insufficient balance for this investment').show();
            submitBtn.prop('disabled', true);
        } else {
            errorDiv.hide();
            submitBtn.prop('disabled', false);
        }
        
        // Calculate profits
        if (amount >= selectedPlan.minAmount && amount <= selectedPlan.maxAmount) {
            const dailyProfitRate = selectedPlan.dailyProfit / 100;
            const dailyProfit = amount * dailyProfitRate;
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
    
    // Form submission validation
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
        
        // Show loading state
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...').prop('disabled', true);
    });
});
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
<script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
@endsection