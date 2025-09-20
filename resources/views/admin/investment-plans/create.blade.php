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
                    <h3 class="card-title">Create New Investment Plan</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.investment-plans.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Plans
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.investment-plans.store') }}" method="POST">
                    @csrf
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
                                           value="{{ old('name') }}" 
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
                                           value="{{ old('duration') }}" 
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
                                           value="{{ old('min_amount') }}" 
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
                                           value="{{ old('max_amount') }}" 
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
                                           value="{{ old('daily_profit_percentage') }}" 
                                           step="0.01" min="0" max="100" required>
                                    @error('daily_profit_percentage')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status">Active Plan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                              placeholder="Enter plan description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <!-- Profit Calculator -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Daily Profit Calculator Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <label>Test Investment Amount ($)</label>
                                        <input type="number" class="form-control" id="test_amount" 
                                               placeholder="1000" step="0.01" min="1">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Daily Profit Earned</label>
                                        <div class="form-control-plaintext text-success font-weight-bold" id="daily_profit_preview">$0.00</div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <small class="text-muted">Users can invest any amount and earn the daily profit percentage on their investment</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Investment Plan
                        </button>
                        <a href="{{ route('admin.investment-plans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate daily profit preview
    function updateProfitPreview() {
        const testAmount = parseFloat($('#test_amount').val()) || 0;
        const dailyProfit = parseFloat($('#daily_profit_percentage').val()) || 0;
        
        const dailyProfitAmount = (testAmount * dailyProfit) / 100;
        
        $('#daily_profit_preview').text('$' + dailyProfitAmount.toFixed(2));
    }
    
    // Update preview on input change
    $('#test_amount, #daily_profit_percentage').on('input', updateProfitPreview);
    
    // Set default test amount
    $('#test_amount').val(1000);
    updateProfitPreview();
});
</script>
@endpush