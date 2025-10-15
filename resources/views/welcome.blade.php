<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <title>{{ config('app.name') }} – Login</title>

  <!-- Bootstrap 5.3 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/user/images/') }}/logo.png">
  <link rel="shortcut icon" href="{{ asset('public/assets/user/images/') }}/logo.png" type="image/x-icon">

  @include('extra.snakbarcss')

  <!-- jQuery (AJAX) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    /* ===== Dark Purple Auth – Full CSS ===== */
    :root{
      --brand-900:#1a0f33; --brand-800:#221347; --brand-700:#2c1860;
      --brand-600:#3b1d7a; --brand-500:#5a24c8; --brand-400:#7d5cf0; --brand-300:#a28cff;
      --text-100:#f4f2fb; --text-200:#d8d2ef; --muted-300:#b0a7d6;
      --invalid:#ff5a5f; --valid:#22c55e; --placeholder:#ff3b30; /* red placeholder */
    }
    html,body{
      height:100%;
      background:
        radial-gradient(1200px 600px at 10% -10%, #2b1b5c 0%, transparent 60%),
        radial-gradient(800px 600px at 110% 10%, #44207f 0%, transparent 55%),
        linear-gradient(180deg, var(--brand-900), var(--brand-800));
      color: var(--text-100);
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,"Noto Sans","Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    }
    .auth-card{
      background: rgba(28, 18, 54, 0.6);
      backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(124, 88, 219, .25);
      border-radius: 1.25rem;
      box-shadow: 0 10px 35px rgba(0,0,0,.45), 0 0 0 1px rgba(124, 88, 219, .15) inset;
    }
    .brand-pill{
      display:inline-flex; align-items:center; gap:.5rem;
      background: linear-gradient(90deg, var(--brand-600), var(--brand-500));
      color:#fff; border-radius:999px; padding:.35rem .75rem; font-weight:600; font-size:.95rem;
      box-shadow: 0 6px 20px rgba(90,36,200,.35);
    }
    .logo img{ max-width: 160px; }

    body{ font-size: clamp(17px, 1.9vw, 19px); line-height:1.65; }
    h1{ font-size: 2.15rem; } .lead{ color: var(--text-200); }

    .form-control,.form-select{
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(124, 88, 219, .35);
      color: var(--text-100); padding:.9rem 1rem; font-size:1.05rem; border-radius:.8rem;
    }
    .form-control:focus,.form-select:focus{
      background: rgba(255,255,255,.06);
      border-color: var(--brand-400);
      box-shadow: 0 0 0 .2rem rgba(124, 88, 240, .25);
      color: var(--text-100);
    }
    /* placeholders (strong cross-browser) */
    input.form-control::placeholder,textarea.form-control::placeholder,.form-control::placeholder{ color: var(--placeholder)!important; opacity:1!important; }
    input.form-control::-webkit-input-placeholder,textarea.form-control::-webkit-input-placeholder{ color: var(--placeholder)!important; opacity:1!important; }
    input.form-control::-moz-placeholder,textarea.form-control::-moz-placeholder{ color: var(--placeholder)!important; opacity:1!important; }
    input.form-control:-ms-input-placeholder,textarea.form-control:-ms-input-placeholder,
    input.form-control::-ms-input-placeholder,textarea.form-control::-ms-input-placeholder{ color: var(--placeholder)!important; opacity:1!important; }
    .form-control:focus::placeholder{ color:#ff7b72!important; }

    .form-label{ color: var(--text-200); font-weight:600; }

    .was-validated .form-control:invalid,.form-control.is-invalid{
      border-color: var(--invalid)!important; box-shadow: 0 0 0 .2rem rgba(255,90,95,.2)!important;
    }
    .invalid-feedback{ color: var(--invalid); font-size:.95rem; }
    .was-validated .form-control:valid,.form-control.is-valid{
      border-color: var(--valid)!important; box-shadow: 0 0 0 .2rem rgba(34,197,94,.2)!important;
    }
    .valid-feedback{ color: var(--valid); font-size:.95rem; }

    .btn-brand{
      background: linear-gradient(90deg, var(--brand-500), var(--brand-400));
      border:0; color:#fff; font-weight:700; padding:.95rem 1.1rem; font-size:1.1rem; border-radius:.9rem;
      box-shadow: 0 10px 30px rgba(90,36,200,.35); transition: filter .15s ease, transform .05s ease;
    }
    .btn-brand:hover{ filter: brightness(1.05); }
    .btn-brand:active{ transform: translateY(1px); }
    .btn-brand:disabled{ opacity:.7; }

    .divider{ height:1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent); border:0; margin:1rem 0; }

    .lang-select{ background: rgba(255,255,255,.06); border-color: rgba(124, 88, 219, .35); color: var(--text-100); }
    a.link-light-muted{ color: var(--text-200); text-decoration:none; }
    a.link-light-muted:hover{ color:#fff; text-decoration:underline; }

    .password-wrapper{ position:relative; }
    .password-wrapper .toggle-eye{
      position:absolute; right:.85rem; top:50%; transform:translateY(-50%);
      cursor:pointer; color: var(--muted-300); font-size:1.05rem;
    }

    @media (max-width: 480px){
      h1{ font-size: 2.25rem; }
      .btn-brand,.form-control,.form-select{ font-size:1.12rem; }
      .logo img{ max-width:150px; }
    }
  </style>
</head>

<body>
  <main class="d-flex min-vh-100 align-items-center">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-5">

          <div class="auth-card p-4 p-md-5">
            <!-- top bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="brand-pill">
                <i class="bi bi-stars"></i> {{ config('app.name') }}
              </div>

              <div class="d-flex align-items-center gap-2">
                <a href="{{ App\Models\Sitesetting::find(1)->support_url }}" class="link-light-muted d-inline-flex align-items-center">
                  <i class="bi bi-headset me-2"></i> Support
                </a>

                <form method="GET" action="{{ route('changeLang') }}">
                  @php $langs = App\Models\Lang::all(); @endphp
                  <select name="lang" class="form-select form-select-sm lang-select" onchange="this.form.submit()">
                    @foreach ($langs as $lang)
                      <option value="{{ $lang->language_code }}" {{ session()->get('locale') == $lang->language_code ? 'selected' : '' }}>
                        {{ $lang->language_name }}
                      </option>
                    @endforeach
                  </select>
                </form>
              </div>
            </div>

            <div class="text-center mb-4">
              <div class="logo mb-3">
                <img src="{{ asset('public/assets/user/images/') }}/logo.png" alt="Logo" class="img-fluid">
              </div>
              <h1 class="h3 fw-bold">Welcome back</h1>
              <p class="lead mb-0">Sign in to continue.</p>
            </div>

            <div class="divider my-4"></div>

            <!-- Login Form -->
            <form id="myform" class="needs-validation" novalidate>
              @csrf

              <div class="mb-3">
                <label for="login_username" class="form-label">Username</label>
                <input type="text" class="form-control" id="login_username" name="phone" placeholder="Enter your username" required>
                <div class="invalid-feedback">Username is required.</div>
              </div>

              <div class="mb-2 password-wrapper">
                <label for="login_password" class="form-label">Password</label>
                <input type="password" class="form-control" id="login_password" name="pwd" placeholder="Your password" required>
                <i class="bi bi-eye toggle-eye" id="togglePassword"></i>
                <div class="invalid-feedback">Password is required.</div>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="remember">
                  <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="link-light-muted">Forgot Password?</a>
              </div>

              <div class="d-grid">
                <button id="loginSubmit" type="submit" class="btn btn-brand btn-lg">
                  <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </button>
              </div>

              <div class="text-center mt-4">
                <span class="me-1">Don’t have an account?</span>
                <a href="{{ url('register') }}" class="link-light-muted">Register here</a>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </main>

  @include('extra.snakbarjs')

  <script>
    // toggle password visibility
    (function(){
      const eye = document.getElementById('togglePassword');
      const pw  = document.getElementById('login_password');
      if (eye && pw){
        eye.addEventListener('click', () => {
          const isPw = pw.getAttribute('type') === 'password';
          pw.setAttribute('type', isPw ? 'text' : 'password');
          eye.classList.toggle('bi-eye');
          eye.classList.toggle('bi-eye-slash');
        });
      }
    })();

    // BS5 validation + AJAX login
    (function(){
      const form = document.getElementById('myform');
      const btn  = document.getElementById('loginSubmit');

      form.addEventListener('submit', function(e){
        e.preventDefault();

        if (!form.checkValidity()){
          form.classList.add('was-validated');
          return;
        }

        $.ajax({
          type: 'POST',
          url: "{{ url('login') }}",
          data: $(form).serialize(),
          beforeSend: function(){
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';
          },
          success: function(res){
            if (res.error){
              $("#snackbar2").text(res.error); myFunction2();
              return;
            }
            if (res.success){
              $("#snackbar").text(res.success); myFunction();
            }
            if (res.location){
              window.location.href = res.location;
            } else if (res.success){
              window.location.href = "{{ url('user/mine') }}";
            }
          },
          error: function(xhr){
            let msg = 'Login failed. Please try again.';
            try{
              if (xhr.responseText){
                const r = JSON.parse(xhr.responseText);
                if (r.error) msg = r.error;
              }
            }catch(e){
              msg = `Server Error (${xhr.status}). Please try again later.`;
            }
            $("#snackbar2").text(msg); myFunction2();
          },
          complete: function(){
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i> Login';
          }
        });
      });
    })();
  </script>

  <!-- Bootstrap 5.3 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
