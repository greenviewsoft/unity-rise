@extends('layouts.admin.app')

@section('css')
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<style>
    .gradient-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .form-modern {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .form-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn-modern {
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header text-center">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold mb-2">
                <i class="fas fa-edit me-3"></i>
                Edit Profit Withdrawal
            </h1>
            <p class="lead mb-0">Update profit withdrawal entry for {{ $profitWithdrawal->username }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.profit-withdrawal.index') }}" class="btn btn-light btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card gradient-card">
            <div class="card-body p-5">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading">Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.profit-withdrawal.update', $profitWithdrawal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="username" class="form-label text-white fw-bold">
                                <i class="fas fa-user me-2"></i>Username
                            </label>
                            <input type="text" 
                                   class="form-control form-modern @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $profitWithdrawal->username) }}" 
                                   placeholder="Enter username"
                                   required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="amount" class="form-label text-white fw-bold">
                                <i class="fas fa-dollar-sign me-2"></i>Amount
                            </label>
                            <input type="number" 
                                   class="form-control form-modern @error('amount') is-invalid @enderror" 
                                   id="amount" 
                                   name="amount" 
                                   value="{{ old('amount', $profitWithdrawal->amount) }}" 
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-dark">
                                        <i class="fas fa-info-circle me-2"></i>Entry Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Created:</small>
                                            <p class="mb-0 text-dark">{{ $profitWithdrawal->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Last Updated:</small>
                                            <p class="mb-0 text-dark">{{ $profitWithdrawal->updated_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-warning btn-modern me-3">
                                <i class="fas fa-save me-2"></i>Update Entry
                            </button>
                            <a href="{{ route('admin.profit-withdrawal.index') }}" class="btn btn-secondary btn-modern">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection