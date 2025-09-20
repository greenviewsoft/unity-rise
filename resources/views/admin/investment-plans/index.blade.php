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
                    <h3 class="card-title">Investment Plans Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.investment-plans.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Plan
                        </a>
                        <a href="{{ route('admin.investment-plans.settings') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Plan Name</th>
                                    <th>Amount Range</th>
                                    <th>Daily Profit</th>
                                    <th>Total Profit</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>{{ $plan->id }}</td>
                                        <td>
                                            <strong>{{ $plan->name }}</strong>
                                            @if($plan->description)
                                                <br><small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $plan->formatted_min_amount }} - {{ $plan->formatted_max_amount }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ $plan->daily_profit_percentage }}% daily
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $plan->total_profit_percentage }}% total
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ $plan->duration_days }} days
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $plan->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $plan->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.investment-plans.show', $plan->id) }}" 
                                                   class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.investment-plans.edit', $plan->id) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($plan->status)
                                                    <a href="{{ route('admin.investment-plans.deactivate', $plan->id) }}" 
                                                       class="btn btn-secondary btn-sm" title="Deactivate"
                                                       onclick="return confirm('Are you sure you want to deactivate this plan?')">
                                                        <i class="fas fa-pause"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.investment-plans.activate', $plan->id) }}" 
                                                       class="btn btn-success btn-sm" title="Activate"
                                                       onclick="return confirm('Are you sure you want to activate this plan?')">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.investment-plans.destroy', $plan->id) }}" 
                                                      method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this plan? This action cannot be undone.')">
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
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Investment Plans Found</h5>
                                                <p class="text-muted">Create your first investment plan to get started.</p>
                                                <a href="{{ route('admin.investment-plans.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create Investment Plan
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($plans->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $plans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush