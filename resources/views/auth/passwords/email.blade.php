<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Password') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            font-family: 'Source Sans Pro', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
        }

        /* Card Design */
        .card {
            background: #222244;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.7);
            color: #fff;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            font-weight: 700;
            text-align: center;
            font-size: 1.4rem;
            padding: 1rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Input Fields */
        .form-control {
            background: #2c2c54;
            border: 1px solid #6a11cb;
            color: #fff;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: #9d4edd;
            box-shadow: 0 0 0 0.2rem rgba(157, 78, 221, 0.3);
        }

        /* Labels */
        .form-label {
            color: #c9c9f0;
            font-weight: 500;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: 0.3s ease-in-out;
            width: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #9d4edd, #5a189a);
            transform: translateY(-2px);
        }

        .btn-home {
            margin-top: 15px;
            width: 100%;
            border-radius: 8px;
            font-weight: 600;
            background: #444466;
            color: #fff;
            transition: 0.3s;
        }

        .btn-home:hover {
            background: #5a189a;
            color: #fff;
            transform: translateY(-2px);
        }

        /* Alerts */
        .alert-success {
            background: #2d6a4f;
            border: none;
            color: #d8f3dc;
            font-weight: 500;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shield-lock-fill"></i> {{ __('Reset Password') }}
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success text-center" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required
                               autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-envelope-fill"></i> {{ __('Send Password Reset Link') }}
                    </button>
                </form>

                <!-- Back to Home Button -->
                <a href="{{ url('/') }}" class="btn btn-home mt-3">
                    <i class="bi bi-house-fill"></i> {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
