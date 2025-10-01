@extends('user.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="rank-requirements-header">
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ URL('user/dashboard') }}" class="btn btn-outline-light me-3">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <div>
                        <h4 class="text-white mb-0">Rank Upgrade Center</h4>
                        <p class="text-light mb-0">
                            Current Rank: <strong>{{ $rankStats['current_rank_name'] ?? 'Rookie' }}</strong>
                            @if($canClaim)
                                → Eligible for: <strong>{{ $rankStats['next_rank_name'] ?? 'N/A' }}</strong>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Status Card -->
            <div class="rank-requirements-card mb-4">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title">Current Status</h5>
                        <p class="rank-subtitle">Your current rank and eligibility status</p>
                    </div>
                    <div class="rank-bonus">
                        <div class="bonus-amount">Rank {{ $user->rank ?? 1 }}</div>
                        <div class="bonus-label">CURRENT RANK</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">${{ number_format($rankStats['personal_investment'] ?? 0, 2) }}</div>
                                <div class="stat-label">Personal Investment</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">${{ number_format($rankStats['team_business_volume'] ?? 0, 2) }}</div>
                                <div class="stat-label">Team Business Volume</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-layers"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ $rankStats['active_levels'] ?? 0 }}</div>
                                <div class="stat-label">Active Levels</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($canClaim)
            <!-- Claim Upgrade Card -->
            <div class="rank-requirements-card mb-4">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title text-success">🎉 Congratulations!</h5>
                        <p class="rank-subtitle">You are eligible for rank upgrade. Click below to claim your rewards!</p>
                    </div>
                    <div class="rank-bonus">
                        @php
                            $totalReward = 0;
                            for($i = $user->rank + 1; $i <= $eligibleRank; $i++) {
                                $requirement = $allRanks->where('rank', $i)->first();
                                if($requirement) {
                                    $totalReward += $requirement->reward_amount;
                                }
                            }
                        @endphp
                        <div class="bonus-amount">${{ number_format($totalReward, 2) }}</div>
                        <div class="bonus-label">TOTAL REWARD</div>
                    </div>
                </div>

                <div class="text-center">
                    <button id="claimUpgradeBtn" class="btn btn-success btn-lg px-5 py-3">
                        <i class="bi bi-trophy-fill me-2"></i>
                        Claim Rank Upgrade (Rank {{ $user->rank }} → {{ $eligibleRank }})
                    </button>
                </div>

                <!-- Upgrade Details -->
                <div class="mt-4">
                    <h6 class="text-white mb-3">Upgrade Details:</h6>
                    <div class="upgrade-details">
                        @for($i = $user->rank + 1; $i <= $eligibleRank; $i++)
                            @php
                                $requirement = $allRanks->where('rank', $i)->first();
                            @endphp
                            @if($requirement)
                            <div class="upgrade-item">
                                <div class="upgrade-rank">Rank {{ $i }}</div>
                                <div class="upgrade-name">{{ $requirement->rank_name }}</div>
                                <div class="upgrade-reward">${{ number_format($requirement->reward_amount, 2) }}</div>
                            </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
            @else
            <!-- Next Rank Requirements -->
            @if($rankStats['next_rank_name'])
            <div class="rank-requirements-card mb-4">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title">Next Rank: {{ $rankStats['next_rank_name'] }}</h5>
                        <p class="rank-subtitle">Complete the requirements below to unlock your next rank</p>
                    </div>
                    <div class="rank-bonus">
                        <div class="bonus-amount">${{ number_format($rankStats['next_rank_reward'] ?? 0, 2) }}</div>
                        <div class="bonus-label">BONUS REWARD</div>
                    </div>
                </div>

                <!-- Requirements Progress -->
                <div class="requirements-list">
                    <!-- Personal Investment -->
                    <div class="requirement-item">
                        <div class="requirement-header">
                            <div class="requirement-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Personal Investment</h6>
                                <p class="requirement-desc">Required: ${{ number_format($rankStats['next_rank_personal_req'] ?? 0, 2) }}</p>
                            </div>
                            <div class="requirement-status">
                                @if(($rankStats['personal_investment'] ?? 0) >= ($rankStats['next_rank_personal_req'] ?? 0))
                                <span class="status-badge completed">
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </span>
                                @else
                                <span class="status-badge pending">
                                    <i class="bi bi-clock-fill"></i> In Progress
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="requirement-progress">
                            <div class="progress-info">
                                <span class="current-value">${{ number_format($rankStats['personal_investment'] ?? 0, 2) }}</span>
                                <span class="target-value">/ ${{ number_format($rankStats['next_rank_personal_req'] ?? 0, 2) }}</span>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    @php
                                        $personalProgress = ($rankStats['next_rank_personal_req'] ?? 0) > 0 
                                            ? min(100, (($rankStats['personal_investment'] ?? 0) / ($rankStats['next_rank_personal_req'] ?? 1)) * 100) 
                                            : 0;
                                    @endphp
                                    <div class="progress-fill" style="width: {{ $personalProgress }}%"></div>
                                </div>
                                <span class="progress-percent">{{ number_format($personalProgress, 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Team Business Volume -->
                    <div class="requirement-item">
                        <div class="requirement-header">
                            <div class="requirement-icon">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Team Business Volume</h6>
                                <p class="requirement-desc">Required: ${{ number_format($rankStats['next_rank_team_req'] ?? 0, 2) }}</p>
                            </div>
                            <div class="requirement-status">
                                @if(($rankStats['team_business_volume'] ?? 0) >= ($rankStats['next_rank_team_req'] ?? 0))
                                <span class="status-badge completed">
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </span>
                                @else
                                <span class="status-badge pending">
                                    <i class="bi bi-clock-fill"></i> In Progress
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="requirement-progress">
                            <div class="progress-info">
                                <span class="current-value">${{ number_format($rankStats['team_business_volume'] ?? 0, 2) }}</span>
                                <span class="target-value">/ ${{ number_format($rankStats['next_rank_team_req'] ?? 0, 2) }}</span>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    @php
                                        $teamProgress = ($rankStats['next_rank_team_req'] ?? 0) > 0 
                                            ? min(100, (($rankStats['team_business_volume'] ?? 0) / ($rankStats['next_rank_team_req'] ?? 1)) * 100) 
                                            : 0;
                                    @endphp
                                    <div class="progress-fill" style="width: {{ $teamProgress }}%"></div>
                                </div>
                                <span class="progress-percent">{{ number_format($teamProgress, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif

            <!-- Recent Rank Rewards -->
            @if($recentRewards->count() > 0)
            <div class="rank-requirements-card">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title">Recent Rank Rewards</h5>
                        <p class="rank-subtitle">Your recent rank upgrade history</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Upgrade</th>
                                <th>Reward</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRewards as $reward)
                            <tr>
                                <td>{{ $reward->created_at->format('M d, Y H:i') }}</td>
                                <td>Rank {{ $reward->old_rank }} → {{ $reward->new_rank }}</td>
                                <td>${{ number_format($reward->reward_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $reward->reward_type)) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($reward->status) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('user.rank.history') }}" class="btn btn-outline-light">
                        View Full History
                    </a>
                </div>
            </div>
            @endif

            <!-- All Ranks Overview -->
            <div class="rank-requirements-card">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title">All Ranks Overview</h5>
                        <p class="rank-subtitle">Complete rank structure and requirements</p>
                    </div>
                </div>

                <div class="ranks-overview">
                    @foreach($allRanks as $rank)
                    <div class="rank-overview-item {{ $user->rank >= $rank->rank ? 'completed' : '' }} {{ $user->rank == $rank->rank ? 'current' : '' }}">
                        <div class="rank-number">{{ $rank->rank }}</div>
                        <div class="rank-details">
                            <div class="rank-name">{{ $rank->rank_name }}</div>
                            <div class="rank-requirements">
                                <small>Personal: ${{ number_format($rank->personal_investment, 2) }}</small>
                                <small>Team: ${{ number_format($rank->team_business_volume, 2) }}</small>
                                <small>Levels: {{ $rank->count_level }}</small>
                            </div>
                        </div>
                        <div class="rank-reward">${{ number_format($rank->reward_amount, 2) }}</div>
                        @if($user->rank >= $rank->rank)
                        <div class="rank-status">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="text-white">Processing Rank Upgrade...</h5>
                <p class="text-muted">Please wait while we process your rank upgrade and rewards.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Claim upgrade button click
    $('#claimUpgradeBtn').click(function() {
        const button = $(this);
        const originalText = button.html();
        
        // Show loading modal
        $('#loadingModal').modal('show');
        
        // Disable button
        button.prop('disabled', true);
        button.html('<i class="bi bi-hourglass-split me-2"></i>Processing...');
        
        $.ajax({
            url: '{{ route("user.rank.claim-upgrade") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loadingModal').modal('hide');
                
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Congratulations!',
                        html: `
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <p class="mb-2">${response.message}</p>
                                <div class="alert alert-success">
                                    <strong>Rank Upgrade:</strong> ${response.old_rank} → ${response.new_rank}<br>
                                    <strong>Total Reward:</strong> $${response.total_reward.toFixed(2)}<br>
                                    <strong>New Balance:</strong> $${response.new_balance.toFixed(2)}
                                </div>
                                ${response.upgraded_ranks.map(rank => 
                                    `<div class="badge bg-primary me-2 mb-2">
                                        ${rank.name} - $${rank.reward.toFixed(2)}
                                    </div>`
                                ).join('')}
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Awesome!',
                        background: '#1a1a1a',
                        color: '#fff'
                    }).then(() => {
                        // Reload page to show updated status
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        background: '#1a1a1a',
                        color: '#fff'
                    });
                    
                    // Re-enable button
                    button.prop('disabled', false);
                    button.html(originalText);
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                
                let errorMessage = 'An error occurred while processing your request.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    background: '#1a1a1a',
                    color: '#fff'
                });
                
                // Re-enable button
                button.prop('disabled', false);
                button.html(originalText);
            }
        });
    });
    
    // Auto-refresh eligibility every 30 seconds
    setInterval(function() {
        $.get('{{ route("user.rank.check-eligibility") }}', function(response) {
            if (response.success && response.can_claim) {
                // If user becomes eligible, reload the page
                if (!$('#claimUpgradeBtn').length) {
                    window.location.reload();
                }
            }
        });
    }, 30000);
});
</script>
@endsection