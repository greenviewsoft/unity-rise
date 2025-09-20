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
            <form action="{{ route('admin.rankcommission.update', $commissionLevel->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Commission Rate (%)</label>
                    <input type="number" name="commission_rate" class="form-control" value="{{ old('commission_rate', $commissionLevel->commission_rate) }}" required step="0.01">
                </div>

                <div class="mb-3">
                    <label class="form-label">Rank Reward (Optional)</label>
                    <input type="number" name="rank_reward" class="form-control" value="{{ old('rank_reward', $commissionLevel->rank_reward) }}" step="0.01">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $commissionLevel->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$commissionLevel->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.rankcommission.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
