@extends('layouts.admin.app')



@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection



@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trading History PDFs</h4>
                    <a href="{{ route('admin.trading-history.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Upload New PDF
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>File Size</th>
                                    <th>Upload Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tradingHistories as $history)
                                    <tr>
                                        <td>{{ $loop->iteration + ($tradingHistories->currentPage() - 1) * $tradingHistories->perPage() }}</td>
                                        <td>
                                            <strong style="font-size: 1.1em;">{{ $history->title }}</strong>
                                        </td>
                                        <td>{{ Str::limit($history->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $history->formatted_file_size }}</span>
                                        </td>
                                      
                                        <td>{{ $history->upload_date->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($history->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                               
                                               
                                                <form method="POST" action="{{ route('admin.trading-history.toggle', $history) }}"
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to toggle status?')">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-sm {{ $history->is_active ? 'btn-danger' : 'btn-success' }}"
                                                            title="{{ $history->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fa {{ $history->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.trading-history.destroy', $history) }}"
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this PDF?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fa fa-file-pdf-o fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No trading history PDFs uploaded yet.</p>
                                                <a href="{{ route('admin.trading-history.create') }}" class="btn btn-primary">
                                                    Upload First PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($tradingHistories->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $tradingHistories->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th, .table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.85em;
}

.card-title {
    font-size: 1.3em;
    font-weight: bold;
}
</style>
@endsection
