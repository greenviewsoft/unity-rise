<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <title>{{ config('app.name') }}</title>

  <!-- Bootstrap 5.3 CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Fonts (optional) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="shortcut icon" href="{{ asset('public/assets/user/images/') }}/logo.png" type="image/x-icon">
  @include('extra.snakbarcss')

  <!-- jQuery (needed for your AJAX) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
   /* ===========================
   Dark Purple Auth – Full CSS
   =========================== */

:root{
  --brand-900:#1a0f33;   /* very dark purple */
  --brand-800:#221347;
  --brand-700:#2c1860;
  --brand-600:#3b1d7a;
  --brand-500:#5a24c8;   /* primary */
  --brand-400:#7d5cf0;
  --brand-300:#a28cff;

  --text-100:#f4f2fb;
  --text-200:#d8d2ef;
  --muted-300:#b0a7d6;

  --invalid:#ff5a5f;
  --valid:#22c55e;

  /* placeholder color (change if you like) */
  --placeholder:#ff3b30; /* or var(--bs-danger) */
}

html,body{
  height:100%;
  background:
    radial-gradient(1200px 600px at 10% -10%, #2b1b5c 0%, transparent 60%),
    radial-gradient(800px 600px at 110% 10%, #44207f 0%, transparent 55%),
    linear-gradient(180deg, var(--brand-900), var(--brand-800));
  color: var(--text-100);
  font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol", "Noto Color Emoji";
}

/* ---------- Layout helpers ---------- */
.min-vh-100{ min-height: 100vh; }

/* ---------- Card / Container ---------- */
.auth-card{
  background: rgba(28, 18, 54, 0.6);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(124, 88, 219, 0.25);
  border-radius: 1.25rem;
  box-shadow: 0 10px 35px rgba(0,0,0,.45), 0 0 0 1px rgba(124, 88, 219, .15) inset;
}

/* tiny brand chip */
.brand-pill{
  display:inline-flex; align-items:center; gap:.5rem;
  background: linear-gradient(90deg, var(--brand-600), var(--brand-500));
  color:white; border-radius: 999px; padding:.35rem .75rem;
  font-weight:600; font-size:.95rem;
  box-shadow: 0 6px 20px rgba(90,36,200,.35);
}

/* logo sizing */
.logo img{ max-width: 160px; }

/* ---------- Typography ---------- */
body{ font-size: clamp(17px, 1.9vw, 19px); line-height: 1.65; }
h1{ font-size: 2.2rem; }
h2{ font-size: 1.7rem; }
.lead{ color: var(--text-200); }

/* ---------- Inputs & Selects ---------- */
.form-control,
.form-select{
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(124, 88, 219, .35);
  color: var(--text-100);
  padding: .9rem 1rem;
  font-size: 1.05rem;
  border-radius: .8rem;
}

.form-control:focus,
.form-select:focus{
  background: rgba(255,255,255,.06);
  border-color: var(--brand-400);
  box-shadow: 0 0 0 .2rem rgba(124, 88, 240, .25);
  color: var(--text-100);
}

/* ---------- Placeholder (strong, cross-browser) ---------- */
/* Global, but biased to Bootstrap fields for specificity */
input.form-control::placeholder,
textarea.form-control::placeholder,
.form-control::placeholder{
  color: var(--placeholder) !important;
  opacity: 1 !important;
}

/* WebKit/Safari/older Android */
input.form-control::-webkit-input-placeholder,
textarea.form-control::-webkit-input-placeholder{
  color: var(--placeholder) !important;
  opacity: 1 !important;
}

/* Firefox */
input.form-control::-moz-placeholder,
textarea.form-control::-moz-placeholder{
  color: var(--placeholder) !important;
  opacity: 1 !important;
}

/* IE/Edge legacy */
input.form-control:-ms-input-placeholder,
textarea.form-control:-ms-input-placeholder,
input.form-control::-ms-input-placeholder,
textarea.form-control::-ms-input-placeholder{
  color: var(--placeholder) !important;
  opacity: 1 !important;
}

/* Optional: softer placeholder while not focused, brighter on focus */
.form-control:focus::placeholder{ color: #ff7b72 !important; }

/* ---------- Labels & helper ---------- */
.form-label{ color: var(--text-200); font-weight: 600; }

/* ---------- Validation (Bootstrap 5 friendly) ---------- */
.was-validated .form-control:invalid,
.form-control.is-invalid{
  border-color: var(--invalid) !important;
  box-shadow: 0 0 0 .2rem rgba(255,90,95,.2) !important;
}
.invalid-feedback{ color: var(--invalid); font-size:.95rem; }

.was-validated .form-control:valid,
.form-control.is-valid{
  border-color: var(--valid) !important;
  box-shadow: 0 0 0 .2rem rgba(34,197,94,.2) !important;
}
.valid-feedback{ color: var(--valid); font-size:.95rem; }

/* ---------- Buttons ---------- */
.btn-brand{
  background: linear-gradient(90deg, var(--brand-500), var(--brand-400));
  border: 0;
  color: #fff;
  font-weight: 700;
  padding: .95rem 1.1rem;
  font-size: 1.1rem;
  border-radius: .9rem;
  box-shadow: 0 10px 30px rgba(90,36,200,.35);
  transition: filter .15s ease, transform .05s ease;
}
.btn-brand:hover{ filter: brightness(1.05); }
.btn-brand:active{ transform: translateY(1px); }
.btn-brand:disabled{ opacity:.7; }

/* ---------- Divider line ---------- */
.divider{
  height:1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent);
  border: 0; margin: 1rem 0;
}

/* ---------- Language select ---------- */
.lang-select{
  background: rgba(255,255,255,.06);
  border-color: rgba(124, 88, 219, .35);
  color: var(--text-100);
}

/* ---------- Links ---------- */
a.link-light-muted{ color: var(--text-200); text-decoration: none; }
a.link-light-muted:hover{ color: #fff; text-decoration: underline; }

/* ---------- Eye icon inside password field ---------- */
.password-wrapper{ position:relative; }
.password-wrapper .toggle-eye{
  position:absolute; right: .85rem; top:50%; transform: translateY(-50%);
  cursor:pointer; color: var(--muted-300); font-size: 1.05rem;
}

/* ---------- Responsive tweaks ---------- */
@media (max-width: 480px){
  h1{ font-size: 2.3rem; }
  .btn-brand, .form-control, .form-select{ font-size: 1.12rem; }
  .logo img{ max-width: 150px; }
}

/* Optional: make bottom links a bit larger */
.bottom_area_register a{ font-size: 1.05rem; }

/* Optional: subtle outline for the whole page container (debug/design) */
/* .container { outline: 1px dashed rgba(255,255,255,.1); } */
  </style>
</head>

<body>
  <main class="d-flex min-vh-100 align-items-center">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-6">

          <div class="auth-card rounded-4 p-4 p-md-5">
            <!-- top bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="brand-pill">
                <i class="bi bi-stars"></i>
                {{ config('app.name') }}
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
              <h1 class="h3 fw-bold">Create your account</h1>
              <p class="lead mb-0">Join and start your journey.</p>
            </div>

            <div class="divider my-4"></div>

            <!-- Register Form -->
            <form id="myform" class="needs-validation" novalidate>
              @csrf

              <div class="mb-3">
                <label for="referCode" class="form-label">Invitation code</label>
                <input type="text" class="form-control" id="referCode" name="refer" placeholder="e.g. 123456" required>
                <div class="invalid-feedback">Invitation code is required.</div>
              </div>

              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Your username" required>
                <div class="invalid-feedback">Username is required.</div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                <div class="invalid-feedback">Please enter a valid email address.</div>
              </div>

              <div class="mb-3 password-wrapper">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Choose password" minlength="6" required>
                <i class="bi bi-eye toggle-eye" id="togglePassword"></i>
                <div class="invalid-feedback">Password must be at least 6 characters.</div>
              </div>

              <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Retype password" required>
                <div class="invalid-feedback">Passwords must match.</div>
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">Phone number</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g. +121XXXXXXXXX" required>
                <div class="invalid-feedback">Phone number is required.</div>
              </div>

              <div class="mb-3">
                <label for="crypto_password" class="form-label">Withdrawal password</label>
                <input type="password" class="form-control" id="crypto_password" name="crypto_password" placeholder="Set withdrawal password" required>
                <div class="invalid-feedback">Withdrawal password is required.</div>
              </div>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="terms" required>
                <label class="form-check-label" for="terms">
                  I agree with the <a href="#" class="link-light-muted">Terms and Conditions</a>.
                </label>
                <div class="invalid-feedback">You must agree before submitting.</div>
              </div>

              <div class="d-grid">
                <button id="login" class="btn btn-brand btn-lg" type="submit">
                  <i class="bi bi-person-plus me-2"></i> Create Account
                </button>
              </div>

              <div class="d-flex justify-content-between mt-4">
                <a href="/password/request" class="link-light-muted">Forgot Password?</a>
                <a href="{{ url('/') }}" class="link-light-muted">Sign In</a>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </main>

  @include('extra.snakbarjs')

  <script>
    // auto-fill refer code from URL
    (function(){
      const m = /\/register\/(\d+)/.exec(window.location.href);
      if (m) document.getElementById('referCode').value = m[1];
    })();

    // toggle eye
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    if (togglePassword) {
      togglePassword.addEventListener('click', () => {
        const isPw = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPw ? 'text' : 'password');
        togglePassword.classList.toggle('bi-eye');
        togglePassword.classList.toggle('bi-eye-slash');
      });
    }

    // Bootstrap 5 validation + AJAX
    (function () {
      'use strict';
      const form = document.getElementById('myform');
      const btn = document.getElementById('login');
      const cpwd = document.getElementById('confirm_password');

      form.addEventListener('submit', function (e) {
        e.preventDefault();

        // custom confirm-password match
        if (passwordInput.value && cpwd.value && passwordInput.value !== cpwd.value) {
          cpwd.setCustomValidity('Passwords do not match');
        } else {
          cpwd.setCustomValidity('');
        }

        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          return;
        }

        $.ajax({
          type: 'POST',
          url: "{{ url('register_submit') }}",
          data: $(form).serialize(),
          beforeSend: function(){
            btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
          },
          success: function(res){
            if (res.error) {
              $("#snackbar2").text(res.error); myFunction2();
            }
            if (res.success) {
              $("#snackbar").text(res.success); myFunction();
              setTimeout(function() {
                if (res.location) window.location.href = res.location;
                else window.location.href = "{{ url('user/dashboard') }}";
              }, 1500);
            }
          },
          error: function(xhr){
            let msg = 'Registration failed. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
            else if (xhr.status === 422) msg = 'Validation error. Please check your input.';
            else if (xhr.status === 500) msg = 'Server error. Please try again later.';
            $("#snackbar2").text(msg); myFunction2();
          },
          complete: function(){ btn.disabled = false; btn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Create Account'; }
        });
      });
    })();
  </script>

  <!-- Bootstrap 5.3 JS (CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
