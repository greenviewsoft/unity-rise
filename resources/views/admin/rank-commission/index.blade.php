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
    }
    .gradient-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
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
    .level-card {
        border-radius: 10px;
        transition: all 0.2s ease;
        border: 2px solid #e9ecef;
    }
    .level-card:hover {
        border-color: #007bff;
        transform: scale(1.02);
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
                        {{ session('success') }}
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
                    </div>
                    @foreach($rankCommissionData as $rank => $data)
                        <div class="col-lg-6 col-md-6 mb-4">
                            <div class="card gradient-card text-white h-100">
                                <div class="card-body p-4">
                                    <div class="row g-0 align-items-center">
                                        <div class="col me-2">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="badge badge-modern bg-light text-dark me-2">Rank {{ $rank }}</span>
                                                <h5 class="mb-0 fw-bold">{{ $data['rank_name'] }}</h5>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="border-end border-light border-opacity-25">
                                                        <h4 class="mb-0 fw-bold">{{ $data['total_levels'] }}</h4>
                                                        <small class="opacity-75">Levels</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end border-light border-opacity-25">
                                                        <h4 class="mb-0 fw-bold">{{ number_format($data['max_rate'], 1) }}%</h4>
                                                        <small class="opacity-75">Max Rate</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h4 class="mb-0 fw-bold">{{ number_format($data['min_rate'], 1) }}%</h4>
                                                    <small class="opacity-75">Min Rate</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="text-center">
                                                <i class="fas fa-crown fa-3x opacity-75 animated-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Detailed Commission Table -->
                <div class="card rank-card mt-5">
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-table me-2"></i>
                                    Detailed Commission Structure
                                </h4>
                                <p class="mb-0 opacity-75">Comprehensive breakdown of all commission levels</p>
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
                                        <th class="fw-bold text-primary"><i class="fas fa-hashtag me-1"></i>Rank</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-crown me-1"></i>Rank Name</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-layer-group me-1"></i>Total Levels</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-percentage me-1"></i>Commission Range</th>
                                        <th class="fw-bold text-primary"><i class="fas fa-cogs me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rankCommissionData as $rank => $data)
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                        <span class="text-white fw-bold">{{ $rank }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-crown text-warning me-2"></i>
                                                    <strong class="text-dark">{{ $data['rank_name'] }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-modern bg-info text-white">
                                                    <i class="fas fa-layer-group me-1"></i>
                                                    {{ $data['total_levels'] }} Levels
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success me-1">{{ number_format($data['max_rate'], 1) }}%</span>
                                                    <i class="fas fa-arrow-right text-muted mx-2"></i>
                                                    <span class="badge bg-warning">{{ number_format($data['min_rate'], 1) }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#rank{{ $rank }}Details" 
                                                            aria-expanded="false"
                                                            title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('admin.rankcommission.edit', $rank) }}" 
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Edit Rank">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="p-0">
                                                <div class="collapse" id="rank{{ $rank }}Details">
                                                    <div class="bg-light p-4">
                                                        <div class="row align-items-center mb-3">
                                                            <div class="col">
                                                                <h5 class="text-primary mb-0">
                                                                    <i class="fas fa-layer-group me-2"></i>
                                                                    Rank {{ $rank }} ({{ $data['rank_name'] }}) - Level Details
                                                                </h5>
                                                            </div>
                                                            <div class="col-auto">
                                                                <span class="badge bg-primary badge-modern">
                                                                    {{ count($data['levels']) }} Levels Total
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            @foreach($data['levels'] as $level)
                                                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                                                    <div class="card level-card h-100">
                                                                        <div class="card-body text-center p-3">
                                                                            <div class="mb-2">
                                                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">
                                                                                    <span class="text-white fw-bold">{{ $level->level }}</span>
                                                                                </div>
                                                                                <small class="text-muted">Level {{ $level->level }}</small>
                                                                            </div>
                                                                            <h5 class="text-primary fw-bold mb-3">{{ number_format($level->commission_rate, 2) }}%</h5>
                                                                            <div class="mb-2">
                                                                                <small class="text-muted d-block">Reward</small>
                                                                                <span class="badge bg-success">${{ number_format($level->rank_reward, 2) }}</span>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <span class="badge {{ $level->is_active ? 'bg-success' : 'bg-secondary' }} badge-modern">
                                                                                    <i class="fas {{ $level->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                                                    {{ $level->is_active ? 'Active' : 'Inactive' }}
                                                                                </span>
                                                                            </div>
                                                                            <a href="{{ route('admin.rankcommission.edit', $level->id) }}" 
                                                                               class="btn btn-sm btn-outline-primary w-100" 
                                                                               title="Edit Level {{ $level->level }}">
                                                                                <i class="fas fa-edit me-1"></i> Edit
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
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

@section('script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Enhanced card animations
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Level card hover effects
    const levelCards = document.querySelectorAll('.level-card');
    levelCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#007bff';
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = '0 8px 25px rgba(0,123,255,0.15)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e9ecef';
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });

    // Smooth scroll animation for collapse
    const collapseButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
    collapseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-eye')) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Add loading animation to edit buttons
    const editButtons = document.querySelectorAll('a[href*="edit"]');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            setTimeout(() => {
                icon.classList.remove('fa-spin');
            }, 1000);
        });
    });

    // Counter animation for statistics
    const counters = document.querySelectorAll('.stats-card h2');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = counter.textContent;
                clearInterval(timer);
            } else {
                const isPercentage = counter.textContent.includes('%');
                counter.textContent = Math.floor(current) + (isPercentage ? '%' : '');
            }
        }, 30);
    });
});
</script>
@endsection
