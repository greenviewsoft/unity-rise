@extends('layouts.admin.app')



@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<style>
    /* Custom styles for responsive recent deposits table */
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    
    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
    }
    
    #recentDepositsTable {
        font-size: 14px;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        #recentDepositsTable {
            font-size: 12px;
        }
        
        #recentDepositsTable th,
        #recentDepositsTable td {
            padding: 8px 4px;
        }
        
        .avatar-sm {
            width: 24px;
            height: 24px;
        }
        
        .avatar-title {
            font-size: 12px;
        }
        
        /* Stack user info vertically on mobile */
        #recentDepositsTable .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        #recentDepositsTable .avatar-sm {
            margin-bottom: 4px;
            margin-right: 0 !important;
        }
    }
    
    /* Extra small devices */
    @media (max-width: 576px) {
        #recentDepositsTable {
            font-size: 11px;
        }
        
        #recentDepositsTable th:nth-child(4),
        #recentDepositsTable td:nth-child(4) {
            display: none; /* Hide method column on very small screens */
        }
    }
    
    /* Hover effects */
    #recentDepositsTable tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
        transition: background-color 0.3s ease;
    }
    
    /* Card styling */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        font-weight: 600;
    }
</style>
@endsection



@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">Today deposite</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $todaydeposite }} USDT</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">Today users</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $todayuser }} USER</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">Pending withdraw</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $pendwithdraw }} USDT</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">Today Withdraw</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $todaywithdraw }} USDT</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>




    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">All deposite</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $totaldeposite }} USDT</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">All users</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $totaluser }} USER</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">Total Profit</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $totaldeposite - $totalwithdraw }} USDT</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">Total Withdraw</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Total : {{ $totalwithdraw }} USDT</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Added Deposits Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Recently Added Deposits (Latest 10)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="recentDepositsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Request By</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDeposits as $deposit)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $deposit->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-primary rounded-circle text-white">
                                                    {{ substr($deposit->user->username ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $deposit->user->username ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $deposit->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">${{ number_format($deposit->amount, 2) }}</span>
                                        <small class="text-muted d-block">USDT</small>
                                    </td>
                                    <td>
    @if($deposit->deposit_type == 'admin')
        <span class="badge bg-primary">Admin Added</span>
    @else
        <span class="badge bg-success">User Confirmed</span>
    @endif
</td>
                                    <td>
                                        @if($deposit->status == 1)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Completed
                                            </span>
                                        @elseif($deposit->status == 0)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $deposit->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $deposit->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">No recent deposits found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- USDT Balances Section -->
   

</div>
@endsection



@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/datatables-simple-demo.js"></script>
@endsection