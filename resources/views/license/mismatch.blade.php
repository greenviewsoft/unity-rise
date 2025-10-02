<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Verification Failed</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right,rgb(15, 112, 215),rgb(24, 4, 179));
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: #d9534f;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            padding: 20px;
        }

        .alert-icon {
            font-size: 4rem;
            color: #d9534f;
            animation: shake 0.6s ease-in-out infinite alternate;
        }

        @keyframes shake {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
            75% { transform: rotate(-3deg); }
            100% { transform: rotate(3deg); }
        }

        .support-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .support-info p {
            margin-bottom: 8px;
        }

        .btn-whatsapp {
            background-color: #25d366;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .btn-whatsapp:hover {
            background-color: #1ebe5d;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
                        <p class="mb-0">License Verification Failed</p>
                    </div>
                    <div class="card-body text-center">
                        <p class="fs-5 text-danger">Your license verification has failed or expired.</p>
                        <p class="text-muted">The license for this application does not match the registered domain. 
                            Please contact support if you believe this is a mistake.</p>

                        <!-- Show API Response Message -->
                        @if(isset($error_message))
                            <div class="alert alert-warning">
                                <strong></strong> {{ $error_message }}
                            </div>
                        @endif

                        <!-- Support Contact Information -->
                        <div class="support-info mt-4">
                            <p><strong>Contact Support</strong></p>
                            <p><i class="bi bi-envelope-fill"></i> Email: <a href="mailto:rexwallets@gmail.com">rexwallets@gmail.com</a></p>
                        </div>

                        <a href="https://wa.me/8801716720487" class="btn btn-whatsapp mt-3">
                            <i class="bi bi-whatsapp"></i> Contact on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
