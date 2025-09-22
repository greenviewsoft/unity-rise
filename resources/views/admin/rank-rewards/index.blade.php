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
                    <h3 class="card-title">Rank Rewards Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.rank-rewards.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Rank Reward
                        </a>
                        <a href="{{ route('admin.rank-rewards.settings') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Auto-Approval System Notice -->
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>Automatic System:</strong> All rank rewards are now automatically approved and processed. Manual approval is no longer required.
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Rank Statistics -->
                    @if($rankStats->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Rank Distribution (12 Ranks)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @for($i = 1; $i <= 12; $i++)
                                                @php
                                                    $rankStat = $rankStats->where('rank', $i)->first();
                                                    $userCount = $rankStat ? $rankStat->user_count : 0;
                                                @endphp
                                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                                    <div class="info-box bg-gradient-primary">
                                                        <span class="info-box-icon"><i class="fas fa-crown"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Rank {{ $i }}</span>
                                                            <span class="info-box-number">{{ $userCount }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Rank Change</th>
                                    <th>Reward Amount</th>
                                    <th>Reward Type</th>
                                    <th>Status</th>
                                    <th>Processed Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rankRewards as $reward)
                                    <tr>
                                        <td>{{ $reward->id }}</td>
                                        <td>
                                            @if($reward->user)
                                                <div>
                                                    <strong>{{ $reward->user->name }}</strong>
                                                    <br><small class="text-muted">{{ $reward->user->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">User not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-secondary mr-1">{{ $reward->old_rank }}</span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span class="badge badge-primary">{{ $reward->new_rank }}</span>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                Rank {{ $reward->old_rank }} → Rank {{ $reward->new_rank }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">
                                                ${{ number_format($reward->reward_amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($reward->reward_type)
                                                @case('rank_upgrade')
                                                    <span class="badge badge-primary">Rank Upgrade</span>
                                                    @break
                                                @case('achievement')
                                                    <span class="badge badge-warning">Achievement</span>
                                                    @break
                                                @case('bonus')
                                                    <span class="badge badge-info">Bonus</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ ucfirst($reward->reward_type) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($reward->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ ucfirst($reward->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($reward->processed_at)
                                                {{ $reward->processed_at->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">Not processed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.rank-rewards.show', $reward->id) }}" 
                                                   class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <!-- Manual approval/rejection removed - System is now fully automatic -->
                                                <form action="{{ route('admin.rank-rewards.destroy', $reward->id) }}" 
                                                      method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this rank reward? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-crown fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Rank Rewards Found</h5>
                                                <p class="text-muted">Create your first rank reward to get started.</p>
                                                <a href="{{ route('admin.rank-rewards.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create Rank Reward
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($rankRewards->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $rankRewards->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve/Reject Forms -->
<form id="approve-form" method="POST" style="display: none;">
    @csrf
    @method('POST')
</form>

<form id="reject-form" method="POST" style="display: none;">
    @csrf
    @method('POST')
</form>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    function approveReward(id) {
        if (confirm('Are you sure you want to approve this rank reward?')) {
            const form = document.getElementById('approve-form');
            form.action = `/admin/rank-rewards/approve/${id}`;
            form.submit();
        }
    }

    function rejectReward(id) {
        if (confirm('Are you sure you want to reject this rank reward?')) {
            const form = document.getElementById('reject-form');
            form.action = `/admin/rank-rewards/reject/${id}`;
            form.submit();
        }
    }
</script>
@endpush