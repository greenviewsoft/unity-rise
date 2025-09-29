@extends('layouts.admin.app')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-share-alt me-2"></i>Social Links Management
            </h4>
            <a href="{{ route('admin.social-links.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add New Link
            </a>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($socialLinks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sort</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>URL</th>
                                <th>Color</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($socialLinks as $link)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $link->sort_order }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $link->name }}</strong>
                                    </td>
                                    <td>
                                        <i class="{{ $link->icon }}" style="color: {{ $link->color }}; font-size: 20px;"></i>
                                    </td>
                                    <td>
                                        <a href="{{ $link->url }}" target="_blank" class="text-decoration-none">
                                            {{ Str::limit($link->url, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: {{ $link->color }}; border-radius: 3px;"></div>
                                            <code>{{ $link->color }}</code>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.social-links.toggle', $link->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $link->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $link->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.social-links.edit', $link->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.social-links.destroy', $link->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this social link?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-share-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Social Links Found</h5>
                    <p class="text-muted">Start by adding your first social link.</p>
                    <a href="{{ route('admin.social-links.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add First Link
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
