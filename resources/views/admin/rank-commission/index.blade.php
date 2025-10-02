@extends('layouts.admin.app')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<style>
    .gradient-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .gradient-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .gradient-card:hover::before {
        opacity: 1;
    }
    .gradient-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .rank-card {
        border-radius: 15px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .rank-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .stats-card {
        border-radius: 15px;
        background: linear-gradient(45deg, var(--bs-primary), var(--bs-primary-dark));
        border: none;
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .animated-icon {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .table-modern {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 500;
    }
    .requirements-details {
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
        backdrop-filter: blur(10px);
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
    .requirements-details.show {
        opacity: 1;
        transform: translateY(0);
    }
    .requirement-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .requirement-item:last-child {
        border-bottom: none;
    }
    .requirement-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    .requirement-label {
        font-size: 0.75rem;
        opacity: 0.9;
        margin-bottom: 0.2rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .requirement-value {
        font-size: 1rem;
        font-weight: 700;
    }
    .collapse-btn {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        border-radius: 20px;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .collapse-btn:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
    }
    .rank-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header text-center">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold mb-2">
                <i class="fas fa-crown animated-icon me-3"></i>
                Rank Commission Management
            </h1>
            <p class="lead mb-0">Complete overview and management of commission rates for all ranks</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-inline-block">
                <i class="fas fa-chart-line fa-4x opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card rank-card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Rank Commission Overview -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-trophy text-warning me-2"></i>
                            Rank Overview 
                        </h3>
                        <p class="text-center text-muted mb-4">Click on any rank card to view detailed requirements</p>
                    </div>
                    @foreach($rankCommissionData as $rank => $data)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card gradient-card text-white h-100">
                                <div class="card-body p-4">
                                    <!-- Rank Header -->
                                    <div class="rank-header">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge badge-modern bg-light text-dark me-2">Rank {{ $rank }}</span>
                                                <h5 class="mb-0 fw-bold">{{ $data['rank_name'] }}</h5>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <i class="fas fa-crown fa-2x opacity-75 animated-icon"></i>
                                        </div>
                                    </div>

                                    <!-- Quick Stats -->
                                    <div class="row text-center mb-3">
                                        <div class="col-3">
                                            <div class="border-end border-light border-opacity-25">
                                                <h5 class="mb-0 fw-bold">{{ $data['total_levels'] }}</h5>
                                                <small class="opacity-75" style="font-size: 0.7rem;">Levels</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="border-end border-light border-opacity-25">
                                                <h5 class="mb-0 fw-bold">{{ number_format($data['max_rate'], 1) }}%</h5>
                                                <small class="opacity-75" style="font-size: 0.7rem;">Max</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="border-end border-light border-opacity-25">
                                                <h5 class="mb-0 fw-bold">{{ number_format($data['min_rate'], 1) }}%</h5>
                                                <small class="opacity-75" style="font-size: 0.7rem;">Min</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <h5 class="mb-0 fw-bold">${{ number_format($data['reward_amount'], 0) }}</h5>
                                            <small class="opacity-75" style="font-size: 0.7rem;">Reward</small>
                                        </div>
                                    </div>

                                    <!-- Collapse Button -->
                                    <div class="text-center mb-2">
                                        <button class="collapse-btn" 
                                                type="button" 
                                                onclick="toggleRequirements({{ $rank }})"
                                                id="btn-{{ $rank }}">
                                            <i class="fas fa-eye me-2" id="icon-{{ $rank }}"></i>
                                        </button>
                                    </div>

                                    <!-- Requirements Details -->
                                    <div class="requirements-details" id="requirements-{{ $rank }}" style="display: none;">
                                        <!-- Team Business Volume -->
                                        <div class="requirement-item">
                                            <div class="requirement-icon">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="requirement-label">Team Business</div>
                                                <div class="requirement-value">${{ number_format($data['requirement']->team_business_volume, 0) }}</div>
                                            </div>
                                        </div>

                                        <!-- Count Level -->
                                        <div class="requirement-item">
                                            <div class="requirement-icon">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="requirement-label">Level Count</div>
                                                <div class="requirement-value">{{ $data['requirement']->count_level }} Levels</div>
                                            </div>
                                        </div>

                                        <!-- Personal Investment -->
                                        <div class="requirement-item">
                                            <div class="requirement-icon">
                                                <i class="fas fa-wallet"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="requirement-label">Personal Investment</div>
                                                <div class="requirement-value">${{ number_format($data['requirement']->personal_investment, 0) }}</div>
                                            </div>
                                        </div>

                                        <!-- Direct Referrals -->
                                        <div class="requirement-item">
                                            <div class="requirement-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="requirement-label">Direct Referrals</div>
                                                <div class="requirement-value">{{ $data['requirement']->direct_referrals }} Members</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- All Commission Levels Table -->
                <div class="card rank-card mt-5">
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-table me-2"></i>
                                    All Commission Levels
                                </h4>
                                <p class="mb-0 opacity-75">Complete list of all commission levels with individual edit access</p>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-bar fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-modern">
                                <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    <tr>
                                        <th class="fw-bold text-primary"><i class="fas fa-hashtag me-1"></i>ID</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-crown me-1"></i>Rank</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-layer-group me-1"></i>Level</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-percentage me-1"></i>Commission Rate</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-dollar-sign me-1"></i>Rank Reward</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-toggle-on me-1"></i>Status</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-cogs me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rankCommissionData as $rank => $data)
                                        @foreach($data['levels'] as $level)
                                            <tr class="align-middle {{ $loop->parent->first && $loop->first ? 'border-top border-3 border-primary' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                            <span class="text-white fw-bold small">{{ $level->id }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                            <span class="text-white fw-bold">{{ $rank }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark">{{ $data['rank_name'] }}</div>
                                                            <small class="text-muted">Rank {{ $rank }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                            <span class="text-white fw-bold">{{ $level->level }}</span>
                                                        </div>
                                                        <span class="fw-bold">Level {{ $level->level }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge {{ $level->commission_rate >= 10 ? 'bg-success' : ($level->commission_rate >= 5 ? 'bg-warning' : 'bg-info') }} badge-modern">
                                                            <i class="fas fa-percentage me-1"></i>
                                                            {{ number_format($level->commission_rate, 2) }}%
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-success badge-modern">
                                                            <i class="fas fa-dollar-sign me-1"></i>
                                                            ${{ number_format($data['reward_amount'], 2) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $level->is_active ? 'bg-success' : 'bg-secondary' }} badge-modern">
                                                        <i class="fas {{ $level->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                        {{ $level->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.rankcommission.edit', $level->id) }}" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Edit Level {{ $level->level }}">
                                                            <i class="fas fa-edit me-1"></i> Edit
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                type="button" 
                                                                data-bs-toggle="tooltip" 
                                                                title="Level {{ $level->level }} - {{ $data['rank_name'] }}">
                                                            <i class="fas fa-info"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="row mt-5">
                    <div class="col-12 text-center mb-4">
                        <h3>
                            <i class="fas fa-chart-pie text-primary me-2"></i>
                            Commission Statistics
                        </h3>
                        <p class="text-muted">Quick overview of your commission structure</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card stats-card text-white h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-crown fa-3x opacity-75"></i>
                                </div>
                                <h2 class="fw-bold mb-2">{{ count($rankCommissionData) }}</h2>
                                <p class="mb-0 opacity-75">Total Ranks</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card stats-card text-white h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-layer-group fa-3x opacity-75"></i>
                                </div>
                                <h2 class="fw-bold mb-2">{{ collect($rankCommissionData)->sum('total_levels') }}</h2>
                                <p class="mb-0 opacity-75">Total Levels</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card stats-card text-white h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-arrow-up fa-3x opacity-75"></i>
                                </div>
                                <h2 class="fw-bold mb-2">{{ number_format(collect($rankCommissionData)->max('max_rate'), 1) }}%</h2>
                                <p class="mb-0 opacity-75">Highest Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card stats-card text-white h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-arrow-down fa-3x opacity-75"></i>
                                </div>
                                <h2 class="fw-bold mb-2">{{ number_format(collect($rankCommissionData)->min('min_rate'), 1) }}%</h2>
                                <p class="mb-0 opacity-75">Lowest Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


