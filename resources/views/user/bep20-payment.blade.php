<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BEP20 Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #e8e9f3;
        }
        .payment-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 20px rgba(138, 43, 226, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        .card {
            background: rgba(30, 30, 60, 0.9);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #8a2be2 0%, #6a1b9a 50%, #4a148c 100%) !important;
            border-radius: 20px 20px 0 0 !important;
            padding: 25px;
            border: none;
        }
        .amount-display {
            background: linear-gradient(135deg, #9c27b0, #673ab7, #3f51b5);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(156, 39, 176, 0.3);
            border: 1px solid rgba(156, 39, 176, 0.3);
        }
        .wallet-section {
            background: rgba(40, 40, 80, 0.6);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(138, 43, 226, 0.3);
            backdrop-filter: blur(5px);
        }
        .form-control {
            background: rgba(50, 50, 100, 0.8);
            border: 1px solid rgba(138, 43, 226, 0.4);
            color: #e8e9f3;
            border-radius: 8px;
            padding: 12px 15px;
        }
        .form-control:focus {
            background: rgba(60, 60, 120, 0.9);
            border-color: #8a2be2;
            box-shadow: 0 0 15px rgba(138, 43, 226, 0.3);
            color: #fff;
        }
        .copy-btn {
            background: linear-gradient(135deg, #8a2be2, #6a1b9a);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(138, 43, 226, 0.3);
        }
        .copy-btn:hover {
            background: linear-gradient(135deg, #9c27b0, #7b1fa2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(138, 43, 226, 0.4);
        }
        .status-badge {
            font-size: 16px;
            padding: 12px 24px;
            border-radius: 25px;
            background: linear-gradient(135deg, #ff9800, #f57c00);
            border: none;
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #3d4043);
            transform: translateY(-2px);
        }
        .alert-info {
            background: rgba(103, 58, 183, 0.2);
            border: 1px solid rgba(103, 58, 183, 0.3);
            color: #e8e9f3;
            border-radius: 12px;
        }
        .form-label {
            color: #b39ddb;
            font-weight: 600;
            margin-bottom: 10px;
        }
        h2, h4, h6 {
            color: #fff;
            font-weight: 700;
        }
        .text-muted {
            color: #9e9e9e !important;
        }
        .qr-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 15px;
            display: inline-block;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .container {
            padding-top: 40px;
            padding-bottom: 40px;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">BEP20 Payment</h4>
                </div>
                <div class="card-body">
                    <!-- Amount Section -->
                    <div class="text-center mb-4">
                        <h2 class="text-success">${{ number_format($depositAmount, 2) }} USD</h2>
                        <p class="text-muted">Amount to Deposit</p>
                    </div>

                    <!-- Wallet Address Section -->
                    <div class="wallet-section mb-4">
                        <label class="form-label fw-bold">Wallet Address:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="walletAddress" value="{{ $walletAddress }}" readonly>
                            <button class="copy-btn" type="button" onclick="copyAddress()">Copy</button>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="text-center mb-4">
                        <label class="form-label fw-bold">QR Code:</label>
                        <div class="d-flex justify-content-center">
                            <div class="qr-container">
                                <img id="qrcode" alt="QR Code">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="alert alert-info">
                        <h6>Payment Instructions:</h6>
                        <ul class="mb-0">
                            <li>Send exactly the amount shown above</li>
                            <li>Use BEP20 network only</li>
                            <li>Payment will be confirmed automatically</li>
                        </ul>
                    </div>

                    <!-- Status Section -->
                    <div class="text-center">
                        <div class="badge bg-warning fs-6 mb-3">Waiting for Payment</div>
                        <br>
                        <a href="#" class="btn btn-secondary">Back to Deposit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Generate QR Code when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const walletAddressInput = document.getElementById('walletAddress');
        const qrCodeImg = document.getElementById('qrcode');
        
        // Check if elements exist
        if (!walletAddressInput || !qrCodeImg) {
            console.error('Required elements not found');
            return;
        }
        
        const walletAddress = walletAddressInput.value;
        
        if (!walletAddress || walletAddress.trim() === '') {
            console.error('Wallet address is empty');
            qrCodeImg.alt = 'No wallet address available';
            // Show fallback message
            const fallbackDiv = document.createElement('div');
            fallbackDiv.className = 'text-center text-muted';
            fallbackDiv.innerHTML = '<p>No wallet address available<br>Please refresh the page</p>';
            qrCodeImg.parentNode.replaceChild(fallbackDiv, qrCodeImg);
            return;
        }
        
        // Try multiple QR code services for better reliability
        const qrServices = [
            `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(walletAddress)}`,
            `https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=${encodeURIComponent(walletAddress)}&choe=UTF-8`,
            `https://qr-server.com/api/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(walletAddress)}`
        ];
        
        let currentServiceIndex = 0;
        
        function tryLoadQR() {
            if (currentServiceIndex >= qrServices.length) {
                console.error('All QR code services failed');
                qrCodeImg.alt = 'QR Code generation failed';
                qrCodeImg.style.display = 'none';
                // Show fallback text
                const fallbackDiv = document.createElement('div');
                fallbackDiv.className = 'text-center text-muted';
                fallbackDiv.innerHTML = '<p>QR Code unavailable<br>Please copy the wallet address above</p>';
                if (qrCodeImg.parentNode) {
                    qrCodeImg.parentNode.appendChild(fallbackDiv);
                }
                return;
            }
            
            qrCodeImg.onload = function() {
                console.log('QR Code loaded successfully from service:', currentServiceIndex + 1);
                qrCodeImg.style.width = '200px';
                qrCodeImg.style.height = '200px';
                qrCodeImg.style.display = 'block';
            };
            
            qrCodeImg.onerror = function() {
                console.warn('QR service', currentServiceIndex + 1, 'failed, trying next...');
                currentServiceIndex++;
                setTimeout(tryLoadQR, 500); // Add small delay before trying next service
            };
            
            qrCodeImg.src = qrServices[currentServiceIndex];
        }
        
        // Start loading QR code
        tryLoadQR();
    });
    
    // Copy wallet address function
    function copyAddress() {
        const walletInput = document.getElementById('walletAddress');
        walletInput.select();
        walletInput.setSelectionRange(0, 99999); // For mobile devices
        
        navigator.clipboard.writeText(walletInput.value).then(function() {
            // Show success message
            const copyBtn = document.querySelector('.copy-btn');
            const originalText = copyBtn.textContent;
            copyBtn.textContent = 'Copied!';
            copyBtn.style.backgroundColor = '#28a745';
            
            setTimeout(function() {
                copyBtn.textContent = originalText;
                copyBtn.style.backgroundColor = '#007bff';
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            alert('Failed to copy address');
        });
    }
</script>
</body>
</html>