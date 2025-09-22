@extends('layouts.admin.app')

@section('css')
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
                    <h3 class="card-title">Investment Details #{{ $investment->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.active-investments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Investment Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Investment Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Investment ID:</strong></td>
                                            <td>{{ $investment->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Amount:</strong></td>
                                            <td><span class="badge bg-info">${{ number_format($investment->amount, 2) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Daily Profit:</strong></td>
                                            <td><span class="badge bg-success">${{ number_format($investment->daily_profit, 2) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Profit Earned:</strong></td>
                                            <td><span class="badge bg-primary">${{ number_format($investment->total_profit, 2) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($investment->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($investment->status == 'completed')
                                                    <span class="badge bg-primary">Completed</span>
                                                @elseif($investment->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($investment->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Start Date:</strong></td>
                                            <td>{{ $investment->start_date ? $investment->start_date->format('M d, Y H:i A') : 'N/A' }}</td>
                                        </tr>
                                        @if($investment->end_date)
                                        <tr>
                                            <td><strong>End Date:</strong></td>
                                            <td>{{ $investment->end_date->format('M d, Y H:i A') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Profit Days Completed:</strong></td>
                                            <td>{{ $investment->profit_days_completed ?? 0 }} days</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>User Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($investment->user)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>User ID:</strong></td>
                                            <td>{{ $investment->user->id }}</td>
                                        </tr>
                                       
                                        <tr>
                                            <td><strong>Username:</strong></td>
                                            <td>{{ $investment->user->username ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $investment->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Current Balance:</strong></td>
                                            <td><span class="badge bg-warning">${{ number_format($investment->user->balance, 2) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rank:</strong></td>
                                            <td>{{ $investment->user->getRankName() }}</td>
                                        </tr>
                                    </table>
                                    @else
                                    <p class="text-muted">User information not available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Plan Information -->
                    @if($investment->plan)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Investment Plan Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Plan Name:</strong></td>
                                                    <td>{{ $investment->plan->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Daily Profit %:</strong></td>
                                                    <td>{{ $investment->plan->daily_profit_percentage }}%</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Profit %:</strong></td>
                                                    <td>{{ $investment->plan->total_profit_percentage }}%</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Duration:</strong></td>
                                                    <td>{{ $investment->plan->duration_days }} days</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Min Amount:</strong></td>
                                                    <td>${{ number_format($investment->plan->min_amount, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Max Amount:</strong></td>
                                                    <td>${{ number_format($investment->plan->max_amount, 2) }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    @if($investment->plan->description)
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Description:</strong>
                                            <p class="mt-2">{{ $investment->plan->description }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Profit History -->
                    @if($investment->profits->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Profit History ({{ $investment->profits->count() }} records)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Day</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($investment->profits->take(10) as $profit)
                                                <tr>
                                                    <td>{{ $profit->profit_date ? $profit->profit_date->format('M d, Y') : 'N/A' }}</td>
                                                    <td>${{ number_format($profit->amount, 2) }}</td>
                                                    <td>Day {{ $profit->day ?? 'N/A' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($investment->profits->count() > 10)
                                        <p class="text-muted">Showing latest 10 profit records...</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    @if($investment->status == 'active')
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Actions</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('admin.active-investments.complete', $investment->id) }}" 
                                       class="btn btn-success me-2"
                                       onclick="return confirm('Are you sure you want to complete this investment? This will return the capital to the user.')">
                                        <i class="fas fa-check"></i> Complete Investment
                                    </a>
                                    <a href="{{ route('admin.active-investments.cancel', $investment->id) }}" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to cancel this investment? This will return the capital to the user.')">
                                        <i class="fas fa-times"></i> Cancel Investment
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
@endsection