@extends('layouts.app')
@section('content')
<div class="container" style="max-width:760px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="fw-bold mb-2">Choose New Password</h3>
            <p class="text-muted mb-4">Set a new password for your account.</p>
            <form method="POST" action="{{ route('password.update') }}">@csrf<input type="hidden" name="token" value="{{ $token }}"><div class="mb-3"><label for="email" class="form-label fw-semibold">Email Address</label><input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="mb-3"><label for="password" class="form-label fw-semibold">Password</label><input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="mb-3"><label for="password-confirm" class="form-label fw-semibold">Confirm Password</label><input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"></div><button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Reset Password</button></form>
        </div>
    </div>
</div>
@endsection
