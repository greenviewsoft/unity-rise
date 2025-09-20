@extends('admin.layout.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User USDT Balances (BEP20)</h4>
                    <div class="card-tools">
                        <div class="alert alert-info mb-0">
                            <strong>Total USDT Balance: ${{ number_format($totalUsdtBalance, 2) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Wallet Address</th>
                                    <th>Platform Balance</th>
                                    <th>USDT Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="wallet-address" title="{{ $user->wallet_address }}">
                                                {{ substr($user->wallet_address, 0, 10) }}...{{ substr($user->wallet_address, -8) }}
                                            </span>
                                            <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyToClipboard('{{ $user->wallet_address }}')">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">
                                            ${{ number_format($user->balance, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->usdt_balance > 0 ? 'badge-success' : 'badge-secondary' }}">
                                            ${{ number_format($user->usdt_balance, 2) }} USDT
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info" onclick="refreshBalance({{ $user->id }}, '{{ $user->wallet_address }}')"
                                                    id="refresh-btn-{{ $user->id }}">
                                                <i class="fa fa-refresh"></i> Refresh
                                            </button>
                                            @if($user->usdt_balance > 0)
                                            <button class="btn btn-sm btn-warning" onclick="transferToAdmin('{{ $user->wallet_address }}', {{ $user->usdt_balance }})">
                                                <i class="fa fa-arrow-right"></i> Transfer
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-warning mb-0">
                                            No users with wallet addresses found.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bulk Actions Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Bulk Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-primary btn-block" onclick="refreshAllBalances()">
                                <i class="fa fa-refresh"></i> Refresh All Balances
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success btn-block" onclick="transferAllToAdmin()">
                                <i class="fa fa-arrow-right"></i> Transfer All to Admin
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-info btn-block" onclick="exportBalances()">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copy wallet address to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Wallet address copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}

// Refresh individual balance
function refreshBalance(userId, walletAddress) {
    const btn = document.getElementById(`refresh-btn-${userId}`);
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
    btn.disabled = true;
    
    fetch('/admin/refresh-usdt-balance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            wallet_address: walletAddress
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error refreshing balance: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error refreshing balance');
    })
    .finally(() => {
        btn.innerHTML = '<i class="fa fa-refresh"></i> Refresh';
        btn.disabled = false;
    });
}

// Transfer to admin wallet
function transferToAdmin(walletAddress, amount) {
    if (!confirm(`Transfer $${amount} USDT to admin wallet?`)) {
        return;
    }
    
    fetch('/admin/transfer-usdt-to-admin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            wallet_address: walletAddress,
            amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Transfer successful!');
            location.reload();
        } else {
            alert('Transfer failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Transfer failed');
    });
}

// Refresh all balances
function refreshAllBalances() {
    if (!confirm('This will refresh all user USDT balances. This may take a while. Continue?')) {
        return;
    }
    
    alert('Refreshing all balances... Please wait.');
    location.reload();
}

// Transfer all to admin
function transferAllToAdmin() {
    if (!confirm('Transfer ALL user USDT balances to admin wallet? This action cannot be undone!')) {
        return;
    }
    
    fetch('/admin/transfer-all-usdt-to-admin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Successfully transferred ${data.total_transferred} USDT to admin wallet`);
            location.reload();
        } else {
            alert('Bulk transfer failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bulk transfer failed');
    });
}

// Export balances to CSV
function exportBalances() {
    window.location.href = '/admin/export-usdt-balances';
}
</script>
@endsection