@extends('layouts.admin.app')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Investment Plan</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.investment-plans.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.investment-plans.update', $plan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name', $plan->name) }}" 
                                           placeholder="Enter plan name" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="duration">Duration (Days) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                           id="duration" name="duration" 
                                           value="{{ old('duration', $plan->duration_days) }}" 
                                           min="1" max="3650" required>
                                    @error('duration')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="min_amount">Minimum Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('min_amount') is-invalid @enderror" 
                                           id="min_amount" name="min_amount" 
                                           value="{{ old('min_amount', $plan->min_amount) }}" 
                                           step="0.01" min="0" required>
                                    @error('min_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="max_amount">Maximum Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_amount') is-invalid @enderror" 
                                           id="max_amount" name="max_amount" 
                                           value="{{ old('max_amount', $plan->max_amount) }}" 
                                           step="0.01" min="0" required>
                                    @error('max_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="daily_profit_percentage">Daily Profit (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('daily_profit_percentage') is-invalid @enderror" 
                                           id="daily_profit_percentage" name="daily_profit_percentage" 
                                           value="{{ old('daily_profit_percentage', $plan->daily_profit_percentage) }}" 
                                           step="0.01" min="0" max="100" required>
                                    @error('daily_profit_percentage')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Users will earn this percentage daily on their investment amount</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', $plan->status ? 'active' : 'inactive') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $plan->status ? 'active' : 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" 
                                              placeholder="Enter plan description">{{ old('description', $plan->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Profit Calculator Preview -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Profit Calculator Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Test Investment ($)</label>
                                        <input type="number" class="form-control" id="test_investment" 
                                               placeholder="1000" step="0.01" min="1" value="1000">
                                    </div>
                                    <div class="col-md-8">
                                        <label>Profit Breakdown</label>
                                        <div id="profit_preview" class="mt-2">
                                            <!-- Profit preview will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Statistics -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Plan Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Investors</span>
                                                <span class="info-box-number">{{ $plan->investments_count ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Invested</span>
                                                <span class="info-box-number">${{ number_format($plan->total_invested ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Profits</span>
                                                <span class="info-box-number">${{ number_format($plan->total_profits ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-primary">
                                            <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Created</span>
                                                <span class="info-box-number">{{ $plan->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Investment Plan
                        </button>
                        <a href="{{ route('admin.investment-plans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        @if($plan->status == 'active')
                            <button type="button" class="btn btn-warning" onclick="toggleStatus('inactive')">
                                <i class="fas fa-pause"></i> Deactivate Plan
                            </button>
                        @else
                            <button type="button" class="btn btn-success" onclick="toggleStatus('active')">
                                <i class="fas fa-play"></i> Activate Plan
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
<script>
$(document).ready(function() {
    // Update profit preview
    function updateProfitPreview() {
        const investment = parseFloat($('#test_investment').val()) || 0;
        const dailyProfit = parseFloat($('#daily_profit_percentage').val()) || 0;
        const totalProfit = parseFloat($('#total_profit_percentage').val()) || 0;
        const duration = parseInt($('#duration').val()) || 0;
        
        if (investment > 0 && dailyProfit > 0 && duration > 0) {
            const dailyAmount = (investment * dailyProfit) / 100;
            const totalDailyProfit = dailyAmount * duration;
            const totalProfitAmount = (investment * totalProfit) / 100;
            const totalReturn = investment + totalProfitAmount;
            
            const html = `
                <div class="row">
                    <div class="col-md-3">
                        <strong>Daily Profit:</strong><br>
                        <span class="badge bg-success">$${dailyAmount.toFixed(2)}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Total Daily Profits:</strong><br>
                        <span class="badge bg-info">$${totalDailyProfit.toFixed(2)}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Total Profit:</strong><br>
                        <span class="badge bg-warning">$${totalProfitAmount.toFixed(2)}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Total Return:</strong><br>
                        <span class="badge bg-primary">$${totalReturn.toFixed(2)}</span>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        Investment: $${investment.toFixed(2)} | Duration: ${duration} days | 
                        Daily: ${dailyProfit}% | Total: ${totalProfit}%
                    </small>
                </div>
            `;
            $('#profit_preview').html(html);
        } else {
            $('#profit_preview').html('<p class="text-muted">Enter investment details to see profit preview</p>');
        }
    }
    
    // Update preview on input change
    $('#test_investment, #daily_profit_percentage, #total_profit_percentage, #duration').on('input', updateProfitPreview);
    
    // Validate amount ranges
    $('#min_amount, #max_amount').on('input', function() {
        const minAmount = parseFloat($('#min_amount').val()) || 0;
        const maxAmount = parseFloat($('#max_amount').val()) || 0;
        
        if (minAmount > 0 && maxAmount > 0 && minAmount >= maxAmount) {
            $('#max_amount').addClass('is-invalid');
            if (!$('#max_amount').next('.invalid-feedback').length) {
                $('#max_amount').after('<div class="invalid-feedback">Maximum amount must be greater than minimum amount</div>');
            }
        } else {
            $('#max_amount').removeClass('is-invalid');
            $('#max_amount').next('.invalid-feedback').remove();
        }
    });
    
    // Auto-calculate total profit based on daily profit and duration
    $('#daily_profit_percentage, #duration').on('input', function() {
        const dailyProfit = parseFloat($('#daily_profit_percentage').val()) || 0;
        const duration = parseInt($('#duration').val()) || 0;
        
        if (dailyProfit > 0 && duration > 0) {
            const totalProfit = dailyProfit * duration;
            $('#total_profit_percentage').val(totalProfit.toFixed(2));
            updateProfitPreview();
        }
    });
    
    // Initial preview update
    updateProfitPreview();
});

// Toggle plan status
function toggleStatus(status) {
    if (confirm(`Are you sure you want to ${status === 'active' ? 'activate' : 'deactivate'} this plan?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = status === 'active' 
            ? '{{ route("admin.investment-plans.activate", $plan->id) }}'
            : '{{ route("admin.investment-plans.deactivate", $plan->id) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection