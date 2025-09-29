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
            <h4>Edit Rank Commission - Level {{ $commissionLevel->level }}</h4>
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

            <form action="{{ route('admin.rankcommission.update', $commissionLevel->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Rank Information</label>
                            <div class="form-control-plaintext">
                                <strong>{{ $commissionLevel->rank_name }} - Level {{ $commissionLevel->level }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Max Levels for this Rank</label>
                            <div class="form-control-plaintext">
                                <span class="badge bg-info">{{ $commissionLevel->max_levels }} Levels</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Commission Rate (%) <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="commission_rate" 
                           class="form-control @error('commission_rate') is-invalid @enderror" 
                           value="{{ old('commission_rate', $commissionLevel->commission_rate) }}" 
                           required 
                           step="0.01"
                           min="0"
                           max="100"
                           placeholder="Enter commission rate (0-100)">
                    @error('commission_rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Commission rate must be between 0% and 100%</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rank Reward ($)</label>
                    <input type="number" 
                           name="rank_reward" 
                           class="form-control @error('rank_reward') is-invalid @enderror" 
                           value="{{ old('rank_reward', $commissionLevel->rank_reward) }}" 
                           step="0.01"
                           min="0"
                           placeholder="Enter rank reward amount">
                    @error('rank_reward')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Optional reward for achieving this rank level</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ old('is_active', $commissionLevel->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $commissionLevel->is_active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div class="form-text">Inactive levels will not be used in commission calculations</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Commission Level
                    </button>
                    <a href="{{ route('admin.rankcommission.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
