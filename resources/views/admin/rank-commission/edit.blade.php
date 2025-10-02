@extends('layouts.admin.app')

@section('css')
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<style>
    .edit-card {
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        border: none;
        transition: all 0.3s ease;
    }
    .edit-card:hover {
        box-shadow: 0 8px 35px rgba(0,0,0,0.15);
    }
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .info-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 3px 15px rgba(102, 126, 234, 0.3);
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .input-group-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    .current-values-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .requirements-card {
        background: #fff;
        border-radius: 10px;
        padding: 1rem;
        border-left: 4px solid #667eea;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }
    .requirements-card:hover {
        transform: translateX(5px);
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }
    .form-switch .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    .animated-icon {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold mb-2">
                <i class="fas fa-edit me-3 animated-icon"></i>
                Edit Commission Level
            </h1>
            <p class="lead mb-0 opacity-90">Update commission rate and rank reward for {{ $rankRequirement->rank_name }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.rankcommission.index') }}" class="btn btn-light btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Main Edit Card -->
        <div class="card edit-card">
            <div class="card-body p-4">
                <!-- Info Badge -->
                <div class="info-badge">
                    <div class="row align-items-center text-center">
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="fas fa-crown fa-2x opacity-75"></i>
                            </div>
                            <h5 class="mb-1 fw-bold">{{ $rankRequirement->rank_name }}</h5>
                            <small class="opacity-75">Rank {{ $rankRequirement->rank }}</small>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="fas fa-layer-group fa-2x opacity-75"></i>
                            </div>
                            <h5 class="mb-1 fw-bold">Level {{ $commissionLevel->level }}</h5>
                            <small class="opacity-75">Commission Level</small>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="fas fa-hashtag fa-2x opacity-75"></i>
                            </div>
                            <h5 class="mb-1 fw-bold">ID: {{ $commissionLevel->id }}</h5>
                            <small class="opacity-75">Record ID</small>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="fas {{ $commissionLevel->is_active ? 'fa-check-circle' : 'fa-times-circle' }} fa-2x opacity-75"></i>
                            </div>
                            <h5 class="mb-1 fw-bold">{{ $commissionLevel->is_active ? 'Active' : 'Inactive' }}</h5>
                            <small class="opacity-75">Current Status</small>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Validation Errors
                        </h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Current Values Display -->
                <div class="current-values-card">
                    <h6 class="mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <strong>Current Values</strong>
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Commission Rate</label>
                            <h4 class="mb-0">
                                <span class="badge bg-primary" style="font-size: 1.1rem;">
                                    <i class="fas fa-percentage me-1"></i>
                                    {{ number_format($commissionLevel->commission_rate, 2) }}%
                                </span>
                            </h4>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Rank Reward Amount</label>
                            <h4 class="mb-0">
                                <span class="badge bg-success" style="font-size: 1.1rem;">
                                    <i class="fas fa-dollar-sign me-1"></i>
                                    ${{ number_format($rankRequirement->reward_amount, 2) }}
                                </span>
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <form action="{{ route('admin.rankcommission.update', $commissionLevel->id) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Commission Rate -->
                        <div class="col-md-6 mb-4">
                            <label for="commission_rate" class="form-label">
                                <i class="fas fa-percentage text-primary me-2"></i>
                                Commission Rate (%)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" 
                                       class="form-control @error('commission_rate') is-invalid @enderror" 
                                       id="commission_rate" 
                                       name="commission_rate" 
                                       value="{{ old('commission_rate', $commissionLevel->commission_rate) }}" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       placeholder="0.00"
                                       required>
                                <span class="input-group-text">
                                    <i class="fas fa-percent"></i>
                                </span>
                                @error('commission_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter rate between 0 and 100
                            </small>
                        </div>

                        <!-- Rank Reward -->
                        <div class="col-md-6 mb-4">
                            <label for="rank_reward" class="form-label">
                                <i class="fas fa-gift text-success me-2"></i>
                                Rank Reward Amount ($)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <input type="number" 
                                       class="form-control @error('rank_reward') is-invalid @enderror" 
                                       id="rank_reward" 
                                       name="rank_reward" 
                                       value="{{ old('rank_reward', $rankRequirement->reward_amount) }}" 
                                       step="0.01" 
                                       min="0" 
                                       placeholder="0.00"
                                       required>
                                @error('rank_reward')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Reward amount for achieving this rank
                            </small>
                        </div>

                        <!-- Status Switch -->
                        <div class="col-md-12 mb-4">
                            <div class="card border-0" style="background: #f8f9fa;">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               role="switch"
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $commissionLevel->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-2" for="is_active">
                                            <strong>
                                                <i class="fas fa-toggle-on text-success me-2"></i>
                                                Active Status
                                            </strong>
                                            <div>
                                                <small class="text-muted">
                                                    Toggle to enable or disable this commission level
                                                </small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.rankcommission.index') }}" class="btn btn-lg btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-lg btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Update Commission Level
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rank Requirements Information Card -->
        <div class="card edit-card mt-4">
            <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: none;">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    <strong>{{ $rankRequirement->rank_name }} - Complete Requirements</strong>
                </h5>
                <small class="text-muted">Full details about this rank's requirements</small>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Team Business Volume -->
                    <div class="col-md-6">
                        <div class="requirements-card">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas fa-chart-line text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Team Business Volume</label>
                                    <h5 class="mb-0 fw-bold text-primary">
                                        ${{ number_format($rankRequirement->team_business_volume, 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Count Level -->
                    <div class="col-md-6">
                        <div class="requirements-card">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas fa-layer-group text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Count Level</label>
                                    <h5 class="mb-0 fw-bold text-info">
                                        {{ $rankRequirement->count_level }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Investment -->
                    <div class="col-md-6">
                        <div class="requirements-card">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas fa-wallet text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Personal Investment</label>
                                    <h5 class="mb-0 fw-bold text-warning">
                                        ${{ number_format($rankRequirement->personal_investment, 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Referrals -->
                    <div class="col-md-6">
                        <div class="requirements-card">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Direct Referrals</label>
                                    <h5 class="mb-0 fw-bold text-success">
                                        {{ $rankRequirement->direct_referrals }} Members
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reward Amount -->
                    <div class="col-md-6">
                        <div class="requirements-card">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas fa-gift text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Reward Amount</label>
                                    <h5 class="mb-0 fw-bold text-danger">
                                        ${{ number_format($rankRequirement->reward_amount, 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <div class="requirements-card">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $rankRequirement->is_active ? 'bg-success' : 'bg-secondary' }}" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas {{ $rankRequirement->is_active ? 'fa-check-circle' : 'fa-times-circle' }} text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Rank Status</label>
                                    <h5 class="mb-0">
                                        <span class="badge {{ $rankRequirement->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size: 1rem;">
                                            {{ $rankRequirement->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card edit-card mt-4" style="border-left: 5px solid #667eea;">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    <strong>Need Help?</strong>
                </h6>
                <ul class="mb-0">
                    <li class="mb-2">
                        <strong>Commission Rate:</strong> Percentage of commission earned on this level (0-100%)
                    </li>
                    <li class="mb-2">
                        <strong>Rank Reward:</strong> One-time bonus amount received when achieving this rank
                    </li>
                    <li class="mb-2">
                        <strong>Active Status:</strong> Controls whether this commission level is currently active
                    </li>
                    <li>
                        <strong>Note:</strong> Changing the rank reward will affect all levels under this rank
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const form = document.getElementById('editForm');
    const commissionRate = document.getElementById('commission_rate');
    const rankReward = document.getElementById('rank_reward');
    const submitBtn = document.getElementById('submitBtn');
    const isActiveSwitch = document.getElementById('is_active');

    // Real-time validation for commission rate
    commissionRate.addEventListener('input', function() {
        const value = parseFloat(this.value);
        
        if (isNaN(value) || value < 0 || value > 100) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Real-time validation for rank reward
    rankReward.addEventListener('input', function() {
        const value = parseFloat(this.value);
        
        if (isNaN(value) || value < 0) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Status switch change animation
    isActiveSwitch.addEventListener('change', function() {
        if (this.checked) {
            this.parentElement.classList.add('border-success');
            this.parentElement.classList.remove('border-secondary');
        } else {
            this.parentElement.classList.add('border-secondary');
            this.parentElement.classList.remove('border-success');
        }
    });

    // Form submission validation and loading state
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        // Validate commission rate
        const rateValue = parseFloat(commissionRate.value);
        if (isNaN(rateValue) || rateValue < 0 || rateValue > 100) {
            isValid = false;
            errorMessage += '• Commission rate must be between 0 and 100\n';
            commissionRate.classList.add('is-invalid');
            commissionRate.focus();
        }

        // Validate rank reward
        const rewardValue = parseFloat(rankReward.value);
        if (isNaN(rewardValue) || rewardValue < 0) {
            isValid = false;
            errorMessage += '• Rank reward must be a positive number\n';
            rankReward.classList.add('is-invalid');
            if (isValid) rankReward.focus();
        }

        // Show error or submit
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errorMessage);
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        
        // Add loading overlay
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 9999; display: flex; align-items: center; justify-content: center;';
        overlay.innerHTML = '<div class="spinner-border text-light" style="width: 4rem; height: 4rem;" role="status"><span class="visually-hidden">Loading...</span></div>';
        document.body.appendChild(overlay);
    });

    // Number formatting on blur
    commissionRate.addEventListener('blur', function() {
        if (this.value && !isNaN(this.value)) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });

    rankReward.addEventListener('blur', function() {
        if (this.value && !isNaN(this.value)) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });

    // Prevent form resubmission on page reload
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Smooth scroll to errors
    const invalidInputs = document.querySelectorAll('.is-invalid');
    if (invalidInputs.length > 0) {
        invalidInputs[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add confirmation dialog for large changes
    const originalRate = parseFloat('{{ $commissionLevel->commission_rate }}');
    const originalReward = parseFloat('{{ $rankRequirement->reward_amount }}');

    form.addEventListener('submit', function(e) {
        const newRate = parseFloat(commissionRate.value);
        const newReward = parseFloat(rankReward.value);
        
        const rateChange = Math.abs(newRate - originalRate);
        const rewardChange = Math.abs(newReward - originalReward);
        
        if (rateChange > 50 || rewardChange > 1000) {
            if (submitBtn.disabled) return; // Already confirmed
            
            e.preventDefault();
            if (confirm('You are making a significant change. Are you sure you want to continue?')) {
                form.submit();
            }
        }
    }, true);
});
</script>
@endsection