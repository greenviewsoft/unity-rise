@extends('layouts.user.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <style>
        .manual-deposit-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .deposit-address {
            background: #fff;
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 15px 0;
        }
        .address-text {
            font-family: monospace;
            font-size: 14px;
            word-break: break-all;
            color: #333;
            font-weight: bold;
        }
        .copy-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }
        .screenshot-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
@endsection

@section('content')
    <div class="page-content footer-clear">
        <div class="page_top_title deposit_page">
            <div class="arrow">
                <a href="{{ url('user/deposit') }}">
                    <i class="bi bi-arrow-left-circle-fill"></i>
                </a>
            </div>
            <h3 class="text-center">Manual Deposit</h3>
            @include('layouts.user.partial.support')
        </div>

        <div class="deposit_page_grave">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="manual-deposit-info">
                <h5><i class="bi bi-info-circle"></i> Manual Deposit Instructions</h5>
                <p>Please send your USDT-BEP20 to the address below, then fill out this form with your transaction details.</p>
                
                @if($bep20Setting && $bep20Setting->wallet_address)
                    <div class="deposit-address">
                        <strong>BEP20 Deposit Address:</strong><br>
                        <div class="address-text" id="depositAddress">{{ $bep20Setting->wallet_address }}</div>
                        <button type="button" class="copy-btn" onclick="copyAddress()">
                            <i class="bi bi-clipboard"></i> Copy Address
                        </button>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <strong>Error:</strong> Deposit address not configured. Please contact support.
                    </div>
                @endif

                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Important:</strong> Only send USDT on BEP20 network to this address. 
                        Sending other tokens or using different networks may result in permanent loss.
                    </small>
                </div>
            </div>

            <form action="{{ route('manual-deposit.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="amount">{{ __('lang.amount') }} (USDT) *</label>
                    <input type="number" 
                           class="form-control" 
                           id="amount" 
                           name="amount" 
                           placeholder="Enter deposit amount" 
                           step="0.01" 
                           min="1" 
                           value="{{ old('amount') }}" 
                           required>
                </div>

                <div class="form-group">
                    <label for="screenshot">Transaction Screenshot *</label>
                    <input type="file" 
                           class="form-control" 
                           id="screenshot" 
                           name="screenshot" 
                           accept="image/*" 
                           required 
                           onchange="previewImage(this)">
                    <small class="text-muted">Upload a screenshot of your transaction (JPG, PNG, GIF - Max 2MB)</small>
                    <div id="imagePreview"></div>
                </div>

                <div class="form-group">
                    <label for="transaction_hash">Transaction Hash (Optional)</label>
                    <input type="text" 
                           class="form-control" 
                           id="transaction_hash" 
                           name="transaction_hash" 
                           placeholder="Enter transaction hash if available" 
                           value="{{ old('transaction_hash') }}">
                    <small class="text-muted">You can find this in your wallet or block explorer</small>
                </div>

                <div class="form-group">
                    <label for="user_notes">Additional Notes (Optional)</label>
                    <textarea class="form-control" 
                              id="user_notes" 
                              name="user_notes" 
                              rows="3" 
                              placeholder="Any additional information about your deposit"
                              maxlength="500">{{ old('user_notes') }}</textarea>
                    <small class="text-muted">Maximum 500 characters</small>
                </div>

                <div class="next_btn_deposit">
                    <button type="submit" class="mx-3 btn btn-full shadow-bg shadow-bg-s">
                        <i class="bi bi-upload"></i> Submit Deposit Request
                    </button>
                </div>
            </form>

            <div class="mt-4">
                <div class="alert alert-info">
                    <h6><i class="bi bi-clock"></i> What happens next?</h6>
                    <ul style="margin: 10px 0 0 20px; padding: 0;">
                        <li>Your deposit request will be reviewed by our admin team</li>
                        <li>We will verify your transaction on the blockchain</li>
                        <li>Once approved, the amount will be added to your account balance</li>
                        <li>You will receive a notification when the deposit is processed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
    <script>
        function copyAddress() {
            const addressText = document.getElementById('depositAddress').textContent;
            navigator.clipboard.writeText(addressText).then(function() {
                const btn = document.querySelector('.copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
                btn.style.backgroundColor = '#28a745';
                
                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.style.backgroundColor = '#007bff';
                }, 2000);
            }).catch(function(err) {
                alert('Failed to copy address. Please copy manually.');
            });
        }

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'screenshot-preview';
                    img.alt = 'Screenshot Preview';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection