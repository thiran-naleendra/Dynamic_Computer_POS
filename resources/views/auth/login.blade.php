@extends('layouts.app')

@section('content')
<style>
  .auth-wrap{
    min-height: calc(100vh - 72px);
    display:flex;
    align-items:center;
  }
  .auth-card{
    border:0;
    border-radius: 18px;
    overflow:hidden;
  }
  .auth-left{
    background: linear-gradient(135deg, #0d6efd, #198754);
    color:#fff;
    padding: 28px;
    height: 100%;
  }
  .auth-left h3{ font-weight:800; margin-bottom:8px; }
  .auth-left p{ opacity:.9; margin-bottom:0; }
</style>

<div class="container auth-wrap">
  <div class="row justify-content-center w-100">
    <div class="col-lg-9 col-xl-8">

      <div class="card auth-card shadow-lg">
        <div class="row g-0">

          {{-- Left side --}}
          <div class="col-md-5 d-none d-md-block">
            <div class="auth-left h-100 d-flex flex-column justify-content-center">
              <h3>Dynamic computer system</h3>
              <p>Stock & Invoice Management</p>
              <hr class="border-light opacity-50 my-3">
              <small class="opacity-75">
                Login to manage products, stock, and invoices.
              </small>
            </div>
          </div>

          {{-- Right side --}}
          <div class="col-md-7">
            <div class="card-body p-4 p-lg-5">

              <div class="mb-3">
                <h4 class="fw-bold mb-1">Welcome Back</h4>
                <small class="text-muted">Please login to continue</small>
              </div>

              <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                  <label for="email" class="form-label fw-semibold">Email Address</label>
                  <div class="input-group">
                    <span class="input-group-text">📧</span>
                    <input id="email" type="email"
                      class="form-control @error('email') is-invalid @enderror"
                      name="email" value="{{ old('email') }}"
                      required autocomplete="email" autofocus
                      placeholder="example@gmail.com">
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                  <label for="password" class="form-label fw-semibold">Password</label>
                  <div class="input-group">
                    <span class="input-group-text">🔒</span>
                    <input id="password" type="password"
                      class="form-control @error('password') is-invalid @enderror"
                      name="password" required autocomplete="current-password"
                      placeholder="Enter password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                      👁
                    </button>
                    @error('password')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                {{-- Remember + Forgot --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember"
                      id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                      Remember me
                    </label>
                  </div>

                  @if (Route::has('password.request'))
                    <a class="text-decoration-none" href="{{ route('password.request') }}">
                      Forgot password?
                    </a>
                  @endif
                </div>

                {{-- Login button --}}
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                  Login
                </button>

              </form>

              <div class="mt-4 text-center text-muted small">
                © {{ date('Y') }} Thiran Naleendra
              </div>

            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('togglePassword');
    const input = document.getElementById('password');
    if(!btn || !input) return;

    btn.addEventListener('click', function () {
      const isPassword = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPassword ? 'text' : 'password');
      btn.innerText = isPassword ? '🙈' : '👁';
    });
  });
</script>
@endsection
