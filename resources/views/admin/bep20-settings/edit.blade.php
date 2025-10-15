@extends('layouts.admin.app')

@section('css')
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
                    <h3 class="card-title">Edit BEP20 Setting #{{ $bep20Setting->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bep20-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
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

                    <form action="{{ route('admin.bep20-settings.update', $bep20Setting) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="min_withdraw" class="form-label">Minimum Withdraw Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="min_withdraw" name="min_withdraw" value="{{ old('min_withdraw', $bep20Setting->min_withdraw) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="withdraw_fee" class="form-label">Withdraw Fee</label>
                                    <input type="number" step="0.01" class="form-control" id="withdraw_fee" name="withdraw_fee" value="{{ old('withdraw_fee', $bep20Setting->withdraw_fee) }}" required>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gas_limit" class="form-label">Gas Limit</label>
                                    <input type="number" class="form-control" id="gas_limit" name="gas_limit" value="{{ old('gas_limit', $bep20Setting->gas_limit) }}" required>
                                </div>
                            </div>
                        </div> --}}

                        {{-- <hr>
                        <h5>Sender Configuration</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sender_address" class="form-label">Sender Address</label>
                                    <input type="text" class="form-control" id="sender_address" name="sender_address" value="{{ old('sender_address', $bep20Setting->sender_address) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sender_status" class="form-label">Sender Status</label>
                                    <select class="form-control" id="sender_status" name="sender_status" required>
                                        <option value="">Select Status</option>
                                        <option value="0" {{ old('sender_status', $bep20Setting->sender_status) == '0' || old('sender_status', $bep20Setting->sender_status) == 'manual' ? 'selected' : '' }}>Manual</option>
                                        <option value="1" {{ old('sender_status', $bep20Setting->sender_status) == '1' || old('sender_status', $bep20Setting->sender_status) == 'auto' ? 'selected' : '' }}>Auto</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="sender_private_key" class="form-label">Sender Private Key</label>
                                    <input type="password" class="form-control" id="sender_private_key" name="sender_private_key" value="{{ old('sender_private_key', $bep20Setting->sender_private_key) }}" required>
                                    <small class="form-text text-muted">This will be encrypted and stored securely.</small>
                                </div>
                            </div>
                        </div> --}}

                        <hr>
                        <h5>Manual Deposit Configuration</h5>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="receiver_address" class="form-label">Admin Deposit Address (All users will see this address)</label>
                                    <input type="text" class="form-control" id="receiver_address" name="receiver_address" value="{{ old('receiver_address', $bep20Setting->receiver_address) }}" placeholder="Enter BEP20 wallet address for manual deposits">
                                    <small class="form-text text-muted">This address will be shown to all users for manual BEP20 deposits.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Setting
                            </button>
                            <a href="{{ route('admin.bep20-settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
@endsection