@extends('user.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="rank-requirements-header">
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ route('user.rank.upgrade-center') }}" class="btn btn-outline-light me-3">
                        <i class="bi bi-arrow-left"></i> Back to Upgrade Center
                    </a>
                    <div>
                        <h4 class="text-white mb-0">Rank Upgrade History</h4>
                        <p class="text-light mb-0">Complete history of your rank upgrades and rewards</p>
                    </div>
                </div>
            </div>

            <!-- History Card -->
            <div class="rank-requirements-card">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title">Rank Upgrade History</h5>
                        <p class="rank-subtitle">All your rank upgrades and associated rewards</p>
                    </div>
                    <div class="rank-bonus">
                        <div class="bonus-amount">{{ $rankRewards->total() }}</div>
                        <div class="bonus-label">TOTAL UPGRADES</div>
                    </div>
                </div>

                @if($rankRewards->count() > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Rank Upgrade</th>
                                <th>Reward Amount</th>
                                <th>Reward Type</th>
                                <th>Status</th>
                                <th>Processed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rankRewards as $reward)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $reward->created_at->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ $reward->created_at->format('H:i:s') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rank-upgrade-badge">
                                            <span class="old-rank">{{ $reward->old_rank }}</span>
                                            <i class="bi bi-arrow-right mx-2"></i>
                                            <span class="new-rank">{{ $reward->new_rank }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">${{ number_format($reward->reward_amount, 2) }}</span>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'automatic' => 'bg-primary',
                                            'manual_claim' => 'bg-success',
                                            'admin_adjustment' => 'bg-warning',
                                            'bonus' => 'bg-info'
                                        ];
                                        $typeColor = $typeColors[$reward->reward_type] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $typeColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $reward->reward_type)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($reward->status === 'processed')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Processed
                                        </span>
                                    @elseif($reward->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock-fill me-1"></i>Pending
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle-fill me-1"></i>Failed
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($reward->processed_at)
                                        <div class="d-flex flex-column">
                                            <span>{{ $reward->processed_at->format('M d, Y') }}</span>
                                            <small class="text-muted">{{ $reward->processed_at->format('H:i:s') }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Not processed</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $rankRewards->links() }}
                </div>

                <!-- Summary Statistics -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ $rankRewards->where('status', 'processed')->count() }}</div>
                                <div class="stat-label">Successful Upgrades</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">${{ number_format($rankRewards->where('status', 'processed')->sum('reward_amount'), 2) }}</div>
                                <div class="stat-label">Total Rewards Earned</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-hand-thumbs-up-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ $rankRewards->where('reward_type', 'manual_claim')->count() }}</div>
                                <div class="stat-label">Manual Claims</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-gear-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ $rankRewards->where('reward_type', 'automatic')->count() }}</div>
                                <div class="stat-label">Automatic Upgrades</div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- No History -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-trophy" style="font-size: 4rem; color: #6c757d;"></i>
                    </div>
                    <h5 class="text-white mb-3">No Rank Upgrades Yet</h5>
                    <p class="text-muted mb-4">
                        You haven't completed any rank upgrades yet. Start investing and building your team to unlock higher ranks and earn rewards!
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('user.investment.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Start Investing
                        </a>
                        <a href="{{ route('user.invite') }}" class="btn btn-outline-light">
                            <i class="bi bi-people me-2"></i>Invite Friends
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.rank-upgrade-badge {
    display: flex;
    align-items: center;
    font-weight: bold;
}

.old-rank {
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.875rem;
}

.new-rank {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.875rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-icon {
    font-size: 2rem;
    color: #007bff;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #fff;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #adb5bd;
}

.table-dark {
    --bs-table-bg: rgba(255, 255, 255, 0.05);
}

.table-dark td, .table-dark th {
    border-color: rgba(255, 255, 255, 0.1);
    vertical-align: middle;
}

.pagination .page-link {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: #fff;
}

.pagination .page-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
    color: #fff;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endsection