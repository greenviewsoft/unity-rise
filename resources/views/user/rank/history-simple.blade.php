@extends('user.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Rank Upgrade History</h4>
                    <div class="card-tools">
                        <a href="{{ route('rank.requirements') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Requirements
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upgrades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>From Rank</th>
                                        <th>To Rank</th>
                                        <th>Reward</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upgrades as $upgrade)
                                    <tr>
                                        <td>{{ $upgrade->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $upgrade->fromRank->name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">
                                                {{ $upgrade->toRank->name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($upgrade->reward_amount > 0)
                                                <span class="text-success">
                                                    ${{ number_format($upgrade->reward_amount, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">No reward</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-success">Completed</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $upgrades->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Rank Upgrades Yet</h5>
                            <p class="text-muted">You haven't upgraded your rank yet. Start by checking the requirements!</p>
                            <a href="{{ route('rank.requirements') }}" class="btn btn-primary">
                                View Requirements
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection