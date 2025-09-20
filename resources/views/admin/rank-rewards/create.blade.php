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
                    <h3 class="card-title">Create New Rank Reward</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.rank-rewards.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.rank-rewards.store') }}" method="POST">
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
                                    <label for="user_id">Select User <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" 
                                            id="user_id" name="user_id" required>
                                        <option value="">Select a user...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->username }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="old_rank">Previous Rank</label>
                                    <select class="form-control @error('old_rank') is-invalid @enderror" 
                                            id="old_rank" name="old_rank">
                                        <option value="">No previous rank</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('old_rank') == $i ? 'selected' : '' }}>
                                                Rank {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('old_rank')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="new_rank">New Rank <span class="text-danger">*</span></label>
                                    <select class="form-control @error('new_rank') is-invalid @enderror" 
                                            id="new_rank" name="new_rank" required>
                                        <option value="">Select new rank...</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('new_rank') == $i ? 'selected' : '' }}>
                                                Rank {{ $i }} ({{ $i * 3 + 3 }} Levels)
                                            </option>
                                        @endfor
                                    </select>
                                    @error('new_rank')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="reward_amount">Reward Amount ($) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('reward_amount') is-invalid @enderror" 
                                           id="reward_amount" name="reward_amount" 
                                           value="{{ old('reward_amount') }}" 
                                           step="0.01" min="0" required>
                                    @error('reward_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="reward_date">Reward Date</label>
                                    <input type="date" class="form-control @error('reward_date') is-invalid @enderror" 
                                           id="reward_date" name="reward_date" 
                                           value="{{ old('reward_date', date('Y-m-d')) }}">
                                    @error('reward_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Optional notes about this rank reward...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rank Information Preview -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Rank Information Preview</h5>
                            </div>
                            <div class="card-body">
                                <div id="rank_preview" class="text-muted">
                                    Select a rank to see commission levels and requirements
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">User Information</h5>
                            </div>
                            <div class="card-body">
                                <div id="user_info" class="text-muted">
                                    Select a user to see their current information
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Rank Reward
                        </button>
                        <a href="{{ route('admin.rank-rewards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
<script>
$(document).ready(function() {
    // Rank information data
    const rankData = {
        1: { levels: 6, commission: '5-10%', requirement: 'Basic investment' },
        2: { levels: 9, commission: '8-15%', requirement: '$1,000+ investment' },
        3: { levels: 12, commission: '10-18%', requirement: '$2,500+ investment' },
        4: { levels: 15, commission: '12-20%', requirement: '$5,000+ investment' },
        5: { levels: 18, commission: '15-22%', requirement: '$10,000+ investment' },
        6: { levels: 22, commission: '18-25%', requirement: '$20,000+ investment' },
        7: { levels: 26, commission: '20-28%', requirement: '$35,000+ investment' },
        8: { levels: 30, commission: '22-30%', requirement: '$50,000+ investment' },
        9: { levels: 33, commission: '25-32%', requirement: '$75,000+ investment' },
        10: { levels: 36, commission: '28-35%', requirement: '$100,000+ investment' },
        11: { levels: 40, commission: '30-38%', requirement: '$150,000+ investment' },
        12: { levels: 40, commission: '35-40%', requirement: '$200,000+ investment' }
    };
    
    // Update rank preview
    $('#new_rank').on('change', function() {
        const rank = $(this).val();
        
        if (rank && rankData[rank]) {
            const data = rankData[rank];
            const html = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Commission Levels:</strong><br>
                        <span class="badge bg-primary">${data.levels} Levels</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Commission Range:</strong><br>
                        <span class="badge bg-success">${data.commission}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Requirement:</strong><br>
                        <span class="badge bg-info">${data.requirement}</span>
                    </div>
                </div>
            `;
            $('#rank_preview').html(html);
        } else {
            $('#rank_preview').html('<p class="text-muted">Select a rank to see commission levels and requirements</p>');
        }
    });
    
    // Load user information
    $('#user_id').on('change', function() {
        const userId = $(this).val();
        
        if (userId) {
            $.get(`/admin/user/${userId}`, function(data) {
                const html = `
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Username:</strong><br>
                            ${data.username}
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong><br>
                            ${data.email}
                        </div>
                        <div class="col-md-3">
                            <strong>Current Balance:</strong><br>
                            <span class="badge bg-success">$${parseFloat(data.balance || 0).toFixed(2)}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Current Rank:</strong><br>
                            <span class="badge bg-primary">Rank ${data.rank || 'None'}</span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Total Investments:</strong><br>
                            <span class="badge bg-info">$${parseFloat(data.total_investments || 0).toFixed(2)}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Total Earnings:</strong><br>
                            <span class="badge bg-warning">$${parseFloat(data.total_earnings || 0).toFixed(2)}</span>
                        </div>
                    </div>
                `;
                $('#user_info').html(html);
                
                // Set old rank if user has one
                if (data.rank) {
                    $('#old_rank').val(data.rank);
                }
            }).fail(function() {
                $('#user_info').html('<p class="text-danger">Failed to load user information</p>');
            });
        } else {
            $('#user_info').html('<p class="text-muted">Select a user to see their current information</p>');
        }
    });
    
    // Auto-calculate reward amount based on rank
    $('#new_rank').on('change', function() {
        const rank = parseInt($(this).val());
        if (rank) {
            // Base reward calculation: Rank * $100 + bonus
            const baseReward = rank * 100;
            const bonus = rank > 6 ? (rank - 6) * 50 : 0;
            const suggestedReward = baseReward + bonus;
            
            $('#reward_amount').val(suggestedReward.toFixed(2));
        }
    });
});
</script>
@endsection