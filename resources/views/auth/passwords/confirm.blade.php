@extends('layouts.app')
@section('content')
<div class="container" style="max-width:720px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="fw-bold mb-2">Confirm Password</h3>
            <p class="text-muted mb-4">Please confirm your password before continuing.</p>
            <form method="POST" action="{{ route('password.confirm') }}">@csrf<div class="mb-3"><label for="password" class="form-label fw-semibold">Password</label><input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Confirm Password</button>@if (Route::has('password.request'))<div class="text-center mt-3"><a class="text-decoration-none" href="{{ route('password.request') }}">Forgot your password?</a></div>@endif</form>
        </div>
    </div>
</div>
@endsection
