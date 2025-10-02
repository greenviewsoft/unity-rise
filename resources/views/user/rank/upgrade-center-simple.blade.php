@extends('user.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Rank Upgrade Center</h4>
                    <div class="card-tools">
                        <a href="{{ route('rank.history') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-history"></i> View History
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Current Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Current Rank</h5>
                                    <h3>{{ $currentRank->name }}</h3>
                                    <p class="mb-0">Rank {{ $currentRank->rank }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Current Balance</h5>
                                    <h3>${{ number_format($user->balance, 2) }}</h3>
                                    <p class="mb-0">Available for upgrades</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Rank Requirements -->
                    @if($nextRank)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Next Rank: {{ $nextRank->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Personal Investment</h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" style="width: {{ min(100, ($userStats['personal_investment'] / $nextRank->personal_investment) * 100) }}%"></div>
                                        </div>
                                        <small>${{ number_format($userStats['personal_investment'], 2) }} / ${{ number_format($nextRank->personal_investment, 2) }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Direct Referrals</h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" style="width: {{ min(100, ($userStats['direct_referrals'] / $nextRank->direct_referrals) * 100) }}%"></div>
                                        </div>
                                        <small>{{ $userStats['direct_referrals'] }} / {{ $nextRank->direct_referrals }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h6>Team Investment</h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" style="width: {{ min(100, ($userStats['team_investment'] / $nextRank->team_investment) * 100) }}%"></div>
                                        </div>
                                        <small>${{ number_format($userStats['team_investment'], 2) }} / ${{ number_format($nextRank->team_investment, 2) }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                @if($canUpgrade)
                                    <button type="button" class="btn btn-success btn-lg" onclick="upgradeRank()">
                                        <i class="fas fa-arrow-up"></i> Upgrade to {{ $nextRank->name }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                                        <i class="fas fa-lock"></i> Requirements Not Met
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <h5><i class="fas fa-crown"></i> Congratulations!</h5>
                        <p class="mb-0">You have reached the highest rank available!</p>
                    </div>
                    @endif

                    <!-- All Ranks Overview -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">All Ranks Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Name</th>
                                            <th>Personal Investment</th>
                                            <th>Direct Referrals</th>
                                            <th>Team Investment</th>
                                            <th>Reward</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allRanks as $rank)
                                        <tr class="{{ $rank->rank == $currentRank->rank ? 'table-primary' : '' }}">
                                            <td>{{ $rank->rank }}</td>
                                            <td>
                                                {{ $rank->name }}
                                                @if($rank->rank == $currentRank->rank)
                                                    <span class="badge badge-primary ml-1">Current</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($rank->personal_investment, 2) }}</td>
                                            <td>{{ $rank->direct_referrals }}</td>
                                            <td>${{ number_format($rank->team_investment, 2) }}</td>
                                            <td>
                                                @if($rank->reward_amount > 0)
                                                    ${{ number_format($rank->reward_amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($rank->rank < $currentRank->rank)
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($rank->rank == $currentRank->rank)
                                                    <span class="badge badge-primary">Current</span>
                                                @else
                                                    <span class="badge badge-secondary">Locked</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function upgradeRank() {
    if (!confirm('Are you sure you want to upgrade your rank?')) {
        return;
    }
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;
    
    fetch('{{ route("rank.upgrade") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rank upgraded successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>
@endsection