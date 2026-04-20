@extends('layouts.app')
@section('content')
<div class="container" style="max-width:720px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="fw-bold mb-2">Reset Password</h3>
            <p class="text-muted mb-4">Enter your email address and we’ll send you a password reset link.</p>
            @if (session('status'))<div class="alert alert-success border-0">{{ session('status') }}</div>@endif
            <form method="POST" action="{{ route('password.email') }}">@csrf<div class="mb-3"><label for="email" class="form-label fw-semibold">Email Address</label><input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Send Reset Link</button></form>
        </div>
    </div>
</div>
@endsection
