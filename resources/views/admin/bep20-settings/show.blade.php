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
                    <h3 class="card-title">BEP20 Setting Details #{{ $bep20Setting->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bep20-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('admin.bep20-settings.edit', $bep20Setting) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>General Settings</h5>
                            <table class="table table-bordered">
                                {{-- <tr>
                                    <th width="40%">ID</th>
                                    <td>{{ $bep20Setting->id }}</td>
                                </tr> --}}
                                <tr>
                                    <th>Minimum Withdraw</th>
                                    <td>${{ number_format($bep20Setting->min_withdraw, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Withdraw Fee</th>
                                    <td>${{ number_format($bep20Setting->withdraw_fee, 2) }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Gas Limit</th>
                                    <td>{{ number_format($bep20Setting->gas_limit) }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $bep20Setting->created_at ? $bep20Setting->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $bep20Setting->updated_at ? $bep20Setting->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr> --}}
                            </table>
                        </div>
                        {{-- <div class="col-md-6">
                            <h5>Status Overview</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Sender Status</th>
                                    <td>
                                        <span class="badge badge-{{ $bep20Setting->sender_status == '1' || $bep20Setting->sender_status == 'auto' ? 'success' : 'warning' }}">
                                            {{ $bep20Setting->sender_status == '1' || $bep20Setting->sender_status == 'auto' ? 'Auto' : 'Manual' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Receiver Status</th>
                                    <td>
                                        <span class="badge badge-{{ $bep20Setting->receiver_status == '1' || $bep20Setting->receiver_status == 'auto' ? 'success' : 'warning' }}">
                                            {{ $bep20Setting->receiver_status == '1' || $bep20Setting->receiver_status == 'auto' ? 'Auto' : 'Manual' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div> --}}
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>Manual Deposit Configuration</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Admin Deposit Address<</th>
                                    <td>
                                        @if($bep20Setting->receiver_address)
                                            <code>{{ $bep20Setting->receiver_address }}</code>
                                            <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyToClipboard('{{ $bep20Setting->receiver_address }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">Not configured</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>This address will be shown to all users for manual BEP20 deposits</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="btn-group">
                            <a href="{{ route('admin.bep20-settings.edit', $bep20Setting) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Setting
                            </a>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // You could add a toast notification here
        alert('Address copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection