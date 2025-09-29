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
        <div class="card-header">
            <h4 class="mb-0">
                <i class="fas fa-plus me-2"></i>Add New Social Link
            </h4>
        </div>
        
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.social-links.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required placeholder="e.g., Telegram Support">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Icon Class <span class="text-danger">*</span></label>
                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                   value="{{ old('icon') }}" required placeholder="e.g., fab fa-telegram-plane">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">FontAwesome icon class (e.g., fab fa-telegram-plane, fab fa-whatsapp)</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">URL <span class="text-danger">*</span></label>
                    <input type="url" name="url" class="form-control @error('url') is-invalid @enderror" 
                           value="{{ old('url') }}" required placeholder="https://t.me/yourchannel">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Color <span class="text-danger">*</span></label>
                            <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                   value="{{ old('color', '#007bff') }}" required>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                   value="{{ old('sort_order', 0) }}" min="0" placeholder="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Lower numbers appear first</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create Social Link
                    </button>
                    <a href="{{ route('admin.social-links.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
