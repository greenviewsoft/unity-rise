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
            <!-- Statistics Cards -->
            </br>
            <div class="row mb-4">
          
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                    
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Total Investments</div>
                                    <div class="h5">{{ number_format($stats['total_investments']) }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Active Investments</div>
                                    <div class="h5">{{ number_format($stats['active_investments']) }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-coins fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Total Amount</div>
                                    <div class="h5">${{ number_format($stats['total_amount'], 2) }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Total Withdraws</div>
                                    <div class="h5">${{ number_format($stats['total_withdraws'], 2) }}</div>
                                    <div class="small text-white-50">{{ $stats['total_withdraws_count'] }} withdrawals</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All User Investment History</h3>
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

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.active-investments.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control" placeholder="Search by username, email..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('admin.active-investments.index') }}" class="btn btn-secondary">Clear</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Daily Profit</th>
                                    <th>Total Profit</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investments as $investment)
                                    <tr>
                                        <td>{{ $investment->id }}</td>
                                        <td>
                                            <strong>{{ $investment->user->username ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $investment->user->email ?? 'N/A' }}</small><br>
                                            
                                        </td>
                                        <td>
                                            @if($investment->plan)
                                                <strong>{{ $investment->plan->name }}</strong><br>
                                                <small class="text-muted">{{ $investment->plan->daily_profit_percentage }}% daily</small>
                                            @else
                                                <span class="text-muted">{{ $investment->plan_type ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">${{ number_format($investment->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">${{ number_format($investment->daily_profit, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">${{ number_format($investment->total_profit, 2) }}</span>
                                        </td>
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
                                        <td>
                                            <small>{{ $investment->start_date ? $investment->start_date->format('M d, Y') : 'N/A' }}</small><br>
                                            <small class="text-muted">{{ $investment->start_date ? $investment->start_date->format('H:i A') : '' }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.active-investments.show', $investment->id) }}" 
                                                   class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($investment->status == 'active')
                                                    <a href="{{ route('admin.active-investments.complete', $investment->id) }}" 
                                                       class="btn btn-success btn-sm" title="Complete"
                                                       onclick="return confirm('Are you sure you want to complete this investment?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="{{ route('admin.active-investments.cancel', $investment->id) }}" 
                                                       class="btn btn-danger btn-sm" title="Cancel"
                                                       onclick="return confirm('Are you sure you want to cancel this investment?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No investments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $investments->appends(request()->query())->links() }}
                    </div>
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