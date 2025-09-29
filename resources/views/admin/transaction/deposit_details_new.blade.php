@extends('layouts.admin.app')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
                <div class="breadcrumb-title pr-3">Deposit Details</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i class='bx bx-home-alt'></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin/deposite') }}">Deposits</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Deposit Details
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="text-primary mb-3">
                                            <i class="fas fa-info-circle me-2"></i>Deposit Information
                                        </h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Deposit ID:</strong></td>
                                                <td><span class="badge bg-primary">{{ $deposite->id }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Order Number:</strong></td>
                                                <td><code>{{ $deposite->order_number }}</code></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Amount:</strong></td>
                                                <td><span class="badge bg-success">{{ number_format($deposite->amount, 2) }} {{ $deposite->currency }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Transaction ID:</strong></td>
                                                <td>
                                                    @if($deposite->txid)
                                                        <code class="text-break">{{ $deposite->txid }}</code>
                                                    @else
                                                        <span class="text-muted">Not available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @if($deposite->status == 1)
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($deposite->status == 0)
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unknown</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Created At:</strong></td>
                                                <td>{{ $deposite->created_at->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="text-primary mb-3">
                                            <i class="fas fa-user me-2"></i>User Information
                                        </h5>
                                        @if($user)
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>User ID:</strong></td>
                                                    <td><span class="badge bg-info">{{ $user->id }}</span></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td><strong>Username:</strong></td>
                                                    <td>{{ $user->username ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Phone:</strong></td>
                                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email:</strong></td>
                                                    <td>{{ $user->email ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Registered:</strong></td>
                                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                User information not available (User ID: {{ $deposite->user_id }})
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($order)
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-primary mb-3">
                                                <i class="fas fa-shopping-cart me-2"></i>Order Information
                                            </h5>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Order Status:</strong></td>
                                                    <td>
                                                        @if($order->status == 1)
                                                            <span class="badge bg-success">Completed</span>
                                                        @elseif($order->status == 0)
                                                            <span class="badge bg-warning">Pending</span>
                                                        @else
                                                            <span class="badge bg-secondary">Unknown</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Auto Receive:</strong></td>
                                                    <td>
                                                        @if($order->autoreceive == 1)
                                                            <span class="badge bg-success">Yes</span>
                                                        @else
                                                            <span class="badge bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Order Created:</strong></td>
                                                    <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($addresstrx)
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-primary mb-3">
                                                <i class="fas fa-wallet me-2"></i>Wallet Information
                                            </h5>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Address (Base58):</strong></td>
                                                    <td><code class="text-break">{{ $addresstrx->address_base58 }}</code></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Address (Hex):</strong></td>
                                                    <td><code class="text-break">{{ $addresstrx->address_hex }}</code></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Private Key:</strong></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="privateKey" 
                                                                   value="{{ $addresstrx->private_key }}" readonly>
                                                            <button class="btn btn-outline-secondary" type="button" 
                                                                    onclick="togglePrivateKey()">
                                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Public Key:</strong></td>
                                                    <td><code class="text-break">{{ $addresstrx->public_key }}</code></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Is Validated:</strong></td>
                                                    <td>
                                                        @if($addresstrx->is_validate == 1)
                                                            <span class="badge bg-success">Yes</span>
                                                        @else
                                                            <span class="badge bg-warning">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <hr>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Wallet information not available for this deposit
                                    </div>
                                @endif

                                <div class="text-center mt-4">
                                    <a class="btn btn-primary" href="{{ url('admin/deposite') }}">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Deposits
                                    </a>
                                    <a class="btn btn-danger ms-2" href="{{ url('admin/deposit/delete', $deposite->id) }}"
                                       onclick="return confirm('Are you sure you want to delete this deposit?')">
                                        <i class="fas fa-trash me-2"></i>Delete Deposit
                                    </a>
                                </div>
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
        function togglePrivateKey() {
            const input = document.getElementById('privateKey');
            const icon = document.getElementById('toggleIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
