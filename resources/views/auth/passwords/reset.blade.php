<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Password') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* Background */
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            font-family: 'Source Sans Pro', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Card container */
        .auth-container {
            width: 100%;
            max-width: 480px;
            animation: fadeIn 0.8s ease-in-out;
        }

        /* Card styling */
        .card {
            background: #222244;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.7);
            color: #fff;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        }

        /* Header */
        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            font-weight: 700;
            text-align: center;
            font-size: 1.5rem;
            padding: 1.2rem;
            letter-spacing: 0.5px;
        }

        /* Body */
        .card-body {
            padding: 2rem;
        }

        /* Inputs */
        .form-control {
            background: #2c2c54;
            border: 1px solid #6a11cb;
            color: #fff;
            border-radius: 10px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #9d4edd;
            box-shadow: 0 0 0 0.2rem rgba(157, 78, 221, 0.4);
        }

        .form-label {
            color: #c9c9f0;
            font-weight: 500;
            margin-bottom: 5px;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #9d4edd, #5a189a);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        .btn-home {
            margin-top: 15px;
            width: 100%;
            border-radius: 10px;
            font-weight: 600;
            background: #444466;
            color: #fff;
            transition: 0.3s ease-in-out;
        }

        .btn-home:hover {
            background: #5a189a;
            transform: translateY(-2px);
            color: #fff;
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        /* Error messages */
        .invalid-feedback {
            color: #ff6b6b;
            font-weight: 500;
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 500px) {
            .card-body { padding: 1.5rem; }
            .card-header { font-size: 1.3rem; padding: 1rem; }
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
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ $email ?? old('email') }}"
                               required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                        <input id="password-confirm" type="password"
                               class="form-control"
                               name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary mb-2">
                        <i class="bi bi-key-fill"></i> {{ __('Reset Password') }}
                    </button>

                    <!-- Back to Home -->
                    <a href="{{ url('/') }}" class="btn btn-home">
                        <i class="bi bi-house-fill"></i> {{ __('Back to Home') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
